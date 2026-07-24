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
                    $query->active()->orderBy('sort_order')->orderBy('name');
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
        return Category::withTrashed()
            ->with('parent')
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
            ->with(['children' => function ($query) {
                $query->active()->orderBy('sort_order');
            }])
            ->first();
    }

    /**
     * Find a category by ID (admin, includes trashed).
     */
    public function findById(int $id): ?Category
    {
        return Category::withTrashed()->with('parent')->find($id);
    }

    /**
     * Find a category by UUID (admin).
     */
    public function findByUuid(string $uuid): ?Category
    {
        return Category::withTrashed()->with('parent')->where('uuid', $uuid)->first();
    }

    /**
     * Create a new category.
     */
    public function create(array $data): Category
    {
        if (isset($data['parent_id'])) {
            $this->guardAgainstCycle(null, (int) $data['parent_id']);
        }

        $category = Category::create($data);
        $this->flushCache();
        return $category;
    }

    /**
     * Update an existing category.
     */
    public function update(Category $category, array $data): Category
    {
        if (isset($data['parent_id'])) {
            $this->guardAgainstCycle($category->id, (int) $data['parent_id']);
        }

        $category->update($data);
        $this->flushCache();
        return $category->fresh();
    }

    /**
     * Soft-delete a category.
     */
    public function delete(Category $category): void
    {
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
     * Flush category-related cache keys.
     */
    private function flushCache(): void
    {
        Cache::forget('categories_tree');
    }
}
