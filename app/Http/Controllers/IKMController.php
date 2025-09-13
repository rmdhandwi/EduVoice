<?php

namespace App\Http\Controllers;

use App\Models\Kuesioner;
use App\Services\IKMService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IKMController extends Controller
{
    protected $ikmService;

    public function __construct(IKMService $ikmService)
    {
        $this->ikmService = $ikmService;
    }

    // halaman index
    public function index()
    {
        $data = $this->ikmService->hitungIKMPerKuesioner();

        return Inertia::render('admin/IKM/Index', [
            'ikms' => $data,
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

        return Inertia::render('admin/IKM/Detail', [
            'kuesioner' => $kuesioner,
            ...$result,
        ]);
    }
}
