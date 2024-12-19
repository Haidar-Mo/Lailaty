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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('cars')->cascadeOnDelete()->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('wedding_category_id')->constrained('wedding_categories')->cascadeOnDelete()->nullable();
            $table->float('price');
            $table->integer('number_of_seats')->default(4);
            $table->boolean('female_driver')->default(false);
            $table->text('note')->nullable();
            $table->enum('type',['private','shared'])->default('private');
            $table->enum('status', ['pending', 'accepted', 'cancelled'])->default('pending');
            $table->text('cancel_reson')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
