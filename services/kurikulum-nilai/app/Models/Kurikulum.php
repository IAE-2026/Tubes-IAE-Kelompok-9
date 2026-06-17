<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    protected $fillable = [
        'kode_matkul',
        'nama_matkul',
        'sks',
        'semester',
        'prodi',
        'prasyarat',
        'deskripsi',
    ];

    /**
     * Relasi ke nilai berdasarkan kode_matkul.
     */
    public function nilais()
    {
        return $this->hasMany(Nilai::class, 'kode_matkul', 'kode_matkul');
    }
}
