<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')->nullable()->constrained('service_categories')->nullOnDelete();
            $table->decimal('rate', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('admins');
            $table->timestamps();

            $table->unique('service_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_settings');
    }
};
