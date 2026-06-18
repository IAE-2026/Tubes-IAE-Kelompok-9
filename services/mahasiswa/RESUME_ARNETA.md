# Resume Individu — Tugas Besar IAE

**Nama:** Arneta Alifiana  
**NIM:** 102022400136  
**Kelas:** BBK2HAB3 (Integrasi Aplikasi Enterprise)  
**Service:** Service A - Data Mahasiswa  

---

Service A yang aku buat ini intinya fokus untuk mengelola seluruh data profil mahasiswa di sistem akademik kita secara terpusat. Di dalam service ini, semua data penting kayak NIM, nama, email kampus, program studi, tahun angkatan, sama status keaktifan mahasiswa disimpan di database lokal. Selain mengelola data profil, service ini juga punya fitur untuk mendaftarkan mahasiswa baru, mencari data mahasiswa spesifik berdasarkan NIM, dan menghandle login SSO yang terhubung langsung ke server pusat dosen supaya user bisa login pake akun terfederasi dan rolenya otomatis terpetakan di database lokal kita.

Untuk integrasinya dengan service lain, Service A ini saling terhubung erat dengan Service B yang mengelola KRS dan Service C yang mengelola kurikulum dan nilai. Hubungan pertamanya adalah pas mahasiswa mau ngisi KRS di Service B atau pas dosen mau nginput nilai di Service C, kedua service itu bakal otomatis ngirim request ke Service A untuk ngecek apakah NIM mahasiswa yang bersangkutan beneran terdaftar dan statusnya masih aktif atau enggak di database. Selain itu, Service A juga punya endpoint agregasi khusus yang bisa ngambil data KRS aktif dari Service B dan riwayat nilai dari Service C secara bersamaan (paralel) lewat HTTP pool, jadi data dari ketiga service tersebut bisa langsung digabungin dan ditampilkan dalam satu respon cepat tanpa perlu nunggu antrean request satus-satu.
