# Integrasi Cloud Pusat — TIM-09

**URL:** https://iae-sso.virtualfri.id  
**Board event:** https://iae-sso.virtualfri.id/board  
**Team ID:** `TIM-09` / `TEAM-09`

---

## Tiga jalur integrasi cloud

| Jalur | Teknologi | Kapan jalan | Bukti sukses |
|-------|-----------|-------------|--------------|
| SSO | REST JWT | Login dosen/mahasiswa | Token Bearer |
| Audit | SOAP XML | Transaksi kritis POST | `ReceiptNumber` (IAE-LOG-2026-...) |
| Event | RabbitMQ via HTTP | Setelah SOAP sukses | Badge di board |

---

## Transaksi per service

### Service A — POST `/api/v1/mahasiswa`

- SOAP ActivityName: **MahasiswaBaru**
- RabbitMQ: **mahasiswa.created**
- Contoh receipt: `IAE-LOG-2026-B377E6F5`

### Service B — POST `/api/v1/krs`

- SOAP ActivityName: **KrsCreated**
- RabbitMQ: **krs.created**
- Contoh receipt: `IAE-LOG-2026-F92E5D18`
- **1 matkul = 1 POST = 1 event** (bukan array banyak matkul)

### Service C — POST `/api/v1/nilai`

- SOAP ActivityName: **NilaiRecorded**
- RabbitMQ: **nilai.recorded**
- Contoh receipt: `IAE-LOG-2026-7C375568`

---

## Alur POST nilai (Service C) — contoh lengkap

```
1. Dosen login cloud → dapat JWT
2. POST /api/v1/nilai via gateway :8080
   - X-IAE-KEY: 102022580023
   - Authorization: Bearer JWT
3. Service C simpan ke MySQL lokal
4. Service C → SOAP audit cloud → ReceiptNumber
5. Service C → publish nilai.recorded → board
6. Response 201: receipt_number + event_published: true
```

Sebelum step 4–5, pastikan token M2M di `.env` fresh:

```bash
php artisan iae:sync-token
```

---

## Format publish RabbitMQ (penting)

Cloud pusat expect struktur:

```json
{
  "routing_key": "nilai.recorded",
  "message": {
    "event": "nilai.recorded",
    "timestamp": "2026-06-10T04:48:48+00:00",
    "data": { ... }
  }
}
```

Harus ada **`routing_key`** dan wrapper **`message`** — kalau tidak, event tidak muncul di board.

---

## Cara cek sukses

1. **Response API** — ada field `receipt_number` / `rabbit_status: terkirim`
2. **Board** — https://iae-sso.virtualfri.id/board → badge hijau TIM-09
3. **Log container** — `docker compose logs -f kurikulum-nilai-service`

---

## GET tidak kirim SOAP/RabbitMQ

Endpoint baca data (`GET /kurikulum`, `GET /nilai`, `GET /mahasiswa`) **tidak** trigger cloud integration — cuma transaksi POST yang mengubah data.

---

## Simulasi end-to-end TIM-09

```
POST mahasiswa (A) → mahasiswa.created
POST krs (B)       → krs.created
PUT approve (B)    → krs.approved
POST nilai (C)     → nilai.recorded
GET matkul (A)     → baca gabungan B+C
```

Panduan Postman detail: [TUTORIAL-MENJALANKAN.md](TUTORIAL-MENJALANKAN.md)
