# Log Prompt Gabungan (Kelompok 9 / TIM-09)

**Mata Kuliah:** BBK2HAB3 (Integrasi Aplikasi Enterprise)  
**Kelompok:** 9 (TIM-09)

---

## Penjelasan singkat

Dokumen ini ringkasan aja dari cara kita pakai AI pas ngerjain Tugas Besar IAE. Log asli per orang ada di folder `log-prompt-masing-masing/`. Isinya copy persis dari file masing-masing, nggak kita edit biar dosen bisa liat keasliannya.

Intinya AI kita pakai buat bantu mikir, nulis kode awal, sama debug. Menurut kami pemakaian AI kira-kira 80% buat bagian teknis, sisanya keputusan dan pengecekan manual. Semua tetap kita cek sendiri di Postman, Docker, sama board RabbitMQ sebelum dianggap bener.

---

## Andi, Service C (Nilai & Kurikulum)

**NIM:** 102022580023  
**AI yang dipake:** Antigravity (Gemini), Cursor (Claude)

Andi banyak nanya ke AI soal setup Laravel 12 dari nol: migration, Swagger, GraphQL, terus pindah-pindah antara SQLite, MySQL XAMPP, sampe akhirnya settle di Docker Sail. Pas Tugas 3, prompting-nya fokus ke SSO JWT dosen, kirim SOAP audit NilaiRecorded, publish event `nilai.recorded`, sama command `iae:sync-token` biar token M2M nggak diisi manual di `.env`.

Yang paling sering dibahas juga bedanya X-IAE-KEY (NIM Andi), KEY-MHS-117 (API key tim), sama JWT dosen. Awalnya sempat bingung dan salah isi header sampe dapet 401, baru paham setelah coba sendiri di Postman.

Pas Tugas Besar, Andi juga yang banyak koordinasi merge repo dan dokumentasi tim. Tapi analisis Tugas 3 (`analisis_tugas_3.md`) dan resume individu (`RESUME_ANDI.md`) ditulis sendiri di Modul 1-4, bukan perintah "buatkan analisis/resume" ke AI.

Log lengkap Andi: [`log-prompt-masing-masing/andi/log_prompting.md`](log-prompt-masing-masing/andi/log_prompting.md)

---

## Arneta, Service A (Data Mahasiswa)

**NIM:** 102022400136  
**AI yang dipake:** Claude AI, Cursor AI

Arneta mulai dari nol banget: migration tabel mahasiswa, middleware API Key, Swagger, sampe berbagai masalah Docker kayak connection refused pas MySQL belum ready. AI dipake buat bikin SOAP client manual (XML string, bukan library PHP-SOAP), RabbitMQ publisher, SSO login, sama fallback token kalau M2M gagal.

Pas gabungin repo kelompok, prompting-nya nambah soal Http::pool biar request ke Service B dan C jalan paralel, sama handle 404 dari Service C kalau mahasiswa baru belum punya nilai.

Log Arneta ada 3 file (beda tahap pengerjaan):

- [`AI_PROMPTING_LOG.md`](log-prompt-masing-masing/arneta/AI_PROMPTING_LOG.md) (awal project, 14 Mei 2026)
- [`AI_PROMTING_LOG_TUGAS 3.md`](log-prompt-masing-masing/arneta/AI_PROMTING_LOG_TUGAS%203.md) (Tugas 3)
- [`AI_PROMTING_TUBES.md`](log-prompt-masing-masing/arneta/AI_PROMTING_TUBES.md) (Tugas Besar / integrasi monorepo)

---

## Jafar, Service B (KRS)

**NIM:** 102022400045  
**AI yang dipake:** AI Assistant

Jafar pakai AI buat bantu paham requirement service-based architecture dari awal: tentuin domain Education System, alur KRS, endpoint apa aja yang perlu, format response JSON standar, Docker, OpenAPI, GraphQL. AI juga dipake waktu debugging route 404 dan masalah routing Laravel.

Log lengkap Jafar: [`log-prompt-masing-masing/jafar/AI_CHAT_HISTORY.md`](log-prompt-masing-masing/jafar/AI_CHAT_HISTORY.md)

---

## Kesimpulan kita

Menurut kami, AI enak buat percepat kerja teknis, tapi nggak bisa langsung dipercaya. Setiap hasil dari AI tetap kita uji ulang sendiri. Bukti nyatanya ada di log per orang di folder masing-masing, plus receipt SOAP dan event di board https://iae-sso.virtualfri.id/board pas testing berhasil.
