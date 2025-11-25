<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_items', function (Blueprint $table) {
            // ตรวจสอบทีละตัว: ถ้าไม่มี created_at ค่อยสร้าง
            if (!Schema::hasColumn('rental_items', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }

            // ตรวจสอบทีละตัว: ถ้าไม่มี updated_at ค่อยสร้าง
            if (!Schema::hasColumn('rental_items', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('rental_items', function (Blueprint $table) {
            // เวลา Rollback ก็เช็คก่อนลบเช่นกัน เพื่อความปลอดภัย
            if (Schema::hasColumn('rental_items', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('rental_items', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
};