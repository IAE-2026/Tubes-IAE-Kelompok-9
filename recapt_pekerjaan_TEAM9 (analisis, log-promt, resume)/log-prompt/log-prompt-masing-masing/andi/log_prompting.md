# Log Prompting AI - Service C: Nilai & Kurikulum

**Identitas Mahasiswa:**
- **Nama:** Andi Muh. Arif Darma Saputra M  
- **NIM:** 102022580023  
- **Mata Kuliah:** BBK2HAB3 - Integrasi Aplikasi Enterprise
- **Layanan:** Service C, Nilai & Kurikulum
- **Tool AI:** Antigravity (Gemini 2.0 Flash), Cursor AI (Claude)
- **Tanggal Buat:** 15 Mei 2026
- **Update Terakhir:** 19 Juni 2026, ~13:15 WIB

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
- `app/Http/Middleware/CheckIaeKey.php`, validasi header `X-IAE-KEY: 102022580023`
- Didaftarkan sebagai alias `iae.key` di `bootstrap/app.php`

### d. Controllers
- `KurikulumController.php`, 2 endpoint (Collection + Resource)
- `NilaiController.php`, 3 endpoint (Collection + Resource + Action)
- Semua menggunakan PHP 8 Attribute Swagger annotations
- POST /nilai memanggil Service A untuk validasi mahasiswa aktif

### e. Response Helper
- `app/Helpers/ApiResponse.php`, wrapper JSON konsisten (status, message, data, meta)

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

1. GET `/api/v1/kurikulum` → menampilkan 10 data kurikulum (OK)
2. GET `/api/v1/kurikulum/SI201` → detail Struktur Data (OK)
3. GET `/api/v1/nilai` → semua data nilai (OK)
4. GET `/api/v1/nilai/102022400136` → nilai + IPS 3.29 (OK)
5. POST `/api/v1/nilai` → catat nilai baru (OK)
6. GET `/api/v1/kurikulum` tanpa key → 401 Unauthorized (OK)
7. `/api/documentation` di browser → Swagger UI tampil (OK)
8. `/graphiql` di browser → GraphQL Playground tampil (OK)
9. Docker container → MySQL & Laravel Sail running (OK)

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

14 Mei 23:31 WIB: bersihin folder proyek buat fresh install Laravel 12.  
14 Mei 23:35 WIB: install Laravel 12 via composer create-project.  
14 Mei 23:36 WIB: pasang L5-Swagger, Lighthouse GraphQL, GraphiQL.  
14 Mei 23:37 WIB: balikin controller model middleware seeder dari backup.  
14 Mei 23:38 WIB: bikin migrasi tabel kurikulums dan nilais terus jalanin.  
14 Mei 23:39 WIB: seed data dummy 10 kurikulum 8 nilai.  
14 Mei 23:43 WIB: sempat pindah ke MySQL XAMPP karena minta user.  
14 Mei 23:46 WIB: balik lagi ke SQLite sesuai spek tugas.  
15 Mei 00:01 WIB: cek Swagger UI di /api/documentation udah tampil.  
15 Mei 00:08 WIB: tes header X-IAE-KEY buat akses API.  
15 Mei 00:13 WIB: matiin server lokal mau pindah ke Docker.  
15 Mei 00:16 WIB: setup Laravel Sail sama docker-compose.yml.  
15 Mei 00:46 WIB: fix error file sharing di Mac plus bersihin file duplikat.  
15 Mei 00:55 WIB: pindah lagi ke MySQL 8.4 di Docker buat kebutuhan tim.  
15 Mei 01:32 WIB: rebuild container di folder Users biar mount aman.  
15 Mei 01:46 WIB: jalanin db:seed isi data NIM mahasiswa ke MySQL.  
15 Mei 01:50 WIB: update log prompting akhir session Mei.

Timeline Tugas 3, 10 Juni 2026

11:00 WIB: minta plan Tugas 3, spek TIM-09, event nilai.recorded, token auto sync.  
11:16 WIB: kena error duplicate SI101, fix pake migrate:fresh --seed.  
11:26 WIB: tes POST nilai SI401, receipt IAE-LOG-2026-240F6F50, board OK.  
11:30 WIB: implement iae:sync-token, VerifyJwtSso, SoapAuditClient, CentralMessagePublisher.  
11:38 WIB: tes NIM 2099000066 SI301 PBO, receipt EB8E14F7.  
11:45 WIB: fix payload publish harus ada message wrapper sama routing_key biar badge muncul.  
12:08 WIB: tes NIM 2099000044 SI201 Struktur Data, receipt B9607615.  
12:15 WIB: cek lagi udah sesuai rubrik Tugas 3 belum.  
12:45 WIB: tes NIM 2099000015 SI302 Jaringan Komputer, receipt 828A266C.  
13:00 WIB: bingung bedain API Key M2M, JWT, sama X-IAE-KEY.  
13:38 WIB: KEY-MHS-117 awalnya 401, abis itu 200 M2M aktif.  
13:45 WIB: konfirmasi POST /nilai memang transaksi kritisnya.  
13:57 WIB: publish dari TEAM-09 SI501 nilai akhir, receipt 05048A23.  
14:22 WIB: konfirmasi SOAP pake TeamID TIM-09 ActivityName NilaiRecorded.  
14:30 WIB: Swagger error, ternyata salah isi X-IAE-KEY vs bearer JWT.  
14:48 WIB: tes SI102 AB/3.5 NIM 10202250023, receipt 54BADCCE.  
15:30 WIB: putusin pake event nilai.recorded bukan nilai.created.  
16:00 WIB: update log prompting session 10 Juni.  
20:00 WIB: nanya kenapa harus 2 token, beda X-IAE-KEY sama KEY-MHS-117.    
11 Juni 10:00 WIB: update log sampai pagi 11 Juni.  
12 Juni 06:58 WIB: cek Sail docker masih jalan, update log sampai hari ini.

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

Log Prompting Service C TIM-09 NIM 102022580023 update 19 Juni 2026

---

## Modul 5: Sesi Tugas Besar & Integrasi Monorepo (17–19 Juni 2026)

Recap prompt saya ke **Cursor AI** dari eksekusi merge tim sampai update log ini.  
**Tool:** Cursor AI · **Repo:** `Tubes-IAE-Kelompok-9` · **Domain:** Education System  

> Catatan waktu: yang bertanda **(exact)** dari timestamp chat/screenshot. Yang **(perkiraan)** dari urutan sesi di hari yang sama.

---

### A. Persiapan sebelum merge (17 Juni 2026)

**[17 Jun ~14:30 WIB · perkiraan] Prompt 1**  
> Pelajari dari log prompt sebelumnya di folder `102022580023_...-PERSYARATAN-DAN-KURIKULUM`

**Hasil:** AI baca ulang `log_prompting.md`, `analisis_tugas_3.md`, transkrip chat lama. Ringkasan perjalanan Service C Mei–Juni biar lanjut Tugas Besar nggak dari nol.

---

**[17 Jun ~14:45 WIB · perkiraan] Prompt 2**  
> Sebelum saya suruh merge, menurut kamu folder Service A, B, sama C udah siap digabung belum? Bandingin dulu apa yang masih beda (port, auth, URL internal).

**Hasil:** AI bandingin ketiga service, port beda, network Docker beda, header auth beda, URL internal masih localhost masing-masing. Menurut saya belum siap merge tanpa harmonisasi dulu.

---

**[17 Jun ~15:10 WIB · perkiraan] Prompt 3**  
> Berikan saran langkah terbaiknya yang mana?

**Hasil:** AI sarankan monorepo + satu Docker network + API Gateway single entry point.

---

**[17 Jun ~15:25 WIB · perkiraan] Prompt 4**  
> Buatkan mapping-nya biar saya paham

**Hasil:** Diagram alur B→A, B→C, C→A, header, port internal vs gateway.

---

**[17 Jun 15:57 WIB · exact] Prompt 5**  
> Apakah rencana ini sudah matang berdasarkan ketentuan dosen? Ini tugas besar bukan tugas 3

**Hasil:** AI jawab belum matang, API Gateway wajib, service nggak boleh diakses langsung dari luar.

---

**[17 Jun 16:00 WIB · exact] Prompt 6**  
> Kirim PDF: `Tugas Besar Tugas 3 - The Enterprise Digital City (1).pdf`

**Hasil:** AI baca rubrik resmi, Gateway 20%, End-to-End 25%, Central Infrastructure 25%, Dokumentasi 30%.

---

**[17 Jun 16:01 WIB · exact] Prompt 7**  
> Repo tim: `git@github.com:IAE-2026/Tubes-IAE-Kelompok-9.git`

**Hasil:** AI clone repo, cek branch, lanjut rencana git subtree.

---

### B. Eksekusi merge & infrastruktur ( 17 Juni 2026

**[17 Jun 16:07 WIB · exact] Prompt 8**  
> `git subtree add services/mahasiswa` → error `fatal: not a git repository`

**Hasil:** Belum di root repo yang benar. AI bantu `git init` / pindah direktori.

---

**[17 Jun 16:10 WIB · exact] Prompt 9**  
> `git subtree add services/krs` → error `working tree has modifications`

**Hasil:** AI commit/stash dulu, subtree lanjut.

---

**[17 Jun 16:24 WIB · exact] Prompt 10**  
> Lanjutkan todo: `gateway/nginx.conf`, Dockerfile Service C, `docker-compose.yml`, validasi config, build & up

**Hasil:** Gateway port **8080**, service internal only, Dockerfile PHP 8.4 Service C, `docker compose config` valid, stack `up`.

---

**[17 Jun ~16:35 WIB · perkiraan] Prompt 11**  
> Coba lanjutkan

**Hasil:** Lanjut subtree Service C + wiring docker network.

---

**[17 Jun ~16:50 WIB · perkiraan] Prompt 12–13**  
> Lanjutkan ke fase 4 (2×)

**Hasil harmonisasi:**
- `MahasiswaSeeder` Service A
- `ApiKeyMiddleware` terima `X-API-KEY` + `X-IAE-KEY`
- `ExternalAcademicService` header per service
- KRS external validation ke A & C
- Fix nginx forward header
- Test POST `/krs` lewat gateway

---

### C. Testing & troubleshooting Postman ( 17 Juni 2026

**[17 Jun ~17:05 WIB · perkiraan] Prompt 14**  
> Test Service A SOAP & RabbitMQ muncul di board `iae-sso.virtualfri.id`

**Hasil:** POST `/api/v1/mahasiswa` → receipt SOAP + `mahasiswa.created` di board.

---

**[17 Jun ~17:08 WIB · perkiraan] Prompt 15**  
> Cek 1 mahasiswa ambil berapa matkul

**Hasil:** Endpoint `/api/v1/mahasiswa/{nim}/matkul`, gabung KRS (B) + nilai (C).

---

**[17 Jun ~17:10 WIB · perkiraan] Prompt 16**  
> Agar sesuai ketentuan tugas besar dosen (format JSON)

**Hasil:** Sesuaikan response JSON Service A + `Http::pool` paralel.

---

**[17 Jun ~17:12 WIB · perkiraan] Prompt 17**  
> Cara cek Postman?

**Hasil:** AI jelaskan header + URL gateway `:8080`.

---

**[17 Jun 17:13 WIB · exact · screenshot]** Prompt 18  
> (Kirim screenshot Postman error, file `Screenshot_2026-06-17_at_17.13.47`)

**Hasil:** Mulai debug auth header.

---

**[17 Jun ~17:15 WIB · perkiraan] Prompt 19**  
> Mau pakai `https://iae-sso.virtualfri.id` bukan lokal

**Hasil:** AI bedain SSO cloud vs gateway lokal Service A.

---

**[17 Jun ~17:18 WIB · perkiraan] Prompt 20**  
> Intinya mau cek format JSON Service A

**Hasil:** Fokus ke response `/matkul`.

---

**[17 Jun 17:20 WIB · exact · screenshot] Prompt 21**  
> Pecahkan masalahnya (`Screenshot_2026-06-17_at_17.20.24`)

**Hasil:** Investigasi nginx + API key.

---

**[17 Jun ~17:22 WIB · perkiraan] Prompt 22**  
> Response 401: `Unauthorized. Invalid or missing API Key`

**Hasil:** Salah key/header, harus `X-API-KEY: KEY-MHS-233` via gateway.

---

**[17 Jun 17:26 WIB · exact · screenshot] Prompt 23**  
> Masih sama (`Screenshot_2026-06-17_at_17.26.36`)

**Hasil:** Lanjut fix forward header nginx.

---

**[17 Jun ~17:28 WIB · perkiraan] Prompt 24**  
> Ok sudah muncul, lihat matkul NIM `102022400136`

**Hasil:** JSON agregasi KRS + nilai tampil.

---

**[17 Jun ~17:30 WIB · perkiraan] Prompt 25**  
> Tambah 3 matakuliah untuk NIM yang sama sebelum dosen acc

**Hasil:** 3× POST `/krs` → 3× `krs.created`.

---

**[17 Jun 17:33 WIB · exact · dari payload RabbitMQ] Prompt 26**  
> Tanya format JSON `krs.created`, contoh timestamp event `2026-06-17T10:33:35+00:00` (= 17:33 WIB)

**Hasil:** 1 matkul = 1 event terpisah, bukan array dalam 1 message.

---

**[17 Jun ~17:35 WIB · perkiraan] Prompt 27**  
> Cara kerja Service A kirim SOAP & RabbitMQ di mana?

**Hasil:** Alur `MahasiswaController@store` → M2M → SOAP → RabbitMQ. Cuma POST maba.

---

**[17 Jun ~17:40 WIB · perkiraan] Prompt 28**  
> Buatkan kalimat git push

**Hasil:** AI kasih perintah push ke repo tim.

---

**[17 Jun ~17:45 WIB · perkiraan] Prompt 29**  
> Buatkan tutorial menjalankan microservice buat Jafar & Arneta

**Hasil:** Jadi `docs/TUTORIAL-MENJALANKAN.md`.

---

**[17 Jun 18:06 WIB · exact · screenshot] Prompt 30**  
> Cara git clone? (`Screenshot_2026-06-17_at_18.06.48`)

**Hasil:** AI jelaskan `git clone` + SSH repo tim.

---

**[17 Jun ~18:10 WIB · perkiraan] Prompt 31**  
> Tahan dulu, Jafar ubah branch dia dulu

**Hasil:** Tunda push, tunggu merge Jafar.

---

**[17 Jun ~18:15 WIB · perkiraan] Prompt 32**  
> Berhentikan container Docker tugas ini

**Hasil:** AI jalankan `docker compose down`.

---

**[17 Jun ~18:20–18:40 WIB · perkiraan] Prompt 33–36**  
> KRS kirim SOAP dari A atau B? · Format JSON SOAP/RabbitMQ? · Matkul >1 gimana? · Saran yang mana?

**Hasil:** Service **B** untuk KRS; 1 POST = 1 SOAP + 1 event; bukan 1 message multi-matkul.

---

### D. Pasca-merge tim & integrasi cloud ( 18–19 Juni 2026

**[18–19 Jun · siang · perkiraan] Prompt 37**  
> Abis git pull main, kayanya banyak perubahan dari merge Arneta & Jafar, tolong cek diff-nya, apa aja yang perlu saya selaraskan buat integrasi?

**Hasil:** AI review diff, Http::pool, gateway `/auth`, KEY-MHS-109/117/233 perlu selaras menurut saya.

---

**[19 Jun ~09:30 WIB · perkiraan] Prompt 38**  
> Dosen wajibkan `nim` di body M2M token SSO, tolong disesuaikan

**Hasil:** Update `{ api_key, nim }` semua service. `IAE_OWNER_NIM` Andi = `102022580023`. Route token di gateway.

---

**[19 Jun ~10:30 WIB · perkiraan] Prompt 39**  
> Cek A/B/C kirim SOAP & RabbitMQ, buat plan dulu

**Hasil:** Plan 3 transaksi kritis.

---

**[19 Jun ~10:45 WIB · perkiraan] Prompt 40**  
> Jalankan oleh Anda agar muncul di board

**Hasil eksekusi:**

- ~10:48 WIB, Service A: receipt `IAE-LOG-2026-B377E6F5`, event `mahasiswa.created`
- ~10:49 WIB, Service B: receipt `IAE-LOG-2026-F92E5D18`, event `krs.created`
- ~10:50 WIB, Service C: receipt `IAE-LOG-2026-7C375568`, event `nilai.recorded`

---

**[19 Jun 10:52 WIB · exact · screenshot] Prompt 41**  
> Status sudah masuk? (`Screenshot_2026-06-19_at_10.52.36`)

**Hasil:** Konfirmasi 3 event TIM-09 muncul di board cloud.

---

**[19 Jun ~11:00 WIB · perkiraan] Prompt 42**  
> Simulasi: KRS → dosen acc → nilai direkam

**Hasil:** NIM `102022580023` SI401, KRS pending → approved → nilai A + receipt SOAP.

---

**[19 Jun ~11:15 WIB · perkiraan] Prompt 43**  
> Simulasikan mahasiswa baru

**Hasil:** Maba NIM `209906191106`, receipt `IAE-LOG-2026-E62E7AB7`, event `mahasiswa.created`.

---

**[19 Jun ~11:25 WIB · perkiraan] Prompt 44**  
> Ambil 3 matakuliah (SI101, SI102, SI103)

**Hasil:** 3 KRS pending, 3× `krs.created`, 3× approve dosen. Nilai 3 matkul belum diinput saat sesi itu.

---

**[19 Jun ~11:35 WIB · perkiraan] Prompt 45**  
> Fix KRS maba, validasi nilai 404

**Hasil:** `ExternalAcademicService` KRS: `allowNotFound: true` untuk mahasiswa tanpa riwayat nilai.

---

### E. Dokumentasi kelompok ( 19 Juni 2026 (sore)

> **Catatan:** `analisis_tugas_3.md` dan `RESUME_ANDI.md` **sudah saya tulis sendiri** pas Tugas 3 (Modul 1–4), bukan hasil perintah AI di sesi ini. Di bagian bawah ini AI cuma bantu **plan struktur folder tim**, **copy file asli** per anggota, dan **rapikan README/file gabungan**, isi analisis & resume utamanya tetap dari tulisan kami masing-masing.

**[19 Jun ~12:00 WIB · perkiraan] Prompt 46**  
> Buat penilaian tugas besar, menurut saya perlu satu folder khusus buat log prompt, resume kontribusi, sama analisis studi kasus. Tolong bikinin plan struktur foldernya dulu, file asli di folder service jangan diubah ya.

**Hasil:** AI kasih plan struktur → saya review → jadi folder `recapt_penkerjaan_TEAM9 (analisis, log-promt, resume)/`.

---

**[19 Jun ~12:15 WIB · perkiraan] Prompt 47**  
> Plan-nya oke, tapi tolong pisahin per anggota: `andi/`, `arneta/`, `jafar/`. Isinya copy file asli kita masing-masing, jangan di-edit isinya.

**Hasil:** Copy identik ke `log-prompt-masing-masing/` (termasuk log & analisis saya yang udah ada sebelumnya).

---

**[19 Jun ~12:25 WIB · perkiraan] Prompt 48**  
> Maksudnya bukan cuma log prompt, resume sama bagian studi kasus juga harus ada subfolder per anggota kayak gitu.

**Hasil:** Sama untuk `resume-kontribusi-masing-masing/` dan `education-system-masing-masing/` (copy `RESUME_ANDI.md` & `analisis_tugas_3.md` dari folder service saya).

---

**[19 Jun ~12:30 WIB · perkiraan] Prompt 49**  
> Nama foldernya jangan cuma `resume` deh, pake `resume-kontribusi` biar lebih jelas buat dosen.

**Hasil:** Rename folder + README index + file gabungan (AI bantu susun, isi dari file asli tim).

---

**[19 Jun ~12:35 WIB · perkiraan] Prompt 50**  
> Resume Andi & Jafar udah ada di masing-masing folder service, copy aja ke subfolder tim. Kalau README perlu penjelasan singkat, AI boleh bantu, tapi jangan nulis ulang resume dari nol. Commit folder recap penkerjaannya.

**Hasil:** Copy `RESUME_ANDI.md` + resume Jafar ke subfolder; README ditambah ringkasan (AI ~80% bantu format, kata-kata penjelasan saya sesuaikan sendiri). Commit `d445f2b`.

---

**[19 Jun ~12:40 WIB · perkiraan] Prompt 51**  
> Oh iya, untuk studi kasus namanya **Education System** ya, bukan studi kasus generik.

**Hasil:** Rename `analisis-studi-kasus/` → `education-system/`.

---

**[19 Jun ~12:45 WIB · perkiraan] Prompt 52**  
> Nama service saya **Nilai & Kurikulum**

**Hasil:** Update dokumentasi + Swagger title. Kode teknis tidak berubah.

---

**[19 Jun ~12:50 WIB · perkiraan] Prompt 53**  
> Apakah ngaruh kalau namanya diubah?

**Hasil:** AI konfirmasi aman, container, URL, key, NIM tetap.

---

**[19 Jun ~13:00 WIB · perkiraan] Prompt 54**  
> File gabungan (`*-gabungan.md`) tolong pakai bahasa mahasiswa, AI boleh bantu susun paragraf, tapi saya mau ada kata-kata saya sendiri biar nggak kesan 100% AI

**Hasil:** AI draft dulu → saya edit manual beberapa kalimat → rewrite 3 file `*-gabungan.md`.

---

**[19 Jun ~13:10 WIB · perkiraan] Prompt 55**  
> Recap log prompting eksekusi ke Anda sampai detik ini

**Hasil:** Modul 5 ditambahkan.

---

**[19 Jun ~13:15 WIB · exact, prompt ini] Prompt 56**  
> Sertakan detail waktunya juga

**Hasil:** Modul 5 diperbarui dengan timestamp WIB per prompt (update log ini).

---

## Timeline kronologis lengkap

### Rabu, 17 Juni 2026

- ~14:30 WIB: baca log lama + bandingin 3 folder service (belum merge)
- ~15:10 WIB: saran merge + mapping integrasi
- **15:57 WIB:** tanya matang/tidak vs rubrik Tugas Besar
- **16:00 WIB:** kirim PDF dosen
- **16:01 WIB:** clone repo tim
- **16:07 WIB:** git subtree Service A (error repo)
- **16:10 WIB:** git subtree Service B (error working tree)
- **16:24 WIB:** gateway + docker-compose + Dockerfile C
- ~16:35–16:50 WIB: fase 4 harmonisasi integrasi
- ~17:05 WIB: test SOAP/RabbitMQ Service A
- ~17:08–17:10 WIB: endpoint matkul + format JSON
- **17:13 WIB:** screenshot Postman error (debug auth)
- **17:20 WIB:** screenshot lanjutan debug
- ~17:22 WIB: error 401 API Key
- **17:26 WIB:** screenshot masih 401
- ~17:28 WIB: JSON matkul berhasil
- ~17:30 WIB: tambah 3 KRS NIM 102022400136
- **17:33 WIB:** konfirmasi format event KRS (1 matkul = 1 event)
- ~17:35–17:45 WIB: penjelasan SOAP A, git push, tutorial tim
- **18:06 WIB:** screenshot tanya git clone
- ~18:10–18:40 WIB: tunda push, stop Docker, tanya SOAP KRS & JSON

### Kamis, 19 Juni 2026

- ~09:30 WIB: git pull review + update M2M token + nim
- ~10:30–10:50 WIB: plan & eksekusi test A/B/C ke cloud board
- **10:52 WIB:** screenshot board, 3 event TIM-09 confirmed
- ~11:00 WIB: simulasi KRS, acc, nilai
- ~11:15–11:25 WIB: simulasi maba + 3 KRS + 3 approve
- ~11:35 WIB: fix KRS maba tanpa riwayat nilai
- ~12:00–13:15 WIB: rapikan folder recap tim (copy file asli, bukan tulis analisis/resume baru)

---

## Refleksi Modul 5

Sesi Tugas Besar jauh lebih berat dari Tugas 3 individu, urusan tim, gateway, selaraskan key, dokumentasi kelompok. Yang paling sering saya tanya: **"udah sesuai rubrik dosen belum?"** dan **"format JSON / event RabbitMQ gimana?"**, terus verifikasi sendiri di Postman & board cloud.

AI saya pakai sekitar **80%** buat coding, debugging, sama bantu struktur folder dokumentasi tim. Keputusan akhir tetap saya cek: receipt SOAP ada nggak, badge event muncul nggak, Postman 401 kenapa. **Analisis Tugas 3** (`analisis_tugas_3.md`) dan **resume individu** (`RESUME_ANDI.md`) saya tulis sendiri pas Modul 1–4, di Modul 5 cuma di-copy ke folder tim, bukan dibuat ulang AI. Rename ke **Nilai & Kurikulum** cuma dokumentasi, yang jalan tetap KEY-MHS-117, NIM 102022580023, Docker network internal.

Log Prompting Service C TIM-09 NIM 102022580023, update 19 Juni 2026, ~13:15 WIB (Modul 5 + detail waktu)
