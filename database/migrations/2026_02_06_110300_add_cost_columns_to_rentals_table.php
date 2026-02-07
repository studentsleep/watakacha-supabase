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
        Schema::table('rentals', function (Blueprint $table) {
            $table->decimal('makeup_cost', 10, 2)->nullable()->comment('ต้นทุนค่าช่างแต่งหน้า');
            $table->decimal('photographer_cost', 10, 2)->nullable()->comment('ต้นทุนค่าช่างภาพ');
            // สถานะการจ่ายเงินให้ช่าง (pending = รอจ่าย/รอกรอก, paid = จ่ายแล้ว/บันทึกแล้ว)
            $table->string('service_cost_status')->default('pending')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            //
        });
    }
};
