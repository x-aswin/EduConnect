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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            // Bridge to the login account
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Personal Information
            $table->string('full_name');
            $table->string('phone');
            $table->date('dob');
            $table->string('gender');

            // Academic Background
            // "+2", "BCA"
            $table->string('current_qualification');

            $table->text('address');
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
