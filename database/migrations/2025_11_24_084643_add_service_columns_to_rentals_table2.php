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
        Schema::table('rentals', function (Blueprint $table) {
            // 1. สร้างคอลัมน์ (ต้องเป็น unsignedBigInteger เพื่อให้ตรงกับ PK ของตารางหลัก)
            $table->unsignedBigInteger('makeup_id')->nullable()->after('promotion_id');
            $table->unsignedBigInteger('photographer_id')->nullable()->after('makeup_id');
            $table->unsignedBigInteger('package_id')->nullable()->after('photographer_id');

            // 2. สร้าง Foreign Key เชื่อมโยง
            // เชื่อม makeup_id ไปที่ตาราง makeup_artists
            $table->foreign('makeup_id')
                  ->references('makeup_id')
                  ->on('makeup_artists')
                  ->onDelete('set null'); // ถ้าช่างแต่งหน้าถูกลบ ให้ช่องนี้ในใบเช่ากลายเป็น NULL (ประวัติการเช่าไม่หาย)

            // เชื่อม photographer_id ไปที่ตาราง photographers
            $table->foreign('photographer_id')
                  ->references('photographer_id')
                  ->on('photographers')
                  ->onDelete('set null');

            // เชื่อม package_id ไปที่ตาราง photographer_packages
            $table->foreign('package_id')
                  ->references('package_id')
                  ->on('photographer_packages')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            // เวลาลบ ต้องลบ FK ก่อน แล้วค่อยลบคอลัมน์
            
            // 1. ลบ Foreign Key (ชื่อ Default คือ ตาราง_คอลัมน์_foreign)
            $table->dropForeign(['makeup_id']);
            $table->dropForeign(['photographer_id']);
            $table->dropForeign(['package_id']);

            // 2. ลบคอลัมน์
            $table->dropColumn(['makeup_id', 'photographer_id', 'package_id']);
        });
    }
};