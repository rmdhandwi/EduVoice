<?php

namespace App\Http\Controllers\Kuesioner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kuesioner\StoreKuesionerRequest;
use App\Http\Requests\Kuesioner\UpdateKuesionerRequest;
use App\Models\Kuesioner;
use App\Services\KuesionerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class KuesionerController extends Controller
{
    protected $service;

    public function __construct(KuesionerService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kuesioners = $this->service->getAll();

        return Inertia::render('admin/kuesioner/Index', [
            'kuesioners' => $kuesioners,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/kuesioner/Kelola');
    }

    public function store(StoreKuesionerRequest $request)
    {
        $data = $request->validated();


        try {
            DB::beginTransaction();

            // Simpan Kuesioner
            $kuesioner = Kuesioner::create([
                'judul' => $data['judul'],
                'deskripsi' => $data['deskripsi'] ?? null,
            ]);

            // Simpan Pertanyaan
            foreach ($data['pertanyaans'] as $q) {
                $pertanyaan = $kuesioner->pertanyaans()->create([
                    'teks' => $q['teks'],
                    'tipe' => $q['tipe'],
                ]);

                // Simpan Opsi jika ada
                if (!empty($q['opsi'])) {
                    foreach ($q['opsi'] as $opsi) {
                        $pertanyaan->opsiJawabans()->create([
                            'teks' => $opsi['teks'],
                            'skor' => $opsi['skor'],
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.kuesioner.index')
                ->with('success', 'Kuesioner berhasil dibuat');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(kuesioner $kuesioner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(Kuesioner $kuesioner)
    {
        $data = $this->service->getDetailForEdit($kuesioner);

        return Inertia::render('admin/kuesioner/Kelola', [
            'kuesioner' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKuesionerRequest $request, Kuesioner $kuesioner)
    {
        $data = $request->validated();

        DB::transaction(function () use ($kuesioner, $data) {
            // Update kuesioner
            $kuesioner->update([
                'judul' => $data['judul'],
                'deskripsi' => $data['deskripsi'] ?? null,
            ]);

            // Hapus pertanyaan & opsi lama
            $kuesioner->pertanyaans()->each(function ($pertanyaan) {
                $pertanyaan->opsiJawabans()->delete();
            });
            $kuesioner->pertanyaans()->delete();

            // Simpan ulang pertanyaan baru
            foreach ($data['pertanyaans'] as $q) {
                $pertanyaan = $kuesioner->pertanyaans()->create([
                    'teks' => $q['teks'],
                    'tipe' => $q['tipe'],
                ]);

                // ✅ hanya simpan opsi kalau tipe radio/checkbox
                if (in_array($q['tipe'], ['radio', 'checkbox']) && !empty($q['opsi'])) {
                    foreach ($q['opsi'] as $opsi) {
                        $pertanyaan->opsiJawabans()->create([
                            'teks' => $opsi['teks'],
                            'skor' => $opsi['skor'] ?? 0,
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('admin.kuesioner.index')
            ->with('success', 'Kuesioner berhasil diperbarui');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kuesioner $kuesioner)
    {
        try {
            $kuesioner->delete();
            return redirect()->back()->with('success', 'Kuesioner berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus kuesioner');
        }
    }

    public function bulkDestroy(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if (!empty($ids)) {
                Kuesioner::whereIn('id', $ids)->delete();
            }

            return redirect()->back()->with('success', count($ids) . ' kuesioner berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data');
        }
    }
}
