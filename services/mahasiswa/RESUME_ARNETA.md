# Resume Individu dan Kontribusi - Tugas Besar IAE

Nama: Arneta Alifiana  
NIM: 102022400136  
Kelas: BBK2HAB3  
Service: Service A - Data Mahasiswa  

---

## Penjelasan Singkat Service A
Service A yang aku buat ini tugas utamanya untuk mengelola seluruh data profil mahasiswa di database lokal kita. Di service ini, aku bikin fitur untuk menyimpan data mahasiswa (seperti NIM, nama, email, prodi, angkatan, status aktif), mendaftarkan mahasiswa baru, dan juga menyediakan fitur login SSO terfederasi biar user bisa login pake akun SSO pusat.

## Integrasi Service A dengan Server Lain
Service A ini terhubung ke beberapa server dan layanan lain, yaitu:
1. SSO Server Pusat: Untuk memvalidasi login user dan mengambil token M2M untuk keperluan autentikasi sistem.
2. SOAP Audit Server Pusat: Setiap kali ada mahasiswa baru yang berhasil didaftarkan, Service A akan mengirimkan laporan audit berbentuk XML secara manual ke server pusat untuk mendapatkan Receipt Number resmi sebagai tanda legal.
3. RabbitMQ: Digunakan untuk menyebarkan event mahasiswa.created secara asinkron setelah pendaftaran maba sukses, biar Service B (KRS) dan Service C (Nilai) tahu ada data mahasiswa baru tanpa mengganggu kinerja server kita.
4. Service B dan Service C: Digunakan untuk validasi data mahasiswa (apakah aktif/terdaftar) saat mereka mengisi KRS atau dosen menginput nilai. Di Service A juga ada fitur agregasi untuk mengambil data KRS aktif dari Service B dan riwayat nilai dari Service C secara paralel biar loading-nya cepat, lengkap dengan penanganan fallback agar tidak error jika mahasiswanya belum punya nilai.

## Kontribusi Aku di Tugas Besar Kelompok 9
Selama mengerjakan tugas besar ini, ini beberapa hal penting yang aku kerjakan:
* Membuat Service A (Data Mahasiswa) dari awal menggunakan Laravel 12 dan MySQL.
* Mengatur konfigurasi Docker Compose kelompok biar semua service dan database bisa dinyalakan bersamaan dengan mudah, termasuk memperbaiki error database connection refused di Docker.
* Mengatur konfigurasi Nginx API Gateway kelompok agar semua rute API dari service kelompok kami bisa diakses lewat satu port yang sama (port 8080).
* Membantu menyelaraskan konfigurasi Team ID kelompok (TEAM-09) di service teman-teman sekelompok agar semuanya seragam.
* Mengimplementasikan login SSO pusat dan memetakan role user secara otomatis berdasarkan pola email mereka.
* Bikin custom SOAP client secara manual dengan XML untuk audit log, mengambil receipt number-nya pake regex, serta membuat publisher event RabbitMQ.
* Mengoptimalkan performa halaman agregasi data mahasiswa dengan parallel request, serta menambahkan fallback token agar backend tetap berjalan stabil walau koneksi ke SSO pusat terputus.


