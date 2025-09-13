<?php

namespace App\Http\Controllers;

use App\Models\Kuesioner;
use App\Services\IKMService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $ikmService;

    public function __construct(IKMService $ikmService)
    {
        $this->ikmService = $ikmService;
    }

    // halaman index
    public function index()
    {
        $data = $this->ikmService->hitungIKMPerKuesioner();

        return Inertia::render('laporan/Index', [
            'laporan' => $data,
        ]);
    }

     // halaman detail
    public function show($id)
    {
        $kuesioner = Kuesioner::with('pertanyaan.opsiJawaban')->find($id);

        if (!$kuesioner) {
            return redirect()->back()->with('error', 'ID tidak Valid');
        }


        $result = $this->ikmService->hitungDetail($kuesioner);

        return Inertia::render('laporan/Detail', [
            'kuesioner' => $kuesioner,
            ...$result,
        ]);
    }
}
