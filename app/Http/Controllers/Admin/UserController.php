<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Models\Wilayah;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', User::class);

        $users = User::with(['wilayah', 'point'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        Gate::authorize('create', User::class);

        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();
        $roles    = UserRole::cases();

        return view('admin.users.create', compact('wilayahs', 'roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $validated = $request->validated();
        unset($validated['password_confirmation']);

        // Role must be set explicitly (not through mass assignment)
        $role = $validated['role'];
        unset($validated['role']);

        $user = User::create($validated);
        $user->role = $role;
        $user->save();

        if ($user->role === UserRole::User->value) {
            $user->point()->create([
                'total_points'   => 0,
                'total_leaves'   => 0,
                'checkin_streak' => 0,
                'checkin_count'  => 0,
            ]);
        }

        AuditLogger::log('CREATE_USER', "Admin mendaftarkan akun baru NIK: {$user->nik} ({$user->role})");

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        Gate::authorize('update', $user);

        $wilayahs = Wilayah::orderBy('nama_wilayah')->get();
        $roles    = UserRole::cases();

        return view('admin.users.edit', compact('user', 'wilayahs', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $validated = $request->validated();
        unset($validated['password_confirmation']);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        // Role via explicit assignment
        $role = $validated['role'];
        unset($validated['role']);

        $user->update($validated);
        $user->role = $role;
        $user->save();

        if ($user->role === UserRole::User->value && ! $user->point) {
            $user->point()->create([
                'total_points'   => 0,
                'total_leaves'   => 0,
                'checkin_streak' => 0,
                'checkin_count'  => 0,
            ]);
        }

        AuditLogger::log('UPDATE_USER', "Admin memperbarui data akun NIK: {$user->nik}");

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        $nik  = $user->nik;
        $name = $user->nama_lengkap;

        // Soft delete — data & audit trail preserved
        $user->delete();

        AuditLogger::log('DELETE_USER', "Admin menghapus akun: {$name} (NIK: {$nik})");

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna {$name} berhasil dihapus.");
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore(int $id): RedirectResponse
    {
        /** @var User $user */
        $user = User::onlyTrashed()->findOrFail($id);
        Gate::authorize('restore', $user);

        $user->restore();
        AuditLogger::log('RESTORE_USER', "Admin memulihkan akun NIK: {$user->nik}");

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dipulihkan.');
    }
}
