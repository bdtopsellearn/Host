<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // Explicitly drop PostgreSQL check constraints created by initial enum column definitions
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check');
            DB::statement('ALTER TABLE invoices DROP CONSTRAINT IF EXISTS invoices_status_check');
            DB::statement('ALTER TABLE services DROP CONSTRAINT IF EXISTS services_status_check');

            // Dynamically drop any remaining check constraints mentioning 'status' on orders, invoices, and services
            DB::statement("
                DO $$ 
                DECLARE 
                    r RECORD;
                BEGIN
                    FOR r IN (
                        SELECT conname, relname 
                        FROM pg_constraint c 
                        JOIN pg_class t ON c.conrelid = t.oid 
                        WHERE c.contype = 'c' 
                          AND t.relname IN ('orders', 'invoices', 'services')
                          AND conname LIKE '%status%'
                    ) LOOP
                        EXECUTE 'ALTER TABLE \"' || r.relname || '\" DROP CONSTRAINT IF EXISTS \"' || r.conname || '\"';
                    END LOOP;
                END $$;
            ");
        } elseif ($driver === 'mysql') {
            try {
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME, TABLE_NAME 
                    FROM information_schema.TABLE_CONSTRAINTS 
                    WHERE CONSTRAINT_TYPE = 'CHECK' 
                      AND TABLE_SCHEMA = DATABASE() 
                      AND TABLE_NAME IN ('orders', 'invoices', 'services')
                      AND CONSTRAINT_NAME LIKE '%status%'
                ");
                foreach ($constraints as $chk) {
                    DB::statement("ALTER TABLE `{$chk->TABLE_NAME}` DROP CHECK `{$chk->CONSTRAINT_NAME}`");
                }
            } catch (\Throwable $e) {
                // Ignore if MySQL version does not use CHECK constraints or query fails
            }
        }

        // Ensure status columns are standardized as VARCHAR(50) with default 'pending'
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 50)->default('pending')->change();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('status', 50)->default('pending')->change();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('status', 50)->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Status remains a flexible VARCHAR column across application lifecycles
    }
};
