<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
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

        return Inertia::render('auth/Login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $user = User::where('username', $request->username)->first();

        if (! $user) {
            return back()
                ->withErrors(['username' => 'Username tidak terdaftar.']);
        }

        if (! Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['password' => 'Password salah.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, ' . $user->name);
            case 'kepala':
                return redirect()->route('kepala.dashboard')->with('success', 'Selamat datang, ' . $user->name);
            default:
                Auth::logout();
                return redirect()->route('login')->with('error', 'Role tidak dikenali.');
        }

        return back()->with('error', 'Gagal login, periksa kembali username dan password.');
    }


    public function logout(): RedirectResponse
    {
        Auth::guard()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }
}
