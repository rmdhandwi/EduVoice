<?php

namespace App\Http\Requests\Kuesioner;

use Illuminate\Foundation\Http\FormRequest;

class PertanyaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // 🔹 Cek apakah request berupa array "pertanyaans" (bulk/create)
        // atau flat field "teks/unsur/opsi" (update single)
        if ($this->has('pertanyaans')) {
            return [
                'pertanyaans'               => 'required|array|min:1',
                'pertanyaans.*.id'          => 'nullable|exists:pertanyaans,id',
                'pertanyaans.*.teks'        => 'required|string|max:500',
                'pertanyaans.*.unsur'       => 'required|string|max:255',
                'pertanyaans.*.opsi'        => 'required|array|min:1',
                'pertanyaans.*.opsi.*.id'   => 'nullable|integer|exists:opsi_jawabans,id',
                'pertanyaans.*.opsi.*.teks' => 'required|string|max:255',
                'pertanyaans.*.opsi.*.skor' => 'required|integer|min:1',
            ];
        }

        // 🔹 Update single pertanyaan
        return [
            'teks'        => 'required|string|max:500',
            'unsur'       => 'required|string|max:255',
            'opsi'        => 'required|array|min:1',
            'opsi.*.id'   => 'nullable|integer|exists:opsi_jawabans,id',
            'opsi.*.teks' => 'required|string|max:255',
            'opsi.*.skor' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'pertanyaans.required'               => 'Minimal 1 pertanyaan harus diisi.',
            'pertanyaans.*.teks.required'        => 'Teks pertanyaan wajib diisi.',
            'pertanyaans.*.unsur.required'       => 'Unsur pelayanan wajib dipilih.',
            'pertanyaans.*.opsi.required'        => 'Setiap pertanyaan wajib memiliki opsi jawaban.',
            'pertanyaans.*.opsi.*.teks.required' => 'Teks opsi jawaban wajib diisi.',
            'pertanyaans.*.opsi.*.teks.string'   => 'Harus berupa string',
            'pertanyaans.*.opsi.*.skor.required' => 'Skor opsi jawaban wajib diisi.',

            'teks.required'        => 'Teks pertanyaan wajib diisi.',
            'unsur.required'       => 'Unsur pelayanan wajib dipilih.',
            'opsi.required'        => 'Pertanyaan wajib memiliki minimal 1 opsi jawaban.',
            'opsi.*.teks.required' => 'Teks opsi jawaban wajib diisi.',
            'opsi.*.skor.required' => 'Skor opsi jawaban wajib diisi.',
        ];
    }
}
