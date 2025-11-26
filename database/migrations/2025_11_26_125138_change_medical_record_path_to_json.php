<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('client_sessions', function (Blueprint $table) {
            // Clear data lama yang tidak valid, ubah ke JSON
            // Pertama set NULL semua untuk hindari JSON error
            DB::table('client_sessions')->update(['medical_record_path' => null]);
            
            // Kemudian ubah type ke JSON
            $table->json('medical_record_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('client_sessions', function (Blueprint $table) {
            $table->string('medical_record_path')->nullable()->change();
        });
    }
};
