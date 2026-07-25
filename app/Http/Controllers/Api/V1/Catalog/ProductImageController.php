<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Domains\Catalog\Models\ProductImage;
use App\Domains\Catalog\Resources\ProductImageResource;
use App\Domains\Catalog\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\ReorderProductImagesRequest;
use App\Http\Requests\Catalog\StoreProductImageRequest;


class ProductImageController extends Controller
{
    public function __construct(private ProductService $service) {}

    public function store(
        StoreProductImageRequest $request,
        string $productUuid,
        ProductService $productService
    ): ProductImageResource {
        $product = $productService->findByUuid($productUuid);
        if (!$product) {
            abort(404);
        }
        
        $image = $product->images()->create([
            'image_url'          => $request->input('image_url'),
            'storage_public_id'  => $request->input('storage_public_id'),
            'alt_text'           => $request->input('alt_text'),
            'sort_order'         => $request->input('sort_order', 0),
        ]);
        
        return new ProductImageResource($image);
    }

    public function reorder(ReorderProductImagesRequest $request, string $productUuid)
    {
        $product = $this->service->findByUuid($productUuid) ?? abort(404, 'Product not found');
        $this->service->reorderImages($product, $request->validated('images'));
        return response()->json(['success' => true, 'data' => null, 'message' => 'Images reordered']);
    }

    public function destroy(string $uuid)
    {
        $image = ProductImage::findByUuid($uuid) ?? abort(404, 'Product image not found');
        $this->service->deleteImage($image);
        return response()->json(['success' => true, 'data' => null, 'message' => 'Deleted']);
    }
}
