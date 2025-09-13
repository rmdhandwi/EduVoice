<?php

namespace App\Services;

use App\Models\Kuesioner;
use App\Models\OpsiJawaban;
use App\Models\Pertanyaan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PertanyaanService
{
    public function getByKuesioner(Kuesioner $kuesioner): Collection
    {
        return $kuesioner->pertanyaan()
            ->select('id', 'teks', 'tipe', 'unsur', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function storeMany(array $pertanyaans, string $kuesionerId): void
    {
        DB::transaction(function () use ($pertanyaans, $kuesionerId) {
            foreach ($pertanyaans as $p) {
                // Simpan pertanyaan
                $pertanyaan = Pertanyaan::create([
                    'kuesioner_id' => $kuesionerId,
                    'teks'         => $p['teks'],
                    'unsur'        => $p['unsur'] ?? null,
                    'tipe'         => 'radio', // default radio
                ]);

                // Simpan opsi jawaban
                foreach ($p['opsi'] as $opsi) {
                    OpsiJawaban::create([
                        'pertanyaan_id' => $pertanyaan->id,
                        'teks'          => $opsi['teks'],
                        'skor'          => $opsi['skor'],
                    ]);
                }
            }
        });
    }

    /**
     * Update pertanyaan dan opsi jawaban
     */
    public function update(Pertanyaan $pertanyaan, array $data): void
    {
        DB::transaction(function () use ($pertanyaan, $data) {
            // Update pertanyaan utama
            $pertanyaan->update([
                'teks'  => $data['teks'],
                'unsur' => $data['unsur'] ?? null,
            ]);

            // Reset opsi lama (kalau memang replace total)
            $pertanyaan->opsiJawaban()->delete();

            // Tambah opsi baru (hanya kalau ada)
            if (!empty($data['opsi']) && is_array($data['opsi'])) {
                $pertanyaan->opsiJawaban()->createMany(
                    collect($data['opsi'])->map(fn($opsi) => [
                        'teks' => $opsi['teks'],
                        'skor' => $opsi['skor'],
                    ])->toArray()
                );
            }
        });
    }

    public function updateBulk(Kuesioner $kuesioner, array $pertanyaans): void
    {
        DB::transaction(function () use ($kuesioner, $pertanyaans) {
            $existingIds = $kuesioner->pertanyaan()->pluck('id')->toArray();
            $incomingIds = collect($pertanyaans)->pluck('id')->filter()->toArray();

            // 🔴 Hapus pertanyaan yang tidak ada di request
            $idsToDelete = array_diff($existingIds, $incomingIds);
            if (!empty($idsToDelete)) {
                Pertanyaan::whereIn('id', $idsToDelete)->delete();
            }

            foreach ($pertanyaans as $data) {
                if (isset($data['id'])) {
                    // 🔄 Update pertanyaan lama
                    $pertanyaan = Pertanyaan::findOrFail($data['id']);
                    $pertanyaan->update([
                        'teks'  => $data['teks'],
                        'unsur' => $data['unsur'] ?? null,
                    ]);

                    // Reset opsi lama → tambah lagi
                    $pertanyaan->opsiJawaban()->delete();
                    if (!empty($data['opsi'])) {
                        $pertanyaan->opsiJawaban()->createMany(
                            collect($data['opsi'])->map(fn($opsi) => [
                                'teks' => $opsi['teks'],
                                'skor' => $opsi['skor'],
                            ])->toArray()
                        );
                    }
                } else {
                    // 🆕 Buat pertanyaan baru
                    $pertanyaanBaru = $kuesioner->pertanyaan()->create([
                        'teks'  => $data['teks'],
                        'unsur' => $data['unsur'] ?? null,
                    ]);

                    if (!empty($data['opsi'])) {
                        $pertanyaanBaru->opsiJawaban()->createMany(
                            collect($data['opsi'])->map(fn($opsi) => [
                                'teks' => $opsi['teks'],
                                'skor' => $opsi['skor'],
                            ])->toArray()
                        );
                    }
                }
            }
        });
    }




    /**
     * Hapus pertanyaan (otomatis hapus opsi jawaban via cascade)
     */
    public function destroy(Pertanyaan $pertanyaan): void
    {
        $pertanyaan->delete();
    }

    /**
     * Hapus banyak pertanyaan sekaligus
     */
    public function bulkDestroy(array $ids): void
    {
        Pertanyaan::whereIn('id', $ids)->delete();
    }
}
