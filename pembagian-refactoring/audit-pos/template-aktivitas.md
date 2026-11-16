# Template aktivitas refactoring

Salin per ID. Template ini belum diisi dengan hasil implementasi.

| Elemen | Catatan |
|---|---|
| ID temuan | RF-___ |
| Pemilik / reviewer | Anggota ___ / Anggota ___ |
| Baseline branch / commit | main / 81a91b8fc0ef479be9e0c751c0d11370d89dd8a2 |
| Modul, file, class/function, lines | [isi dari inventaris] |
| Kondisi awal | [smell/kompleksitas/duplikasi/struktur dan dampak] |
| Metrik sebelum, alat/metode | [angka terukur atau tulis belum diukur] |
| Kode BEFORE | [cuplikan source baseline dan link commit tetap] |
| Teknik / alasan | [teknik spesifik dan manfaatnya] |
| Scope perubahan | [yang dipindah/dibagi/diubah nama, caller, file baru] |
| Kontrak yang dipertahankan | [output/response/validation/access/UI/DB/audit/order/locking] |
| Kode AFTER | BELUM DIIMPLEMENTASIKAN |
| Commit implementasi / diff | BELUM ADA |
| Test ID / file / command | [rencana; bedakan file existing vs USULAN] |
| Data dan prasyarat | [fixture, auth/abilities, clock, DB, fake integrasi] |
| Langkah / expected | [normal, invalid, boundary, failure] |
| Hasil baseline aktual | [run, tanggal, pass/fail/skip dan bukti; jangan mengarang] |
| Hasil sesudah aktual | BELUM DIJALANKAN |
| Perbandingan sebelum/sesudah | BELUM TERSEDIA |
| Bukti | [log/JUnit/screenshot/output API/snapshot DB] |
| Review silang | BELUM DIREVIEW |
| Keterbatasan/bug terpisah | [ID BF/ENV bila ada; jangan campur dengan RF] |
| Metrik sesudah | BELUM DIUKUR |
| Status selesai | BELUM; isi hanya jika seluruh kriteria RF terpenuhi |
| Bagian laporan / materi demo | [bagian milik anggota dan durasi] |

Alur pencatatan: ID → kondisi awal → teknik dan alasan → perubahan sebelum/sesudah → pengujian → hasil aktual → penanggung jawab/reviewer → commit/bukti. Jika hasil test gagal, catat kegagalannya dan commit koreksi; jangan menimpa bukti baseline.
