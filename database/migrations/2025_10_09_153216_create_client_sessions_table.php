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
    Schema::create('client_sessions', function (Blueprint $table) {
        $table->id();

        // Menghubungkan ke tabel clients dan users
        $table->foreignId('client_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->comment('ID Psikolog')->constrained()->onDelete('cascade');

        $table->text('session_description')->nullable();
        $table->date('session_date');
        $table->time('session_start_time');
        $table->time('session_end_time');
        $table->date('transfer_date')->nullable();
        $table->enum('payment_status', ['dp', 'lunas'])->default('dp');
        $table->unsignedInteger('payment_amount')->default(0);
        $table->enum('session_status', ['terpakai', 'belum_terpakai'])->default('belum_terpakai');
        $table->string('medical_record_path')->nullable(); // Path untuk file upload RM
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_sessions');
    }
};
