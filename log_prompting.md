# Log Prompting AI - Service C: Prasyarat dan Kurikulum

**Identitas Mahasiswa:**
- **Nama:** Andi Muh. Arif Darma Saputra M  
- **NIM:** 102022580023  
- **Mata Kuliah:** BBK2HAB3 - Integrasi Aplikasi Enterprise
- **Layanan:** Service C - Validasi Prasyarat dan Kurikulum
- **Tool AI:** Antigravity (Gemini 2.0 Flash), Cursor AI (Claude)
- **Tanggal Buat:** 15 Mei 2026
- **Update Terakhir:** 10 Juni 2026

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
| 11:00 (10 Jun) | **Tugas 3 Planning** | Plan Tugas 3, spek TIM-09, event `nilai.recorded`, auto-sync token, sequence diagram. |
| 11:16 (10 Jun) | **Docker Seed Fix** | Diagnosa error duplicate SI101, solusi `migrate:fresh --seed`. |
| 11:26 (10 Jun) | **E2E Test #1** | POST nilai SI401, receipt IAE-LOG-2026-240F6F50, board OK. |
| 11:30 (10 Jun) | **Tugas 3 Implementation** | `iae:sync-token`, `VerifyJwtSso`, `SoapAuditClient`, `CentralMessagePublisher`, POST /nilai. |
| 11:38 (10 Jun) | **E2E Test #2** | NIM anonim 2099000066, SI301 PBO, receipt EB8E14F7. |
| 11:45 (10 Jun) | **RabbitMQ Debug** | Fix payload `message` wrapper + `routing_key` untuk badge board. |
| 12:08 (10 Jun) | **E2E Test Anonim** | NIM 2099000044, SI201 Struktur Data, receipt B9607615. |
| 12:15 (10 Jun) | **Compliance Review** | Review kesesuaian rubrik Tugas 3 (~90% sesuai). |
| 12:45 (10 Jun) | **E2E Test #4** | NIM 2099000015, SI302 Jaringan Komputer, receipt 828A266C. |
| 13:00 (10 Jun) | **API Key Explained** | Penjelasan perbedaan API Key M2M vs JWT vs X-IAE-KEY. |
| 13:38 (10 Jun) | **API Key Check** | KEY-MHS-117 awalnya HTTP 401, kemudian HTTP 200 (M2M aktif). |
| 13:45 (10 Jun) | **Transaksi Kritis** | Konfirmasi POST /nilai + Service A sebagai alur bisnis. |
| 13:57 (10 Jun) | **M2M Aktif + Board** | Publish dari **TEAM-09**, SI501 nilai akhir, receipt 05048A23. |
| 14:08 (10 Jun) | **Rewrite Analisis** | `analisis_tugas_3.md` diperbarui tanpa TEAM-01. |
| 14:22 (10 Jun) | **SOAP ActivityName** | Konfirmasi `NilaiRecorded` + TeamID TIM-09. |
| 14:30 (10 Jun) | **Swagger Fix** | Troubleshoot X-IAE-KEY vs bearerAuth di Swagger. |
| 14:48 (10 Jun) | **Float Nilai Test** | SI102 AB/3.5, NIM 10202250023, receipt 54BADCCE. |
| 15:30 (10 Jun) | **Event Naming** | Keputusan pakai `nilai.recorded` (bukan `.created`). |
| 16:00 (10 Jun) | **Log Update** | Update log prompting session 10 Juni 2026. |

---

## Prompt 6: Tugas 3 — Integrasi Cloud Pusat (TIM-09)

**Waktu:** 10 Juni 2026, 11:30 WIB

**Prompt:**
> Implement plan Tugas 3 Service C TIM-09: auto-sync token ke .env, Federated SSO JWT, SOAP audit, publish `nilai.recorded` ke RabbitMQ. Akun warga01@ktp.iae.id, API-KEY KEY-MHS-117, TIM-09.

**Hasil:**
AI mengimplementasikan integrasi 3 lapis pada transaksi kritis `POST /api/v1/nilai`:
- **`php artisan iae:sync-token`** — fetch token M2M otomatis, simpan `IAE_SSO_TOKEN` ke `.env`
- **`VerifyJwtSso` middleware** — verifikasi JWT RS256 via JWKS, mapping role lokal (warga01 → dosen)
- **`SoapAuditClient`** — kirim XML Envelope `TeamID=TIM-09`, simpan `ReceiptNumber`
- **`CentralMessagePublisher`** — publish event `nilai.recorded` via `/api/v1/messages/publish`
- Migration `receipt_number`, `recorded_by` pada tabel `nilais`
- Dokumentasi Swagger Bearer JWT + analisis_tugas_3.md

---

## Prompt 7: Perencanaan Tugas 3 + Spek Cloud Pusat

**Waktu:** 10 Juni 2026, 11:00 WIB

**Prompt:**
> Buatkan plan Tugas 3 berdasarkan PDF dosen. Akun warga01@ktp.iae.id, API-KEY KEY-MHS-117, TIM-09. Service mana yang cocok di RabbitMQ? Token auto-sync ke .env tanpa manual hit /api/v1/auth/token. Update sequence diagram.

**Hasil:**
- AI membaca PDF Tugas 3 & URL Cloud Pusat (`iae-sso.virtualfri.id`)
- Merekomendasikan event **`nilai.recorded`** pada transaksi kritis `POST /api/v1/nilai`
- Menyusun plan 4 fase: token otomatis, SSO JWT, SOAP audit, RabbitMQ publish
- Membuat draft `analisis_tugas_3.md` dengan 2 sequence diagram (bootstrap token + POST /nilai end-to-end)

---

## Prompt 8: Implementasi Penuh Tugas 3

**Waktu:** 10 Juni 2026, 11:30 WIB

**Prompt:**
> Implement the plan as specified. Jangan edit plan file. Complete all todos.

**Hasil:**
AI mengimplementasikan seluruh modul Tugas 3:
- `SyncIaeToken` command + `IaeTokenService` + `EnvWriter`
- `VerifyJwtSso` middleware + migration roles + `RoleSeeder`
- `SoapAuditClient` (XML Envelope TIM-09, ActivityName NilaiRecorded)
- `CentralMessagePublisher` (publish ke `/api/v1/messages/publish`)
- Orkestrasi di `NilaiController@store` (SSO → DB → SOAP → RabbitMQ)
- Dependency `firebase/php-jwt`, test `IaeIntegrationTest.php`

---

## Prompt 9: Debugging RabbitMQ Board

**Waktu:** 10 Juni 2026, 11:45 WIB

**Prompt:**
> Masih belum konek dan belum muncul di papan RabbitMQ. Kenapa tampilan beda dengan tim (badge `mahasiswa.created` vs kosong)?

**Hasil:**
- AI mendiagnosis 2 masalah: (1) event hanya terpicu saat `POST /nilai`, bukan saat sync-token saja; (2) payload publish salah — API dosen wajib field `message`, bukan flat JSON
- Fix: tambah `routing_key` agar badge hijau muncul di board
- Verifikasi end-to-end sukses: receipt_number + event di https://iae-sso.virtualfri.id/board

---

## Prompt 10: Testing Input Nilai (Terminal & Board)

**Waktu:** 10 Juni 2026, 11:30–14:48 WIB

**Prompt:**
> Input nilai baru NIM anonim + matkul real. Testing lihat hasil di terminal/Swagger. Input nilai akhir semester.

**Hasil:**
AI menjalankan beberapa POST /nilai via terminal:

| Waktu (WIB) | NIM | Matkul | Receipt | Board |
|---|---|---|---|---|
| 11:26 | 102022580023 | SI401 Integrasi Aplikasi Enterprise | IAE-LOG-2026-240F6F50 | ✅ |
| 11:38 | 2099000066 | SI301 Pemrograman Berorientasi Objek | IAE-LOG-2026-EB8E14F7 | ✅ |
| 12:08 | 2099000044 | SI201 Struktur Data | IAE-LOG-2026-B9607615 | ✅ |
| 12:45 | 2099000015 | SI302 Jaringan Komputer | IAE-LOG-2026-828A266C | ✅ |
| 13:57 | 2099000099 | SI501 Keamanan Sistem Informasi | IAE-LOG-2026-05048A23 | ✅ TEAM-09 |
| 14:22 | 10202250023 | SI102 Matematika Diskrit (AB/3.5) | IAE-LOG-2026-54BADCCE | ✅ |

---

## Prompt 11: Docker & Database Seeding

**Waktu:** 10 Juni 2026, 11:16 WIB

**Prompt:**
> Error migrate --seed: Duplicate entry 'SI101' for key kurikulums_kode_matkul_unique

**Hasil:**
- AI menjelaskan penyebab: data lama masih ada, seeder INSERT duplikat
- Solusi: `./vendor/bin/sail artisan migrate:fresh --seed`
- Panduan menjalankan Docker Sail (`sail up -d`, sync token, POST /nilai)

---

## Prompt 12: Penjelasan Tugas 3 & Compliance Check

**Waktu:** 10 Juni 2026, 12:15 WIB

**Prompt:**
> Tugas 3 saya sudah sesuai belum? SOAP dan RabbitMQ sudah benar?

**Hasil:**
AI menilai ~90% sesuai rubrik:
- ✅ SSO JWT, SOAP ReceiptNumber, RabbitMQ `nilai.recorded`
- ⚠️ API Key M2M masih 401 (fallback warga01 aktif)
- ⚠️ `analisis_tugas_3.md` perlu diselaraskan dengan kode

---

## Prompt 13: API Key & Token — Penjelasan Konsep

**Waktu:** 10 Juni 2026, 13:00–14:30 WIB

**Prompt:**
> Fungsi API Key KEY-MHS-117 apa? Cek status 401 atau 200. Token di .env sama JWT Swagger beda?

**Hasil:**
- **API Key M2M** → token outbound untuk SOAP + RabbitMQ (identitas TIM-09)
- **JWT Swagger** → token inbound user warga01 untuk `POST /nilai`
- **X-IAE-KEY** → NIM `102022580023` (bukan API Key!)
- Pengecekan awal: HTTP **401** Unauthorized
- Pengecekan ulang (13:38 WIB): HTTP **200** — M2M key sudah aktif, board tampil **"Dari: TEAM-09"**

---

## Prompt 14: SOAP ActivityName & Sample Dosen

**Waktu:** 10 Juni 2026, 14:22 WIB

**Prompt:**
> Apakah sample SOAP dosen (TEAM-01 ShipmentCreated) sudah terimplementasi? ActivityName Service C apa?

**Hasil:**
- Sample PDF hanya contoh tim lain; Service C pakai:
  - `TeamID`: **TIM-09**
  - `ActivityName`: **NilaiRecorded**
  - `LogContent`: CDATA JSON transaksi nilai
- Response: `<iae:Status>SUCCESS</iae:Status>` + `ReceiptNumber`

---

## Prompt 15: Troubleshooting Swagger

**Waktu:** 10 Juni 2026, 14:30 WIB

**Prompt:**
> Swagger error: "Unauthorized. Header X-IAE-KEY tidak valid atau tidak ditemukan."

**Hasil:**
AI menjelaskan Swagger wajib **2 authorize**:
1. **X-IAE-KEY** = `102022580023` (NIM, bukan KEY-MHS-117)
2. **bearerAuth** = JWT dari login warga01 (beda dari `IAE_SSO_TOKEN` di `.env`)

---

## Prompt 16: Transaksi Kritis & POST /nilai + Service A

**Waktu:** 10 Juni 2026, 13:45 WIB

**Prompt:**
> Transaksi penting SOAP dari service saya mana? POST /nilai + validasi Service A GET /mahasiswa/{nim} sesuai?

**Hasil:**
- Transaksi kritis Tugas 3: **`POST /api/v1/nilai`**
- Validasi Service A = alur bisnis internal (Tugas Besar), kode sudah ada tapi graceful jika Service A mati
- Fokus rubrik Tugas 3: SSO → SOAP → RabbitMQ (Cloud Pusat)

---

## Prompt 17: nilai.recorded vs nilai.created

**Waktu:** 10 Juni 2026, 15:30 WIB

**Prompt:**
> Lebih baik nilai.recorded atau nilai.created?

**Hasil:**
AI merekomendasikan **`nilai.recorded`** — lebih tepat domain akademik (nilai dicatat setelah semester). Kode dan dokumen diselaraskan ke `nilai.recorded`.

---

## Prompt 18: Update analisis_tugas_3.md

**Waktu:** 10 Juni 2026, 14:08 WIB

**Prompt:**
> Buat ulang analisis_tugas_3.md sesuai implementasi, tanpa TEAM-01, pakai nilai.recorded, checklist centang.

**Hasil:**
- Rewrite `analisis_tugas_3.md` fokus TIM-09
- 2 sequence diagram Mermaid (§4a bootstrap token, §4b POST /nilai)
- Checklist implementasi semua `[x]`
- Contoh payload dari testing nyata

---

## Prompt 19: Update Log Prompting

**Waktu:** 10 Juni 2026, 16:00 WIB

**Prompt:**
> Update log prompting dari seluruh percakapan session ini, sertakan jam.

**Hasil:**
Update file `log_prompting.md` ini dengan Prompt 7–19 dan timeline lengkap 10 Juni 2026.
