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
    Schema::create('daily_recaps', function (Blueprint $table) {
        $table->id();
        $table->date('recap_date')->unique(); // Tanggal unik
        $table->integer('session_count')->default(0); // Jumlah sesi praktek
        $table->integer('new_chats')->default(0); // Chat masuk baru
        $table->integer('new_client_goals')->default(0); // Goals klien baru
        $table->integer('gmap_reviews')->default(0); // Ulasan google map
        // Sumber klien
        $table->integer('source_tiktok')->default(0);
        $table->integer('source_google')->default(0);
        $table->integer('source_instagram')->default(0);
        $table->integer('source_friend')->default(0);
        // Jam gandeng
        $table->string('extra_notes')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_recaps');
    }
};
