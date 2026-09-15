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
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('gateway_key')->unique(); // bkash, nagad, rocket, binance, stripe, support_phone
            $table->string('gateway_name'); // e.g. bKash Personal / Merchant
            $table->string('account_number')->nullable(); // phone number or pay ID / wallet
            $table->string('account_type')->default('personal'); // personal, merchant, agent
            $table->string('qr_code_url')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('conversion_rate_bdt_usd', 8, 2)->default(120.00); // 1 USD = 120 BDT
            $table->string('support_phone')->nullable(); // Customer support phone number
            $table->string('support_whatsapp')->nullable();
            $table->timestamps();
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'sender_number')) {
                $table->string('sender_number')->nullable()->after('crypto_txid');
            }
            if (!Schema::hasColumn('invoices', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('sender_number');
            }
            if (!Schema::hasColumn('invoices', 'payment_notes')) {
                $table->text('payment_notes')->nullable()->after('transaction_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['sender_number', 'transaction_id', 'payment_notes']);
        });
    }
};

