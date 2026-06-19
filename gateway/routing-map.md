# Routing Map API Gateway — TIM-09

**Base URL (Postman):** `http://127.0.0.1:8080`

Semua request di bawah ini **harus** lewat gateway, bukan langsung ke port `8001`/`8002`/`8003`.

---

## Service A — Data Mahasiswa (Arneta)

**Folder:** `services/102022400136-Arneta Alifiana-Data Mahasiswa`  
**Header wajib:** `X-API-KEY: KEY-MHS-233`

| Method | Path gateway | Keterangan |
|--------|--------------|------------|
| GET | `/api/v1/mahasiswa` | Daftar mahasiswa |
| GET | `/api/v1/mahasiswa/{nim}` | Detail mahasiswa by NIM |
| GET | `/api/v1/mahasiswa/{nim}/matkul` | Agregasi KRS + nilai (Http::pool) |
| POST | `/api/v1/mahasiswa` | Daftar maba baru → **SOAP + RabbitMQ** |
| POST | `/api/v1/auth/login` | Login SSO (forward ke Service A) |
| POST | `/api/v1/auth/token` | Token M2M `{ api_key, nim }` — proxy ke Service A |

**Contoh M2M per service (ketentuan dosen):**

```http
# Service A — Arneta
POST http://127.0.0.1:8080/api/v1/auth/token
Content-Type: application/json

{"api_key":"KEY-MHS-233","nim":"102022400136"}
```

```http
# Service B — Jafar
POST http://127.0.0.1:8080/api/v1/auth/token
Content-Type: application/json

{"api_key":"KEY-MHS-109","nim":"102022400045"}
```

```http
# Service C — Andi
POST http://127.0.0.1:8080/api/v1/auth/token
Content-Type: application/json

{"api_key":"KEY-MHS-117","nim":"102022580023"}
```

Response sukses: `"token_type":"m2m"`, `"team":"TEAM-09"`.

**Contoh Postman — GET matkul:**

```http
GET http://127.0.0.1:8080/api/v1/mahasiswa/102022400136/matkul
X-API-KEY: KEY-MHS-233
```

**Contoh Postman — POST maba (trigger SOAP/RabbitMQ):**

```http
POST http://127.0.0.1:8080/api/v1/mahasiswa
X-API-KEY: KEY-MHS-233
Content-Type: application/json

{
  "nim": "209906191106",
  "nama": "Mahasiswa Baru Simulasi",
  "email": "maba.simulasi@iae.id",
  "prodi": "SI",
  "angkatan": 2026,
  "status": "aktif"
}
```

---

## Service B — KRS (Jafar)

**Folder:** `services/102022400045-Mochammad Jafar Arrazi-KRS`  
**Header wajib:** `X-IAE-KEY: KEY-MHS-109` (atau NIM owner service saat testing)

| Method | Path gateway | Keterangan |
|--------|--------------|------------|
| GET | `/api/v1/krs` | Daftar KRS |
| GET | `/api/v1/krs/{id}` | Detail KRS |
| POST | `/api/v1/krs` | Ajukan KRS → **SOAP + RabbitMQ** `krs.created` |
| PUT | `/api/v1/krs/{id}/approve` | Approve dosen (JWT) → `krs.approved` |

**Contoh Postman — Approve KRS (JWT dosen):**

```http
PUT http://127.0.0.1:8080/api/v1/krs/1/approve
Authorization: Bearer <JWT_dosen>
Content-Type: application/json
```

Login JWT dosen (warga01):

```http
POST https://iae-sso.virtualfri.id/api/v1/auth/token
Content-Type: application/json

{
  "email": "warga01@ktp.iae.id",
  "password": "KtpDigital2026!"
}
```

> Shortcut testing tim: bisa pakai `X-IAE-KEY: KEY-MHS-109` saja (role di-bypass). Alur resmi approve tetap JWT dosen.

**Contoh Postman — POST KRS:**

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

> **Catatan:** 1 mata kuliah = 1 POST = 1 event RabbitMQ terpisah.

---

## Service C — Nilai & Kurikulum (Andi)

**Folder:** `services/102022580023-Andi Muh. Arif Darma Saputra M-Nilai & Kurikulum`  
**Header wajib:** `X-IAE-KEY: KEY-MHS-117`  
**POST /nilai tambahan:** `Authorization: Bearer <JWT dosen warga01>`

| Method | Path gateway | Keterangan |
|--------|--------------|------------|
| GET | `/api/v1/kurikulum` | Daftar kurikulum |
| GET | `/api/v1/kurikulum/{kode}` | Detail matkul |
| GET | `/api/v1/nilai` | Semua nilai |
| GET | `/api/v1/nilai/{nim}` | Nilai + IPS per NIM |
| POST | `/api/v1/nilai` | Input nilai dosen → **SOAP + RabbitMQ** `nilai.recorded` |

**Contoh Postman — POST nilai:**

```http
POST http://127.0.0.1:8080/api/v1/nilai
X-IAE-KEY: KEY-MHS-117
Authorization: Bearer eyJ...
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

**Verifikasi nilai lewat Service A (agregasi):**

```http
GET http://127.0.0.1:8080/api/v1/mahasiswa/209907170001/matkul
X-API-KEY: KEY-MHS-233
```

Cek field `matkul_bernilai` dan `ringkasan.ips` di response.

Sebelum POST nilai, jalankan di host (bukan di container):

```bash
cd services/102022580023-Andi\ Muh.\ Arif\ Darma\ Saputra\ M-Nilai\ \&\ Kurikulum
php artisan iae:sync-token
```

---

## Alur end-to-end lewat gateway

```
1. POST /api/v1/mahasiswa          → mahasiswa.created
2. POST /api/v1/krs               → krs.created
3. PUT  /api/v1/krs/{id}/approve  → krs.approved
4. POST /api/v1/nilai             → nilai.recorded
5. GET  /api/v1/mahasiswa/{nim}/matkul → baca gabungan
```

Board event: https://iae-sso.virtualfri.id/board

---

## Troubleshooting gateway

| Gejala | Penyebab umum | Solusi |
|--------|---------------|--------|
| 401 Unauthorized | Header salah / tidak dikirim | Cek `X-API-KEY` vs `X-IAE-KEY` per service |
| 502 Bad Gateway | Service backend belum ready | `docker compose ps`, tunggu MySQL healthy |
| 404 Not Found | Path typo | Harus pakai prefix `/api/v1/...` |
| Header tidak sampai | Config nginx lama | Pastikan `proxy_set_header` ada di `nginx.conf` |

Restart setelah edit config:

```bash
docker compose restart gateway
```
