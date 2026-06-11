<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showRegister()
    {
        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();
        return view('auth.register', compact('wilayahs'));
    }

    public function processRegister(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Role is forced to 'user' — never trust input for role assignment.
        unset($validated['role'], $validated['password_confirmation']);

        $user = User::create($validated);
        $user->role = UserRole::User->value;
        $user->save();

        $user->point()->create([
            'total_points'   => 0,
            'total_leaves'   => 0,
            'checkin_streak' => 0,
            'checkin_count'  => 0,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('user.dashboard')
            ->with('success', 'Pendaftaran berhasil! Selamat datang di GARDA.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function authenticate(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        /** @var User $authUser */
        $authUser = Auth::user();
        $role = UserRole::fromString($authUser->role);

        if ($role === null) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Peran akun tidak dikenali. Hubungi administrator.');
        }

        return redirect()->intended(route($role->dashboardRoute()));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dengan aman.');
    }
}
