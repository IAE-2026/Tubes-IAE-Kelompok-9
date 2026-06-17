# 🤖 AI Prompting Log
**Service A - Data Mahasiswa Service**  
**BBK2HAB3 - Integrasi Aplikasi Enterprise**  
**Nama:** Arneta Alifiana  
**NIM:** 102022400136  
**Kelompok:** Group 9 (TEAM-09)  

---

## 📌 Pendahuluan & Tujuan Penggunaan AI
Dokumen ini mencatat proses diskusi kolaboratif (peer programming dengan porsi kontribusi 50:50) antara mahasiswa (Arneta) dengan kecerdasan buatan (AI) selama fase perencanaan, setup, implementasi, dan pengujian **Service A (Data Mahasiswa)** dari awal hingga siap diintegrasikan.

Diskusi difokuskan pada perancangan arsitektur microservice berbasis Laravel 12, skema database, pengamanan dengan API Key, dockerisasi, pemecahan masalah (debugging), hingga integrasi GraphQL dan dokumentasi API otomatis menggunakan Swagger UI.

---

## 📝 Log Diskusi & Prompting Teknis (50:50 Collaborative Style)

### 1. Diskusi Perencanaan Awal Project
*   **Mahasiswa (Arneta):**
    > Aku ingin membuat Service A (Data Mahasiswa) untuk tugas besar Integrasi Aplikasi Enterprise menggunakan Laravel 12, MySQL 8, dan Docker. Rencananya tabel database `mahasiswas` akan menyimpan data NIM, Nama, Email, Prodi, Angkatan, dan Status. Rute API yang kubutuhkan adalah melihat semua mahasiswa, melihat detail mahasiswa, dan menambah mahasiswa baru. Bagaimana arsitektur awal dan implementasi yang harus kita lakukan?
*   **AI (Assistant):**
    > Ini rancangan arsitektur yang sangat bagus untuk microservice! Kita bisa membaginya ke dalam 5 langkah penting: inisialisasi Laravel, setup migration database, pembuatan controller REST API, pengamanan API Key, dan Dockerisasi menggunakan docker-compose. Mari kita mulai dari instalasi awal Laravel 12.
*   **Hasil:** Mendapatkan roadmap pengerjaan yang jelas dan terstruktur untuk diimplementasikan secara bertahap.

---

### 2. Diskusi Setup Migration Database
*   **Mahasiswa (Arneta):**
    > Untuk database migration tabel `mahasiswas`, aku ingin menetapkan field NIM dan Email bersifat unik (unique) serta menambahkan kolom status berupa enum ('aktif', 'cuti', 'lulus', 'do') dengan nilai default 'aktif'. Bagaimana penulisan file migration-nya di Laravel?
*   **AI (Assistant):**
    > Tentu, mari kita susun file migration-nya dengan batasan skema unik yang Anda inginkan. Kita bisa menggunakan `Blueprint` Laravel untuk mendefinisikan kolom `$table->string('nim', 20)->unique()` dan `$table->enum('status', ['aktif', 'cuti', 'lulus', 'do'])->default('aktif')`. Ini akan memastikan integritas data mahasiswa terjaga secara ketat di MySQL.
*   **Hasil:** Migration berhasil dibuat dan dapat dijalankan di database MySQL kontainer secara sukses.

---

### 3. Diskusi Membuat API Key Middleware
*   **Mahasiswa (Arneta):**
    > Semua endpoint REST API di Service A harus dilindungi menggunakan otorisasi API Key. Aku ingin middleware-nya memeriksa header `X-API-KEY` atau query parameter `api_key` secara dinamis dan membandingkannya dengan nilai di config. Bagaimana implementasi middleware-nya di Laravel 12?
*   **AI (Assistant):**
    > Ide pengamanan endpoint ini sangat penting untuk mencegah akses liar. Kita bisa membuat `ApiKeyMiddleware.php` yang membaca config `app.api_key`. Middleware akan mengambil header dengan `$request->header('X-API-KEY')` atau query parameter `$request->query('api_key')` lalu membandingkannya menggunakan fungsi `hash_equals` untuk mencegah timing-attack.
*   **Hasil:** Middleware berhasil berjalan, endpoint terlindungi dengan API Key.

---

### 4. Diskusi Membuat REST API Controller
*   **Mahasiswa (Arneta):**
    > Aku ingin membuat `MahasiswaController` untuk mengimplementasikan fungsionalitas CRUD data mahasiswa. Aku juga ingin mendokumentasikan setiap endpoint REST API ini menggunakan Swagger (OpenAPI) agar service lain dapat membaca dokumentasinya dengan mudah. Bagaimana kerangka controller dan dokumentasi annotasinya?
*   **AI (Assistant):**
    > Pilihan mendokumentasikan API menggunakan Swagger sangat baik untuk interoperabilitas service. Kita bisa menulis controller lengkap dengan anotasi OpenAPI Attributes (`#[OA\Get]`, `#[OA\Post]`, dan `#[OA\Response]`) langsung di atas metode controller Anda. Kita juga perlu menambahkan definisi keamanan `securityScheme` untuk `ApiKeyAuth`.
*   **Hasil:** Controller berhasil dibuat dengan 3 endpoint fungsional beserta anotasi dokumentasi Swagger yang lengkap.

---

### 5. Diskusi Troubleshooting Routes API
*   **Mahasiswa (Arneta):**
    > Aku menyadari di folder `routes` project Laravel 12-ku tidak ada file `api.php` secara default. Apakah ada cara untuk mengaktifkan routing API secara otomatis di versi Laravel terbaru ini?
*   **AI (Assistant):**
    > Betul sekali. Mulai Laravel 11/12, file `api.php` tidak disertakan secara default untuk merampingkan struktur awal. Anda dapat memunculkannya secara otomatis dengan menjalankan satu perintah saja di terminal: `php artisan install:api`. Perintah ini akan membuat berkas rute API baru dan mendaftarkannya pada konfigurasi bootstrap aplikasi.
*   **Hasil:** File `routes/api.php` berhasil dibuat dan rute API siap didaftarkan.

---

### 6. Diskusi Setup Docker
*   **Mahasiswa (Arneta):**
    > Aku ingin menguji aplikasi di dalam container Docker. Rencananya kita menggunakan multi-container: satu untuk web server Nginx + PHP-FPM, dan satu lagi untuk MySQL 8. Bagaimana konfigurasi `Dockerfile` dan `docker-compose.yml` agar kedua container ini saling terhubung?
*   **AI (Assistant):**
    > Mari kita konfigurasikan lingkungan Docker Anda. Kita akan menggunakan port `8000` untuk Nginx internal dan database port `3306` pada database MySQL. Di `docker-compose.yml`, kita kaitkan service `mahasiswa-service` dan `mahasiswa-db` ke dalam satu jaringan yang sama (`iae-network` / `education-network`) agar keduanya dapat saling berkomunikasi via nama container.
*   **Hasil:** Docker berhasil berjalan dengan app di port 8001 (atau 8000) dan MySQL berjalan dengan lancar.

---

### 7. Diskusi Troubleshooting Docker - Connection Refused
*   **Mahasiswa (Arneta):**
    > Saat menjalankan container Docker, aku mendapati error `Connection refused` dari MySQL database. Tampaknya container aplikasi Laravel menyala lebih cepat sebelum container MySQL siap menerima koneksi. Bagaimana kita mengatasi masalah sinkronisasi startup ini?
*   **AI (Assistant):**
    > Masalah startup race condition ini sangat umum di Docker. Kita bisa menambahkan penundaan waktu tunggu (`sleep 10`) pada perintah startup aplikasi di `Dockerfile` sebelum menjalankan perintah migrasi, atau menggunakan healthcheck pada database MySQL di file `docker-compose.yml` dengan syarat `condition: service_healthy` pada kontainer aplikasi.
*   **Hasil:** Docker berhasil berjalan dengan stabil setelah kontainer MySQL siap menerima koneksi.

---

### 8. Diskusi Troubleshooting Docker - Duplicate Migration
*   **Mahasiswa (Arneta):**
    > Saat menjalankan migrasi database di container, muncul error `Table 'mahasiswas' already exists`. Aku curiga ada file migrasi yang terduplikat secara tidak sengaja di folder migrations. Bagaimana cara membersihkan dan meresetnya secara aman?
*   **AI (Assistant):**
    > Analisis Anda sangat tepat. Jika muncul error tersebut, periksa folder `database/migrations` dan hapus salah satu file migrasi yang terduplikat. Setelah itu, jalankan perintah `php artisan migrate:fresh` untuk menghapus semua tabel lama dan mengulang kembali migrasi dari awal secara bersih.
*   **Hasil:** Migrasi database berhasil berjalan tanpa memicu error duplikasi tabel lagi.

---

### 9. Diskusi Setup Swagger UI
*   **Mahasiswa (Arneta):**
    > Aku mengalami error saat merender dokumentasi Swagger: `Required @OA\Info() not found`. Aku sudah menaruh atribut Swagger di controller. Apakah ada file konfigurasi l5-swagger yang terlewat, atau ada cara instalasi manual?
*   **AI (Assistant):**
    > Error ini biasanya terjadi jika file konfigurasi L5 Swagger belum memindai direktori controller dengan benar. Pastikan Anda telah menaruh atribut global `#[OA\Info(title: "Service A - Mahasiswa API", version: "1.0.0")]` di atas deklarasi class `MahasiswaController`. Jika masih error, jalankan perintah `php artisan l5-swagger:generate` secara manual untuk memperbarui file JSON/YAML hasil pemindaian.
*   **Hasil:** Dokumentasi API Swagger UI berhasil diakses di rute `/api/documentation`.

---

### 10. Diskusi Setup GraphQL
*   **Mahasiswa (Arneta):**
    > Aku ingin menambahkan antarmuka GraphQL di Service A selain REST API konvensional agar data mahasiswa bisa di-query secara fleksibel. Rencananya aku memakai library Lighthouse dan GraphQL Playground untuk testing. Bagaimana kita mendefinisikan schema GraphQL-nya?
*   **AI (Assistant):**
    > Ini tambahan fitur yang luar biasa! GraphQL akan mempermudah service lain melakukan field-filtering. Kita dapat menggunakan Lighthouse untuk mendefinisikan skema GraphQL di file `graphql/schema.graphql`. Kita buat tipe data `Mahasiswa` dengan field: id, nim, nama, email, prodi, angkatan, status. Selanjutnya kita definisikan query `mahasiswa: [Mahasiswa!]! @all` dan `mahasiswaByNim(nim: String! @eq): Mahasiswa @find` menggunakan directive bawaan Lighthouse.
*   **Hasil:** GraphQL Playground berhasil diakses secara lokal dan query data mahasiswa dapat dijalankan dengan sukses.

---

## ✅ Kesimpulan Diskusi Bersama AI
Proses peer programming kolaboratif (50:50) mempercepat siklus development Service A:
1. **Kejelasan Roadmap:** Menghindari pengerjaan acak-acakan berkat pembagian tugas bertahap.
2. **Pemecahan Masalah Cepat:** Masalah seputar Docker startup, duplikasi migration, dan konfigurasi rute Laravel 12 berhasil diselesaikan secara efisien.
3. **Peningkatan Fitur:** Penambahan dukungan format dokumentasi API (Swagger) dan alternatif query data (GraphQL) berjalan dengan lancar.