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
        Schema::table('photographers', function (Blueprint $table) {
            $table->string('lineid')->nullable()->after('email');
        });

        Schema::table('makeup_artists', function (Blueprint $table) {
            $table->string('lineid')->nullable()->after('email');
        });
    }

    public function down()
    {
        Schema::table('photographers', function (Blueprint $table) {
            $table->dropColumn('lineid');
        });

        Schema::table('makeup_artists', function (Blueprint $table) {
            $table->dropColumn('lineid');
        });
    }
};
