# LAPORAN TUGAS 3
# REFACTORING DAN TESTING TERHADAP STUDI KASUS
## APLIKASI POINT OF SALES

Disusun untuk memenuhi tugas mata kuliah **[Nama Mata Kuliah]**

Dosen Pengampu: **[Nama Dosen]**

Disusun oleh **Kelompok 3**:

| Anggota | Nama | NIM |
|---|---|---|
| Anggota 1 | [Nama lengkap] | [NIM] |
| Anggota 2 | [Nama lengkap] | [NIM] |
| Anggota 3 | [Nama lengkap] | [NIM] |

**[Program Studi]**

**[Fakultas]**

**[Nama Perguruan Tinggi]**

**2026**

# DAFTAR ISI

[Tambahkan daftar isi Google Docs setelah seluruh bagian dan penataan halaman selesai.]

Catatan penyusunan: ganti seluruh placeholder dalam kurung siku. Cuplikan kode perlu diambil dari source sebenarnya; tanda `...` hanya menunjukkan bagian yang tidak ditampilkan. Hasil pengujian di bagian 4.4 merupakan hasil sesi implementasi yang sudah tercatat, bukan pengujian baru saat template ini dibuat. Tambahkan bukti pengujian ulang kelompok secara terpisah.

# 1. KONDISI KODE SEBELUM REFACTORING

## 1.1 Gambaran Umum Proyek

Studi kasus yang digunakan adalah aplikasi Point of Sales (POS). Aplikasi menyediakan pengelolaan produk, pelanggan dan member, transaksi penjualan, persediaan, piutang, hutang, retur, dan laporan. Backend menggunakan Laravel dengan Eloquent untuk akses data. Antarmuka menggunakan React melalui Inertia, sedangkan aset frontend diproses menggunakan Vite. Proyek juga memiliki layanan WhatsApp terpisah yang tidak menjadi sasaran delapan implementasi dalam laporan ini.

Repositori sumber: [aryadwiputra/point-of-sales](https://github.com/aryadwiputra/point-of-sales).

Repositori kelompok: [ItsDannz/kelompok3-epl](https://github.com/ItsDannz/kelompok3-epl).

Branch kerja kelompok: `codex/audit-refactoring`.

[Tambahkan ringkasan hubungan route → controller → service/model → database serta controller → Inertia → komponen React sesuai modul yang dibahas.]

## 1.2 Tujuan dan Ruang Lingkup

Refactoring bertujuan memperbaiki struktur, mengurangi duplikasi, dan mempermudah pemeliharaan serta pengujian, dengan mempertahankan perilaku fungsional yang berkaitan dengan perubahan.

Inventaris audit mencatat 91 kandidat yang dikelompokkan menjadi 8 target yang direkomendasikan untuk paket tugas, 60 kandidat opsional, dan 23 kandidat yang ditunda. Delapan target dipilih berdasarkan manfaat, keterkaitan modul, risiko regresi, dan kapasitas kelompok. Jumlah tersebut merupakan inventaris dalam cakupan audit, bukan klaim bahwa semua kemungkinan masalah proyek telah ditemukan atau diselesaikan.

Target implementasi:

| ID | Nama refactoring |
|---|---|
| RF-001 | Satukan alur update produk |
| RF-002 | Ekstraksi komponen form produk bersama |
| RF-003 | Ekstraksi resolver wilayah |
| RF-007 | Ekstraksi pencatatan pembayaran piutang |
| RF-008 | Ekstraksi pencatatan pembayaran hutang |
| RF-011 | Ekstraksi penentuan bucket aging |
| RF-015 | Satukan aturan validasi retur |
| RF-032 | Ekstraksi formatter Rupiah bersama |

Daftar kandidat lain dan cakupan pemeriksaan dapat dirujuk pada [audit-dan-rencana.md](../audit-pos/audit-dan-rencana.md) dan [B-inventaris-refactoring.md](../audit-pos/B-inventaris-refactoring.md).

## 1.3 Identitas Versi yang Dibandingkan

| Keterangan | Referensi |
|---|---|
| Commit sumber saat audit awal | `81a91b8fc0ef479be9e0c751c0d11370d89dd8a2` |
| Commit repositori kelompok sebelum implementasi | `d0f42ea581e0d39f11eef1a88524daacba5ef3c7` |
| Commit implementasi refactoring di repositori kelompok | `7c9ba504818ad24b7bbe906b0103ccb86d5846af` |
| Branch kelompok | `codex/audit-refactoring` |
| Tanggal pengujian ulang kelompok | [isi] |
| Commit yang dipakai saat pengujian ulang | [isi hash sebelum dan sesudah] |

Perbandingan implementasi: [diff sebelum dan sesudah refactoring](https://github.com/ItsDannz/kelompok3-epl/compare/d0f42ea...7c9ba50).

Commit dokumentasi yang ditambahkan setelah implementasi harus dibedakan dari commit perubahan source aplikasi.

## 1.4 Identifikasi Code Smells dan Masalah Struktur

| ID | Lokasi utama sebelum perubahan | Masalah | Dampak terhadap pemeliharaan/pengujian |
|---|---|---|---|
| RF-001 | `ProductController::update()` | Duplikasi alur update dengan dan tanpa penggantian gambar | Perubahan atribut, sinkronisasi, atau audit harus konsisten di kedua cabang. |
| RF-002 | Halaman produk `Create.jsx` dan `Edit.jsx` | Duplikasi JSX informasi dasar, editor satuan, dan editor komponen | Perubahan field dan interaksi perlu disalin ke dua halaman. |
| RF-003 | `CustomerController` dan `MemberController` | Query dan pemetaan kode/nama wilayah berulang | Aturan pemetaan tersebar dan berisiko berbeda antarjalur. |
| RF-007 | Controller piutang web dan API | Duplikasi logika pembayaran; tanggung jawab HTTP bercampur dengan transaksi bisnis | Perubahan pembayaran harus diterapkan konsisten pada kedua antarmuka. |
| RF-008 | Controller hutang web dan API | Duplikasi logika pencatatan pembayaran | Pemeriksaan sisa, history, dan status berisiko berbeda antarjalur. |
| RF-011 | Accessor `aging_bucket` model `Receivable` dan `Payable` | Algoritma bucket aging berulang | Perubahan batas umur piutang/hutang harus konsisten. |
| RF-015 | `StoreSalesReturnRequest` dan `UpdateSalesReturnRequest` | Array aturan validasi identik | Aturan create dan update dapat menyimpang ketika salah satu diubah. |
| RF-032 | Halaman transaksi `Index.jsx` dan `Print.jsx` | Formatter Rupiah identik didefinisikan berulang | Format nominal kasir dan cetakan dapat berbeda saat pemeliharaan. |

[Untuk setiap baris, lengkapi path file, nama fungsi, dan rentang baris pada commit sebelum refactoring. Gunakan inventaris audit dan source versi tersebut; jangan memakai nomor baris versi sesudah.]

## 1.5 Kompleksitas dan Pembagian Tanggung Jawab

Penilaian kompleksitas pada bagian ini bersifat kualitatif. RF-001 menunjukkan langkah update yang diulang pada dua cabang. RF-002 memperlihatkan halaman yang menampung banyak rendering dan interaksi baris form. RF-007 dan RF-008 mencampurkan penanganan request/response dengan pencatatan pembayaran dalam transaksi database. Duplikasi pada target lain membuat satu aturan harus dipelihara pada beberapa lokasi.

Tidak dicantumkan angka cyclomatic complexity, coverage, atau persentase perbaikan karena metrik tersebut belum diukur dalam bukti yang dirujuk laporan ini.

[Jika menambahkan metrik ukuran fungsi atau duplikasi, tuliskan alat/metode, commit yang diukur, hasil sebelum/sesudah, dan batas interpretasinya.]

# 2. STRATEGI REFACTORING

## 2.1 Pendekatan

Paket implementasi dibatasi pada delapan target. Perubahan diarahkan pada pemusatan aturan yang sama dan pemisahan bagian kode yang memiliki tanggung jawab jelas. Input, validasi, otorisasi, bentuk respons, efek database, audit, serta perilaku UI yang terkait harus tetap sama.

Paket ini tidak mencakup penambahan fitur, migrasi database, perubahan rumus bisnis, atau perbaikan bug terpisah. Temuan bug dicatat dan ditangani sebagai pekerjaan tersendiri agar perubahan perilaku dapat ditelusuri.

## 2.2 Teknik dan Alasan Pemilihan

| ID | Teknik | Perubahan konkret | Alasan |
|---|---|---|---|
| RF-001 | Extract Method dan konsolidasi cabang | Membentuk atribut melalui `productUpdateAttributes()`, menyisakan cabang khusus gambar, lalu menjalankan update/sinkronisasi/audit bersama | Menghilangkan pengulangan tanpa mengganti alur bisnis. |
| RF-002 | Extract Component | Membuat `ProductFormFields`, `ProductUnitRows`, dan `ProductCompositeRows` untuk Create dan Edit | Bagian form yang sama dipelihara dan diuji di satu tempat. |
| RF-003 | Extract Class | Memindahkan pemetaan wilayah ke `RegionPayloadResolver::resolve()` | Memusatkan query dan bentuk payload tanpa menambah aturan wilayah. |
| RF-007 | Extract Class/Move Method | Memindahkan pencatatan pembayaran ke `ReceivablePaymentRecorder::record()` | Web dan API memakai transaksi bisnis yang sama; respons tetap di controller. |
| RF-008 | Extract Class/Move Method | Memindahkan pencatatan pembayaran ke `PayablePaymentRecorder::record()` | Mengurangi duplikasi web/API sambil mempertahankan perbedaan model hutang dan piutang. |
| RF-011 | Extract Method ke helper bersama | Kedua accessor memakai `DebtAgingBucket::resolve()` | Batas aging dan urutan kondisinya dipelihara sekali. |
| RF-015 | Extract Class untuk aturan bersama | Kedua request memakai `SalesReturnRules::all()` | Menghindari perbedaan aturan identik antara create dan update. |
| RF-032 | Extract Function | Formatter dipindahkan ke `formatRupiah()` dan diimpor sebagai `formatPrice` | Format kasir dan cetakan memakai implementasi yang sama. |

## 2.3 Kontrak Perilaku yang Dipertahankan

| Area | Kontrak yang harus sama sebelum/sesudah |
|---|---|
| Update produk | Validasi/default atribut, penyimpanan dan penghapusan gambar, sinkronisasi satuan/komponen, audit dan redirect; stok tidak diedit melalui update ini. |
| Form produk | Initial state dan submit tetap di parent; field, placeholder Create/Edit, error, filter pilihan, dan estimasi tetap sesuai perilaku awal. |
| Wilayah | Pasangan kode/nama tetap sama; kode tidak dikenal tetap tersimpan dengan nama null; wilayah POS tetap opsional. |
| Pembayaran | Transaksi, urutan lock/pemeriksaan sisa, batas nominal, rekening aktif/outlet, history, status, izin, dan respons web/API dipertahankan. |
| Aging | Prioritas paid/no_due_date/current dan batas 30/60/90 hari, termasuk pecahan hari Carbon, tetap sama. |
| Validasi retur | Nama field, required/nullability, exists, batas qty/alasan, tipe retur, dan authorize() tetap sama. |
| Rupiah | `Number(value || 0)`, locale `id-ID`, mata uang IDR, pembulatan, tanda negatif, dan spasi NBSP tetap sama. |

# 3. AKTIVITAS REFACTORING

Gunakan cuplikan yang benar-benar berubah. Sertakan konteks yang cukup, tetapi tidak perlu menempel seluruh file. Isi kode sebelum dari commit `d0f42ea` dan kode sesudah dari `7c9ba50`. Untuk file yang baru dibuat, tuliskan bahwa file belum ada pada versi sebelum, lalu tampilkan logika asal di file pemanggil.

## 3.1 RF-001: Satukan Alur Update Produk

**File utama:** `app/Http/Controllers/Apps/ProductController.php`.

**Kondisi awal dan alasan:** Cabang dengan gambar baru dan tanpa gambar mengulang atribut serta langkah update. Pengulangan menambah lokasi yang harus diselaraskan ketika atribut atau audit berubah.

**Kode sebelum:**

```php
...
[Tempel bagian update dengan/tanpa gambar yang menunjukkan duplikasi.]
...
```

**Kode sesudah:**

```php
...
[Tempel pemanggilan productUpdateAttributes(), cabang khusus gambar, dan alur update bersama.]
...
```

**Penjelasan perubahan dan dampak:** Atribut disusun sekali. Cabang gambar menangani penggantian file, sedangkan update, sinkronisasi, audit, dan redirect memakai jalur bersama. Perubahan mengurangi duplikasi; bukan perubahan cara pengguna mengedit produk.

**Perilaku yang dipertahankan:** Validasi, gambar, unit/komponen, audit, redirect dan stok.

**Pengujian/bukti:** [Isi hasil update produk normal/komposit dengan dan tanpa gambar, pemeriksaan stok/audit, serta screenshot UI.]

**Penanggung jawab:** [isi]. **Commit/bukti:** [isi tautan diff dan pengujian].

## 3.2 RF-002: Ekstraksi Komponen Form Produk Bersama

**File utama:** `resources/js/Pages/Dashboard/Products/Create.jsx`, `Edit.jsx`, serta tiga komponen baru di `resources/js/Components/Dashboard/Products/`: `ProductFormFields.jsx`, `ProductUnitRows.jsx`, dan `ProductCompositeRows.jsx`.

**Kondisi awal dan alasan:** Create dan Edit memiliki pengulangan JSX serta interaksi informasi dasar, satuan, dan komponen produk. Perubahan pada form perlu dipelihara di dua halaman.

**Kode sebelum:**

```jsx
...
[Tempel bagian yang sama pada Create dan Edit; beri keterangan asal setiap cuplikan.]
...
```

**Kode sesudah:**

```jsx
...
[Tempel penggunaan komponen bersama dan potongan implementasi komponen yang relevan.]
...
```

**Penjelasan perubahan dan dampak:** Rendering bersama diekstrak menjadi tiga komponen. Parent tetap memiliki `useForm`, initial state, preview gambar, toggle komposit, dan submit Inertia. Pemeliharaan bagian bersama menjadi terpusat tanpa memindahkan seluruh tanggung jawab halaman ke komponen baru.

**Perilaku yang dipertahankan:** Placeholder, error field, pilihan/filter satuan dan komponen, pengaturan satuan dasar, estimasi harga, serta pengecualian produk sendiri saat edit.

**Pengujian/bukti:** [Isi pengujian perubahan field, tambah/hapus satuan, pilihan dasar, pilihan komponen/qty, estimasi, dan screenshot Create/Edit.]

**Penanggung jawab:** [isi]. **Commit/bukti:** [isi].

## 3.3 RF-003: Ekstraksi Resolver Wilayah

**File utama:** `app/Services/RegionPayloadResolver.php`, `app/Http/Controllers/Apps/CustomerController.php`, dan `MemberController.php`.

**Kondisi awal dan alasan:** Query wilayah dan pembentukan pasangan kode/nama berulang pada jalur pelanggan dan member. Aturan pemetaan berisiko berbeda ketika dipelihara terpisah.

**Kode sebelum:**

```php
...
[Tempel query dan pembentukan payload wilayah yang berulang.]
...
```

**Kode sesudah:**

```php
...
[Tempel pemanggilan resolver serta bagian resolve() yang menggantikan pengulangan.]
...
```

**Penjelasan perubahan dan dampak:** Pemetaan dipusatkan di resolver. Jalur POS tetap dapat melewati query untuk kode kosong. Pengujian pemetaan dapat dilakukan pada satu implementasi yang dipakai beberapa controller.

**Perilaku yang dipertahankan:** Create/update biasa, kode tidak dikenal dengan nama null, dan wilayah opsional pada POS; tidak menambahkan validasi hierarki.

**Pengujian/bukti:** [Isi create/update pelanggan dan member, wilayah kosong pada POS, kode tidak dikenal, dan screenshot data tersimpan.]

**Penanggung jawab:** [isi]. **Commit/bukti:** [isi].

## 3.4 RF-007: Ekstraksi Pencatatan Pembayaran Piutang

**File utama:** `app/Services/ReceivablePaymentRecorder.php` serta `app/Http/Controllers/Apps/ReceivableController.php` dan `app/Http/Controllers/Api/V1/ReceivableController.php`.

**Kondisi awal dan alasan:** Controller web dan API mengulang transaksi pencatatan pembayaran. Pencampuran request/response dengan operasi bisnis menyulitkan pemeliharaan konsistensi pembayaran.

**Kode sebelum:**

```php
...
[Tempel bagian transaksi pembayaran yang berulang pada controller web dan API.]
...
```

**Kode sesudah:**

```php
...
[Tempel pemanggilan record() serta bagian recorder yang menampung transaksi bersama.]
...
```

**Penjelasan perubahan dan dampak:** Recorder menangani pencatatan pembayaran dalam transaksi. Controller tetap mengurus akses, validasi request, dan respons. Perubahan aturan pembayaran tidak perlu disalin ke dua controller.

**Perilaku yang dipertahankan:** Lock sebelum pemeriksaan sisa, penolakan overpay, rekening aktif/outlet, history, status overdue/partial/paid, dan `payment_status` transaksi terkait.

**Pengujian/bukti:** [Isi pembayaran parsial/lunas, overdue, overpay, rekening nonaktif dan rollback, web/API, serta bukti UI yang benar-benar dijalankan.]

**Penanggung jawab:** [isi]. **Commit/bukti:** [isi].

## 3.5 RF-008: Ekstraksi Pencatatan Pembayaran Hutang

**File utama:** `app/Services/PayablePaymentRecorder.php` serta `app/Http/Controllers/Apps/PayableController.php` dan `app/Http/Controllers/Api/V1/PayableController.php`.

**Kondisi awal dan alasan:** Pencatatan pembayaran hutang pada web dan API mengulang pemeriksaan serta perubahan database. Pengulangan meningkatkan risiko perbedaan status atau history antarjalur.

**Kode sebelum:**

```php
...
[Tempel bagian pembayaran hutang yang berulang pada web dan API.]
...
```

**Kode sesudah:**

```php
...
[Tempel pemanggilan PayablePaymentRecorder dan transaksi yang dipindahkan.]
...
```

**Penjelasan perubahan dan dampak:** Satu recorder digunakan kedua controller. Recorder hutang tetap terpisah dari piutang karena model dan efek bisnisnya berbeda; tidak dibuat abstraksi pembayaran generik tambahan.

**Perilaku yang dipertahankan:** Transaksi/lock, batas pembayaran, rekening, history, perubahan status, otorisasi, dan bentuk respons.

**Pengujian/bukti:** [Isi pembayaran parsial/lunas, overdue, overpay, rekening nonaktif/rollback, jalur web/API, serta bukti UI.]

**Penanggung jawab:** [isi]. **Commit/bukti:** [isi].

## 3.6 RF-011: Ekstraksi Penentuan Bucket Aging

**File utama:** `app/Support/DebtAgingBucket.php`, `app/Models/Receivable.php`, dan `app/Models/Payable.php`.

**Kondisi awal dan alasan:** Kedua model memiliki algoritma penentuan bucket yang sama. Batas hari dan urutan kondisi harus dipelihara pada dua lokasi.

**Kode sebelum:**

```php
...
[Tempel kondisi pada accessor aging_bucket kedua model.]
...
```

**Kode sesudah:**

```php
...
[Tempel delegasi accessor dan DebtAgingBucket::resolve().]
...
```

**Penjelasan perubahan dan dampak:** Accessor memakai helper bersama, sehingga aturan klasifikasi dipelihara di satu tempat. Query scope dan rumus ringkasan lain tidak diubah.

**Perilaku yang dipertahankan:** Paid, tanpa jatuh tempo, current, bucket 0–30/31–60/61–90/90+, termasuk semantik pecahan hari Carbon.

**Pengujian/bukti:** [Isi hasil batas ±1 detik pada 30/60/90 hari dengan waktu acuan tetap, serta screenshot laporan aging.]

**Penanggung jawab:** [isi]. **Commit/bukti:** [isi].

## 3.7 RF-015: Satukan Aturan Validasi Retur

**File utama:** `app/Http/Requests/Rules/SalesReturnRules.php`, `app/Http/Requests/StoreSalesReturnRequest.php`, dan `app/Http/Requests/UpdateSalesReturnRequest.php`.

**Kondisi awal dan alasan:** Dua request memiliki aturan identik. Perubahan aturan pada satu request dapat membuat perilaku create dan update berbeda tanpa sengaja.

**Kode sebelum:**

```php
...
[Tempel bagian rules() yang identik pada kedua request.]
...
```

**Kode sesudah:**

```php
...
[Tempel SalesReturnRules::all() dan pemanggilannya pada kedua request.]
...
```

**Penjelasan perubahan dan dampak:** Array aturan disediakan oleh satu kelas bersama. `authorize()` dan proses penyelesaian retur/refund tidak dipindahkan atau diubah.

**Perilaku yang dipertahankan:** Field, required/nullability, exists, batas qty dan alasan, serta tipe retur.

**Pengujian/bukti:** [Isi input valid, qty tidak valid, alasan terlalu panjang, referensi tidak valid, serta respons error create/update dan screenshot form.]

**Penanggung jawab:** [isi]. **Commit/bukti:** [isi].

## 3.8 RF-032: Ekstraksi Formatter Rupiah Bersama

**File utama:** `resources/js/Utils/formatRupiah.js`, `resources/js/Pages/Dashboard/Transactions/Index.jsx`, dan `Print.jsx`.

**Kondisi awal dan alasan:** Formatter yang sama didefinisikan pada halaman kasir dan cetakan. Pemeliharaan terpisah berisiko menghasilkan tampilan nominal berbeda.

**Kode sebelum:**

```javascript
...
[Tempel definisi formatter identik dari kedua halaman.]
...
```

**Kode sesudah:**

```javascript
...
[Tempel formatRupiah() dan import sebagai formatPrice pada kedua halaman.]
...
```

**Penjelasan perubahan dan dampak:** Kedua halaman memakai fungsi bersama. Perubahan format dapat dilakukan pada satu lokasi; perhitungan total maupun pembayaran tidak berubah.

**Perilaku yang dipertahankan:** Konversi nilai kosong/string angka, locale, IDR, pembulatan, angka negatif, dan spasi NBSP.

**Pengujian/bukti:** [Isi perbandingan output nilai normal, kosong, string angka, pecahan, negatif; screenshot nominal kasir dan cetakan.]

**Penanggung jawab:** [isi]. **Commit/bukti:** [isi].

## 3.9 Kontribusi Anggota dan Review Silang

Pembagian berikut merupakan acuan tanggung jawab. Isi kolom kontribusi aktual sesuai pekerjaan yang benar-benar dilakukan; jangan mengklaim implementasi atau review yang belum dikerjakan.

| Anggota | Target utama | Kontribusi aktual: analisis, implementasi, testing, dokumentasi, demo | Reviewer | Status dan bukti review |
|---|---|---|---|---|
| Anggota 1 | RF-001, RF-003, RF-011 | [isi] | Anggota 3 | [isi/belum dilakukan] |
| Anggota 2 | RF-007, RF-008, RF-015 | [isi] | Anggota 1 | [isi/belum dilakukan] |
| Anggota 3 | RF-002, RF-032 | [isi] | Anggota 2 | [isi/belum dilakukan] |

## 3.10 Template Pencatatan Per Aktivitas

Salin blok ini untuk setiap aktivitas atau revisi tambahan:

| Elemen | Catatan |
|---|---|
| ID temuan dan nama | [RF-xxx: nama] |
| Kondisi awal dan bukti | [file, fungsi, rentang baris, commit, masalah] |
| Teknik dan alasan | [teknik serta manfaat konkretnya] |
| Perubahan sebelum/sesudah | [cuplikan yang berubah dan penjelasan] |
| Kontrak yang dipertahankan | [input, respons, izin, efek DB, UI] |
| Pengujian | [ID skenario, prasyarat, input, langkah, expected] |
| Hasil aktual | [hasil, status lulus/gagal/belum dijalankan, tanggal] |
| Penanggung jawab dan reviewer | [nama dan hasil review] |
| Commit/bukti | [tautan diff, log, screenshot] |

# 4. TESTING DAN VALIDASI

## 4.1 Tujuan dan Metode

Pengujian bertujuan memastikan perubahan struktur tidak mengubah perilaku fungsional pada area yang direfactor. Characterization test merekam perilaku awal, kemudian test yang sama dijalankan sesudah perubahan. Perbandingan UI harus memakai akun/izin, data, outlet, waktu acuan bila relevan, serta langkah yang sama.

Pisahkan data uji sebelum/sesudah agar aksi pembayaran atau update pada satu versi tidak mengubah prasyarat versi pembanding. Gunakan database lokal khusus pengujian atau salinan fixture yang sama. Jangan mereset database kerja yang berisi data penting untuk menyiapkan demo.

## 4.2 Lingkungan Pengujian

| Komponen | Lingkungan sesi implementasi yang tercatat | Pengujian ulang kelompok |
|---|---|---|
| PHP | 8.4.19 | [isi] |
| PHPUnit | 12 | [isi] |
| Database test otomatis | SQLite `:memory:` | [isi] |
| Database aplikasi lokal | MariaDB/MySQL | [isi versi] |
| Node.js | 22.23.1 | [isi] |
| Frontend | React, Inertia, Vite | [isi jika berbeda] |
| Browser/perangkat | [lengkapi dari bukti sesi terkait] | [isi] |
| Ukuran screenshot UI | 1702 × 1007 | [isi] |
| Commit dan tanggal | Commit implementasi pada bagian 1.3; [isi tanggal dari log] | [isi] |

## 4.3 Matriks Skenario Pengujian

Tabel ini adalah rencana/rujukan skenario. Kolom hasil aktual pada bukti terpisah wajib diisi berdasarkan eksekusi; daftar skenario sendiri bukan bukti lulus.

| ID test | RF | Prasyarat/data dan input | Langkah | Hasil yang diharapkan |
|---|---|---|---|---|
| T-001 | RF-001 | Akun berizin edit; produk normal dan komposit; stok awal tercatat; upload valid/tanpa upload | Jalankan update masing-masing variasi; periksa produk, gambar, pivot, audit, dan redirect | Nilai serta efek sesuai baseline; stok tidak berubah melalui alur ini. |
| T-002 | RF-001 | Input wajib tidak valid atau akun tanpa izin | Kirim update invalid dan akses tanpa izin yang sesuai baseline | Error/penolakan sama; tidak ada perubahan data yang tidak semestinya. |
| T-003 | RF-002 | Form Create/Edit; beberapa satuan dan komponen tersedia | Ubah field, tambah/hapus satuan, pilih dasar, aktifkan komposit, pilih qty | State, filter, error, dan estimasi sesuai baseline; UI tetap dapat digunakan. |
| T-004 | RF-003 | Data kode wilayah valid, kosong, dan tidak dikenal | Create/update pelanggan dan member; jalur POS tanpa wilayah | Pasangan kode/nama sesuai baseline; kode tidak dikenal bernama null; POS tetap menerima wilayah opsional. |
| T-005 | RF-007 | Piutang bersisa dengan kondisi overdue/non-overdue; rekening aktif dan izin sesuai | Bayar sebagian lalu tepat sisa pada fixture terpisah melalui web/API | History, sisa, status dan payment_status transaksi terkait sesuai baseline. |
| T-006 | RF-007 | Piutang bersisa; nominal di atas sisa atau rekening nonaktif | Kirim pembayaran melalui web/API; periksa data setelah penolakan | Penolakan sama dan transaksi rollback; history/saldo/status tidak berubah. |
| T-007 | RF-008 | Hutang bersisa; rekening aktif; izin sesuai | Ulangi pembayaran parsial dan tepat sisa melalui web/API | History, sisa dan status sama dengan baseline. |
| T-008 | RF-008 | Hutang bersisa; overpay atau rekening nonaktif | Kirim pembayaran ditolak; periksa perubahan database | Respons penolakan sesuai baseline dan tidak ada perubahan parsial. |
| T-009 | RF-011 | Waktu dibekukan; data paid, tanpa due date, current, dan batas aging | Bandingkan kedua model pada ±1 detik dari 30/60/90 hari | Output sama dengan baseline termasuk pecahan hari; jangan menambahkan pembulatan baru. |
| T-010 | RF-015 | Request Store/Update; referensi valid/tidak valid; qty dan alasan pada batas | Jalankan validator dengan data normal, kosong, qty tidak valid dan alasan terlalu panjang | Kedua request menjaga aturan awal; error field dan izin tetap sama. |
| T-011 | RF-032 | Nilai normal, kosong, string angka, pecahan dan negatif | Bandingkan output formatter awal dan utility baru; buka kasir/cetakan | String output sama termasuk pembulatan, tanda, dan NBSP. |
| T-012 | RF-002, RF-032 | Produk dan qty yang sama; total 4496, input cash 5000 pada fixture terkait | Jalankan interaksi kasir sebelum/sesudah | Total tetap 4496 dan kembalian 504; hasil format konsisten. |

Untuk setiap skenario yang dijalankan ulang, catat ID fixture, nilai awal/akhir yang relevan, respons atau pesan aktual, dan tautan buktinya. Pengujian otorisasi harus memakai akun/token dengan izin yang tepat; jangan menyimpulkan izin aman hanya dari pengujian admin.

## 4.4 Hasil Pengujian yang Sudah Tercatat

Angka berikut dirujuk dari [catatan implementasi](../../docs/refactoring-8-targets.md) dan [hasil-validasi.json](../../docs/refactoring/hasil-validasi.json). Test tidak dijalankan ulang saat pembuatan template ini.

| Pemeriksaan | Hasil sesi implementasi |
|---|---|
| Suite awal | 581 test: 580 lulus, 1 skipped; 2452 assertions; 0 error/failure |
| Characterization tambahan pada source awal | 20 test lulus; 195 assertions |
| Suite sesudah, termasuk characterization tambahan | 601 test: 600 lulus, 1 skipped; 2647 assertions; 0 error/failure |
| Test frontend Node | 5 lulus; 0 gagal |
| Build Vite | Berhasil |
| Pint pada file PHP terkait | Format telah diterapkan |
| `git diff --check` | Lulus |
| Konsistensi fixture database lokal | 84 pemeriksaan lulus; bukan pengujian submit UI |
| Browser lokal | Delapan target dibuka; interaksi satuan/komponen/estimasi dan input kasir diperiksa tanpa submit |

Dua puluh characterization test sudah termasuk dalam 601 test sesudah perubahan. Pertambahan jumlah test bukan persentase coverage atau ukuran perbaikan kualitas.

Satu test yang skipped sama sebelum/sesudah: `V1DineInApiTest::test_dine_table_qr_returns_png`, karena ekstensi Imagick tidak tersedia pada runtime pengujian.

**Bukti terlampir:** [Tambahkan log/JUnit/screenshot terminal yang cocok dengan angka di atas. Untuk pengujian ulang, catat hasil baru beserta tanggal, runtime, dan commit; jangan menimpa sejarah hasil tanpa keterangan.]

## 4.5 Perintah Pengujian

Jalankan dari root project setelah dependency terpasang. Konfigurasi test menggunakan SQLite in-memory; periksa konfigurasi lokal sebelum menjalankan ulang.

```bash
php -d memory_limit=768M vendor/bin/phpunit
php -d memory_limit=768M vendor/bin/phpunit tests/Feature/Refactoring
node tests/Frontend/refactoring.test.cjs
PUPPETEER_SKIP_DOWNLOAD=true npm run build
git diff --check
```

PHPUnit dijalankan langsung agar batas memori berlaku pada proses test. Build memeriksa keberhasilan kompilasi aset, bukan membuktikan seluruh perilaku UI atau bisnis benar. Pemeriksaan format/diff juga bukan pengganti test fungsional.

**Hasil pengujian ulang kelompok:** [belum dijalankan / isi perintah, waktu, commit dan hasil aktual].

## 4.6 Dokumentasi Perbandingan UI

Screenshot tersedia pada folder [sebelum](../audit-pos/SS_UI/sebelum/) dan [sesudah](../audit-pos/SS_UI/sesudah/). Pilih pasangan dengan data dan halaman yang sebanding. Beri caption yang menyebut RF, skenario, data, dan kondisi layar.

Salin format berikut untuk setiap skenario:

| Elemen | Isi |
|---|---|
| ID pengujian UI | UI-RF-xxx-01 |
| Target refactoring | [RF dan nama] |
| Halaman/fitur | [isi] |
| Akun/izin/outlet | [isi tanpa password/token] |
| Prasyarat dan data uji | [identitas fixture, nilai awal, waktu acuan bila perlu] |
| Input | [isi nilai field/nominal/qty] |
| Langkah | 1. [buka halaman] 2. [aksi] 3. [periksa hasil] |
| Hasil yang diharapkan | [isi kontrak perilaku] |
| Hasil aktual sebelum | [isi / belum dijalankan] |
| Bukti sebelum | [nomor gambar dan path/tautan screenshot] |
| Hasil aktual sesudah | [isi / belum dijalankan] |
| Bukti sesudah | [nomor gambar dan path/tautan screenshot] |
| Status | [lulus / gagal / belum dijalankan] |
| Analisis perbandingan | [nilai, pesan, state UI dan efek DB yang dibandingkan] |

**Gambar [nomor].** [RF, tampilan sebelum, skenario dan data.]

[Tempel screenshot sebelum.]

**Gambar [nomor].** [RF, tampilan sesudah, skenario dan data.]

[Tempel screenshot sesudah.]

## 4.7 Keterbatasan Validasi

1. Screenshot pembayaran lunas atau retur dapat berasal dari fixture database. Tampilan tersebut tidak otomatis membuktikan tombol pembayaran/penyelesaian retur telah dijalankan melalui UI.
2. Submit create/update produk, pembayaran, dan retur belum diulang pada browser dengan database lokal dalam sesi implementasi yang dirujuk. Kontrak HTTP terkait diuji menggunakan SQLite. Jika kelompok menjalankannya kemudian, tambahkan bukti aktual.
3. Pengujian transaksi bersamaan pada MariaDB/MySQL belum dijalankan. Transaksi dan `lockForUpdate()` dipertahankan, tetapi suite SQLite tidak membuktikan perilaku concurrency MySQL.
4. Test komponen frontend memakai stub `Input`/`InputSelect`; hasilnya bukan end-to-end widget atau jaringan Inertia.
5. Kegagalan edit produk yang pernah dilaporkan pada UI belum ditelusuri dan tidak diklaim diperbaiki oleh paket refactoring ini.
6. Tidak tersedia angka coverage terukur untuk mendukung klaim seluruh perilaku sistem terlindungi.

**Validasi tambahan kelompok:** [isi hanya jika sudah dilakukan; selain itu tulis belum dijalankan].

# 5. KESIMPULAN DAN REKOMENDASI

## 5.1 Kesimpulan

Delapan target implementasi memusatkan alur update produk, komponen form, pemetaan wilayah, pembayaran piutang/hutang, bucket aging, validasi retur, dan format Rupiah. Perubahan struktur mengurangi lokasi implementasi aturan yang sama serta memisahkan operasi bisnis dari penanganan HTTP pada modul pembayaran.

Hasil pengujian yang tercatat mendukung kesamaan perilaku pada skenario yang diuji. Hasil tersebut harus dibaca bersama keterbatasan pengujian UI, concurrency, dan test yang dilewati; bukan sebagai jaminan seluruh perilaku aplikasi telah tervalidasi.

[Tambahkan kesimpulan kelompok berdasarkan pengujian ulang, review silang, dan demo yang benar-benar sudah dilakukan. Jangan menyatakan semua UI lulus apabila belum diuji.]

## 5.2 Keterbatasan Pengerjaan

Implementasi dibatasi pada delapan target. Kandidat lain tetap menjadi backlog dan tidak semuanya harus dikerjakan dalam tugas ini. Prioritas berikutnya perlu mempertimbangkan manfaat, risiko, dan ketersediaan test.

[Tambahkan keterbatasan aktual kelompok: waktu, lingkungan, integrasi eksternal, pengujian yang belum dijalankan, dan alasan penundaan.]

## 5.3 Rekomendasi

1. Lengkapi pengujian submit UI sebelum/sesudah menggunakan fixture, akun/izin, dan langkah yang sama.
2. Selesaikan review silang serta dokumentasikan temuan dan tindak lanjutnya.
3. Tambahkan pengujian concurrency pembayaran/persediaan pada database yang sesuai lingkungan aplikasi jika area tersebut akan dikembangkan.
4. Kerjakan backlog bertahap dengan characterization test sebelum mengubah modul berisiko.
5. Pisahkan bug fix dan penambahan fitur dari refactoring agar alasan perubahan perilaku dapat ditelusuri.

[Tambahkan rekomendasi khusus berdasarkan hasil kelompok; jangan mengklaim rekomendasi sudah diimplementasikan.]

# DAFTAR PUSTAKA

1. Aryadwiputra. Repositori Point of Sales. [https://github.com/aryadwiputra/point-of-sales](https://github.com/aryadwiputra/point-of-sales). [Isi tanggal akses sebenarnya.]
2. Kelompok 3. Repositori tugas refactoring. [https://github.com/ItsDannz/kelompok3-epl](https://github.com/ItsDannz/kelompok3-epl). [Isi tanggal akses sebenarnya.]
3. [Sumber teori refactoring yang benar-benar dibaca; judul, penulis, tahun, tautan dan tanggal akses sesuai format kampus.]
4. [Dokumentasi tooling/framework yang benar-benar digunakan.]

# LAMPIRAN

## Lampiran A. Source Code, Versi, dan Petunjuk Menjalankan

| Artefak | Referensi |
|---|---|
| Source audit awal | `81a91b8fc0ef479be9e0c751c0d11370d89dd8a2` pada repositori sumber |
| Source sebelum pada repositori kelompok | `d0f42ea581e0d39f11eef1a88524daacba5ef3c7` |
| Source implementasi sesudah | `7c9ba504818ad24b7bbe906b0103ccb86d5846af` |
| Branch | `codex/audit-refactoring` |
| Perbandingan | [diff implementasi](https://github.com/ItsDannz/kelompok3-epl/compare/d0f42ea...7c9ba50) |
| Petunjuk setup | [Cantumkan file petunjuk yang telah dicoba kelompok, versi dependency, konfigurasi database lokal dan langkah menjalankan.] |

Dependency dan `.env` perlu disiapkan pada setiap mesin. Dokumentasikan nama konfigurasi yang dibutuhkan tanpa menyertakan password, token, atau isi `.env` pribadi. Pastikan petunjuk mencakup backend, frontend, database, storage gambar, dan layanan tambahan hanya bila digunakan dalam demo.

## Lampiran B. Bukti Pengujian

[Tempel log baseline, characterization, suite sesudah, frontend, build, serta bukti pemeriksaan lain yang digunakan. Beri caption, tanggal, commit dan keterangan status.]

## Lampiran C. Screenshot UI Tambahan

[Tempel pasangan screenshot sebelum/sesudah untuk setiap RF yang belum dimuat pada bagian 4.6. Bedakan tampilan fixture dari bukti eksekusi alur UI.]

## Lampiran D. Rencana Demo 15 Menit

| Waktu | Penyaji | Materi |
|---|---|---|
| Menit 0–5 | Anggota 1 | Gambaran proyek dan versi; alasan/perubahan RF-001, RF-003, RF-011; tunjukkan cuplikan source sebelum/sesudah dan alur UI terkait dengan data sama. |
| Menit 5–10 | Anggota 2 | RF-007, RF-008, RF-015; tunjukkan source, kontrak pembayaran/validasi dan bukti test; jalankan alur UI yang telah disiapkan dengan kondisi pembanding sama. |
| Menit 10–15 | Anggota 3 | RF-002, RF-032; tunjukkan komponen/formatter sebelum/sesudah, form produk dan nominal POS/cetakan; jelaskan hasil pengujian serta batas validasi. |

Sebelum demo, siapkan dua versi aplikasi atau bukti source yang mudah dibandingkan, data uji setara, akun/izin, dependency, dan perintah menjalankan. Latih durasi agar ketiga anggota memperoleh waktu presentasi. Jangan mengganti bukti UI dengan klaim build berhasil.

**Wakil pengunggah PDF:** [Nama anggota].

**Nama file akhir:** `Kelompok3_Laporan_Refactoring_dan_Testing.pdf`.

**Status dokumen:** Template; lengkapi placeholder, verifikasi bukti, rapikan di Google Docs, lalu ekspor satu PDF untuk dikumpulkan oleh satu wakil kelompok.
