# Resume Individu (Tugas Besar IAE)

**Nama:** Andi Muh. Arif Darma Saputra M  
**NIM:** 102022580023  
**Kelas:** BBK2HAB3 (Integrasi Aplikasi Enterprise)  
**Service:** Service C (Nilai & Kurikulum)  
**Kelompok:** 9 (TIM-09)

---

Service C saya fokus ke data kurikulum prodi dan pencatatan nilai mahasiswa. Menurut saya ini bagian penting di sistem akademik karena Service B (KRS) butuh cek IPS dan matkul valid, sedangkan dosen butuh tempat resmi buat input nilai.

Di service ini saya simpan data kurikulum (kode matkul, SKS, semester, prasyarat) dan riwayat nilai di MySQL lokal. Endpoint yang saya buat antara lain GET kurikulum, GET nilai per NIM (plus hitung IPS), dan POST nilai oleh dosen. Saya juga tambahin GraphQL (Lighthouse + GraphiQL) dan Swagger biar API gampang diuji sama teman-teman pas integrasi.

Soal keamanan, setiap request ke Service C lewat middleware `CheckIaeKey` yang cek header `X-IAE-KEY` (NIM saya: 102022580023). Untuk POST /nilai, ada tambahan `VerifyJwtSso` supaya cuma dosen/admin yang punya JWT dari cloud pusat yang boleh input nilai.

Integrasi ke service lain: waktu dosen catat nilai, Service C ngecek dulu ke Service A apakah NIM mahasiswa valid dan statusnya aktif. Service B juga panggil Service C pas mahasiswa isi KRS, buat validasi IPS dan kurikulum. Ke cloud pusat (https://iae-sso.virtualfri.id), transaksi POST /nilai saya hubungin ke SOAP audit (ActivityName: NilaiRecorded, TeamID: TIM-09) dan event RabbitMQ `nilai.recorded`. Token M2M buat SOAP/RabbitMQ saya sync lewat `php artisan iae:sync-token` pakai KEY-MHS-117, terpisah dari JWT dosen di Postman (awalnya saya juga sempat bingung bedain keduanya).

Pas gabung ke monorepo kelompok, Service C jalan bareng Service A dan B lewat API Gateway port 8080. Jadi alur akademik TIM-09 bisa kita uji end-to-end dari satu repo, bukan cuma service saya sendiri.

Di Tugas Besar saya juga ikut harmonisasi integrasi tim: mapping header auth per service di gateway, selaraskan token M2M `{ api_key, nim }`, dan bantu debug alur POST nilai lewat gateway (wajib `X-IAE-KEY: KEY-MHS-117` plus Bearer JWT warga01). Untuk lihat hasil nilai dari sisi mahasiswa, Service A sediakan `GET /api/v1/mahasiswa/{nim}/matkul` yang menggabungkan KRS dan nilai dalam satu response.

*Catatan: resume ini saya tulis sendiri berdasarkan pekerjaan Tugas 3 dan integrasi Tugas Besar. AI saya pakai mostly buat coding, bukan buat nulis resume ini dari nol.*
