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
        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();

            $table->string('reference_code', 30)
                  ->comment('Human-readable order ID e.g. GB-202506-00001 | رقم الطلب');

            $table->string('reference_code_active', 30)
                  ->virtualAs("IF(`deleted_at` IS NULL, `reference_code`, NULL)")
                  ->nullable()
                  ->comment('Generated: unique reference for active records only | رقم مرجعي فريد للسجلات النشطة');

            $table->string('customer_name', 255)
                  ->nullable()
                  ->comment('Customer name from checkout popup | اسم العميل');

            $table->string('customer_phone', 32)
                  ->nullable()
                  ->comment('Customer phone from checkout popup | رقم العميل');

            $table->unsignedInteger('total_items')
                  ->comment('Total quantity across all items | إجمالي عدد القطع');

            $table->decimal('subtotal', 12, 2)
                  ->comment('Sum before adjustments | المجموع قبل التعديلات');

            $table->decimal('total_price', 12, 2)
                  ->comment('Final total | الإجمالي النهائي');

            $table->unsignedBigInteger('whatsapp_number_id')
                  ->nullable()
                  ->comment('FK → whatsapp_numbers, SET NULL if deleted | FK لمجموعة الأرقام');

            $table->string('assigned_phone_number', 32)
                  ->comment('Frozen phone snapshot at order time | نسخة ثابتة من الرقم');

            $table->text('checkout_message')
                  ->nullable()
                  ->comment('Exact WhatsApp message generated | نص رسالة الواتساب');

            $table->enum('status', ['pending', 'redirected', 'failed'])
                  ->default('pending')
                  ->comment('Order status | حالة الطلب');

            $table->softDeletes()
                  ->comment('Soft delete timestamp | وقت الحذف المؤقت');

            $table->timestamps();

            $table->unique('reference_code_active', 'order_logs_reference_active_unique');
            $table->index('whatsapp_number_id', 'order_logs_whatsapp_number_index');
            $table->index(['status', 'created_at'], 'order_logs_status_created_index');
            $table->index('customer_phone', 'order_logs_customer_phone_index');
            $table->index('deleted_at', 'order_logs_deleted_at_index');

            $table->foreign('whatsapp_number_id', 'order_logs_whatsapp_number_id_foreign')
                  ->references('id')
                  ->on('whatsapp_numbers')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_logs');
    }
};
