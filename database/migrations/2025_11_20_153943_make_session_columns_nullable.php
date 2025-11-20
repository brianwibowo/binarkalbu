<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_sessions', function (Blueprint $table) {
            // Ubah kolom menjadi nullable (boleh kosong)
            $table->date('session_date')->nullable()->change();
            $table->time('session_start_time')->nullable()->change();
            $table->time('session_end_time')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('client_sessions', function (Blueprint $table) {
            // Kembalikan ke kondisi semula (tidak boleh kosong) jika rollback
            // Hati-hati, ini bisa error jika sudah ada data NULL
            $table->date('session_date')->nullable(false)->change();
            $table->time('session_start_time')->nullable(false)->change();
            $table->time('session_end_time')->nullable(false)->change();
        });
    }
};