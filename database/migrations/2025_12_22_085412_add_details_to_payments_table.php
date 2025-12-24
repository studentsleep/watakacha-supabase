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
        Schema::table('payments', function (Blueprint $table) {
            // เพิ่ม column 'type' เพื่อระบุประเภท: deposit(มัดจำ), remaining(ส่วนที่เหลือ), fine(ค่าปรับ)
            // กำหนด default เป็น 'remaining' เพื่อให้ข้อมูลเก่าไม่ error
            if (!Schema::hasColumn('payments', 'type')) {
                $table->string('type')->default('remaining')->after('amount')->comment('deposit, remaining, fine');
            }

            // เพิ่ม column 'proof_image' เก็บ path รูปสลิป (อนุญาตให้ว่างได้)
            if (!Schema::hasColumn('payments', 'proof_image')) {
                $table->string('proof_image')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('payments', 'proof_image')) {
                $table->dropColumn('proof_image');
            }
        });
    }
};