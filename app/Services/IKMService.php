<?php

namespace App\Services;

use App\Models\Jawaban;
use App\Models\Kuesioner;

class IKMService
{
    public function hitungIKMPerKuesioner()
    {
        $kuesioners = Kuesioner::with([
            'pertanyaan.opsiJawaban',
        ])->get();

        $results = $kuesioners->map(function ($kuesioner) {
            // Ambil semua jawaban responden di kuesioner ini
            $jawaban = Jawaban::whereIn(
                'pertanyaan_id',
                $kuesioner->pertanyaan->pluck('id')
            )->with(['opsiJawaban', 'pertanyaan'])->get();

            // Hitung jumlah responden unik
            $jumlahResponden = $jawaban->pluck('respondens_id')->unique()->count();

            // Kelompokkan jawaban berdasarkan UNSUR
            $byUnsur = $jawaban->groupBy(fn($j) => $j->pertanyaan->unsur);

            $unsurScores = $byUnsur->map(function ($jawabanUnsur) {
                $totalSkor = $jawabanUnsur->sum(fn($j) => $j->opsiJawaban->skor ?? 0);
                $jumlah = $jawabanUnsur->count();

                return $jumlah > 0 ? $totalSkor / $jumlah : 0;
            });

            // Rata-rata dari semua unsur
            $ikm = $unsurScores->count() > 0
                ? $unsurScores->avg()
                : 0;

            // SKM biasanya: IKM × 25
            $skm = $ikm * 25;

            return [
                'id' => $kuesioner->id,
                'judul' => $kuesioner->judul,
                'ikm' => round($ikm, 2),
                'skm' => round($skm, 2),
                'mutu' => $this->getMutu($skm),
                'jumlah_responden' => $jumlahResponden, // ✅ ditambahkan
            ];
        });

        return $results;
    }



    /**
     * Hitung detail (unsur, nilai tertimbang, dll) per kuesioner.
     * Dipakai di show.
     */
    public function hitungDetail(Kuesioner $kuesioner)
    {
        $pertanyaanIds = $kuesioner->pertanyaan->pluck('id');

        $jawaban = Jawaban::whereIn('pertanyaan_id', $pertanyaanIds)
            ->with(['opsiJawaban', 'pertanyaan'])
            ->get();

        $jumlahResponden = $jawaban->pluck('respondens_id')->unique()->count();

        $byUnsur = $jawaban->groupBy(fn($j) => $j->pertanyaan->unsur);

        $unsurData = $byUnsur->map(function ($jawabanUnsur, $unsur) {
            $totalSkor = $jawabanUnsur->sum(fn($j) => $j->opsiJawaban->skor ?? 0);
            $jumlah = $jawabanUnsur->count();

            $nilaiUnsur = $jumlah > 0 ? $totalSkor / $jumlah : 0;

            $nilaiTertimbang = $nilaiUnsur * 25;

            return [
                'unsur' => $unsur,
                'nilai_unsur' => round($nilaiUnsur, 2),
                'nilai_tertimbang' => round($nilaiTertimbang, 2),
            ];
        })->values();

        $ikm = $unsurData->avg('nilai_unsur') ?? 0;


        $skm = $ikm * 25;

        $sumTertimbang = $unsurData->sum('nilai_tertimbang');

        return [
            'unsurs' => $unsurData,
            'ikm' => round($ikm, 2),
            'skm' => round($skm, 2),
            'nilai_dasar' => $unsurData->count(),        // jumlah unsur (misal 4) → kalau mau tampil
            'total_tertimbang' => round($sumTertimbang, 2), // kalau dipakai di laporan
            'mutu' => $this->getMutu($skm),
            'jumlah_responden' => $jumlahResponden,
        ];
    }



    private function getMutu($skm)
    {
        if ($skm >= 88.31) return 'A (Sangat Baik)';
        if ($skm >= 76.61) return 'B (Baik)';
        if ($skm >= 65.00) return 'C (Kurang Baik)';
        return 'D (Tidak Baik)';
    }
}
