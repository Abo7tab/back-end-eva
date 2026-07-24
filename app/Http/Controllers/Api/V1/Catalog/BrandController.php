<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Domains\Catalog\Resources\BrandResource;
use App\Domains\Catalog\Services\BrandService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreBrandRequest;
use App\Http\Requests\Catalog\UpdateBrandRequest;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct(private BrandService $service) {}

    public function index(Request $request)
    {
        return BrandResource::collection($this->service->listForPublic());
    }

    public function show(string $slug)
    {
        $brand = $this->service->findBySlug($slug) ?? abort(404, 'Brand not found');
        return new BrandResource($brand);
    }

    public function indexAdmin(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);
        return BrandResource::collection($this->service->listForAdmin($perPage));
    }

    public function showAdmin(string $uuid)
    {
        $brand = $this->service->findByUuid($uuid) ?? abort(404, 'Brand not found');
        return new BrandResource($brand);
    }

    public function store(StoreBrandRequest $request)
    {
        $brand = $this->service->create($request->validated());
        return new BrandResource($brand);
    }

    public function update(UpdateBrandRequest $request, string $uuid)
    {
        $brand = $this->service->findByUuid($uuid) ?? abort(404, 'Brand not found');
        $updated = $this->service->update($brand, $request->validated());
        return new BrandResource($updated);
    }

    public function destroy(string $uuid)
    {
        $brand = $this->service->findByUuid($uuid) ?? abort(404, 'Brand not found');
        $this->service->delete($brand);
        return response()->json(['success' => true, 'data' => null, 'message' => 'Deleted']);
    }
}
