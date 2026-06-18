# Log Prompt AI — Tugas Besar & Tugas 3 IAE

**Nama:** Mochammad Jafar Arrazi
**NIM:** 102022400045
**Repository:** `102022400045_MochammadJafarArrazi-KRS-Service`
**Service:** KRS-Service (Service B)
**Kelompok:** Kelompok 9

---

## 1. Perancangan & Integrasi Kepatuhan Infrastruktur Pusat (SOAP & RabbitMQ)

### Prompt

> Saya harus mengintegrasikan KRS-Service saya dengan Central SSO, SOAP Audit Log, dan RabbitMQ pusat.
> 1. SSO menggunakan token M2M dengan caching agar token tidak di-request terus-menerus.
> 2. SOAP Audit Log memerlukan pengiriman request XML SOAP Envelope secara manual menggunakan HTTP Client Laravel karena keterbatasan pustaka SOAP bawaan. Payload harus mengandung XML tag `TeamID`, `ActivityName`, dan `LogContent` (dalam format JSON CDATA).
> 3. Setelah SOAP audit log sukses dan mengembalikan `ReceiptNumber`, kirim pesan pemberitahuan ke RabbitMQ pusat melalui HTTP gateway dengan format JSON berisi data KRS dan `ReceiptNumber` tersebut.
> Bantu saya merancang helper/service class `CentralSsoClient.php` di Laravel yang menangani ketiga fungsi tersebut.

### Hasil

AI merancang class [CentralSsoClient.php](file:///c:/Users/User/Documents/smt%204/IAE/TUBES/services/krs/app/Services/CentralSsoClient.php) dengan:
* **M2M Token Caching:** Menggunakan `Cache::remember` dengan durasi 3000 detik untuk menyimpan token JWT M2M SSO dari endpoint `/api/v1/auth/token`.
* **SOAP Client Manual:** Membentuk raw XML string SOAP Envelope dengan tag `<soap:Envelope>` dan `<iae:AuditRequest>`, memasukkan data logs ke dalam `<iae:LogContent><![CDATA[ ... ]]></iae:LogContent>`, mengirimkannya via POST ke `/soap/v1/audit`, serta melakukan parsing regex dan fallback `SimpleXMLElement` untuk mengekstrak `<iae:ReceiptNumber>`.
* **RabbitMQ HTTP Publisher:** Mengirimkan pesan JSON ke `/api/v1/messages/publish` dengan dynamic `routing_key` (`krs.created` atau `krs.approved`) serta bearer token hasil autentikasi M2M.

---

## 2. Integrasi Sistem ke Belakang API Gateway (Nginx)

### Prompt

> Kelompok saya (Kelompok 9) menggabungkan tiga microservice (Service A - Mahasiswa, Service B - KRS, Service C - Kurikulum & Nilai) ke dalam satu repositori bersama di belakang Nginx API Gateway.
> Nginx berjalan pada port 8080. KRS-Service berjalan di Docker container port 8002.
> Namun, ketika mengakses KRS-Service via API Gateway (misal `POST http://127.0.0.1:8080/api/v1/krs`), built-in web server PHP Laravel (`php artisan serve`) di dalam container melempar error `404 Not Found` atau `NotFoundHttpException` secara konsisten, meskipun route `/api/v1/krs` sudah benar.
> Mengapa hal ini terjadi dan bagaimana solusinya pada konfigurasi Nginx gateway?

### Hasil

AI menjelaskan bahwa built-in web server PHP (`artisan serve` yang berbasis PHP built-in web server) sangat sensitif terhadap header `Host` yang dikirimkan. Ketika Nginx melakukan proxy dengan memforward header `Host: localhost:8080` (port gateway), PHP server internal tidak dapat mencocokkan host tersebut dengan routing lokalnya dan menganggap request tersebut salah sehingga mengembalikan HTTP 404.

**Solusi:**
AI memberikan saran untuk memodifikasi konfigurasi Nginx Gateway ([gateway/nginx.conf](file:///c:/Users/User/Documents/smt%204/IAE/TUBES/gateway/nginx.conf)) dengan mengganti header `Host` yang diteruskan ke server internal menjadi `$proxy_host` (yaitu host internal container tanpa port gateway luar).
```nginx
location /api/v1/krs {
    proxy_pass http://krs-service:8000;
    proxy_set_header Host $proxy_host; # Solusi bug 404
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
}
```

---

## 3. Penyelarasan API Key Lintas Service

### Prompt

> Kami harus memastikan seluruh komunikasi komunikasi internal (M2M) antarservice menggunakan format API key standar `KEY-MHS-XXX`.
> Untuk KRS-Service (Service B), kami mendapatkan key `KEY-MHS-109`.
> Tolong tunjukkan bagian mana saja di file konfigurasi Laravel KRS-Service yang harus saya ubah untuk menerapkan standardisasi key ini, baik untuk validasi key yang masuk (incoming request) maupun key yang dikirim ke service lain (outgoing request).

### Hasil

AI memandu perubahan pada file-file berikut:
* [docker-compose.yml](file:///c:/Users/User/Documents/smt%204/IAE/TUBES/docker-compose.yml): Menyesuaikan environment variable `IAE_API_KEY=KEY-MHS-109` untuk `krs-service`.
* [.env.example](file:///c:/Users/User/Documents/smt%204/IAE/TUBES/services/krs/.env.example): Mengubah default placeholder ke `KEY-MHS-109`.
* [config/iae.php](file:///c:/Users/User/Documents/smt%204/IAE/TUBES/services/krs/config/iae.php): Menyelaraskan mapping pembacaan env variable `IAE_API_KEY` agar selalu terbaca sebagai key otentikasi internal.
* [phpunit.xml](file:///c:/Users/User/Documents/smt%204/IAE/TUBES/services/krs/phpunit.xml): Memastikan unit testing menggunakan key simulasi yang selaras agar testing tidak fail saat dijalankan di server lokal.

---

## 4. Debugging & Pemecahan Masalah Container (Seeder & Faker)

### Prompt

> Saat deployment kelompok di Docker, composer install dijalankan dengan flag `--no-dev` untuk mempercepat build dan keamanan production.
> Tetapi, saat menjalankan perintah `php artisan db:seed`, proses migrasi crash dengan error `Class "Faker\Factory" not found` di dalam Laravel container.
> Setelah dicek, ternyata `UserFactory.php` saya masih menggunakan function `fake()`.
> Bagaimana cara terbaik memperbaikinya tanpa harus meng-install package Faker di production environment?

### Hasil

AI menganalisis bahwa helper `fake()` Laravel bergantung pada pustaka `fakerphp/faker` yang biasanya berada di `require-dev` pada `composer.json`. Ketika di-install dengan flag `--no-dev`, pustaka tersebut tidak ada, sehingga seeder yang memanggil factory akan crash.

**Solusi:**
AI merekomendasikan untuk menghindari penggunaan helper `fake()` atau instance `Faker\Generator` di dalam Eloquent Factory yang dijalankan saat seeding awal database. Sebagai gantinya, data input seeder diubah menggunakan data statis/hardcoded yang tetap realistis, seperti:
```diff
- 'name' => fake()->name(),
- 'email' => fake()->unique()->safeEmail(),
+ 'name' => 'Mock User',
+ 'email' => 'mockuser@ktp.iae.id',
```
Perbaikan diterapkan langsung pada [UserFactory.php](file:///c:/Users/User/Documents/smt%204/IAE/TUBES/services/krs/database/factories/UserFactory.php) sehingga deployment kontainer dapat melakukan seeding database secara mulus.

---

## Kesimpulan

AI digunakan secara intensif dalam perancangan dan troubleshooting Tugas Besar ini sebagai:
1. **Asisten Integrasi Middleware & Gateway:** Mengonstruksi request SOAP manual dan routing API Gateway Nginx.
2. **Asisten Debugging Environment:** Memecahkan masalah server routing 404 dan masalah dependency packaging pada Docker container (`--no-dev`).
3. **Asisten Standardisasi Kode:** Menyelaraskan API Key komunikasi antar-layanan kelompok.

Seluruh kode hasil diskusi telah ditinjau, diuji di lingkungan lokal kelompok, dan berfungsi dengan baik.
