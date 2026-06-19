# Analisis Studi Kasus Gabungan — Tugas 3 (TIM-09)

**Mata Kuliah:** BBK2HAB3 — Integrasi Aplikasi Enterprise  
**Studi Kasus:** The Enterprise Digital City  
**Cloud Pusat:** https://iae-sso.virtualfri.id

---

## Konteks Studi Kasus

Tugas 3 mensimulasikan kota digital enterprise di mana setiap tim mengelola satu layanan akademik lokal yang terhubung ke **Cloud Pusat** (SSO, SOAP audit, RabbitMQ board). Setiap service wajib:

1. Menentukan **satu transaksi kritis** (POST yang mengubah data)
2. Mengirim **SOAP audit** ke `/soap/v1/audit` dan menyimpan `ReceiptNumber`
3. Mempublish **event RabbitMQ** via `/api/v1/messages/publish`

Kelompok 9 memilih tiga transaksi yang membentuk alur akademik lengkap:

| Service | Transaksi Kritis | SOAP | RabbitMQ |
|---------|------------------|------|----------|
| A (Arneta) | POST /api/v1/mahasiswa | MahasiswaBaru | mahasiswa.created |
| B (Jafar) | POST /api/v1/krs | KrsCreated | krs.created |
| C (Andi) | POST /api/v1/nilai | NilaiRecorded | nilai.recorded |

---

## Analisis per Service

### Service A — Pencatatan Mahasiswa Baru (Arneta)

Mahasiswa baru memicu efek domino ke seluruh sistem. SOAP berfungsi sebagai bukti legal audit (ReceiptNumber), RabbitMQ memberitahu Service B dan C tanpa coupling langsung — jika Service B mati sementara, registrasi mahasiswa tetap sukses.

→ Analisis lengkap: [`analisis-studi-kasus-masing-masing/arneta/analisis_tugas_3.md`](analisis-studi-kasus-masing-masing/arneta/analisis_tugas_3.md)

### Service B — Pencatatan KRS Baru (Jafar)

KRS adalah transaksi kritis karena mengubah rencana studi mahasiswa. SSO memetakan role (mahasiswa vs dosen). Setelah KRS tersimpan, SOAP + RabbitMQ `krs.created` memastikan pusat dan layanan lain aware.

→ Analisis lengkap: [`analisis-studi-kasus-masing-masing/jafar/analisis_tugas_3.md`](analisis-studi-kasus-masing-masing/jafar/analisis_tugas_3.md)

### Service C — Pencatatan Nilai (Andi)

POST /nilai dipilih karena menambah data baru (GET hanya baca). Alur: verifikasi JWT dosen → simpan DB → SOAP audit → publish `nilai.recorded`. Token M2M (KEY-MHS-117) terpisah dari JWT dosen dan X-IAE-KEY (NIM owner).

→ Analisis lengkap: [`analisis-studi-kasus-masing-masing/andi/analisis_tugas_3.md`](analisis-studi-kasus-masing-masing/andi/analisis_tugas_3.md)

---

## Alur Integrasi End-to-End TIM-09

```
[Admin/Client]
      │
      ▼ POST /mahasiswa ──► Service A ──► SOAP + mahasiswa.created
      │
      ▼ POST /krs ────────► Service B ──► validasi A & C ──► SOAP + krs.created
      │
      ▼ PUT /krs/approve ► Service B ──► krs.approved
      │
      ▼ POST /nilai ──────► Service C ──► validasi A ──► SOAP + nilai.recorded
      │
      ▼ GET /mahasiswa/{nim}/matkul ► Service A ──► Http::pool(B + C) ──► agregasi
```

---

## Bukti Testing Cloud Pusat

Transaksi berhasil diverifikasi di board RabbitMQ dengan ReceiptNumber SOAP, antara lain:

- Service A: `IAE-LOG-2026-B377E6F5` (mahasiswa.created)
- Service B: `IAE-LOG-2026-F92E5D18` (krs.created)
- Service C: `IAE-LOG-2026-7C375568` (nilai.recorded)

---

## Kesimpulan Kelompok

Ketiga service TIM-09 saling melengkapi dalam simulasi enterprise city: Service A membuka identitas mahasiswa, Service B mengatur rencana studi, Service C menutup siklus dengan nilai. SOAP menjamin auditability ke pusat; RabbitMQ menjamin decoupling antar layanan. GET endpoint sengaja tidak dipilih sebagai transaksi kritis karena tidak mengubah state sistem.
