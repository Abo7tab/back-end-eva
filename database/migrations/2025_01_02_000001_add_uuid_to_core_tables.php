<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private array $tables = [
        'brands',
        'categories',
        'products',
        'product_images',
        'whatsapp_numbers',
        'order_logs',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->char('uuid', 36)->nullable()->after('id');
                $table->unique('uuid', "{$tableName}_uuid_unique");
            });

            // Backfill
            DB::table($tableName)->whereNull('uuid')->orderBy('id')->each(function ($row) use ($tableName) {
                DB::table($tableName)->where('id', $row->id)->update([
                    'uuid' => (string) Str::uuid(),
                ]);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropUnique("{$tableName}_uuid_unique");
                $table->dropColumn('uuid');
            });
        }
    }
};
