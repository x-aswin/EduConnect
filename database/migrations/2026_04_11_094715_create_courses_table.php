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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('college_id')->constrained('colleges')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('mentor_id')->nullable()->constrained('mentors')->onDelete('set null');

            // Core Content
            $table->string('title');
            $table->string('slug')->unique(); // For SEO friendly URLs
            $table->text('description')->nullable();
            $table->string('course_image')->nullable();
            
            // Pricing and Type
            $table->enum('course_type', ['student_only', 'firm_only']);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->boolean('is_certified')->default(false);

            // Logistics
            $table->integer('total_seats')->nullable();
            $table->integer('available_seats')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('time_slot')->nullable(); // e.g., "10:00 AM - 01:00 PM"
            $table->string('venue')->nullable();     // e.g., "Seminar Hall A" or "Online"

            // Status
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
