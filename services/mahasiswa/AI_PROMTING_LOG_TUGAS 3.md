# 🤖 AI Prompting Log - Tugas 3 (Integrasi Layanan Enterprise)
**Service A - Data Mahasiswa Service**  
**Nama:** Arneta Alifiana  
**NIM:** 102022400136  
**Kelompok:** Group 9 (TEAM-09)  

---

## 📌 Pendahuluan & Tujuan Penggunaan AI
Dokumen ini mencatat secara lengkap interaksi tanya-jawab (prompting) antara mahasiswa dengan kecerdasan buatan (AI) selama pengerjaan **Tugas 3 - The Enterprise Digital City** pada Service A (Data Mahasiswa). 

Fokus utama penggunaan AI dalam tugas ini adalah untuk memandu arsitektur kode integrasi, mengatasi kendala transmisi data eksternal, membuat client SOAP tanpa library bawaan PHP yang kompleks, dan mengatur pengiriman antrean pesan (AMQP) ke RabbitMQ.

---

## 📝 Log Prompting Teknis Tugas 3 (Detail & Lengkap)

### 1. Perencanaan Autentikasi Aplikasi ke Aplikasi (M2M Token)
**Prompt:**
> Aku kelompok 9 (TEAM-09) sedang mengerjakan Tugas 3 Service A (Laravel). Aku punya API Key MHS "KEY-MHS-233" dan akun warga warga29@ktp.iae.id. Sesuai aturan, proses audit SOAP dan RabbitMQ harus memakai token M2M dari API Key tim kelompokku (TEAM-09), bukan token warga biasa. Bagaimana cara aplikasi secara otomatis meminta token M2M ke SSO Cloud Dosen setiap kali ada transaksi pencatatan mahasiswa baru sebelum menjalankan integrasi lainnya?

**Respons AI:**
Memberikan panduan implementasi M2M Auth:
- Menyarankan penyimpanan API Key di `.env` (seperti `API_KEY=KEY-MHS-233`) dan mendaftarkannya di `config/app.php`.
- Menyarankan pembuatan method privat `getM2MToken()` di dalam `MahasiswaController.php`.
- Menggunakan `Illuminate\Support\Facades\Http` untuk melakukan POST request ke `https://iae-sso.virtualfri.id/api/v1/auth/token` dengan payload `{ "api_key": "KEY-MHS-233" }`.
- Mengekstrak string `token` dari respon JSON untuk digunakan sebagai Bearer Token pada request berikutnya.

**Hasil:** Token M2M berhasil diambil secara dinamis di latar belakang aplikasi setiap kali endpoint registrasi mahasiswa dipanggil.

---

### 2. Implementasi Login & Role Mapping Terfederasi (Federated SSO)
**Prompt:**
> Aku mau buat fitur login SSO yang terhubung ke server Cloud Dosen (POST /api/v1/auth/token). Setelah warga sukses login dan dapat token JWT beserta data profile dari SSO pusat, aku mau email-nya dicek di aplikasi lokalku: jika email mengandung kata "admin", beri role lokal admin; jika mengandung kata "dosen", beri role dosen; sisanya dipetakan ke role mahasiswa. Semua data profil ini harus disimpan/diupdate ke tabel sso_users di database lokalku. Bagaimana kode migrasi dan controllernya di Laravel?

**Respons AI:**
Membimbing pembuatan skema Federated SSO:
- Membuat file migrasi database `sso_users` dengan skema:
  ```php
  Schema::create('sso_users', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('email')->unique();
      $table->string('nim')->nullable();
      $table->string('role')->default('mahasiswa');
      $table->text('jwt_token')->nullable();
      $table->timestamps();
  });
  ```
- Membuat `SsoController.php` dengan method `login()`.
- Menggunakan logika pencocokan string `str_contains($profile['email'], 'dosen')` atau `str_contains($profile['email'], 'admin')` untuk memetakan role.
- Menggunakan Eloquent `updateOrCreate()` untuk mencocokkan email dan memperbarui token JWT terbaru di database lokal.

**Hasil:** SSO User lokal berhasil dibuat. Saat user login via SSO, data langsung tersimpan dengan role yang sesuai di MySQL lokal.

---

### 3. Pembuatan Client SOAP XML Kustom untuk Log Audit
**Prompt:**
> Setelah data mahasiswa baru tersimpan di database lokal, aku harus mengirim log audit ke server SOAP terpusat (POST https://iae-sso.virtualfri.id/soap/v1/audit). Payload-nya harus berupa XML SOAP Envelope kaku. Di dalamnya ada tag `<iae:TeamID>` diisi TEAM-09, `<iae:ActivityName>` diisi MahasiswaBaru, dan `<iae:LogContent>` berisi string JSON mahasiswa baru yang dibungkus CDATA. Setelah berhasil dikirim, aku harus mengambil nilai `<iae:ReceiptNumber>` dari XML responnya untuk disimpan ke tabel audit_logs lokal. Tolong buatkan Service SOAP di Laravel tanpa menggunakan ekstensi PHP-SOAP bawaan karena sering error.

**Respons AI:**
Menyusun class `SoapAuditService.php` berbasis raw HTTP POST:
- Membuat migrasi tabel `audit_logs` dengan kolom: `activity_name`, `log_content`, `receipt_number`, dan `status`.
- Merakit template string XML SOAP Envelope secara manual:
  ```php
  $xml = '<?xml version="1.0" encoding="UTF-8"?>
  <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:iae="http://iae.central/audit">
      <soap:Body>
          <iae:AuditRequest>
              <iae:TeamID>TEAM-09</iae:TeamID>
              <iae:ActivityName>'.$activityName.'</iae:ActivityName>
              <iae:LogContent><![CDATA['.$logContent.']]></iae:LogContent>
          </iae:AuditRequest>
      </soap:Body>
  </soap:Envelope>';
  ```
- Mengirimkan data menggunakan `Http::withToken($token)->withHeaders(['Content-Type' => 'text/xml'])->withBody($xml, 'text/xml')->post(...)`.
- Mengekstrak `ReceiptNumber` menggunakan regular expression (Regex):
  ```php
  preg_match('/<iae:ReceiptNumber>(.*?)<\/iae:ReceiptNumber>/', $responseBody, $matches);
  $receiptNumber = $matches[1] ?? null;
  ```
- Menyimpan log audit ke tabel `audit_logs` lokal dengan status `'success'` atau `'failed'`.

**Hasil:** Data mahasiswa baru berhasil dilaporkan ke server audit SOAP Cloud Dosen, dan Receipt Number resmi (`IAE-LOG-...`) berhasil disimpan di database MySQL lokal.

---

### 4. Implementasi Penyebaran Event Asinkron (AMQP Publisher via HTTP Proxy)
**Prompt:**
> Aku harus mengirim sinyal event mahasiswa baru ke RabbitMQ (POST /api/v1/messages/publish). Payload yang diminta oleh Cloud Dosen harus memiliki struktur:
> - routing_key: "mahasiswa.created"
> - message: di dalamnya ada field event ("mahasiswa.created"), timestamp (ISO 8601), team_id ("TEAM-09"), dan data (berisi data mahasiswa).
> Request ini harus diautentikasi dengan Bearer Token M2M. Tolong buatkan class RabbitMQService di Laravel agar rapi.

**Respons AI:**
Merancang class `RabbitMQService.php` dengan struktur payload yang tepat:
- Menentukan endpoint publikasi pesan: `https://iae-sso.virtualfri.id/api/v1/messages/publish`.
- Menyusun array berstruktur sesuai ketentuan:
  ```php
  $payload = [
      'routing_key' => $eventName,
      'message' => [
          'event' => $eventName,
          'timestamp' => now()->toISOString(),
          'team_id' => 'TEAM-09',
          'data' => $data,
      ],
  ];
  ```
- Mengirimkan request menggunakan HTTP Client Laravel dengan bearer token hasil otorisasi M2M.

**Hasil:** Event `mahasiswa.created` berhasil terpublikasi ke broker RabbitMQ terpusat sehingga dapat dikonsumsi oleh Service B dan Service C.

---

### 5. Penanganan Error & Sinkronisasi Alur Integrasi (Debugging)
**Prompt:**
> Aku menghadapi masalah di MahasiswaController saat menyatukan ketiganya. Jika pemanggilan M2M token gagal atau bernilai null, aplikasi akan crash saat mencoba mengirim SOAP/RabbitMQ. Bagaimana cara membuat penanganan error agar jika token tidak didapatkan dari API Key, aplikasi melakukan fallback ke token JWT milik user SSO yang sedang login? Dan bagaimana cara mengembalikan response gabungan yang informatif ke client?

**Respons AI:**
Memberikan rekomendasi perbaikan kode kontrol alur di `MahasiswaController@store`:
- Menambahkan logika fallback pencarian token: jika token M2M gagal diambil secara dinamis, aplikasi akan mencari token dari user SSO terakhir yang tercatat di database lokal (`SsoUser::latest()->first()`).
- Menambahkan struktur pengecekan `if ($token)` sebelum menjalankan service SOAP dan RabbitMQ agar aplikasi tidak memicu *fatal error* (null pointer).
- Memodifikasi format JSON response terakhir agar menampilkan data mahasiswa lokal, `receipt_number` dari SOAP, dan status pengiriman ke RabbitMQ (`rabbit_status`).

**Hasil:** Alur integrasi menjadi tangguh (robust), bebas dari crash jika koneksi luar terganggu, dan menghasilkan feedback JSON yang lengkap ke client.

---

### 6. Ekspos Rute Autentikasi di API Gateway (Nginx)
**Prompt:**
> Saat ini gateway Nginx hanya meneruskan /api/v1/mahasiswa ke Service A. Tapi aku punya endpoint login dan profile di /api/v1/auth/login dan /api/v1/auth/profile. Karena port Service A ga di-expose ke luar Docker secara langsung, bagaimana cara menambahkannya ke gateway Nginx?

**Respons AI:**
Membantu menambahkan konfigurasi lokasi `/api/v1/auth` pada `gateway/nginx.conf` agar merutekan request tersebut secara tepat ke container `mahasiswa-service` di port 8000.

**Hasil:** Client luar (Postman/Frontend) sekarang dapat melakukan login SSO secara langsung melalui port 8080 API Gateway.

---

### 7. Penanganan Fallback Agregasi Nilai 404
**Prompt:**
> Pada endpoint agregasi /api/v1/mahasiswa/{nim}/matkul, jika mahasiswa itu mahasiswa baru semester 1 dan belum punya nilai di database Service C, maka Service C me-return 404. Ini bikin agregasi matkul di Service A langsung ikutan error 404 dan ga bisa nampilin KRS aktif. Bagaimana cara membuat penanganan fallback agar jika nilai 404, request tetap berhasil tapi data nilai dibuat kosong?

**Respons AI:**
Mengarahkan modifikasi logika pada `MahasiswaController@matkul` agar mengecek `if ($nilaiResult['status'] === 404)`. Jika ya, buat array data nilai default (`total_sks = 0`, `ips = 0.0`, `nilai = []`) sehingga alur request KRS aktif tetap berjalan lancar.

**Hasil:** Mahasiswa baru sekarang dapat memanggil endpoint agregasi dengan status 200 tanpa terganggu error 404 dari Service C.

---

### 8. Peningkatan Performa dengan Parallel API Composition (Concurrency)
**Prompt:**
> Saat ini di MahasiswaController@matkul, pemanggilan API ke Service B (KRS) dan Service C (Nilai) dilakukan secara berurutan (sekuensial). Ini bikin total waktu respon jadi lama (penjumlahan dari kedua request). Apakah bisa dilakukan secara bersamaan (paralel) menggunakan Laravel HTTP Client?

**Respons AI:**
Menyarankan dan membimbing penggunaan fitur `Http::pool` (HTTP request concurrency) di Laravel. Menulis ulang `ExternalAcademicService` dengan metode `fetchAcademicSummary` yang menginisialisasi request ke Service B dan Service C secara paralel.

**Hasil:** Waktu respon endpoint agregasi berkurang drastis karena total delay hanya mengikuti waktu eksekusi request yang paling lambat (bukan penjumlahan keduanya).

---

## ✅ Kesimpulan Eksplorasi Bersama AI
Pemanfaatan AI membantu mempercepat implementasi Tugas 3 melalui:
1. **Penyusunan Raw SOAP Request:** Mempermudah pembuatan amplop XML (SOAP Envelope) secara manual tanpa ketergantungan pada modul SOAP PHP native yang sering bermasalah di Docker.
2. **Parallel API Composition:** Menggunakan `Http::pool` untuk menembak beberapa microservice eksternal secara asinkron/bersamaan guna memangkas latency response time.
3. **Pencegahan Error & Gateway Routing:** Menyediakan mekanisme fallback penanganan error HTTP 404 dari service lain dan merancang integrasi gateway routing hub yang solid untuk autentikasi SSO.
