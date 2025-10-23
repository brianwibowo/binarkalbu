<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('client_code')->nullable()->change(); // Bisa kosong dulu
            // Jika ada unique constraint, hapus dulu:
            // $table->dropUnique(['client_code']);
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('client_code')->nullable(false)->change();
        });
    }
};