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
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('recurring_amount', 10, 2)->default(0.00)->after('billing_cycle');
            $table->json('specs_snapshot')->nullable()->after('recurring_amount');
            $table->json('active_addons')->nullable()->after('specs_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['recurring_amount', 'specs_snapshot', 'active_addons']);
        });
    }
};
