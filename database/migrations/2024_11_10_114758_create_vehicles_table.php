<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('vehicle_type', ['car', 'motorcycle']);
            $table->foreignId('wedding_category_id')->nullable()->constrained('wedding_categories')->cascadeOnDelete();
            $table->foreignId('car_brand_id')->nullable()->constrained('car_brands')->cascadeOnDelete();
            $table->enum('gear_type', ['manual', 'auto']);
            $table->string('model_year');
            $table->boolean('is_modified')->default(false);
            $table->foreignId('original_car_brand_id')->nullable()->constrained('car_brands');
            $table->boolean('available')->default(true);
            $table->boolean('more_than_four_seats')->default(false);
            $table->string('latitude');
            $table->string('longitude');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
