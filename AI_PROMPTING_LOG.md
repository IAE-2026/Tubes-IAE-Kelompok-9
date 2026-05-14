# 🤖 AI Prompting Log
**Service A - Data Mahasiswa Service**  
BBK2HAB3 - Integrasi Aplikasi Enterprise  
**Nama:** Arneta Alifiana  
**Tool AI:** Claude AI (claude.ai)  
**Tanggal:** 14 Mei 2026

---

## 📌 Tujuan Penggunaan AI
Membantu perencanaan, implementasi, dan debugging Service A (Data Mahasiswa) dalam proyek Integrasi Aplikasi Enterprise berbasis microservice.

---

## 📝 Log Prompting

### 1. Perencanaan Awal Project
**Prompt:**
> Aku mau mengerjakan tugas ini dengan contract ini... tolong tutorialin aku harus gimana mulai dari harus buka aplikasi apa

**Respons AI:**
Memberikan full implementation plan mencakup:
- Arsitektur service (Laravel + MySQL + Docker)
- Database schema tabel mahasiswas
- Daftar endpoint REST API
- Rencana setup Docker
- Checklist implementasi lengkap

**Hasil:** Mendapatkan roadmap pengerjaan yang jelas dan terstruktur

---

### 2. Setup Migration Database
**Prompt:**
> Buat migration untuk tabel mahasiswas

**Respons AI:**
Memberikan kode migration lengkap dengan field: id, nim, nama, email, prodi, angkatan, status, timestamps

**Hasil:** Migration berhasil dibuat dan dijalankan di Docker

---

### 3. Membuat API Key Middleware
**Prompt:**
> Buat middleware untuk autentikasi menggunakan API Key di request header X-API-KEY

**Respons AI:**
Memberikan kode `ApiKeyMiddleware.php` dan cara mendaftarkannya di `bootstrap/app.php` dan `config/app.php`

**Hasil:** Middleware berhasil berjalan, endpoint terlindungi dengan API Key

---

### 4. Membuat REST API Controller
**Prompt:**
> Buat MahasiswaController dengan 3 endpoint beserta Swagger documentation

**Respons AI:**
Memberikan kode controller lengkap dengan anotasi Swagger `@OA\Info`, `@OA\Get`, `@OA\Post` dan response format JSON yang konsisten

**Hasil:** Controller berhasil dibuat dengan 3 endpoint fungsional

---

### 5. Troubleshooting Routes API
**Prompt:**
> Di routes aku kok ga ada api.php

**Respons AI:**
Menjelaskan bahwa Laravel 11/12 tidak lagi menyertakan `api.php` secara default, perlu menjalankan `php artisan install:api`

**Hasil:** File `routes/api.php` berhasil dibuat

---

### 6. Setup Docker
**Prompt:**
> Buat Dockerfile dan docker-compose.yml untuk menjalankan Laravel + MySQL

**Respons AI:**
Memberikan kode `Dockerfile`, `docker-compose.yml`, dan `nginx.conf` lengkap

**Hasil:** Docker berhasil berjalan dengan app di port 8001 dan MySQL di port 3307

---

### 7. Troubleshooting Docker - Connection Refused
**Prompt:**
> SQLSTATE[HY000] [2002] Connection refused

**Respons AI:**
Menjelaskan bahwa app container nyala sebelum MySQL siap, solusinya tambahkan `sleep 10` di CMD Dockerfile

**Hasil:** Docker berhasil jalan setelah MySQL ready

---

### 8. Troubleshooting Docker - Duplicate Migration
**Prompt:**
> SQLSTATE[42S01]: Base table or view already exists: Table 'mahasiswas' already exists

**Respons AI:**
Menjelaskan ada 2 file migration duplikat, solusinya hapus salah satu dan gunakan `migrate:fresh`

**Hasil:** Migration berhasil berjalan tanpa error

---

### 9. Setup Swagger UI
**Prompt:**
> Swagger error: Required @OA\Info() not found

**Respons AI:**
Membantu membuat file `config/l5-swagger.php` manual dan mengubah anotasi ke PHP Attributes, serta cara copy file ke container dengan `docker cp`

**Hasil:** Swagger UI berhasil diakses di `/api/documentation`

---

### 10. Setup GraphQL
**Prompt:**
> Setup GraphQL Playground untuk query data mahasiswa

**Respons AI:**
Membantu install Lighthouse dan mll-lab/laravel-graphql-playground langsung di container, serta copy schema.graphql ke container

**Hasil:** GraphQL Playground berhasil diakses dan query data mahasiswa berhasil dijalankan

---

## ✅ Kesimpulan
AI sangat membantu dalam:
- Mempercepat setup project dari nol
- Debugging error Docker dan Laravel
- Memahami konsep microservice dan API integration
- Menulis kode boilerplate yang efisien

Total sesi prompting: **10+ interaksi**