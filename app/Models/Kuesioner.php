<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Kuesioner extends Model
{
    use HasUuids;

    protected $table = 'kuesioners';
    protected $primaryKey = 'id';
    public $incrementing = false;   // karena bukan auto-increment
    protected $keyType = 'string'; // UUID adalah string
    protected $fillable = [
        'judul',
        'deskripsi',
        'created_at',
        'updated_at'
    ];
    public $timestamps = true;


    public function pertanyaans()
    {
        return $this->hasMany(Pertanyaan::class, 'kuesioner_id');
    }
}
