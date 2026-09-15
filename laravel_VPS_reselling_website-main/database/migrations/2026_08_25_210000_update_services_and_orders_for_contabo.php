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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 50)->default('pending_approval')->change();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('status', 50)->default('awaiting_provisioning')->change();
            $table->string('server_name')->nullable()->after('contabo_instance_id');
            $table->string('default_user')->default('root')->after('server_name');
            $table->string('os_image')->nullable()->after('default_user');
            $table->string('region')->default('EU')->after('os_image');
            $table->string('cpu_cores')->nullable()->after('region');
            $table->string('ram_size')->nullable()->after('cpu_cores');
            $table->string('disk_size')->nullable()->after('ram_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'server_name',
                'default_user',
                'os_image',
                'region',
                'cpu_cores',
                'ram_size',
                'disk_size',
            ]);
        });
    }
};
