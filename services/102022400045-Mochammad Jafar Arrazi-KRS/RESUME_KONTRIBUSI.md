# Resume Kontribusi Individu — Tugas Besar IAE

**Nama:** Mochammad Jafar Arrazi  
**NIM:** 102022400045  
**Service:** KRS-Service (Service B)  
**Kelompok:** 9  

---

Service B (KRS-Service) yang aku buat ini intinya fokus untuk mengelola seluruh data pencatatan Kartu Rencana Studi (KRS) mahasiswa di sistem akademik kita. Di dalam service ini, semua data penting kayak NIM, kode mata kuliah, nama mata kuliah, jumlah SKS, tahun ajaran, semester, status persetujuan (apakah disetujui dosen atau masih pending), sama receipt number dari audit log disimpan di database lokal kita. Selain mengelola data krs, service ini juga punya fitur buat mahasiswa mengajukan KRS baru, memfilter KRS berdasarkan semester atau NIM tertentu, dan mengelola persetujuan (approval) KRS oleh dosen.

Untuk integrasinya dengan service lain, Service B ini terhubung langsung ke Service A (Mahasiswa) untuk validasi data mahasiswa dan Service C (Kurikulum & Nilai) untuk validasi mata kuliah. Pas mahasiswa mau ngisi KRS di Service B, sistem kita bakal otomatis ngirim request ke Service A buat ngecek apakah NIM mahasiswa yang bersangkutan beneran terdaftar dan statusnya aktif. Kita juga ngirim request ke Service C buat validasi apakah kode mata kuliah yang diambil itu valid, sesuai kurikulum, dan ngambil info nama mata kuliah beserta jumlah SKS-nya. Semua proses ini berjalan secara otomatis di belakang layar lewat komunikasi HTTP Client yang aman pake API Key masing-masing service.

Selain komunikasi antar-service internal kelompok, Service B juga punya integrasi wajib dengan infrastruktur pusat. Pertama, kita pake akun SSO terfederasi (`warga21@ktp.iae.id`) buat login dosen/mahasiswa dan ngeluarin JWT token biar request ke API kita aman. Kedua, setiap kali ada KRS baru yang dibuat atau disetujui dosen, Service B bakal ngirim data audit log dalam format XML SOAP Envelope ke server pusat (`/soap/v1/audit`) dan ngambil `ReceiptNumber` unik buat disimpan ke database sebagai bukti transaksi. Ketiga, setelah resi disimpan, service kita langsung mempublikasikan event `krs.created` atau `krs.approved` ke antrian RabbitMQ pusat melalui HTTP gateway biar service lainnya tau kalau ada perubahan status KRS secara real-time.

Di bagian integrasi akhir (Tubes), aku juga nanganin setup API Gateway (Nginx) biar traffic eksternal bisa di-routing dengan bener ke port service kita (`8002`). Pas integrasi sempet ada kendala server internal Laravel (`php artisan serve`) ngasih error 404 karena masalah header Host dari port gateway (`8080`), tapi berhasil diselesaikan dengan konfigurasi `proxy_set_header Host $proxy_host` di Nginx gateway. Selain itu, aku juga benerin error database seeder di production container yang crash gara-gara helper `fake()` (faker library) ga ke-install di environment production (`--no-dev`), jadi aku ganti pake data mock statis di `UserFactory.php` biar build docker-nya lancar jaya.

---

## Log Commit Git (Branch: `jafar`)
Berikut adalah bukti kontribusi coding nyata saya pada repositori kelompok:

1. `56b09d9` - **fix: override Host header for krs and kurikulum services to avoid artisan serve 404 routing bugs**  
   *Menambahkan `proxy_set_header Host $proxy_host;` di konfigurasi Nginx gateway.*
2. `28f7448` - **fix: replace faker in UserFactory to avoid missing class in production/no-dev env**  
   *Menghapus dependency Faker pada seeder KRS agar seeding di Docker berjalan sukses.*
3. `fa24761` - **chore: align Service C local validation key to KEY-MHS-117**  
   *Penyelarasan API key validasi lokal di Service C (Kurikulum & Nilai).*
4. `f8f9041` - **chore: add default warga password to kurikulum-nilai .env.example**  
   *Menambahkan konfigurasi default password untuk integrasi akun warga.*
5. `7990bea` - **perubahan api key**  
   *Mengubah setting API key KRS-Service menjadi `KEY-MHS-109`.*
6. `128f9ea` - **perubahan docker-compose**  
   *Memperbarui environment variable API key & SSO credentials di docker-compose.yml.*
7. `6b1a90a` - **chore(docker): allow tests folder in docker builds**  
   *Mengubah konfigurasi `.dockerignore` agar folder tests ikut ter-build.*
8. `906f271` - **chore(config): update IAE_TEAM_ID to TEAM-09**  
   *Mengubah ID tim di config agar terdaftar sebagai TEAM-09 di server audit pusat.*
9. `a3d61ef` - **Tugas 3: Penyempurnaan Sequence Diagram, Dokumentasi Analisis, dan Implementasi**  
   *Commit lengkap integrasi Federated SSO, SOAP Audit Client, dan RabbitMQ Publisher.*
10. `6d87ebf` - **Handle duplicate KRS submissions**  
    *Validasi pencegahan record KRS ganda untuk mahasiswa yang sama pada matkul & semester yang sama.*
11. `8510149` - **Refine prompts in AI_CHAT_HISTORY.md for better clarity**  
    *Merapikan format penulisan rekap log prompt AI Tugas 2.*
12. `1672c5a` - **Revise AI Chat History for IAE Assignment 2**  
    *Inisiasi awal file rekap log prompt AI.*
13. `fdcb04d` - **Initial KRS service implementation**  
    *Pembuatan awal database schema, migration, models, controllers, OpenAPI, dan routes untuk KRS.*
