<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_items', function (Blueprint $table) {
            // 1. ถ้ามี Primary Key เดิมอยู่ ให้ลบออกก่อน (เพื่อไม่ให้ซ้ำ)
            // หมายเหตุ: ต้องใช้ try-catch หรือเช็ค เพราะเราไม่รู้ชื่อ PK เดิมที่แน่นอน
            // แต่โดยปกติถ้าเป็น composite key จะไม่มีชื่อ column 'id'
            
            // ลองลบ PK เดิม (ถ้ามี)
            try {
                $table->dropPrimary(); 
            } catch (\Exception $e) {
                // ถ้าไม่มี PK ให้ลบ ก็ปล่อยผ่าน
            }

            // 2. ตรวจสอบและสร้าง id ใหม่
            if (!Schema::hasColumn('rental_items', 'id')) {
                $table->id(); // สร้าง id เป็น Primary Key ใหม่
            }
        });
    }

    public function down(): void
    {
        Schema::table('rental_items', function (Blueprint $table) {
            if (Schema::hasColumn('rental_items', 'id')) {
                $table->dropColumn('id');
            }
            // หมายเหตุ: การ rollback จะไม่คืน PK เดิม เพราะเราไม่รู้ว่าเดิมคืออะไร
        });
    }
};