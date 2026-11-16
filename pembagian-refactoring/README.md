# Kelompok 3 — Refactoring dan Testing Studi Kasus POS

Dokumentasi audit, rencana kerja, dan **laporan implementasi refactoring** untuk **Anggota 1, Anggota 2, dan Anggota 3**. Seluruh 8 target refactoring Paket Inti telah berhasil diimplementasikan dan diverifikasi pada commit `7c9ba504818ad24b7bbe906b0103ccb86d5846af`.

## Status Pelaksanaan Tugas

- **Status:** **SELESAI & TERVALIDASI (8 dari 8 Paket Inti)**
- **Commit Implementasi:** `7c9ba50` (`refactor: implement eight audited RF targets`)
- **Hasil Pengujian:** **600 PHP tests lulus, 1 dilewati** (Imagick QR); **5 frontend tests lulus**; build frontend Vite berhasil.
- **Bukti Visual (Screenshot UI):** Tersedia lengkap sebelum & sesudah di [`audit-pos/SS_UI/`](audit-pos/SS_UI/).
- **Rangkuman Teknis:** Tersedia di [`docs/refactoring-8-targets.md`](../docs/refactoring-8-targets.md) dan [`docs/refactoring/hasil-validasi.json`](../docs/refactoring/hasil-validasi.json).

## Mulai membaca

1. [Laporan utama dan rencana A–I](audit-pos/audit-dan-rencana.md)
2. [Bukti lengkap 91 kandidat refactoring](audit-pos/B-inventaris-refactoring.md)
3. [Pembagian seluruh kandidat dan pemilik utama](audit-pos/pembagian-semua-temuan.csv)
4. [Rencana testing dan hasil baseline](audit-pos/testing.md)
5. [Matriks kandidat ke testing](audit-pos/matriks-RF-test.csv)
6. [Temuan di luar refactoring](audit-pos/temuan-di-luar-refactoring.md)
7. [Dokumentasi aktivitas implementasi per anggota (Terisi)](audit-pos/template-aktivitas.md)
8. [Setup lokal dan bukti menjalankan proyek](audit-pos/runtime.md)
9. [Bukti visual tangkapan layar Sebelum & Sesudah](audit-pos/SS_UI/)

## Paket pengerjaan yang direkomendasikan

Delapan kandidat berikut merupakan rekomendasi scope kelompok, bukan kewajiban yang ditetapkan dosen. Setiap anggota bertanggung jawab atas analisis awal, implementasi, testing, dokumentasi sebelum/sesudah, bagian laporan, dan demo.

| Anggota | Target utama | Mereview pekerjaan |
|---|---|---|
| Anggota 1 | RF-001, RF-003, RF-011 | Anggota 2 |
| Anggota 2 | RF-007, RF-008, RF-015 | Anggota 3 |
| Anggota 3 | RF-002, RF-032 | Anggota 1 |

Terdapat 60 kandidat opsional dan 23 kandidat ditunda. Selesaikan testing, review, dan dokumentasi target utama sebelum memperluas scope. Pembagian mempertimbangkan usaha dan risiko, bukan hanya jumlah temuan.

## Baseline dan batas audit

- Sumber: [aryadwiputra/point-of-sales](https://github.com/aryadwiputra/point-of-sales).
- Branch yang dianalisis: `main`.
- Commit: `81a91b8fc0ef479be9e0c751c0d11370d89dd8a2`.
- Tanggal audit: 15 November 2026, Asia/Jakarta.
- Arsip source sebelum refactoring: [baseline-81a91b8.tar.gz](audit-pos/baseline-81a91b8.tar.gz). Arsip mempertahankan lisensi dan atribusi proyek sumber; lihat berkas lisensi di dalamnya.
- Hasil baseline aktual: **580 test lulus, 1 dilewati, 2452 assertions**; build frontend berhasil. Satu test QR dilewati karena ekstensi imagick tidak tersedia.
- Analisis statik memetakan seluruh tracked source, tetapi pendalaman semantik manual setiap fungsi belum selesai. Sebanyak 91 kandidat adalah temuan terkonfirmasi dalam cakupan audit, bukan jaminan inventaris final seluruh kemungkinan masalah.
- **Status Implementasi:** Delapan target refactoring inti telah berhasil diimplementasikan di commit `7c9ba50` dengan 600 PHP tests lulus (1 skipped) dan 5 frontend tests lulus. Bukti tangkapan layar UI sebelum & sesudah disimpan di `audit-pos/SS_UI/`. Dokumen laporan aktivitas dan hasil validasi telah diperbarui.

Folder `audit-pos` berisi laporan, CSV, metrik, log, screenshot data demo lokal, serta arsip baseline. Path absolut dalam bukti mencatat lingkungan audit asal; gunakan tautan relatif dokumentasi ini saat membaca di GitHub. Skrip alat audit mungkin memerlukan penyesuaian path di komputer anggota.

Tidak menyertakan `.env` lokal, database audit, dependensi terinstal, atau hasil build aplikasi. Untuk menjalankan sistem gunakan petunjuk pada `runtime.md` dan dokumentasi proyek sumber.
