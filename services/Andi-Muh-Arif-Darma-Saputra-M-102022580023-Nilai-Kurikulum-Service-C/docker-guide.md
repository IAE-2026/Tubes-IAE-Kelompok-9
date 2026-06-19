# Panduan Docker Laravel Sail (MySQL Edition)

Proyek ini telah dikonfigurasi untuk berjalan di Docker menggunakan **Laravel Sail** dengan database **MySQL**.

## Persiapan
Pastikan **Docker Desktop** sudah berjalan.

## Cara Menjalankan
1. **Jalankan Container:**
   ```bash
   ./vendor/bin/sail up -d
   ```
   *Catatan: Pertama kali dijalankan akan memakan waktu untuk mengunduh image MySQL.*

2. **Akses Aplikasi:**
   Buka browser dan akses [http://localhost:8000](http://localhost:8000).

3. **Migrasi Database:**
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

## Integrasi MySQL
- Database: `laravel`
- User: `sail`
- Password: `password`
- Host (di dalam Docker): `mysql`
- Host (dari luar Docker/XAMPP): `127.0.0.1` port `3306`

## Perintah Umum
- **Berhenti:** `./vendor/bin/sail stop`
- **Hapus Container:** `./vendor/bin/sail down`
- **Artisan:** `./vendor/bin/sail artisan <command>`
