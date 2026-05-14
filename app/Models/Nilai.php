<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $fillable = [
        'nim',
        'kode_matkul',
        'nama_matkul',
        'nilai_huruf',
        'nilai_angka',
        'sks',
        'semester',
        'tahun_ajaran',
    ];

    /**
     * Relasi ke kurikulum berdasarkan kode_matkul.
     */
    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kode_matkul', 'kode_matkul');
    }
}
