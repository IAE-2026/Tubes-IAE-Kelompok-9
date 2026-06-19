# Recap Penkerjaan TEAM-9 (Analisis, Log-Prompt, Resume)

**Mata Kuliah:** BBK2HAB3 — Integrasi Aplikasi Enterprise  
**Repository:** Tubes-IAE-Kelompok-9  
**Cloud Pusat:** https://iae-sso.virtualfri.id

---

## Anggota Tim

| Nama | NIM | Service | Folder Service |
|------|-----|---------|----------------|
| Arneta Alifiana | 102022400136 | A — Data Mahasiswa | `services/102022400136-Arneta Alifiana-Data Mahasiswa/` |
| Mochammad Jafar Arrazi | 102022400045 | B — KRS | `services/102022400045-Mochammad Jafar Arrazi-KRS/` |
| Andi Muh. Arif Darma Saputra M | 102022580023 | C — Nilai & Kurikulum | `services/102022580023-Andi Muh. Arif Darma Saputra M-Nilai & Kurikulum/` |

---

## Struktur Folder

```
recapt_penkerjaan_TEAM9 (analisis, log-promt, resume)/
├── README.md                          ← halaman ini
├── log-prompt/
│   ├── log-prompt-gabungan.md
│   └── log-prompt-masing-masing/
│       ├── andi/                      ← file asli (copy, tidak diubah)
│       ├── arneta/
│       └── jafar/
├── resume-kontribusi/
│   ├── resume-kontribusi-gabungan.md
│   └── resume-kontribusi-masing-masing/
│       ├── andi/
│       ├── arneta/
│       └── jafar/
└── education-system/
    ├── education-system-gabungan.md
    └── education-system-masing-masing/
        ├── andi/
        ├── arneta/
        └── jafar/
```

---

## Isi Subfolder per Anggota

| Folder | Andi | Arneta | Jafar |
|--------|------|--------|-------|
| `log-prompt-masing-masing/` | `log_prompting.md` | `AI_PROMPTING_LOG.md`, `AI_PROMTING_LOG_TUGAS 3.md`, `AI_PROMTING_TUBES.md` | `AI_CHAT_HISTORY.md` |
| `resume-kontribusi-masing-masing/` | `RESUME_ANDI.md` | `RESUME_ARNETA.md` | `RESUME_KONTRIBUSI.md` |
| `education-system-masing-masing/` | `analisis_tugas_3.md` | `analisis_tugas_3.md` | `analisis_tugas_3.md` |

---

## Catatan untuk Penilaian

- Semua folder `*-masing-masing/` memakai **subfolder per anggota** (`andi/`, `arneta/`, `jafar/`). Isi tiap subfolder adalah **salinan identik** dari dokumen asli dengan **nama file asli** — tidak diubah agar keaslian terjaga.
- File `*-gabungan.md` adalah ringkasan kelompok yang mengarahkan ke dokumen individu.
- Sumber asli tetap ada di folder `services/` masing-masing anggota (format `NIM-Nama-Service`).

---

## Peta Integrasi SOAP & RabbitMQ (Tugas 3)

| Service | Transaksi Kritis | SOAP Activity | RabbitMQ Event |
|---------|------------------|---------------|----------------|
| A | POST /api/v1/mahasiswa | MahasiswaBaru | mahasiswa.created |
| B | POST /api/v1/krs | KrsCreated | krs.created |
| C | POST /api/v1/nilai | NilaiRecorded | nilai.recorded |
