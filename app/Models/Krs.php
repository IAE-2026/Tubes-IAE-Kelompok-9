<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Krs extends Model
{
    protected $table = 'krs';

    protected $fillable = [
        'nim',
        'kode_mata_kuliah',
        'nama_mata_kuliah',
        'sks',
        'tahun_ajaran',
        'semester',
        'status_persetujuan',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'sks' => 'integer',
        ];
    }
}
