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
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('wedding_category_id')->nullable()->constrained('wedding_categories')->cascadeOnDelete();
            $table->boolean('female_driver')->default(false);
            $table->string('source_latitude');
            $table->string('source_longitude');
            $table->date('date')->default(date('Y-m-d'));
            $table->time('time')->default(now());
            $table->integer('number_of_seats')->default(4);
            $table->float('price');
            $table->text('note')->nullable();
            $table->enum('type', ['private', 'shared'])->default('private');
            $table->boolean('auto_accept')->default(0);
            $table->enum('status', ['pending', 'accepted', 'cancelled', 'delivering','ended'])->default('pending'); 
            $table->text('cancel_reason')->nullable();
            $table->string('reference_key')->nullable();
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
