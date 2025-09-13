<?php

namespace App\Http\Requests\Kuesioner;

use Illuminate\Foundation\Http\FormRequest;

class KuesionerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ];
    }

     public function messages(): array
    {
        return [
            'judul.required' => 'Judul kuesioner wajib diisi.',
            'judul.max' => 'Judul kuesioner tidak boleh lebih dari 255 karakter.',
        ];
    }

}
