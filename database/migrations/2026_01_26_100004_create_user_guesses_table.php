<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserGuessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_guesses', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('date')->nullable();
            $table->unsignedBigInteger('game_location_id')->nullable();
            $table->unsignedBigInteger('game_slot_id')->nullable();
            $table->unsignedBigInteger('game_mode_id')->nullable();
            $table->string('guess', 255)->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_guesses');
    }
}
