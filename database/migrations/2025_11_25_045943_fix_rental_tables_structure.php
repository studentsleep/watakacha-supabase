<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. แก้ไขตาราง rentals (เพิ่มบริการเสริม)
        Schema::table('rentals', function (Blueprint $table) {
            // เช็คก่อนว่ามีคอลัมน์หรือยัง ถ้ายังไม่มีค่อยเพิ่ม
            if (!Schema::hasColumn('rentals', 'makeup_id')) {
                $table->unsignedBigInteger('makeup_id')->nullable()->after('promotion_id');
                $table->foreign('makeup_id')->references('makeup_id')->on('makeup_artists')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('rentals', 'photographer_id')) {
                $table->unsignedBigInteger('photographer_id')->nullable()->after('makeup_id');
                $table->foreign('photographer_id')->references('photographer_id')->on('photographers')->onDelete('set null');
            }

            if (!Schema::hasColumn('rentals', 'package_id')) {
                $table->unsignedBigInteger('package_id')->nullable()->after('photographer_id');
                $table->foreign('package_id')->references('package_id')->on('photographer_packages')->onDelete('set null');
            }
        });

        // 2. แก้ไขตาราง rental_items (เพิ่ม item_id และ timestamps)
        Schema::table('rental_items', function (Blueprint $table) {
            // เพิ่ม item_id (ถ้ายังไม่มี)
            if (!Schema::hasColumn('rental_items', 'item_id')) {
                $table->unsignedBigInteger('item_id')->after('rental_id'); 
                // ถ้าตาราง items ใช้ id เป็น PK ให้ใช้บรรทัดนี้
                $table->foreign('item_id')->references('id')->on('items');
            }

            // เพิ่ม timestamps (created_at, updated_at) ถ้ายังไม่มี
            if (!Schema::hasColumn('rental_items', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        // เขียนเผื่อไว้กรณี rollback (ลบสิ่งที่เพิ่มไป)
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropForeign(['makeup_id']);
            $table->dropForeign(['photographer_id']);
            $table->dropForeign(['package_id']);
            $table->dropColumn(['makeup_id', 'photographer_id', 'package_id']);
        });

        Schema::table('rental_items', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropColumn('item_id');
            $table->dropTimestamps();
        });
    }
};