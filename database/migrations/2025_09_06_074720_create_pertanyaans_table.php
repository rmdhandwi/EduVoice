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
        Schema::create('pertanyaans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // relasi ke kuesioners pakai UUID
            $table->foreignUuid('kuesioner_id')
                ->constrained('kuesioners')
                ->cascadeOnDelete();

            $table->text('teks');
            $table->enum('tipe', ['text', 'number', 'radio', 'checkbox', 'date'])->default('text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertanyaans');
    }
};
