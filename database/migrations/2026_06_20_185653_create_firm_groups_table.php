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
        Schema::create('firm_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firm_id')->constrained()->onDelete('cascade');
            $table->string('group_name'); // e.g. "Youth Group 2025", "Sunday School Team"
            $table->boolean('is_active')->default(true); // firm can deactivate without deleting
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firm_groups');
    }
};
