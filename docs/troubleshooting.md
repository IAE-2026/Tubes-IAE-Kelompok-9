# Troubleshooting — TIM-09

Panduan cepat kalau testing macet. Kalau masih error, cek log container dulu.

---

## Perintah diagnosa dasar

```bash
docker compose ps                    # semua container Up?
docker compose logs -f gateway       # routing error?
docker compose logs -f mahasiswa-service
docker compose logs -f krs-service
docker compose logs -f kurikulum-nilai-service
curl http://127.0.0.1:8080/        # gateway hidup?
```

---

## Error 401 Unauthorized

| Gejala | Penyebab | Solusi |
|--------|----------|--------|
| Service A 401 | Header salah | Pakai `X-API-KEY: KEY-MHS-233` |
| Service B 401 | Header salah | Pakai `X-IAE-KEY: KEY-MHS-109` |
| Service C 401 | Isi KEY-MHS-117 di X-IAE-KEY | Harus `X-IAE-KEY: 102022580023` |
| POST nilai 401 | JWT expired / tidak ada | Login ulang warga01, copy Bearer baru |
| Header "tidak valid" di Postman | Key di tab Params bukan Headers | Pindah ke tab **Headers** |

Detail auth: [auth-dan-token.md](auth-dan-token.md)

---

## Error 502 Bad Gateway

- Service backend belum ready (MySQL masih starting)
- Tunggu 2–5 menit setelah `docker compose up`
- Cek: `docker compose ps` → database harus **healthy**

```bash
docker compose restart mahasiswa-service krs-service kurikulum-nilai-service
```

---

## Connection refused port 8080

```bash
docker compose up -d --build
```

Pastikan Docker Desktop running.

---

## POST mahasiswa 422 (NIM/email duplikat)

Ganti NIM dan email dengan nilai unik:

```json
{
  "nim": "209906191106",
  "email": "unik.barubaru@student.telkomuniversity.ac.id"
}
```

---

## SOAP gagal / receipt_number null

1. Token M2M expired di `.env`
2. Service C: `php artisan iae:sync-token` dari host
3. Cek koneksi internet ke `iae-sso.virtualfri.id`

---

## Event tidak muncul di board RabbitMQ

Checklist:

- [ ] Sudah POST transaksi kritis (bukan cuma sync token)?
- [ ] Response ada `receipt_number`?
- [ ] Payload publish punya `routing_key` + `message` wrapper?
- [ ] Cek board: https://iae-sso.virtualfri.id/board
- [ ] Filter tim **TEAM-09**

---

## KRS gagal untuk mahasiswa baru (404 nilai)

Mahasiswa baru belum punya riwayat nilai — Service B harus toleransi 404 dari Service C.

Sudah difix di monorepo: `ExternalAcademicService` KRS dengan `allowNotFound: true`.

Kalau masih error: `docker compose up -d --build` ulang.

---

## GET matkul kosong / error

- Pastikan NIM ada di Service A (seeder atau POST mahasiswa)
- Pastikan ada KRS di Service B untuk NIM tersebut
- Mahasiswa baru tanpa nilai → Service A return nilai kosong (bukan 404)

---

## Reset database penuh

```bash
docker compose down -v
docker compose up -d --build
```

⚠️ Semua data lokal hilang — seeder jalan ulang.

---

## Salah server di Postman

| ❌ Salah | ✅ Benar |
|---------|---------|
| `https://iae-sso.virtualfri.id/api/v1/mahasiswa` | `http://127.0.0.1:8080/api/v1/mahasiswa` |
| `localhost:8001` langsung | `localhost:8080` via gateway |

Cloud pusat cuma untuk **SSO, SOAP, RabbitMQ** — bukan API mahasiswa/KRS/nilai lokal.

---

## Masih bingung?

1. Baca [auth-dan-token.md](auth-dan-token.md)
2. Baca [gateway/routing-map.md](../gateway/routing-map.md)
3. Tanya di grup TIM-09 dengan lampiran screenshot + log error
