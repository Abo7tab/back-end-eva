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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            $table->string('setting_key', 255)
                  ->comment('Unique setting identifier | معرف الإعداد');

            $table->text('setting_value')
                  ->nullable()
                  ->comment('Stored value: text/HTML/JSON | القيمة المخزنة');

            $table->string('setting_group', 255)
                  ->default('general')
                  ->comment('Admin UI group: seo, general, whatsapp | تصنيف للوحة التحكم');

            $table->string('value_type', 50)
                  ->default('text')
                  ->comment('Input hint: text, textarea, image, json | نوع حقل الإدخال');

            $table->timestamps();

            $table->unique('setting_key', 'site_settings_key_unique');
            $table->index('setting_group', 'site_settings_group_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
