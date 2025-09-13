<?php

namespace App\Http\Controllers\Kuesioner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kuesioner\PertanyaanRequest;
use App\Models\Kuesioner;
use App\Models\Pertanyaan;
use App\Services\PertanyaanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Throwable;

class PertanyaanController extends Controller
{

    protected $service;
    protected $unsur;

    public function __construct(PertanyaanService $service)
    {
        $this->service = $service;
        $this->unsur = [
            'Persyaratan Pelayanan',
            'Prosedur Pelayanan',
            'Kecepatan Pelayanan',
            'Kewajaran Biaya Pelayanan',
            'Kesesuaian Produk Layanan',
            'Kemampuan Petugas Pelayanan',
            'Kesopanan Petugas Layanan',
            'Kualitas Sarana Prasarana',
            'Penanganan Pengaduan',
        ];
    }
    /**
     * Display a listing of the resource.
     */

    public function index(Kuesioner $kuesioner)
    {
        $pertanyaans = $this->service->getByKuesioner($kuesioner);

        return Inertia::render('admin/kuesioner/Pertanyaan', [
            'kuesioner'   => $kuesioner->only(['id', 'judul', 'deskripsi']),
            'pertanyaans' => $pertanyaans,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Kuesioner $kuesioner)
    {
        // lempar ke form Vue
        return Inertia::render('admin/kuesioner/Kelola', [
            'kuesioner' => $kuesioner,
            'unsurs' => collect($this->unsur)->map(fn($u) => [
                'label' => $u,
                'value' => $u,
            ]),
            'mode' => 'create'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PertanyaanRequest $request, Kuesioner $kuesioner)
    {
        try {
            DB::beginTransaction();

            $this->service->storeMany(
                $request->validated()['pertanyaans'],
                $kuesioner->id
            );

            DB::commit();

            return redirect()
                ->route('admin.kuesioner.pertanyaan.index', $kuesioner->id)
                ->with('success', 'Pertanyaan berhasil ditambahkan.');
        } catch (Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pertanyaan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kuesioner $kuesioner, Pertanyaan $pertanyaan)
    {
        $pertanyaan->load('opsiJawaban');

        return Inertia::render('admin/kuesioner/Kelola', [
            'kuesioner' => $kuesioner,
            'unsurs' => collect($this->unsur)->map(fn($u) => [
                'label' => $u,
                'value' => $u,
            ]),
            'pertanyaans' => [[
                'id' => $pertanyaan->id,
                'teks' => $pertanyaan->teks,
                'unsur' => $pertanyaan->unsur, // pastikan kolomnya string, sesuai value di unsurs
                'opsi' => $pertanyaan->opsiJawaban->map(fn($o) => [
                    'teks' => $o->teks,
                    'skor' => $o->skor,
                ])->toArray(),
            ]],
            'mode' => 'edit',
        ]);
    }



    public function bulkEdit(Request $request, Kuesioner $kuesioner)
    {
        $ids = $request->query('ids', []);
        $pertanyaans = $kuesioner->pertanyaan()
            ->whereIn('id', $ids)
            ->with('opsiJawaban')
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'teks' => $p->teks,
                'unsur' => $p->unsur, // langsung string
                'opsi' => $p->opsiJawaban->map(fn($o) => [
                    'teks' => $o->teks,
                    'skor' => $o->skor,
                ])->toArray(),
            ]);

        return Inertia::render('admin/kuesioner/Kelola', [
            'kuesioner' => $kuesioner,
            'unsurs' => collect($this->unsur)->map(fn($u) => [
                'label' => $u,
                'value' => $u,
            ]),
            'pertanyaans' => $pertanyaans,
            'mode' => 'bulk',
        ]);
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(PertanyaanRequest $request, Kuesioner $kuesioner, Pertanyaan $pertanyaan)
    {
        try {
            DB::beginTransaction();

            $this->service->update($pertanyaan, $request->validated());

            DB::commit();

            return redirect()
                ->route('admin.kuesioner.pertanyaan.index', $kuesioner->id)
                ->with('success', 'Pertanyaan berhasil diperbarui.');
        } catch (Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui pertanyaan: ' . $e->getMessage());
        }
    }


    public function bulkUpdate(PertanyaanRequest $request, Kuesioner $kuesioner)
    {
        try {
            DB::transaction(function () use ($request, $kuesioner) {
                $this->service->updateBulk($kuesioner, $request->validated()['pertanyaans']);
            });

            return redirect()
                ->route('admin.kuesioner.pertanyaan.index', $kuesioner->id)
                ->with('success', 'Pertanyaan berhasil diperbarui.');
        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal update pertanyaan: ' . $e->getMessage());
        }
    }


    // Hapus satu pertanyaan
    public function destroy(Kuesioner $kuesioner, Pertanyaan $pertanyaan)
    {
        try {
            DB::beginTransaction();

            $pertanyaan->delete();

            DB::commit();

            return back()->with('success', 'Pertanyaan berhasil dihapus.');
        } catch (Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menghapus pertanyaan: ' . $e->getMessage());
        }
    }

    // Hapus banyak pertanyaan sekaligus
    public function bulkDestroy(Request $request, Kuesioner $kuesioner)
    {
        try {
            DB::beginTransaction();

            $ids = $request->input('ids', []);

            if (!empty($ids)) {
                Pertanyaan::whereIn('id', $ids)->delete();
            }

            DB::commit();

            return back()->with('success', 'Pertanyaan terpilih berhasil dihapus.');
        } catch (Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menghapus pertanyaan: ' . $e->getMessage());
        }
    }
}
