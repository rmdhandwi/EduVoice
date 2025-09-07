<?php

namespace App\Services;

use App\Models\Kuesioner;
use Illuminate\Support\Collection;

class KuesionerService
{
    /**
     * Ambil semua kuesioner (select field yang dibutuhkan saja).
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Kuesioner::select('id', 'judul', 'deskripsi', 'created_at', 'updated_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

     public function getDetailForEdit(Kuesioner $kuesioner): array
    {
        // Load relasi pertanyaan + opsi jawaban
        $kuesioner->load('pertanyaans.opsiJawabans');

        // Normalisasi agar langsung cocok dengan form Vue
        return [
            'id' => $kuesioner->id,
            'judul' => $kuesioner->judul,
            'deskripsi' => $kuesioner->deskripsi,
            'pertanyaans' => $kuesioner->pertanyaans->map(function ($pertanyaan) {
                return [
                    'id' => $pertanyaan->id,
                    'teks' => $pertanyaan->teks,
                    'tipe' => $pertanyaan->tipe,
                    'opsi' => $pertanyaan->opsiJawabans->map(function ($opsi) {
                        return [
                            'id' => $opsi->id,
                            'teks' => $opsi->teks,
                            'skor' => $opsi->skor,
                        ];
                    })->values(),
                ];
            })->values(),
        ];
    }
}
