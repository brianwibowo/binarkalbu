<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menambahkan kolom 'deleted_at' ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Menambahkan kolom 'deleted_at' ke tabel clients
        Schema::table('clients', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        // Logika untuk membatalkan (jika perlu)
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};