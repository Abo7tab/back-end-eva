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
        Schema::create('order_log_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_log_id')
                  ->comment('FK → order_logs | مفتاح خارجي للطلب');

            $table->unsignedBigInteger('product_id')
                  ->nullable()
                  ->comment('FK → products, nullable if deleted | FK للمنتج');

            $table->string('product_name_snapshot', 255)
                  ->comment('Product name at order time | اسم المنتج وقت الطلب');

            $table->string('product_slug_snapshot', 255)
                  ->nullable()
                  ->comment('Product slug at order time | Slug وقت الطلب');

            $table->string('product_image_snapshot', 2048)
                  ->nullable()
                  ->comment('Product image URL at order time | رابط الصورة وقت الطلب');

            $table->unsignedInteger('quantity')
                  ->comment('Ordered quantity | الكمية المطلوبة');

            $table->decimal('unit_price', 12, 2)
                  ->comment('Unit price at order time | سعر الوحدة وقت الطلب');

            $table->decimal('line_total', 12, 2)
                  ->comment('quantity × unit_price | الكمية × سعر الوحدة');

            $table->timestamps();

            $table->index('order_log_id', 'order_log_items_order_log_id_index');
            $table->index('product_id', 'order_log_items_product_id_index');

            $table->foreign('order_log_id', 'order_log_items_order_log_id_foreign')
                  ->references('id')
                  ->on('order_logs')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('product_id', 'order_log_items_product_id_foreign')
                  ->references('id')
                  ->on('products')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });

        // CHECK constraints — defense-in-depth (MariaDB 10.4 may not enforce,
        // but we define them for documentation and forward-compatibility).
        // Actual validation is always enforced in FormRequests per §3.4.
        DB::statement('ALTER TABLE `order_log_items` ADD CHECK (`quantity` > 0)');
        DB::statement('ALTER TABLE `order_log_items` ADD CHECK (`unit_price` >= 0)');
        DB::statement('ALTER TABLE `order_log_items` ADD CHECK (`line_total` >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_log_items');
    }
};
