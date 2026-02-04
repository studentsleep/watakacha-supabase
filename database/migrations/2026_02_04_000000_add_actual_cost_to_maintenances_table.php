<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('item_maintenances', function (Blueprint $table) {
            // เพิ่มต้นทุนจริงที่จ่ายให้ร้าน (แยกกับ shop_cost ที่อาจจะเป็นราคาประเมิน หรือราคาที่เรียกเก็บลูกค้า)
            $table->decimal('actual_cost', 10, 2)->default(0)->after('shop_cost')->comment('รายจ่ายจริงที่ทางร้านจ่ายไป');
        });
    }

    public function down()
    {
        Schema::table('item_maintenances', function (Blueprint $table) {
            $table->dropColumn('actual_cost');
        });
    }
};
