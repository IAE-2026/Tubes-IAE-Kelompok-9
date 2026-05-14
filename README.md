# Service C - Validasi Prasyarat & Kurikulum 🎓
**Integrasi Aplikasi Enterprise (IAE) - Tugas Besar**

[![Laravel 12](https://img.shields.io/badge/Laravel-12.0-red?style=flat-square&logo=laravel)](https://laravel.com)
[![MySQL](https://img.shields.io/badge/Database-MySQL-blue?style=flat-square&logo=mysql)](https://mysql.com)
[![Docker](https://img.shields.io/badge/Docker-Enabled-blue?style=flat-square&logo=docker)](https://www.docker.com/)
[![GraphQL](https://img.shields.io/badge/GraphQL-Lighthouse-e10098?style=flat-square&logo=graphql)](https://lighthouse-php.com/)

---

## 🧑‍🎓 Identitas Mahasiswa
- **Nama:** Andi Muh. Arif Darma Saputra M
- **NIM:** 102022580023
- **Kelas:** Integrasi Aplikasi Enterprise
- **Layanan:** Service C (Kurikulum & Nilai Mahasiswa)

---

## 🚀 Gambaran Proyek
Service C adalah komponen dari ekosistem IAE yang bertanggung jawab untuk mengelola data kurikulum program studi dan validasi nilai mahasiswa. Layanan ini dibangun dengan arsitektur modern yang mendukung REST API dan GraphQL secara simultan.

### Fitur Utama:
- **RESTful API**: Dokumentasi lengkap menggunakan Swagger (OpenAPI).
- **GraphQL API**: Query data yang fleksibel menggunakan Lighthouse.
- **Security**: Proteksi middleware menggunakan header `X-IAE-KEY`.
- **Integrasi**: Validasi silang ke Service A untuk pengecekan status mahasiswa aktif.
- **Containerization**: Berjalan sepenuhnya di Docker menggunakan Laravel Sail.

---

## 🛠️ Stack Teknologi
- **Framework**: Laravel 12.0 (PHP 8.5)
- **Database**: MySQL 8.4
- **API Documentation**: L5-Swagger
- **GraphQL Interface**: Lighthouse GraphQL + GraphiQL IDE
- **Dockerization**: Laravel Sail

---

## 📦 Instalasi & Cara Menjalankan
Pastikan **Docker Desktop** sudah berjalan di komputer Anda, lalu ikuti langkah berikut:

1. **Clone & Masuk ke Direktori Proyek**
2. **Copy Konfigurasi Environment**
   ```bash
   cp .env.example .env
   ```
3. **Nyalakan Docker Container**
   ```bash
   ./vendor/bin/sail up -d --build
   ```
4. **Instalasi Dependencies & Generate Key**
   ```bash
   ./vendor/bin/sail composer install
   ./vendor/bin/sail artisan key:generate
   ```
5. **Migrasi Database & Seeding Data**
   ```bash
   ./vendor/bin/sail artisan migrate:fresh --seed
   ```

---

## 🔗 Endpoint & Dokumentasi

### 1. REST API (Swagger)
Akses dokumentasi interaktif untuk mencoba semua endpoint:
> **URL:** [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

### 2. GraphQL (Playground)
Gunakan GraphiQL untuk melakukan query data kurikulum dan nilai:
> **URL:** [http://localhost:8000/graphiql](http://localhost:8000/graphiql)

### 3. Keamanan (X-IAE-KEY)
Setiap request ke API wajib menyertakan header keamanan berikut:
- **Header Key**: `X-IAE-KEY`
- **Value**: `102022580023` (NIM Anda)

---

## 🧪 Contoh Query GraphQL
```graphql
{
  nilaiByNim(nim: "102022580023") {
    nim
    ips
    data {
      nama_matkul
      nilai_huruf
    }
  }
}
```

---

## 🧪 Contoh Request & Response

### 1. REST API: Lihat Nilai (GET)
**Endpoint:** `/api/v1/nilai/102022580023`  
**Header:** `X-IAE-KEY: 102022580023`

**Sample Response:**
```json
{
  "status": "success",
  "message": "Data nilai ditemukan",
  "data": {
    "nim": "102022580023",
    "ips": 3.75,
    "total_sks": 20,
    "data_nilai": [
      {
        "kode_matkul": "SI101",
        "nama_matkul": "Pengantar Sistem Informasi",
        "nilai_huruf": "A",
        "nilai_angka": 4,
        "sks": 3
      },
      {
        "kode_matkul": "SI102",
        "nama_matkul": "Algoritma Pemrograman",
        "nilai_huruf": "B+",
        "nilai_angka": 3.5,
        "sks": 4
      }
    ]
  }
}
```

### 2. GraphQL: Query Detail Kurikulum
**Query:**
```graphql
query {
  kurikulum(kode_matkul: "SI201") {
    nama_matkul
    sks
    semester
    prasyarat
    deskripsi
  }
}
```

**Sample Response:**
```json
{
  "data": {
    "kurikulum": {
      "nama_matkul": "Struktur Data",
      "sks": 3,
      "semester": 3,
      "prasyarat": "Algoritma Pemrograman",
      "deskripsi": "Mempelajari struktur penyimpanan data efisien."
    }
  }
}
```

---

*Dibuat untuk memenuhi tugas mata kuliah Integrasi Aplikasi Enterprise.*
