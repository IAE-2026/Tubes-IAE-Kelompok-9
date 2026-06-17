A. Analisis Transaksi Kritis & Alur Integrasi

Dalam pengerjaan KRS-Service pada Tugas 3 ini, saya menganalisis satu transaksi yang menurut sya kritis dan berpengaruh pada data akademik mahasiswa yaitu Pencatatan KRS Baru oleh Mahasiswa. Transaksi ini adalah alur proses saat mahasiswa mengajukan mata kuliah yang ingin diambil pada semester tertentu. Dalam transaksi ini, sistem menggunakan pembagian peran hanya untuk lokal, untuk mengatur hak akses pengguna. Saat login dengan akun SSO pusat, sistem otomatis membaca data profil, jika memiliki "NIM", pengguna didaftarkan sebagai "mahasiswa" dan diizinkan mengisi KRS. kalau tidak memiliki "NIM", pengguna dianggap "dosen" dan hanya bisa memberikan persetujuan KRS.

Bagaimana Sistem Saya Terintegrasi dengan Sistem Pusat (SSO Dosen)?
Untuk menjaga agar data dengan server pusat tetap sejalan, saya menggunakan dua jalur yang saling terintegrasi satu sama lain:
1. SOAP Audit Log: Setiap kali ada pengajuan KRS baru, sistem saya akan mengirimkan data laporan ke server pusat "/soap/v1/audit" menggunakan format XML (SOAP). Setelah berhasil, server pusat akan mengembalikan nomor resi unik berupa "ReceiptNumber" yang selanjutnya disimpan ke dalam database sebagai bukti transaksi.
2. RabbitMQ Event Publisher: setelah resi SOAP berhasil disimpan, sistem akan mengirim pesan pemberitahuan ke antrian RabbitMQ pusat melalui HTTP gateway "/api/v1/messages/publish". Pesan ini dikirim menggunakan tanda "routing key" 
"krs.created" saat KRS dibuat, agar sistem atau layanan lain tahu bahwa ada data KRS yang baru.

B. Sequence Diagram Internal

Berikut adalah "sequence diagram" yang menggambarkan alur data di dalam sistem yang saya buat, mulai dari proses login, verifikasi keamanan token JWT, hingga pengiriman bukti transaksi ke sistem pusat (SOAP dan RabbitMQ):
(sequenceDiagram saya letakkan di file terpisah berbentuk PNG dengan nama "sequence diagram pencatatan KRS.png")

