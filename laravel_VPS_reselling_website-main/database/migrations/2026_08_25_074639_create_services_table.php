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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained();
            $table->string('contabo_instance_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('encrypted_credentials')->nullable();
            $table->enum('status', ['awaiting_provisioning', 'provisioning_failed', 'active', 'suspended', 'terminated']);
            $table->date('next_due_date')->nullable();
            $table->string('billing_cycle');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
