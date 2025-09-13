<?php

namespace App\Http\Controllers\Kuesioner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kuesioner\KuesionerRequest;
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
    public function create(Kuesioner $kuesioner)
    {
        return Inertia::render('admin/kuesioner/Pertanyaan');
    }

    public function store(KuesionerRequest $request)
    {
        $validate = $request->validated();

        try {
            DB::beginTransaction();

            Kuesioner::create($validate);

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
    public function show(Kuesioner $kuesioner)
    {
        $data = $this->service->getDetailForView($kuesioner);

        return inertia('admin/kuesioner/Detail', [
            'kuesioner' => $data,
        ]);
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
    public function update(KuesionerRequest $request, Kuesioner $kuesioner)
    {
        $validate = $request->validated();

        try {
            DB::beginTransaction();

            $kuesioner->update($validate);

            DB::commit();

            return redirect()
                ->route('admin.kuesioner.index')
                ->with('success', 'Kuesioner berhasil diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
}
