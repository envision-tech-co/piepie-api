<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->unique()->constrained('service_providers')->cascadeOnDelete();
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->decimal('heading', 5, 2)->nullable();
            $table->decimal('speed_kmh', 5, 2)->nullable();
            $table->decimal('accuracy_meters', 8, 2)->nullable();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->timestamps();

            $table->index(['provider_id', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_locations');
    }
};
