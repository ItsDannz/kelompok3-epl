# Implementasi delapan target refactoring

Diterapkan di checkout `/home/zdann/Documents/epl/pos_platform/point-of-sales`, branch `codex/refactor-8-targets`, dari commit `81a91b8fc0ef479be9e0c751c0d11370d89dd8a2`. Implementasi dicatat dalam commit refactoring. Untuk identitas commit yang memuat dokumentasi ini, gunakan `git log -1 -- docs/refactoring-8-targets.md`. Perubahan `package-lock.json` yang telah ada sebelum implementasi dipertahankan.

Komentar `RF-xxx: nama refactoring` tersedia pada setiap file aplikasi yang terkait. Penanda adalah komentar source, sehingga tidak menambahkan label ke UI pengguna.

| Penanda | Implementasi dan alasan | Kontrak yang dipertahankan |
|---|---|---|
| RF-001: Satukan alur update produk | `ProductController::update()` menyusun atribut sekali melalui `productUpdateAttributes()`. Cabang upload hanya mengurus penggantian gambar, lalu menggunakan satu jalur update, sinkronisasi, audit, dan redirect. Menghilangkan dua blok update yang sama. | Validasi dan default atribut, path penyimpanan/penghapusan gambar, pemilihan syncUnits/syncComponents, stok tidak diedit, audit harga/produk dan redirect tetap sama. |
| RF-002: Ekstraksi komponen form produk bersama | Informasi dasar, baris satuan, dan baris komponen dipindahkan ke `ProductFormFields`, `ProductUnitRows`, `ProductCompositeRows`. Create dan Edit memakai komponen yang sama sehingga perubahan field tidak perlu disalin di dua halaman. | Parent tetap memiliki useForm, initial state, submit Inertia, preview gambar, toggle komposit dan field harga/stok. Placeholder Create/Edit, filter satuan/komponen, error field, estimasi, dan pengecualian produk sendiri tetap dipertahankan. |
| RF-003: Ekstraksi resolver wilayah | `RegionPayloadResolver::resolve()` membentuk pasangan kode/nama wilayah untuk Customer dan Member; jalur POS dapat melewati query kode kosong. Mengurangi query/pemetaan berulang. | Create/update biasa tetap menyimpan kode tak dikenal dengan nama null; wilayah POS tetap opsional. Tidak menambahkan validasi hierarki atau mengganti aturan keanggotaan. |
| RF-007: Ekstraksi pencatatan pembayaran piutang | `ReceivablePaymentRecorder::record()` memusatkan transaksi pembayaran yang sebelumnya disalin di controller web dan API. Controller tetap menangani akses, validasi input dan respons. | Lock row sebelum pemeriksaan sisa, batas nominal, rekening aktif/outlet, history, status overdue/partial/paid, update payment_status transaksi dan respons web/API tetap sama. |
| RF-008: Ekstraksi pencatatan pembayaran hutang | `PayablePaymentRecorder::record()` dipakai controller web/API hutang. Recorder dipisahkan dari piutang karena efek dan modelnya berbeda. | Transaksi/lock, penolakan overpay/rekening tidak tersedia, history, status, otorisasi controller dan respons tetap sama. |
| RF-011: Ekstraksi penentuan bucket aging | `DebtAgingBucket::resolve()` dipanggil accessor `aging_bucket` kedua model. Menghilangkan dua implementasi identik. | Prioritas paid, no_due_date, current, bucket 0–30/31–60/61–90/90+, serta semantik pecahan hari Carbon dipertahankan. Query scope dan rumus ringkasan lain tidak diubah. |
| RF-015: Satukan aturan validasi retur | `SalesReturnRules::all()` dipakai rules() request Store dan Update. Mencegah aturan identik berbeda saat salah satu request diedit. | authorize(), nama field, required/nullability, exists, batas qty/alasan dan tipe return tetap sama. Penyelesaian retur/refund tidak dipindahkan atau diubah. |
| RF-032: Ekstraksi formatter Rupiah bersama | `formatRupiah()` di Utils diimpor sebagai `formatPrice` oleh halaman kasir dan Print. Menghilangkan formatter identik. | Konversi Number(value || 0), locale id-ID, IDR, pembulatan, tanda negatif, dan spasi NBSP sama. Tidak mengubah perhitungan pembayaran. |

## Lokasi kode

- RF-001: `app/Http/Controllers/Apps/ProductController.php`.
- RF-002: `resources/js/Components/Dashboard/Products/{ProductFormFields,ProductUnitRows,ProductCompositeRows}.jsx` dan `resources/js/Pages/Dashboard/Products/{Create,Edit}.jsx`.
- RF-003: `app/Services/RegionPayloadResolver.php` dan `app/Http/Controllers/Apps/{CustomerController,MemberController}.php`.
- RF-007: `app/Services/ReceivablePaymentRecorder.php` dan controller Receivable di `Apps/` serta `Api/V1/`.
- RF-008: `app/Services/PayablePaymentRecorder.php` dan controller Payable di `Apps/` serta `Api/V1/`.
- RF-011: `app/Support/DebtAgingBucket.php` dan `app/Models/{Receivable,Payable}.php`.
- RF-015: `app/Http/Requests/Rules/SalesReturnRules.php` dan `StoreSalesReturnRequest.php`/`UpdateSalesReturnRequest.php`.
- RF-032: `resources/js/Utils/formatRupiah.js` dan `resources/js/Pages/Dashboard/Transactions/{Index,Print}.jsx`.

## Pengujian yang benar-benar dijalankan

| Pemeriksaan | Hasil |
|---|---|
| Suite awal | 581 test: 580 lulus, 1 skipped; 2452 assertions; 0 error/failure |
| Characterization tambahan pada source awal | 20 test lulus; 195 assertions |
| Suite sesudah, termasuk tambahan | 601 test: 600 lulus, 1 skipped; 2647 assertions; 0 error/failure |
| Test frontend Node | 5 lulus; 0 gagal |
| Build Vite | Berhasil |
| Pint pada file PHP terkait | Format telah diterapkan |
| Git diff --check | Lulus |
| Konsistensi data dummy lokal | 84 pemeriksaan lulus; bukan pengujian submit UI |
| Browser lokal | Delapan target dibuka; tambah dua satuan, pilih komponen/qty, estimasi 20000, dan input cash 5000/total 4496/kembalian 504 diperiksa tanpa submit |

Satu test yang dilewati sama sebelum/sesudah: `V1DineInApiTest::test_dine_table_qr_returns_png`, karena ekstensi Imagick tidak tersedia pada runtime pengujian. Hasil terstruktur dan hash file: [hasil-validasi.json](refactoring/hasil-validasi.json).

Characterization tests berada di `tests/Feature/Refactoring/`: update normal/komposit dengan/tanpa upload, stok/pivot/audit, wilayah seluruh form dan POS, kode tak dikenal, pembayaran overdue/parsial/lunas/overpay lewat web dan API, rollback rekening nonaktif, batas aging ±1 detik di 30/60/90 hari, serta input valid/tidak valid pada kedua request retur. Test yang sama telah dijalankan sebelum dan sesudah perubahan.

`tests/Frontend/refactoring.test.cjs` menguji perubahan field, tambah/filter/hapus satuan, pilihan dasar, filter komponen, estimasi harga dan output Rupiah. Input/InputSelect di-stub; ini melindungi kontrak komponen, bukan end-to-end widget atau jaringan Inertia.

## Menjalankan ulang

Gunakan PHP 8.3+ dengan ekstensi sesuai Composer dan SQLite untuk test, Node/npm serta dependency project yang sudah terpasang. Runtime yang dipakai pada sesi ini PHP 8.4.19 dan Node 22.23.1.

```bash
php -d memory_limit=768M vendor/bin/phpunit
php -d memory_limit=768M vendor/bin/phpunit tests/Feature/Refactoring
node tests/Frontend/refactoring.test.cjs
PUPPETEER_SKIP_DOWNLOAD=true npm run build
git diff --check
```

PHPUnit dijalankan langsung agar batas memori 768 MB berlaku pada proses test; wrapper artisan sempat menjalankan subprocess dengan batas 128 MB sehingga baseline pertama berhenti pada Dompdf. Pengulangan baseline langsung di atas selesai. SQLite `:memory:` dipilih dari `phpunit.xml`; tidak menjalankan migrate:fresh/seeder demo pada database lokal.

Source sebelum dapat dilihat lewat `git show 81a91b8:path/file`; source sesudah ada pada working tree. Gunakan `git diff` untuk file terlacak dan periksa file baru yang ditampilkan `git status --short`. Snapshot 15 file source awal dan patch per RF juga tersimpan di folder artefak sesi `/home/zdann/Documents/ChatGPT/epl/output/implementasi-8-refactoring/`.

## Bukti UI dan batas validasi

Dua belas screenshot sesudah implementasi berukuran 1702 × 1007 disimpan pada folder RF di `/home/zdann/Documents/epl/bukti_ui/sesudah/`, beserta indeks/keterangan. Folder `sebelum` dipertahankan. Data dummy yang sama dibaca oleh UI; screenshot pembayaran lunas/retur adalah tampilan fixture DB, bukan bukti tombol pembayaran atau penyelesaian retur baru saja ditekan.

Submit create/update produk, pembayaran, dan retur pada browser dengan database lokal belum diulang pada sesi implementasi. Kontrak HTTP-nya dilindungi suite SQLite. Kegagalan edit produk yang pernah dilaporkan pada UI belum ditelusuri atau diklaim diperbaiki oleh refactoring ini. Uji concurrency MariaDB/MySQL sungguhan belum dijalankan; transaksi dan lockForUpdate tetap dipertahankan. Tidak ada angka coverage atau klaim semua perilaku sistem telah tervalidasi.

Implementasi ini mengubah struktur kode. Tidak ada migrasi database, route, hak akses, fitur baru, perbaikan bug terpisah, atau perubahan perhitungan bisnis dalam paket ini.
