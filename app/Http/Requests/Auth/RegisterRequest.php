<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public registration
    }

    public function rules(): array
    {
        return [
            'nik'                   => ['required', 'string', 'size:16', 'unique:users,nik', 'regex:/^[0-9]+$/'],
            'nama_lengkap'          => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email:rfc,dns', 'unique:users,email', 'max:255'],
            'password'              => ['required', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required', 'string'],
            'tempat_lahir'          => ['required', 'string', 'max:100'],
            'tanggal_lahir'         => ['required', 'date', 'before_or_equal:today'],
            'jenis_kelamin'         => ['required', 'in:L,P'],
            'alamat'                => ['required', 'string', 'max:1000'],
            'berat_badan'           => ['required', 'numeric', 'min:10', 'max:300'],
            'wilayah_id'            => ['required', 'exists:wilayahs,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.min'           => 'Password minimal 8 karakter.',
            'password.letters'       => 'Password harus mengandung huruf.',
            'password.numbers'       => 'Password harus mengandung angka.',
            'password.uncompromised' => 'Password terlalu umum atau pernah bocor. Gunakan password yang berbeda.',
            'password.confirmed'     => 'Konfirmasi password tidak cocok.',
            'nik.size'               => 'NIK harus tepat 16 digit.',
            'nik.regex'              => 'NIK hanya boleh berisi angka.',
            'nik.unique'             => 'NIK sudah terdaftar di sistem.',
            'email.unique'           => 'Email sudah digunakan akun lain.',
        ];
    }
}
