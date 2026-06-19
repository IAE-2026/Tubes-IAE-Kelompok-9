<?php

namespace Database\Seeders;

use App\Models\Nilai;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NilaiSeeder extends Seeder
{
    /**
     * Seed data nilai mahasiswa contoh.
     */
    public function run(): void
    {
        $nilais = [
            // Semester 1 - NIM 102022400136 (Arneta Alifiana)
            [
                'nim' => '102022400136',
                'kode_matkul' => 'SI101',
                'nama_matkul' => 'Algoritma dan Pemrograman',
                'nilai_huruf' => 'A',
                'nilai_angka' => 4.0,
                'sks' => 3,
                'semester' => 1,
                'tahun_ajaran' => '2024/2025',
            ],
            [
                'nim' => '102022400136',
                'kode_matkul' => 'SI102',
                'nama_matkul' => 'Matematika Diskrit',
                'nilai_huruf' => 'AB',
                'nilai_angka' => 3.5,
                'sks' => 3,
                'semester' => 1,
                'tahun_ajaran' => '2024/2025',
            ],
            [
                'nim' => '102022400136',
                'kode_matkul' => 'SI103',
                'nama_matkul' => 'Pengantar Sistem Informasi',
                'nilai_huruf' => 'A',
                'nilai_angka' => 4.0,
                'sks' => 3,
                'semester' => 1,
                'tahun_ajaran' => '2024/2025',
            ],
            // Semester 2 - NIM 102022400136
            [
                'nim' => '102022400136',
                'kode_matkul' => 'SI201',
                'nama_matkul' => 'Struktur Data',
                'nilai_huruf' => 'B',
                'nilai_angka' => 3.0,
                'sks' => 3,
                'semester' => 2,
                'tahun_ajaran' => '2024/2025',
            ],
            [
                'nim' => '102022400136',
                'kode_matkul' => 'SI202',
                'nama_matkul' => 'Basis Data',
                'nilai_huruf' => 'AB',
                'nilai_angka' => 3.5,
                'sks' => 4,
                'semester' => 2,
                'tahun_ajaran' => '2024/2025',
            ],
            // Semester 1 - NIM 102022580023 (Andi Muh. Arif Darma Saputra)
            [
                'nim' => '102022580023',
                'kode_matkul' => 'SI101',
                'nama_matkul' => 'Algoritma dan Pemrograman',
                'nilai_huruf' => 'AB',
                'nilai_angka' => 3.5,
                'sks' => 3,
                'semester' => 1,
                'tahun_ajaran' => '2024/2025',
            ],
            [
                'nim' => '102022580023',
                'kode_matkul' => 'SI102',
                'nama_matkul' => 'Matematika Diskrit',
                'nilai_huruf' => 'B',
                'nilai_angka' => 3.0,
                'sks' => 3,
                'semester' => 1,
                'tahun_ajaran' => '2024/2025',
            ],
            [
                'nim' => '102022580023',
                'kode_matkul' => 'SI103',
                'nama_matkul' => 'Pengantar Sistem Informasi',
                'nilai_huruf' => 'A',
                'nilai_angka' => 4.0,
                'sks' => 3,
                'semester' => 1,
                'tahun_ajaran' => '2024/2025',
            ],
        ];

        foreach ($nilais as $nilai) {
            Nilai::create($nilai);
        }
    }
}
