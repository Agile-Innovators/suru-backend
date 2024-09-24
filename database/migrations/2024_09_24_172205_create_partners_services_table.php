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
        Schema::create('partners_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partner_profiles')->onDelete('cascade');
            $table->decimal('price', 10, 2)->nullable(); 
            $table->decimal('price_max', 10, 2)->nullable();
            $table->foreignId('business_service_id')->constrained('business_services')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners_services');
    }
};
