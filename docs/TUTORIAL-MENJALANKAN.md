# Tutorial Menjalankan Microservice — Tubes IAE Kelompok 9

Panduan ini untuk **Arneta** (Service A), **Jafar** (Service B), dan **Andi** (Service C) agar paham cara menjalankan dan menguji sistem gabungan.

---

## 1. Gambaran Arsitektur

```
                    ┌─────────────────────────────────────┐
  Postman / Client  │         API GATEWAY (Nginx)         │
  ───────────────►  │         http://localhost:8080       │
                    └──────────┬──────────┬──────────┬────┘
                               │          │          │
                    ┌──────────▼──┐  ┌────▼────┐  ┌──▼──────────────┐
                    │  Service A  │  │Service B│  │    Service C     │
                    │  Mahasiswa  │  │   KRS   │  │ Kurikulum & Nilai│
                    │  (Arneta)   │  │ (Jafar) │  │     (Andi)       │
                    └──────┬──────┘  └────┬────┘  └────────┬─────────┘
                           │              │                   │
                    ┌──────▼──────┐  ┌────▼────┐  ┌─────────▼────────┐
                    │ mahasiswa-db│  │ krs-db  │  │   kurikulum-db   │
                    └─────────────┘  └─────────┘  └──────────────────┘

  Service A/B/C ──► https://iae-sso.virtualfri.id (SSO, SOAP, RabbitMQ)
```

**Aturan penting:**
- Hanya **port 8080** yang boleh diakses dari luar (laptop/Postman).
- Service A, B, C **tidak** punya port sendiri ke host — hanya lewat gateway.
- Cloud `iae-sso.virtualfri.id` dipakai untuk **SSO, SOAP audit, dan RabbitMQ** — bukan untuk API mahasiswa/KRS/nilai.

---

## 2. Prasyarat

| Software | Cek versi |
|----------|-----------|
| Docker Desktop | `docker --version` |
| Docker Compose | `docker compose version` |
| Git | `git --version` |
| Postman (opsional) | untuk uji API |

Pastikan Docker Desktop **sudah running** sebelum lanjut.

---

## 3. Clone & Masuk ke Folder Proyek

```bash
git clone git@github.com:IAE-2026/Tubes-IAE-Kelompok-9.git
cd Tubes-IAE-Kelompok-9
```

Struktur folder utama:

```
Tubes-IAE-Kelompok-9/
├── docker-compose.yml      ← orkestrator semua service
├── gateway/nginx.conf      ← routing API Gateway
└── services/
    ├── mahasiswa/          ← Service A (Arneta)
    ├── krs/                ← Service B (Jafar)
    └── kurikulum-nilai/    ← Service C (Andi)
```

---

## 4. Menjalankan Semua Service

### Start (pertama kali atau setelah pull)

```bash
docker compose up -d --build
```

Proses ini akan:
1. Build image Docker tiap service
2. Jalankan 3 database MySQL
3. Migrate + seed data awal
4. Nyalakan gateway di port **8080**

Tunggu ±2–5 menit (tergantung laptop).

### Cek status container

```bash
docker compose ps
```

Semua harus **Up** (database **healthy**).

### Cek gateway hidup

```bash
curl http://127.0.0.1:8080/
```

Response contoh:
```json
{
  "gateway": "IAE Kelompok 9",
  "services": ["/api/v1/mahasiswa", "/api/v1/mahasiswa/{nim}/matkul", ...]
}
```

### Stop

```bash
docker compose down
```

### Stop + hapus data database (reset penuh)

```bash
docker compose down -v
docker compose up -d --build
```

---

## 5. Pembagian Service & Kredensial

| Service | Folder | Pemilik | Header Auth | Nilai |
|---------|--------|---------|-------------|-------|
| **A** — Mahasiswa | `services/mahasiswa` | Arneta | `X-API-KEY` | `KEY-MHS-233` |
| **B** — KRS | `services/krs` | Jafar | `X-IAE-KEY` | `102022400045` |
| **C** — Kurikulum & Nilai | `services/kurikulum-nilai` | Andi | `X-IAE-KEY` | `102022580023` |

**Base URL semua request:** `http://127.0.0.1:8080`

---

## 6. Routing Gateway

| Request dari Postman | Diteruskan ke |
|----------------------|---------------|
| `/api/v1/mahasiswa` | Service A |
| `/api/v1/mahasiswa/{nim}/matkul` | Service A (agregasi B + C) |
| `/api/v1/krs` | Service B |
| `/api/v1/kurikulum` | Service C |
| `/api/v1/nilai` | Service C |

---

## 7. Uji per Service (Postman)

### Untuk Arneta — Service A (Mahasiswa)

**List mahasiswa**
```
GET http://127.0.0.1:8080/api/v1/mahasiswa
Header: X-API-KEY = KEY-MHS-233
```

**Detail mahasiswa**
```
GET http://127.0.0.1:8080/api/v1/mahasiswa/102022400136
Header: X-API-KEY = KEY-MHS-233
```

**Lihat matkul yang diambil (KRS + nilai)**
```
GET http://127.0.0.1:8080/api/v1/mahasiswa/102022400136/matkul
Header: X-API-KEY = KEY-MHS-233
```

**Tambah mahasiswa baru (SOAP + RabbitMQ)**
```
POST http://127.0.0.1:8080/api/v1/mahasiswa
Header: X-API-KEY = KEY-MHS-233
Header: Content-Type = application/json

Body:
{
  "nim": "2099001234",
  "nama": "Mahasiswa Baru",
  "email": "baru2099001234@student.telkomuniversity.ac.id",
  "prodi": "S1 Sistem Informasi",
  "angkatan": 2026,
  "status": "aktif"
}
```

Cek sukses: `receipt_number` (SOAP) dan `rabbit_status: "terkirim"`.
Cek board: https://iae-sso.virtualfri.id/board → cari `mahasiswa.created`.

> **Tips Postman:** API key harus di tab **Headers**, bukan di tab Params/URL.

---

### Untuk Jafar — Service B (KRS)

**Lihat semua KRS**
```
GET http://127.0.0.1:8080/api/v1/krs
Header: X-IAE-KEY = 102022400045
```

**Submit KRS baru**
```
POST http://127.0.0.1:8080/api/v1/krs
Header: X-IAE-KEY = 102022400045
Header: Content-Type = application/json

Body:
{
  "nim": "102022400136",
  "kode_mata_kuliah": "SI301",
  "tahun_ajaran": "2025/2026",
  "semester": "ganjil"
}
```

Saat POST KRS, Service B **otomatis**:
1. Cek mahasiswa aktif ke **Service A**
2. Cek IPS & kurikulum ke **Service C**
3. Simpan KRS + kirim SOAP & RabbitMQ ke cloud

**Approve KRS (butuh JWT dosen dari cloud)**
```
PUT http://127.0.0.1:8080/api/v1/krs/{id}/approve
Header: Authorization = Bearer <JWT_dosen>
```

---

### Untuk Andi — Service C (Kurikulum & Nilai)

**List kurikulum**
```
GET http://127.0.0.1:8080/api/v1/kurikulum
Header: X-IAE-KEY = 102022580023
```

**Nilai per mahasiswa**
```
GET http://127.0.0.1:8080/api/v1/nilai/102022400136
Header: X-IAE-KEY = 102022580023
```

**Tambah nilai (butuh JWT dosen + SOAP/RabbitMQ)**
```
POST http://127.0.0.1:8080/api/v1/nilai
Header: X-IAE-KEY = 102022580023
Header: Authorization = Bearer <JWT_dosen>
Header: Content-Type = application/json
```

---

## 8. Alur End-to-End (Wajib Paham untuk Demo)

### Alur 1: Mahasiswa baru terdaftar

```
1. POST /api/v1/mahasiswa          → Service A simpan DB
2. Service A → cloud: token M2M
3. Service A → cloud: SOAP audit   → dapat receipt_number
4. Service A → cloud: RabbitMQ     → event mahasiswa.created
5. Cek board iae-sso.virtualfri.id
```

### Alur 2: Mahasiswa ambil KRS

```
1. POST /api/v1/krs                → Service B
2. Service B → GET Service A       → cek mahasiswa aktif
3. Service B → GET Service C       → cek IPS & mata kuliah valid
4. Service B simpan KRS (status: pending)
5. Service B → cloud: SOAP + RabbitMQ (krs.created)
```

### Alur 3: Lihat matkul yang diambil

```
1. GET /api/v1/mahasiswa/{nim}/matkul  → Service A
2. Service A → GET Service B (KRS aktif)
3. Service A → GET Service C (nilai historis)
4. Response gabungan ke client
```

---

## 9. Data Awal (Seeder)

Setelah `docker compose up`, data contoh sudah terisi:

**Mahasiswa (Service A):**
| NIM | Nama |
|-----|------|
| 102022400136 | Arneta Alifiana |
| 102022580023 | Andi Muh. Arif Darma Saputra M |
| 2099000015 | Mahasiswa Anonim 0015 |

**Kurikulum (Service C):** SI101, SI102, SI103, SI201, SI202, SI301, SI302, SI401, SI402, SI501

---

## 10. Troubleshooting

| Masalah | Solusi |
|---------|--------|
| `Connection refused` port 8080 | Jalankan `docker compose up -d` |
| `401 Unauthorized` | Cek header auth sesuai tabel di atas |
| Header tidak terbaca di Postman | Pakai tab **Headers**, bukan Params |
| `404` di `iae-sso.virtualfri.id/api/v1/mahasiswa` | Salah server — pakai `localhost:8080` |
| POST mahasiswa `422` NIM duplikat | Ganti NIM & email dengan yang unik |
| Container restart terus | `docker compose logs mahasiswa-service` |
| Reset semua data | `docker compose down -v` lalu `up --build` |

### Lihat log service

```bash
docker compose logs -f mahasiswa-service   # Arneta
docker compose logs -f krs-service         # Jafar
docker compose logs -f kurikulum-nilai-service  # Andi
docker compose logs -f gateway
```

---

## 11. Checklist Demo Tugas Besar

- [ ] `docker compose up -d --build` berhasil
- [ ] `GET http://127.0.0.1:8080/` menampilkan info gateway
- [ ] Service A: list & detail mahasiswa OK
- [ ] Service A: POST mahasiswa → `receipt_number` + `rabbit_status: terkirim`
- [ ] Board RabbitMQ: event `mahasiswa.created` muncul (TEAM-09)
- [ ] Service B: POST KRS → validasi ke A & C berhasil
- [ ] Service A: GET `/mahasiswa/{nim}/matkul` menampilkan KRS aktif
- [ ] Port service internal (8000) **tidak** bisa diakses langsung dari host

---

## 12. Kontak & Tanggung Jawab

| Anggota | Service | Fokus integrasi |
|---------|---------|-----------------|
| Arneta Alifiana | A — Mahasiswa | SOAP `MahasiswaBaru`, event `mahasiswa.created` |
| Jafar Arrazi | B — KRS | Validasi lintas service, event `krs.created` |
| Andi Saputra | C — Kurikulum & Nilai | Kurikulum, nilai, event `nilai.recorded` |
| Semua | Gateway + Compose | Orkestrasi Docker, routing Nginx |

---

*Dokumen ini bagian dari repo `Tubes-IAE-Kelompok-9`. Update jika ada perubahan konfigurasi.*
