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
        Schema::table('package_addons', function (Blueprint $table) {
            $table->string('api_identifier')->nullable()->after('value');
            $table->boolean('is_out_of_stock')->default(false)->after('is_global');
            $table->boolean('is_enabled')->default(true)->after('is_out_of_stock');
            $table->integer('sort_order')->default(0)->after('billing_cycle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_addons', function (Blueprint $table) {
            $table->dropColumn(['api_identifier', 'is_out_of_stock', 'is_enabled', 'sort_order']);
        });
    }
};
