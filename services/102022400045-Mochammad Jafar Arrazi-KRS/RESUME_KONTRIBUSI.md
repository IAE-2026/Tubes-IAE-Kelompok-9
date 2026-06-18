# Resume Kontribusi Individu — Tugas Besar IAE

**Nama:** Mochammad Jafar Arrazi  
**NIM:** 102022400045  
**Service:** KRS-Service (Service B)  
**Kelompok:** 9  

---

## Deskripsi Tugas & Peran
Dalam pengerjaan Tugas Besar mata kuliah *Integrasi Aplikasi Enterprise* (IAE), saya bertanggung jawab atas pembangunan, pemeliharaan, integrasi, dan debugging **KRS-Service (Service B)**. Layanan ini mencatat dan memproses Kartu Rencana Studi (KRS) mahasiswa secara aman dan terintegrasi dengan komponen eksternal (Service Mahasiswa, Service Kurikulum, Central SSO, SOAP Audit Log, dan RabbitMQ Event Broker) melalui API Gateway (Nginx).

---

## Rincian Kontribusi Individu

### 1. Tugas 2 — Pembangunan Mini-Service Individu (KRS-Service)
* **Inisiasi & Arsitektur Framework:** Menginisiasi struktur project menggunakan framework **Laravel** dan melakukan containerization menggunakan **Docker & Docker Compose** (running pada port `8002`).
* **Database & Migration:** Merancang skema tabel database untuk `krs` dan `krs_details` yang mencakup field kritis seperti `nim`, `kode_mata_kuliah`, `sks`, `tahun_ajaran`, `semester`, `status_persetujuan` (pending/approved), dan `receipt_number`.
* **REST API Development:** Membangun seluruh HTTP REST endpoint di bawah prefix `/api/v1` untuk pengelolaan KRS:
  * `GET /api/v1/krs` (mengambil seluruh data KRS)
  * `GET /api/v1/krs/{id}` (detail KRS)
  * `GET /api/v1/krs/semester/{tahunAjaran}/{semester}` (filter KRS per semester)
  * `POST /api/v1/krs` (mencatat pengajuan KRS baru)
* **GraphQL Integration:** Mengintegrasikan GraphQL API (`POST /graphql` dan GUI `/graphiql`) menggunakan package Laravel GraphQL untuk memfasilitasi query data KRS secara fleksibel.
* **OpenAPI/Swagger:** Menyusun spesifikasi dokumentasi API interaktif menggunakan OpenAPI 3.0 (`public/docs/openapi.json`).
* **Validasi & Duplikasi:** Mengimplementasikan logic pencegahan duplikasi data KRS agar mahasiswa tidak dapat mengambil mata kuliah yang sama di semester yang sama secara berulang.

### 2. Tugas 3 — Pemenuhan Kepatuhan Infrastruktur Pusat (Central Compliance)
* **Federated SSO & JWT Authentication:** 
  * Mengintegrasikan login dengan Central SSO menggunakan M2M Client.
  * Mengatur *Role Mapping* lokal: Jika response SSO menyertakan atribut `NIM`, user dipetakan sebagai `mahasiswa` (diperbolehkan mengajukan KRS). Jika tidak, dipetakan sebagai `dosen` (untuk persetujuan/approval KRS).
  * Mengamankan request API menggunakan token **JWT** (`tymon/jwt-auth`).
* **SOAP Audit Log Sync:**
  * Membangun klien SOAP internal menggunakan Laravel HTTP Client untuk mengirimkan payload XML SOAP Envelope secara synchronous ke endpoint `/soap/v1/audit`.
  * Mengamankan log transaksi KRS baru (`KrsCreated`) dan persetujuan KRS (`KrsApproved`).
  * Mengekstrak tag `<iae:ReceiptNumber>` dari response XML dan menyimpannya di database sebagai bukti audit log yang valid.
* **RabbitMQ Message Broker Publisher:**
  * Mengimplementasikan event publishing ke RabbitMQ melalui gateway pusat `/api/v1/messages/publish`.
  * Mengirimkan data event dengan routing key `krs.created` dan `krs.approved` setiap kali status KRS berubah, agar layanan lain (seperti Service Kurikulum/Nilai) dapat menyinkronkan status data mereka secara asinkron.
* **Sequence Diagram & Analisis:** Merancang sequence diagram interaksi komponen lokal dengan infrastruktur pusat (SSO, SOAP, RabbitMQ).

### 3. Tugas Besar — Integrasi Lintas Service & API Gateway
* **Konfigurasi API Gateway (Nginx):** Berkolaborasi dalam penyusunan routing Nginx Gateway (`gateway/nginx.conf`) agar request eksternal ke port `8080/api/v1/krs` diteruskan dengan benar ke port kontainer `8002`.
* **Standardisasi API Key Lintas Service:**
  * Menyelaraskan seluruh API key yang digunakan untuk komunikasi antarservice (M2M) menggunakan format standar: KRS-Service menggunakan `KEY-MHS-109`.
  * Melakukan update referensi konfigurasi API Key di file `docker-compose.yml`, `config/iae.php`, `.env.example`, dan `phpunit.xml`.
* **Troubleshooting Routing & Proxy Bug:** 
  * Menemukan dan menyelesaikan bug routing `404 Not Found` pada built-in web server PHP (`php artisan serve`) ketika diakses via API Gateway. Bug disebabkan oleh PHP server yang menolak header Host dari port gateway (`8080`).
  * Solusi dilakukan dengan memodifikasi block location gateway Nginx untuk me-rewrite header Host menggunakan `$proxy_host`.
* **Troubleshooting Seeder Container (Production Mode):**
  * Memperbaiki error crash seeder database saat docker container dijalankan dengan flag `--no-dev` (tidak memasang dependensi Faker).
  * Mengubah dependency dynamic `fake()` di `UserFactory.php` menjadi array berisi static mock data agar proses seeding berjalan lancar tanpa dependency dev.

---

## Log Commit Git (Branch: `jafar`)
Berikut adalah daftar commit yang merepresentasikan kontribusi coding nyata saya pada repositori kelompok:

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


