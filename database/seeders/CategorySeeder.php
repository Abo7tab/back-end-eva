<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Catalog\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $sunProtection = Category::updateOrCreate(
            ['name' => 'Sun Protection'],
            [
                'parent_id' => null,
                'sort_order' => 1,
            ]
        );

        Category::updateOrCreate(
            ['name' => 'Skincare'],
            [
                'parent_id' => null,
                'sort_order' => 2,
            ]
        );

        Category::updateOrCreate(
            ['name' => 'Face Sunblock'],
            [
                'parent_id' => $sunProtection->id,
                'sort_order' => 1,
            ]
        );
    }
}
