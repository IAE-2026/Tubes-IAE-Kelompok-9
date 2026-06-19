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

### 1. Collection (Mengambil Daftar Data)
**Endpoint:** `GET /api/v1/nilai`  
**Deskripsi:** Mengambil seluruh daftar nilai mahasiswa yang tersimpan di database.

**Sample Response:**
```json
{
  "status": "success",
  "message": "Data nilai berhasil diambil",
  "data": [
    {
      "id": 1,
      "nim": "102022400136",
      "kode_matkul": "SI101",
      "nama_matkul": "Algoritma dan Pemrograman",
      "nilai_huruf": "A",
      "nilai_angka": "4.00",
      "sks": 3,
      "semester": 1,
      "tahun_ajaran": "2024/2025",
      "created_at": "2026-05-14T18:37:11.000000Z",
      "updated_at": "2026-05-14T18:37:11.000000Z"
    },
    {
      "id": 8,
      "nim": "102022580023",
      "kode_matkul": "SI101",
      "nama_matkul": "Algoritma dan Pemrograman",
      "nilai_huruf": "AB",
      "nilai_angka": "3.50",
      "sks": 3,
      "semester": 1,
      "tahun_ajaran": "2024/2025",
      "created_at": "2026-05-14T18:47:22.000000Z",
      "updated_at": "2026-05-14T18:47:22.000000Z"
    }
  ],
  "meta": {
    "service_name": "Prasyarat-Kurikulum-Service",
    "api_version": "v1",
    "total": 10
  }
}
```

---

### 2. Resource (Mengambil Data Spesifik)
**Endpoint:** `GET /api/v1/nilai/102022580023`  
**Deskripsi:** Mengambil detail nilai dan perhitungan IPS mahasiswa tertentu.

**Sample Response:**
```json
{
  "status": "success",
  "message": "Data nilai untuk NIM 102022580023 ditemukan",
  "data": {
    "nim": "102022580023",
    "ips": 3.65,
    "total_sks": 20,
    "data_nilai": [
      {
        "id": 8,
        "kode_matkul": "SI101",
        "nama_matkul": "Algoritma dan Pemrograman",
        "nilai_huruf": "AB",
        "nilai_angka": 3.5,
        "sks": 3,
        "created_at": "2026-05-14T18:47:22.000000Z"
      }
    ]
  },
  "meta": {
    "service_name": "Prasyarat-Kurikulum-Service",
    "api_version": "v1"
  }
}
```

---

### 3. Action (Menambah Data / Memicu Proses)
**Endpoint:** `POST /api/v1/nilai`  
**Body (JSON):**
```json
{
  "nim": "102022580023",
  "kode_matkul": "SI301",
  "nama_matkul": "Basis Data",
  "nilai_huruf": "A",
  "nilai_angka": 4,
  "sks": 3,
  "semester": 3,
  "tahun_ajaran": "2025/2026"
}
```

**Sample Response:**
```json
{
  "status": "success",
  "message": "Data nilai berhasil dicatat",
  "data": {
    "id": 11,
    "nim": "102022580023",
    "kode_matkul": "SI301",
    "created_at": "2026-05-15T02:30:00.000000Z"
  }
}
```

---

## 🔗 GraphQL Showcase
Gunakan GraphiQL IDE di `/graphiql` untuk melakukan query yang lebih fleksibel.

**Query:**
```graphql
query {
  kurikulum(kode_matkul: "SI201") {
    nama_matkul
    sks
    semester
    prasyarat
  }
}
```

---

*Dibuat untuk memenuhi tugas mata kuliah Integrasi Aplikasi Enterprise.*
