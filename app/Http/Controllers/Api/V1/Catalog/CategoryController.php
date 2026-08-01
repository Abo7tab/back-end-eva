<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Domains\Catalog\Resources\CategoryResource;
use App\Domains\Catalog\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreCategoryRequest;
use App\Http\Requests\Catalog\UpdateCategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $service) {}

    public function index(Request $request)
    {
        return CategoryResource::collection($this->service->listTreeForPublic());
    }

    public function show(string $slug)
    {
        $category = $this->service->findBySlug($slug) ?? abort(404, 'Category not found');
        return new CategoryResource($category);
    }

    public function indexAdmin(Request $request)
    {
        $perPage = (int) $request->query('per_page', 12);
        return CategoryResource::collection($this->service->listForAdmin($perPage));
    }

    public function showAdmin(string $uuid)
    {
        $category = $this->service->findByUuid($uuid) ?? abort(404, 'Category not found');
        return new CategoryResource($category);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->service->create($request->toServiceData());
        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, string $uuid)
    {
        $category = $this->service->findByUuid($uuid) ?? abort(404, 'Category not found');
        $updated = $this->service->update($category, $request->toServiceData());
        return new CategoryResource($updated);
    }

    public function destroy(string $uuid)
    {
        $category = $this->service->findByUuid($uuid) ?? abort(404, 'Category not found');
        $this->service->delete($category);
        return response()->json(['success' => true, 'data' => null, 'message' => 'Deleted']);
    }
}
