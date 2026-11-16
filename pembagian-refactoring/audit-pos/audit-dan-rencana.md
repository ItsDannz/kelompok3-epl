# Audit dan rencana kerja “Refactoring dan Testing terhadap Studi Kasus”

**Tahap ini hanya audit dan perencanaan. Tidak ada source code, manifest, lockfile, migration, route atau test proyek yang direfactor.** Setup hanya menambahkan dependensi/generated assets dan environment/database lokal yang diabaikan Git. Tidak ada commit implementasi atau source versi “sesudah”.

Dokumen ini mempunyai urutan A–I. Detail panjang dipisahkan tanpa menghapus temuan: [B: bukti lengkap per ID](B-inventaris-refactoring.md), [F: testing](testing.md), [temuan non-refactoring](temuan-di-luar-refactoring.md), [cakupan per file](coverage.csv), [pembagian seluruh ID](pembagian-semua-temuan.csv), [matriks semua RF→test](matriks-RF-test.csv), [semua clone mentah](lampiran-semua-clone.md), dan [bukti runtime](runtime.md). Path pada lampiran mengacu checkout yang sama; link GitHub memakai commit hash tetap.

## A. Ringkasan proyek, branch/commit, dan cakupan audit

Repositori: https://github.com/aryadwiputra/point-of-sales.git. Checkout lokal `/home/zdann/Documents/ChatGPT/epl/point-of-sales`. Branch **main**, commit **81a91b8fc0ef479be9e0c751c0d11370d89dd8a2**, tanggal commit10 November2026; judul `test(api): add V1 API test suites`. Audit15 November2026, Asia/Jakarta. Hash sumber dipertahankan dan setiap cuplikan RF menunjuk baris pada commit ini.

Project telah benar-benar dijalankan lokal, bukan hanya dibaca README: PHP8.4.15+Composer2.11.2, Node22.23.1/npm10.9.8, SQLite khusus audit, migrasi100 file, seeder dasar lalu demo opt-in, built assets Vite dan `php artisan serve`. Browser berhasil login, membuka shift, menambah produk dan menyelesaikan checkout tunai hingga invoice. Daftar/form produk, piutang, hutang, insights dan meja dine-in juga dibuka untuk memeriksa bentuk UI; operasi CRUD modul-modul tambahan itu belum diuji lengkap.

Baseline aktual: **580 test lulus,1 skipped,2452 assertions**. Build frontend lulus. Checkout UI totalRp4.496/cashRp5.000/changeRp504 menghasilkan invoice paid, mutation stok50→49, aggregate inventory tetap konsisten. Layanan Node WhatsApp berjalan lokal; endpoint tanpa token mengembalikan503 seperti implementasinya. Tidak ada provider pembayaran/WhatsApp nyata yang dipanggil. Rincian, kegagalan setup dan batas test ada di F/I/runtime.md.

### Stack dan arsitektur aktual

| Area | Implementasi yang diperiksa | Relasi dan tanggung jawab |
|---|---|---|
| Backend | composer.lock: Laravel13.20.0/PHP^8.3; inertia-laravel3.1.1; Eloquent | Monolit modular lewat routes→middleware→controllers→services/models; service layer sudah ada, bukan proyek tanpa service layer |
| UI | React19.2.7, Inertia React3.6.1, Vite5.4.21, Tailwind3 | `resources/js/app.jsx` resolve seluruh Pages via import.meta.glob; Ziggy route helper; hooks/context untuk theme/online/tour/scanner |
| API/auth | routes/api.php, Sanctum4, Spatie Permission8.3; API resources dan ApiResponder/ApiResponse | Token abilities dan permissions web berbeda mekanisme; V1 banyak mewarisi controller Apps/Reports untuk helper protected; step_up web vs cache token API |
| POS | Apps/TransactionController dan Api/PosApiController→CheckoutService | CheckoutContext/Result/Fingerprint sudah ada. Preview harga/promo→loyalty/voucher/tax→tender→sale/detail/profit→stock/mutation→cart cleanup→loyalty/receivable; gateway dipanggil setelah execute |
| Inventory | Product/ProductWarehouse, stock opname/transfer, ProductBatch, UnitConversion, StockMutationService | `product_warehouse.stock` stok operasional; `products.stock` aggregate; transaksi dan locking perlu dipertahankan. Produk komposit mengurangi komponennya; FEFO menyimpan allocation per detail |
| Purchasing/finance | PO→GoodsReceiving→payable; supplier return; sales return; receivable/payable payments/aging | Ada service purchasing, controller masih menyimpan sejumlah operasi keuangan. Retur dapat restock/refund/store-credit dan memengaruhi profit/piutang |
| CRM/pricing | PricingService, LoyaltyService, PriceListService, CustomerSegmentationService, CrmAutomationService | Promo standar/qty-break/bundle/buy-get, voucher/poin/tier, segments/campaign/reminders; akses outlet dan customer berpengaruh pada harga |
| Dine-in/public | DineMenuController, public DineOrderController, Apps/API DineOrder, DineOrderService; PublicPortal/Document | QR meja/order status memakai token; accept membuat Transaction dan inventory. pay_online dine-in ditolak, pay_at_counter didukung; invoice publik memerlukan access token |
| Reports/documents | DashboardController, Reports/*, Exports/Imports, DocumentController, ThermalPrintService, Blade PDF | Query/aggregate dan view data disiapkan di controller; React invoice/thermal, PDF dan ESC/POS adalah renderer berbeda |
| Offline/PWA | public/sw.js, Utils/offlineDb.js, Context/OnlineStatusContext, POS page | IndexedDB antrean; flush ke API sync memakai UUID+fingerprint; cart server-side, bukan keseluruhan POS tersedia offline |
| WhatsApp | PHP WhatsAppService→HTTP localhost3001→Express/whatsapp-web.js | Service proses terpisah dengan token header, QR/session/lifecycle; tidak otomatis ikut root dev stack |
| Data/ops |100 migrations,13 seeders,3 factories,9 commands; routes/console.php | Seeder utama4 seeder esensial+warehouse, tanpa default user; demo opt-in. Scheduler CRM01:00/01:15,reorder02:00,expiry hourly sesuai timezone konfigurasi app |
| Pengujian | PHPUnit12.5.31;88 file PHP tests; SQLite:memory: dan fake drivers | Sebagian besar feature/integration; Unit/ExampleTest hanya assertTrue(true). Tidak ada suite JS/Node atau npm test/lint/typecheck |

`route:list --json` pada runtime menghasilkan495 entry:218 berawalan api/,277 web/internal/framework. Ini jumlah runtime termasuk route package/framework, bukan klaim495 route buatan mahasiswa. Bukan SPA REST murni: sebagian besar web memakai Inertia, API adalah adapter JSON tambahan. Tidak ada alasan merombak ke microservices/repository generik untuk tugas ini.

### Cakupan dan metode

Inventaris dari **git ls-files:911 file**. Kategori kode PHP/JS/JSX/CSS berjumlah**682 file/102.523 physical lines**, tidak termasuk file JSON. Ini hitungan fisik script Python, bukan SLOC dan bukan kompleksitas. Semua tracked source masuk pemetaan struktur, hash, penelusuran simbol/pola; PHP non-Blade serta seluruh JS/JSX berhasil diparse. CSS/Blade dibaca sebagai teks/clone dan konteks view, bukan PHP AST. Terdapat**121 file dengan bukti kontekstual RF**, lihat evidence-locations.json; angka itu bukan jumlah seluruh file yang sempat dibaca dan bukan coverage testing.

**Batas klaim:** cakupan inventaris/analisis statik lintas seluruh direktori selesai; pendalaman semantik manual seluruh baris dari682 file belum diklaim selesai. coverage.csv secara sengaja membedakan file yang memiliki pendalaman bukti kandidat dengan file yang baru memperoleh pemeriksaan struktural. Jadi91 RF adalah seluruh kandidat yang terkonfirmasi dalam audit ini, bukan jaminan bahwa semua kemungkinan smell di setiap fungsi telah ditemukan. Jangan menuliskan “seluruh kode bebas smell selain91 temuan” dalam laporan kelompok.

| Bagian | Jumlah file kode | Pemeriksaan dan keputusan |
|---|---:|---|
| Controllers web/Apps/Reports/Api |103|Peta semua method, routes/callsite, ukuran dan clone; pendalaman POS, finance, reports, settings, CRM, admin, dine/public serta pasangan API; bukti RF spesifik |
| Requests/middleware/resources/response/traits |15 requests,10 middleware,9 resources|Rules dan auth/serialization dipetakan;15 requests dan custom middleware dibaca; shared props ditandai, guard kecil tidak dipaksa menjadi smell; ApiResponder yang sudah ada tidak disarankan dibuat ulang |
| Models/services/support/provider/exception |59 models,27 services,5 support|Relasi/casts/events/data contracts dan all method metadata; service inti ditelusuri; model Eloquent relasi/fillable pendek bukan Large Class hanya karena banyak kolom |
| Commands/imports/exports |9/2/3|Signature/caller/queries/output; mutasi expiry, penomoran, import mapping ditandai; query optimization dipisahkan |
| Frontend |186 JS/JSX,2 CSS;2 locale JSON tambahan|Semua JS/JSX AST, hook count, import/page resolution dan clone; pendalaman komponen bisnis+duplikasi; CSS tokens/i18n/config menjadi konteks, bukan target abstractions tanpa alasan |
| Blade views |7|App bootstrap dan6 template PDF; perhitungan/markup struk dan debt statement; dynamic view names dari DocumentController ikut ditelusuri |
| Database |100 migrations,13 seeders,3 factories|Peta schema/data operations, seeder order, fresh migration/seed; historical schema tidak direfactor dalam inti; runtime model coupling dicatat RF-091 |
| Tests |88|Semua method inventory dan full execution; assertions/fixture penting ditelusuri, duplikasi setup ditandai; ketidakcukupan frontend/Node ditulis terpisah |
| Routes/config/lang/bootstrap/public/WA |4 routes,17 configs,8 lang PHP; public2 kode dan WA1|Daftar route/middleware/schedule dan manifest/env diperiksa; lang enum/output hanya konteks; service worker dan WA tidak diabaikan |
| Docs/manifests/lockfiles |Seluruh daftar dalam inventory.json|README, AGENTS, architecture, setup/config/demo/manual/feature docs jadi orientasi; klaim temuan wajib dikonfirmasi kode; versi dari lock/instalasi |
| Dikecualikan dari sasaran |vendor,node_modules,public/build,cache/compiled views,generated OpenAPI/dependency assets dan147 aset biner tracked|Kode pihak ketiga/hasil build tidak menjadi RF; manifest/lock tetap diperiksa. Screenshots README bukan bukti runtime, screenshot audit diambil sendiri |
| Tidak dipaksa direfactor |Framework skeleton/config konvensional, data deklaratif, model relasi kecil, wrapper delegasi sederhana|Duplikasi deklaratif/boilerplate belum tentu manfaat ekstraksinya melebihi coupling baru; semua clone mentah tetap tersedia |

Pencarian dead code memeriksa import, barrel exports, `import.meta.glob('./Pages/**/*.jsx')`, routes, container `app(...)`, scheduler signatures, Eloquent relations/accessor/events dan template name dinamis. Tidak menghapus relasi/accessor, public service API atau pages hanya karena `rg` tidak menemukan panggilan literal.

### Metrik yang benar-benar diukur

| Metrik | Hasil | Metode/batas |
|---|---:|---|
| POS Index.jsx|2083 baris;31useState,10useEffect,18useMemo,4useCallback|Physical lines + Babel AST call counts; bukan complexity score |
| Product Create/Edit|767/798 baris|Physical lines; clone snippets pada RF-002 |
| Insights React / Print|1300/1045 baris|Physical lines, tidak otomatis berarti semua harus dipecah |
| HandleInertiaRequests::share|258 baris|PHP AST getStartLine/getEndLine; termasuk comments/blank/closure |
| DineOrderService::accept|191 baris|PHP AST |
| PosApiController::checkout/syncOne|181/153 baris|PHP AST |
| PricingRuleController::validateRule|173 baris|PHP AST |
| CheckoutService::processCartItems|165 baris;8 parameter|PHP AST |
| StockMutationService::recordMutation|10 parameter|PHP AST |
| Clone detection|204 clone,5239 duplicated lines dari99672 lines pada603 sources;5,2562% menurut alat|jscpd5.4.3, min-lines12/min-tokens90/default formats. Angka khusus corpus/algoritma alat, bukan angka semua682 source atau estimasi pengurangan code |

File `php-method-metrics.json` berisi2279 method PHP yang terparse, termasuk tests/migration. `decision_nodes_including_closures` menghitung AST if/elseif/loop/catch/ternary/boolean and/or di body termasuk nested closures. **Itu bukan McCabe cyclomatic complexity**: switch/match/null-coalescing tidak dihitung seperti standar CC; tidak boleh dipakai sebagai angka CC atau quality grade. Ukuran/clone digunakan untuk mengarahkan review; masalah struktur dibuktikan lewat tanggung jawab/duplikasi pada B.

## B. Inventaris lengkap kandidat terkonfirmasi beserta bukti

**91 RF:8 inti,60 opsional,23 ditunda.** Setiap ID memiliki satu pemilik, modul/file/simbol/rentang, cuplikan source sebelum, smell+dampak, teknik+alasan, perubahan USULAN, perilaku yang dijaga, affected modules/dependensi, prioritas/risiko/estimasi, kriteria selesai dan test. Seluruh detail berada dalam [B-inventaris-refactoring.md](B-inventaris-refactoring.md); tidak diganti hanya dengan judul ringkasan.

Ringkasan seluruh kandidat di bawah. P1 berarti dipilih untuk tugas; P2 merupakan backlog opsional; P3 ditunda. Prioritas ini mengukur kesesuaian scope akademik, bukan tingkat keparahan bug produksi.

|ID|Modul/kandidat|Primary|Scope|Risiko|
|---|---|---|---|---|
|[RF-001](B-inventaris-refactoring.md#rf-001)|Payload pembaruan produk diduplikasi pada cabang gambar|Anggota 1|Inti|sedang|
|[RF-002](B-inventaris-refactoring.md#rf-002)|Form produk Create/Edit menggandakan field dan editor baris|Anggota 3|Inti|sedang|
|[RF-003](B-inventaris-refactoring.md#rf-003)|Resolusi wilayah customer/member berulang|Anggota 1|Inti|rendah–sedang|
|[RF-004](B-inventaris-refactoring.md#rf-004)|Query statistik dan riwayat customer/member berulang|Anggota 1|Opsional|sedang|
|[RF-005](B-inventaris-refactoring.md#rf-005)|Rangkaian dropdown wilayah pada form pelanggan/member berulang|Anggota 3|Opsional|sedang|
|[RF-006](B-inventaris-refactoring.md#rf-006)|Detail customer dan member menggandakan panel riwayat|Anggota 3|Opsional|rendah–sedang|
|[RF-007](B-inventaris-refactoring.md#rf-007)|Pencatatan pembayaran piutang diduplikasi web/API|Anggota 2|Inti|sedang–tinggi|
|[RF-008](B-inventaris-refactoring.md#rf-008)|Pencatatan pembayaran hutang diduplikasi web/API|Anggota 2|Inti|sedang–tinggi|
|[RF-009](B-inventaris-refactoring.md#rf-009)|Filter dan dekorasi daftar piutang/hutang web/API berulang|Anggota 2|Opsional|sedang|
|[RF-010](B-inventaris-refactoring.md#rf-010)|Scoping hutang berulang di controller dan aging service|Anggota 2|Opsional|tinggi|
|[RF-011](B-inventaris-refactoring.md#rf-011)|Perhitungan aging pada model piutang/hutang identik|Anggota 1|Inti|sedang|
|[RF-012](B-inventaris-refactoring.md#rf-012)|Agregasi aging service berulang|Anggota 2|Opsional|rendah–sedang|
|[RF-013](B-inventaris-refactoring.md#rf-013)|Finalisasi stock opname menggandakan transaksi inventory|Anggota 2|Tunda|tinggi|
|[RF-014](B-inventaris-refactoring.md#rf-014)|Completion retur penjualan dan API menggandakan settlement|Anggota 2|Tunda|tinggi|
|[RF-015](B-inventaris-refactoring.md#rf-015)|Aturan request create/update retur penjualan identik|Anggota 2|Inti|rendah|
|[RF-016](B-inventaris-refactoring.md#rf-016)|Validasi pricing rule sekaligus menormalisasi banyak bentuk promo|Anggota 1|Tunda|tinggi|
|[RF-017](B-inventaris-refactoring.md#rf-017)|Pricing buildPreview mencampur beberapa tahap algoritma|Anggota 1|Tunda|tinggi|
|[RF-018](B-inventaris-refactoring.md#rf-018)|calculateLineCandidate menggandakan struktur hasil|Anggota 1|Opsional|rendah–sedang|
|[RF-019](B-inventaris-refactoring.md#rf-019)|Loyalty preview juga mengakses produk dan menghitung pajak|Anggota 1|Tunda|tinggi|
|[RF-020](B-inventaris-refactoring.md#rf-020)|Normalisasi tender memuat validasi baris dan agregat|Anggota 2|Tunda|tinggi|
|[RF-021](B-inventaris-refactoring.md#rf-021)|Checkout execute mengorkestrasi terlalu banyak detail pembentukan transaksi|Anggota 2|Tunda|tinggi|
|[RF-022](B-inventaris-refactoring.md#rf-022)|Checkout cart processor memiliki delapan parameter dan dua jalur stok|Anggota 2|Tunda|tinggi|
|[RF-023](B-inventaris-refactoring.md#rf-023)|Alokasi batch FEFO persis berulang pada checkout dan dine-in|Anggota 2|Tunda|tinggi|
|[RF-024](B-inventaris-refactoring.md#rf-024)|Tambah/ubah cart web dan API menggandakan pricing serta cek stok|Anggota 2|Tunda|tinggi|
|[RF-025](B-inventaris-refactoring.md#rf-025)|Lifecycle hold/resume berulang pada adapter POS|Anggota 2|Opsional|sedang|
|[RF-026](B-inventaris-refactoring.md#rf-026)|Pembuatan discount approval setelah checkout berulang|Anggota 2|Tunda|tinggi|
|[RF-027](B-inventaris-refactoring.md#rf-027)|Pembuatan pembayaran gateway setelah checkout diduplikasi|Anggota 2|Tunda|tinggi|
|[RF-028](B-inventaris-refactoring.md#rf-028)|Offline sync memanggil controller checkout lewat synthetic Request|Anggota 2|Tunda|tinggi|
|[RF-029](B-inventaris-refactoring.md#rf-029)|Sinkronisasi offline tertanam di komponen POS|Anggota 3|Tunda|tinggi|
|[RF-030](B-inventaris-refactoring.md#rf-030)|Halaman POS menggabungkan shift, cart, payment dan UI responsif|Anggota 3|Opsional|sedang–tinggi|
|[RF-031](B-inventaris-refactoring.md#rf-031)|Payload checkout online/offline dan reset tersebar|Anggota 3|Opsional|sedang–tinggi|
|[RF-032](B-inventaris-refactoring.md#rf-032)|Formatter Rupiah identik tersebar pada halaman transaksi|Anggota 3|Inti|rendah|
|[RF-033](B-inventaris-refactoring.md#rf-033)|Halaman invoice menggabungkan polling, print dan rendering|Anggota 3|Opsional|sedang–tinggi|
|[RF-034](B-inventaris-refactoring.md#rf-034)|Proyeksi struk dan label pembayaran tersebar lintas renderer|Anggota 3|Tunda|tinggi|
|[RF-035](B-inventaris-refactoring.md#rf-035)|Store profile/normalisasi URL logo dirakit ulang|Anggota 1|Opsional|sedang|
|[RF-036](B-inventaris-refactoring.md#rf-036)|Dua view receipt PDF menggandakan perhitungan dan markup|Anggota 3|Opsional|sedang|
|[RF-037](B-inventaris-refactoring.md#rf-037)|View hutang/piutang PDF menggandakan struktur dan CSS|Anggota 3|Opsional|rendah–sedang|
|[RF-038](B-inventaris-refactoring.md#rf-038)|Filter laporan penjualan dan profit identik|Anggota 1|Opsional|rendah–sedang|
|[RF-039](B-inventaris-refactoring.md#rf-039)|Insights controller mencampur metrik sales, inventory, promo, loyalty dan CRM|Anggota 1|Opsional|sedang–tinggi|
|[RF-040](B-inventaris-refactoring.md#rf-040)|Insights mengulang subquery subtotal dan ekspresi margin|Anggota 1|Opsional|sedang|
|[RF-041](B-inventaris-refactoring.md#rf-041)|Insights React mencampur banyak panel laporan|Anggota 3|Opsional|sedang|
|[RF-042](B-inventaris-refactoring.md#rf-042)|Lifecycle Chart.js berulang pada dua chart insights|Anggota 3|Opsional|rendah–sedang|
|[RF-043](B-inventaris-refactoring.md#rf-043)|Dashboard index melakukan seluruh query dan perakitan widget|Anggota 1|Opsional|sedang|
|[RF-044](B-inventaris-refactoring.md#rf-044)|Inertia share menggabungkan query notifikasi dan konfigurasi global|Anggota 1|Opsional|sedang–tinggi|
|[RF-045](B-inventaris-refactoring.md#rf-045)|Update profil toko/printer/WhatsApp diduplikasi web/API|Anggota 1|Opsional|sedang|
|[RF-046](B-inventaris-refactoring.md#rf-046)|Update konfigurasi payment mencampur secret, readiness dan audit|Anggota 1|Tunda|tinggi|
|[RF-047](B-inventaris-refactoring.md#rf-047)|API V1 mewarisi controller web untuk reuse protected helper|Anggota 1|Tunda|tinggi|
|[RF-048](B-inventaris-refactoring.md#rf-048)|Operasi admin user web/API menggandakan update dan guard delete|Anggota 1|Opsional|tinggi|
|[RF-049](B-inventaris-refactoring.md#rf-049)|Penomoran dokumen PO/GR/SR/ST memakai algoritma sama|Anggota 2|Opsional|sedang–tinggi|
|[RF-050](B-inventaris-refactoring.md#rf-050)|GoodsReceiving receive menyatukan penerimaan, stok, batch dan payable|Anggota 2|Tunda|tinggi|
|[RF-051](B-inventaris-refactoring.md#rf-051)|StockMutationService menggandakan payload audit before/after/meta|Anggota 2|Opsional|sedang|
|[RF-052](B-inventaris-refactoring.md#rf-052)|recordMutation menerima sepuluh parameter primitif|Anggota 2|Tunda|sedang–tinggi|
|[RF-053](B-inventaris-refactoring.md#rf-053)|CRM automation menggabungkan audience, campaign, delivery dan template|Anggota 1|Opsional|sedang–tinggi|
|[RF-054](B-inventaris-refactoring.md#rf-054)|Pembuatan reminder due-soon dan overdue berulang|Anggota 1|Opsional|sedang|
|[RF-055](B-inventaris-refactoring.md#rf-055)|WhatsApp PHP wrapper mengulang try/request/fallback|Anggota 1|Opsional|rendah–sedang|
|[RF-056](B-inventaris-refactoring.md#rf-056)|Node gateway menggabungkan state perangkat, route dan bootstrap|Anggota 3|Opsional|sedang–tinggi|
|[RF-057](B-inventaris-refactoring.md#rf-057)|Setting getter/setter mengulang compatibility guard schema|Anggota 1|Tunda|sedang–tinggi|
|[RF-058](B-inventaris-refactoring.md#rf-058)|Fixture setup transaksi/produk berulang lintas test|Anggota 2|Opsional|rendah–sedang|
|[RF-059](B-inventaris-refactoring.md#rf-059)|Test laporan panjang karena arrange berbagai domain sekaligus|Anggota 2|Opsional|rendah|
|[RF-060](B-inventaris-refactoring.md#rf-060)|Dependency dan parameter private sisa ekstraksi checkout|Anggota 2|Opsional|rendah–sedang|
|[RF-061](B-inventaris-refactoring.md#rf-061)|LowStockAlerts lokal kosong dan PaymentPanel tidak terjangkau dari app|Anggota 3|Opsional|rendah–sedang|
|[RF-062](B-inventaris-refactoring.md#rf-062)|Helper permission menggunakan hook tetapi bernama fungsi biasa|Anggota 3|Opsional|sedang|
|[RF-063](B-inventaris-refactoring.md#rf-063)|Dua Modal menggandakan shell HeadlessUI|Anggota 3|Opsional|rendah–sedang|
|[RF-064](B-inventaris-refactoring.md#rf-064)|Editor item PO/transfer/retur supplier mengulang operasi baris|Anggota 3|Opsional|sedang|
|[RF-065](B-inventaris-refactoring.md#rf-065)|Panel konfirmasi pembayaran berulang di history dan invoice|Anggota 3|Opsional|sedang|
|[RF-066](B-inventaris-refactoring.md#rf-066)|Tabel/log CRM campaign dan reminder memiliki blok berulang|Anggota 3|Opsional|rendah–sedang|
|[RF-067](B-inventaris-refactoring.md#rf-067)|Label status purchasing diulang di index/show|Anggota 3|Opsional|rendah|
|[RF-068](B-inventaris-refactoring.md#rf-068)|Seeder coverage/demo memiliki skenario bisnis sangat panjang|Anggota 2|Opsional|sedang|
|[RF-069](B-inventaris-refactoring.md#rf-069)|ProductsImport model mencampur normalisasi row, kategori dan persist|Anggota 1|Opsional|sedang|
|[RF-070](B-inventaris-refactoring.md#rf-070)|Command expiry memuat workflow stok dan batch|Anggota 2|Opsional|tinggi|
|[RF-071](B-inventaris-refactoring.md#rf-071)|Query perbandingan aggregate inventory berulang|Anggota 2|Opsional|rendah–sedang|
|[RF-072](B-inventaris-refactoring.md#rf-072)|Setup store menggabungkan validasi, user, outlet/gudang dan profil|Anggota 1|Opsional|sedang–tinggi|
|[RF-073](B-inventaris-refactoring.md#rf-073)|Wizard React menyatukan validasi langkah dan seluruh panel setup|Anggota 3|Opsional|sedang|
|[RF-074](B-inventaris-refactoring.md#rf-074)|Form pricing rule memuat editor untuk empat kind promo|Anggota 3|Opsional|sedang–tinggi|
|[RF-075](B-inventaris-refactoring.md#rf-075)|Adapter purchasing menggandakan validasi dan query dokumen|Anggota 2|Opsional|sedang|
|[RF-076](B-inventaris-refactoring.md#rf-076)|CRUD item stock opname web/API mengulang pemeriksaan dan audit|Anggota 2|Opsional|sedang|
|[RF-077](B-inventaris-refactoring.md#rf-077)|Voucher daftar/persist web/API mengulang scoping dan audit|Anggota 1|Opsional|sedang|
|[RF-078](B-inventaris-refactoring.md#rf-078)|Serializer detail dan membership segment web/API berulang|Anggota 1|Opsional|sedang|
|[RF-079](B-inventaris-refactoring.md#rf-079)|DineTable web/API dan store/update menggandakan aturan meja|Anggota 2|Opsional|sedang|
|[RF-080](B-inventaris-refactoring.md#rf-080)|Pembuatan outlet menggandakan warehouse dan assignment|Anggota 1|Opsional|sedang–tinggi|
|[RF-081](B-inventaris-refactoring.md#rf-081)|Query audit log web/API menggandakan filter akses|Anggota 1|Opsional|sedang|
|[RF-082](B-inventaris-refactoring.md#rf-082)|Daftar pending approval berulang web/API/middleware|Anggota 2|Opsional|sedang–tinggi|
|[RF-083](B-inventaris-refactoring.md#rf-083)|Form user Create/Edit menggandakan field dan pilihan outlet/role|Anggota 3|Opsional|sedang|
|[RF-084](B-inventaris-refactoring.md#rf-084)|Form kategori Create/Edit menggandakan field dan image preview|Anggota 3|Opsional|rendah|
|[RF-085](B-inventaris-refactoring.md#rf-085)|Filter/tabel laporan Sales dan Profit menggandakan UI|Anggota 3|Opsional|rendah–sedang|
|[RF-086](B-inventaris-refactoring.md#rf-086)|Form pembayaran hutang/piutang UI menggandakan panel|Anggota 3|Opsional|sedang|
|[RF-087](B-inventaris-refactoring.md#rf-087)|CRUD bank account menggandakan rules upload dan audit web/API|Anggota 1|Opsional|sedang–tinggi|
|[RF-088](B-inventaris-refactoring.md#rf-088)|DineOrder accept menulis sale, profit dan stok di satu metode|Anggota 2|Tunda|tinggi|
|[RF-089](B-inventaris-refactoring.md#rf-089)|Submit pesanan dine publik mencampur validasi dan persiapan sale items|Anggota 2|Opsional|sedang–tinggi|
|[RF-090](B-inventaris-refactoring.md#rf-090)|Webhook provider menggandakan settlement tender/transaction|Anggota 2|Tunda|tinggi|
|[RF-091](B-inventaris-refactoring.md#rf-091)|Data migration bergantung pada model aplikasi yang terus berubah|Anggota 1|Tunda|tinggi|

Temuan di luar refactoring berada di [dokumen terpisah](temuan-di-luar-refactoring.md): ENV-001 lockfile; BF-001–BF-013 (setCarts, walk-in UI, scope harga, import/API stock, saldo receiving, camera/USB policy, share token, temporary carts, offline scope, inclusive tax, split drawer dan merge unit cart); DOC-001 teks lisensi; QA-001 gap test; OPT-001 query optimization; FIT-001 fitur/arsitektur. Banyak BF masih indikasi statik dengan test reproduksi belum dijalankan. Tidak mencampurnya ke RF atau mengklaimnya sudah diperbaiki.

## C. Prioritas dan paket pengerjaan yang direkomendasikan

### Paket inti yang cukup untuk tugas tiga anggota

| Pemilik | ID inti | Teknik yang didemonstrasikan | Alasan dipilih | Estimasi bagian implementasi/karakterisasi |
|---|---|---|---|---:|
|Anggota 1|RF-001,RF-003,RF-011|Extract Method/Mapper; Move Method; pure aging helper|Duplikasi nyata, scope terbatas; mencakup backend data mapping dan logika tanggal dengan batas yang bisa diuji|14–20jam|
|Anggota 2|RF-007,RF-008,RF-015|Extract Action khusus piutang/hutang; shared validation rules|Duplikasi operasi finansial nyata pada web/API; tersedia PaymentIntegrityTest dan V1FinanceApiTest; dua action spesifik lebih jelas daripada abstraksi DebtService universal|13–18jam|
|Anggota 3|RF-002,RF-032|Extract Component; hook/array helper hanya jika perlu; Extract Function formatter|Create/Edit produk memiliki clone besar, dengan perbedaan mode jelas; formatter identik memberi unit test murni dan exact-output proof|13–19jam|

Tidak ada kewajiban menyelesaikan91 perubahan. Pilihan8 RF menunjukkan keempat aspek soal (smell, kompleksitas lokal, duplikasi, pembagian tanggung jawab) dengan test nyata dan ruang review yang wajar. Tambahkan **6jam/orang** untuk analisis baseline bersama, review silang, integrasi, dokumentasi dan latihan demo; total**58–75jam-orang**, sekitar19–26jam per anggota. Estimasi bukan pengukuran produktivitas dan backlog tidak dijumlahkan ke paket inti.

Urutan opsi tambahan paling masuk akal setelah inti selesai: RF-018(candidate builder pricing kecil),RF-038(filter reports),RF-058(fixture builder terbatas),RF-063(modal shell),RF-067(status label). Pilih maksimum satu per anggota bila semua karakterisasi inti dan dokumentasi selesai, bukan mengejar jumlah ID.

23 RF bertanda Tunda ditahan karena menyentuh atomicity/stock/finance/provider/offline/state contract/historical migrations: RF-013,014,016,017,019,020,021,022,023,024,026,027,028,029,034,046,047,050,052,057,088,090,091. Memindahkan kode mungkin sederhana secara sintaks, tetapi membuktikan lock/order/error boundary dan output lama jauh lebih mahal.60 opsional lain memiliki pemilik di tabel/CSV, bukan tugas yang harus selesai semester ini.

Batas paket inti: tidak menambah fitur, tidak mengubah schema/izin/route, tidak mengganti formula total/PPN/stok, tidak memperbaiki BF secara tersembunyi, tidak memperbarui dependency massal. Bila bug menghalangi skenario inti, buat characterization dan ticket BF terpisah serta bukti keputusan scope; jangan memakai expected baru untuk mengklaim RF setara.

## D. Pembagian kerja tepat tiga orang

| Anggota | Modul/file utama | ID temuan | Rencana perubahan | Testing | Dokumentasi | Estimasi usaha | Dependensi |
|---|---|---|---|---|---|---|---|
|**Anggota 1**|Apps/ProductController; Apps/CustomerController,MemberController; Models/Receivable,Payable; helper baru bertanda USULAN|**RF-001,003,011**. Seluruh backlog miliknya tercantum di B/CSV|Rekam kondisi awal; unify update product; ekstrak region resolver; ekstrak aging pure helper; maintain existing adapters|T02,T04,T06; unit batas aging/nullable region; feature product/member; ulang baseline dan validasi UI bersama Anggota 3|Tulis kondisi awal/strategi/aktivitas/hasil test miliknya; before/after+commit;3slide/demo; sekaligus wakil penggabung PDF, tetap mengerjakan source dan tests sendiri|14–20jam+6jam bersama=**20–26jam**|Sepakati field produk dengan3; merge helper aging sebelum final validation finance2; reviewer untuk2|
|**Anggota 2**|Apps+Api/V1/ReceivableController,PayableController; Store/UpdateSalesReturnRequest; action baru USULAN|**RF-007,008,015**. Backlog setiap ID tetap satu pemilik di B/CSV|Rekam transaction boundary/response; pindahkan payment recorder khusus tiap domain; satukan rules retur tanpa mengubah class request routes|T01,T05,T08; saldo partial/exact/invalid, bank/outlet, rollback; API ability; test karakterisasi completion API baru hanya bila RF-014 kelak dipilih|Before/after locking+persistence; catat schema snapshot dan actual HTTP; bagian laporan/slide finansial; rekap matriks hasil, bukan hanya tester kelompok|13–18jam+6jam=**19–24jam**|Contract aging1; tidak membuat generic payment layer; reviewer untuk3|
|**Anggota 3**|Products/Create,Edit.jsx; Transactions/Index,Print.jsx formatter saja; komponen/utility baru USULAN|**RF-002,032**. Backlog frontend/Node miliknya di B/CSV|Rekam form mode dan payload; shared product fields/rows, parent submit tetap; extract formatter identik|T02,T03,T16; Node unit utility baru bila dibuat, manual baseline/replay create/edit units/composite/image; build; checkout display sama|Before/after JSX/handler+payload+screenshots; test hasil miliknya; materi demo UI; bantu QA visual PDF tanpa menjadi orang laporan saja|13–19jam+6jam=**19–25jam**|Field produk1; POS page hanya ia edit; reviewer untuk1|

Dasar keseimbangan adalah rentang usaha, risiko dan jumlah jalur yang harus dibuktikan. Anggota 2 hanya beberapa file tetapi menyentuh saldo/locking dan dua transport; Anggota 3 hanya dua RF tetapi form besar dan verifikasi UI/payload mahal; Anggota 1 tiga ekstraksi lintas model/controller dengan batas data/tanggal. Tidak membagi menjadi backend/frontend/testing yang membuat satu orang tidak melakukan refactoring. Semua anggota wajib memiliki analisis, implementasi, testing, dokumentasi, laporan dan presentasi miliknya.

**Semua91 ID memiliki satu pemilik utama** di `pembagian-semua-temuan.csv` dan B, termasuk yang ditunda. Jumlah ID backlog(30/34/27) bukan komitmen volume kerja dan bukan dasar menilai beban paket inti. Usaha backlog tumpang tindih dan harus diestimasi ulang bila dipilih.

File bersama: ProductController→1; form/utility/Index/Print→3; models Receivable/Payable→1; payment controllers→2. `tests/TestCase.php` bila helper bersama diperlukan→2; config/routes/composer.lock tidak diubah inti, integrator1 mengoordinasi bila benar-benar perlu. Root/WA package-lock milik koordinasi3 untuk ENV-001 di commit maintenance terpisah. Middleware HandleInertiaRequests→1; RF-082 primary2 hanya mengusulkan query/helper dan perubahan middleware diintegrasikan1. Satu primary RF tetap ada, integrator bukan pemilik kedua.

## E. Urutan pengerjaan, dependensi, dan strategi integrasi

**Seluruh langkah implementasi di bagian ini masih rencana, belum dilakukan.**

1. **Bekukan baseline.** Simpan commit hash/source archive, versi runtime, baseline log dan fixture. Kelompok sepakati8 ID inti dan invariant/error outputs. Buat characterization test pada source lama sebelum memindah kode. ENV-001 ditangani di branch maintenance terpisah bila kelompok memerlukan clean npm ci; jangan mengubah lockfile saat dokumentasi mengklaim source baseline sama.
2. **Sepakati kontrak kecil:** field form Create/Edit dengan1+3; nullable region/full-vs-AJAX dengan1; aging bucket vs payment status dengan1+2. Tidak perlu design ulang semua project.
3. **Kerja mandiri pada branch:** dari commit pinned, buat `codex/refactor-integration`; `codex/anggota-1-produk-region-aging`, `codex/anggota-2-pembayaran-rules`, `codex/anggota-3-form-produk-format`. Main audit tidak diubah. Repo menyarankan kontribusi upstream dari/ke development; bila mengirim PR upstream, bandingkan development terhadap pinned commit, rebase/ulang baseline sesuai perbedaan, target development, bukan langsung main.
4. **Commit bertahap per ID:** `test(RF-xxx): characterize ...` → `refactor(RF-xxx): extract ...` → catatan test/before-after. Test yang hanya bisa muncul sesudah helper boleh ditambah pada commit refactor dengan assertion publik baseline tetap. Bug fix memakai commit `fix(BF-xxx)` terpisah bila kelak disetujui scope-nya; feature bukan RF. Jangan commit vendor/node_modules/build/.env/DB/log credential.
5. **Urutan merge:** RF-011→RF-001→RF-003 (Anggota 1); RF-015 bisa paralel; RF-007→RF-008 setelah kontrak aging disepakati; RF-002 danRF-032 bisa paralel di branch3 pada file terpisah, diintegrasikan setelah kontrak payload1. RF-007/008 tidak memerlukan penggunaan helper bucket untuk menentukan status, hanya validasi ulang bersama karena model consumer berubah.
6. **Review silang wajib:** Anggota 1 mereview2; Anggota 2 mereview3; Anggota 3 mereview1. Reviewer memeriksa invariant/hasil, tidak hanya style. Review cycle ini tidak mengubah pemilik implementasi/test. Tiap PR melampirkan ID, scope, before/after, test aktual+log dan batasan.
7. **Integrasi:** integrator1 merge PR kecil setelah reviewer lulus; jalankan focused suites setiap merge, full suite dan UI bersama saat paket selesai. Rebase feature branch dari integration sebelum merge berikut. Konflik controller/models resolved oleh pemilik file, reviewer memeriksa ulang; jangan memakai `ours/theirs` membabi buta. Conflict resolution yang mengubah behavior harus punya test ulang.
8. **Rilis tugas:** baseline tag/commit dan after tag/commit (nama tag hanya rencana); `git diff BASE..AFTER`; clean setup dari archive/clone baru, test/build, replay data identik, final report PDF dan demo. Tiada bukti after pada audit ini.

Dependensi besar backlog: RF-021/022/023→inventory action sebelum RF-088 bila dipilih; RF-024/027→RF-028 sync controller; RF-029/030/031/033 tidak dikerjakan paralel pada page yang sama; RF-038/040 sebelum pemecahan reportsRF-039; domain extraction per modul sebelum inheritanceRF-047. Tidak ada alasan menjadikan backlog ini prasyarat8 inti.

## F. Matriks testing serta kriteria keberhasilan

Dokumen [testing.md](testing.md) berisi hasil baseline aktual,22 keluarga skenario dengan prasyarat/input/langkah/normal-invalid-batas-failure/expected/lapisan, test existing, perintah dan batas validasinya. [matriks-RF-test.csv](matriks-RF-test.csv) memetakan **setiapRF-001–091**, termasuk backlog, ke unit/feature/UI dan pemilik.

| Paket inti | Test utama | Bukti kesetaraan yang wajib |
|---|---|---|
|RF-001+002|T02/T03; ProductStoreTest,ProductUnitTest,CompositeProductTest,ProductTaxTest; characterization baru update gambar/audit|Input Create/Edit sama; field/pivot/file/audit/redirect/payload serta UI tetap; stock Edit tidak berubah|
|RF-003|T04; MemberManagementTest/CrmCoreTest + unit resolver baru|Kode/nama4 wilayah dan null AJAX tetap; full form required tidak dilonggarkan; region change UI sama|
|RF-011|T06; unit aging table-driven baru dan finance API checks|paid/no_due_date/current/30/60/90 boundaries dan fractional time semantics tetap; appends/types tetap|
|RF-007+008|T01/T05; PaymentIntegrityTest/V1FinanceApiTest + rollback/foreign bank characterization baru|Payment row, paid/status/remaining, transaction status piutang, lock+rollback, permissions, redirects/JSON/error keys tetap|
|RF-015|T08; SalesReturnTest + kedua request invalid-input characterization|Nama rules/error keys, nullable qty dan return type identik; tidak perlu mengubah completion|
|RF-032|T16; Node unit formatter baru+UI total/cash/change; build|Exact Intl string/coercion/rounding, termasuk NBSP; tidak mengubah nilai bisnis|

Baseline sebelum refactor:580 lulus/1 skip; semua test tambahan dan sesudah refactoring **belum dijalankan**. Pakai PHPUnit/SQLite sebagaimana konfigurasi; mock HTTP/WA dan WebUSB, jangan memakai provider nyata untuk regression core. Test row locking/concurrent payments perlu MySQL terisolasi dua koneksi; suite SQLite tidak cukup membuktikannya.

Kriteria selesai kelompok:8 RF punya bukti before/after dan commit; semua characterization terkait serta suite baseline tetap lulus; skip tidak disembunyikan; build berhasil; alur UI sama pada fixture sama; API/validation/permissions/database/audit unchanged; setup clean dapat dijalankan anggota lain; tidak ada klaim coverage atau keberhasilan setelah refactoring sebelum hasilnya benar-benar ada.

## G. Kerangka laporan dan template dokumentasi

Laporan akhir mempertahankan lima bagian soal:

1. **Kondisi sebelum refactoring:** branch/hash, setup aktual, scope+exclusions, arsitektur, daftar RF terpilih dan backlog, bukti smell/kompleksitas/duplikasi/struktur, baseline tests. Kaitkan setiap masalah ke source lines+ID. Lampiran audit91 RF tetap tersedia.
2. **Strategi refactoring:** teknik setiap RF terpilih dan alasan lebih cocok dari abstraksi lebih luas; invariant/risiko/dependensi dan scope yang sengaja ditunda. Nama class/component baru harus disebut rancangan sampai benar-benar dibuat.
3. **Aktivitas refactoring:** satu catatan per ID/commit, actual before/after (bukan hanya pseudo-code), penjelasan tanggung jawab pindah dan siapa reviewer. **Saat ini kosong/belum diimplementasikan.**
4. **Testing dan validasi:** input/fixture dan baseline aktual, karakterisasi baru, hasil after aktual, bukti UI/DB/API/log dan keterbatasan. Jangan menyalin580 baseline sebagai hasil after. Kolom actual after tetap belum dijalankan sampai ada run.
5. **Kesimpulan dan rekomendasi:** hanya RF yang benar-benar selesai dan diverifikasi, keterbatasan, bug terpisah, backlog, rekomendasi berikut. **Kesimpulan keberhasilan belum diisi pada tahap audit.**

Template per aktivitas (tersedia pula pada template-aktivitas.md):

| Field | Isi yang dicatat |
|---|---|
|ID→pemilik/reviewer|RF-xxx → AnggotaN / AnggotaM|
|Kondisi awal|Commit, file:symbol:lines, cuplikan BEFORE dan bukti masalah/metrik|
|Teknik dan alasan|Teknik spesifik, manfaat ekstraksi, pilihan scope|
|Perubahan sebelum/sesudah|Cuplikan actual AFTER dan lokasi baru; sebelum ada implementasi tulis BELUM DIIMPLEMENTASIKAN|
|Perilaku yang dipertahankan|Input/output, validasi/izin, urutan dan DB/audit effects|
|Pengujian|TestID/file/command, data/prasyarat/langkah dan expected|
|Hasil|Baseline aktual; after BELUM DIJALANKAN sampai ada bukti; failure/skip/retest|
|Penanggung jawab|Penulis implementasi/test/dokumentasi dan reviewer silang|
|Commit/bukti|Baseline/after hash, diff, log, screenshot, DB snapshot; jangan isi hash rencana sebagai hash aktual|

Deliverable akhir yang **direncanakan**, belum diunggah/dibuat sebagai hasil refactor:

- **SatuPDF laporan gabungan:** setiap anggota menulis bagiannya sendiri; Anggota 1 wakil penggabung dan pengunggah; Anggota 2 validasi matriks hasil/commit; Anggota 3 memeriksa code blocks/gambar/layout. Render dan QA PDF sebelum unggah; upload belum dilakukan di audit ini.
- **Sistem hasil refactoring runnable:** source after pada commit/tag tetap, README setup PHP/ext/Composer/npm/DB/environment, migrations+seed esensial, optional demo dan WA, test/build commands; .env.example tanpa secret. Clean setup diverifikasi oleh anggota yang bukan penulis utama.
- **Baseline vs after:** baseline archive sudah disediakan, SHA256 di runtime.md. Versi after belum ada; nanti sertakan commit/tag after serta `git diff` dan dua dataset fixture yang sama untuk comparison. Dependencies/generated outputs tidak menjadi bukti refactoring source.
- **Bukti validasi:** JUnit/log baseline sudah ada; karakterisasi/after/UI replay masih harus dibuat. Demo recording bukan pengganti tests.

## H. Rencana demo15 menit

**Demo ini rencana, belum dilaksanakan.** Siapkan dua checkout/runtime terpisah baseline dan after dengan DB fixture identik; hindari live edit source. Waktu/timezone/user/outlet/stock/prices sama. Checkout after belum tersedia saat audit ini.

| Menit | Presenter | Isi dan bukti |
|---|---|---|
|00:00–01:00|Anggota 1|Tujuan, scope8 RF, baseline hash/after hash yang kelak nyata; tunjuk dua source dan fixture identik|
|01:00–04:00|Anggota 1|Before/after product update duplikat→satu mapping, region resolver, aging helper; tunjuk test batas30/60/90 dan create/edit/customer yang sama|
|04:00–08:00|Anggota 2|Before/after web/API payment copy→dua action domain spesifik; bayar partial lalu exact pada dua DB reset sama, overpay dan wrong-outlet ditolak; tunjuk rules retur identik dan DB/payment/audit|
|08:00–12:00|Anggota 3|Before/after Product Create/Edit→shared fields/rows dan formatter; demo tambah/hapus unit serta edit gambar/data; jalankan alur POS yang sama (produk/promo/PPN/cash/change/invoice), desktop/mobile bila cukup|
|12:00–14:00|Ketiganya, dipandu2|Focused/full test aktual after di samping baseline, satu bukti per anggota; build disebut sebagai build saja; tunjuk diff scope dan hasil reviewer|
|14:00–15:00|Masing-masing20detik|Hasil miliknya yang terverifikasi, satu keterbatasan/backlog;1 menutup dengan cara menjalankan paket dan lokasi laporan|

Siapkan screenshot/log bila layanan lokal bermasalah, tetapi jelaskan bila rekaman menjadi fallback. Tidak memanggil WA/payment provider nyata dalam demo inti. Jangan memilih cabang offline yang diketahui BF-001 sebagai bukti RF sukses; bug itu dipresentasikan sebagai keterbatasan terpisah bila relevan.

## I. Keterbatasan audit dan pekerjaan belum diverifikasi

- **Cakupan statik ≠ pembacaan semantik manual setiap baris.** Semua911 file terinventaris,682 file kode dipetakan;121 file memiliki bukti RF yang dibaca dalam konteks. coverage.csv menandai sisanya dengan batas pendalaman yang nyata. Inventory91 kandidat bukan klaim exhaustiveness matematis. Kelompok perlu melanjutkan review fungsi yang berstatus struktural saja bila dosen mensyaratkan sign-off manual seluruh source.
- Tidak ada source setelah refactor, test karakterisasi tambahan, test after, PDF laporan akhir atau upload. Semua rancangan perubahan ditandai USULAN. Satu-satunya hasil kelulusan adalah baseline unchanged.
- Runtime audit SQLite, sedangkan default environment repo MySQL. LockForUpdate, deadlock, unique collision dan concurrent worker belum dibuktikan dengan dua koneksi MySQL; nama test concurrency tidak mengubah batas ini.
- imagick tidak tersedia; PNG QR meja dilewati. Browser scanner kamera, printer USB/laci dan receipt kertas asli belum diuji. PDF authorization suite lulus, tetapi seluruh output PDF visual58/80/invoice belum dibandingkan dengan golden pages.
- UI POS satu alur tunai berhasil; produk edit, finance list, insights dan dine tables diamati. Seluruh CRUD/peran/mobile/offline/perubahan outlet dan semua error UI belum diuji end-to-end. Penambahan karakterisasi pada matriks masih rencana.
- Tidak ada sandbox credentials Midtrans/Xendit, WhatsApp device, SMTP/service eksternal. Suite menggunakan fake/mocks pada kasus yang disediakan. WA hanya smoke listen/503 tanpa token; pengiriman/QR lifecycle tidak diuji nyata. Tidak ada pesan terkirim.
- Root npm ci gagal karena lock drift; build memakai instalasi tanpa menulis lock. Karena itu setup clean reproducible perlu ENV-001 tersendiri sebelum menyerahkan after. Runtime PHP8.5 ditolak salah satu locked dependency; gunakan8.3/8.4 yang sesuai dan catat ekstensi.
- Tidak ada pengukuran coverage, query latency atau perubahan complexity sesudah. CC tidak dihitung oleh decision-node proxy. Estimasi jam dan risiko merupakan penilaian reviewer; tidak ada persentase perbaikan karangan.
- Banyak temuan BF adalah indikasi kode, belum test reproduksi. Tidak ada klaim exploit, kehilangan uang produksi atau user data yang terdampak. Temuan license mismatch hanya observasi string proyek.
- Semua tracked files tetap sesuai commit; .env/DB/dependency/build lokal tidak termasuk baseline archive. Server audit dihentikan setelah bukti diambil; petunjuk menjalankan ulang di runtime.md.

**Hasil tahap sekarang:** audit berbukti, source baseline runnable dan rencana seimbang tepat3 anggota. Aktivitas refactoring serta kesimpulan keberhasilan tugas baru boleh ditulis setelah8 RF dipilih, diimplementasikan dan diverifikasi.
