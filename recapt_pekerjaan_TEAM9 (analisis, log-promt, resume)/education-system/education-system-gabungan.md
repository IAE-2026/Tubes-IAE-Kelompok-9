# Analisis Education System Gabungan (Tugas 3 / TIM-09)

**Mata Kuliah:** BBK2HAB3 (Integrasi Aplikasi Enterprise)  
**Domain:** Education System  
**Cloud Pusat:** https://iae-sso.virtualfri.id

---

## Konteksnya apa sih?

Tugas 3 kita kerjain di domain **Education System**. Bayangin sistem akademik kampus yang terpisah-pisah jadi beberapa service, tapi semuanya harus lapor ke **Cloud Pusat** dosen (SSO, SOAP audit, sama papan RabbitMQ).

Aturan dari dosen yang harus kita penuhi:

1. Pilih **satu transaksi kritis**, yaitu endpoint yang benar-benar nambah atau ubah data (biasanya POST)
2. Setiap transaksi itu kirim **SOAP audit** ke cloud pusat, terus simpan `ReceiptNumber`-nya
3. Abis itu publish **event RabbitMQ** biar service lain tau ada data baru

Karena kita tim 9 punya tiga service, kami bagi tugasnya kayak gini:

**Service A (Arneta)**  
Transaksi kritis: POST /api/v1/mahasiswa  
SOAP: MahasiswaBaru  
RabbitMQ: mahasiswa.created

**Service B (Jafar)**  
Transaksi kritis: POST /api/v1/krs  
SOAP: KrsCreated  
RabbitMQ: krs.created

**Service C (Andi)**  
Transaksi kritis: POST /api/v1/nilai  
SOAP: NilaiRecorded  
RabbitMQ: nilai.recorded

Ketiganya nyambung jadi satu alur akademik: maba masuk, isi KRS, dosen approve, input nilai.

---

## Analisis per service (ringkas)

### Service A: Daftar mahasiswa baru (Arneta)

Menurut Arneta, POST `/api/v1/mahasiswa` itu transaksi paling krusial karena efek domino-nya ke service lain. SOAP dipake buat bukti resmi ke pusat (ReceiptNumber). RabbitMQ buat ngasih tau Service B & C ada maba baru, tanpa harus nunggu B dan C online dulu pas pendaftaran jalan.

Analisis lengkap Arneta: [`education-system-masing-masing/arneta/analisis_tugas_3.md`](education-system-masing-masing/arneta/analisis_tugas_3.md)

### Service B: Ajukan KRS (Jafar)

Jafar pilih POST `/api/v1/krs` karena itu titik di mana mahasiswa resmi ngajuin mata kuliah. SSO dipake buat bedain role mahasiswa vs dosen. Abis KRS ke-save, kirim SOAP plus event `krs.created` supaya pusat dan service lain pada tau.

Analisis lengkap Jafar: [`education-system-masing-masing/jafar/analisis_tugas_3.md`](education-system-masing-masing/jafar/analisis_tugas_3.md)

### Service C: Catat nilai (Andi)

Andi pilih POST `/api/v1/nilai` karena cuma endpoint itu yang nambah data nilai baru. GET kurikulum/nilai menurutnya cuma baca, jadi nggak perlu SOAP/RabbitMQ. Alurnya: dosen login SSO, POST nilai, simpan DB, SOAP audit, publish `nilai.recorded`. Token buat keluar ke cloud pusat beda lagi sama JWT dosen di Postman. Awalnya Andi juga sempat bingung soal ini, baru jelas setelah testing berkali-kali.

Analisis lengkap Andi (tulis sendiri, bukan AI generate): [`education-system-masing-masing/andi/analisis_tugas_3.md`](education-system-masing-masing/andi/analisis_tugas_3.md)

---

## Alur lengkap TIM-09 (dari sudut pandang kita)

```
Admin/dosen daftar maba
    -> Service A (SOAP + mahasiswa.created)

Mahasiswa isi KRS
    -> Service B cek ke A & C dulu
    -> Service B (SOAP + krs.created)

Dosen approve KRS
    -> Service B (krs.approved)

Dosen input nilai
    -> Service C cek ke A dulu
    -> Service C (SOAP + nilai.recorded)

Mau liat gabungan KRS + nilai
    -> Service A endpoint /mahasiswa/{nim}/matkul
```

---

## Bukti testing yang udah kita coba

Pas uji coba ke cloud pusat, beberapa transaksi kita berhasil dan muncul di board:

- Service A: receipt `IAE-LOG-2026-B377E6F5`, event `mahasiswa.created`
- Service B: receipt `IAE-LOG-2026-F92E5D18`, event `krs.created`
- Service C: receipt `IAE-LOG-2026-7C375568`, event `nilai.recorded`

---

## Kesimpulan kelompok

Menurut kami, ketiga service TIM-09 saling melengkapi di domain Education System. Service A buka identitas mahasiswa, Service B atur rencana studi, Service C tutup siklus dengan nilai. SOAP buat bukti resmi ke pusat. RabbitMQ buat ngasih tau service lain tanpa bikin proses utama jadi lambat atau error kalau service lain lagi mati sementara.

Endpoint GET sengaja nggak kita jadiin transaksi kritis karena menurut kami cuma baca data, nggak nambah apa-apa ke sistem.

*Ringkasan gabungan ini AI bantu susun dari analisis asli masing-masing anggota. Isi analisis per orang tetap ada di subfolder `education-system-masing-masing/`.*
