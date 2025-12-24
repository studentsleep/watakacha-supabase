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
        Schema::create('rental_accessories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained('rentals', 'rental_id')->onDelete('cascade');
            $table->foreignId('accessory_id')->constrained('accessories');
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // ราคาต่อหน่วย ณ ตอนเช่า
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_accessories');
    }
};
