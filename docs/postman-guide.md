# Panduan Postman — TIM-09

**Base URL:** `http://127.0.0.1:8080`  
**Jangan** pakai `iae-sso.virtualfri.id` untuk API lokal mahasiswa/KRS/nilai.

Import manual: copy request di bawah ke Postman (tab **Headers** wajib diisi).

---

## 0. Cek gateway hidup

```http
GET http://127.0.0.1:8080/
```

Harus return JSON dengan daftar path service.

---

## 1. Service A — Arneta (Mahasiswa)

**Header:** `X-API-KEY: KEY-MHS-233`

### List mahasiswa
```http
GET http://127.0.0.1:8080/api/v1/mahasiswa
X-API-KEY: KEY-MHS-233
```

### Detail mahasiswa
```http
GET http://127.0.0.1:8080/api/v1/mahasiswa/102022400136
X-API-KEY: KEY-MHS-233
```

### Matkul yang diambil (agregasi B + C)
```http
GET http://127.0.0.1:8080/api/v1/mahasiswa/102022400136/matkul
X-API-KEY: KEY-MHS-233
```

Response gabungan: `krs_aktif` (Service B) + `matkul_bernilai` (Service C) + `ringkasan.ips`.

### Matkul dummy demo (setelah input nilai)
```http
GET http://127.0.0.1:8080/api/v1/mahasiswa/209907170001/matkul
X-API-KEY: KEY-MHS-233
```

Cek `data.matkul_bernilai` untuk lihat nilai SI101, dan `data.ringkasan.ips` untuk IPS mahasiswa.

### Daftar mahasiswa baru (SOAP + RabbitMQ)
```http
POST http://127.0.0.1:8080/api/v1/mahasiswa
X-API-KEY: KEY-MHS-233
Content-Type: application/json

{
  "nim": "209906191106",
  "nama": "Mahasiswa Baru Simulasi",
  "email": "maba.simulasi@iae.id",
  "prodi": "S1 Sistem Informasi",
  "angkatan": 2026,
  "status": "aktif"
}
```

Cek: `receipt_number` + `rabbit_status: "terkirim"` + board `mahasiswa.created`.

### Token M2M (via gateway)
```http
POST http://127.0.0.1:8080/api/v1/auth/token
Content-Type: application/json

{
  "api_key": "KEY-MHS-233",
  "nim": "102022400136"
}
```

---

## 2. Service B — Jafar (KRS)

**Header:** `X-IAE-KEY: KEY-MHS-109`

### List KRS
```http
GET http://127.0.0.1:8080/api/v1/krs
X-IAE-KEY: KEY-MHS-109
```

### Submit KRS (1 matkul = 1 POST)
```http
POST http://127.0.0.1:8080/api/v1/krs
X-IAE-KEY: KEY-MHS-109
Content-Type: application/json

{
  "nim": "102022400136",
  "kode_mata_kuliah": "SI301",
  "tahun_ajaran": "2025/2026",
  "semester": "ganjil"
}
```

Ulangi POST untuk SI302, SI401 (3 matkul = 3 event `krs.created`).

### Approve KRS (JWT dosen)
```http
PUT http://127.0.0.1:8080/api/v1/krs/1/approve
X-IAE-KEY: KEY-MHS-109
Authorization: Bearer <JWT_dosen>
```

Login JWT dosen:
```http
POST https://iae-sso.virtualfri.id/api/v1/auth/token
Content-Type: application/json

{
  "email": "warga01@ktp.iae.id",
  "password": "KtpDigital2026!"
}
```

---

## 3. Service C — Andi (Nilai & Kurikulum)

**Header GET:** `X-IAE-KEY: KEY-MHS-117`  
**Header POST nilai:** `X-IAE-KEY: KEY-MHS-117` + `Authorization: Bearer <JWT_dosen>`

### List kurikulum
```http
GET http://127.0.0.1:8080/api/v1/kurikulum
X-IAE-KEY: KEY-MHS-117
```

### Nilai per NIM
```http
GET http://127.0.0.1:8080/api/v1/nilai/102022400136
X-IAE-KEY: KEY-MHS-117
```

### Input nilai (SOAP + RabbitMQ)

**Sebelum POST**, sync token M2M di laptop:
```bash
cd "services/102022580023-Andi Muh. Arif Darma Saputra M-Nilai & Kurikulum"
php artisan iae:sync-token
```

```http
POST http://127.0.0.1:8080/api/v1/nilai
X-IAE-KEY: KEY-MHS-117
Authorization: Bearer <JWT_dosen>
Content-Type: application/json

{
  "nim": "209907170001",
  "kode_matkul": "SI101",
  "nama_matkul": "Algoritma dan Pemrograman",
  "nilai_huruf": "A",
  "nilai_angka": 4,
  "sks": 3,
  "semester": 1,
  "tahun_ajaran": "2025/2026"
}
```

---

## 4. Simulasi demo lengkap (urutan)

| # | Request | Bukti sukses |
|---|---------|--------------|
| 1 | POST mahasiswa | receipt + `mahasiswa.created` |
| 2 | POST krs SI301 | receipt + `krs.created` |
| 3 | POST krs SI302 | receipt + `krs.created` |
| 4 | POST krs SI401 | receipt + `krs.created` |
| 5 | PUT approve krs | `krs.approved` |
| 6 | POST nilai | receipt + `nilai.recorded` |
| 7 | GET matkul | list gabungan KRS + nilai |

Board: https://iae-sso.virtualfri.id/board → filter **TEAM-09**

Routing detail: [../gateway/routing-map.md](../gateway/routing-map.md)
