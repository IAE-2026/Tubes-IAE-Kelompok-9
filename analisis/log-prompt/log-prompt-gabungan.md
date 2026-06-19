# Log Prompt Gabungan — Kelompok 9 (TIM-09)

**Mata Kuliah:** BBK2HAB3 — Integrasi Aplikasi Enterprise  
**Kelompok:** 9 (TIM-09)

---

## Ringkasan

Dokumen ini merangkum penggunaan AI (prompting) selama pengerjaan Tugas Besar IAE oleh ketiga anggota tim. Log lengkap per individu tersedia di subfolder `log-prompt-masing-masing/` — isinya salinan asli tanpa modifikasi.

---

## Andi Saputra — Service C (Kurikulum & Nilai)

**NIM:** 102022580023  
**Tool AI:** Antigravity (Gemini 2.0 Flash), Cursor AI (Claude)

**Fokus prompting:**
- Perencanaan dan implementasi Laravel 12 (REST, Swagger, GraphQL, Docker/Sail)
- Middleware `CheckIaeKey`, validasi ke Service A saat POST /nilai
- Tugas 3: SSO JWT dosen, SOAP audit NilaiRecorded, RabbitMQ `nilai.recorded`, `iae:sync-token`
- Debugging: duplicate seeder, payload RabbitMQ, perbedaan X-IAE-KEY vs KEY-MHS-117 vs JWT

**File asli:**
- [`log-prompt-masing-masing/andi/log_prompting.md`](log-prompt-masing-masing/andi/log_prompting.md)

---

## Arneta Alifiana — Service A (Data Mahasiswa)

**NIM:** 102022400136  
**Tool AI:** Claude AI, Cursor AI

**Fokus prompting:**
- Setup project, migration, API Key middleware, Swagger, Docker troubleshooting
- SSO federated login, SOAP audit manual (XML kustom), RabbitMQ publisher
- Tugas 3: M2M token, fallback token, gateway routing `/api/v1/auth`
- Tugas Besar: Http::pool paralel ke Service B & C, fallback 404 nilai mahasiswa baru

**File asli (3 dokumen):**
- [`log-prompt-masing-masing/arneta/AI_PROMPTING_LOG.md`](log-prompt-masing-masing/arneta/AI_PROMPTING_LOG.md) — log awal (14 Mei 2026)
- [`log-prompt-masing-masing/arneta/AI_PROMTING_LOG_TUGAS 3.md`](log-prompt-masing-masing/arneta/AI_PROMTING_LOG_TUGAS%203.md) — log Tugas 3
- [`log-prompt-masing-masing/arneta/AI_PROMTING_TUBES.md`](log-prompt-masing-masing/arneta/AI_PROMTING_TUBES.md) — log Tugas Besar

---

## Mochammad Jafar Arrazi — Service B (KRS)

**NIM:** 102022400045  
**Tool AI:** AI Assistant (Assignment 2)

**Fokus prompting:**
- Analisis requirement service-based architecture
- Desain alur bisnis KRS, validasi antarservice (Mahasiswa, Nilai, Kurikulum)
- Standard response contract, Docker, OpenAPI, GraphQL
- Debugging route 404 dan error handling

**File asli:**
- [`log-prompt-masing-masing/jafar/AI_CHAT_HISTORY.md`](log-prompt-masing-masing/jafar/AI_CHAT_HISTORY.md)

---

## Kesimpulan Kelompok

AI digunakan sebagai asisten perencanaan, implementasi boilerplate, dan debugging — bukan pengganti analisis mandiri. Setiap anggota memverifikasi hasil AI melalui testing Postman, Docker, dan board RabbitMQ Cloud Pusat sebelum diintegrasikan ke monorepo kelompok.
