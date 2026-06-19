# Tubes IAE Kelompok 9

Monorepo integrasi 3 microservice IAE dengan API Gateway (Nginx) + Docker Compose.

| Service | Folder | Anggota |
|---------|--------|---------|
| A — Mahasiswa | `services/102022400136-Arneta Alifiana-Data Mahasiswa` | Arneta Alifiana |
| B — KRS | `services/102022400045-Mochammad Jafar Arrazi-KRS` | Jafar Arrazi |
| C — Nilai & Kurikulum | `services/102022580023-Andi Muh. Arif Darma Saputra M-Nilai & Kurikulum` | Andi Saputra |

## Quick Start

```bash
docker compose up -d --build
curl http://127.0.0.1:8080/
```

Gateway: **http://127.0.0.1:8080** (satu-satunya port yang diakses dari luar).

Dokumentasi gateway: **[gateway/README.md](gateway/README.md)** · [routing-map.md](gateway/routing-map.md)

## Dokumentasi Penilaian (TIM-09)

Lihat **[recapt_pekerjaan_TEAM9 (analisis, log-promt, resume)/README.md](recapt_pekerjaan_TEAM9%20(analisis,%20log-promt,%20resume)/README.md)** untuk log-prompt, resume kontribusi, dan analisis Education System per anggota.

## Tutorial Lengkap

Mulai dari index: **[docs/README.md](docs/README.md)**

Panduan utama: **[docs/TUTORIAL-MENJALANKAN.md](docs/TUTORIAL-MENJALANKAN.md)**

Dokumen pendukung:
- [docs/arsitektur-sistem.md](docs/arsitektur-sistem.md)
- [docs/auth-dan-token.md](docs/auth-dan-token.md)
- [docs/integrasi-cloud.md](docs/integrasi-cloud.md)
- [docs/troubleshooting.md](docs/troubleshooting.md)
