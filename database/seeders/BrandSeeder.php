<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Catalog\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        Brand::updateOrCreate(
            ['name' => 'EVA Cosmetics'],
            [
                'description' => 'Egyptian leading cosmetics brand',
                'active_status' => true,
                'sort_order' => 1,
            ]
        );

        Brand::updateOrCreate(
            ['name' => 'Nivea'],
            [
                'description' => 'Global skincare brand',
                'active_status' => true,
                'sort_order' => 2,
            ]
        );
    }
}
