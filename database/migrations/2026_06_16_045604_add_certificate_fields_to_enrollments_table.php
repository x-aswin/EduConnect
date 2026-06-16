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
        Schema::table('enrollments', function (Blueprint $table) {
            $table->boolean('certificate_issued')->default(false)->after('payment_status');
            $table->timestamp('certificate_issued_at')->nullable()->after('certificate_issued');
            $table->string('certificate_code')->nullable()->unique()->after('certificate_issued_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'certificate_issued', 
                'certificate_issued_at', 
                'certificate_code'
            ]);
        });
    }
};
