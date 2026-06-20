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
        Schema::create('firm_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')
                  ->constrained('firm_groups')
                  ->onDelete('cascade'); // deleting a group removes its members
            $table->string('name');
            $table->string('contact_info'); // phone or email
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firm_group_members');
    }
};
