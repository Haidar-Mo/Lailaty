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
            $table->string('license_plate');
            $table->string('model_year');
            $table->foreignId('car_brand_id')->nullable()->constrained('car_brands')->cascadeOnDelete();
            $table->boolean('is_modified')->default(false);
            $table->foreignId('original_car_brand_id')->nullable()->constrained('car_brands')->nullOnDelete();
            $table->enum('gear_type', ['manual', 'auto'])->nullable();
            $table->string('color')->nullable();
            $table->boolean('more_than_four_seats')->default(false);
            $table->boolean('is_comfort')->default(false);
            $table->foreignId('wedding_category_id')->nullable()->constrained('wedding_categories')->cascadeOnDelete();
            $table->boolean('available')->default(true);
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
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
