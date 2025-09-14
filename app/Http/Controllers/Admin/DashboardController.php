<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kuesioner;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(DashboardService $dashboardService)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $summary = $dashboardService->getSummary();
        $charts = $dashboardService->getCharts();
        $kuesioners = Kuesioner::select('id', 'judul')->get();

        return Inertia::render('Dashboard', [
            'summary'    => $summary,
            'charts'     => $charts,
            'kuesioners' => $kuesioners,
        ]);
    }

    public function ini()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }
        return Inertia::render('ErrorPage');
    }
}
