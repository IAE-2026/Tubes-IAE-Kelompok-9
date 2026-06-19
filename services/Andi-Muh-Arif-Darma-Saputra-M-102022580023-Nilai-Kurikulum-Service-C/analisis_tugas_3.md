# Analisis Tugas 3 — Service C Kurikulum dan Nilai

Mahasiswa: Andi Muh. Arif Darma Saputra M  
NIM: 102022580023  
Tim Lab: TIM-09  
Layanan: Service C Validasi Prasyarat dan Kurikulum  
Cloud Pusat: https://iae-sso.virtualfri.id

---

## 1. Justifikasi Transaksi Kritis

Service C punya tiga endpoint utama. Yang saya pakai buat Tugas 3 cuma POST /api/v1/nilai, karena endpoint itu yang benar-benar nambah data nilai baru ke database. GET /api/v1/kurikulum dan GET /api/v1/nilai cuma baca data, jadi menurut saya tidak perlu audit SOAP maupun broadcast RabbitMQ.

Untuk transaksi penting SOAP, saya pilih POST /api/v1/nilai. Setiap nilai dicatat, Service C kirim audit ke Cloud Pusat dan dapat ReceiptNumber sebagai bukti. Contoh dari testing saya: IAE-LOG-2026-828A266C. Untuk transaksi yang harus disebar lewat RabbitMQ, saya pakai endpoint yang sama. Setelah audit SOAP selesai, Service C publish event nilai.recorded ke Cloud Pusat supaya service lain tahu ada nilai baru. Alurnya: login SSO, POST /nilai, simpan DB, audit SOAP, publish RabbitMQ, lalu response sukses.

---

## 2. Token, Key, dan SSO

Sebelum input nilai, user harus login ke Cloud Pusat dulu dan dapat JWT. Service C verifikasi JWT itu, lalu cek role lokal. Yang boleh input nilai cuma dosen dan admin. Akun warga01@ktp.iae.id saya mapping sebagai dosen buat testing di Postman.

Awalnya saya juga bingung karena kayanya banyak key sekaligus. Setelah dicoba, ternyata beda fungsi. X-IAE-KEY itu bukan token, cuma NIM saya 102022580023 di header setiap request ke Service C. JWT dosen dari login warga01, dipakai sebagai Bearer saat POST /nilai, dan tidak diambil dari .env. KEY-MHS-117 adalah API Key tim, dipakai server lewat perintah php artisan iae:sync-token buat dapat IAE_SSO_TOKEN di .env. Token .env itu yang dipakai Service C saat kirim SOAP dan RabbitMQ ke Cloud Pusat.

X-IAE-KEY tidak bisa diganti KEY-MHS-117 meskipun sama-sama disebut key. Middleware CheckIaeKey di Service C cuma terima NIM 102022580023. Kalau di Postman isi KEY-MHS-117, langsung 401. NIM di header X-IAE-KEY juga beda dengan NIM di body POST /nilai. Header tetap NIM saya, body bisa NIM mahasiswa lain misalnya 2099000020. Jadi layer masuk ke Service C pakai X-IAE-KEY plus JWT, layer keluar ke Cloud Pusat pakai token dari sync-token.

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

Sebelum testing jalankan ./vendor/bin/sail up -d lalu php artisan iae:sync-token dari host. Langkah 1, POST ke https://iae-sso.virtualfri.id/api/v1/auth/token dengan body email warga01@ktp.iae.id dan password akun lab, copy field token untuk Bearer. Langkah 2 opsional, POST ke URL yang sama dengan body api_key KEY-MHS-117, kalau dapat token_type m2m dan team TEAM-09 berarti API Key aktif. Langkah 3, GET http://localhost:8000/api/v1/kurikulum dengan header X-IAE-KEY 102022580023 saja. Langkah 4, POST http://localhost:8000/api/v1/nilai dengan header Content-Type application/json, X-IAE-KEY 102022580023, Authorization Bearer JWT, dan body:

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

Kalau sukses dapat 201 dengan receipt_number dan event_published true, cek board iae-sso.virtualfri.id. Error yang sering saya temui: X-IAE-KEY salah kalau isi KEY-MHS-117, harus NIM 102022580023. JWT expired ulang login. SOAP gagal biasanya token .env expired, jalankan sync-token lagi.

---

## 7. Hasil Testing

Saya uji lewat Postman dan beberapa transaksi berhasil: NIM 2099000015 SI302 receipt IAE-LOG-2026-828A266C, NIM 2099000099 SI501 receipt IAE-LOG-2026-05048A23, NIM 10202250023 SI102 receipt IAE-LOG-2026-54BADCCE. Semuanya dapat receipt SOAP dan event nilai.recorded muncul di board.

---

## 8. Kesimpulan

Transaksi penting SOAP dan transaksi RabbitMQ saya pakai endpoint yang sama yaitu POST /api/v1/nilai. GET tidak dipilih karena cuma baca data. Integrasi SSO, SOAP, dan RabbitMQ sudah saya uji dan terverifikasi di Cloud Pusat.

---

Analisis Tugas 3 Service C TIM-09 NIM 102022580023
