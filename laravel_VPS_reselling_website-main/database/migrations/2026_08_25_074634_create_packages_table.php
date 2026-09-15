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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('category', ['vps', 'rdp']);
            $table->json('specs')->nullable(); // CPU, RAM, Storage, etc
            $table->decimal('price_monthly', 10, 2)->nullable();
            $table->decimal('price_quarterly', 10, 2)->nullable();
            $table->decimal('price_semi_annually', 10, 2)->nullable();
            $table->decimal('price_annually', 10, 2)->nullable();
            $table->decimal('setup_fee', 10, 2)->default(0);
            $table->string('contabo_product_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
