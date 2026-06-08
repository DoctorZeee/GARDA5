<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreHealthLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'user';
    }

    public function rules(): array
    {
        return [
            'tekanan_darah' => ['nullable', 'string', 'regex:/^\d{2,3}\/\d{2,3}$/'],
            'berat_badan' => ['required', 'numeric', 'min:10', 'max:250'],
            'tinggi_badan' => ['required', 'integer', 'min:50', 'max:250'],
            'konsumsi_garam' => ['required', 'in:less,ideal,more'],
            'keluhan' => ['nullable', 'string', 'max:500'],
        ];
    }
}