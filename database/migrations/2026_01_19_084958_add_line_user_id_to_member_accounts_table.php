<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('rental_items', function (Blueprint $table) {
            // 1. ปรับ item_id ให้เป็น Nullable (เผื่อแถวนั้นเป็น Accessory)
            $table->unsignedBigInteger('item_id')->nullable()->change();

            // 2. เพิ่ม accessory_id ต่อท้าย item_id
            $table->unsignedBigInteger('accessory_id')->nullable()->after('item_id');

            // (Optional) สร้าง Foreign Key ถ้าต้องการ
            // $table->foreign('accessory_id')->references('id')->on('accessories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('rental_items', function (Blueprint $table) {
            $table->dropColumn('accessory_id');
            // $table->unsignedBigInteger('item_id')->nullable(false)->change(); // คืนค่าเดิม (ถ้าทำได้)
        });
    }
};
