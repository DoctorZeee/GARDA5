<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        // Mengambil ID user dari route (misal: /admin/users/{user})
        $userId = $this->route('user')->id;

        return [
            'nik' => ['required', 'string', 'size:16', 'unique:users,nik,' . $userId, 'regex:/^[0-9]+$/'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $userId],
            'password' => ['nullable', 'string', 'min:8'], // Nullable saat edit
            'role' => ['required', 'in:admin,puskesmas,kader,user'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'alamat' => ['required', 'string'],
            'berat_badan' => ['required', 'numeric', 'min:10', 'max:300'],
            'tekanan_darah' => ['nullable', 'string', 'regex:/^\d{2,3}\/\d{2,3}$/'],
            'wilayah_id' => ['nullable', 'exists:wilayahs,id']
        ];
    }
}