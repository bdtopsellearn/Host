<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('status');
            $table->string('crypto_network')->nullable()->after('payment_method');
            $table->string('crypto_wallet_address')->nullable()->after('crypto_network');
            $table->string('crypto_txid')->nullable()->after('crypto_wallet_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'crypto_network', 'crypto_wallet_address', 'crypto_txid']);
        });
    }
};
