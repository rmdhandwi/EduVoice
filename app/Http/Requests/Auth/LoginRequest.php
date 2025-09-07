<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rules validasi
     */
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9_]+$/',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
            ],
        ];
    }

    /**
     * Custom message error
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Username wajib diisi.',
            'username.string'   => 'Username tidak valid.',
            'username.unique'   => 'Username sudah digunakan, silakan pilih yang lain.',
            'username.regex'    => 'Username hanya boleh huruf, angka, dan underscore (_), tanpa spasi.',

            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal harus 6 karakter.',
        ];
    }
}
