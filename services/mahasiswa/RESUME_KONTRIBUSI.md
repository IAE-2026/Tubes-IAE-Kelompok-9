# Resume Kontribusi Individu — Tugas Besar IAE

**Nama:** Arneta Alifiana  
**NIM:** 102022400136  
**Service:** Mahasiswa-Service (Service A)  
**Kelompok:** 9 (TEAM-09)  

Berikut adalah ringkasan kontribusi nyata yang saya lakukan pada repositori bersama kelompok (`Tubes-IAE-Kelompok-9`) berdasarkan log commit git:

## 1. Daftar Commit & Kontribusi Kode

| Commit Hash | Judul Commit | Deskripsi Kontribusi |
| :--- | :--- | :--- |
| `f657626` | `perf: hindari hashing bcrypt berulang di middleware VerifyJwtSso...` | Mengoptimalkan middleware JWT SSO di Service C agar tidak menjalankan `bcrypt` pada setiap request API, yang mempercepat respons API kelompok sebesar 100ms - 200ms. |
| `2d053b6` | `chore: selaraskan default Team ID dari TIM-09 menjadi TEAM-09...` | Menyelaraskan ID Tim kelompok dari `TIM-09` menjadi `TEAM-09` di berkas konfigurasi default dan pengujian unit test Service C demi konsistensi log terpusat. |
| `075b7c8` | `fix: tangani error 404 dari Service C (nilai) secara anggun...` | Menambahkan penanganan fallback anggun untuk mahasiswa baru (semester 1) yang belum memiliki riwayat nilai di database agar endpoint agregasi matkul di Service A tidak crash/kembali 404. |
| `73ce677` | `Menambahkan blok routing baru untuk /api/v1/auth yang mengarah ke...` | Mengintegrasikan endpoint autentikasi SSO lokal (`/api/v1/auth`) pada Nginx API Gateway kelompok dan menyelaraskan penulisan `IAE_TEAM_ID` di `docker-compose.yml`. |
| `91cd3c5` | `docs: update README and add AI prompting log` | Menyusun dokumentasi awal `README.md` Service A dan merekap log prompting bantuan kecerdasan buatan (AI) untuk akuntabilitas pengerjaan. |
| `51fd463` | `feat: initial Service A - Data Mahasiswa Service` | Menginisialisasi awal microservice Data Mahasiswa berbasis Laravel (REST API `/api/v1/mahasiswa`, GraphQL `/graphql`, database migration, model `Mahasiswa`, database seeder, dan `Dockerfile`). |

---

## 2. Rincian Pekerjaan & Fitur Utama yang Dikerjakan

1. **Setup & Inisialisasi Microservice (Service A):**
   * Mengatur struktur awal Laravel, database migration untuk tabel `mahasiswas`, model `Mahasiswa`, dan seeding data mahasiswa contoh (seperti NIM Anda dan NIM Andi).
   * Membangun REST API (`GET /api/v1/mahasiswa`, `GET /api/v1/mahasiswa/{nim}`, `POST /api/v1/mahasiswa`) dan integrasi GraphQL via Lighthouse.
   * Membangun Dockerfile mandiri untuk Service A.

2. **Keamanan & Autentikasi:**
   * Mengimplementasikan `ApiKeyMiddleware` untuk melindungi endpoint internal Service A menggunakan header `X-API-KEY` atau `X-IAE-KEY`.
   * Membangun controller autentikasi SSO (`SsoController`) untuk melakukan penanganan login federasi warga ke SSO terpusat (`https://iae-sso.virtualfri.id`) dan menyimpan data profile warga secara lokal dengan mapping role.

3. **Kepatuhan Infrastruktur Pusat & Integrasi E2E:**
   * Mengimplementasikan SOAP Audit logging (`SoapAuditService`) untuk melaporkan transaksi registrasi mahasiswa baru secara rigid menggunakan envelope XML ke server SOAP terpusat.
   * Mengimplementasikan RabbitMQ event publisher (`RabbitMQService`) untuk mempublikasikan event `mahasiswa.created` ke broker RabbitMQ terpusat.
   * Membangun endpoint agregasi data `GET /api/v1/mahasiswa/{nim}/matkul` yang melakukan API Composition secara internal dengan memanggil Service B (KRS) dan Service C (Nilai).

4. **Optimalisasi & Penyempurnaan Bersama:**
   * Melakukan routing-enabling pada Nginx Gateway untuk endpoint login (`/api/v1/auth`).
   * Menyelaraskan ID Tim kelompok (`TEAM-09`) di seluruh file konfigurasi dan test project.
   * Mengoptimalkan performa middleware JWT SSO agar terhindar dari enkripsi berulang `bcrypt` di setiap request API.
