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

Framework Laravel 12, database MySQL 8.4 lewat Sail, dokumentasi REST pakai L5-Swagger, GraphQL pakai Lighthouse plus GraphiQL. Bahasa PHP 8.x. Integrasi cloud ke IAE SSO iae-sso.virtualfri.id, SOAP audit, RabbitMQ lewat REST proxy. AI yang dipakai Antigravity Gemini 2.0 Flash sama Cursor AI Claude.

---

## Log History Timeline

14 Mei 23:31 WIB — bersihin folder proyek buat fresh install Laravel 12.  
14 Mei 23:35 WIB — install Laravel 12 via composer create-project.  
14 Mei 23:36 WIB — pasang L5-Swagger, Lighthouse GraphQL, GraphiQL.  
14 Mei 23:37 WIB — balikin controller model middleware seeder dari backup.  
14 Mei 23:38 WIB — bikin migrasi tabel kurikulums dan nilais terus jalanin.  
14 Mei 23:39 WIB — seed data dummy 10 kurikulum 8 nilai.  
14 Mei 23:43 WIB — sempat pindah ke MySQL XAMPP karena minta user.  
14 Mei 23:46 WIB — balik lagi ke SQLite sesuai spek tugas.  
15 Mei 00:01 WIB — cek Swagger UI di /api/documentation udah tampil.  
15 Mei 00:08 WIB — tes header X-IAE-KEY buat akses API.  
15 Mei 00:13 WIB — matiin server lokal mau pindah ke Docker.  
15 Mei 00:16 WIB — setup Laravel Sail sama docker-compose.yml.  
15 Mei 00:46 WIB — fix error file sharing di Mac plus bersihin file duplikat.  
15 Mei 00:55 WIB — pindah lagi ke MySQL 8.4 di Docker buat kebutuhan tim.  
15 Mei 01:32 WIB — rebuild container di folder Users biar mount aman.  
15 Mei 01:46 WIB — jalanin db:seed isi data NIM mahasiswa ke MySQL.  
15 Mei 01:50 WIB — update log prompting akhir session Mei.

Timeline Tugas 3, 10 Juni 2026

11:00 WIB — minta plan Tugas 3, spek TIM-09, event nilai.recorded, token auto sync.  
11:16 WIB — kena error duplicate SI101, fix pake migrate:fresh --seed.  
11:26 WIB — tes POST nilai SI401, receipt IAE-LOG-2026-240F6F50, board OK.  
11:30 WIB — implement iae:sync-token, VerifyJwtSso, SoapAuditClient, CentralMessagePublisher.  
11:38 WIB — tes NIM 2099000066 SI301 PBO, receipt EB8E14F7.  
11:45 WIB — fix payload publish harus ada message wrapper sama routing_key biar badge muncul.  
12:08 WIB — tes NIM 2099000044 SI201 Struktur Data, receipt B9607615.  
12:15 WIB — cek lagi udah sesuai rubrik Tugas 3 belum.  
12:45 WIB — tes NIM 2099000015 SI302 Jaringan Komputer, receipt 828A266C.  
13:00 WIB — bingung bedain API Key M2M, JWT, sama X-IAE-KEY.  
13:38 WIB — KEY-MHS-117 awalnya 401, abis itu 200 M2M aktif.  
13:45 WIB — konfirmasi POST /nilai memang transaksi kritisnya.  
13:57 WIB — publish dari TEAM-09 SI501 nilai akhir, receipt 05048A23.  
14:22 WIB — konfirmasi SOAP pake TeamID TIM-09 ActivityName NilaiRecorded.  
14:30 WIB — Swagger error, ternyata salah isi X-IAE-KEY vs bearer JWT.  
14:48 WIB — tes SI102 AB/3.5 NIM 10202250023, receipt 54BADCCE.  
15:30 WIB — putusin pake event nilai.recorded bukan nilai.created.  
16:00 WIB — update log prompting session 10 Juni.  
20:00 WIB — nanya kenapa harus 2 token, beda X-IAE-KEY sama KEY-MHS-117.    
11 Juni 10:00 WIB — update log sampai pagi 11 Juni.  
12 Juni 06:58 WIB — cek Sail docker masih jalan, update log sampai hari ini.

---

## Modul 4 Log Prompting Tugas 3

Ini catatan prompt saya ke Cursor pas ngerjain Tugas 3. Hasil di bawah dari yang saya coba sendiri di Postman sama board RabbitMQ, bukan asal tulis.

Saya nanya: buatkan plan Tugas 3 dari PDF dosen. Akun warga01@ktp.iae.id, API-KEY KEY-MHS-117, TIM-09. Service mana yang cocok di RabbitMQ? Token auto sync ke .env jangan manual hit /api/v1/auth/token.

Hasilnya AI baca spek Cloud Pusat, pilih POST /api/v1/nilai sebagai transaksi kritis, event nilai.recorded, plus plan SSO SOAP RabbitMQ sama token otomatis.

Saya nanya: lanjutin implement plan tadi, jangan edit file plannya, selesaiin semua todos.

Hasilnya jadi kode iae:sync-token, VerifyJwtSso, SoapAuditClient TIM-09 NilaiRecorded, CentralMessagePublisher nilai.recorded, semua diorkestrasi di NilaiController store. Test IaeIntegrationTest.php jalan.

Saya nanya: error migrate --seed Duplicate entry SI101 for key kurikulums_kode_matkul_unique

Hasilnya data lama masih nempel di DB. Saya jalanin ./vendor/bin/sail artisan migrate:fresh --seed baru beres.

Saya nanya: masih belum konek dan belum muncul di papan RabbitMQ. Kenapa tampilan beda sama tim lain yang udah ada badge mahasiswa.created tapi punyaku kosong?

Hasilnya ternyata saya cuma sync token doang belum POST nilai. Abis POST nilai payload publish masih salah karena harus ada field message sama routing_key. Setelah difix di CentralMessagePublisher board muncul badge nilai.recorded.

Saya nanya: tugas 3 saya udah sesuai belum? SOAP sama RabbitMQ udah bener?

Hasilnya SSO SOAP ReceiptNumber sama RabbitMQ nilai.recorded udah jalan. API Key KEY-MHS-117 sempat 401 tapi fallback warga01 aktif sampai key M2M hidup.

Saya nanya: fungsi API Key KEY-MHS-117 apa sih? cek status 401 atau 200. Token di .env sama JWT di Swagger beda kan?

Hasilnya saya cek sendiri di Postman. Awalnya KEY-MHS-117 balas 401. Nanti balas 200 token_type m2m team TEAM-09. Baru paham X-IAE-KEY itu NIM saya, JWT warga01 buat POST /nilai, IAE_SSO_TOKEN di env buat SOAP sama RabbitMQ.

Saya nanya: swagger error Unauthorized, header X-IAE-KEY tidak valid atau tidak ditemukan

Hasilnya saya salah isi KEY-MHS-117 di X-IAE-KEY. Harusnya NIM 102022580023. Bearer JWT terpisah dari login warga01 ke Cloud Pusat.

Saya nanya: sample SOAP dosen TEAM-01 ShipmentCreated udah keimplement belum? ActivityName Service C apa?

Hasilnya sample PDF itu tim lain. Punyaku TeamID TIM-09 ActivityName NilaiRecorded.

Saya nanya: lebih baik nilai.recorded atau nilai.created?

Hasilnya saya pilih nilai.recorded karena lebih pas artinya nilai resmi udah dicatat. Kode saya sesuaikan.

Saya nanya: kenapa pakai 2 token? kenapa gak 1 aja? apa resikonya?

Hasilnya JWT dosen buat masuk ke Service C cek role. IAE_SSO_TOKEN buat keluar ke Cloud Pusat SOAP sama RabbitMQ. Kalau dipaksain satu token salah satu arah pasti bermasalah.

Saya nanya: AGAR SAYA TOKEN DARI KEY-MHS-117 DI POSTMAN GIMANA? token_type m2m team TEAM-09, ini yang dipakai SSO sama RabbitMQ?

Hasilnya POST ke iae-sso.virtualfri.id/api/v1/auth/token body api_key KEY-MHS-117. Token itu buat SOAP sama RabbitMQ, bukan Bearer di POST /nilai. SSO dosen tetap JWT dari login warga01.

Saya nanya: JIKA X-IAE-KEY DAN NIM NYA DI GANTI KE CLOUD PURE APAKAH BISA? DAN APAKAH ADA RESIKO?

Hasilnya bisa diubah di kode tapi saya gak lakukan. X-IAE-KEY identitas service lokal, KEY-MHS-117 identitas tim ke Cloud Pusat. Kalau dicampur request bisa 401 alur tugas jadi kacau.

Saya nanya: cara cek di postman gimana ya? lupa jalanin dockernya pake sail

Hasilnya sail up -d, php artisan iae:sync-token dari Mac host, login warga01 dapat JWT, POST /nilai pake X-IAE-KEY 102022580023 plus Bearer JWT.

Saya nanya: git ignore yang agent sama .cursor bisa gak

Hasilnya ditambah ke .gitignore .agents .cursor skills-lock.json.

---

## Lanjutan sesi 12 Juni 2026

Saya nanya: saya mau jalanin dockernya pake sail tapi lupa caranya

Hasilnya diingatkin urutan ./vendor/bin/sail up -d, migrate seed kalau perlu, php artisan iae:sync-token dari Mac host, baru testing Postman. Saya jalanin sail up -d container mysql sama laravel.test running.

Saya nanya: update log prompt sampai hari ini

Hasilnya log diperbarui sampai 12 Juni 2026. Prompt implementasi sama testing Tugas 3 udah tercatat.

---

## Hasil Testing Nyata 10 Juni 2026

Ini dari POST /nilai yang beneran saya jalanin:

11:26 WIB NIM 102022580023 SI401 Integrasi Aplikasi Enterprise receipt IAE-LOG-2026-240F6F50 board OK  
11:38 WIB NIM 2099000066 SI301 Pemrograman Berorientasi Objek receipt IAE-LOG-2026-EB8E14F7 board OK  
12:08 WIB NIM 2099000044 SI201 Struktur Data receipt IAE-LOG-2026-B9607615 board OK  
12:45 WIB NIM 2099000015 SI302 Jaringan Komputer receipt IAE-LOG-2026-828A266C board OK  
13:57 WIB NIM 2099000099 SI501 Keamanan Sistem Informasi receipt IAE-LOG-2026-05048A23 board OK TEAM-09  
14:48 WIB NIM 10202250023 SI102 Matematika Diskrit AB/3.5 receipt IAE-LOG-2026-54BADCCE board OK

Semua dapet receipt_number dari SOAP. Event nilai.recorded muncul di iae-sso.virtualfri.id/board.

---

## Refleksi Modul 4

Yang paling kepake dari prompting ke AI itu jangan langsung percaya, selalu cek sendiri di Postman sama board RabbitMQ. Awalnya saya kira sync token aja udah cukup biar muncul di board, ternyata harus POST /nilai dulu. Saya juga sempat salah isi KEY-MHS-117 di X-IAE-KEY sampe dapet 401.

Log ini buat akuntabilitas Modul 4, catatan pake AI pas implementasi sama testing Service C TIM-09.

Log Prompting Service C TIM-09 NIM 102022580023 update 12 Juni 2026
