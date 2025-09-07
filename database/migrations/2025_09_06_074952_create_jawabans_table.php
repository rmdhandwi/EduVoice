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
        Schema::create('jawabans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke pertanyaans (UUID)
            $table->foreignUuid('pertanyaan_id')
                ->constrained('pertanyaans')
                ->cascadeOnDelete();

            // Jika users pakai UUID:
            $table->foreignUuid('respondens_id')
                ->nullable()
                ->constrained('respondens')
                ->cascadeOnDelete();

            // Jawaban fleksibel
            $table->text('teks')->nullable();       // untuk tipe text
            $table->integer('angka')->nullable();   // untuk tipe number
            $table->date('tanggal')->nullable();    // untuk tipe date

            // Kritik & Saran (khusus pertanyaan statis)
            $table->text('saran')->nullable();
            $table->text('kritik')->nullable();

            // Relasi ke opsi jawaban (radio/checkbox)
            $table->foreignUuid('opsi_id')
                ->nullable()
                ->constrained('opsi_jawabans')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawabans');
    }
};
