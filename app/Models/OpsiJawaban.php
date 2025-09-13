<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class OpsiJawaban extends Model
{
    use HasUuids;

    protected $table = 'opsi_jawabans';
    protected $primaryKey = 'id';
    public $incrementing = false;   // karena bukan auto-increment
    protected $keyType = 'string'; // UUID adalah string
    protected $fillable = [
        'pertanyaan_id',
        'teks',
        'skor',
        'created_at',
        'updated_at'
    ];
    public $timestamps = true;

    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class, 'pertanyaan_id');
    }
}
