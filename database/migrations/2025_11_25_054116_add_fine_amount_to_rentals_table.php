<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            // เก็บยอดค่าปรับรวม (Overdue + Damage)
            $table->decimal('fine_amount', 10, 2)->default(0)->after('total_amount');
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn('fine_amount');
        });
    }
};