<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Responden;
use App\Services\RespondenService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RespondenController extends Controller
{

    protected $service;

    public function __construct(RespondenService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $respondens = $this->service->getAll();

        return Inertia::render('admin/responden/Index', [
            'respondens' => $respondens,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Responden $responden)
    {
        if (!$responden) {
            return redirect()
                ->route('admin.responden.index')
                ->with('error', 'Responden tidak ditemukan.');
        }

        $data = $this->service->getDetailForResponden($responden);
        $pertanyaan = $this->service->getPertanyaan();

        return Inertia::render('admin/responden/Detail', [
            'responden' => $data,
            'pertanyaan' => $pertanyaan
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Responden $responden)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Responden $responden)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Responden $responden)
    {
        //
    }
}
