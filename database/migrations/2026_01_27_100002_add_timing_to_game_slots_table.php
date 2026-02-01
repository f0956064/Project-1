<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimingToGameSlotsTable extends Migration
{
    public function up()
    {
        Schema::table('game_slots', function (Blueprint $table) {
            $table->time('result_time')->nullable()->after('end_time');
            $table->string('off_days', 255)->nullable()->after('result_time');
        });
    }

    public function down()
    {
        Schema::table('game_slots', function (Blueprint $table) {
            $table->dropColumn(['result_time', 'off_days']);
        });
    }
}
