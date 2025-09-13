<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Responden\RespondenRequest;
use App\Models\Jawaban;
use App\Models\Responden;
use App\Services\KuesionerService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RespondenController extends Controller
{
    protected  $service;

    public function __construct(KuesionerService $service)
    {
        $this->service = $service;
    }

    /**
     * Halaman utama user dengan daftar kuesioner
     */
    public function index()
    {
        return Inertia::render('user/Index', [
            'kuesioners' => $this->service->getAll(),
            'currentKuesionerId' => null,
            'kuesioner' => null, // kosong karena belum memilih
            'mode' => 'responden'
        ]);
    }

    /**
     * Tampilkan kuesioner terpilih
     */
    public function show(string $id)
    {
        return Inertia::render('user/Index', [
            'kuesioners' => $this->service->getAll(),
            'currentKuesionerId' => $id,
            'kuesioner' => $this->service->getById($id),
            'mode' => 'create'
        ]);
    }

    public function kritikSaran()
    {
        return Inertia::render('user/Index', [
            'kuesioners' => $this->service->getAll(),
            'kuesioner' => null,
            'currentKuesionerId' => null,
            'responden' => null,
            'kritik_saran' => $this->service->getKritikSaran(),
            'mode' => 'kritik',
        ]);
    }

    public function submit(RespondenRequest $request, $kuesionerId)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            // ✅ Kalau ada responden_id → update
            // ✅ Kalau tidak ada → buat baru
            if (!empty($validated['responden_id'])) {
                $responden = Responden::find($validated['responden_id']);

                if ($responden) {
                    $responden->update([
                        'name' => $validated['name'],
                        'jk' => $validated['jk'],
                        'umur' => $validated['umur'],
                        'pekerjaan' => $validated['pekerjaan'],
                        'pendidikan_terakhir' => $validated['pendidikan_terakhir'],
                        'kritik' => $validated['kritik'] ?? null,
                        'saran' => $validated['saran'] ?? null,
                    ]);
                } else {
                    // kalau id tidak valid → buat baru
                    $responden = Responden::create([
                        'name' => $validated['name'],
                        'jk' => $validated['jk'],
                        'umur' => $validated['umur'],
                        'pekerjaan' => $validated['pekerjaan'],
                        'pendidikan_terakhir' => $validated['pendidikan_terakhir'],
                        'kritik' => $validated['kritik'] ?? null,
                        'saran' => $validated['saran'] ?? null,
                    ]);
                }
            } else {
                $responden = Responden::create([
                    'name' => $validated['name'],
                    'jk' => $validated['jk'],
                    'umur' => $validated['umur'],
                    'pekerjaan' => $validated['pekerjaan'],
                    'pendidikan_terakhir' => $validated['pendidikan_terakhir'],
                    'kritik' => $validated['kritik'] ?? null,
                    'saran' => $validated['saran'] ?? null,
                ]);
            }

            // Simpan jawaban
            foreach ($validated['jawaban'] as $jawab) {
                if (is_array($jawab['opsi_id'])) {
                    foreach ($jawab['opsi_id'] as $opsiId) {
                        Jawaban::create([
                            'pertanyaan_id' => $jawab['pertanyaan_id'],
                            'respondens_id' => $responden->id,
                            'opsi_id' => $opsiId,
                            'teks' => null,
                        ]);
                    }
                } else {
                    Jawaban::create([
                        'pertanyaan_id' => $jawab['pertanyaan_id'],
                        'respondens_id' => $responden->id,
                        'opsi_id' => $jawab['opsi_id'] ?? null,
                        'teks' => $jawab['teks'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('thankyou')
                ->with('success', 'Jawaban berhasil disimpan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
