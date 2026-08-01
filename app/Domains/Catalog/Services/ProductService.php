<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Services;

use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductImage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    /**
     * List products with filters (public, paginated).
     */
    public function listForPublic(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $query = Product::active()
            ->with(['brand', 'category', 'images']);

        $this->applyFilters($query, $filters);
        $this->applySorting($query, $filters['sort_by'] ?? null);

        return $query->paginate($perPage);
    }

    /**
     * List trending products (cached).
     */
    public function listTrending(): mixed
    {
        return Cache::remember('products_trending', 900, function () {
            return Product::active()
                ->trending()
                ->with(['brand', 'category'])
                ->orderBy('sort_order')
                ->limit(12)
                ->get();
        });
    }

    /**
     * List featured products (cached).
     */
    public function listFeatured(): mixed
    {
        return Cache::remember('products_featured', 900, function () {
            return Product::active()
                ->featured()
                ->with(['brand', 'category'])
                ->orderBy('sort_order')
                ->limit(12)
                ->get();
        });
    }

    /**
     * Find a product by slug (public, cached).
     */
    public function findBySlug(string $slug): ?Product
    {
        $product = Cache::remember("product_{$slug}", 900, function () use ($slug) {
            return Product::where('slug', $slug)
                ->active()
                ->with(['brand', 'category', 'images'])
                ->first();
        });

        if ($product) {
            \App\Jobs\IncrementProductViewJob::dispatch($product->id);
            return $product;
        }

        return null;
    }

    /**
     * List all products (admin, paginated).
     */
    public function listForAdmin(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $query = Product::with(['brand', 'category', 'images']);

        $this->applyFilters($query, $filters);
        $this->applySorting($query, $filters['sort_by'] ?? null);

        return $query->paginate($perPage);
    }

    /**
     * Find a product by ID (admin, includes trashed).
     */
    public function findById(int $id): ?Product
    {
        return Product::withTrashed()
            ->with(['brand', 'category', 'images'])
            ->find($id);
    }

    /**
     * Find a product by UUID (admin).
     */
    public function findByUuid(string $uuid): ?Product
    {
        return Product::withTrashed()
            ->with(['brand', 'category', 'images'])
            ->where('uuid', $uuid)
            ->first();
    }

    /**
     * Create a new product.
     */
    public function create(array $data): Product
    {
        $product = Product::create($data);
        
        if (!empty($data['is_trending'])) {
            Cache::forget('products_trending');
        }
        if (!empty($data['is_featured'])) {
            Cache::forget('products_featured');
        }

        return $product->load(['brand', 'category', 'images']);
    }

    /**
     * Update an existing product.
     */
    public function update(Product $product, array $data): Product
    {
        $oldSlug = $product->slug;
        $product->update($data);

        Cache::forget("product_{$oldSlug}");
        $this->forgetProductCaches($product);

        return $product->fresh(['brand', 'category', 'images']);
    }

    /**
     * Soft-delete a product.
     */
    public function delete(Product $product): void
    {
        $this->forgetProductCaches($product);
        $product->delete();
    }

    /**
     * Add an image to a product.
     */
    public function addImage(Product $product, array $data): ProductImage
    {
        $data['product_id'] = $product->id;
        $image = ProductImage::create($data);
        $this->flushCache($product->slug);
        return $image;
    }

    /**
     * Delete a product image.
     */
    public function deleteImage(ProductImage $image): void
    {
        $product = $image->product;
        $image->delete();
        if ($product) {
            $this->flushCache($product->slug);
        }
    }

    /**
     * Reorder product images.
     */
    public function reorderImages(Product $product, array $images): void
    {
        foreach ($images as $imageData) {
            ProductImage::where('uuid', $imageData['uuid'])
                ->where('product_id', $product->id)
                ->update(['sort_order' => $imageData['sort_order']]);
        }
        $this->flushCache($product->slug);
    }

    /**
     * Increment product views count (called from job).
     */
    public function incrementViews(int $productId): void
    {
        Product::where('id', $productId)->increment('views_count');
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['brand_uuid'])) {
            $query->whereHas('brand', fn($q) => $q->where('uuid', $filters['brand_uuid']));
        } elseif (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (!empty($filters['category_uuid'])) {
            $categoryId = \App\Domains\Catalog\Models\Category::where('uuid', $filters['category_uuid'])->value('id');
            if ($categoryId) {
                $allCats = \App\Domains\Catalog\Models\Category::select('id', 'parent_id')->get()->toArray();
                $descendants = $this->getCategoryDescendants($allCats, $categoryId);
                $descendants[] = $categoryId;
                $query->whereIn('category_id', $descendants);
            }
        } elseif (!empty($filters['category_id'])) {
            $allCats = \App\Domains\Catalog\Models\Category::select('id', 'parent_id')->get()->toArray();
            $descendants = $this->getCategoryDescendants($allCats, (int)$filters['category_id']);
            $descendants[] = (int)$filters['category_id'];
            $query->whereIn('category_id', $descendants);
        }

        if (isset($filters['is_trending'])) {
            $query->where('is_trending', filter_var($filters['is_trending'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', filter_var($filters['is_featured'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($filters['active_status'])) {
            $query->where('active_status', filter_var($filters['active_status'], FILTER_VALIDATE_BOOLEAN));
        }

        if (!empty($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('short_description', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }
    }

    /**
     * Apply sorting to query.
     */
    private function applySorting(Builder $query, ?string $sortBy): void
    {
        match ($sortBy) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'newest'     => $query->orderBy('created_at', 'desc'),
            'popular',
            'views'      => $query->orderBy('views_count', 'desc'),
            default      => $query->orderBy('sort_order')->orderBy('created_at', 'desc'),
        };
    }

    /**
     * Flush product-related cache keys.
     */
    private function flushCache(string $slug): void
    {
        Cache::forget("product_{$slug}");
    }

    private function forgetProductCaches(Product $product): void
    {
        Cache::forget('product_' . $product->slug);
        Cache::forget('products_trending');
        Cache::forget('products_featured');
    }

    private function getCategoryDescendants(array $allCategories, int $parentId): array
    {
        $ids = [];
        foreach ($allCategories as $cat) {
            if ($cat['parent_id'] === $parentId) {
                $ids[] = $cat['id'];
                $ids = array_merge($ids, $this->getCategoryDescendants($allCategories, $cat['id']));
            }
        }
        return $ids;
    }
}
