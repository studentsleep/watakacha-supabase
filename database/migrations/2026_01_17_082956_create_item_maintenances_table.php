<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('item_maintenances', function (Blueprint $table) {
            $table->id(); // PK ของตารางนี้ใช้ id ปกติได้

            // 1. เชื่อมกับ Items (สมมติว่าตาราง items ใช้ id ปกติ)
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');

            // 2. ✅ เชื่อมกับ Rentals (ต้องระบุ rental_id)
            $table->unsignedBigInteger('rental_id')->nullable();
            $table->foreign('rental_id')->references('rental_id')->on('rentals')->onDelete('set null');

            // 3. ✅ เชื่อมกับ CareShops (ต้องระบุ care_shop_id)
            $table->unsignedBigInteger('care_shop_id')->nullable();
            $table->foreign('care_shop_id')->references('care_shop_id')->on('care_shops')->onDelete('set null');

            $table->string('type')->default('repair'); // repair, laundry
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->text('damage_description')->nullable(); // ดึงมาจากหน้าคืน
            $table->decimal('shop_cost', 10, 2)->default(0);

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_maintenances');
    }
};
