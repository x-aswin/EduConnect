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
        Schema::create('firms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
            $table->string('org_name'); // "Victory Sports Club"
            $table->string('org_type'); // "NGO", "Church", "Corporate"
            
            // Point of Contact details
            $table->string('contact_person'); // The name of the person who will be the main contact for this firm
            $table->string('designation');    // Their role (e.g. "Secretary")
            $table->string('phone');
            
            // Verification for Admin
            $table->string('verification_doc'); 
            
            $table->text('address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firms');
    }
};
