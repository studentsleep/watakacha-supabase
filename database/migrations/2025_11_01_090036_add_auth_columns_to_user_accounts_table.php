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
        // นี่คือคำสั่ง "แก้ไขตาราง"
        Schema::table('user_accounts', function (Blueprint $table) {
            // เพิ่มคอลัมน์ที่ Laravel Breeze ต้องการ
            $table->string('password')->after('email'); // <--- เพิ่มคอลัมน์ password
            $table->rememberToken()->after('password');  // <--- เพิ่มคอลัมน์ remember_token
            $table->timestamp('email_verified_at')->nullable()->after('email'); // <--- เพิ่มคอลัมน์ยืนยันอีเมล
        });
    }

    /**
     * Reverse the migrations.
     */
   public function down(): void
    {
        // นี่คือคำสั่ง "ย้อนกลับ" (เผื่อเราทำผิด)
        Schema::table('user_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'password',
                'remember_token',
                'email_verified_at'
            ]);
        });
    }
};
