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
        Schema::create('respondens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('jk', ['Laki-Laki', 'Perempuan'])->default('Laki-Laki');
            $table->unsignedTinyInteger('umur'); // umur lebih cocok integer kecil
            $table->string('pekerjaan')->nullable(); // bisa kosong
            $table->string('pendidikan_terakhir')->nullable();
            $table->text('saran')->nullable();
            $table->text('kritik')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respondens');
    }
};
