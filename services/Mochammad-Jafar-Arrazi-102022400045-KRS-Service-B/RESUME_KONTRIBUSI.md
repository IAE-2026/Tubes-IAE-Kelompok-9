# Resume Kontribusi Individu — Tugas Besar IAE

**Nama:** Mochammad Jafar Arrazi  
**NIM:** 102022400045  
**Kelas:** BBK2HAB3 (Integrasi Aplikasi Enterprise)  
**Service:** KRS-Service (Service B)  
**Kelompok:** 9 (TIM-09)

---

Service B (KRS-Service) yang saya buat fokus mengelola pencatatan Kartu Rencana Studi (KRS) mahasiswa di sistem akademik terdistribusi. Semua data KRS — NIM, kode mata kuliah, nama mata kuliah, semester, tahun ajaran, dan status persetujuan — disimpan di database MySQL lokal milik service ini sendiri, tanpa akses langsung ke database service lain.

Fitur utama yang saya implementasikan meliputi endpoint REST untuk melihat daftar KRS, detail KRS per ID, filter per semester, serta pengajuan KRS baru oleh mahasiswa. Mahasiswa login via SSO pusat; sistem memetakan role otomatis — jika profil memiliki NIM, user diperlakukan sebagai mahasiswa yang boleh mengisi KRS; jika tidak, diperlakukan sebagai dosen yang hanya bisa memberikan persetujuan KRS. Dosen dapat menyetujui KRS melalui endpoint approve.

Untuk integrasi antarservice, saat mahasiswa mengajukan KRS (POST /api/v1/krs), Service B memvalidasi data secara HTTP ke Service A (Mahasiswa) untuk memastikan NIM terdaftar dan aktif, ke Service C (Kurikulum & Nilai) untuk validasi IPS semester lalu dan keberadaan mata kuliah di kurikulum. Validasi ini memakai Laravel HTTP Client dengan header `X-IAE-KEY`, sesuai aturan komunikasi M2M antar service.

Pada Tugas 3 (Enterprise Digital City), transaksi kritis yang saya pilih adalah POST /api/v1/krs. Setiap pengajuan KRS baru dikirim audit SOAP ke Cloud Pusat (ActivityName: KrsCreated, TeamID: TIM-09) dan mendapat ReceiptNumber sebagai bukti resmi. Setelah itu, event `krs.created` dipublish ke RabbitMQ via gateway pusat agar layanan lain mengetahui ada KRS baru tanpa coupling langsung. Saat dosen menyetujui KRS, event `krs.approved` juga dipublish.

Service ini juga dilengkapi dokumentasi Swagger/OpenAPI, GraphQL query daftar KRS, Docker Compose, dan testing otomatis. Dalam monorepo kelompok, Service B terintegrasi dengan API Gateway port 8080 bersama Service A dan C sehingga alur akademik end-to-end dapat diuji dari satu repository.
