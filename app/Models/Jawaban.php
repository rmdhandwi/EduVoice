<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    use HasUuids;

    protected $table = 'jawabans';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';

    protected $fillable = [
        'pertanyaan_id',
        'respondens_id',
        'teks',
        'opsi_id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public $timestamps = true;


    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class, 'pertanyaan_id');
    }

    public function responden()
    {
        return $this->belongsTo(Responden::class, 'respondens_id');
    }

    public function opsiJawaban()
    {
        return $this->belongsTo(OpsiJawaban::class, 'opsi_id');
    }
}
