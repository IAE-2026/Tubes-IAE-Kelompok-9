# Resume Kontribusi Individu — Tugas Besar IAE

**Nama:** Mochammad Jafar Arrazi  
**NIM:** 102022400045  
**Service:** KRS-Service (Service B)  
**Kelompok:** 9  

---
di pengerjaan tugas besar ini, saya dapet tanggung jawab untuk bagian KRS service yang berfungsi sebagai layanan utama untuk pencatatan dan kellola krs, semua data krs disimpan di databse, yang isinya identitas mahasiswa, matkul yang diambil, jumlah krs, tahun ajaran, status persetujuan, serta nomor bukti audit transaksinya.

saya juga membuat fitur" utama yang memungkinkan mahasiswa mengajukan krs, melihat data krs yg diajukan, serta melakukan pencarian berdasrakan semester maupun id mahasiswa, lalu saya juga mengembangkan di bagian persetujuan krs sehingga dosen dapat melakukan proses vaidasi dan persetujuan untuk pengajuan yang masuk. 
dan untuk konsistensi datanya saya menambahkan validasi agar mahasiswa tidak bisa mengajukan mata kuliah yang sama lebih dari satu kali di semester yang sama

dan untuk integrasi dengan service lain, service saya terhubung langsung ke service A yaitu data mahasiswa dan service C yaitu kurikulum dan nilai untuk validasi matkul, saat mahasiswa ngisi di B, sistem bakal otomatis ngirim request ke A untuk cek apakah nim yg bersangkutan ada dan aktif, B juga ngirim request  ke C untuk validasi apakah kode mata kuliah yg diambil itu valid sesuai kurikulum dan ambil info nama matkul beserta jumlah sksnya, semua otomatis di belakang layar lewat  http client yang api key masing"

### Log Commit Kontribusi
Berikut adalah daftar log commit kontribusi saya pada repositori tugas besar ini:
- test: resolve key mismatch in KRS API and GraphQL tests
- sisa resume masih proses
- docs: restore original AI chat history, write informal resume, and create AI_PROMTING_TUBES.md
- docs: remove signature section from individual resume
- docs: make AI prompt log exclusive to Tugas Besar & Tugas 3
- docs: update individual resume and AI chat history prompting logs
- fix: override Host header for krs and kurikulum services to avoid artisan     serve 404 routing bugs
- fix: replace faker in UserFactory to avoid missing class in production/no-dev env
- chore: align Service C local validation key to KEY-MHS-117
- chore: add default warga password to kurikulum-nilai .env.example
- perubahan api key
- perubahan docker-compose
- chore(docker): allow tests folder in docker builds
- docs: update resume kontribusi
- perubahan resuma pada file services/krs/RESUME_KONTRIBUSI.md
- Integrasikan 3 microservice lewat API Gateway dengan end-to-end flow lintas service
