# Resume Individu — Tugas Besar IAE

**Nama:** Andi Muh. Arif Darma Saputra M  
**NIM:** 102022580023  
**Kelas:** BBK2HAB3 (Integrasi Aplikasi Enterprise)  
**Service:** Service C — Validasi Prasyarat & Kurikulum  
**Kelompok:** 9 (TIM-09)

---

Service C yang saya kerjakan fokus mengelola data kurikulum program studi dan pencatatan nilai mahasiswa di sistem akademik terdistribusi. Di dalam service ini, data kurikulum (kode mata kuliah, SKS, semester, prasyarat) dan riwayat nilai mahasiswa disimpan di database MySQL lokal. Service ini menyediakan endpoint REST untuk melihat kurikulum, melihat nilai per NIM (termasuk perhitungan IPS), serta mencatat nilai baru oleh dosen. Selain REST, saya juga menambahkan GraphQL (Lighthouse + GraphiQL) dan dokumentasi Swagger agar API mudah diuji dan diintegrasikan.

Untuk keamanan, setiap request ke Service C dilindungi middleware `CheckIaeKey` yang memvalidasi header `X-IAE-KEY`. Endpoint POST /nilai tambahan memverifikasi JWT SSO dosen/admin lewat middleware `VerifyJwtSso`, sehingga hanya pengguna berwenang yang bisa input nilai.

Integrasi dengan service lain: saat dosen mencatat nilai, Service C memvalidasi status mahasiswa ke Service A (Mahasiswa) untuk memastikan NIM terdaftar dan aktif. Service B (KRS) juga memanggil Service C untuk validasi IPS dan riwayat nilai saat mahasiswa mengisi KRS. Di sisi Cloud Pusat (https://iae-sso.virtualfri.id), transaksi kritis POST /nilai terhubung ke SOAP audit (ActivityName: NilaiRecorded, TeamID: TIM-09) dan RabbitMQ event `nilai.recorded`. Token M2M untuk SOAP/RabbitMQ di-sync otomatis lewat perintah `php artisan iae:sync-token` menggunakan API Key KEY-MHS-117, terpisah dari JWT dosen di Postman.

Pada integrasi monorepo kelompok, Service C berjalan di Docker bersama Service A dan B lewat API Gateway port 8080, sehingga seluruh ekosistem akademik TIM-09 dapat diuji end-to-end dari satu repository.
