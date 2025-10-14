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
    Schema::table('users', function (Blueprint $table) {
        // Menambah kolom 'role' setelah kolom 'password'
        $table->enum('role', ['admin', 'psikolog'])->default('psikolog')->after('password');
    });
    }

    public function down(): void
    {
    Schema::table('users', function (Blueprint $table) {
        // Untuk membatalkan migrasi, hapus kolom 'role'
        $table->dropColumn('role');
    });
    }
};
