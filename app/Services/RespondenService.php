<?php

namespace App\Services;

use App\Models\Pertanyaan;
use App\Models\Responden;
use Illuminate\Support\Collection;

class RespondenService
{
    /**
     * Ambil semua Responden (select field yang dibutuhkan saja).
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        $respondens = Responden::select(
            'id',
            'name',
            'jk',
            'umur',
            'pekerjaan',
            'pendidikan_terakhir',
            'created_at'
        )
            ->with([
                'jawaban.pertanyaan.kuesioner', // ambil kuesioner dari pertanyaan
                'jawaban.opsiJawaban'           // ambil opsi jawaban
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return $respondens->map(function ($responden) {
            // Ambil judul kuesioner pertama (kalau ada jawaban)
            $judul = optional($responden->jawaban->first()?->pertanyaan?->kuesioner)->judul ?? '-';

            // Kelompokkan jawaban berdasarkan judul kuesioner
            $grouped = $responden->jawaban
                ->groupBy(fn($jawaban) => $jawaban->pertanyaan->kuesioner->judul);

            return [
                'id'                   => $responden->id,
                'name'                 => $responden->name,
                'jk'                   => $responden->jk,
                'umur'                 => $responden->umur,
                'pekerjaan'            => $responden->pekerjaan,
                'pendidikan_terakhir'  => $responden->pendidikan_terakhir,
                'created_at'           => $responden->created_at,
                'kuesioner_judul'      => $judul,   // langsung sisipkan judul
                // 'kuesioner'            => $grouped, // tetap ada grouping by judul
            ];
        });
    }



    /**
     * Ambil semua pertanyaan beserta opsi
     */
    public function getPertanyaan(): array
    {
        return Pertanyaan::with('opsiJawaban') // load relasi opsiJawaban
            ->get()
            ->map(function ($pertanyaan) {
                return [
                    'id' => $pertanyaan->id,
                    'teks' => $pertanyaan->teks,
                    'tipe' => $pertanyaan->tipe,
                    'opsi' => $pertanyaan->opsiJawaban->map(function ($opsi) {
                        return [
                            'id' => $opsi->id,
                            'teks' => $opsi->teks,
                            'skor' => $opsi->skor ?? null,
                        ];
                    }),
                ];
            })
            ->toArray();
    }


    /**
     * Ambil detail responden beserta jawaban yang dicocokkan
     */
    public function getDetailForResponden(Responden $responden): array
    {
        $responden->load(['jawaban.pertanyaan.opsiJawaban', 'jawaban.opsiJawaban', 'jawaban.pertanyaan.kuesioner']);

        // Ambil semua kuesioner unik dari jawaban
        $kuesioners = $responden->jawaban
            ->pluck('pertanyaan.kuesioner')
            ->unique('id')
            ->values();

        return [
            'id' => $responden->id,
            'name' => $responden->name,
            'jk' => $responden->jk,
            'umur' => $responden->umur,
            'pekerjaan' => $responden->pekerjaan,
            'pendidikan_terakhir' => $responden->pendidikan_terakhir,
            'kritik' => $responden->kritik,
            'saran' => $responden->saran,
            'kuesioners' => $kuesioners->map(function ($kuesioner) use ($responden) {
                return [
                    'id' => $kuesioner->id,
                    'judul' => $kuesioner->judul,
                    'deskripsi' => $kuesioner->deskripsi,
                    'pertanyaans' => $kuesioner->pertanyaan->map(function ($pertanyaan) use ($responden) {
                        $jawabanPertanyaan = $responden->jawaban->where('pertanyaan_id', $pertanyaan->id);
                        $firstJawaban = $jawabanPertanyaan->first();

                        return [
                            'id' => $pertanyaan->id,
                            'teks' => $pertanyaan->teks,
                            'tipe' => $pertanyaan->tipe,
                            'opsi' => $pertanyaan->opsiJawaban->map(function ($opsi) use ($jawabanPertanyaan, $firstJawaban, $pertanyaan) {
                                return [
                                    'id' => $opsi->id,
                                    'teks' => $opsi->teks,
                                    'skor' => $opsi->skor ?? null,
                                    'dipilih' => $pertanyaan->tipe === 'radio'
                                        ? ($firstJawaban->opsi_id ?? null) === $opsi->id
                                        : $jawabanPertanyaan->pluck('opsi_id')->contains($opsi->id),
                                ];
                            }),
                            'opsi_id' => $pertanyaan->tipe === 'radio' ? ($firstJawaban->opsi_id ?? null) : null,
                            'opsi_terpilih' => $pertanyaan->tipe === 'checkbox' ? $jawabanPertanyaan->pluck('opsi_id')->filter()->values() : null,
                            'teks_jawaban' => $pertanyaan->tipe === 'text' ? ($firstJawaban->teks ?? null) : null,
                            'angka' => $pertanyaan->tipe === 'number' ? ($firstJawaban->angka ?? null) : null,
                            'tanggal' => $pertanyaan->tipe === 'date' ? ($firstJawaban->tanggal ?? null) : null,
                        ];
                    })->values(),
                ];
            })->values(),
        ];
    }
}
