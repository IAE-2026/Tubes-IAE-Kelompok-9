# AI Prompting Log - Tugas Besar IAE
**Service A - Data Mahasiswa Service**  
**BBK2HAB3 - Integrasi Aplikasi Enterprise**  
**Nama:** Arneta Alifiana  
**NIM:** 102022400136  
**Kelompok:** Group 9 (TEAM-09)  

---

## Pendahuluan & Tujuan Penggunaan AI
Dokumen ini merekam secara lengkap riwayat interaksi dan diskusi teknis (prompting) antara pengembang (Arneta) dengan AI selama proses pengerjaan Tugas Besar Integrasi Aplikasi Enterprise. Seluruh rancangan arsitektur, implementasi fitur, hingga langkah-langkah optimalisasi performa dikerjakan dengan kolaborasi intensif, di mana pengembang aktif memberikan masukan arsitektur, memandu skema kode, serta menyelesaikan masalah integrasi bersama AI.

---

## Log Diskusi Teknis & Prompting

### 1. Inisialisasi Project & Skema Database
*   **Prompt (Arneta):**
    > Aku ingin men-setup Service A (Data Mahasiswa) menggunakan Laravel 12, MySQL 8, dan Docker. Rancanganku untuk tabel `mahasiswas` harus memiliki field: id (PK), nim (unique, max 20), nama (max 100), email (unique, max 100), prodi (max 100), angkatan (year), dan status (enum: aktif, cuti, lulus, do; default: aktif). Tolong buatkan file migration Laravel-nya agar sesuai dengan spesifikasi database ini.
*   **Respons AI:**
    > Tentu, mari kita buat file migration tersebut. Kita akan menggunakan class Blueprint di Laravel untuk mendefinisikan tipe kolom sesuai spesifikasi Anda, termasuk constraint `unique()` untuk NIM dan Email serta nilai default pada kolom `status`.
*   **Hasil:** File migrasi berhasil dibuat dan database siap dijalankan di container MySQL.

---

### 2. Pengamanan API Key
*   **Prompt (Arneta):**
    > Aku ingin melindungi endpoint-endpoint di routes/api.php menggunakan middleware API Key kustom. Otorisasinya akan membaca header `X-API-KEY` atau `X-IAE-KEY`. Jika tidak cocok dengan `API_KEY` di `.env`, return JSON response 401 Unauthorized. Tolong bantu buatkan class middleware `ApiKeyMiddleware` ini di Laravel.
*   **Respons AI:**
    > Ide pengamanan ini sangat penting untuk komunikasi antar-service (M2M). Kita bisa membuat class `ApiKeyMiddleware` di Laravel. Kita akan membaca header `X-API-KEY` dan `X-IAE-KEY` menggunakan `$request->header()`, dan jika kosong kita fallback ke query parameter. Gunakan `hash_equals()` untuk perbandingannya agar aman dari timing attacks.
*   **Hasil:** Middleware berhasil dibuat dan endpoint internal Service A berhasil terlindungi dengan aman.

---

### 3. REST API Controller dengan Swagger
*   **Prompt (Arneta):**
    > Sekarang buatkan `MahasiswaController` untuk menghandle routing GET `/api/v1/mahasiswa`, GET `/api/v1/mahasiswa/{nim}`, dan POST `/api/v1/mahasiswa`. Aku ingin setiap endpoint ini langsung didekorasi dengan anotasi L5 Swagger (OpenAPI) agar dokumentasinya bisa di-generate otomatis.
*   **Respons AI:**
    > Bagus sekali, dengan Swagger dokumentasi API Anda akan otomatis sinkron dengan kode program. Kita buat class `MahasiswaController` lengkap dengan atribut PHP attributes seperti `#[OA\Get]`, `#[OA\Post]`, dan `#[OA\Response]` di atas masing-masing method.
*   **Hasil:** Controller dan Swagger dokumentasi berhasil terbuat dan diakses di rute `/api/documentation`.

---

### 4. Setup Docker & Troubleshooting Koneksi
*   **Prompt (Arneta):**
    > Aku mendapati error `Connection refused` dari MySQL saat pertama kali menjalankan container Laravel. Sepertinya database butuh waktu lebih lama untuk siap daripada Laravel. Bagaimana cara memodifikasi CMD di `Dockerfile` agar memberi delay 10 detik dan melakukan `migrate:fresh` otomatis sebelum container Laravel berjalan?
*   **Respons AI:**
    > Ini adalah masalah startup race condition yang umum di Docker. Kita bisa memodifikasi instruksi `CMD` di `Dockerfile` menggunakan shell chain. Kita tambahkan `sleep 10` sebelum memanggil `php artisan migrate:fresh --force`, lalu jalankan `php-fpm` dan `nginx` secara bersamaan di latar belakang.
*   **Hasil:** Container berjalan sinkron dan database termigrasi secara bersih tanpa kendala.

---

### 5. Integrasi SSO (Federated Login)
*   **Prompt (Arneta):**
    > Aku punya endpoint login warga di `/api/v1/auth/token` terpusat. Di database lokal, aku sudah menyiapkan tabel `sso_users` (name, email, nim, role, jwt_token). Aku ingin membuat `SsoController` lokal yang mem-forward email/password ke SSO pusat, dan jika sukses, email-nya dipetakan ke role lokal (jika mengandung 'admin' -> admin, 'dosen' -> dosen, lainnya -> mahasiswa) lalu disimpan ke database lokal.
*   **Respons AI:**
    > Tentu, kita bisa merancang `SsoController` yang menggunakan HTTP Client Laravel untuk mem-forward data ke endpoint SSO pusat. Setelah mendapat profil dan token, gunakan string helper `str_contains()` untuk memeriksa kata 'admin' atau 'dosen' di email warga, lalu gunakan `updateOrCreate()` pada model `SsoUser` lokal untuk menyimpan token JWT terbarunya.
*   **Hasil:** Integrasi login SSO lokal berhasil berjalan dan profil warga terpetakan secara otomatis ke database.

---

### 6. Integrasi SOAP Audit Log (XML Kustom)
*   **Prompt (Arneta):**
    > Setiap pendaftaran mahasiswa baru sukses, aku harus melaporkan log audit ke endpoint SOAP pusat. Karena library SOAP native PHP sering bermasalah di Docker, aku ingin menyusun template XML SOAP Envelope secara manual sebagai string di `SoapAuditService`. Strukturnya berisi `<iae:TeamID>`, `<iae:ActivityName>`, dan `<iae:LogContent>` dibungkus CDATA. Nanti kita kirim pakai HTTP Client Laravel dan ambil `ReceiptNumber` dari respon XML-nya pakai Regex. Bagaimana implementasinya?
*   **Respons AI:**
    > Ini alternatif yang sangat baik untuk menghindari isu kompatibilitas. Kita bisa membuat class `SoapAuditService` dan merakit string XML SOAP Envelope secara dinamis di dalamnya. Kirimkan string tersebut sebagai raw body menggunakan `Http::withBody($xml, 'text/xml')->post()`. Setelah sukses, gunakan `preg_match` untuk mengekstrak string di dalam tag `<iae:ReceiptNumber>` dan simpan ke database lokal.
*   **Hasil:** SOAP client kustom berhasil dibangun dan nomor bukti audit (`ReceiptNumber`) sukses tersimpan.

---

### 7. Integrasi RabbitMQ (AMQP Publisher)
*   **Prompt (Arneta):**
    > Aku juga perlu menyebarkan event asinkron `mahasiswa.created` ke RabbitMQ via HTTP Gateway pusat di `/api/v1/messages/publish`. Payload-nya harus berisi `routing_key` dan objek `message` (event, timestamp, team_id, data). Tolong buatkan class `RabbitMQService` untuk mempublikasikan event ini dengan benar.
*   **Respons AI:**
    > Baik, mari kita buat service `RabbitMQService.php`. Kita susun payload JSON sesuai dengan requirement: `routing_key` diisi nama event, dan di dalam `message` ada event, timestamp berformat ISO 8601, `team_id` diisi `'TEAM-09'`, serta data mahasiswa. Kirim request POST tersebut dengan menyertakan token M2M SSO.
*   **Hasil:** Event berhasil terpublikasi ke broker RabbitMQ terpusat setiap kali ada pendaftaran mahasiswa baru.

---

### 8. Fallback Token (Ketahanan Sistem)
*   **Prompt (Arneta):**
    > Aku menyadari jika koneksi ke SSO mati saat pendaftaran mahasiswa baru, backend akan crash karena tidak bisa mendapat token M2M. Bagaimana kalau kita tambahkan logika fallback? Jika token M2M dari API Key gagal didapat, kita cari token JWT dari user SSO yang login terakhir kali di lokal (`SsoUser::latest()->first()`). Ini akan menjaga aplikasi tetap berjalan.
*   **Respons AI:**
    > Ide Anda sangat bagus untuk penanganan kesalahan (*error handling*) dan ketahanan sistem. Kita bisa memodifikasi controller pendaftaran mahasiswa. Sebelum mengirim data SOAP/RabbitMQ, kita lakukan pemeriksaan M2M token. Jika gagal, cari token user SSO terakhir di database lokal. Kita juga bungkus pengiriman SOAP dan RabbitMQ dalam pengecekan `if ($token)` agar tidak memicu fatal error.
*   **Hasil:** Aplikasi menjadi lebih tangguh (*fault-tolerant*) dan tidak mengalami crash saat integrasi luar bermasalah.

---

### 9. Gateway Routing untuk Autentikasi
*   **Prompt (Arneta):**
    > Rute login SSO `/api/v1/auth/login` tidak bisa diakses dari luar karena gateway Nginx hanya meneruskan path `/api/v1/mahasiswa`. Tolong tambahkan lokasi `/api/v1/auth` di `gateway/nginx.conf` agar mengarah ke `mahasiswa-service` di port 8000.
*   **Respons AI:**
    > Tentu, karena Service A berjalan di internal Docker network, rute autentikasi juga harus diekspos melalui API Gateway. Kita tambahkan konfigurasi lokasi baru `location /api/v1/auth` di dalam HTTP block `nginx.conf` yang melakukan proxy pass ke `mahasiswa_service`.
*   **Hasil:** Endpoint login SSO lokal dapat diakses secara langsung lewat port gateway 8080.

---

### 10. Penanganan Fallback Nilai 404 (Agregasi)
*   **Prompt (Arneta):**
    > Pada endpoint agregasi matkul, jika mahasiswa baru belum punya nilai di Service C, Service C merespons 404. Ini bikin agregasi di Service A ikut gagal dengan error 404. Kita harus buat penanganan fallback di `MahasiswaController@matkul`: jika status respon dari Service C adalah 404, kita buat default nilai kosong (`total_sks = 0`, `ips = 0.0`, `nilai = []`), bukan mengembalikan error.
*   **Respons AI:**
    > Ide penanganan kasus khusus ini sangat penting. Di controller Service A, kita tambahkan pengecekan respon. Jika `$nilaiResult['status'] === 404`, kita tangkap dan buatkan array data kosong secara manual, lalu lanjutkan proses tanpa mengembalikan JSON error 404.
*   **Hasil:** Endpoint agregasi matkul dapat menampilkan KRS aktif mahasiswa baru dengan sukses tanpa terpengaruh ketiadaan riwayat nilai di Service C.

---

### 11. Optimalisasi Concurrency (Parallel HTTP Pool)
*   **Prompt (Arneta):**
    > Saat ini pemanggilan API ke Service B (KRS) dan Service C (Nilai) di method `matkul` berjalan secara sekuensial (bergantian) sehingga total waktu respon lambat. Bisakah kita optimalkan menggunakan `Http::pool` agar request ke kedua service tersebut berjalan secara paralel?
*   **Respons AI:**
    > Ide optimasi performa yang luar biasa! Kita bisa merombak `ExternalAcademicService` untuk memanggil kedua service secara bersamaan menggunakan pool HTTP asinkron. Dengan `Http::pool()`, request ke KRS dan Nilai berjalan secara bersamaan dan latency backend akan berkurang secara signifikan.
*   **Hasil:** Kecepatan respon endpoint agregasi matkul meningkat drastis.

