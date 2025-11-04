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
    Schema::create('users', function (Blueprint $table) {
        $table->id(); // นี่คือ Primary Key (เหมือน member_id)

        // --- ข้อมูลโปรไฟล์ ---
        $table->string('first_name');
        $table->string('last_name');
        $table->string('username')->unique(); // <--- ใช้สำหรับ Login และต้องไม่ซ้ำ
        $table->string('email')->unique(); // <--- ยังคงเก็บ email แต่ไม่ใช้ login
        $table->timestamp('email_verified_at')->nullable();
        $table->string('tel')->nullable(); // <--- เพิ่มเบอร์โทร

        // --- ข้อมูลระบบ ---
        $table->string('password'); // <--- รหัสผ่านที่เข้ารหัส (Hashed)
        $table->integer('points')->default(0); // <--- เพิ่มแต้ม
        $table->string('status')->default('active'); // <--- เพิ่มสถานะ
        $table->bigInteger('transaction_id')->nullable(); // <--- เพิ่ม transaction_id

        $table->rememberToken();
        $table->timestamps(); // (created_at, updated_at)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
