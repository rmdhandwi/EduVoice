<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        // Jika user sudah login
        if (Auth::check()) {
            $role = Auth::user()->role;
            $user = Auth::user();
            // Redirect berdasarkan role
            return redirect()->route(match ($role) {
                'admin' => 'admin.dashboard',
                'kepala' => 'kepala.dashboard',
                default => 'home',
            })->with('success', 'Sudah Login sebagai ' . $user->name);
        }

        return Inertia::render('Welcome');
    }
}
