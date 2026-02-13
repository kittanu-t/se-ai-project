<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // เพิ่มคอลัมน์ตามสคีมา
            $table->enum('role', ['admin', 'staff', 'user'])
                  ->default('user')
                  ->after('password');
            $table->string('phone', 30)->nullable()->after('role');
            $table->boolean('active')->default(true)->after('phone');

            // หากยังไม่ตั้ง unique ให้ email (ปกติ Laravel ตั้งอยู่แล้ว)
            // $table->unique('email');
            //
            // **ไม่แนะนำ** แก้ความยาว email เป็น 150 หลัง migrate แล้ว
            // เพราะต้องใช้ doctrine/dbal และ ALTER COLUMN
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'active']);
        });
    }
};