<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswas = [
            [
                'nim' => '102022400136',
                'nama' => 'Arneta Alifiana',
                'email' => 'arneta@student.telkomuniversity.ac.id',
                'prodi' => 'S1 Sistem Informasi',
                'angkatan' => 2024,
                'status' => 'aktif',
            ],
            [
                'nim' => '102022580023',
                'nama' => 'Andi Muh. Arif Darma Saputra M',
                'email' => 'andi@student.telkomuniversity.ac.id',
                'prodi' => 'S1 Sistem Informasi',
                'angkatan' => 2022,
                'status' => 'aktif',
            ],
            [
                'nim' => '2099000015',
                'nama' => 'Mahasiswa Anonim 0015',
                'email' => '2099000015@student.telkomuniversity.ac.id',
                'prodi' => 'S1 Sistem Informasi',
                'angkatan' => 2024,
                'status' => 'aktif',
            ],
        ];

        foreach ($mahasiswas as $mahasiswa) {
            Mahasiswa::updateOrCreate(
                ['nim' => $mahasiswa['nim']],
                $mahasiswa
            );
        }
    }
}
