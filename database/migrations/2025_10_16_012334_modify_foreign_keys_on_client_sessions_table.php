<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_sessions', function (Blueprint $table) {
            // 1. Hapus foreign key yang lama
            $table->dropForeign(['client_id']);
            $table->dropForeign(['user_id']);

            // 2. Buat lagi foreign key yang baru TANPA onDelete('cascade')
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::table('client_sessions', function (Blueprint $table) {
            // Logika untuk membatalkan: buat ulang foreign key DENGAN cascade
            $table->dropForeign(['client_id']);
            $table->dropForeign(['user_id']);

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};