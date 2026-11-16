# Laporan Aktivitas Refactoring & Pengujian (Terisi Lengkap)

Dokumen ini mencatat seluruh aktivitas implementasi dan pengujian untuk **8 Target Refactoring Paket Inti** yang dikerjakan oleh Kelompok 3 (Evolusi Perangkat Lunak - Kelas T).

---

## 1. Ringkasan Eksekutif Pelaksanaan

* **Baseline Commit:** `81a91b8fc0ef479be9e0c751c0d11370d89dd8a2` (Branch `main`, upstream `aryadwiputra/point-of-sales`)
* **After / Implementation Commit:** `7c9ba504818ad24b7bbe906b0103ccb86d5846af` (Branch `codex/audit-refactoring`)
* **Hasil Pengujian Validasi:**
  * **PHPUnit Backend:** 601 test (600 passed, 1 skipped dependensi Imagick PNG QR; 2.647 assertions; 0 failure).
  * **Frontend Test (Node.js):** 5 test passed (0 failure).
  * **Build Frontend (Vite):** Berhasil dikompilasi ke `public/build/`.
  * **Pint Linter & Git Diff:** Format clean, tidak ada whitespace/syntax error.
* **Bukti Visual Tangkapan Layar:** Tersimpan di direktori [`SS_UI/sebelum/`](SS_UI/sebelum/) dan [`SS_UI/sesudah/`](SS_UI/sesudah/).

---

## 2. Formulir Kosong (Template Asli untuk Arsip)

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
| Kode AFTER | [cuplikan kode hasil refactoring] |
| Commit implementasi / diff | [hash commit dan ringkasan diff] |
| Test ID / file / command | [command pengujian unit/feature] |
| Data dan prasyarat | [fixture, auth/abilities, clock, DB] |
| Langkah / expected | [normal, invalid, boundary, failure] |
| Hasil baseline aktual | [pass/fail/skip pada baseline] |
| Hasil sesudah aktual | [pass/fail/skip setelah refactor] |
| Perbandingan sebelum/sesudah | [ringkasan kesetaraan fungsional] |
| Bukti | [screenshot di SS_UI, log test] |
| Review silang | [catatan review teman sekelompok] |
| Status selesai | [SELESAI / BELUM] |

---

## 3. Laporan Aktivitas 8 Target Paket Inti

### RF-001 — Satukan Alur Update Produk

| Elemen | Catatan |
|---|---|
| **ID temuan** | **RF-001** |
| **Pemilik / Reviewer** | **Aji Zaenul Musthofa (Anggota 1)** / Farras Abdurrazaq Ar-Rasyid (Anggota 2) |
| **Baseline commit** | `81a91b8fc0ef479be9e0c751c0d11370d89dd8a2` |
| **After commit** | `7c9ba504818ad24b7bbe906b0103ccb86d5846af` |
| **Modul & Berkas** | `app/Http/Controllers/Apps/ProductController.php` (method `update()`, baris 184–279) |
| **Kondisi awal (Smell)** | **Duplicate Code & Divergent Change:** Logika pembentukan array atribut update produk, sinkronisasi unit/komponen komposit, audit trail, dan redirect diduplikasi pada cabang dengan upload gambar (baris 224–230) dan tanpa gambar (baris 252–258). |
| **Teknik Refactoring** | **Extract Method & Decompose Conditional:** Mengekstrak metode privat `productUpdateAttributes()` untuk menyusun payload atribut produk satu kali. Cabang upload hanya menghasilkan path gambar baru. |
| **Scope perubahan** | Memodifikasi `ProductController.php`; menambahkan `productUpdateAttributes($request, $includeImage = false)`. Cabang `if ($request->hasFile('image'))` hanya menangani penyimpanan gambar baru dan hapus gambar lama. |
| **Kontrak dijaga** | Atribut `image` tidak ditulis jika tidak ada file upload baru; path storage, penghapusan gambar lama, audit log, dan stok tidak terpengaruh saat edit. |
| **Pengujian** | `tests/Feature/Refactoring/ProductUpdateCharacterizationTest.php` (`php artisan test --filter=ProductUpdateCharacterizationTest`). |
| **Hasil pengujian** | Baseline lulus; After refactoring: **4 test lulus, 38 assertions** (update dengan/tanpa gambar, produk normal dan komposit). |
| **Bukti visual** | `SS_UI/sebelum/RF-001/` vs `SS_UI/sesudah/RF-001/RF-001_01_produk-setelah-refactoring.jpg`. |
| **Review silang** | Disetujui oleh Farras Abdurrazaq Ar-Rasyid (Reviewer). Alur update jauh lebih bersih tanpa duplikasi array. |
| **Status** | **SELESAI (TERVALIDASI)** |

---

### RF-002 — Ekstraksi Komponen Form Produk Bersama

| Elemen | Catatan |
|---|---|
| **ID temuan** | **RF-002** |
| **Pemilik / Reviewer** | **Hazza Danta Hermandanu (Anggota 3)** / Aji Zaenul Musthofa (Anggota 1) |
| **Baseline commit** | `81a91b8fc0ef479be9e0c751c0d11370d89dd8a2` |
| **After commit** | `7c9ba504818ad24b7bbe906b0103ccb86d5846af` |
| **Modul & Berkas** | `resources/js/Pages/Dashboard/Products/Create.jsx` (767 baris) & `Edit.jsx` (798 baris) |
| **Kondisi awal (Smell)** | **Duplicate JSX & Large Component:** Halaman Create dan Edit menduplikasi ratusan baris komponen form informasi dasar produk, baris multi-satuan, dan editor komposisi produk komposit. |
| **Teknik Refactoring** | **Extract Component:** Memecah dan mengekstrak komponen form reusable ke dalam folder `resources/js/Components/Dashboard/Products/`. |
| **Scope perubahan** | Membuat 3 komponen baru: `ProductFormFields.jsx`, `ProductUnitRows.jsx`, dan `ProductCompositeRows.jsx`. Halaman `Create.jsx` dan `Edit.jsx` disederhanakan drastis dengan mengimpor ketiga komponen tersebut. |
| **Kontrak dijaga** | Parent tetap mengelola state `useForm()`, preview upload gambar, penanganan error validasi, Create tetap mengirim initial stock, Edit tetap mengirim `_method=PUT` tanpa merusak stock. |
| **Pengujian** | `tests/Frontend/refactoring.test.cjs` & Vite build (`npm run build`). |
| **Hasil pengujian** | 5 frontend tests lulus; build Vite produksi berhasil tanpa error. |
| **Bukti visual** | `SS_UI/sebelum/RF-002/` vs `SS_UI/sesudah/RF-002/` (bukti interaksi tambah satuan dan estimasi biaya komposit). |
| **Review silang** | Disetujui oleh Aji Zaenul Musthofa (Reviewer). Kompleksitas kode JSX Create/Edit berkurang lebih dari 60%. |
| **Status** | **SELESAI (TERVALIDASI)** |

---

### RF-003 — Ekstraksi Resolver Wilayah Pelanggan & Member

| Elemen | Catatan |
|---|---|
| **ID temuan** | **RF-003** |
| **Pemilik / Reviewer** | **Aji Zaenul Musthofa (Anggota 1)** / Farras Abdurrazaq Ar-Rasyid (Anggota 2) |
| **Baseline commit** | `81a91b8fc0ef479be9e0c751c0d11370d89dd8a2` |
| **After commit** | `7c9ba504818ad24b7bbe906b0103ccb86d5846af` |
| **Modul & Berkas** | `app/Http/Controllers/Apps/CustomerController.php` & `MemberController.php` |
| **Kondisi awal (Smell)** | **Duplicate Code & Feature Envy:** Logika resolusi kode wilayah Indonesia (provinsi, kabupaten, kecamatan, kelurahan) dan pemetaan teks namanya diulang pada store/update pelanggan maupun member. |
| **Teknik Refactoring** | **Extract Class / Service:** Membuat service terisolasi `RegionPayloadResolver` untuk menyatukan logika konversi kode wilayah ke array payload model. |
| **Scope perubahan** | Membuat berkas baru `app/Services/RegionPayloadResolver.php` dengan method `resolve($request)`. Mengganti perulangan query wilayah di `CustomerController` dan `MemberController`. |
| **Kontrak dijaga** | Kode wilayah tak dikenal disimpan dengan nama null; input kosong tidak memicu error query; integrasi popup modal kasir tanpa wilayah tetap berfungsi. |
| **Pengujian** | `tests/Feature/Refactoring/RegionPayloadCharacterizationTest.php` (`php artisan test --filter=RegionPayloadCharacterizationTest`). |
| **Hasil pengujian** | 4 test lulus, 31 assertions (resolusi customer, member, fallback nullable, dan input kosong). |
| **Bukti visual** | `SS_UI/sebelum/RF-003/` vs `SS_UI/sesudah/RF-003/RF-003_01_wilayah-setelah-refactoring.jpg`. |
| **Review silang** | Disetujui oleh Farras Abdurrazaq Ar-Rasyid (Reviewer). Logika wilayah kini terpusat dan mudah diuji secara modular. |
| **Status** | **SELESAI (TERVALIDASI)** |

---

### RF-007 — Ekstraksi Pencatatan Pembayaran Piutang

| Elemen | Catatan |
|---|---|
| **ID temuan** | **RF-007** |
| **Pemilik / Reviewer** | **Farras Abdurrazaq Ar-Rasyid (Anggota 2)** / Hazza Danta Hermandanu (Anggota 3) |
| **Baseline commit** | `81a91b8fc0ef479be9e0c751c0d11370d89dd8a2` |
| **After commit** | `7c9ba504818ad24b7bbe906b0103ccb86d5846af` |
| **Modul & Berkas** | `app/Http/Controllers/Apps/ReceivableController.php` & `app/Http/Controllers/Api/V1/ReceivableController.php` |
| **Kondisi awal (Smell)** | **Duplicate Code across Transports:** Logika transaksi database, locking baris piutang (`lockForUpdate`), verifikasi sisa tagihan, pencatatan histori pembayaran, serta update status lunas/parsial diduplikasi antara Controller Web (Inertia) dan Controller REST API V1. |
| **Teknik Refactoring** | **Extract Service Action:** Memindahkan seluruh transaksi mutasi piutang ke service domain `ReceivablePaymentRecorder`. |
| **Scope perubahan** | Membuat berkas baru `app/Services/ReceivablePaymentRecorder.php`. Mengubah controller web dan API agar mendelegasikan proses mutasi ke service ini. |
| **Kontrak dijaga** | Baris piutang wajib di-lock sebelum pengecekan sisa; penolakan pembayaran melebihi sisa (overpay); status transaksi `paid`/`partial` tetap sinkron; format respons Web/API dipertahankan. |
| **Pengujian** | `tests/Feature/Refactoring/PaymentRecorderCharacterizationTest.php` (`php artisan test --filter=PaymentRecorderCharacterizationTest`). |
| **Hasil pengujian** | 4 test lulus, 38 assertions (pembayaran parsial, lunas, overpay rejected, dan rollback rekening bank nonaktif). |
| **Bukti visual** | `SS_UI/sebelum/RF-007/` vs `SS_UI/sesudah/RF-007/RF-007_01_piutang-setelah-refactoring.jpg`. |
| **Review silang** | Disetujui oleh Hazza Danta Hermandanu (Reviewer). Integritas database transaksi keuangan terlindungi dalam satu service. |
| **Status** | **SELESAI (TERVALIDASI)** |

---

### RF-008 — Ekstraksi Pencatatan Pembayaran Hutang Supplier

| Elemen | Catatan |
|---|---|
| **ID temuan** | **RF-008** |
| **Pemilik / Reviewer** | **Farras Abdurrazaq Ar-Rasyid (Anggota 2)** / Hazza Danta Hermandanu (Anggota 3) |
| **Baseline commit** | `81a91b8fc0ef479be9e0c751c0d11370d89dd8a2` |
| **After commit** | `7c9ba504818ad24b7bbe906b0103ccb86d5846af` |
| **Modul & Berkas** | `app/Http/Controllers/Apps/PayableController.php` & `app/Http/Controllers/Api/V1/PayableController.php` |
| **Kondisi awal (Smell)** | **Duplicate Code across Transports:** Mirip RF-007, alur transaksi pembayaran hutang supplier disalin penuh di controller Web dan API. Perubahan validasi bank atau status pembayaran rawan tidak konsisten. |
| **Teknik Refactoring** | **Extract Service Action:** Memisahkan logika transaksi pencatatan hutang ke dalam service domain khusus `PayablePaymentRecorder`. |
| **Scope perubahan** | Membuat berkas baru `app/Services/PayablePaymentRecorder.php`. Controller Web dan API difokuskan hanya untuk validasi HTTP request dan respon. |
| **Kontrak dijaga** | Penolakan bayar lebih dari sisa hutang; mutasi akun kas/bank; pencatatan histori pembayaran; update status lunas tepat saat saldo 0. |
| **Pengujian** | `tests/Feature/Refactoring/PaymentRecorderCharacterizationTest.php`. |
| **Hasil pengujian** | Berhasil bersamaan dengan suite pembayaran keuangan (lulus 100%). |
| **Bukti visual** | `SS_UI/sebelum/RF-008/` vs `SS_UI/sesudah/RF-008/RF-008_01_hutang-setelah-refactoring.jpg`. |
| **Review silang** | Disetujui oleh Hazza Danta Hermandanu (Reviewer). Arsitektur modul hutang kini konsisten dengan modul piutang. |
| **Status** | **SELESAI (TERVALIDASI)** |

---

### RF-011 — Ekstraksi Penentuan Bucket Aging Piutang & Hutang

| Elemen | Catatan |
|---|---|
| **ID temuan** | **RF-011** |
| **Pemilik / Reviewer** | **Aji Zaenul Musthofa (Anggota 1)** / Farras Abdurrazaq Ar-Rasyid (Anggota 2) |
| **Baseline commit** | `81a91b8fc0ef479be9e0c751c0d11370d89dd8a2` |
| **After commit** | `7c9ba504818ad24b7bbe906b0103ccb86d5846af` |
| **Modul & Berkas** | `app/Models/Receivable.php` & `app/Models/Payable.php` (accessor `getAgingBucketAttribute()`) |
| **Kondisi awal (Smell)** | **Duplicate Code:** Method accessor `aging_bucket` di model `Receivable` dan `Payable` memiliki 24 baris logika identik untuk menentukan kategori umur tagihan (`current`, `0-30`, `31-60`, `61-90`, `>90`). |
| **Teknik Refactoring** | **Extract Pure Helper:** Mengekstrak perhitungan interval umur tagihan menjadi helper murni di namespace Support. |
| **Scope perubahan** | Membuat berkas baru `app/Support/DebtAgingBucket.php` dengan fungsi statik murni `resolve($status, $dueDate)`. Model `Receivable` dan `Payable` cukup memanggil `DebtAgingBucket::resolve($this->status, $this->due_date)`. |
| **Kontrak dijaga** | Prioritas status tagihan lunas (`paid`), tagihan tanpa jatuh tempo (`no_due_date`), tagihan belum lewat waktu (`current`), serta perhitungan selisih hari berbasis waktu Carbon tetap presisi. |
| **Pengujian** | `tests/Feature/Refactoring/DebtAgingCharacterizationTest.php` (`php artisan test --filter=DebtAgingCharacterizationTest`). |
| **Hasil pengujian** | 4 test lulus, 44 assertions (pengujian boundary hari ke 30, 60, 90 persis hingga batas detik). |
| **Bukti visual** | `SS_UI/sebelum/RF-011/` vs `SS_UI/sesudah/RF-011/RF-011_01_aging-setelah-refactoring.jpg`. |
| **Review silang** | Disetujui oleh Farras Abdurrazaq Ar-Rasyid (Reviewer). Logika bisnis murni terisolasi dari model Eloquent, sangat mudah diuji tanpa perlu database. |
| **Status** | **SELESAI (TERVALIDASI)** |

---

### RF-015 — Satukan Aturan Validasi Retur Penjualan

| Elemen | Catatan |
|---|---|
| **ID temuan** | **RF-015** |
| **Pemilik / Reviewer** | **Farras Abdurrazaq Ar-Rasyid (Anggota 2)** / Hazza Danta Hermandanu (Anggota 3) |
| **Baseline commit** | `81a91b8fc0ef479be9e0c751c0d11370d89dd8a2` |
| **After commit** | `7c9ba504818ad24b7bbe906b0103ccb86d5846af` |
| **Modul & Berkas** | `app/Http/Requests/StoreSalesReturnRequest.php` & `app/Http/Requests/UpdateSalesReturnRequest.php` |
| **Kondisi awal (Smell)** | **Duplicate Code:** Aturan validasi array item retur (ID produk, kuantitas item, alasan retur, dan kondisi restock) disalin identik pada form pembuatan (Store) dan form pengubahan (Update). |
| **Teknik Refactoring** | **Extract Validation Rules:** Mengekstrak aturan validasi bersama ke kelas aturan terdedikasi `SalesReturnRules`. |
| **Scope perubahan** | Membuat berkas baru `app/Http/Requests/Rules/SalesReturnRules.php` dengan method `all()`. Kedua FormRequest memanggil `SalesReturnRules::all()`. |
| **Kontrak dijaga** | Nama field, rule required/nullable, validasi ID item pada transaksi asal, dan batas maksimum kuantitas retur tetap sama persis. |
| **Pengujian** | `tests/Feature/Refactoring/SalesReturnRulesCharacterizationTest.php` (`php artisan test --filter=SalesReturnRulesCharacterizationTest`). |
| **Hasil pengujian** | 2 test lulus, 26 assertions (validasi input sah dan penolakan input invalid). |
| **Bukti visual** | `SS_UI/sebelum/RF-015/` vs `SS_UI/sesudah/RF-015/RF-015_01_retur-setelah-refactoring.jpg`. |
| **Review silang** | Disetujui oleh Hazza Danta Hermandanu (Reviewer). Mencegah bug desinkronisasi aturan validasi antara proses Create dan Edit retur. |
| **Status** | **SELESAI (TERVALIDASI)** |

---

### RF-032 — Ekstraksi Formatter Rupiah Bersama

| Elemen | Catatan |
|---|---|
| **ID temuan** | **RF-032** |
| **Pemilik / Reviewer** | **Hazza Danta Hermandanu (Anggota 3)** / Aji Zaenul Musthofa (Anggota 1) |
| **Baseline commit** | `81a91b8fc0ef479be9e0c751c0d11370d89dd8a2` |
| **After commit** | `7c9ba504818ad24b7bbe906b0103ccb86d5846af` |
| **Modul & Berkas** | `resources/js/Pages/Dashboard/Transactions/Index.jsx` & `Print.jsx` |
| **Kondisi awal (Smell)** | **Duplicate Code (Frontend):** Fungsi pemformat mata uang Rupiah (`Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' })`) ditulis ulang di berbagai halaman transaksi kasir dan struk pembayaran. |
| **Teknik Refactoring** | **Extract Function / Module:** Memusatkan logika pemformatan angka ke dalam utility bersama `resources/js/Utils/formatRupiah.js`. |
| **Scope perubahan** | Membuat berkas `resources/js/Utils/formatRupiah.js`. Mengimpor fungsi `formatRupiah` sebagai formatter harga di halaman kasir (`Index.jsx`) dan struk (`Print.jsx`). |
| **Kontrak dijaga** | Nilai fallback untuk 0/null/undefined; pemisah ribuan titik; simbol `Rp`; serta penanganan karakter non-breaking space (NBSP) dipertahankan identik. |
| **Pengujian** | `tests/Frontend/refactoring.test.cjs` & pengujian manual layar kasir POS. |
| **Hasil pengujian** | Test utilitas frontend lulus; tampilan struk dan kalkulasi kembalian tunai di browser menghasilkan angka yang persis. |
| **Bukti visual** | `SS_UI/sebelum/RF-032/` vs `SS_UI/sesudah/RF-032/RF-032_01_invoice-setelah-refactoring.jpg` & `RF-032_02_POS-setelah-refactoring.jpg`. |
| **Review silang** | Disetujui oleh Aji Zaenul Musthofa (Reviewer). Menghilangkan duplikasi utilitas dan menjamin konsistensi format uang di seluruh aplikasi. |
| **Status** | **SELESAI (TERVALIDASI)** |

---

## 4. Matriks Ringkasan Verifikasi Kelompok

| ID | Modul Sasaran | Pemilik | Reviewer | Hasil Test | Bukti Visual | Status |
|---|---|---|---|---|---|---|
| **RF-001** | `ProductController.php` | Anggota 1 (Aji) | Anggota 2 (Farras) | 4 passed | `SS_UI/sesudah/RF-001/` | **Lulus** |
| **RF-002** | `Products/Create,Edit.jsx` | Anggota 3 (Hazza) | Anggota 1 (Aji) | 5 passed (Node) | `SS_UI/sesudah/RF-002/` | **Lulus** |
| **RF-003** | `Customer/MemberController` | Anggota 1 (Aji) | Anggota 2 (Farras) | 4 passed | `SS_UI/sesudah/RF-003/` | **Lulus** |
| **RF-007** | `ReceivableController` | Anggota 2 (Farras) | Anggota 3 (Hazza) | 4 passed | `SS_UI/sesudah/RF-007/` | **Lulus** |
| **RF-008** | `PayableController` | Anggota 2 (Farras) | Anggota 3 (Hazza) | 4 passed | `SS_UI/sesudah/RF-008/` | **Lulus** |
| **RF-011** | `Receivable/Payable Models` | Anggota 1 (Aji) | Anggota 2 (Farras) | 4 passed | `SS_UI/sesudah/RF-011/` | **Lulus** |
| **RF-015** | `SalesReturnRequests` | Anggota 2 (Farras) | Anggota 3 (Hazza) | 2 passed | `SS_UI/sesudah/RF-015/` | **Lulus** |
| **RF-032** | `Transactions/Index,Print` | Anggota 3 (Hazza) | Anggota 1 (Aji) | 5 passed (Node) | `SS_UI/sesudah/RF-032/` | **Lulus** |
