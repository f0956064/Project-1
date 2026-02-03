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
        Schema::create('game_settings', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('show_games')->default(1);
            $table->tinyInteger('deposit')->default(1);
            $table->tinyInteger('withdrawal')->default(1);
            $table->timestamps();
        });

        // Seed data
        DB::table('game_settings')->insert([
            'id' => 1,
            'show_games' => 1,
            'deposit' => 1,
            'withdrawal' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_settings');
    }
};
