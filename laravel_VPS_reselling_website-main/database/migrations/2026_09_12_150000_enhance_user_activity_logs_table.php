<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_activity_logs')) {
            return;
        }

        Schema::table('user_activity_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('user_activity_logs', 'admin_id')) {
                $table->foreignId('admin_id')->nullable()->after('user_id')->constrained('admins')->nullOnDelete();
            }
            if (!Schema::hasColumn('user_activity_logs', 'actor_type')) {
                $table->string('actor_type', 20)->default('user')->after('admin_id')->index();
            }
            if (!Schema::hasColumn('user_activity_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('user_activity_logs', 'device')) {
                $table->string('device', 50)->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('user_activity_logs', 'browser')) {
                $table->string('browser', 50)->nullable()->after('device');
            }
            if (!Schema::hasColumn('user_activity_logs', 'platform')) {
                $table->string('platform', 50)->nullable()->after('browser');
            }
            if (!Schema::hasColumn('user_activity_logs', 'request_method')) {
                $table->string('request_method', 10)->nullable()->after('platform');
            }
            if (!Schema::hasColumn('user_activity_logs', 'request_url')) {
                $table->text('request_url')->nullable()->after('request_method');
            }
            if (!Schema::hasColumn('user_activity_logs', 'properties')) {
                $table->json('properties')->nullable()->after('request_url');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('user_activity_logs')) {
            return;
        }

        Schema::table('user_activity_logs', function (Blueprint $table) {
            $dropCols = [];
            foreach (['actor_type', 'user_agent', 'device', 'browser', 'platform', 'request_method', 'request_url', 'properties'] as $col) {
                if (Schema::hasColumn('user_activity_logs', $col)) {
                    $dropCols[] = $col;
                }
            }

            if (!empty($dropCols)) {
                $table->dropColumn($dropCols);
            }

            if (Schema::hasColumn('user_activity_logs', 'admin_id')) {
                $table->dropConstrainedForeignId('admin_id');
            }
        });
    }
};
