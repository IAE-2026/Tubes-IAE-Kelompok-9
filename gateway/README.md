# API Gateway — TIM-09 (Kelompok 9)

**Teknologi:** Nginx Alpine  
**Port publik:** `8080` (host) → `80` (container)  
**Container name:** `iae-gateway`

Folder ini berisi konfigurasi **API Gateway** untuk Tugas Besar IAE. Semua request dari Postman/browser **wajib** lewat gateway — service A/B/C tidak boleh diakses langsung dari luar Docker network (sesuai rubrik dosen).

---

## Isi folder

| File | Fungsi |
|------|--------|
| `nginx.conf` | Konfigurasi routing Nginx (upstream + proxy header) |
| `README.md` | Penjelasan gateway (file ini) |
| `routing-map.md` | Tabel routing lengkap per endpoint + header auth |

---

## Arsitektur singkat

```
Postman / Browser
       │
       ▼  http://127.0.0.1:8080
┌──────────────────┐
│   iae-gateway    │  ← folder gateway/ (nginx.conf)
│   (Nginx :80)    │
└────────┬─────────┘
         │ Docker network: iae-network (internal)
    ┌────┼────┐
    ▼    ▼    ▼
 Service A  Service B  Service C
 (Arneta)   (Jafar)    (Andi)
```

Service A/B/C cuma punya port debug opsional (`8001`/`8002`/`8003`) buat development — **alur resmi tugas besar tetap lewat `:8080`.**

---

## Cara jalanin

Dari root repo:

```bash
docker compose up -d --build
```

Cek gateway hidup:

```bash
curl http://127.0.0.1:8080/
```

Response contoh:

```json
{
  "gateway": "IAE Kelompok 9",
  "services": [
    "/api/v1/auth/token",
    "/api/v1/mahasiswa",
    "/api/v1/mahasiswa/{nim}/matkul",
    "/api/v1/krs",
    "/api/v1/kurikulum",
    "/api/v1/nilai"
  ]
}
```

---

## Header yang diteruskan gateway

Nginx meneruskan header penting ke service backend (lihat `nginx.conf`):

| Header | Dipakai untuk |
|--------|----------------|
| `X-API-KEY` | Service A (KEY-MHS-233) |
| `X-IAE-KEY` | Service B & C (NIM owner / key service) |
| `Authorization` | JWT SSO dosen (POST nilai, approve KRS, dll.) |

Kalau header ini hilang di Postman, sering muncul error **401 Unauthorized** — bukan karena service-nya mati, tapi karena gateway/nginx tidak meneruskan header (sudah kita set di config).

---

## Routing cepat

| Path prefix | Service tujuan | Anggota |
|-------------|----------------|---------|
| `/api/v1/mahasiswa` | Service A — Data Mahasiswa | Arneta |
| `/api/v1/auth` | Service A — SSO & token M2M | Arneta |
| `/api/v1/krs` | Service B — KRS | Jafar |
| `/api/v1/kurikulum` | Service C — Nilai & Kurikulum | Andi |
| `/api/v1/nilai` | Service C — Nilai & Kurikulum | Andi |

Detail lengkap + contoh Postman: [`routing-map.md`](routing-map.md)

---

## Hubungan dengan docker-compose

Di `docker-compose.yml` root, service `gateway` mount file ini:

```yaml
gateway:
  image: nginx:alpine
  ports:
    - "8080:80"
  volumes:
    - ./gateway/nginx.conf:/etc/nginx/nginx.conf:ro
```

Kalau `nginx.conf` diubah, restart gateway:

```bash
docker compose restart gateway
```

---

## Catatan TIM-09

Gateway ini memenuhi komponen **API Gateway & Routing Hub** pada rubrik Tugas Besar. Tanpa folder `gateway/`, stack Docker tetap bisa `up`, tapi **tidak ada single entry point** — integrasi end-to-end lewat Postman jadi tidak sesuai ketentuan dosen.

Penanggung jawab integrasi monorepo & gateway: **Andi Saputra (102022580023)** — dengan kontribusi routing auth dari **Arneta** (endpoint `/api/v1/auth`).
