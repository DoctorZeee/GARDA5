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

        // Langsung simpan karena $validated sudah berisi 'wilayah_id' yang valid
        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'user';

        $user = User::create($validated);
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

        return match (Auth::user()->role) {
            'admin' => redirect()->intended(route('admin.dashboard')),
            'puskesmas' => redirect()->intended(route('puskesmas.dashboard')),
            'kader' => redirect()->intended(route('kader.dashboard')),
            'user' => redirect()->intended(route('user.dashboard')),
            default => redirect('/'),
        };
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
