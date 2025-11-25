<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. ลบตารางเก่าทิ้ง (เพื่อเริ่มใหม่แบบสะอาดหมดจด)
        Schema::dropIfExists('rental_items');

        // 2. สร้างตารางใหม่
        Schema::create('rental_items', function (Blueprint $table) {
            // Primary Key: id (Auto Increment)
            $table->id(); 
            
            // Foreign Keys: เชื่อมกับ rental และ item
            $table->unsignedBigInteger('rental_id');
            $table->unsignedBigInteger('item_id'); 
            
            // ข้อมูลสินค้าในรายการนั้น
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2); // ราคา ณ ตอนเช่า
            
            // ข้อมูลสำหรับการคืน (ค่าปรับ/ความเสียหาย)
            $table->decimal('fine_amount', 10, 2)->default(0)->nullable();
            $table->text('description')->nullable(); // รายละเอียดความเสียหาย

            // Timestamps: สร้าง created_at และ updated_at อัตโนมัติ
            $table->timestamps(); 

            // (Optional) เชื่อม Foreign Key จริงๆ เพื่อความสมบูรณ์ของข้อมูล
            $table->foreign('rental_id')->references('rental_id')->on('rentals')->onDelete('cascade');
            // $table->foreign('item_id')->references('id')->on('items'); // ตรวจสอบว่าตาราง items ใช้ id เป็น PK หรือไม่
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_items');
    }
};