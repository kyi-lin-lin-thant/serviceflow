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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                    ->constrained('users')
                    ->restrictOnDelete();

            $table->foreignId('service_id')
                    ->constrained('services')
                    ->restrictOnDelete();

            $table->date('booking_date');
            $table->time('booking_time');
            $table->string('address');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
