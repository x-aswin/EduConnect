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
        Schema::create('mentors', function (Blueprint $table) {
            $table->id();
        
            //Link to the 'users' table (for login)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
            //Link to the 'colleges' table (who does this mentor work for?)
            $table->foreignId('college_id')->constrained()->onDelete('cascade');

            $table->string('qualification');
            $table->string('expertise'); //"Mathematics", "Placement Training"
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentors');
    }
};
