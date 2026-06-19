# Arsitektur Sistem — TIM-09

## Prinsip dasar

1. **Satu pintu masuk** — semua API publik lewat API Gateway (`:8080`)
2. **Database terpisah** — Service A/B/C masing-masing punya MySQL sendiri
3. **Komunikasi HTTP** — service saling panggil via REST, bukan akses DB silang
4. **Cloud pusat** — SSO, SOAP audit, RabbitMQ di `iae-sso.virtualfri.id`

---

## Diagram komponen

```
┌─────────────────────────────────────────────────────────────┐
│                        LAPTOP / POSTMAN                      │
│                   http://127.0.0.1:8080                      │
└─────────────────────────────┬───────────────────────────────┘
                              │
┌─────────────────────────────▼───────────────────────────────┐
│  DOCKER — network: iae-network                                │
│  ┌─────────────┐                                              │
│  │ iae-gateway │  nginx.conf (folder gateway/)                │
│  └──────┬──────┘                                              │
│         │                                                     │
│  ┌──────▼──────┐  ┌─────────────┐  ┌─────────────────────┐   │
│  │ mahasiswa-  │  │ krs-        │  │ kurikulum-nilai-    │   │
│  │ service     │  │ service     │  │ service             │   │
│  │ (Arneta)    │  │ (Jafar)     │  │ (Andi)              │   │
│  └──────┬──────┘  └──────┬──────┘  └──────────┬──────────┘   │
│         │                │                     │              │
│  ┌──────▼──────┐  ┌──────▼──────┐  ┌──────────▼──────────┐   │
│  │ mahasiswa-db│  │ krs-db      │  │ kurikulum-db        │   │
│  └─────────────┘  └─────────────┘  └─────────────────────┘   │
└─────────────────────────────┬─────────────────────────────────┘
                              │ HTTPS (SOAP, RabbitMQ, SSO)
┌─────────────────────────────▼───────────────────────────────┐
│  CLOUD PUSAT — iae-sso.virtualfri.id                         │
│  SSO · SOAP /soap/v1/audit · RabbitMQ board                  │
└─────────────────────────────────────────────────────────────┘
```

---

## Port yang dipakai

| Komponen | Port host | Keterangan |
|----------|-----------|------------|
| API Gateway | **8080** | ✅ Akses Postman (wajib) |
| Service A (debug) | 8001 | Opsional dev — bukan alur resmi demo |
| Service B (debug) | 8002 | Opsional dev |
| Service C (debug) | 8003 | Opsional dev |
| MySQL | internal | Tidak expose ke host |

---

## Integrasi antar service (internal)

| Dari | Ke | Kapan | Contoh |
|------|-----|-------|--------|
| Service B | Service A | POST KRS | Cek NIM aktif |
| Service B | Service C | POST KRS | Cek IPS & kurikulum |
| Service C | Service A | POST nilai | Cek mahasiswa valid |
| Service A | Service B + C | GET matkul | Agregasi KRS + nilai (Http::pool) |

URL internal (di dalam Docker): `http://mahasiswa-service:8000`, `http://krs-service:8000`, `http://kurikulum-nilai-service:8000`

---

## Transaksi kritis Tugas 3 (SOAP + RabbitMQ)

| Service | Endpoint | SOAP | RabbitMQ |
|---------|----------|------|----------|
| A | POST `/api/v1/mahasiswa` | MahasiswaBaru | mahasiswa.created |
| B | POST `/api/v1/krs` | KrsCreated | krs.created |
| C | POST `/api/v1/nilai` | NilaiRecorded | nilai.recorded |

GET endpoint **tidak** trigger SOAP/RabbitMQ.

---

## File orkestrasi

| File | Fungsi |
|------|--------|
| `docker-compose.yml` | Jalankan gateway + 3 service + 3 DB |
| `gateway/nginx.conf` | Routing path → service |
| `services/<NIM-Nama-Service>/` | Kode Laravel masing-masing anggota |

Lihat juga: [gateway/routing-map.md](../gateway/routing-map.md)
