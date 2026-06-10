<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'youtube_id'    => ['required', 'string', 'max:20', 'regex:/^[a-zA-Z0-9_\-]+$/'],
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string', 'max:1000'],
            'points_reward' => ['required', 'integer', 'min:1', 'max:100'],
            'is_active'     => ['nullable', 'boolean'],
            'sort_order'    => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    public function messages(): array
    {
        return [
            'youtube_id.regex' => 'YouTube ID hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($url = $this->input('youtube_id')) {
            if (preg_match('/(?:youtu\.be\/|v=|embed\/)([a-zA-Z0-9_\-]{11})/', $url, $m)) {
                $this->merge(['youtube_id' => $m[1]]);
            }
        }

        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
