<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('service_category_id')->constrained('service_categories');
            $table->foreignId('provider_id')->nullable()->constrained('service_providers')->nullOnDelete();

            $table->string('status')->default('pending');
            $table->string('booking_type')->default('immediate');
            $table->timestamp('scheduled_at')->nullable();

            $table->decimal('customer_lat', 10, 7);
            $table->decimal('customer_lng', 10, 7);
            $table->text('customer_address');
            $table->decimal('provider_lat', 10, 7)->nullable();
            $table->decimal('provider_lng', 10, 7)->nullable();
            $table->text('customer_notes')->nullable();

            $table->decimal('estimated_price', 10, 2);
            $table->decimal('final_price', 10, 2)->nullable();
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 10, 2)->nullable();
            $table->decimal('provider_earning', 10, 2)->nullable();

            $table->string('payment_method')->default('cash');
            $table->string('payment_status')->default('pending');

            $table->string('cancelled_by')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('customer_id');
            $table->index('provider_id');
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
