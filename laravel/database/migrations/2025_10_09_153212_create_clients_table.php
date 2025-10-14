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
    Schema::create('clients', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->date('date_of_birth')->nullable();
        $table->string('whatsapp_number')->nullable();
        $table->text('address')->nullable();
        $table->text('initial_diagnosis')->nullable();
        $table->timestamps(); // membuat kolom created_at dan updated_at
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
