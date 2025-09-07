<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Pertanyaan extends Model
{
    use HasUuids;

    protected $table = 'pertanyaans';
    protected $primaryKey = 'id';
    public $incrementing = false;   // karena bukan auto-increment
    protected $keyType = 'string'; // UUID adalah string
    protected $fillable = [
        'kuesioner_id',
        'teks',
        'tipe',
        'created_at',
        'updated_at'
    ];
    
    public $timestamps = true;

    public function kuesioner()
    {
        return $this->belongsTo(Kuesioner::class, 'kuesioner_id');
    }

    public function opsiJawabans()
    {
        return $this->hasMany(OpsiJawaban::class, 'pertanyaan_id');
    }
}
