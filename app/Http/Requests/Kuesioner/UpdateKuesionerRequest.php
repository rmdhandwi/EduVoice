<?php

namespace App\Http\Requests\Kuesioner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKuesionerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'pertanyaans' => 'required|array|min:1',

            'pertanyaans.*.teks' => 'required|string',
            'pertanyaans.*.tipe' => 'required|in:text,number,date,radio,checkbox',

            'pertanyaans.*.opsi' => 'nullable|array',
            'pertanyaans.*.opsi.*.teks' => 'required_with:pertanyaans.*.opsi|string',
            'pertanyaans.*.opsi.*.skor' => 'numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul kuesioner wajib diisi.',
            'judul.max' => 'Judul kuesioner tidak boleh lebih dari 255 karakter.',
            'pertanyaans.required' => 'Minimal harus ada satu pertanyaan.',
            'pertanyaans.*.teks.required' => 'Teks pertanyaan wajib diisi.',
            'pertanyaans.*.tipe.required' => 'Tipe pertanyaan wajib dipilih.',
            'pertanyaans.*.tipe.in' => 'Tipe pertanyaan tidak valid.',
            'pertanyaans.*.opsi.*.teks.required_with' => 'Teks opsi jawaban wajib diisi.',
            'pertanyaans.*.opsi.*.skor.numeric' => 'Skor jawaban harus berupa angka.',
            'pertanyaans.*.opsi.*.skor.min' => 'Skor jawaban tidak boleh negatif.',
        ];
    }
}
