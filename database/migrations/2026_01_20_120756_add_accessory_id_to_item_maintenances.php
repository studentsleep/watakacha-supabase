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
        Schema::table('item_maintenances', function (Blueprint $table) {
            // ทำให้ item_id เป็น null ได้ (เผื่อเป็นงานซ่อมอุปกรณ์เสริมอย่างเดียว)
            $table->unsignedBigInteger('item_id')->nullable()->change();

            // เพิ่ม accessory_id
            $table->unsignedBigInteger('accessory_id')->nullable()->after('item_id');
        });
    }

    public function down()
    {
        Schema::table('item_maintenances', function (Blueprint $table) {
            $table->dropColumn('accessory_id');
            // $table->unsignedBigInteger('item_id')->nullable(false)->change();
        });
    }
};
