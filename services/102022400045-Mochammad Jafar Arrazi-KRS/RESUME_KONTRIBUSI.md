# Resume Kontribusi Individu — Tugas Besar IAE

**Nama:** Mochammad Jafar Arrazi  
**NIM:** 102022400045  
**Kelas:** BBK2HAB3 (Integrasi Aplikasi Enterprise)  
**Service:** KRS-Service (Service B)  
**Kelompok:** 9 (TIM-09)

---

Service B (KRS-Service) yang saya buat fokus mengelola pencatatan Kartu Rencana Studi (KRS) mahasiswa di sistem akademik terdistribusi. Semua data KRS — NIM, kode mata kuliah, nama mata kuliah, semester, tahun ajaran, dan status persetujuan — disimpan di database MySQL lokal milik service ini sendiri, tanpa akses langsung ke database service lain.

Fitur utama yang saya implementasikan meliputi endpoint REST untuk melihat daftar KRS, detail KRS per ID, filter per semester, serta pengajuan KRS baru oleh mahasiswa. Mahasiswa login via SSO pusat; sistem memetakan role otomatis — jika profil memiliki NIM, user diperlakukan sebagai mahasiswa yang boleh mengisi KRS; jika tidak, diperlakukan sebagai dosen yang hanya bisa memberikan persetujuan KRS. Dosen dapat menyetujui KRS melalui endpoint approve.

Untuk integrasi antarservice, saat mahasiswa mengajukan KRS (POST /api/v1/krs), Service B memvalidasi data secara HTTP ke Service A (Mahasiswa) untuk memastikan NIM terdaftar dan aktif, ke Service C (Nilai & Kurikulum) untuk validasi IPS semester lalu dan keberadaan mata kuliah di kurikulum. Validasi ini memakai Laravel HTTP Client dengan header `X-IAE-KEY`, sesuai aturan komunikasi M2M antar service.

Pada Tugas 3 (Enterprise Digital City), transaksi kritis yang saya pilih adalah POST /api/v1/krs. Setiap pengajuan KRS baru dikirim audit SOAP ke Cloud Pusat (ActivityName: KrsCreated, TeamID: TIM-09) dan mendapat ReceiptNumber sebagai bukti resmi. Setelah itu, event `krs.created` dipublish ke RabbitMQ via gateway pusat agar layanan lain mengetahui ada KRS baru tanpa coupling langsung. Saat dosen menyetujui KRS, event `krs.approved` also dipublish.

Service ini juga dilengkapi dokumentasi Swagger/OpenAPI, GraphQL query daftar KRS, Docker Compose, dan testing otomatis. Dalam monorepo kelompok, Service B terintegrasi dengan API Gateway port 8080 bersama Service A dan C sehingga alur akademik end-to-end dapat diuji dari satu repository.

---

## Log Commit Git (Branch: `jafar`)
Berikut adalah bukti kontribusi coding nyata saya pada repositori kelompok:

1. `cc3c08d` - **test: resolve key mismatch in KRS API and GraphQL tests**  
   *Memperbaiki mismatch key X-IAE-KEY pada header testing KRS API dan GraphQL agar mengambil dynamic key dari konfigurasi.*
2. `420de65` - **sisa resume masih proses**  
   *Pembaruan rekapitulasi file resume.*
3. `6bcd344` - **docs: restore original AI chat history, write informal resume, and create AI_PROMTING_TUBES.md**  
   *Mengembalikan histori AI chat, menulis draf resume informal, dan menyusun rekap log prompt AI.*
4. `8cf27e5` - **docs: remove signature section from individual resume**  
   *Menghapus kolom tanda tangan pada halaman resume kontribusi individu.*
5. `747c91d` - **docs: make AI prompt log exclusive to Tugas Besar & Tugas 3**  
   *Mensesuaikan isi berkas panduan agar fokus rekap log prompt khusus untuk Tugas Besar & Tugas 3.*
6. `81bdd5b` - **docs: update individual resume and AI chat history prompting logs**  
   *Pembaruan berkas resume kontribusi dan log chat.*
7. `3659dc7` - **fix: override Host header for krs and kurikulum services to avoid artisan serve 404 routing bugs**  
   *Menambahkan `proxy_set_header Host $proxy_host;` di konfigurasi Nginx gateway.*
8. `28f7448` - **fix: replace faker in UserFactory to avoid missing class in production/no-dev env**  
   *Menghapus dependency Faker pada seeder KRS agar seeding di Docker berjalan sukses.*
9. `fa24761` - **chore: align Service C local validation key to KEY-MHS-117**  
   *Penyelarasan API key validasi lokal di Service C (Kurikulum & Nilai).*
10. `f8f9041` - **chore: add default warga password to kurikulum-nilai .env.example**  
    *Menambahkan konfigurasi default password untuk integrasi akun warga.*
11. `7990bea` - **perubahan api key**  
    *Mengubah setting API key KRS-Service menjadi `KEY-MHS-109`.*
12. `128f9ea` - **perubahan docker-compose**  
    *Memperbarui environment variable API key & SSO credentials di docker-compose.yml.*
13. `6b1a90a` - **chore(docker): allow tests folder in docker builds**  
    *Mengubah konfigurasi `.dockerignore` agar folder tests ikut ter-build.*
14. `906f271` - **chore(config): update IAE_TEAM_ID to TEAM-09**  
    *Mengubah ID tim di config agar terdaftar sebagai TEAM-09 di server audit pusat.*
15. `a3d61ef` - **Tugas 3: Penyempurnaan Sequence Diagram, Dokumentasi Analisis, dan Implementasi**  
    *Commit lengkap integrasi Federated SSO, SOAP Audit Client, dan RabbitMQ Publisher.*
16. `6d87ebf` - **Handle duplicate KRS submissions**  
    *Validasi pencegahan record KRS ganda untuk mahasiswa yang sama pada matkul & semester yang sama.*
17. `8510149` - **Refine prompts in AI_CHAT_HISTORY.md for better clarity**  
    *Merapikan format penulisan rekap log prompt AI Tugas 2.*
18. `1672c5a` - **Revise AI Chat History for IAE Assignment 2**  
    *Inisiasi awal file rekap log prompt AI.*
19. `fdcb04d` - **Initial KRS service implementation**  
    *Pembuatan awal database schema, migration, models, controllers, OpenAPI, dan routes untuk KRS.*
