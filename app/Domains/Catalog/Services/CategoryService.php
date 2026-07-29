<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Services;

use App\Domains\Catalog\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    /**
     * List categories as a tree (cached for public).
     */
    public function listTreeForPublic(): mixed
    {
        return Cache::remember('categories_tree', 1800, function () {
            return Category::active()
                ->root()
                ->with(['children' => function ($query) {
                    $query->active()
                        ->with('parent')
                        ->orderBy('sort_order')
                        ->orderBy('name');
                }])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * List all categories (admin, paginated).
     */
    public function listForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return Category::with('parent')
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Find a category by slug (public).
     */
    public function findBySlug(string $slug): ?Category
    {
        return Category::active()
            ->where('slug', $slug)
            ->with('parent')
            ->with(['children' => function ($query) {
                $query->active()
                    ->with('parent')
                    ->orderBy('sort_order');
            }])
            ->first();
    }

    /**
     * Find a category by ID (admin, includes trashed).
     */
    public function findById(int $id): ?Category
    {
        return Category::withTrashed()->with('parent')->withCount('products')->find($id);
    }

    /**
     * Find a category by UUID (admin).
     */
    public function findByUuid(string $uuid): ?Category
    {
        return Category::withTrashed()->with('parent')->withCount('products')->where('uuid', $uuid)->first();
    }

    /**
     * Create a new category.
     */
    public function create(array $data): Category
    {
        if (array_key_exists('parent_id', $data) && $data['parent_id'] !== null) {
            $this->guardAgainstCycle(null, (int) $data['parent_id']);
        }

        $category = Category::create($data);
        $this->flushCache();
        return $category->load('parent', 'children');
    }

    /**
     * Update an existing category.
     */
    public function update(Category $category, array $data): Category
    {
        if (array_key_exists('parent_id', $data) && $data['parent_id'] !== null) {
            $this->guardAgainstCycle($category->id, (int) $data['parent_id']);
        }

        $category->update($data);
        $this->flushCache();
        return $category->fresh(['parent', 'children']);
    }

    /**
     * Soft-delete a category.
     */
    public function delete(Category $category): void
    {
        if ($category->products()->exists()) {
            abort(409, 'لا يمكن حذف هذا القسم لأنه يحتوي على منتجات. الرجاء حذف المنتجات أولاً أو نقلها لقسم آخر.');
        }

        if ($category->children()->exists()) {
            abort(409, 'لا يمكن حذف هذا القسم لأنه يحتوي على أقسام فرعية. الرجاء حذفها أو نقلها أولاً.');
        }

        $category->delete();
        $this->flushCache();
    }

    /**
     * Guard against parent_id cycles (Known Open Decision #2).
     *
     * Walks up the ancestor chain from $parentId. If it ever encounters
     * $categoryId, a cycle would be created — throw an exception.
     */
    private function guardAgainstCycle(?int $categoryId, int $parentId): void
    {
        // عند الإنشاء (categoryId = null) لا يمكن أن يكون هناك cycle
        if ($categoryId === null) {
            return;
        }

        // لا يمكن أن يكون القسم أب نفسه
        if ($categoryId === $parentId) {
            abort(422, 'A category cannot be its own parent.');
        }

        // تتبع سلسلة الآباء
        $visited = [$categoryId];
        $currentId = $parentId;

        while ($currentId !== null) {
            if (in_array($currentId, $visited, true)) {
                abort(422, 'Setting this parent would create a circular reference.');
            }
            $visited[] = $currentId;
            $currentId = Category::where('id', $currentId)->value('parent_id');
        }
    }

    /**
     * Get the projected depth if a category is placed under $parentId.
     */
    public function getDepth(?int $parentId): int
    {
        if ($parentId === null) {
            return 1;
        }
        
        $depth = 1;
        $current = Category::find($parentId);
        
        while ($current && $current->parent_id !== null) {
            $depth++;
            if ($depth > 10) {
                break; // Safety infinite loop protection
            }
            $current = Category::find($current->parent_id);
        }
        
        return $depth + 1; // +1 for the new category being added
    }

    /**
     * Flush category-related cache keys.
     */
    private function flushCache(): void
    {
        Cache::forget('categories_tree');
    }
}
