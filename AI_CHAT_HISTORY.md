# AI Chat History

File ini berisi rekap prompting dan keputusan desain saat pembuatan service IAE Tugas 2.

## Prompt Awal

User meminta bantuan desain dan implementasi project service-based untuk IAE Assignment 2 dengan ketentuan:

- Setiap mahasiswa membuat repository service masing-masing.
- Service berjalan mandiri dengan Docker.
- Komunikasi antarservice melalui HTTP endpoint.
- Tidak boleh mengakses database service lain secara langsung.
- REST endpoint menggunakan prefix `/api/v1`.
- Semua endpoint memakai JSON dan header `X-IAE-KEY`.
- Response mengikuti Standard Integration Contract.
- Service menyediakan Swagger/OpenAPI, GraphQL, test, Dockerfile, docker-compose, README, `.env.example`, migration/schema, dan riwayat chat AI.

## Klarifikasi

Informasi yang diberikan user:

- Domain: Education System.
- Proses bisnis: Dosen melakukan persetujuan KRS semester baru.
- Service tanggung jawab user: Pencatatan KRS mahasiswa.
- NIM/API key: `102022400045`.
- Framework: Laravel.
- Bahasa response dan dokumentasi: Indonesia.
- Nama: Mochammad Jafar Arrazi.

## Keputusan Desain

- Repository: `102022400045_MochammadJafarArrazi-KRS-Service`.
- Service name: `KRS-Service`.
- Database: MySQL.
- Host port: `8002`.
- Docker service name: `krs-service`.
- Shared Docker network: `iae-network`.
- Resource utama: `krs`.
- Status persetujuan dosen disimpan pada field `status_persetujuan` dengan default `pending`.

## Integrasi Antarservice

Saat mencatat KRS, Service B memanggil:

- Service A Mahasiswa untuk validasi mahasiswa aktif.
- Service C Nilai untuk validasi IPS semester lalu.
- Service C Kurikulum untuk validasi mata kuliah.

## Endpoint REST

Endpoint yang diimplementasikan:

- `GET /api/v1/krs`
- `GET /api/v1/krs/{id}`
- `GET /api/v1/krs/semester/{tahunAjaran}/{semester}`
- `POST /api/v1/krs`

## Endpoint GraphQL

Endpoint:

- `POST /graphql`
- `GET /graphiql`

Query:

```graphql
query {
  krsList {
    id
    nim
    kode_mata_kuliah
    nama_mata_kuliah
    status_persetujuan
  }
}
```

## Catatan Implementasi

OpenAPI dibuat sebagai JSON statis di `public/docs/openapi.json`, sementara Swagger UI disediakan melalui route `/api/documentation`. GraphQL dibuat ringan untuk kebutuhan query assignment agar service tetap mandiri tanpa dependensi tambahan besar.
