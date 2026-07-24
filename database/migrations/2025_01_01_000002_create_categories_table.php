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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('parent_id')
                  ->nullable()
                  ->default(null)
                  ->comment('Self-referencing FK for sub-categories | للأقسام الفرعية');

            $table->string('name', 255)
                  ->comment('Category display name | اسم القسم');

            $table->string('slug', 255)
                  ->comment('SEO-friendly URL identifier | معرف الرابط');

            $table->string('slug_active', 255)
                  ->virtualAs("IF(`deleted_at` IS NULL, `slug`, NULL)")
                  ->nullable()
                  ->comment('Generated: unique slug for active records only | Slug فريد للسجلات النشطة فقط');

            $table->text('description')
                  ->nullable()
                  ->comment('Category description for SEO | وصف القسم');

            $table->string('cover_image_url', 2048)
                  ->nullable()
                  ->comment('Full external URL — Cloudinary | رابط صورة الغلاف الخارجي');

            $table->string('storage_public_id', 255)
                  ->nullable()
                  ->comment('Cloudinary public_id for deletion | معرف Cloudinary للحذف');

            $table->string('seo_title', 255)
                  ->nullable()
                  ->comment('Custom SEO page title | عنوان SEO مخصص');

            $table->string('meta_description', 320)
                  ->nullable()
                  ->comment('Meta description for search engines | وصف محركات البحث');

            $table->boolean('active_status')
                  ->default(true)
                  ->comment('Visible on storefront? | ظاهر بالموقع؟');

            $table->smallInteger('sort_order')
                  ->unsigned()
                  ->default(0)
                  ->comment('Manual display order | ترتيب العرض اليدوي');

            $table->softDeletes()
                  ->comment('Soft delete timestamp | وقت الحذف المؤقت');

            $table->timestamps();

            $table->unique('slug_active', 'categories_slug_active_unique');
            $table->index('parent_id', 'categories_parent_id_index');
            $table->index(['active_status', 'sort_order'], 'categories_active_sort_index');
            $table->index('deleted_at', 'categories_deleted_at_index');

            $table->foreign('parent_id', 'categories_parent_id_foreign')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
