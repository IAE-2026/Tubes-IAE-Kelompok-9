# Checklist Demo Tugas Besar — TIM-09

Centang sebelum presentasi ke dosen.

---

## Persiapan environment

- [ ] Docker Desktop running
- [ ] `docker compose up -d --build` sukses
- [ ] `docker compose ps` — semua container **Up**, DB **healthy**
- [ ] `curl http://127.0.0.1:8080/` return info gateway
- [ ] Postman base URL = `http://127.0.0.1:8080`

---

## Service A — Mahasiswa (Arneta)

- [ ] `GET /api/v1/mahasiswa` + `X-API-KEY: KEY-MHS-233` → 200
- [ ] `GET /api/v1/mahasiswa/102022400136` → detail mahasiswa
- [ ] `POST /api/v1/mahasiswa` → `receipt_number` ada
- [ ] Response POST → `rabbit_status: "terkirim"`
- [ ] Board → event `mahasiswa.created` (TEAM-09)

---

## Service B — KRS (Jafar)

- [ ] `GET /api/v1/krs` + `X-IAE-KEY: KEY-MHS-109` → 200
- [ ] `POST /api/v1/krs` → validasi ke Service A & C jalan
- [ ] Response POST → `receipt_number` + `krs.created`
- [ ] 3 matkul = 3 POST terpisah (SI301, SI302, SI401)
- [ ] `PUT /api/v1/krs/{id}/approve` + JWT dosen → sukses

---

## Service C — Nilai & Kurikulum (Andi)

- [ ] `GET /api/v1/kurikulum` + `X-IAE-KEY: 102022580023` → 200
- [ ] `GET /api/v1/nilai/{nim}` → data nilai + IPS
- [ ] `php artisan iae:sync-token` sudah dijalankan
- [ ] `POST /api/v1/nilai` + JWT dosen → `receipt_number`
- [ ] Board → event `nilai.recorded`

---

## Integrasi end-to-end

- [ ] `GET /api/v1/mahasiswa/{nim}/matkul` menampilkan KRS + nilai gabungan
- [ ] Port 8001/8002/8003 **tidak** dipakai untuk demo (hanya gateway :8080)
- [ ] Cloud `iae-sso.virtualfri.id` hanya untuk SSO/SOAP/RabbitMQ

---

## Dokumentasi repo

- [ ] `docs/README.md` — index dokumentasi
- [ ] `docs/TUTORIAL-MENJALANKAN.md` — panduan tim
- [ ] `gateway/README.md` + `routing-map.md` — routing gateway
- [ ] `recapt_pekerjaan_TEAM9/` — log prompt + resume kontribusi

---

## Kalau ada yang gagal

Lihat [troubleshooting.md](troubleshooting.md) atau jalankan:

```bash
docker compose logs -f gateway
docker compose logs -f mahasiswa-service
docker compose logs -f krs-service
docker compose logs -f kurikulum-nilai-service
```

Reset penuh (hati-hati, data hilang):

```bash
docker compose down -v && docker compose up -d --build
```
