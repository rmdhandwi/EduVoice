<?php

namespace App\Services;

use App\Models\Kuesioner;
use App\Models\OpsiJawaban;
use App\Models\Responden;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getSummary(): array
    {
        // total semua kuesioner
        $totalKuesioner = Kuesioner::count();

        // total semua responden
        $totalResponden = Responden::count();

        // total kritik & saran (hanya jika keduanya tidak null)
        $totalKritikSaran = Responden::whereNotNull('kritik')
            ->orWhereNotNull('saran')
            ->count();


        // jumlah responden unik per kuesioner
        $respondenPerKuesioner = Kuesioner::select('kuesioners.id', 'kuesioners.judul')
            ->join('pertanyaans as p', 'p.kuesioner_id', '=', 'kuesioners.id')
            ->join('jawabans as j', 'j.pertanyaan_id', '=', 'p.id')
            ->selectRaw('kuesioners.id, kuesioners.judul, COUNT(DISTINCT j.respondens_id) as total_responden')
            ->groupBy('kuesioners.id', 'kuesioners.judul')
            ->get();

        // total jawaban = jumlah seluruh responden unik di semua kuesioner
        $totalJawaban = $respondenPerKuesioner->sum('total_responden');

        return [
            'total_kuesioner'    => $totalKuesioner,
            'total_responden'    => $totalResponden,
            'total_kritik_saran' => $totalKritikSaran,
            'total_jawaban'      => $totalJawaban,
        ];
    }

    public function getCharts($kuesionerId = null)
    {
        if ($kuesionerId) {
            // === Distribusi khusus per kuesioner ===
            $distribusi = OpsiJawaban::select(
                'opsi_jawabans.teks',
                'opsi_jawabans.skor',
                'pertanyaans.kuesioner_id',
                DB::raw('COUNT(jawabans.id) as total')
            )
                ->join('pertanyaans', 'pertanyaans.id', '=', 'opsi_jawabans.pertanyaan_id')
                ->leftJoin('jawabans', 'jawabans.opsi_id', '=', 'opsi_jawabans.id')
                ->where('pertanyaans.kuesioner_id', $kuesionerId)
                ->groupBy('opsi_jawabans.teks', 'opsi_jawabans.skor', 'pertanyaans.kuesioner_id')
                ->orderBy('opsi_jawabans.skor')
                ->get()
                ->map(fn($row) => [
                    'label'        => $row->teks . ' (' . $row->total . ')',
                    'value'        => (int) $row->total,
                    'skor'         => $row->skor,
                    'kuesioner_id' => (string) $row->kuesioner_id,
                ]);
        } else {
            // === Distribusi global semua kuesioner ===
            $distribusi = OpsiJawaban::select(
                'opsi_jawabans.teks',
                'opsi_jawabans.skor',
                'pertanyaans.kuesioner_id',
                DB::raw('COUNT(jawabans.id) as total')
            )
                ->join('pertanyaans', 'pertanyaans.id', '=', 'opsi_jawabans.pertanyaan_id')
                ->leftJoin('jawabans', 'jawabans.opsi_id', '=', 'opsi_jawabans.id')
                ->groupBy('opsi_jawabans.teks', 'opsi_jawabans.skor', 'pertanyaans.kuesioner_id')
                ->orderBy('opsi_jawabans.skor')
                ->get()
                ->map(fn($row) => [
                    'label'        => $row->teks . ' (' . $row->total . ')',
                    'value'        => (int) $row->total,
                    'skor'         => $row->skor,
                    'kuesioner_id' => (string) $row->kuesioner_id,
                ]);
        }

        // === Tren Responden (tetap sama) ===
        $trenQuery = Responden::selectRaw('DATE(respondens.created_at) as tanggal, COUNT(DISTINCT respondens.id) as total, pertanyaans.kuesioner_id')
            ->join('jawabans', 'jawabans.respondens_id', '=', 'respondens.id')
            ->join('pertanyaans', 'pertanyaans.id', '=', 'jawabans.pertanyaan_id');

        if ($kuesionerId) {
            $trenQuery->where('pertanyaans.kuesioner_id', $kuesionerId);
        }

        $tren = $trenQuery
            ->groupBy('tanggal', 'pertanyaans.kuesioner_id')
            ->orderBy('tanggal')
            ->get()
            ->map(fn($row) => [
                'tanggal'      => Carbon::parse($row->tanggal)->format('d M Y'),
                'value'        => $row->total,
                'kuesioner_id' => $row->kuesioner_id,
            ]);

        return [
            'distribusi' => $distribusi,
            'tren'       => $tren,
        ];
    }
}
