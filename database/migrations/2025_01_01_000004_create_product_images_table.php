<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id')
                  ->comment('FK → products | مفتاح خارجي للمنتج');

            $table->string('image_url', 2048)
                  ->comment('Full external URL — Cloudinary | رابط الصورة الخارجي');

            $table->string('storage_public_id', 255)
                  ->nullable()
                  ->comment('Cloudinary public_id for deletion | معرف Cloudinary للحذف');

            $table->string('alt_text', 255)
                  ->nullable()
                  ->comment('SEO alt text | نص بديل للصورة');

            $table->smallInteger('sort_order')
                  ->unsigned()
                  ->default(0)
                  ->comment('Display order in gallery | ترتيب الظهور في المعرض');

            $table->timestamps();

            $table->index(['product_id', 'sort_order'], 'product_images_product_sort_index');

            $table->foreign('product_id', 'product_images_product_id_foreign')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
