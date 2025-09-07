<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Responden extends Model
{
    protected $table = 'respondens';
    protected $primaryKey = 'id';
    public $incrementing = false;   // karena bukan auto-increment
    protected $keyType = 'string'; // UUID adalah string
    protected $fillable = [
        'nama',
        'jk',
        'umur',
        'pekerjaan',
        'pendidikan_akhir',
        'created_at',
        'updated_at'
    ];
    
    public $timestamps = true;

    public function jawabans()
    {
        return $this->hasMany(Jawaban::class, 'respondens_id');
    }

    
}
