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
        Schema::create('certificate_signatories', function (Blueprint $table) {
            $table->id();
            
            // Foreign key referencing courses.id
            $table->foreignId('course_id')
                  ->constrained('courses')
                  ->onDelete('cascade'); // Automatically removes signatories if the course is deleted
            
            $table->string('name');
            $table->string('designation');
            $table->string('signature_image'); // Stores the path, e.g., 'signatures/xyz.png'
            $table->integer('display_order')->default(1); // To handle sorting/placement (1, 2, 3)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_signatories');
    }
};
