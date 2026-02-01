<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('ifsc_code', 50)->nullable();
            $table->string('paytm_detail', 255)->nullable();
            $table->string('upi_address', 255)->nullable();
            $table->string('google_pay_number', 50)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
}
