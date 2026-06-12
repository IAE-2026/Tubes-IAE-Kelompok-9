# Log Prompting AI - Service C: Prasyarat dan Kurikulum

**Identitas Mahasiswa:**
- **Nama:** Andi Muh. Arif Darma Saputra M  
- **NIM:** 102022580023  
- **Mata Kuliah:** BBK2HAB3 - Integrasi Aplikasi Enterprise
- **Layanan:** Service C - Validasi Prasyarat dan Kurikulum
- **Tool AI:** Antigravity (Gemini 2.0 Flash), Cursor AI (Claude)
- **Tanggal Buat:** 15 Mei 2026
- **Update Terakhir:** 12 Juni 2026

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
- **Integrasi Cloud:** IAE SSO (`iae-sso.virtualfri.id`), SOAP Audit, RabbitMQ via REST proxy
- **AI Assistant:** Antigravity (Gemini 2.0 Flash), Cursor AI (Claude)

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

Timeline Tugas 3 — 10 Juni 2026

11:00 WIB — Plan Tugas 3, spek TIM-09, event nilai.recorded, auto-sync token.  
11:16 WIB — Error duplicate SI101, solusi migrate:fresh --seed.  
11:26 WIB — E2E test POST nilai SI401, receipt IAE-LOG-2026-240F6F50, board OK.  
11:30 WIB — Implement iae:sync-token, VerifyJwtSso, SoapAuditClient, CentralMessagePublisher.  
11:38 WIB — NIM 2099000066 SI301 PBO, receipt EB8E14F7.  
11:45 WIB — Fix payload message wrapper dan routing_key untuk badge board.  
12:08 WIB — NIM 2099000044 SI201 Struktur Data, receipt B9607615.  
12:15 WIB — Review kesesuaian rubrik Tugas 3.  
12:45 WIB — NIM 2099000015 SI302 Jaringan Komputer, receipt 828A266C.  
13:00 WIB — Penjelasan API Key M2M vs JWT vs X-IAE-KEY.  
13:38 WIB — KEY-MHS-117 awalnya HTTP 401, lalu HTTP 200 M2M aktif.  
13:45 WIB — Konfirmasi POST /nilai transaksi kritis.  
13:57 WIB — Publish dari TEAM-09, SI501 nilai akhir, receipt 05048A23.  
14:22 WIB — Konfirmasi NilaiRecorded TeamID TIM-09.  
14:30 WIB — Troubleshoot X-IAE-KEY vs bearerAuth di Swagger.  
14:48 WIB — SI102 AB/3.5 NIM 10202250023, receipt 54BADCCE.  
15:30 WIB — Keputusan pakai nilai.recorded.  
16:00 WIB — Update log prompting session 10 Juni 2026.  
20:00 WIB — Tanya kenapa pakai 2 token, X-IAE-KEY vs KEY-MHS-117, cloud pure.  
08:00 WIB 11 Jun — Rapikan log Modul 4, gitignore .agents .cursor.  
09:00 WIB 11 Jun — Rapikan log: hasil nyata saja, bagian Mei tidak diubah, tanpa asterisk dan pipe di Tugas 3.  
10:00 WIB 11 Jun — Update log prompting sampai pagi 11 Juni.  
06:58 WIB 12 Jun — Cek ulang Sail docker jalan, final update log prompting sampai hari ini.

---

## Modul 4 — Log Prompting Tugas 3 (10 Juni 2026)

Catatan di bawah ini prompt asli saya ke Cursor AI saat ngerjain Tugas 3. Hasilnya yang saya tulis sendiri setelah dicoba, bukan reka-reka.

Prompt:
> Buatkan plan Tugas 3 berdasarkan PDF dosen. Akun warga01@ktp.iae.id, API-KEY KEY-MHS-117, TIM-09. Service mana yang cocok di RabbitMQ? Token auto-sync ke .env tanpa manual hit /api/v1/auth/token.

Hasil: AI baca spek Cloud Pusat, pilih POST /api/v1/nilai sebagai transaksi kritis, event nilai.recorded, plan token otomatis + SSO + SOAP + RabbitMQ.

Prompt:
> Implement the plan as specified. Jangan edit plan file. Complete all todos.

Hasil: Kode jadi iae:sync-token, VerifyJwtSso, SoapAuditClient TIM-09 NilaiRecorded, CentralMessagePublisher nilai.recorded, orkestrasi di NilaiController@store. Test IaeIntegrationTest.php jalan.

Prompt:
> Error migrate --seed: Duplicate entry 'SI101' for key kurikulums_kode_matkul_unique

Hasil: Data lama masih ada di DB. Saya jalankan ./vendor/bin/sail artisan migrate:fresh --seed dan berhasil.

Prompt:
> Masih belum konek dan belum muncul di papan RabbitMQ. Kenapa tampilan beda dengan tim badge mahasiswa.created vs kosong?

Hasil: Saya ternyata cuma sync token, belum POST /nilai. Setelah POST nilai, payload publish masih salah karena harus ada field message dan routing_key. Setelah difix di CentralMessagePublisher, board muncul badge nilai.recorded.

Prompt:
> Tugas 3 saya sudah sesuai belum? SOAP dan RabbitMQ sudah benar?

Hasil: SSO, SOAP ReceiptNumber, dan RabbitMQ nilai.recorded sudah jalan. API Key KEY-MHS-117 sempat 401 tapi fallback warga01 aktif sampai key M2M hidup.

Prompt:
> Fungsi API Key KEY-MHS-117 apa? Cek status 401 atau 200. Token di .env sama JWT Swagger beda?

Hasil: Saya cek sendiri di Postman. Awalnya KEY-MHS-117 balas 401. Nanti balas 200 dengan token_type m2m dan team TEAM-09. AI jelasin X-IAE-KEY itu NIM saya, JWT warga01 buat POST /nilai, IAE_SSO_TOKEN di env buat SOAP dan RabbitMQ.

Prompt:
> Swagger error: Unauthorized. Header X-IAE-KEY tidak valid atau tidak ditemukan.

Hasil: Saya salah isi KEY-MHS-117 di X-IAE-KEY. Harus NIM 102022580023. Bearer JWT terpisah, dari login warga01 ke Cloud Pusat.

Prompt:
> Apakah sample SOAP dosen TEAM-01 ShipmentCreated sudah terimplementasi? ActivityName Service C apa?

Hasil: Sample PDF itu tim lain. Punya saya TeamID TIM-09, ActivityName NilaiRecorded.

Prompt:
> Lebih baik nilai.recorded atau nilai.created?

Hasil: Saya pilih nilai.recorded karena lebih pas artinya nilai resmi dicatat. Kode dan dokumen saya sesuaikan.

Prompt:
> kenapa pakai 2 token? kenapa gak 1 aja? apa resikonya?

Hasil: JWT dosen buat masuk ke Service C cek role. IAE_SSO_TOKEN buat keluar ke Cloud Pusat SOAP dan RabbitMQ. Kalau dipaksain satu token, salah satu arah jadi bermasalah.

Prompt:
> AGAR SAYA TOKEN DARI KEY-MHS-117 DI POSTMAN GIMANA? token_type m2m team TEAM-09, ini yang dipakai SSO dan RabbitMQ?

Hasil: POST ke iae-sso.virtualfri.id/api/v1/auth/token body api_key KEY-MHS-117. Token itu buat SOAP dan RabbitMQ, bukan Bearer di POST /nilai. SSO dosen tetap JWT dari login warga01.

Prompt:
> JIKA X-IAE-KEY DAN NIM NYA DI GANTI KE CLOUD PURE APAKAH BISA? DAN APAKAH ADA RESIKO?

Hasil: Bisa diubah di kode tapi saya tidak lakukan. X-IAE-KEY identitas service lokal, KEY-MHS-117 identitas tim ke Cloud Pusat. Kalau dicampur request bisa 401 dan alur tugas jadi kacau.

Prompt:
> cara cek di postman gimana ya? lupa jalanin dockernya dengan sail

Hasil: sail up -d, php artisan iae:sync-token dari Mac host, login warga01 dapat JWT, POST /nilai dengan X-IAE-KEY 102022580023 plus Bearer JWT.

Prompt:
> git ignore yang agent dan .cursor bisa gak

Hasil: Ditambah ke .gitignore: .agents, .cursor, skills-lock.json.

---

## Lanjutan sesi — 12 Juni 2026

Prompt:
> saya mau jalanin dockernya dengan sail tapi saya lupa

Hasil: AI ingatkan urutan ./vendor/bin/sail up -d, migrate seed kalau perlu, php artisan iae:sync-token dari Mac host, baru testing Postman. Saya jalankan sail up -d dan container mysql plus laravel.test status running.

Prompt:
> update log prompt sampai hari ini

Hasil: Log diperbarui sampai 12 Juni 2026. Prompt implementasi dan testing Tugas 3 sudah tercatat lengkap.

---

## Hasil Testing Nyata — 10 Juni 2026

Data ini dari POST /nilai yang benar-benar saya jalankan:

11:26 WIB — NIM 102022580023, SI401 Integrasi Aplikasi Enterprise, receipt IAE-LOG-2026-240F6F50, board OK.  
11:38 WIB — NIM 2099000066, SI301 Pemrograman Berorientasi Objek, receipt IAE-LOG-2026-EB8E14F7, board OK.  
12:08 WIB — NIM 2099000044, SI201 Struktur Data, receipt IAE-LOG-2026-B9607615, board OK.  
12:45 WIB — NIM 2099000015, SI302 Jaringan Komputer, receipt IAE-LOG-2026-828A266C, board OK.  
13:57 WIB — NIM 2099000099, SI501 Keamanan Sistem Informasi, receipt IAE-LOG-2026-05048A23, board OK TEAM-09.  
14:48 WIB — NIM 10202250023, SI102 Matematika Diskrit AB/3.5, receipt IAE-LOG-2026-54BADCCE, board OK.

Semua dapat receipt_number dari SOAP. Event nilai.recorded muncul di https://iae-sso.virtualfri.id/board.

---

## Refleksi Modul 4

Yang paling saya pelajari dari prompting ke AI: jangan langsung percaya, selalu cek sendiri di Postman dan board RabbitMQ. Awalnya saya kira sync token saja sudah cukup untuk board, ternyata harus POST /nilai dulu. Saya juga sempat salah pakai KEY-MHS-117 di X-IAE-KEY sampai dapat 401.

Luaran Modul 4: log_prompting.md ini sebagai catatan akuntabilitas penggunaan AI saat implementasi dan testing.

---

Log Prompting Service C TIM-09 NIM 102022580023 — update 12 Juni 2026
