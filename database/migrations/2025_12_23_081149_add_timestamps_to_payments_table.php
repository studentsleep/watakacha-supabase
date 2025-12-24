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
        // สร้างตาราง payments ใหม่
        Schema::create('payments', function (Blueprint $table) {
            // 1. Primary Key ชื่อ payment_id
            $table->id('payment_id'); 

            // 2. Foreign Key เชื่อมกับตาราง rentals
            // (ใช้ unsignedBigInteger เพื่อให้ตรงกับ format ของ id ส่วนใหญ่)
            $table->unsignedBigInteger('rental_id')->nullable();

            // 3. ข้อมูลการจ่ายเงิน
            $table->decimal('amount', 10, 2); // ยอดเงิน (ทศนิยม 2 ตำแหน่ง)
            $table->string('payment_method'); // cash, transfer, etc.
            $table->string('type');           // deposit, fine, fine_remaining
            $table->string('status')->default('paid'); // paid, pending
            
            // 4. วันที่จ่ายเงิน (ระบุเอง)
            $table->dateTime('payment_date');

            // 5. Timestamps (สร้าง created_at และ updated_at ให้อัตโนมัติ)
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};