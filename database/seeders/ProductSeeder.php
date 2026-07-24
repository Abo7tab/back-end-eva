<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Catalog\Models\Brand;
use App\Domains\Catalog\Models\Category;
use App\Domains\Catalog\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $evaBrand = Brand::where('name', 'EVA Cosmetics')->firstOrFail();
        $niveaBrand = Brand::where('name', 'Nivea')->firstOrFail();

        $sunProtectionCat = Category::where('name', 'Sun Protection')->firstOrFail();
        $skincareCat = Category::where('name', 'Skincare')->firstOrFail();
        $faceSunblockCat = Category::where('name', 'Face Sunblock')->firstOrFail();

        // Product 1
        Product::updateOrCreate(
            ['name' => 'EVA Sun Block SPF 50 - 100ml'],
            [
                'brand_id' => $evaBrand->id,
                'category_id' => $faceSunblockCat->id,
                'price' => 250.00,
                'compare_at_price' => 300.00,
                'stock_quantity' => 100,
                'weight' => 100,
                'weight_unit' => 'ml',
                'is_trending' => true,
                'is_featured' => true,
                'active_status' => true,
            ]
        );

        // Product 2
        Product::updateOrCreate(
            ['name' => 'EVA Sun Block SPF 30 - 200ml'],
            [
                'brand_id' => $evaBrand->id,
                'category_id' => $sunProtectionCat->id,
                'price' => 320.00,
                'compare_at_price' => null,
                'stock_quantity' => 50,
                'weight' => 200,
                'weight_unit' => 'ml',
                'is_trending' => true,
                'is_featured' => false,
                'active_status' => true,
            ]
        );

        // Product 3
        Product::updateOrCreate(
            ['name' => 'EVA Moisturizer Cream - 50g'],
            [
                'brand_id' => $evaBrand->id,
                'category_id' => $skincareCat->id,
                'price' => 180.00,
                'compare_at_price' => null,
                'stock_quantity' => 200,
                'weight' => 50,
                'weight_unit' => 'g',
                'is_trending' => false,
                'is_featured' => true,
                'active_status' => true,
            ]
        );

        // Product 4
        Product::updateOrCreate(
            ['name' => 'Nivea Sun Kids SPF 50+'],
            [
                'brand_id' => $niveaBrand->id,
                'category_id' => $sunProtectionCat->id,
                'price' => 400.00,
                'compare_at_price' => null,
                'stock_quantity' => 30,
                'weight' => 150,
                'weight_unit' => 'ml',
                'is_trending' => true,
                'is_featured' => false,
                'active_status' => true,
            ]
        );

        // Product 5
        Product::updateOrCreate(
            ['name' => 'Nivea Soft Cream - 100ml'],
            [
                'brand_id' => $niveaBrand->id,
                'category_id' => $skincareCat->id,
                'price' => 120.00,
                'compare_at_price' => null,
                'stock_quantity' => 0, // Intentionally out of stock
                'weight' => 100,
                'weight_unit' => 'ml',
                'is_trending' => false,
                'is_featured' => false,
                'active_status' => true,
            ]
        );
    }
}
