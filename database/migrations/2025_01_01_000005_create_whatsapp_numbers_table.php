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
        Schema::create('whatsapp_numbers', function (Blueprint $table) {
            $table->id();

            $table->string('phone_number', 32)
                  ->comment('International format +201XXXXXXXXX | رقم دولي');

            $table->string('display_name', 255)
                  ->nullable()
                  ->comment('Agent label for admin UI | اسم توضيحي للإدمن');

            $table->boolean('is_active')
                  ->default(true)
                  ->comment('In round-robin routing? | يدخل في التوزيع الدوري؟');

            $table->unsignedBigInteger('order_count')
                  ->default(0)
                  ->comment('Cumulative assigned orders | عداد الطلبات التراكمي');

            $table->dateTime('last_assigned_at')
                  ->nullable()
                  ->default(null)
                  ->comment('Last order assignment time | وقت آخر تعيين طلب');

            $table->timestamps();

            $table->unique('phone_number', 'whatsapp_numbers_phone_unique');
            $table->index(
                ['is_active', 'last_assigned_at', 'order_count', 'id'],
                'whatsapp_numbers_distribution_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_numbers');
    }
};
