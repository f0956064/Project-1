<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRulesToGameModesTable extends Migration
{
    public function up()
    {
        Schema::table('game_modes', function (Blueprint $table) {
            $table->string('type', 50)->nullable()->after('name');
            $table->decimal('play_amount', 10, 2)->default(10)->after('type');
            $table->decimal('win_amount', 10, 2)->default(0)->after('play_amount');
            $table->decimal('min_bet', 10, 2)->default(5)->after('win_amount');
            $table->decimal('max_bet', 10, 2)->default(100)->after('min_bet');
            $table->unsignedTinyInteger('digit_length')->default(1)->after('max_bet');
        });
    }

    public function down()
    {
        Schema::table('game_modes', function (Blueprint $table) {
            $table->dropColumn(['type', 'play_amount', 'win_amount', 'min_bet', 'max_bet', 'digit_length']);
        });
    }
}
