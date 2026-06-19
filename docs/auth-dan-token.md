# Auth & Token — TIM-09

Dokumen ini jawab pertanyaan yang sering muncul pas testing: **"Kok banyak banget key/token-nya?"**

---

## Ringkasan cepat

| Nama | Contoh nilai | Dipakai di | Fungsi |
|------|--------------|------------|--------|
| `X-API-KEY` | `KEY-MHS-233` | Header Postman → Service A | Auth Service A |
| `X-IAE-KEY` | `KEY-MHS-109` / `KEY-MHS-117` | Header Postman → Service B/C | Auth service lokal |
| JWT Bearer | `eyJ...` | Header Postman | Login SSO dosen/mahasiswa |
| M2M token | di `.env` server | Server → cloud | SOAP + RabbitMQ |
| `api_key` + `nim` | body POST `/auth/token` | Minta M2M token | **Ketentuan dosen terbaru** |

---

## Service A — Arneta (Data Mahasiswa)

> *Gaya penjelasan Arneta:* pakai header **`X-API-KEY`**, bukan `X-IAE-KEY`.

```http
GET http://127.0.0.1:8080/api/v1/mahasiswa
X-API-KEY: KEY-MHS-233
```

Pas aku POST mahasiswa baru dan mau SOAP + RabbitMQ jalan, server-ku otomatis minta token M2M ke cloud. Sekarang dosen minta **dua field** di body, bukan cuma `api_key`:

```http
POST http://127.0.0.1:8080/api/v1/auth/token
Content-Type: application/json

{
  "api_key": "KEY-MHS-233",
  "nim": "102022400136"
}
```

Kalau `nim`-nya kosong atau salah, cloud balas 401. Endpoint ini juga bisa lewat gateway (proxy ke Service A).

---

## Service B — Jafar (KRS)

> *Gaya penjelasan Jafar:* header service **`X-IAE-KEY: KEY-MHS-109`**. Approve KRS resmi pakai JWT dosen.

```http
POST http://127.0.0.1:8080/api/v1/krs
X-IAE-KEY: KEY-MHS-109
Content-Type: application/json
```

Untuk integrasi ke cloud (SOAP audit dan RabbitMQ), `CentralSsoClient` saya kirim request token dengan pasangan key dan NIM owner service:

```http
POST http://127.0.0.1:8080/api/v1/auth/token
Content-Type: application/json

{
  "api_key": "KEY-MHS-109",
  "nim": "102022400045"
}
```

Token hasil response disimpan sementara di cache, lalu dipakai sebagai Bearer saat hit `/soap/v1/audit` dan `/api/v1/messages/publish`.

---

## Service C — Andi (Nilai & Kurikulum)

> *Gaya penjelasan Andi:* header **`X-IAE-KEY: KEY-MHS-117`**. POST nilai tambahan wajib JWT dosen (warga01).

```http
GET http://127.0.0.1:8080/api/v1/kurikulum
X-IAE-KEY: KEY-MHS-117
```

```http
POST http://127.0.0.1:8080/api/v1/nilai
X-IAE-KEY: KEY-MHS-117
Authorization: Bearer <JWT_dosen>
```

Token M2M buat SOAP/RabbitMQ saya sync lewat perintah ini (dari folder Service C, di host Mac):

```bash
php artisan iae:sync-token
```

Perintah itu POST ke cloud dengan body:

```json
{
  "api_key": "KEY-MHS-117",
  "nim": "102022580023"
}
```

Hasilnya masuk ke `IAE_SSO_TOKEN` di `.env` — **beda** sama JWT dosen di Postman.

---

## JWT dosen (POST nilai / approve KRS)

Login ke cloud (bukan M2M):

```http
POST https://iae-sso.virtualfri.id/api/v1/auth/token
Content-Type: application/json

{
  "email": "warga01@ktp.iae.id",
  "password": "KtpDigital2026!"
}
```

Copy field `token` → `Authorization: Bearer ...`

---

## Tabel M2M TIM-09 (ketentuan dosen)

| Service | Anggota | api_key | nim (owner) |
|---------|---------|---------|-------------|
| A | Arneta | KEY-MHS-233 | 102022400136 |
| B | Jafar | KEY-MHS-109 | 102022400045 |
| C | Andi | KEY-MHS-117 | 102022580023 |

Semua bisa diuji lewat gateway:

```http
POST http://127.0.0.1:8080/api/v1/auth/token
```

Response sukses: `token_type: m2m`, `team: TEAM-09`.

---

## Kesalahan umum

| Salah | Benar |
|-------|-------|
| Body cuma `{ "api_key": "..." }` | Wajib tambah `"nim": "..."` |
| `nim` beda owner | Harus NIM pemilik service di tim |
| JWT dosen dipakai buat SOAP | Pakai M2M token (sync-token / otomatis server) |
| API key di tab Params Postman | Harus di tab **Headers** atau **Body JSON** |

Lihat: [troubleshooting.md](troubleshooting.md)
