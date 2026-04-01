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
        Schema::create('colleges', function (Blueprint $table) {
            $table->id();

            // Connection to the Security/Login table
            // This links 'colleges.user_id' to 'users.id'
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Institutional Details
            $table->string('institution_name');
            $table->string('college_phone');
            $table->text('address');
            $table->string('website')->nullable();

            // Contact Person Details
            $table->string('contact_person'); // "Prof. xxx"
            $table->string('designation');    // "HOD" or "Placement Officer"
            $table->string('contact_number'); // The direct mobile of the person

            // Admin Verification
            // Path of the uploaded document
            $table->string('verification_doc');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colleges');
    }
};
