<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Services;

use App\Domains\Catalog\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class BrandService
{
    /**
     * List active brands (cached for public, uncached for admin).
     */
    public function listForPublic(): mixed
    {
        return Cache::remember('brands_active_list', 1800, function () {
            return Brand::active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * List all brands (admin, paginated).
     */
    public function listForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return Brand::withTrashed()
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Find a brand by slug (public).
     */
    public function findBySlug(string $slug): ?Brand
    {
        return Brand::active()->where('slug', $slug)->first();
    }

    /**
     * Find a brand by ID (admin, includes trashed).
     */
    public function findById(int $id): ?Brand
    {
        return Brand::withTrashed()->find($id);
    }

    /**
     * Find a brand by UUID (admin).
     */
    public function findByUuid(string $uuid): ?Brand
    {
        return Brand::withTrashed()->where('uuid', $uuid)->first();
    }

    /**
     * Create a new brand.
     */
    public function create(array $data): Brand
    {
        $brand = Brand::create($data);
        $this->flushCache();
        return $brand;
    }

    /**
     * Update an existing brand.
     */
    public function update(Brand $brand, array $data): Brand
    {
        $brand->update($data);
        $this->flushCache();
        return $brand->fresh();
    }

    /**
     * Soft-delete a brand.
     */
    public function delete(Brand $brand): void
    {
        if ($brand->products()->exists()) {
            abort(409, 'لا يمكن حذف هذا البراند لأنه يحتوي على منتجات. الرجاء حذف المنتجات أولاً أو نقلها لبراند آخر.');
        }

        $brand->delete();
        $this->flushCache();
    }

    /**
     * Flush brand-related cache keys.
     */
    private function flushCache(): void
    {
        Cache::forget('brands_active_list');
    }
}
