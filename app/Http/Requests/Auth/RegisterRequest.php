<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Bisa diakses oleh publik (guest)
    }

    public function rules(): array
    {
        return [
            'nik'                   => ['required', 'string', 'size:16', 'unique:users,nik', 'regex:/^[0-9]+$/'],
            'nama_lengkap'          => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
            'tempat_lahir'          => ['required', 'string', 'max:100'],
            'tanggal_lahir'         => ['required', 'date', 'before_or_equal:today'],
            'jenis_kelamin'         => ['required', 'in:L,P'],
            'alamat'                => ['required', 'string'],
            'berat_badan'           => ['required', 'numeric', 'min:10', 'max:300'],
            'wilayah_id'            => ['required', 'exists:wilayahs,id'],
        ];
    }
}
