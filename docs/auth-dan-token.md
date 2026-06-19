# Auth & Token — TIM-09

Dokumen ini jawab pertanyaan yang sering muncul pas testing: **"Kok banyak banget key/token-nya?"**

---

## Ringkasan cepat

| Nama | Contoh nilai | Dipakai di | Fungsi |
|------|--------------|------------|--------|
| `X-API-KEY` | `KEY-MHS-233` | Header Postman → Service A | Auth Service A |
| `X-IAE-KEY` | `KEY-MHS-109` / `102022580023` | Header Postman → Service B/C | Auth service lokal |
| JWT Bearer | `eyJ...` | Header Postman | Login SSO dosen/mahasiswa |
| M2M token | di `.env` server | Server → cloud | SOAP + RabbitMQ |
| `api_key` + `nim` body | POST `/auth/token` | Minta M2M token | Ketentuan dosen terbaru |

---

## Service A — Mahasiswa (Arneta)

```http
GET http://127.0.0.1:8080/api/v1/mahasiswa
X-API-KEY: KEY-MHS-233
```

- Header: **`X-API-KEY`** (bukan `X-IAE-KEY`)
- Nilai: **`KEY-MHS-233`**
- Salah isi → `401 Unauthorized`

---

## Service B — KRS (Jafar)

```http
POST http://127.0.0.1:8080/api/v1/krs
X-IAE-KEY: KEY-MHS-109
Content-Type: application/json
```

- Header: **`X-IAE-KEY`**
- Nilai di monorepo: **`KEY-MHS-109`**
- Owner NIM Jafar (M2M): **`102022400045`**

---

## Service C — Nilai & Kurikulum (Andi)

```http
GET http://127.0.0.1:8080/api/v1/kurikulum
X-IAE-KEY: 102022580023
```

```http
POST http://127.0.0.1:8080/api/v1/nilai
X-IAE-KEY: 102022580023
Authorization: Bearer <JWT_dosen>
```

- Header masuk: **`X-IAE-KEY: 102022580023`** (NIM Andi, bukan KEY-MHS-117)
- POST nilai tambahan butuh **JWT dosen** dari login cloud
- **`KEY-MHS-117`** dipakai server internal buat sync token M2M, **bukan** di header Postman

Sync token Service C (dari folder service Andi, di host Mac):

```bash
php artisan iae:sync-token
```

---

## JWT dosen (untuk POST nilai / approve KRS)

Login ke cloud:

```http
POST https://iae-sso.virtualfri.id/api/v1/auth/token
Content-Type: application/json

{
  "email": "warga01@ktp.iae.id",
  "password": "KtpDigital2026!"
}
```

Copy field `token` → pakai sebagai `Authorization: Bearer ...`

---

## M2M token (tim → cloud)

Ketentuan dosen: body wajib ada **`api_key`** + **`nim`**

```http
POST http://127.0.0.1:8080/api/v1/auth/token
Content-Type: application/json

{
  "api_key": "KEY-MHS-233",
  "nim": "102022400136"
}
```

| Service | api_key | nim (owner) |
|---------|---------|-------------|
| A — Arneta | KEY-MHS-233 | 102022400136 |
| B — Jafar | KEY-MHS-109 | 102022400045 |
| C — Andi | KEY-MHS-117 | 102022580023 |

Token M2M dipakai server saat kirim SOAP/RabbitMQ — **bukan** Bearer di Postman untuk POST nilai.

---

## Kesalahan umum

| Salah | Benar |
|-------|-------|
| `X-IAE-KEY: KEY-MHS-117` di Postman Service C | `X-IAE-KEY: 102022580023` |
| `X-API-KEY` di Service B | `X-IAE-KEY: KEY-MHS-109` |
| API key di tab Params Postman | Harus di tab **Headers** |
| JWT dosen untuk SOAP | Token M2M di `.env` (sync-token) |

Lihat: [troubleshooting.md](troubleshooting.md)
