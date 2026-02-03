<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_slot_results', function (Blueprint $table) {
            $table->unsignedBigInteger('game_location_id')->nullable()->after('game_slot_id');
            $table->unsignedBigInteger('game_mode_id')->nullable()->after('game_location_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_slot_results', function (Blueprint $table) {
            $table->dropColumn(['game_location_id', 'game_mode_id']);
        });
    }
};
