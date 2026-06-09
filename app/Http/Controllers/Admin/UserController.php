<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Models\Wilayah;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['wilayah', 'point'])->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $wilayahs = Wilayah::all();
        return view('admin.users.create', compact('wilayahs'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        if ($user->role === 'user') {
            $user->point()->create(['total_points' => 0, 'total_leaves' => 0]);
        }

        // FIX: Pakai AuditLogger::log() bukan AuditLog::create() langsung
        // → route, method, user_agent kini ikut tercatat
        AuditLogger::log('CREATE_USER', "Admin mendaftarkan akun baru NIK: {$user->nik} ({$user->role})");

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $wilayahs = Wilayah::all();
        return view('admin.users.edit', compact('user', 'wilayahs'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if ($user->role === 'user' && !$user->point) {
            $user->point()->create(['total_points' => 0, 'total_leaves' => 0]);
        }

        // FIX: Pakai AuditLogger::log()
        AuditLogger::log('UPDATE_USER', "Admin memperbarui data akun NIK: {$user->nik}");

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $nik = $user->nik;
        $user->delete();

        // FIX: Pakai AuditLogger::log()
        AuditLogger::log('DELETE_USER', "Admin menghapus akun NIK: {$nik}");

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
