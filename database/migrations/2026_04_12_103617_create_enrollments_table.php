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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->enum('type', ['student', 'firm']);

            $table->enum('status', ['pending', 'confirmed', 'rejected'])
                  ->default('pending');

            // Firm only fields — null for student enrollments
            $table->text('requested_venue')->nullable();
            $table->dateTime('proposed_schedule')->nullable();

            // firm booking extras
            $table->integer('participant_count')->nullable(); // firm only
            $table->decimal('total_amount', 10, 2)->nullable(); // calculated at booking
            $table->string('college_note')->nullable(); // reason if rejected


            // Payment
            $table->enum('payment_status', ['pending', 'paid', 'na'])
                  ->default('na');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
