# Resume Kontribusi Gabungan — Kelompok 9 (TIM-09)

**Mata Kuliah:** BBK2HAB3 — Integrasi Aplikasi Enterprise  
**Repository:** Tubes-IAE-Kelompok-9  
**Gateway:** http://127.0.0.1:8080

---

## Gambaran Sistem

Kelompok 9 membangun ekosistem akademik terdistribusi dengan tiga microservice Laravel yang berkomunikasi via HTTP REST (bukan akses database silang). Seluruh service berjalan di Docker dan diakses publik melalui Nginx API Gateway.

| Service | Pengembang | Domain | API Key |
|---------|------------|--------|---------|
| A — Data Mahasiswa | Arneta | Profil & registrasi mahasiswa, SSO login, agregasi matkul | KEY-MHS-233 (`X-API-KEY`) |
| B — KRS | Jafar | Pengisian & persetujuan KRS | KEY-MHS-109 (`X-IAE-KEY`) |
| C — Kurikulum & Nilai | Andi | Data kurikulum, nilai, IPS | KEY-MHS-117 (`X-IAE-KEY`) |

---

## Kontribusi per Anggota

### Arneta — Service A
Mengelola data profil mahasiswa (NIM, nama, email, prodi, status). Menyediakan endpoint registrasi mahasiswa baru, pencarian by NIM, login SSO terfederasi, dan agregasi KRS + nilai paralel (Http::pool) dari Service B dan C. Transaksi kritis Tugas 3: POST /mahasiswa → SOAP MahasiswaBaru + RabbitMQ `mahasiswa.created`.

→ Detail: [`resume-kontribusi-masing-masing/arneta/RESUME_ARNETA.md`](resume-kontribusi-masing-masing/arneta/RESUME_ARNETA.md)

### Jafar — Service B
Mengelola pengisian KRS mahasiswa dengan validasi silang ke Service A (mahasiswa aktif), Service C (IPS/nilai), dan kurikulum. Mendukung persetujuan KRS oleh dosen via SSO. Transaksi kritis Tugas 3: POST /krs → SOAP KrsCreated + RabbitMQ `krs.created`.

→ Detail: [`resume-kontribusi-masing-masing/jafar/RESUME_KONTRIBUSI.md`](resume-kontribusi-masing-masing/jafar/RESUME_KONTRIBUSI.md)

### Andi — Service C
Mengelola data kurikulum dan pencatatan nilai mahasiswa. Menyediakan REST + GraphQL, middleware keamanan, validasi ke Service A saat input nilai. Transaksi kritis Tugas 3: POST /nilai → SOAP NilaiRecorded + RabbitMQ `nilai.recorded`, dengan token M2M auto-sync.

→ Detail: [`resume-kontribusi-masing-masing/andi/RESUME_ANDI.md`](resume-kontribusi-masing-masing/andi/RESUME_ANDI.md)

---

## Integrasi Kelompok (Monorepo)

Setelah subtree merge ke `Tubes-IAE-Kelompok-9`:
- `docker-compose.yml` menjalankan ketiga service + gateway + MySQL per service
- `MahasiswaSeeder` menyinkronkan data mahasiswa antar service
- Service A menerima header `X-API-KEY` dan `X-IAE-KEY`
- KRS memvalidasi mahasiswa/nilai ke service lain via gateway internal
- M2M token memerlukan `api_key` + `nim` owner per service

---

## Alur Bisnis End-to-End

1. **Mahasiswa baru** didaftarkan di Service A → event `mahasiswa.created` ke RabbitMQ
2. **Mahasiswa mengisi KRS** di Service B → validasi ke A & C → event `krs.created`
3. **Dosen approve KRS** → event `krs.approved`
4. **Dosen input nilai** di Service C → event `nilai.recorded`
5. **Agregasi** GET `/mahasiswa/{nim}/matkul` di Service A menggabungkan KRS + nilai paralel

Semua transaksi kritis Tugas 3 terverifikasi di Cloud Pusat dengan ReceiptNumber SOAP dan badge event di https://iae-sso.virtualfri.id/board.
