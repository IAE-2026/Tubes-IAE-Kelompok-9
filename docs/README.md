# Dokumentasi Tubes IAE — Kelompok 9 (TIM-09)

**Mata Kuliah:** BBK2HAB3 — Integrasi Aplikasi Enterprise  
**Repository:** `Tubes-IAE-Kelompok-9`  
**Domain:** Education System

Folder `docs/` berisi panduan teknis tim untuk menjalankan, menguji, dan demo sistem gabungan 3 microservice + API Gateway.

---

## Isi folder docs/

| File | Untuk siapa | Isi |
|------|-------------|-----|
| [README.md](README.md) | Semua | Index dokumentasi (file ini) |
| [TUTORIAL-MENJALANKAN.md](TUTORIAL-MENJALANKAN.md) | Arneta, Jafar, Andi | **Panduan utama** — clone, docker, Postman, checklist demo |
| [arsitektur-sistem.md](arsitektur-sistem.md) | Semua + dosen | Diagram arsitektur, pembagian service, alur data |
| [auth-dan-token.md](auth-dan-token.md) | Semua | Beda `X-API-KEY`, `X-IAE-KEY`, JWT, M2M token |
| [integrasi-cloud.md](integrasi-cloud.md) | Semua | SSO, SOAP audit, RabbitMQ board |
| [troubleshooting.md](troubleshooting.md) | Dev / demo | Error umum + solusi cepat |
| [postman-guide.md](postman-guide.md) | Semua | Copy-paste request Postman per service |
| [checklist-demo.md](checklist-demo.md) | Demo | Checklist sebelum presentasi |

---

## Dokumentasi di folder lain (tetap bagian tim)

| Lokasi | Isi |
|--------|-----|
| [gateway/README.md](../gateway/README.md) | API Gateway Nginx — routing hub port 8080 |
| [gateway/routing-map.md](../gateway/routing-map.md) | Tabel endpoint + contoh Postman per path |
| [recapt_pekerjaan_TEAM9 (analisis, log-promt, resume)/](../recapt_pekerjaan_TEAM9%20(analisis,%20log-promt,%20resume)/README.md) | Log prompt, resume kontribusi, analisis Education System |

---

## Quick start (5 menit)

```bash
git clone git@github.com:IAE-2026/Tubes-IAE-Kelompok-9.git
cd Tubes-IAE-Kelompok-9
docker compose up -d --build
curl http://127.0.0.1:8080/
```

**Base URL Postman:** `http://127.0.0.1:8080`  
**Cloud pusat:** https://iae-sso.virtualfri.id  
**Board RabbitMQ:** https://iae-sso.virtualfri.id/board

---

## Pembagian service

| Service | Anggota | NIM | Folder | Header | Key / NIM header |
|---------|---------|-----|--------|--------|------------------|
| A — Data Mahasiswa | Arneta Alifiana | 102022400136 | `services/102022400136-Arneta Alifiana-Data Mahasiswa` | `X-API-KEY` | `KEY-MHS-233` |
| B — KRS | Mochammad Jafar Arrazi | 102022400045 | `services/102022400045-Mochammad Jafar Arrazi-KRS` | `X-IAE-KEY` | `KEY-MHS-109` |
| C — Nilai & Kurikulum | Andi Saputra | 102022580023 | `services/102022580023-Andi Muh. Arif Darma Saputra M-Nilai & Kurikulum` | `X-IAE-KEY` | `102022580023` |

---

## Urutan baca (disarankan)

1. **Pertama kali clone** → [TUTORIAL-MENJALANKAN.md](TUTORIAL-MENJALANKAN.md) bagian 1–4  
2. **Bingung auth/header** → [auth-dan-token.md](auth-dan-token.md)  
3. **Demo SOAP/RabbitMQ** → [integrasi-cloud.md](integrasi-cloud.md)  
4. **Error pas testing** → [troubleshooting.md](troubleshooting.md)  
5. **Penilaian / laporan** → folder `recapt_pekerjaan_TEAM9`

---

*TIM-09 — Integrasi Aplikasi Enterprise 2026*
