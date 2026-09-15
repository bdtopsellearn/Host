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
            $table->string('type')->after('package_id')->default('feature'); // os, region, storage, feature
            $table->string('value')->after('name')->nullable();
            $table->boolean('is_global')->after('id')->default(true);
            $table->foreignId('package_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('package_addons')->whereNull('package_id')->delete();

        $columnsToDrop = array_filter(['type', 'value', 'is_global'], function ($col) {
            return Schema::hasColumn('package_addons', $col);
        });

        if (!empty($columnsToDrop)) {
            Schema::table('package_addons', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }

        try {
            Schema::table('package_addons', function (Blueprint $table) {
                $table->foreignId('package_id')->nullable(false)->change();
            });
        } catch (\Throwable $e) {
            // Reverting nullability can be skipped if table is about to be dropped or column already in target state
        }
    }
};
