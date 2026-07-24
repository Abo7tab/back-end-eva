<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('brand_id')
                  ->comment('FK → brands | مفتاح خارجي للبراند');

            $table->unsignedBigInteger('category_id')
                  ->comment('FK → categories | مفتاح خارجي للقسم');

            $table->string('name', 255)
                  ->comment('Product display name | اسم المنتج');

            $table->string('slug', 255)
                  ->comment('SEO-friendly URL identifier | معرف الرابط');

            $table->string('slug_active', 255)
                  ->virtualAs("IF(`deleted_at` IS NULL, `slug`, NULL)")
                  ->nullable()
                  ->comment('Generated: unique slug for active records only | Slug فريد للسجلات النشطة فقط');

            $table->string('sku', 100)
                  ->nullable()
                  ->comment('Stock Keeping Unit | كود المنتج');

            $table->string('sku_active', 100)
                  ->virtualAs("IF(`deleted_at` IS NULL, `sku`, NULL)")
                  ->nullable()
                  ->comment('Generated: unique SKU for active records only | SKU فريد للسجلات النشطة فقط');

            $table->text('short_description')
                  ->nullable()
                  ->comment('Brief summary shown on listing cards | وصف مختصر للبطاقة');

            $table->longText('description')
                  ->nullable()
                  ->comment('Full rich-text product description | الوصف الكامل');

            $table->decimal('weight', 8, 2)
                  ->nullable()
                  ->comment('Product weight or volume | الوزن أو الحجم');

            $table->string('weight_unit', 10)
                  ->nullable()
                  ->default('ml')
                  ->comment('Unit: ml, g, kg, piece | وحدة الحجم أو الوزن');

            $table->decimal('price', 12, 2)
                  ->comment('Current selling price | سعر البيع الحالي');

            $table->decimal('compare_at_price', 12, 2)
                  ->nullable()
                  ->comment('Original price shown crossed-out | السعر الأصلي المشطوب');

            $table->unsignedInteger('stock_quantity')
                  ->default(0)
                  ->comment('Available stock units | الكمية المتاحة');

            $table->string('cover_image_url', 2048)
                  ->nullable()
                  ->comment('Full external URL — Cloudinary | رابط صورة الغلاف الخارجي');

            $table->string('storage_public_id', 255)
                  ->nullable()
                  ->comment('Cloudinary public_id for cover image deletion | معرف Cloudinary للحذف');

            $table->boolean('is_trending')
                  ->default(false)
                  ->comment('Seasonal/trending flag | علامة المنتج الرائج');

            $table->boolean('is_featured')
                  ->default(false)
                  ->comment('Homepage featured flag | علامة المنتج المميز');

            $table->unsignedBigInteger('views_count')
                  ->default(0)
                  ->comment('Page view counter | عداد المشاهدات');

            $table->smallInteger('sort_order')
                  ->unsigned()
                  ->default(0)
                  ->comment('Manual display order | ترتيب العرض اليدوي');

            $table->boolean('active_status')
                  ->default(true)
                  ->comment('Visible and purchasable? | ظاهر ومتاح للشراء؟');

            $table->string('seo_title', 255)
                  ->nullable()
                  ->comment('Custom SEO page title | عنوان SEO مخصص');

            $table->string('meta_description', 320)
                  ->nullable()
                  ->comment('Meta description for search engines | وصف محركات البحث');

            $table->softDeletes()
                  ->comment('Soft delete timestamp | وقت الحذف المؤقت');

            $table->timestamps();

            $table->unique('slug_active', 'products_slug_active_unique');
            $table->unique('sku_active', 'products_sku_active_unique');
            $table->index('brand_id', 'products_brand_id_index');
            $table->index('category_id', 'products_category_id_index');
            $table->index('deleted_at', 'products_deleted_at_index');
            $table->index(
                ['active_status', 'is_trending', 'is_featured', 'sort_order'],
                'products_storefront_index'
            );
            $table->index('views_count', 'products_views_count_index');

            $table->foreign('brand_id', 'products_brand_id_foreign')
                  ->references('id')
                  ->on('brands')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreign('category_id', 'products_category_id_foreign')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });

        // CHECK constraints — defense-in-depth (MariaDB 10.4 may not enforce,
        // but we define them for documentation and forward-compatibility).
        // Actual validation is always enforced in FormRequests per §3.4.
        DB::statement('ALTER TABLE `products` ADD CHECK (`price` >= 0)');
        DB::statement('ALTER TABLE `products` ADD CHECK (`stock_quantity` >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
