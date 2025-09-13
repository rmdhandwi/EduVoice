<?php

namespace App\Services;

use App\Models\Kuesioner;
use App\Models\Responden;
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

    public function getKritikSaran()
    {
        return Responden::query()
            ->whereNotNull('kritik')
            ->orWhereNotNull('saran')
            ->select('id', 'name', 'jk', 'kritik', 'saran', 'created_at')
            ->latest()
            ->get();
    }

    public function getDetailForEdit(Kuesioner $kuesioner): array
    {
        // Load relasi pertanyaan + opsi jawaban
        $kuesioner->load('pertanyaan.opsiJawaban');

        // Normalisasi agar langsung cocok dengan form Vue
        return [
            'id' => $kuesioner->id,
            'judul' => $kuesioner->judul,
            'deskripsi' => $kuesioner->deskripsi,
            'pertanyaans' => $kuesioner->pertanyaan->map(function ($pertanyaan) {
                return [
                    'id' => $pertanyaan->id,
                    'teks' => $pertanyaan->teks,
                    'tipe' => $pertanyaan->tipe,
                    'opsi' => $pertanyaan->opsiJawaban->map(function ($opsi) {
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

    public function getDetailForView(Kuesioner $kuesioner): array
    {
        // Load relasi pertanyaan + opsi jawaban
        $kuesioner->load('pertanyaan.opsiJawaban');

        return [
            'id' => $kuesioner->id,
            'judul' => $kuesioner->judul,
            'deskripsi' => $kuesioner->deskripsi,
            'pertanyaans' => $kuesioner->pertanyaan->map(function ($pertanyaan) {
                return [
                    'id' => $pertanyaan->id,
                    'teks' => $pertanyaan->teks,
                    'tipe' => $pertanyaan->tipe,
                    'opsi' => $pertanyaan->opsiJawaban->map(function ($opsi) {
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


    public function getById(string $id)
    {
        return Kuesioner::with([
            'pertanyaan',              // relasi Kuesioner -> Pertanyaan
            'pertanyaan.opsiJawaban',  // relasi Pertanyaan -> OpsiJawaban
        ])->findOrFail($id);
    }
}
