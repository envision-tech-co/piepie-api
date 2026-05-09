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
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone', 20)->unique();
            $table->string('profile_photo')->nullable();
            $table->enum('vehicle_type', ['car', 'motorcycle', 'truck', 'other'])->nullable();
            $table->string('service_speciality')->nullable();
            $table->string('id_document_path')->nullable();
            $table->enum('language', ['en', 'ar', 'ku'])->default('en');
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->boolean('is_online')->default(false);
            $table->decimal('overall_rating', 3, 2)->default(0.00);
            $table->integer('total_jobs')->default(0);
            $table->timestamp('phone_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            $table->index('phone');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};
