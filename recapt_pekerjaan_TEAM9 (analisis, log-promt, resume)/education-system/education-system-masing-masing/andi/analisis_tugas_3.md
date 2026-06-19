# Analisis Tugas 3 — Service C Nilai & Kurikulum

Mahasiswa: Andi Muh. Arif Darma Saputra M  
NIM: 102022580023  
Tim Lab: TIM-09  
Layanan: Service C — Nilai & Kurikulum  
Cloud Pusat: https://iae-sso.virtualfri.id

---

## 1. Justifikasi Transaksi Kritis

Service C punya tiga endpoint utama. Yang saya pakai buat Tugas 3 cuma POST /api/v1/nilai, karena endpoint itu yang benar-benar nambah data nilai baru ke database. GET /api/v1/kurikulum dan GET /api/v1/nilai cuma baca data, jadi menurut saya tidak perlu audit SOAP maupun broadcast RabbitMQ.

Untuk transaksi penting SOAP, saya pilih POST /api/v1/nilai. Setiap nilai dicatat, Service C kirim audit ke Cloud Pusat dan dapat ReceiptNumber sebagai bukti. Contoh dari testing saya: IAE-LOG-2026-828A266C. Untuk transaksi yang harus disebar lewat RabbitMQ, saya pakai endpoint yang sama. Setelah audit SOAP selesai, Service C publish event nilai.recorded ke Cloud Pusat supaya service lain tahu ada nilai baru. Alurnya: login SSO, POST /nilai, simpan DB, audit SOAP, publish RabbitMQ, lalu response sukses.

---

## 2. Token, Key, dan SSO

Sebelum input nilai, user harus login ke Cloud Pusat dulu dan dapat JWT. Service C verifikasi JWT itu, lalu cek role lokal. Yang boleh input nilai cuma dosen dan admin. Akun warga01@ktp.iae.id saya mapping sebagai dosen buat testing di Postman.

Awalnya saya juga bingung karena kayanya banyak key sekaligus. Setelah dicoba, ternyata beda fungsi. **`X-IAE-KEY`** di header Postman isinya **`KEY-MHS-117`** buat masuk ke Service C. JWT dosen dari login warga01 dipakai sebagai Bearer saat POST /nilai. **`KEY-MHS-117` + NIM `102022580023`** dipakai bareng di body request M2M ke cloud (`api_key` + `nim` owner), lewat `php artisan iae:sync-token` atau hit manual `/api/v1/auth/token`. Token hasil sync masuk `.env` buat SOAP dan RabbitMQ, bukan JWT di Postman.

NIM di body POST /nilai bisa beda (NIM mahasiswa yang dinilai). NIM di body token M2M tetap NIM owner service saya.

Contoh response sukses POST /nilai:

```json
{
  "status": "success",
  "message": "Nilai berhasil dicatat",
  "data": {
    "id": 14,
    "nim": "2099000015",
    "kode_matkul": "SI302",
    "nama_matkul": "Jaringan Komputer",
    "nilai_huruf": "A",
    "nilai_angka": 4,
    "sks": 3,
    "semester": 3,
    "tahun_ajaran": "2025/2026",
    "recorded_by": "warga01@ktp.iae.id",
    "receipt_number": "IAE-LOG-2026-828A266C",
    "event_published": true
  }
}
```

---

## 3. Integrasi SOAP Audit

Setelah nilai tersimpan, Service C kirim log audit ke Cloud Pusat lewat SOAP dengan TeamID TIM-09 dan ActivityName NilaiRecorded. Cloud Pusat balas ReceiptNumber yang saya simpan di database. Contoh isi log di dalam SOAP:

```json
{
  "nim": "2099000015",
  "kode_matkul": "SI302",
  "nama_matkul": "Jaringan Komputer",
  "nilai_huruf": "A",
  "nilai_angka": 4,
  "team_id": "TIM-09"
}
```

---

## 4. Integrasi RabbitMQ

Setelah SOAP sukses, Service C publish event nilai.recorded ke Cloud Pusat. Event muncul di https://iae-sso.virtualfri.id/board dengan badge hijau. Saya pakai nama nilai.recorded karena menurut saya lebih pas artinya nilai sudah resmi dicatat. Contoh payload dari testing:

```json
{
  "event": "nilai.recorded",
  "timestamp": "2026-06-10T04:48:48+00:00",
  "data": {
    "id": 14,
    "nim": "2099000015",
    "kode_matkul": "SI302",
    "nama_matkul": "Jaringan Komputer",
    "nilai_huruf": "A",
    "nilai_angka": 4,
    "sks": 3,
    "semester": 3,
    "tahun_ajaran": "2025/2026",
    "recorded_by": "warga01@ktp.iae.id",
    "receipt_number": "IAE-LOG-2026-828A266C",
    "team_id": "TIM-09"
  }
}
```

Format publish ke Cloud Pusat wajib ada routing_key dan wrapper message:

```json
{
  "routing_key": "nilai.recorded",
  "message": {
    "event": "nilai.recorded",
    "timestamp": "2026-06-10T04:48:48+00:00",
    "data": {
      "nim": "2099000015",
      "kode_matkul": "SI302",
      "nilai_huruf": "A",
      "receipt_number": "IAE-LOG-2026-828A266C",
      "team_id": "TIM-09"
    }
  }
}
```

Saat testing, board menampilkan pengirim dari tim saya TEAM-09.

---

## 5. Sequence Diagram

5a. Bootstrap Token Otomatis — Saya tidak isi IAE_SSO_TOKEN manual di .env. Cukup jalankan php artisan iae:sync-token dari terminal Mac, bukan lewat sail. Perintah itu ambil token dari Cloud Pusat pakai KEY-MHS-117, kalau belum aktif fallback ke warga01, lalu update otomatis ke .env. Token itu dipakai server buat SOAP dan RabbitMQ, bukan JWT dosen di Postman. Gambar: docs/images/bootstrap-token-sync.png

5b. Transaksi Kritis POST /nilai — Dosen login via Postman dapat JWT, kirim POST /api/v1/nilai dengan X-IAE-KEY dan Bearer JWT. Service C verifikasi SSO, simpan nilai ke MySQL, kirim SOAP audit, publish nilai.recorded, lalu balas 201 dengan receipt_number. Gambar: docs/images/post-nilai-flow.png

---

## 6. Cara Testing di Postman

Sebelum testing jalankan `docker compose up -d` (monorepo) lalu `php artisan iae:sync-token` dari host Mac.

**Langkah 1 — JWT dosen (bukan M2M):** POST ke cloud dengan email/password warga01, copy `token` untuk Bearer.

**Langkah 2 — cek M2M (opsional):** POST ke gateway dengan body ketentuan dosen:

```json
{
  "api_key": "KEY-MHS-117",
  "nim": "102022580023"
}
```

Kalau dapat `token_type: m2m` dan `team: TEAM-09`, API key tim aktif.

**Langkah 3:** GET `http://127.0.0.1:8080/api/v1/kurikulum` dengan header `X-IAE-KEY: KEY-MHS-117`.

**Langkah 4:** POST `http://127.0.0.1:8080/api/v1/nilai` dengan `X-IAE-KEY: KEY-MHS-117`, `Authorization: Bearer <JWT>`, dan body:

```json
{
  "nim": "2099000020",
  "kode_matkul": "SI202",
  "nama_matkul": "Basis Data",
  "nilai_huruf": "A",
  "nilai_angka": 4,
  "sks": 4,
  "semester": 2,
  "tahun_ajaran": "2025/2026"
}
```

Kalau sukses dapat 201 dengan receipt_number dan event_published true, cek board iae-sso.virtualfri.id. Error yang sering saya temui: lupa isi `nim` di body token M2M, JWT expired (login ulang), atau SOAP gagal karena belum `sync-token`.

---

## 7. Hasil Testing

Saya uji lewat Postman dan beberapa transaksi berhasil: NIM 2099000015 SI302 receipt IAE-LOG-2026-828A266C, NIM 2099000099 SI501 receipt IAE-LOG-2026-05048A23, NIM 10202250023 SI102 receipt IAE-LOG-2026-54BADCCE. Semuanya dapat receipt SOAP dan event nilai.recorded muncul di board.

---

## 8. Kesimpulan

Transaksi penting SOAP dan transaksi RabbitMQ saya pakai endpoint yang sama yaitu POST /api/v1/nilai. GET tidak dipilih karena cuma baca data. Integrasi SSO, SOAP, dan RabbitMQ sudah saya uji dan terverifikasi di Cloud Pusat.

---

## 9. Testing lewat Monorepo (Gateway port 8080)

Setelah merge ke `Tubes-IAE-Kelompok-9`, semua request Postman ke Service C lewat gateway:

```http
GET  http://127.0.0.1:8080/api/v1/kurikulum
POST http://127.0.0.1:8080/api/v1/nilai
X-IAE-KEY: KEY-MHS-117
```

POST nilai tambahkan `Authorization: Bearer <JWT warga01>`. Verifikasi nilai tersimpan bisa lewat Service A:

```http
GET http://127.0.0.1:8080/api/v1/mahasiswa/{nim}/matkul
X-API-KEY: KEY-MHS-233
```

Contoh uji 19 Juni 2026: NIM `209907170001` matkul SI101 nilai A, IPS 4.0, receipt SOAP dan event `nilai.recorded` muncul di board TEAM-09.

---

Analisis Tugas 3 Service C TIM-09 NIM 102022580023
