# Resume Kontribusi Individu — Tugas Besar IAE

**Nama:** Mochammad Jafar Arrazi  
**NIM:** 102022400045  
**Service:** KRS-Service (Service B)  
**Kelompok:** 9  

Berikut adalah ringkasan kontribusi nyata yang saya lakukan pada repositori bersama kelompok (`Tubes-IAE-Kelompok-9`) berdasarkan log commit git:

## 1. Daftar Commit & Kontribusi Kode

| Commit Hash | Judul Commit | Deskripsi Kontribusi |
| :--- | :--- | :--- |
| `906f271` | `chore(config): update IAE_TEAM_ID to TEAM-09` | Menyelaraskan konfigurasi ID Tim menjadi `TEAM-09` pada environment integrasi SSO. |
| `a3d61ef` | `Tugas 3: Penyempurnaan Sequence Diagram, Dokumentasi Analisis, dan Implementasi` | Melakukan penyempurnaan sequence diagram untuk alur pencatatan KRS, menyusun dokumen `analisis_tugas_3.md`, serta memperhalus implementasi integrasi 3 lapis (SSO JWT, SOAP Audit, dan RabbitMQ). |
| `6d87ebf` | `Handle duplicate KRS submissions` | Menambahkan pengecekan duplikasi pada controller KRS (`KrsController`) sehingga mahasiswa tidak dapat mengajukan mata kuliah yang sama di tahun ajaran dan semester yang sama. |
| `8510149` | `Refine prompts in AI_CHAT_HISTORY.md for better clarity` | Merapikan format rekap prompt AI agar lebih mudah dipahami dan sesuai format yang diwajibkan. |
| `1672c5a` | `Revise AI Chat History for IAE Assignment 2` | Mengompilasi dan mengedit riwayat prompt bantuan AI untuk pengerjaan service KRS (Tugas 2). |
| `fdcb04d` | `Initial KRS service implementation` | Menginisialisasi keseluruhan codebase **KRS-Service** berbasis Laravel (REST API `/api/v1/krs`, GraphQL `/graphql`, skema database migration, dan konfigurasi `Dockerfile` / `docker-compose.yml`). |
| `3427a93` | `Delete .gitattributes` | Pembersihan konfigurasi repository yang tidak diperlukan. |

---

## 2. Rincian Pekerjaan & Fitur Utama yang Dikerjakan

1. **Inisialisasi & Setup Project (Tugas 2):**
   * Mengatur struktur awal Laravel, database migration untuk tabel `krs`, model `Krs`, dan seeding data awal.
   * Menyiapkan endpoint REST API (`GET /api/v1/krs`, `POST /api/v1/krs`, `GET /api/v1/krs/{id}`) dan GraphQL.
   * Mengatur dockerisasi service menggunakan `Dockerfile` khusus.

2. **Integrasi Antar-Service:**
   * Menghubungkan KRS-Service secara internal dengan **Service A (Mahasiswa)** dan **Service C (Kurikulum-Nilai)** untuk melakukan validasi NIM aktif, kelayakan IPS, dan kode mata kuliah secara real-time via HTTP Client.

3. **Kepatuhan Infrastruktur Pusat (Tugas 3):**
   * **SSO JWT & Multi-role Authorization:** Mengintegrasikan JWKS dari SSO pusat untuk mendecode JWT Token dan membagi otorisasi pengguna (`mahasiswa` hanya bisa mengisi KRS, `dosen` hanya bisa melakukan persetujuan).
   * **SOAP Audit Log:** Mengimplementasikan pengiriman data SOAP (XML Envelope) secara rigid ke server pusat `/soap/v1/audit` dan mengekstrak `ReceiptNumber` yang kemudian disimpan di database lokal.
   * **RabbitMQ Message Broker:** Mengirimkan event `krs.created` dan `krs.approved` ke antrean RabbitMQ pusat lewat gateway HTTP `/api/v1/messages/publish` setelah audit log sukses.

4. **Dokumentasi & Pengujian:**
   * Menyusun Sequence Diagram aliran internal pendaftaran KRS.
   * Membuat file unit/feature testing (`KrsApiTest.php`, `Tugas3Test.php`, `GraphqlTest.php`) untuk memastikan seluruh alur transaksi berjalan lancar.
