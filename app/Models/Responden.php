<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Responden extends Model
{
    protected $table = 'respondens';
    protected $primaryKey = 'id';
    public $incrementing = false;   // karena bukan auto-increment
    protected $keyType = 'string'; // UUID adalah string
    protected $fillable = [
        'name',
        'jk',
        'umur',
        'pekerjaan',
        'pendidikan_terakhir',
        'saran',
        'kritik',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    public function jawaban()
    {
        return $this->hasMany(Jawaban::class, 'respondens_id');
    }

    protected static function booted()
    {
        parent::booted();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

}
