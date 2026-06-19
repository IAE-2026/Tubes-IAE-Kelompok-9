# Tubes IAE Kelompok 9

Monorepo integrasi 3 microservice IAE dengan API Gateway (Nginx) + Docker Compose.

| Service | Folder | Anggota |
|---------|--------|---------|
| A — Mahasiswa | `services/mahasiswa` | Arneta Alifiana |
| B — KRS | `services/krs` | Jafar Arrazi |
| C — Kurikulum & Nilai | `services/kurikulum-nilai` | Andi Saputra |

## Quick Start

```bash
docker compose up -d --build
curl http://127.0.0.1:8080/
```

Gateway: **http://127.0.0.1:8080** (satu-satunya port yang diakses dari luar).

## Tutorial Lengkap

Lihat **[docs/TUTORIAL-MENJALANKAN.md](docs/TUTORIAL-MENJALANKAN.md)** untuk:
- Arsitektur & alur end-to-end
- Cara uji di Postman (per service)
- Kredensial auth header
- Troubleshooting
- Checklist demo tugas besar
