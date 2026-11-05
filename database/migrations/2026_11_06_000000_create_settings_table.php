<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('value');
        });

        DB::table('settings')->insert([
            'key' => 'email_verification_enabled',
            'value' => '0',
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
