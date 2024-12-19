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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('wedding_category_id')->constrained('wedding_categories')->cascadeOnDelete()->nullable();
            $table->foreignId('car_brand_id')->constrained('car_brands')->cascadeOnDelete();
            $table->enum('gear_type', ['manual', 'auto']);
            $table->boolean('is_modified')->default(false);
            $table->foreignId('original_car_brand_id')->constrained('car_brands');
            $table->boolean('available')->default(true);
            $table->boolean('more_than_four_seats')->default(true);
            $table->float('rate', 2, 1)->default(0.0);
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
        Schema::dropIfExists('cars');
    }
};
