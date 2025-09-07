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
        Schema::create('opsi_jawabans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // relasi ke pertanyaans pakai UUID
            $table->foreignUuid('pertanyaan_id')
                ->constrained('pertanyaans')
                ->cascadeOnDelete();

            $table->string('teks');
            $table->integer('skor')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opsi_jawabans');
    }
};
