<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'jawabans';
    protected $primaryKey = 'id';

    protected $fillable = [
        'pertanyaan_id',
        'repondens_id',
        'teks',
        'angka',
        'tanggal',
        'saran',
        'kritik',
        'opsi_id',
    ];

    public function pertanyaans()
    {
        return $this->belongsTo(Pertanyaan::class, 'pertanyaan_id');
    }

    public function respondens()
    {
        return $this->belongsTo(User::class, 'repondens_id');
    }

    public function opsiJawabans()
    {
        return $this->belongsTo(OpsiJawaban::class, 'opsi_id');
    }
}
