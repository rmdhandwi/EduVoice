<?php

namespace App\Http\Requests\Responden;

use Illuminate\Foundation\Http\FormRequest;

class RespondenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // UUID opsional → jangan langsung pakai exists, biar tidak error saat null
            'responden_id' => ['nullable', 'uuid'],
            'kuesioner_id' => ['required', 'uuid', 'exists:kuesioners,id'],

            'name' => ['required', 'string', 'max:255'],
            'jk' => ['required', 'in:Laki-Laki,Perempuan'],
            'umur' => ['required', 'integer', 'min:10', 'max:120'],
            'pekerjaan' => ['required', 'string', 'max:255'],
            'pendidikan_terakhir' => ['required', 'string', 'max:255'],

            // jawaban array
            'jawaban' => ['required', 'array'],
            'jawaban.*.pertanyaan_id' => ['required', 'uuid', 'exists:pertanyaans,id'],
            'jawaban.*.opsi_id' => ['nullable'], // validasi detail di withValidator

            // teks/angka/tanggal
            'jawaban.*.teks' => ['nullable', 'string'],
            'jawaban.*.angka' => ['nullable', 'numeric'],
            'jawaban.*.tanggal' => ['nullable', 'date'],
            'kritik' => ['nullable', 'string'],
            'saran' => ['nullable', 'string']
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $jawabans = $this->input('jawaban', []);

            foreach ($jawabans as $idx => $jawab) {
                $tipe = $jawab['tipe'] ?? null;

                switch ($tipe) {
                    case 'radio':
                        if (empty($jawab['opsi_id'])) {
                            $validator->errors()->add("jawaban.$idx.opsi_id", 'Harap pilih salah satu opsi.');
                        }
                        break;

                    case 'checkbox':
                        if (!is_array($jawab['opsi_id'] ?? null) || count($jawab['opsi_id']) === 0) {
                            $validator->errors()->add("jawaban.$idx.opsi_id", 'Harap pilih minimal satu opsi.');
                        }
                        break;

                    case 'text':
                        if (empty($jawab['teks'])) {
                            $validator->errors()->add("jawaban.$idx.teks", 'Jawaban teks wajib diisi.');
                        }
                        break;

                    case 'number':
                        if (!isset($jawab['angka'])) {
                            $validator->errors()->add("jawaban.$idx.angka", 'Jawaban angka wajib diisi.');
                        }
                        break;

                    case 'date':
                        if (empty($jawab['tanggal'])) {
                            $validator->errors()->add("jawaban.$idx.tanggal", 'Tanggal wajib dipilih.');
                        }
                        break;
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'jk.required' => 'Jenis kelamin wajib dipilih.',
            'umur.required' => 'Umur wajib diisi.',
            'pekerjaan.required' => 'Pekerjaan wajib diisi.',
            'pendidikan_terakhir.required' => 'Pendidikan terakhir wajib diisi.',
            'jawaban.required' => 'Jawaban wajib diisi.',
        ];
    }
}
