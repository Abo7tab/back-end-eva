<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Domains\Catalog\Resources\ProductResource;
use App\Domains\Catalog\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreProductRequest;
use App\Http\Requests\Catalog\UpdateProductRequest;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductService $service) {}

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 12);
        return ProductResource::collection($this->service->listForPublic($request->all(), $perPage));
    }

    public function show(string $slug)
    {
        $product = $this->service->findBySlug($slug) ?? abort(404, 'Product not found');
        return new ProductResource($product);
    }

    public function trending()
    {
        return ProductResource::collection($this->service->listTrending());
    }

    public function featured()
    {
        return ProductResource::collection($this->service->listFeatured());
    }

    public function indexAdmin(Request $request)
    {
        $perPage = (int) $request->query('per_page', 12);
        return ProductResource::collection($this->service->listForAdmin($request->all(), $perPage));
    }

    public function showAdmin(string $uuid)
    {
        $product = $this->service->findByUuid($uuid) ?? abort(404, 'Product not found');
        return new ProductResource($product);
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->service->create($request->toServiceData());
        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, string $uuid)
    {
        $product = $this->service->findByUuid($uuid) ?? abort(404, 'Product not found');
        $updated = $this->service->update($product, $request->toServiceData());
        return new ProductResource($updated);
    }

    public function destroy(string $uuid)
    {
        $product = $this->service->findByUuid($uuid) ?? abort(404, 'Product not found');
        $this->service->delete($product);
        return response()->json(['success' => true, 'data' => null, 'message' => 'Deleted']);
    }
}
