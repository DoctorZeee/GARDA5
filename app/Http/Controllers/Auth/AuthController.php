<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        $wilayahs = Wilayah::all();
        return view('auth.register', compact('wilayahs'));
    }

    public function processRegister(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        // Role di-set secara eksplisit SETELAH create (tidak lewat fillable)
        // untuk mencegah privilege escalation
        $user = User::create($validated);
        $user->role = 'user';
        $user->save();

        $user->point()->create(['total_points' => 0, 'total_leaves' => 0]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('user.dashboard')->with('success', 'Pendaftaran berhasil!');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function authenticate(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // FIX: tambah flash message jika role tidak dikenali
        return match (Auth::user()->role) {
            'admin'     => redirect()->intended(route('admin.dashboard')),
            'puskesmas' => redirect()->intended(route('puskesmas.dashboard')),
            'kader'     => redirect()->intended(route('kader.dashboard')),
            'user'      => redirect()->intended(route('user.dashboard')),
            default     => redirect('/')->with('error', 'Peran akun tidak dikenali. Hubungi administrator.'),
        };
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah berhasil keluar dengan aman. Tetap pantau dosis garam harian Anda!');
    }
}
