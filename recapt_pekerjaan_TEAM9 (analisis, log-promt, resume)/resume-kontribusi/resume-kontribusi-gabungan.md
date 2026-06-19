# Resume Kontribusi Gabungan (Kelompok 9 / TIM-09)

**Mata Kuliah:** BBK2HAB3 (Integrasi Aplikasi Enterprise)  
**Repository:** Tubes-IAE-Kelompok-9  
**Gateway lokal:** http://127.0.0.1:8080

---

## Gambaran sistem kita

Kelompok 9 bikin tiga service Laravel terpisah buat domain **Education System**, yaitu urusan akademik kampus. Masing-masing service punya database sendiri, nggak boleh saling colok database orang lain. Komunikasi antar service cuma lewat HTTP REST plus header API key.

Semuanya jalan di Docker, dan dari luar cuma kelihatan satu pintu masuk lewat API Gateway Nginx di port 8080.

**Service A (Data Mahasiswa)**  
Anggota: Arneta  
Tugas: profil mahasiswa, daftar maba, login SSO, gabung data matkul  
API Key: KEY-MHS-233 (`X-API-KEY`)

**Service B (KRS)**  
Anggota: Jafar  
Tugas: isi KRS, approve dosen  
API Key: KEY-MHS-109 (`X-IAE-KEY`)

**Service C (Nilai & Kurikulum)**  
Anggota: Andi  
Tugas: data kurikulum, catat nilai, hitung IPS  
API Key: KEY-MHS-117 (`X-IAE-KEY`)

---

## Kontribusi masing-masing

### Arneta, Service A

Arneta yang ngurus data mahasiswa: NIM, nama, email, prodi, status aktif/cuti/lulus, dan sebagainya. Service-nya bisa daftarin mahasiswa baru, cari data by NIM, login SSO ke cloud dosen, sama ada endpoint buat ngambil KRS + nilai sekaligus dari Service B dan C (pakai Http::pool biar lebih cepat).

Pas Tugas 3, transaksi pentingnya POST `/api/v1/mahasiswa`. Setiap maba baru kirim audit SOAP plus event `mahasiswa.created` ke RabbitMQ.

Detail: [`resume-kontribusi-masing-masing/arneta/RESUME_ARNETA.md`](resume-kontribusi-masing-masing/arneta/RESUME_ARNETA.md)

### Jafar, Service B

Jafar ngurus KRS. Mahasiswa isi mata kuliah yang mau diambil, terus dosen yang approve. Sebelum KRS disimpan, Service B ngecek dulu ke Service A (NIM valid dan aktif?) dan ke Service C (IPS cukup? matkul ada di kurikulum?).

Pas Tugas 3, transaksi pentingnya POST `/api/v1/krs`. Dapat ReceiptNumber dari SOAP plus event `krs.created`. Kalau dosen approve, ada juga event `krs.approved`.

Detail: [`resume-kontribusi-masing-masing/jafar/RESUME_KONTRIBUSI.md`](resume-kontribusi-masing-masing/jafar/RESUME_KONTRIBUSI.md)

### Andi, Service C (Nilai & Kurikulum)

Andi ngurus kurikulum sama nilai. Bisa liat daftar matkul, liat nilai per NIM (plus IPS), dan dosen bisa input nilai baru. Ada REST, GraphQL, sama Swagger. Pas input nilai, Service C cek dulu ke Service A apakah mahasiswanya beneran ada dan aktif. POST nilai wajib dua header: `X-IAE-KEY: KEY-MHS-117` dan Bearer JWT dosen (warga01). Token M2M untuk SOAP/RabbitMQ di-sync lewat `php artisan iae:sync-token`, terpisah dari JWT Postman.

Pas Tugas Besar, Andi juga urus harmonisasi gateway, dokumentasi routing-map, folder recapt penilaian tim, dan endpoint agregasi matkul di Service A supaya nilai bisa dilihat end-to-end lewat `GET /mahasiswa/{nim}/matkul`.

Pas Tugas 3, transaksi pentingnya POST `/api/v1/nilai` dengan audit SOAP NilaiRecorded plus event `nilai.recorded`. Resume lengkap Andi ditulis sendiri, bukan hasil AI generate penuh.

Detail: [`resume-kontribusi-masing-masing/andi/RESUME_ANDI.md`](resume-kontribusi-masing-masing/andi/RESUME_ANDI.md)

---

## Setelah digabung jadi satu repo

Waktu ketiga service kita merge ke `Tubes-IAE-Kelompok-9`, yang kita lakukan antara lain:

- Satu `docker-compose.yml` jalanin ketiga service + gateway + MySQL masing-masing
- `MahasiswaSeeder` di Service A buat data mahasiswa yang selaras antar service
- Service A terima header `X-API-KEY` dan `X-IAE-KEY` biar temen-temen bisa akses
- Service B validasi ke A & C lewat URL internal Docker, bukan localhost masing-masing
- Token M2M ke cloud pusat sekarang butuh `api_key` + `nim` owner per service

---

## Alur bisnis dari awal sampe akhir

1. Admin/dosen daftarin **mahasiswa baru** di Service A, keluar event `mahasiswa.created`
2. Mahasiswa **isi KRS** di Service B, dicek ke A & C dulu, keluar event `krs.created`
3. Dosen **approve KRS**, keluar event `krs.approved`
4. Dosen **input nilai** di Service C, keluar event `nilai.recorded`
5. Kalau mau liat ringkasan, GET `/api/v1/mahasiswa/{nim}/matkul` di Service A buat ngambil KRS + nilai sekaligus

Semua transaksi penting Tugas 3 kita udah coba dan muncul di board cloud pusat dengan receipt SOAP-nya.

*File gabungan ini AI bantu susun paragrafnya, tapi isi kontribusi per anggota kita ambil dari resume asli masing-masing.*
