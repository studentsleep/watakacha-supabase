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
        Schema::table('rental_items', function (Blueprint $table) {
            // 1. ปรับ item_id ให้เป็น NULL ได้ (เผื่อแถวนั้นเป็นอุปกรณ์เสริม ไม่มีสินค้าหลัก)
            // หมายเหตุ: ถ้าใช้ Laravel เก่ากว่า v10 อาจต้องลง doctrine/dbal ก่อน
            $table->unsignedBigInteger('item_id')->nullable()->change();

            // 2. เพิ่ม accessory_id ต่อท้าย item_id
            $table->unsignedBigInteger('accessory_id')->nullable()->after('item_id');

            // (Optional) สร้าง Foreign Key เพื่อความสมบูรณ์ของข้อมูล
            // $table->foreign('accessory_id')->references('id')->on('accessories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_items', function (Blueprint $table) {
            // ลบคอลัมน์ออกเมื่อ Rollback
            $table->dropColumn('accessory_id');

            // คืนค่า item_id ให้ห้ามว่างเหมือนเดิม (ถ้าทำได้)
            // $table->unsignedBigInteger('item_id')->nullable(false)->change();
        });
    }
};
