<?php

namespace Database\Seeders;

use App\Models\Kurikulum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KurikulumSeeder extends Seeder
{
    /**
     * Seed data kurikulum (mata kuliah) untuk prodi S1 Sistem Informasi.
     */
    public function run(): void
    {
        $kurikulums = [
            [
                'kode_matkul' => 'SI101',
                'nama_matkul' => 'Algoritma dan Pemrograman',
                'sks' => 3,
                'semester' => 1,
                'prodi' => 'S1 Sistem Informasi',
                'prasyarat' => null,
                'deskripsi' => 'Mata kuliah dasar pemrograman menggunakan konsep algoritma dan logika.',
            ],
            [
                'kode_matkul' => 'SI102',
                'nama_matkul' => 'Matematika Diskrit',
                'sks' => 3,
                'semester' => 1,
                'prodi' => 'S1 Sistem Informasi',
                'prasyarat' => null,
                'deskripsi' => 'Konsep dasar matematika untuk ilmu komputer.',
            ],
            [
                'kode_matkul' => 'SI103',
                'nama_matkul' => 'Pengantar Sistem Informasi',
                'sks' => 3,
                'semester' => 1,
                'prodi' => 'S1 Sistem Informasi',
                'prasyarat' => null,
                'deskripsi' => 'Pengenalan konsep dasar sistem informasi dan perannya dalam organisasi.',
            ],
            [
                'kode_matkul' => 'SI201',
                'nama_matkul' => 'Struktur Data',
                'sks' => 3,
                'semester' => 2,
                'prodi' => 'S1 Sistem Informasi',
                'prasyarat' => 'SI101',
                'deskripsi' => 'Studi tentang penyimpanan dan pengorganisasian data secara efisien.',
            ],
            [
                'kode_matkul' => 'SI202',
                'nama_matkul' => 'Basis Data',
                'sks' => 4,
                'semester' => 2,
                'prodi' => 'S1 Sistem Informasi',
                'prasyarat' => 'SI101',
                'deskripsi' => 'Perancangan dan pengelolaan basis data relasional.',
            ],
            [
                'kode_matkul' => 'SI301',
                'nama_matkul' => 'Pemrograman Berorientasi Objek',
                'sks' => 3,
                'semester' => 3,
                'prodi' => 'S1 Sistem Informasi',
                'prasyarat' => 'SI201',
                'deskripsi' => 'Paradigma pemrograman OOP menggunakan Java atau Python.',
            ],
            [
                'kode_matkul' => 'SI302',
                'nama_matkul' => 'Jaringan Komputer',
                'sks' => 3,
                'semester' => 3,
                'prodi' => 'S1 Sistem Informasi',
                'prasyarat' => null,
                'deskripsi' => 'Konsep dasar jaringan komputer dan protokol komunikasi.',
            ],
            [
                'kode_matkul' => 'SI401',
                'nama_matkul' => 'Rekayasa Perangkat Lunak',
                'sks' => 4,
                'semester' => 4,
                'prodi' => 'S1 Sistem Informasi',
                'prasyarat' => 'SI301',
                'deskripsi' => 'Proses pengembangan perangkat lunak secara sistematis.',
            ],
            [
                'kode_matkul' => 'SI402',
                'nama_matkul' => 'Integrasi Aplikasi Enterprise',
                'sks' => 3,
                'semester' => 4,
                'prodi' => 'S1 Sistem Informasi',
                'prasyarat' => 'SI202',
                'deskripsi' => 'Konsep integrasi sistem dan layanan enterprise menggunakan API dan middleware.',
            ],
            [
                'kode_matkul' => 'SI501',
                'nama_matkul' => 'Keamanan Sistem Informasi',
                'sks' => 3,
                'semester' => 5,
                'prodi' => 'S1 Sistem Informasi',
                'prasyarat' => 'SI302',
                'deskripsi' => 'Keamanan informasi, kriptografi, dan manajemen risiko.',
            ],
        ];

        foreach ($kurikulums as $kurikulum) {
            Kurikulum::create($kurikulum);
        }
    }
}
