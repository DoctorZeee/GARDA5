<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === UserRole::Admin->value;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;
        $roles  = implode(',', array_column(UserRole::cases(), 'value'));

        return [
            'nik'                   => ['required', 'string', 'size:16', "unique:users,nik,{$userId}", 'regex:/^[0-9]+$/'],
            'nama_lengkap'          => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email:rfc,dns', "unique:users,email,{$userId}", 'max:255'],
            // Nullable on update — only validated if provided
            'password'              => ['nullable', 'confirmed', Password::defaults()],
            'password_confirmation' => ['nullable', 'string'],
            'role'                  => ['required', "in:{$roles}"],
            'tempat_lahir'          => ['required', 'string', 'max:100'],
            'tanggal_lahir'         => ['required', 'date', 'before_or_equal:today'],
            'jenis_kelamin'         => ['required', 'in:L,P'],
            'alamat'                => ['required', 'string', 'max:1000'],
            'berat_badan'           => ['required', 'numeric', 'min:10', 'max:300'],
            'tekanan_darah'         => ['nullable', 'string', 'regex:/^\d{2,3}\/\d{2,3}$/'],
            'wilayah_id'            => ['nullable', 'exists:wilayahs,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.min'           => 'Password minimal 8 karakter.',
            'password.letters'       => 'Password harus mengandung huruf.',
            'password.numbers'       => 'Password harus mengandung angka.',
            'password.uncompromised' => 'Password terlalu umum atau pernah bocor.',
            'password.confirmed'     => 'Konfirmasi password tidak cocok.',
            'nik.size'               => 'NIK harus tepat 16 digit.',
            'nik.regex'              => 'NIK hanya boleh berisi angka.',
            'nik.unique'             => 'NIK sudah terdaftar di sistem.',
            'email.unique'           => 'Email sudah digunakan akun lain.',
            'tekanan_darah.regex'    => 'Format tekanan darah tidak valid. Contoh: 120/80',
        ];
    }
}
