<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Wilayah;
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

        AuditLog::create([
            'user_id' => Auth::id(), // Gunakan cara ini
            'action' => 'CREATE_USER',
            'description' => "Admin mendaftarkan akun baru NIK: {$user->nik} ({$user->role})",
            'ip_address' => request()->ip()
        ]);

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

        // Jika form password diisi, hash password baru. Jika kosong, hapus dari array agar tidak ter-update kosong.
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // Jika role diubah menjadi 'user' tapi dia belum punya relasi point, buatkan.
        if ($user->role === 'user' && !$user->point) {
            $user->point()->create(['total_points' => 0, 'total_leaves' => 0]);
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'UPDATE_USER',
            'description' => "Admin memperbarui data akun NIK: {$user->nik}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $nik = $user->nik; // Simpan NIK sementara untuk keperluan Log

        $user->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'DELETE_USER',
            'description' => "Admin menghapus akun NIK: {$nik}",
            'ip_address' => request()->ip()
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
