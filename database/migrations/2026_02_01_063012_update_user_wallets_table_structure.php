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
        // Check if 'amount' column exists and rename it to 'wallet_balance'
        $hasAmount = DB::select("SHOW COLUMNS FROM user_wallets LIKE 'amount'");
        if (!empty($hasAmount)) {
            DB::statement("ALTER TABLE user_wallets CHANGE COLUMN amount wallet_balance DECIMAL(10,2) DEFAULT 0.00");
        }
        
        // Make user_id non-nullable
        DB::statement("ALTER TABLE user_wallets MODIFY COLUMN user_id BIGINT UNSIGNED NOT NULL");
        
        // Add max_withdrawal column if it doesn't exist
        $hasMaxWithdrawal = DB::select("SHOW COLUMNS FROM user_wallets LIKE 'max_withdrawal'");
        if (empty($hasMaxWithdrawal)) {
            DB::statement("ALTER TABLE user_wallets ADD COLUMN max_withdrawal INT DEFAULT 5 AFTER wallet_balance");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reverse: rename wallet_balance back to amount
        $hasWalletBalance = DB::select("SHOW COLUMNS FROM user_wallets LIKE 'wallet_balance'");
        if (!empty($hasWalletBalance)) {
            DB::statement("ALTER TABLE user_wallets CHANGE COLUMN wallet_balance amount DECIMAL(10,2) DEFAULT 0.00");
        }
        
        // Make user_id nullable again
        DB::statement("ALTER TABLE user_wallets MODIFY COLUMN user_id BIGINT UNSIGNED NULL");
        
        // Drop max_withdrawal column
        $hasMaxWithdrawal = DB::select("SHOW COLUMNS FROM user_wallets LIKE 'max_withdrawal'");
        if (!empty($hasMaxWithdrawal)) {
            DB::statement("ALTER TABLE user_wallets DROP COLUMN max_withdrawal");
        }
    }
};
