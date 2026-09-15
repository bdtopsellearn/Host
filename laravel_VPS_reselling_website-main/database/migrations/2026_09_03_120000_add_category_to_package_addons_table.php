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
            $table->string('category')->nullable()->after('type'); // e.g. ubuntu, debian, rhel, windows
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_addons', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
