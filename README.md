# 🎓 Service A - Data Mahasiswa Service
**BBK2HAB3 - Integrasi Aplikasi Enterprise**  
Telkom University | Fakultas Rekayasa Industri

---

## 📋 Deskripsi
Service A adalah microservice yang bertanggung jawab mengelola **data mahasiswa** dalam ekosistem Education System. Service ini menyediakan data mahasiswa untuk dikonsumsi oleh service lain:
- **Service B (KRS)** → memanggil `GET /api/v1/mahasiswa/{nim}` untuk validasi mahasiswa aktif sebelum menyimpan KRS
- **Service C (Nilai)** → memanggil `GET /api/v1/mahasiswa/{nim}` untuk validasi mahasiswa masih aktif sebelum mencatat nilai

---

## 🛠️ Tech Stack
| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| Laravel | 12 | PHP Framework |
| MySQL | 8.0 | Database |
| Docker | - | Containerization |
| L5-Swagger | - | API Documentation |
| Lighthouse | 6.x | GraphQL Server |
| Nginx | - | Web Server |

---

## 🚀 Cara Menjalankan

### Prerequisites
- Docker Desktop (sudah Running)
- Git

### 1. Clone Repository
```bash
git clone <url-repo>
cd Data-Mahasiswa-Service
```

### 2. Setup Environment
```bash
cp .env.example .env
```

### 3. Jalankan Docker
```bash
docker compose up --build
```

### 4. Akses Aplikasi
| URL | Keterangan |
|-----|------------|
| http://localhost:8001/api/v1/mahasiswa | REST API |
| http://localhost:8001/api/documentation | Swagger UI |
| http://localhost:8001/graphql-playground | GraphQL Playground |

---

## 🔌 API Endpoints

### Authentication
Semua endpoint membutuhkan API Key di request header:
```
X-API-KEY: secret-api-key-mahasiswa-123
```

### Daftar Endpoint
| Method | Endpoint | Deskripsi | Status Code |
|--------|----------|-----------|-------------|
| GET | /api/v1/mahasiswa | Lihat semua mahasiswa | 200 |
| GET | /api/v1/mahasiswa/{nim} | Lihat detail mahasiswa by NIM | 200, 404 |
| POST | /api/v1/mahasiswa | Tambah mahasiswa baru | 201, 422 |

### Request Body (POST /api/v1/mahasiswa)
```json
{
  "nim": "102022400136",
  "nama": "Arneta Alifiana",
  "email": "arneta@student.telkomuniversity.ac.id",
  "prodi": "S1 Sistem Informasi",
  "angkatan": 2024,
  "status": "aktif"
}
```

### Standard Response Format
```json
{
  "success": true,
  "message": "Daftar mahasiswa berhasil diambil.",
  "data": [
    {
      "id": 1,
      "nim": "102022400136",
      "nama": "Arneta Alifiana",
      "email": "arneta@student.telkomuniversity.ac.id",
      "prodi": "S1 Sistem Informasi",
      "angkatan": 2024,
      "status": "aktif",
      "created_at": "2026-05-14T11:00:00.000000Z",
      "updated_at": "2026-05-14T11:00:00.000000Z"
    }
  ]
}
```

### Response Error
```json
{
  "success": false,
  "message": "Mahasiswa dengan NIM 123 tidak ditemukan.",
  "data": null
}
```

---

## 🗄️ Database Schema

### Tabel `mahasiswas`
| Field | Type | Keterangan |
|-------|------|------------|
| id | bigint PK | Auto increment |
| nim | varchar(20) UNIQUE | Nomor Induk Mahasiswa |
| nama | varchar(100) | Nama lengkap |
| email | varchar(100) UNIQUE | Email mahasiswa |
| prodi | varchar(100) | Program studi |
| angkatan | year | Tahun angkatan |
| status | enum | aktif, cuti, lulus, do |
| created_at | timestamp | - |
| updated_at | timestamp | - |

---

## 🔷 GraphQL

Akses playground di: `http://localhost:8001/graphql-playground`

### Query Semua Mahasiswa
```graphql
{
  mahasiswa {
    id
    nim
    nama
    email
    prodi
    angkatan
    status
  }
}
```

### Query Mahasiswa by NIM
```graphql
{
  mahasiswaByNim(nim: "102022400136") {
    id
    nim
    nama
    email
    prodi
    angkatan
    status
  }
}
```

---

## 🐳 Docker

### Services
| Service | Image | Port |
|---------|-------|------|
| app | php:8.2-fpm + nginx | 8001 |
| db | mysql:8.0 | 3307 |

### Network
Semua service berjalan di network `education-network` agar bisa berkomunikasi dengan Service B dan Service C.

---

## 🔐 Environment Variables
| Variable | Deskripsi | Default |
|----------|-----------|---------|
| APP_NAME | Nama aplikasi | Mahasiswa Service |
| DB_DATABASE | Nama database | database_data_mahasiswa |
| DB_USERNAME | Username database | root |
| DB_PASSWORD | Password database | secret |
| API_KEY | API Key untuk autentikasi | - |

---

## 👩‍💻 Author
**Arneta Alifiana**  
BBK2HAB3 - Integrasi Aplikasi Enterprise  
Telkom University