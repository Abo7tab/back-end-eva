<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domains\Catalog\Models\Category;
use App\Domains\Catalog\Models\ProductImage;
use App\Domains\Catalog\Resources\CategoryResource;
use App\Domains\Catalog\Resources\ProductImageResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Tests\TestCase;

class ResourcesAvoidLazyLoadingTest extends TestCase
{
    protected function tearDown(): void
    {
        Model::preventLazyLoading(false);

        parent::tearDown();
    }

    public function test_category_resource_does_not_lazy_load_parent(): void
    {
        Model::preventLazyLoading();

        $category = new Category([
            'uuid' => '11111111-1111-1111-1111-111111111111',
            'parent_id' => 1,
            'name' => 'Child category',
            'slug' => 'child-category',
            'active_status' => true,
            'sort_order' => 0,
        ]);
        $category->exists = true;

        $payload = (new CategoryResource($category))->resolve(Request::create('/api/v1/categories'));

        $this->assertArrayHasKey('parent_uuid', $payload);
        $this->assertNull($payload['parent_uuid']);
    }

    public function test_product_image_resource_does_not_lazy_load_product(): void
    {
        Model::preventLazyLoading();

        $image = new ProductImage([
            'uuid' => '22222222-2222-2222-2222-222222222222',
            'product_id' => 1,
            'image_url' => 'https://example.com/product.webp',
            'alt_text' => 'Product image',
            'sort_order' => 0,
        ]);
        $image->exists = true;

        $payload = (new ProductImageResource($image))->resolve(Request::create('/api/v1/admin/products/1/images'));

        $this->assertArrayHasKey('product_uuid', $payload);
        $this->assertNull($payload['product_uuid']);
    }
}
