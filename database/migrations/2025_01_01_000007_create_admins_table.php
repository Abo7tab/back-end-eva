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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();

            $table->string('name', 255)
                  ->comment('Admin full name | الاسم الكامل');

            $table->string('email', 255)
                  ->comment('Login email | البريد الإلكتروني للدخول');

            $table->timestamp('email_verified_at')
                  ->nullable()
                  ->comment('Email verification timestamp | وقت التحقق من البريد');

            $table->string('password', 255)
                  ->comment('Bcrypt hashed password | كلمة المرور المشفرة');

            $table->rememberToken();

            $table->timestamps();

            $table->unique('email', 'admins_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
