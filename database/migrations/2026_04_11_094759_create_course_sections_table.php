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
        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();

            // Foreign Key linking to the main course
            $table->foreignId('course_id')
                  ->constrained('courses')
                  ->onDelete('cascade'); // If course is deleted, syllabus is too

            $table->string('section_heading'); // some heading
            $table->text('section_content');   // content of heading
            
            // order
            $table->integer('priority_order')->default(0);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_sections');
    }
};
