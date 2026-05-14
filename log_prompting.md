# Log Prompting AI - Service C: Prasyarat dan Kurikulum

**Identitas Mahasiswa:**
- **Nama:** Andi Muh. Arif Darma Saputra M  
- **NIM:** 102022580023  
- **Mata Kuliah:** BBK2HAB3 - Integrasi Aplikasi Enterprise
- **Layanan:** Service C - Validasi Prasyarat dan Kurikulum
- **Tool AI:** Antigravity (Gemini 2.0 Flash)
- **Tanggal Buat:** 15 Mei 2026  

---

## Prompt 1: Perencanaan Proyek

**Prompt:**
> Validasi prasyarat dan kurikulum Service C dengan endpoint:
> - GET /api/v1/nilai/{nim} → Lihat nilai & IPS semester lalu
> - GET /api/v1/kurikulum/{kode} → Lihat detail kurikulum
> - POST /api/v1/nilai → Catat nilai mahasiswa
> Gunakan Laravel 12, Swagger, GraphQL, dan middleware X-IAE-KEY. Buat plan dalam bahasa Indonesia.

**Hasil:**
AI membuat rencana implementasi (implementation plan) yang mencakup:
- Inisialisasi proyek Laravel 12
- Instalasi dependencies: L5-Swagger, Lighthouse (GraphQL), GraphiQL
- Pembuatan migration & model untuk tabel `kurikulums` dan `nilais`
- Middleware `CheckIaeKey` untuk validasi header `X-IAE-KEY`
- Controller dengan Swagger annotations (PHP 8 Attributes)
- Integrasi Service A untuk validasi status mahasiswa saat POST /nilai
- GraphQL schema dengan query kurikulum dan nilai

---

## Prompt 2: Eksekusi Implementasi

**Prompt:**
> Approve plan, lanjutkan eksekusi.

**Hasil:**
AI mengeksekusi seluruh rencana secara bertahap:

### a. Inisialisasi Proyek
- `composer create-project laravel/laravel .`
- `composer require darkaonline/l5-swagger`
- `composer require nuwave/lighthouse`
- `composer require mll-lab/laravel-graphiql`
- `php artisan install:api`

### b. Database
- Migration `kurikulums`: kode_matkul, nama_matkul, sks, semester, prodi, prasyarat, deskripsi
- Migration `nilais`: nim, kode_matkul, nama_matkul, nilai_huruf, nilai_angka, sks, semester, tahun_ajaran
- Seeder dengan 10 data kurikulum dan 8 data nilai contoh

### c. Middleware
- `app/Http/Middleware/CheckIaeKey.php` — validasi header `X-IAE-KEY: 102022580023`
- Didaftarkan sebagai alias `iae.key` di `bootstrap/app.php`

### d. Controllers
- `KurikulumController.php` — 2 endpoint (Collection + Resource)
- `NilaiController.php` — 3 endpoint (Collection + Resource + Action)
- Semua menggunakan PHP 8 Attribute Swagger annotations
- POST /nilai memanggil Service A untuk validasi mahasiswa aktif

### e. Response Helper
- `app/Helpers/ApiResponse.php` — wrapper JSON konsisten (status, message, data, meta)

### f. GraphQL
- `graphql/schema.graphql` dengan query: kurikulums, kurikulum, nilais, nilaiByNim
- Tipe Kurikulum dan Nilai dengan relasi hasMany/belongsTo

### g. Konfigurasi
- `.env` ditambahkan `L5_SWAGGER_GENERATE_ALWAYS=true` dan `SERVICE_A_URL`
- Swagger berhasil di-generate di `/api/documentation`
- GraphQL Playground tersedia di `/graphiql`

---

## Prompt 3: Pengujian Endpoint

**Hasil pengujian:**

| # | Endpoint | Method | Hasil | Status |
|---|----------|--------|-------|--------|
| 1 | `/api/v1/kurikulum` | GET | Menampilkan 10 data kurikulum | ✅ |
| 2 | `/api/v1/kurikulum/SI201` | GET | Menampilkan detail Struktur Data | ✅ |
| 3 | `/api/v1/nilai` | GET | Menampilkan semua data nilai | ✅ |
| 4 | `/api/v1/nilai/102022400136` | GET | Menampilkan nilai + IPS 3.29 | ✅ |
| 5 | `/api/v1/nilai` | POST | Berhasil mencatat nilai baru | ✅ |
| 6 | `/api/v1/kurikulum` (tanpa key) | GET | 401 Unauthorized | ✅ |
| 7 | `/api/documentation` | Browser | Swagger UI tampil | ✅ |
| 8 | `/graphiql` | Browser | GraphQL Playground tampil | ✅ |
| 9 | `Docker Container` | Terminal | MySQL & Laravel Sail Running | ✅ |

---

## Prompt 4: Dockerization & MySQL Integration

**Prompt:**
> Saya mau app ini jalan di docker. Pakai MySQL agar selaras dengan teman-teman agar mudah integrasinya.

**Hasil:**
AI melakukan setup containerization:
- Pembuatan `docker-compose.yml` menggunakan Laravel Sail.
- Konfigurasi `.env` untuk koneksi ke host `mysql` di dalam network Docker.
- **Troubleshooting Mac:** Mengatasi error `mounts denied` dengan memindahkan proyek ke direktori `/Users/andisaputra/Documents/` (standard shared path).
- **Data Seeding:** Menjalankan `php artisan db:seed` di dalam container untuk mengisi ulang data mahasiswa dan nilai ke MySQL.
- Verifikasi pencarian NIM `102022580023` berhasil di Swagger setelah database di-populate.

---

## Teknologi yang Digunakan
- **Framework:** Laravel 12
- **Database:** MySQL 8.4 (Dockerized via Sail)
- **Dokumentasi REST:** L5-Swagger (darkaonline/l5-swagger v11)
- **GraphQL:** Lighthouse (nuwave/lighthouse v6.66) + GraphiQL
- **Docker:** Laravel Sail
- **Bahasa:** PHP 8.x
- **AI Assistant:** Antigravity (Gemini 2.0 Flash)

---

## Log History (Timeline)

| Waktu (WIB) | Aktivitas | Detail |
|-------------|-----------|--------|
| 23:31 (14 Mei) | **Initial Cleanup** | Membersihkan direktori proyek untuk persiapan fresh install Laravel 12. |
| 23:35 (14 Mei) | **Install Laravel 12** | Instalasi murni Laravel 12.0 via Composer create-project. |
| 23:36 (14 Mei) | **Dependency Setup** | Instalasi L5-Swagger, Lighthouse GraphQL, dan GraphiQL. |
| 23:37 (14 Mei) | **Code Restoration** | Mengembalikan logic Controller, Model, Middleware, dan Seeder dari backup. |
| 23:38 (14 Mei) | **Migration Setup** | Membuat dan menjalankan migrasi tabel `kurikulums` dan `nilais`. |
| 23:39 (14 Mei) | **DB Seeding** | Memasukkan data dummy (10 kurikulum, 8 nilai) ke database. |
| 23:43 (14 Mei) | **MySQL Switch** | Sempat migrasi ke MySQL XAMPP atas permintaan user. |
| 23:46 (14 Mei) | **SQLite Revert** | Mengembalikan database ke SQLite sesuai spesifikasi tugas. |
| 00:01 (15 Mei) | **Swagger Verify** | Verifikasi dokumentasi API via Swagger UI (`/api/documentation`). |
| 00:08 (15 Mei) | **Auth Verify** | Pengetesan header `X-IAE-KEY` untuk akses API yang aman. |
| 00:13 (15 Mei) | **Server Stop** | Mematikan server lokal untuk persiapan pindah ke Docker. |
| 00:16 (15 Mei) | **Dockerization** | Inisialisasi Laravel Sail & `docker-compose.yml`. |
| 00:46 (15 Mei) | **Mount Fix** | Mengatasi error file sharing Mac & pembersihan file duplikat. |
| 00:55 (15 Mei) | **MySQL Switch** | Re-integrasi MySQL 8.4 di Docker untuk kebutuhan tim. |
| 01:32 (15 Mei) | **Re-Build** | Build ulang container di direktori `/Users` yang aman. |
| 01:46 (15 Mei) | **Data Population** | Menjalankan `db:seed` untuk mengisi data NIM mahasiswa ke MySQL. |
| 01:50 (15 Mei) | **Final Log** | Update log prompting akhir. |
