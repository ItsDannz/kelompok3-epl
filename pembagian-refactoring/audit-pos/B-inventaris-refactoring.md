# B. Inventaris kandidat refactoring — bukti dan strategi

Commit acuan: `81a91b8fc0ef479be9e0c751c0d11370d89dd8a2` (`main`). Semua potongan **SEBELUM**, diambil langsung dari checkout; rentang metode PHP berasal dari AST, nomor baris cuplikan dihitung dari source. Semua perubahan di bawah adalah **USULAN**, belum diimplementasikan.

91 kandidat terkonfirmasi dikelompokkan sebagai unit pekerjaan; kandidat yang saling tumpang tindih bukan estimasi untuk dijumlahkan. Angka usaha adalah estimasi jam-orang (analisis lokal, implementasi, focused tests dan catatan per kandidat), bukan hasil pengukuran. Kode proyek memiliki 204 clone hasil alat; clone bukan otomatis smell. Lampiran clone mentah dipertahankan agar bagian yang tidak dipilih tidak disembunyikan.

**Aturan selesai semua RF:** karakterisasi baseline direkam lebih dahulu; teknik yang disebut diterapkan pada scope terbatas; focused tests serta seluruh baseline suite dijalankan; kontrak UI/API/validasi/akses/efek DB dibandingkan; tidak ada bug fix tersembunyi; cuplikan sebelum/sesudah dan commit dicatat. Kriteria khusus dan invariannya tercantum per RF. Jika bukti kesetaraan belum tersedia, RF belum selesai.

**Status test per RF:** test suite repositori sebelum perubahan telah dijalankan secara keseluruhan; semua test tambahan, mock, snapshot, browser regression dan test sesudah refactoring di bawah **belum dijalankan**. T01–T22 merujuk matriks skenario dalam `testing.md`.

| ID | Kandidat | Pemilik utama | Scope | Prioritas | Risiko | Jam-orang estimasi |
|---|---|---|---|---|---|---|
| [RF-001](#rf-001) | Payload pembaruan produk diduplikasi pada cabang gambar | Anggota 1 | Inti | P1 | sedang | 4–6 |
| [RF-002](#rf-002) | Form produk Create/Edit menggandakan field dan editor baris | Anggota 3 | Inti | P1 | sedang | 10–14 |
| [RF-003](#rf-003) | Resolusi wilayah customer/member berulang | Anggota 1 | Inti | P1 | rendah–sedang | 6–8 |
| [RF-004](#rf-004) | Query statistik dan riwayat customer/member berulang | Anggota 1 | Opsional | P2 | sedang | 5–8 |
| [RF-005](#rf-005) | Rangkaian dropdown wilayah pada form pelanggan/member berulang | Anggota 3 | Opsional | P2 | sedang | 6–9 |
| [RF-006](#rf-006) | Detail customer dan member menggandakan panel riwayat | Anggota 3 | Opsional | P2 | rendah–sedang | 5–8 |
| [RF-007](#rf-007) | Pencatatan pembayaran piutang diduplikasi web/API | Anggota 2 | Inti | P1 | sedang–tinggi | 6–8 |
| [RF-008](#rf-008) | Pencatatan pembayaran hutang diduplikasi web/API | Anggota 2 | Inti | P1 | sedang–tinggi | 5–7 |
| [RF-009](#rf-009) | Filter dan dekorasi daftar piutang/hutang web/API berulang | Anggota 2 | Opsional | P2 | sedang | 4–7 |
| [RF-010](#rf-010) | Scoping hutang berulang di controller dan aging service | Anggota 2 | Opsional | P2 | tinggi | 4–6 |
| [RF-011](#rf-011) | Perhitungan aging pada model piutang/hutang identik | Anggota 1 | Inti | P1 | sedang | 4–6 |
| [RF-012](#rf-012) | Agregasi aging service berulang | Anggota 2 | Opsional | P2 | rendah–sedang | 3–5 |
| [RF-013](#rf-013) | Finalisasi stock opname menggandakan transaksi inventory | Anggota 2 | Tunda | P3 | tinggi | 10–16 |
| [RF-014](#rf-014) | Completion retur penjualan dan API menggandakan settlement | Anggota 2 | Tunda | P3 | tinggi | 14–22 |
| [RF-015](#rf-015) | Aturan request create/update retur penjualan identik | Anggota 2 | Inti | P1 | rendah | 2–3 |
| [RF-016](#rf-016) | Validasi pricing rule sekaligus menormalisasi banyak bentuk promo | Anggota 1 | Tunda | P3 | tinggi | 10–16 |
| [RF-017](#rf-017) | Pricing buildPreview mencampur beberapa tahap algoritma | Anggota 1 | Tunda | P3 | tinggi | 10–16 |
| [RF-018](#rf-018) | calculateLineCandidate menggandakan struktur hasil | Anggota 1 | Opsional | P2 | rendah–sedang | 2–4 |
| [RF-019](#rf-019) | Loyalty preview juga mengakses produk dan menghitung pajak | Anggota 1 | Tunda | P3 | tinggi | 8–14 |
| [RF-020](#rf-020) | Normalisasi tender memuat validasi baris dan agregat | Anggota 2 | Tunda | P3 | tinggi | 6–10 |
| [RF-021](#rf-021) | Checkout execute mengorkestrasi terlalu banyak detail pembentukan transaksi | Anggota 2 | Tunda | P3 | tinggi | 8–14 |
| [RF-022](#rf-022) | Checkout cart processor memiliki delapan parameter dan dua jalur stok | Anggota 2 | Tunda | P3 | tinggi | 8–14 |
| [RF-023](#rf-023) | Alokasi batch FEFO persis berulang pada checkout dan dine-in | Anggota 2 | Tunda | P3 | tinggi | 10–16 |
| [RF-024](#rf-024) | Tambah/ubah cart web dan API menggandakan pricing serta cek stok | Anggota 2 | Tunda | P3 | tinggi | 10–16 |
| [RF-025](#rf-025) | Lifecycle hold/resume berulang pada adapter POS | Anggota 2 | Opsional | P2 | sedang | 5–8 |
| [RF-026](#rf-026) | Pembuatan discount approval setelah checkout berulang | Anggota 2 | Tunda | P3 | tinggi | 5–8 |
| [RF-027](#rf-027) | Pembuatan pembayaran gateway setelah checkout diduplikasi | Anggota 2 | Tunda | P3 | tinggi | 8–14 |
| [RF-028](#rf-028) | Offline sync memanggil controller checkout lewat synthetic Request | Anggota 2 | Tunda | P3 | tinggi | 12–20 |
| [RF-029](#rf-029) | Sinkronisasi offline tertanam di komponen POS | Anggota 3 | Tunda | P3 | tinggi | 8–14 |
| [RF-030](#rf-030) | Halaman POS menggabungkan shift, cart, payment dan UI responsif | Anggota 3 | Opsional | P2 | sedang–tinggi | 10–16 |
| [RF-031](#rf-031) | Payload checkout online/offline dan reset tersebar | Anggota 3 | Opsional | P2 | sedang–tinggi | 5–8 |
| [RF-032](#rf-032) | Formatter Rupiah identik tersebar pada halaman transaksi | Anggota 3 | Inti | P1 | rendah | 3–5 |
| [RF-033](#rf-033) | Halaman invoice menggabungkan polling, print dan rendering | Anggota 3 | Opsional | P2 | sedang–tinggi | 8–12 |
| [RF-034](#rf-034) | Proyeksi struk dan label pembayaran tersebar lintas renderer | Anggota 3 | Tunda | P3 | tinggi | 10–18 |
| [RF-035](#rf-035) | Store profile/normalisasi URL logo dirakit ulang | Anggota 1 | Opsional | P2 | sedang | 4–7 |
| [RF-036](#rf-036) | Dua view receipt PDF menggandakan perhitungan dan markup | Anggota 3 | Opsional | P2 | sedang | 5–8 |
| [RF-037](#rf-037) | View hutang/piutang PDF menggandakan struktur dan CSS | Anggota 3 | Opsional | P2 | rendah–sedang | 3–5 |
| [RF-038](#rf-038) | Filter laporan penjualan dan profit identik | Anggota 1 | Opsional | P2 | rendah–sedang | 4–6 |
| [RF-039](#rf-039) | Insights controller mencampur metrik sales, inventory, promo, loyalty dan CRM | Anggota 1 | Opsional | P2 | sedang–tinggi | 10–18 |
| [RF-040](#rf-040) | Insights mengulang subquery subtotal dan ekspresi margin | Anggota 1 | Opsional | P2 | sedang | 5–8 |
| [RF-041](#rf-041) | Insights React mencampur banyak panel laporan | Anggota 3 | Opsional | P2 | sedang | 8–12 |
| [RF-042](#rf-042) | Lifecycle Chart.js berulang pada dua chart insights | Anggota 3 | Opsional | P2 | rendah–sedang | 3–5 |
| [RF-043](#rf-043) | Dashboard index melakukan seluruh query dan perakitan widget | Anggota 1 | Opsional | P2 | sedang | 6–10 |
| [RF-044](#rf-044) | Inertia share menggabungkan query notifikasi dan konfigurasi global | Anggota 1 | Opsional | P2 | sedang–tinggi | 8–14 |
| [RF-045](#rf-045) | Update profil toko/printer/WhatsApp diduplikasi web/API | Anggota 1 | Opsional | P2 | sedang | 6–10 |
| [RF-046](#rf-046) | Update konfigurasi payment mencampur secret, readiness dan audit | Anggota 1 | Tunda | P3 | tinggi | 8–14 |
| [RF-047](#rf-047) | API V1 mewarisi controller web untuk reuse protected helper | Anggota 1 | Tunda | P3 | tinggi | 16–28 |
| [RF-048](#rf-048) | Operasi admin user web/API menggandakan update dan guard delete | Anggota 1 | Opsional | P2 | tinggi | 6–10 |
| [RF-049](#rf-049) | Penomoran dokumen PO/GR/SR/ST memakai algoritma sama | Anggota 2 | Opsional | P2 | sedang–tinggi | 4–7 |
| [RF-050](#rf-050) | GoodsReceiving receive menyatukan penerimaan, stok, batch dan payable | Anggota 2 | Tunda | P3 | tinggi | 8–14 |
| [RF-051](#rf-051) | StockMutationService menggandakan payload audit before/after/meta | Anggota 2 | Opsional | P2 | sedang | 5–8 |
| [RF-052](#rf-052) | recordMutation menerima sepuluh parameter primitif | Anggota 2 | Tunda | P3 | sedang–tinggi | 6–10 |
| [RF-053](#rf-053) | CRM automation menggabungkan audience, campaign, delivery dan template | Anggota 1 | Opsional | P2 | sedang–tinggi | 8–14 |
| [RF-054](#rf-054) | Pembuatan reminder due-soon dan overdue berulang | Anggota 1 | Opsional | P2 | sedang | 4–6 |
| [RF-055](#rf-055) | WhatsApp PHP wrapper mengulang try/request/fallback | Anggota 1 | Opsional | P2 | rendah–sedang | 3–5 |
| [RF-056](#rf-056) | Node gateway menggabungkan state perangkat, route dan bootstrap | Anggota 3 | Opsional | P2 | sedang–tinggi | 6–10 |
| [RF-057](#rf-057) | Setting getter/setter mengulang compatibility guard schema | Anggota 1 | Tunda | P3 | sedang–tinggi | 4–7 |
| [RF-058](#rf-058) | Fixture setup transaksi/produk berulang lintas test | Anggota 2 | Opsional | P2 | rendah–sedang | 4–7 |
| [RF-059](#rf-059) | Test laporan panjang karena arrange berbagai domain sekaligus | Anggota 2 | Opsional | P2 | rendah | 3–5 |
| [RF-060](#rf-060) | Dependency dan parameter private sisa ekstraksi checkout | Anggota 2 | Opsional | P2 | rendah–sedang | 2–4 |
| [RF-061](#rf-061) | LowStockAlerts lokal kosong dan PaymentPanel tidak terjangkau dari app | Anggota 3 | Opsional | P2 | rendah–sedang | 2–4 |
| [RF-062](#rf-062) | Helper permission menggunakan hook tetapi bernama fungsi biasa | Anggota 3 | Opsional | P2 | sedang | 3–5 |
| [RF-063](#rf-063) | Dua Modal menggandakan shell HeadlessUI | Anggota 3 | Opsional | P2 | rendah–sedang | 3–5 |
| [RF-064](#rf-064) | Editor item PO/transfer/retur supplier mengulang operasi baris | Anggota 3 | Opsional | P2 | sedang | 6–9 |
| [RF-065](#rf-065) | Panel konfirmasi pembayaran berulang di history dan invoice | Anggota 3 | Opsional | P2 | sedang | 4–6 |
| [RF-066](#rf-066) | Tabel/log CRM campaign dan reminder memiliki blok berulang | Anggota 3 | Opsional | P2 | rendah–sedang | 4–6 |
| [RF-067](#rf-067) | Label status purchasing diulang di index/show | Anggota 3 | Opsional | P2 | rendah | 2–4 |
| [RF-068](#rf-068) | Seeder coverage/demo memiliki skenario bisnis sangat panjang | Anggota 2 | Opsional | P2 | sedang | 6–10 |
| [RF-069](#rf-069) | ProductsImport model mencampur normalisasi row, kategori dan persist | Anggota 1 | Opsional | P2 | sedang | 4–7 |
| [RF-070](#rf-070) | Command expiry memuat workflow stok dan batch | Anggota 2 | Opsional | P2 | tinggi | 8–12 |
| [RF-071](#rf-071) | Query perbandingan aggregate inventory berulang | Anggota 2 | Opsional | P2 | rendah–sedang | 3–5 |
| [RF-072](#rf-072) | Setup store menggabungkan validasi, user, outlet/gudang dan profil | Anggota 1 | Opsional | P2 | sedang–tinggi | 8–12 |
| [RF-073](#rf-073) | Wizard React menyatukan validasi langkah dan seluruh panel setup | Anggota 3 | Opsional | P2 | sedang | 8–12 |
| [RF-074](#rf-074) | Form pricing rule memuat editor untuk empat kind promo | Anggota 3 | Opsional | P2 | sedang–tinggi | 8–12 |
| [RF-075](#rf-075) | Adapter purchasing menggandakan validasi dan query dokumen | Anggota 2 | Opsional | P2 | sedang | 6–10 |
| [RF-076](#rf-076) | CRUD item stock opname web/API mengulang pemeriksaan dan audit | Anggota 2 | Opsional | P2 | sedang | 5–8 |
| [RF-077](#rf-077) | Voucher daftar/persist web/API mengulang scoping dan audit | Anggota 1 | Opsional | P2 | sedang | 5–8 |
| [RF-078](#rf-078) | Serializer detail dan membership segment web/API berulang | Anggota 1 | Opsional | P2 | sedang | 4–7 |
| [RF-079](#rf-079) | DineTable web/API dan store/update menggandakan aturan meja | Anggota 2 | Opsional | P2 | sedang | 5–8 |
| [RF-080](#rf-080) | Pembuatan outlet menggandakan warehouse dan assignment | Anggota 1 | Opsional | P2 | sedang–tinggi | 6–10 |
| [RF-081](#rf-081) | Query audit log web/API menggandakan filter akses | Anggota 1 | Opsional | P2 | sedang | 3–5 |
| [RF-082](#rf-082) | Daftar pending approval berulang web/API/middleware | Anggota 2 | Opsional | P2 | sedang–tinggi | 4–7 |
| [RF-083](#rf-083) | Form user Create/Edit menggandakan field dan pilihan outlet/role | Anggota 3 | Opsional | P2 | sedang | 5–8 |
| [RF-084](#rf-084) | Form kategori Create/Edit menggandakan field dan image preview | Anggota 3 | Opsional | P2 | rendah | 3–5 |
| [RF-085](#rf-085) | Filter/tabel laporan Sales dan Profit menggandakan UI | Anggota 3 | Opsional | P2 | rendah–sedang | 4–7 |
| [RF-086](#rf-086) | Form pembayaran hutang/piutang UI menggandakan panel | Anggota 3 | Opsional | P2 | sedang | 5–8 |
| [RF-087](#rf-087) | CRUD bank account menggandakan rules upload dan audit web/API | Anggota 1 | Opsional | P2 | sedang–tinggi | 5–8 |
| [RF-088](#rf-088) | DineOrder accept menulis sale, profit dan stok di satu metode | Anggota 2 | Tunda | P3 | tinggi | 10–16 |
| [RF-089](#rf-089) | Submit pesanan dine publik mencampur validasi dan persiapan sale items | Anggota 2 | Opsional | P2 | sedang–tinggi | 6–10 |
| [RF-090](#rf-090) | Webhook provider menggandakan settlement tender/transaction | Anggota 2 | Tunda | P3 | tinggi | 10–16 |
| [RF-091](#rf-091) | Data migration bergantung pada model aplikasi yang terus berubah | Anggota 1 | Tunda | P3 | tinggi | 8–12 |

<a id="rf-001"></a>

## RF-001 — Payload pembaruan produk diduplikasi pada cabang gambar

**Pemilik:** Anggota 1. **Scope:** Inti. **Prioritas:** P1. **Risiko:** sedang. **Estimasi:** 4–6 jam-orang.

[app/Http/Controllers/Apps/ProductController.php — ProductController::update(), 184–279](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/ProductController.php#L184-L279). Bukti baris 252–258:

```php
252: 
253:         // update product without image
254:         $product->update([
255:             'barcode' => $request->barcode,
256:             'sku' => $request->sku,
257:             'title' => $request->title,
258:             'description' => $request->description,
```

[app/Http/Controllers/Apps/ProductController.php — ProductController::update(), 184–279](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/ProductController.php#L184-L279). Bukti baris 224–230:

```php
224: 
225:             // update product with new image
226:             $product->update([
227:                 'image' => $image->hashName(),
228:                 'barcode' => $request->barcode,
229:                 'sku' => $request->sku,
230:                 'title' => $request->title,
```

**Masalah:** Duplicate Code; Divergent Change. Daftar atribut, sinkronisasi unit/komponen, logging dan redirect muncul dua kali. Penambahan atribut harus diubah pada kedua cabang.

**Teknik dan alasan:** Extract Method; Decompose Conditional; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: bangun atribut update sekali; cabang upload hanya menghasilkan atribut image; jalankan update, syncComponents/syncUnits dan logProductUpdate satu kali. Jangan menambah validasi gambar dalam commit refactoring.

**Invarian dan kriteria khusus selesai:** Atribut image tetap tidak ditulis saat tidak ada upload; path penyimpanan, penghapusan gambar lama, validasi, event audit dan redirect tetap identik. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Koordinasikan kontrak field dengan RF-002; implementasi dapat paralel karena file berbeda.

**Pengujian wajib:** T02: update tanpa/dengan gambar, produk normal/komposit; bandingkan DB, Storage::fake dan dua event audit. Update tidak boleh mengubah stock. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-002"></a>

## RF-002 — Form produk Create/Edit menggandakan field dan editor baris

**Pemilik:** Anggota 3. **Scope:** Inti. **Prioritas:** P1. **Risiko:** sedang. **Estimasi:** 10–14 jam-orang.

[resources/js/Pages/Dashboard/Products/Create.jsx — Create/addUnitRow, konteks 21–765](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Products/Create.jsx#L21-L765). Bukti baris 55–61:

```jsx
55: 
56:     const addUnitRow = () => {
57:         const firstUnused = units.find(
58:             (u) => !data.units.some((row) => row.unit_id === u.id)
59:         );
60:         if (!firstUnused) return;
61:         setData("units", [
```

[resources/js/Pages/Dashboard/Products/Edit.jsx — Edit/addUnitRow, konteks 21–796](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Products/Edit.jsx#L21-L796). Bukti baris 74–80:

```jsx
74: 
75:     const addUnitRow = () => {
76:         const firstUnused = units.find(
77:             (u) => !data.units.some((row) => row.unit_id === u.id)
78:         );
79:         if (!firstUnused) return;
80:         setData("units", [
```

**Masalah:** Duplicate JSX; Large Component. Create 767 baris dan Edit 798 baris; jscpd menemukan beberapa blok panjang identik. Editor unit/komponen dan field produk berubah berpasangan.

**Teknik dan alasan:** Extract Component; Extract Custom Hook seperlunya; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: ProductFormFields, ProductUnitRows dan CompositeRows menerima data/setData/errors/options; halaman mempertahankan useForm, nilai awal serta submit masing-masing. Ekstrak operasi array murni jika benar-benar dipakai keduanya.

**Invarian dan kriteria khusus selesai:** Create tetap mengirim stock, Edit tetap _method=PUT; pilihan produk Edit mengecualikan diri sendiri; imagePreview, default pajak, error key, urutan field dan zeroing komposit tetap sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-001 berbagi kontrak payload, tidak berbagi file. RF-032 dapat digabung setelah format output dipotret.

**Pengujian wajib:** T02/T03: ulangi create/edit biasa dan komposit; tambah/hapus unit, ubah base, gambar kosong/baru, barcode duplikat; rekam payload serta hasil UI. Node test untuk operasi array jika diekstrak. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-003"></a>

## RF-003 — Resolusi wilayah customer/member berulang

**Pemilik:** Anggota 1. **Scope:** Inti. **Prioritas:** P1. **Risiko:** rendah–sedang. **Estimasi:** 6–8 jam-orang.

[app/Http/Controllers/Apps/CustomerController.php — CustomerController::store(), 73–113](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/CustomerController.php#L73-L113). Bukti baris 89–95:

```php
89: 
90:         $province = Province::where('code', $request->province_id)->first();
91:         $regency = City::where('code', $request->regency_id)->first();
92:         $district = District::where('code', $request->district_id)->first();
93:         $village = Village::where('code', $request->village_id)->first();
94: 
95:         // create customer
```

[app/Http/Controllers/Apps/MemberController.php — MemberController::resolveRegionPayload(), 219–236](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/MemberController.php#L219-L236). Bukti baris 220–226:

```php
220:     {
221:         $province = Province::where('code', $validated['province_id'])->first();
222:         $regency = City::where('code', $validated['regency_id'])->first();
223:         $district = District::where('code', $validated['district_id'])->first();
224:         $village = Village::where('code', $validated['village_id'])->first();
225: 
226:         return [
```

**Masalah:** Duplicate Code; Data Clumps. Empat pasangan kode/nama wilayah dipetakan pada store/update/AJAX dan member. Perubahan pemetaan wilayah tersebar di beberapa jalur.

**Teknik dan alasan:** Extract Class kecil; Move Method; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: resolver wilayah menerima array kode nullable dan mengembalikan delapan key id/name. Gunakan pada customer store/update/storeAjax dan member; validasi tetap berada di masing-masing adapter.

**Invarian dan kriteria khusus selesai:** Form penuh tetap mewajibkan wilayah, AJAX tetap boleh null; kode yang tidak ditemukan tetap menghasilkan nama null; jangan diam-diam menambah validasi hirarki atau mengubah keanggotaan. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-005 hanya konsumen frontend; tidak wajib menunggu.

**Pengujian wajib:** T04: empat kode valid, null AJAX, kode tidak ditemukan, update wilayah, telepon duplikat; assert kode/nama dan response web/AJAX masing-masing. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-004"></a>

## RF-004 — Query statistik dan riwayat customer/member berulang

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 5–8 jam-orang.

[app/Http/Controllers/Apps/CustomerController.php — CustomerController::buildStats(), 447–456](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/CustomerController.php#L447-L456). Bukti baris 448–454:

```php
448:     {
449:         return Transaction::where('customer_id', $customer->id)
450:             ->selectRaw('
451:                 COUNT(*) as total_transactions,
452:                 SUM(grand_total) as total_spent,
453:                 MAX(created_at) as last_visit
454:             ')
```

[app/Http/Controllers/Apps/MemberController.php — MemberController::buildStats(), 261–270](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/MemberController.php#L261-L270). Bukti baris 262–268:

```php
262:     {
263:         return Transaction::where('customer_id', $customer->id)
264:             ->selectRaw('
265:                 COUNT(*) as total_transactions,
266:                 SUM(grand_total) as total_spent,
267:                 MAX(created_at) as last_visit
268:             ')
```

**Masalah:** Duplicate Query; Feature Envy. Kedua controller menyusun statistik transaksi, barang sering dibeli dan reward customer; penambahan metrik berisiko berbeda.

**Teknik dan alasan:** Extract Query/Move Method; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: reuse query statistik, recentTransactions dan frequentProducts dengan parameter customer serta limit eksplisit; serializer tetap per halaman bila berbeda.

**Invarian dan kriteria khusus selesai:** Jumlah/urutan limit, tipe nilai, format tanggal dan customer scope tetap mengikuti tiap endpoint; jangan menyisipkan filter status pembayaran baru. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Pisahkan dari RF-003 agar review tetap kecil.

**Pengujian wajib:** T04/T14: customer tanpa transaksi, satu/banyak transaksi, tie qty; bandingkan Inertia props dan API member. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-005"></a>

## RF-005 — Rangkaian dropdown wilayah pada form pelanggan/member berulang

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 6–9 jam-orang.

[resources/js/Pages/Dashboard/Customers/Create.jsx — Create, konteks 14–325](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Customers/Create.jsx#L14-L325). Bukti baris 23–29:

```jsx
23:         province_id: "",
24:         regency_id: "",
25:         district_id: "",
26:         village_id: "",
27:     });
28: 
29:     const [regencies, setRegencies] = useState([]);
```

[resources/js/Pages/Dashboard/Customers/Edit.jsx — Edit, konteks 10–362](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Customers/Edit.jsx#L10-L362). Bukti baris 27–33:

```jsx
27:         province_id: customer.province_id || "",
28:         regency_id: customer.regency_id || "",
29:         district_id: customer.district_id || "",
30:         village_id: customer.village_id || "",
31:         _method: "PUT",
32:     });
33: 
```

[resources/js/Pages/Dashboard/Members/Form.jsx — Form, konteks 14–443](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Members/Form.jsx#L14-L443). Bukti baris 31–37:

```jsx
31:         province_id: member?.province_id ?? "",
32:         regency_id: member?.regency_id ?? "",
33:         district_id: member?.district_id ?? "",
34:         village_id: member?.village_id ?? "",
35:         _method: isEdit ? "PUT" : "POST",
36:     });
37: 
```

**Masalah:** Duplicate JSX; State/Effect Coupling. Pergantian provinsi/kota memerlukan reset field turunan dan request berurutan di beberapa form.

**Teknik dan alasan:** Extract Custom Hook; Extract Component; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: useRegionOptions mengelola opsi dan reset turunan; RegionFields menerima pilihan serta error. Pertahankan initial options halaman Edit dan opsionalitas modal POS.

**Invarian dan kriteria khusus selesai:** URL regions, timing fetch/reset, empty options dan nilai awal Edit tetap sama; pembatalan request/stale response fix ditulis terpisah jika mengubah perilaku. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Sesudah kontrak RF-003 disepakati; tidak mengubah resolver backend.

**Pengujian wajib:** T04: pilih provinsi lalu kota/kecamatan/desa, ganti provinsi, edit existing, HTTP region gagal dan opsi kosong. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-006"></a>

## RF-006 — Detail customer dan member menggandakan panel riwayat

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah–sedang. **Estimasi:** 5–8 jam-orang.

[resources/js/Pages/Dashboard/Customers/Show.jsx — Show, konteks 29–496](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Customers/Show.jsx#L29-L496). Bukti baris 34–40:

```jsx
34:     stats,
35:     recentTransactions,
36:     frequentProducts,
37:     rewardHistory,
38:     vouchers,
39: }) {
40:     const segmentForm = useForm({
```

[resources/js/Pages/Dashboard/Members/Show.jsx — Show, konteks 30–385](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Members/Show.jsx#L30-L385). Bukti baris 32–38:

```jsx
32:     stats,
33:     recentTransactions = [],
34:     frequentProducts = [],
35:     rewardHistory = [],
36:     vouchers = [],
37:     segments = [],
38: }) {
```

**Masalah:** Duplicate JSX. Panel profil, reward/voucher dan riwayat memiliki banyak blok clone; perubahan visual atau field dilakukan pada dua halaman.

**Teknik dan alasan:** Extract Component; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: ekstrak TransactionHistoryPanel dan RewardHistoryPanel dari blok yang benar-benar sama; adapter memberi label customer/member serta route yang berbeda.

**Invarian dan kriteria khusus selesai:** Tautan, tanggal, empty state, pagination/limit, permission tombol upgrade/deactivate tetap sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-004 menyediakan data tetapi ekstraksi UI bisa mandiri.

**Pengujian wajib:** T04: member aktif/nonaktif, tanpa riwayat, voucher aktif/terpakai, izin berbeda; bandingkan screenshot dan link. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-007"></a>

## RF-007 — Pencatatan pembayaran piutang diduplikasi web/API

**Pemilik:** Anggota 2. **Scope:** Inti. **Prioritas:** P1. **Risiko:** sedang–tinggi. **Estimasi:** 6–8 jam-orang.

[app/Http/Controllers/Apps/ReceivableController.php — ReceivableController::pay(), 85–144](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/ReceivableController.php#L85-L144). Bukti baris 95–101:

```php
95: 
96:         DB::transaction(function () use ($validated, $receivable, $request) {
97:             // ponytail: lock the receivable row so concurrent payments cannot double-apply past the remaining balance
98:             $receivable = Receivable::whereKey($receivable->id)->lockForUpdate()->firstOrFail();
99: 
100:             $remaining = $receivable->remaining;
101:             if ($validated['amount'] > $remaining) {
```

[app/Http/Controllers/Api/V1/ReceivableController.php — ReceivableController::payReceivable(), 134–191](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/ReceivableController.php#L134-L191). Bukti baris 144–150:

```php
144: 
145:         DB::transaction(function () use ($validated, $receivable, $request) {
146:             // ponytail: lock the receivable row so concurrent payments cannot double-apply past the remaining balance
147:             $receivable = Receivable::whereKey($receivable->id)->lockForUpdate()->firstOrFail();
148: 
149:             $remaining = $receivable->remaining;
150:             if ($validated['amount'] > $remaining) {
```

**Masalah:** Duplicate Business Logic; Controller terlalu banyak tanggung jawab. Kedua jalur mengunci piutang, membatasi nominal, memeriksa rekening outlet, membuat payment, menentukan overdue dan menyelaraskan transaction.

**Teknik dan alasan:** Extract Class; Move Method; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: ReceivablePaymentRecorder khusus piutang menerima receivable ID, payload, actor dan outlet; pindahkan isi transaksi sekali. Adapter tetap ensureAccess, validate dan membentuk redirect/JSON masing-masing.

**Invarian dan kriteria khusus selesai:** Lock dan urutan pengecekan tetap; partial/paid/overdue, nilai paid, payment_status transaksi, pesan error, response status dan rollback harus sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-008 paralel pada file terpisah. RF-011 harus selesai sebelum rebase final karena logika overdue diuji bersamaan.

**Pengujian wajib:** T05: saldo 100000, bayar 40000→partial, 60000→paid; overpay/0/invalid-date ditolak; rekening outlet lain, akses tanpa ability, gagal insert rollback; bandingkan web/API. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-008"></a>

## RF-008 — Pencatatan pembayaran hutang diduplikasi web/API

**Pemilik:** Anggota 2. **Scope:** Inti. **Prioritas:** P1. **Risiko:** sedang–tinggi. **Estimasi:** 5–7 jam-orang.

[app/Http/Controllers/Apps/PayableController.php — PayableController::pay(), 166–219](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/PayableController.php#L166-L219). Bukti baris 176–182:

```php
176: 
177:         DB::transaction(function () use ($validated, $payable, $request) {
178:             // ponytail: lock the payable row so concurrent payments cannot double-apply past the remaining balance
179:             $payable = Payable::whereKey($payable->id)->lockForUpdate()->firstOrFail();
180: 
181:             $remaining = $payable->remaining;
182:             if ($validated['amount'] > $remaining) {
```

[app/Http/Controllers/Api/V1/PayableController.php — PayableController::payPayable(), 170–221](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/PayableController.php#L170-L221). Bukti baris 180–186:

```php
180: 
181:         DB::transaction(function () use ($validated, $payable, $request) {
182:             // ponytail: lock the payable row so concurrent payments cannot double-apply past the remaining balance
183:             $payable = Payable::whereKey($payable->id)->lockForUpdate()->firstOrFail();
184: 
185:             $remaining = $payable->remaining;
186:             if ($validated['amount'] > $remaining) {
```

**Masalah:** Duplicate Business Logic; Coupling controller/DB. Lock saldo, validasi bank, payment row dan status hutang disalin dari adapter web ke API.

**Teknik dan alasan:** Extract Class; Move Method; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: PayablePaymentRecorder khusus hutang dengan batas transaksi dan input eksplisit; jangan menyatukannya dengan piutang melalui generic repository.

**Invarian dan kriteria khusus selesai:** Tidak menambah update transaction karena hutang tidak memakai cabang itu; nominal float yang saat ini diterima, status overdue, error hutang dan response web/API tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-011 memengaruhi penentuan aging; test ulang kombinasi setelah integrasi.

**Pengujian wajib:** T05: partial/exact/overpay, pembayaran kedua melebihi sisa, rekening tidak aktif/lintas outlet, permission, gagal insert rollback. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-009"></a>

## RF-009 — Filter dan dekorasi daftar piutang/hutang web/API berulang

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 4–7 jam-orang.

[app/Http/Controllers/Apps/ReceivableController.php — ReceivableController::index(), 23–63](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/ReceivableController.php#L23-L63). Bukti baris 24–30:

```php
24:     {
25:         $filters = [
26:             'status' => $request->input('status'),
27:             'customer' => $request->input('customer'),
28:             'invoice' => $request->input('invoice'),
29:             'due_from' => $request->input('due_from'),
30:             'due_to' => $request->input('due_to'),
```

[app/Http/Controllers/Api/V1/ReceivableController.php — ReceivableController::listReceivables(), 23–60](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/ReceivableController.php#L23-L60). Bukti baris 24–30:

```php
24:     {
25:         $filters = [
26:             'status' => $request->input('status'),
27:             'customer' => $request->input('customer'),
28:             'invoice' => $request->input('invoice'),
29:             'due_from' => $request->input('due_from'),
30:             'due_to' => $request->input('due_to'),
```

[app/Http/Controllers/Api/V1/PayableController.php — PayableController::listPayables(), 25–62](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/PayableController.php#L25-L62). Bukti baris 26–32:

```php
26:     {
27:         $filters = [
28:             'status' => $request->input('status'),
29:             'supplier' => $request->input('supplier'),
30:             'invoice' => $request->input('invoice'),
31:             'due_from' => $request->input('due_from'),
32:             'due_to' => $request->input('due_to'),
```

**Masalah:** Duplicate Query/Transformation. Filter status, customer/supplier, invoice, jatuh tempo, total_paid dan status display berulang.

**Teknik dan alasan:** Extract Query dan presenter kecil; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: query daftar terpisah untuk receivable/payable; ekstrak deklarasi filter dan map status display yang sama tanpa menyatukan query relasi berbeda.

**Invarian dan kriteria khusus selesai:** Filter dijalankan sebelum display overdue seperti saat ini; pagination serta total_paid dan status tidak dipersist hanya karena render. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tidak menumpang RF-007/008 pada controller yang sama sebelum keduanya terintegrasi.

**Pengujian wajib:** T05: filter invoice, range due, status paid/overdue, halaman kosong, query-string dan tipe field; assert DB tidak berubah oleh GET. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-010"></a>

## RF-010 — Scoping hutang berulang di controller dan aging service

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** tinggi. **Estimasi:** 4–6 jam-orang.

[app/Http/Controllers/Apps/PayableController.php — PayableController::scopeQuery(), 221–232](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/PayableController.php#L221-L232). Bukti baris 222–228:

```php
222:     {
223:         return $query->where(function ($builder) use ($warehouseIds) {
224:             $builder->whereHas('purchaseOrder', fn ($purchaseOrder) => $purchaseOrder->whereIn('warehouse_id', $warehouseIds));
225: 
226:             if ($warehouseIds->isEmpty()) {
227:                 $builder->whereRaw('1 = 0');
228:             } elseif (Outlet::active()->count() <= 1) {
```

[app/Services/PayableAgingService.php — PayableAgingService::scopeQuery(), 34–51](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/PayableAgingService.php#L34-L51). Bukti baris 39–45:

```php
39: 
40:         return $query->where(function ($builder) use ($warehouseIds) {
41:             if ($warehouseIds->isEmpty()) {
42:                 $builder->whereRaw('1 = 0');
43:             } else {
44:                 $builder->whereHas('purchaseOrder', fn ($purchaseOrder) => $purchaseOrder->whereIn('warehouse_id', $warehouseIds));
45: 
```

**Masalah:** Duplicate Query; Shotgun Surgery otorisasi. Aturan warehouse kosong serta legacy single-outlet disusun ulang. Perubahan akses harus konsisten di statement dan aging.

**Teknik dan alasan:** Move Method; Encapsulate Query; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: gunakan satu payable scopeQuery; jangan mengubah kriteria legacy atau menggeneralisasi semua scope multi-outlet sebelum dibandingkan.

**Invarian dan kriteria khusus selesai:** Koleksi warehouse null berarti unrestricted pada service, kosong berarti tidak ada akses; legacy tanpa PO hanya di satu outlet aktif. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Bergantung kesepakatan policy; setelah RF-008 untuk menghindari konflik controller.

**Pengujian wajib:** T01/T05: null/empty/dua gudang, satu/dua outlet aktif, payable tanpa PO dan tidak berwenang. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-011"></a>

## RF-011 — Perhitungan aging pada model piutang/hutang identik

**Pemilik:** Anggota 1. **Scope:** Inti. **Prioritas:** P1. **Risiko:** sedang. **Estimasi:** 4–6 jam-orang.

[app/Models/Receivable.php — Receivable::getAgingBucketAttribute(), 60–82](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Models/Receivable.php#L60-L82). Bukti baris 73–79:

```php
73: 
74:         $daysOverdue = now()->diffInDays($this->due_date, true);
75: 
76:         return match (true) {
77:             $daysOverdue <= 30 => '0-30',
78:             $daysOverdue <= 60 => '31-60',
79:             $daysOverdue <= 90 => '61-90',
```

[app/Models/Payable.php — Payable::getAgingBucketAttribute(), 54–76](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Models/Payable.php#L54-L76). Bukti baris 67–73:

```php
67: 
68:         $daysOverdue = now()->diffInDays($this->due_date, true);
69: 
70:         return match (true) {
71:             $daysOverdue <= 30 => '0-30',
72:             $daysOverdue <= 60 => '31-60',
73:             $daysOverdue <= 90 => '61-90',
```

**Masalah:** Duplicate Code; Magic Strings terlokalisasi. Accessor yang sama memuat batas 30/60/90 dan label bucket. Kesalahan batas hari harus diperbaiki dua kali.

**Teknik dan alasan:** Extract pure Function/Class; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: DebtAgingBucket::resolve(status,dueDate,at) mengembalikan string bucket; kedua accessor mendelegasikan. Tetap gunakan Carbon dan semantik diffInDays yang sama, jangan membulatkan hari.

**Invarian dan kriteria khusus selesai:** Urutan paid→no_due_date→current→bucket dan pembandingan now terhadap tanggal tetap; appends dan tipe remaining tetap sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-007/008 memakai status overdue yang berkaitan tetapi tidak boleh mengganti aturan status lewat helper bucket.

**Pengujian wajib:** T06: waktu dibekukan, paid, tanpa due, due sekarang, 30/60/90 hari dan satu detik di kedua sisi batas; model dan output JSON harus identik. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-012"></a>

## RF-012 — Agregasi aging service berulang

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah–sedang. **Estimasi:** 3–5 jam-orang.

[app/Services/ReceivableService.php — ReceivableService::getAgingSummary(), 12–32](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/ReceivableService.php#L12-L32). Bukti baris 18–24:

```php
18: 
19:         $buckets = ['current', '0-30', '31-60', '61-90', '90+'];
20: 
21:         return collect($buckets)->map(function ($bucket) use ($receivables) {
22:             $filtered = $receivables->filter(fn ($r) => $r->aging_bucket === $bucket);
23: 
24:             return [
```

[app/Services/PayableAgingService.php — PayableAgingService::getAgingSummary(), 12–32](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/PayableAgingService.php#L12-L32). Bukti baris 18–24:

```php
18: 
19:         $buckets = ['current', '0-30', '31-60', '61-90', '90+'];
20: 
21:         return collect($buckets)->map(function ($bucket) use ($payables) {
22:             $filtered = $payables->filter(fn ($p) => $p->aging_bucket === $bucket);
23: 
24:             return [
```

**Masalah:** Duplicate Transformation. Pemetaan lima bucket ke count,total,paid,remaining identik; perubahan output summary tersebar.

**Teknik dan alasan:** Extract Function; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: agregator koleksi menerima record dan ordered bucket list; query dan scope tetap per domain.

**Invarian dan kriteria khusus selesai:** Urutan bucket, pengecualian paid/no due, tipe sum dan clamp remaining sama; tidak mengganti agregasi SQL dalam refactoring ini. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-011 terlebih dahulu jika dikerjakan; tidak wajib untuk paket inti.

**Pengujian wajib:** T06: semua bucket, bucket kosong, paid dan tanpa jatuh tempo; assert array lengkap dan order. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-013"></a>

## RF-013 — Finalisasi stock opname menggandakan transaksi inventory

**Pemilik:** Anggota 2. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 10–16 jam-orang.

[app/Http/Controllers/Apps/StockOpnameController.php — StockOpnameController::finalize(), 217–310](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/StockOpnameController.php#L217-L310). Bukti baris 232–238:

```php
232: 
233:         DB::transaction(function () use ($request, $stockOpname) {
234:             foreach ($stockOpname->items as $item) {
235:                 if ($item->physical_stock === null) {
236:                     continue;
237:                 }
238: 
```

[app/Http/Controllers/Api/V1/StockOpnameController.php — StockOpnameController::finalizeOpname(), 197–290](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/StockOpnameController.php#L197-L290). Bukti baris 212–218:

```php
212: 
213:         DB::transaction(function () use ($request, $stockOpname) {
214:             foreach ($stockOpname->items as $item) {
215:                 if ($item->physical_stock === null) {
216:                     continue;
217:                 }
218: 
```

**Masalah:** Duplicate Business Logic; Long Method. Finalisasi menggabungkan pemeriksaan draft, lock product/pivot, koreksi aggregate, mutasi dan audit dalam dua adapter.

**Teknik dan alasan:** Extract Class; Move Method; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: FinalizeStockOpname menerima actor serta ID; pertahankan boundary transaksi, validasi item dan format audit; response tetap di adapter.

**Invarian dan kriteria khusus selesai:** Pivot operasional dan aggregate sinkron, hanya draft bisa finalize, item milik opname, null physical_stock, urutan audit dan error identik. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tunda sampai karakterisasi concurrency MySQL tersedia; jangan digabung perubahan RF-051 audit.

**Pengujian wajib:** T07: stok 10→7 dan 10→12, unchanged, physical null, cross-opname, cross-outlet, finalize ulang serta rollback item kedua gagal. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-014"></a>

## RF-014 — Completion retur penjualan dan API menggandakan settlement

**Pemilik:** Anggota 2. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 14–22 jam-orang.

[app/Http/Controllers/Apps/SalesReturnController.php — SalesReturnController::complete(), 180–338](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/SalesReturnController.php#L180-L338). Bukti baris 187–193:

```php
187: 
188:         DB::transaction(function () use ($request, $salesReturn) {
189:             $activeShift = $this->cashierShiftService->requireActiveShiftForUser(
190:                 $request->user()->id,
191:                 lockForUpdate: true
192:             );
193: 
```

[app/Http/Controllers/Api/V1/SalesReturnController.php — SalesReturnController::completeReturn(), 177–328](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/SalesReturnController.php#L177-L328). Bukti baris 184–190:

```php
184: 
185:         DB::transaction(function () use ($request, $salesReturn) {
186:             $activeShift = $this->cashierShiftService->requireActiveShiftForUser(
187:                 $request->user()->id,
188:                 lockForUpdate: true
189:             );
190: 
```

**Masalah:** Long Method; Duplicate Business Logic. Completion menangani qty tersisa, restock base unit, profit reversal, settlement piutang dan store credit sekaligus.

**Teknik dan alasan:** Extract Class; Extract Method; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: SalesReturnCompletion mengorkestrasi validateRemaining, restock, reverseMargin dan applySettlement; method kalkulasi murni dipisah dari mutasi.

**Invarian dan kriteria khusus selesai:** Refund vs store_credit, pembulatan baseQty, formula margin saat ini, cap sisa qty, shift dan izin serta audit tetap sama; koreksi formula lama merupakan bug fix terpisah. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-015 dapat lebih dulu. RF-023 bila kelak berbagi inventory action perlu kontrak berbeda untuk retur.

**Pengujian wajib:** T08: refund tunai, store credit, pay_later sebagian, conversion>1, no restock, qty melebihi sisa, double-complete serta failure rollback. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-015"></a>

## RF-015 — Aturan request create/update retur penjualan identik

**Pemilik:** Anggota 2. **Scope:** Inti. **Prioritas:** P1. **Risiko:** rendah. **Estimasi:** 2–3 jam-orang.

[app/Http/Requests/StoreSalesReturnRequest.php — StoreSalesReturnRequest::rules(), 14–25](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Requests/StoreSalesReturnRequest.php#L14-L25). Bukti baris 16–22:

```php
16:         return [
17:             'return_type' => ['required', 'in:refund_cash,store_credit'],
18:             'notes' => ['nullable', 'string'],
19:             'items' => ['required', 'array', 'min:1'],
20:             'items.*.transaction_detail_id' => ['required', 'integer', 'exists:transaction_details,id'],
21:             'items.*.qty_return' => ['nullable', 'integer', 'min:0'],
22:             'items.*.return_reason' => ['nullable', 'string', 'max:255'],
```

[app/Http/Requests/UpdateSalesReturnRequest.php — UpdateSalesReturnRequest::rules(), 14–25](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Requests/UpdateSalesReturnRequest.php#L14-L25). Bukti baris 16–22:

```php
16:         return [
17:             'return_type' => ['required', 'in:refund_cash,store_credit'],
18:             'notes' => ['nullable', 'string'],
19:             'items' => ['required', 'array', 'min:1'],
20:             'items.*.transaction_detail_id' => ['required', 'integer', 'exists:transaction_details,id'],
21:             'items.*.qty_return' => ['nullable', 'integer', 'min:0'],
22:             'items.*.return_reason' => ['nullable', 'string', 'max:255'],
```

**Masalah:** Duplicate Validation. Dua rules array identik mudah menyimpang saat field retur berubah.

**Teknik dan alasan:** Extract Method/Shared Rule Provider; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: satu provider rules statis dipanggil dua request atau satu parent request kecil; class request yang dipakai route tetap dipertahankan.

**Invarian dan kriteria khusus selesai:** Semua nama field, nullable qty, min/max, return_type, messages Laravel, authorize dan HTTP behavior tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tidak perlu menunggu RF-014; perubahan ini tidak menyentuh logika completion.

**Pengujian wajib:** T08: kedua endpoint dengan qty 0/null/negatif, items kosong, detail hilang, alasan >255 dan return_type invalid; error key harus sama. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-016"></a>

## RF-016 — Validasi pricing rule sekaligus menormalisasi banyak bentuk promo

**Pemilik:** Anggota 1. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 10–16 jam-orang.

[app/Http/Controllers/Apps/PricingRuleController.php — PricingRuleController::validateRule(), 268–440](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/PricingRuleController.php#L268-L440). Bukti baris 269–275:

```php
269:     {
270:         $validated = $request->validate([
271:             'name' => ['required', 'string', 'max:255'],
272:             'kind' => ['required', Rule::in([
273:                 PricingRule::KIND_STANDARD_DISCOUNT,
274:                 PricingRule::KIND_QTY_BREAK,
275:                 PricingRule::KIND_BUNDLE_PRICE,
```

**Masalah:** Long Method; Conditional Complexity. validateRule 173 baris menggabungkan aturan umum, kind-specific, constraint bundle/buy-get dan normalisasi relasi.

**Teknik dan alasan:** Extract Method; Decompose Conditional; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: pecah baseRules, validateKind dan normalizeRelations; pertahankan satu entry validateRule agar web/API menggunakan aturan yang sama.

**Invarian dan kriteria khusus selesai:** Urutan error, preview sample, target/kind, relation sort_order dan default field sama; jangan mengubah eligible tiers atau currency rounding. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Setelah characterization PricingRuleTest dan V1AdminApiTest; tidak bersamaan RF-074 Form.jsx.

**Pengujian wajib:** T09: empat jenis promo, relasi kosong, produk duplikat, tanggal terbalik, scope member/tier serta preview vs saved rule. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-017"></a>

## RF-017 — Pricing buildPreview mencampur beberapa tahap algoritma

**Pemilik:** Anggota 1. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 10–16 jam-orang.

[app/Services/PricingService.php — PricingService::buildPreview(), 172–295](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/PricingService.php#L172-L295). Bukti baris 206–212:

```php
206: 
207:         $bundleRules = $eligibleRules
208:             ->filter(fn (PricingRule $rule) => $rule->kind === PricingRule::KIND_BUNDLE_PRICE)
209:             ->values();
210:         $appliedGroups = array_merge(
211:             $appliedGroups,
212:             $this->applyComplexStage($bundleRules, $items, $remainingQuantities, 'bundle')
```

[app/Services/PricingService.php — PricingService::applyComplexStage(), 297–350](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/PricingService.php#L297-L350). Bukti baris 304–310:

```php
304: 
305:         while (true) {
306:             $candidates = $rules
307:                 ->map(function (PricingRule $rule) use ($items, $remainingQuantities, $stage) {
308:                     return $stage === 'bundle'
309:                         ? $this->buildBundleCandidate($rule, $items, $remainingQuantities)
310:                         : $this->buildBuyGetCandidate($rule, $items, $remainingQuantities);
```

**Masalah:** Long Method; Temporal Coupling; Primitive Array Shapes. Tahap bundle→buy-get→line discount, remaining qty dan hasil akhir memakai array mutable yang harus dipahami bersama.

**Teknik dan alasan:** Extract Method; Encapsulate bentuk hasil secukupnya; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: pisah initializeItems, applyDirectRules dan summarizePreview; dokumentasikan shape array. Tetap gunakan urutan dan tie-break yang ada, tanpa strategy framework baru.

**Invarian dan kriteria khusus selesai:** Priority desc, discount desc, ID asc; urutan tahap, discount cap, floor/round dan group-key acak tetap. Normalize hanya UUID saat membandingkan baseline. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tunda karena bug potensial algoritma harus dipisahkan dari restrukturisasi; RF-018 dapat lebih kecil.

**Pengujian wajib:** T09: promo bertumpuk, tie priority, bundle tidak lengkap, reward qty sama produk, tanpa rule dan sisa qty. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-018"></a>

## RF-018 — calculateLineCandidate menggandakan struktur hasil

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah–sedang. **Estimasi:** 2–4 jam-orang.

[app/Services/PricingService.php — PricingService::calculateLineCandidate(), 529–584](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/PricingService.php#L529-L584). Bukti baris 537–543:

```php
537: 
538:         if ($rule->kind === PricingRule::KIND_QTY_BREAK) {
539:             $break = $rule->qtyBreaks
540:                 ->filter(fn (PricingRuleQtyBreak $break) => $quantity >= (int) $break->min_qty)
541:                 ->sortBy([
542:                     ['min_qty', 'desc'],
543:                     ['sort_order', 'asc'],
```

[app/Services/PricingService.php — PricingService::calculateLineCandidate(), 529–584](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/PricingService.php#L529-L584). Bukti baris 562–568:

```php
562:                 'base_unit_price' => $baseUnitPrice,
563:                 'line_base_total' => $lineBaseTotal,
564:                 'line_total' => max(0, $lineBaseTotal - $discount),
565:                 'line_discount' => $discount,
566:             ];
567:         }
568: 
```

**Masalah:** Duplicate Code. Return candidate pada qty-break dan diskon standar memiliki field identik; hanya pemilihan discount yang berbeda.

**Teknik dan alasan:** Decompose Conditional; Extract Method; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: cabang hanya menghitung discount atau null; satu builder menyusun candidate.

**Invarian dan kriteria khusus selesai:** Qty-break tidak ditemukan tetap null; angka integer, cap line_total dan object rule tetap persis. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Mandiri dari RF-017 tetapi satu file: satu pemilik mengintegrasikan berurutan.

**Pengujian wajib:** T09 unit: standard percentage/fixed/fixed-price, qty break sebelum/pada/setelah batas dan nilai nol. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-019"></a>

## RF-019 — Loyalty preview juga mengakses produk dan menghitung pajak

**Pemilik:** Anggota 1. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 8–14 jam-orang.

[app/Services/LoyaltyService.php — LoyaltyService::previewCheckout(), 173–286](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/LoyaltyService.php#L173-L286). Bukti baris 207–213:

```php
207: 
208:         // Calculate tax
209:         $taxService = app(TaxService::class);
210:         $defaultTaxRate = $taxService->getDefaultRate($outlet);
211:         $items = data_get($pricingPreview, 'items', []);
212:         $productIds = collect($items)->pluck('product_id')->filter()->unique()->values();
213:         $productTaxes = Product::whereIn('id', $productIds)->pluck('tax_rate', 'id');
```

[app/Services/TaxService.php — TaxService::calculateTransactionTax(), 42–71](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/TaxService.php#L42-L71). Bukti baris 47–53:

```php
47: 
48:         foreach ($items as $item) {
49:             $lineTotal = (int) ($item['line_total'] ?? $item['price'] ?? 0);
50:             $itemTaxType = $item['tax_type'] ?? 'exclusive';
51:             $itemTaxRate = (float) ($item['tax_rate'] ?? $defaultRate);
52: 
53:             $taxResult = $this->calculateLineItem($lineTotal, $itemTaxType, $itemTaxRate);
```

**Masalah:** Divergent Change; Mixed Responsibilities. LoyaltyService.previewCheckout 114 baris menghitung voucher/poin/manual discount sekaligus memuat tax product dan shipping tax; TaxService sudah memiliki kalkulasi item.

**Teknik dan alasan:** Extract Method; Move Method; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: ekstrak kalkulasi pajak preview ke fungsi khusus TaxService dengan input item + metadata tax; presenter summary tetap mempertahankan key lama.

**Invarian dan kriteria khusus selesai:** Formula saat ini, rate efektif terakhir, inclusive/exclusive, pajak ongkir dan urutan diskon tetap; memperbaiki pajak inclusive merupakan bug fix berbeda. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tunda sampai expected inclusive disetujui terpisah; RF-017 mengubah shape preview sehingga jangan paralel.

**Pengujian wajib:** T10/T09: tax0, exclusive/inclusive, mixed rates, shipping, voucher+redeem+manual cap dan product hilang. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-020"></a>

## RF-020 — Normalisasi tender memuat validasi baris dan agregat

**Pemilik:** Anggota 2. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 6–10 jam-orang.

[app/Services/TransactionTenderService.php — TransactionTenderService::normalize(), 33–167](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/TransactionTenderService.php#L33-L167). Bukti baris 54–60:

```php
54: 
55:         foreach (array_values($input) as $index => $tender) {
56:             $method = strtolower(trim((string) ($tender['method'] ?? '')));
57: 
58:             if (! in_array($method, self::METHODS, true)) {
59:                 throw ValidationException::withMessages([
60:                     "tenders.{$index}.method" => 'Metode pembayaran tidak valid.',
```

[app/Services/TransactionTenderService.php — TransactionTenderService::normalize(), 33–167](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/TransactionTenderService.php#L33-L167). Bukti baris 143–149:

```php
143: 
144:         $total = array_sum(array_column($normalized, 'amount'));
145: 
146:         if ($total !== $grandTotal) {
147:             throw ValidationException::withMessages([
148:                 'tenders' => 'Jumlah pembayaran harus sama dengan total transaksi.',
149:             ]);
```

**Masalah:** Long Method; Conditional Complexity. normalize 135 baris mencampur format, nominal, cash/change, rekening, batas count, total dan readiness gateway.

**Teknik dan alasan:** Extract Method; Guard Clauses yang mempertahankan urutan; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: normalizeTenderRow plus validateTenderSet; tetapkan named max count lokal hanya bila dipakai berulang.

**Invarian dan kriteria khusus selesai:** Maksimal dua metode; satu gateway/satu bank; transfer tender saat ini berstatus paid; error index dan urutan error, fallback cash_received serta total exact tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Konsumen RF-021/027; tunda perubahan massal status/payment enums.

**Pengujian wajib:** T11: 0/1/2/3 tender, total ±1, cash kurang/lebih, bank lintas outlet, dua gateway, QRIS fallback; Http::fake untuk provider. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-021"></a>

## RF-021 — Checkout execute mengorkestrasi terlalu banyak detail pembentukan transaksi

**Pemilik:** Anggota 2. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 8–14 jam-orang.

[app/Services/CheckoutService.php — CheckoutService::execute(), 31–159](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/CheckoutService.php#L31-L159). Bukti baris 90–96:

```php
90: 
91:             $transaction = Transaction::create([
92:                 'cashier_id' => $ctx->userId,
93:                 'cashier_shift_id' => $activeShift->id,
94:                 'warehouse_id' => $activeShift->warehouse_id,
95:                 'customer_id' => $ctx->customer?->id,
96:                 'invoice' => 'TRX-'.strtoupper(Str::random(10)),
```

[app/Services/CheckoutService.php — CheckoutService::execute(), 31–159](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/CheckoutService.php#L31-L159). Bukti baris 132–138:

```php
132: 
133:             if ($ctx->isPayLater) {
134:                 Receivable::create([
135:                     'customer_id' => $ctx->customer?->id,
136:                     'transaction_id' => $transaction->id,
137:                     'invoice' => $transaction->invoice,
138:                     'total' => $grandTotal,
```

**Masalah:** Long Method; Mixed Abstraction Levels. execute 129 baris mencampur lock shift, preview harga, normalisasi tender, attribute mapping, cart deletion, loyalty dan receivable.

**Teknik dan alasan:** Extract Method; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: extract buildTransactionAttributes, persistTenders dan createReceivableIfNeeded; tetap satu DB::transaction utama.

**Invarian dan kriteria khusus selesai:** Tidak memindah gateway HTTP masuk DB transaction; stock/detail/profit/cart/loyalty tetap atomik; client_uuid/fingerprint/onlyCartIds dipertahankan. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-022/023 satu service; dikerjakan berurutan oleh pemilik sama, bukan serentak.

**Pengujian wajib:** T12: cash/pay_later/split, cart kosong, stok kurang, rollback loyalty dan onlyCartIds; seluruh CheckoutServiceTest dan StockIntegrity. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-022"></a>

## RF-022 — Checkout cart processor memiliki delapan parameter dan dua jalur stok

**Pemilik:** Anggota 2. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 8–14 jam-orang.

[app/Services/CheckoutService.php — CheckoutService::processCartItems(), 161–325](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/CheckoutService.php#L161-L325). Bukti baris 160–166:

```php
160: 
161:     private function processCartItems(
162:         Transaction $transaction,
163:         $carts,
164:         array $pricingPreview,
165:         $outlet,
166:         int $subtotalAfterPromo,
```

**Masalah:** Long Parameter List; Data Clumps; Long Method. processCartItems 165 baris menerima delapan parameter termasuk outlet yang tidak dipakai dalam body; menghitung detail/profit dan inventory.

**Teknik dan alasan:** Introduce Parameter Object secukupnya; Extract Method; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: hilangkan parameter private outlet setelah seluruh callsite diperiksa; kelompokkan warehouse/user/subtotal/manualDiscount ke context pemrosesan yang bernama, atau gunakan CheckoutContext yang sudah ada; ekstrak operasi inventory tanpa menambah DTO besar kedua.

**Invarian dan kriteria khusus selesai:** Pembagian manual discount per line, cost komposit, conversion factor round, note mutation, reference dan DB lock sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-021 terlebih dulu; RF-023 menyentuh blok batch yang sama.

**Pengujian wajib:** T12: beberapa item dengan diskon pembulatan, komposit, warehouse null legacy, multi-unit; assert detail/profit/mutasi. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-023"></a>

## RF-023 — Alokasi batch FEFO persis berulang pada checkout dan dine-in

**Pemilik:** Anggota 2. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 10–16 jam-orang.

[app/Services/CheckoutService.php — CheckoutService::processCartItems(), 161–325](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/CheckoutService.php#L161-L325). Bukti baris 286–292:

```php
286:                 if ($warehouseId) {
287:                     $batches = ProductBatch::where('product_id', $product->id)
288:                         ->where('warehouse_id', $warehouseId)
289:                         ->where('stock', '>', 0)
290:                         ->where(function (Builder $q) {
291:                             $q->whereNull('expired_at')->orWhere('expired_at', '>', now());
292:                         })
```

[app/Services/DineOrderService.php — DineOrderService::accept(), 22–212](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/DineOrderService.php#L22-L212). Bukti baris 167–173:

```php
167: 
168:                     $batches = ProductBatch::where('product_id', $product->id)
169:                         ->where('warehouse_id', $warehouseId)
170:                         ->where('stock', '>', 0)
171:                         ->where(function (Builder $q) {
172:                             $q->whereNull('expired_at')->orWhere('expired_at', '>', now());
173:                         })
```

**Masalah:** Duplicate Query/Business Logic. Filter batch berlaku, lockForUpdate, cek covered, decrement, allocation rows dan firstBatchId disalin. BatchService.allocate tidak melakukan persist/lock sehingga bukan pengganti langsung.

**Teknik dan alasan:** Extract Method/Class; Move Method; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: BatchService.reserveForDetail menerima detail, product, warehouse dan baseQty di dalam transaksi caller; jangan sekadar memanggil allocate yang berbeda semantik.

**Invarian dan kriteria khusus selesai:** Urutan expired_at/received_at termasuk null mengikuti DB saat ini; batch expired dikecualikan, tanpa batch tetap boleh, batch kurang rollback seluruh sale. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Setelah RF-021/022; jangan mengubah schema/tie-break/null order bersamaan.

**Pengujian wajib:** T07/T12/T13: dua batch FEFO, expired/null expiry, no batches, insufficient batch, allocation exact dan rollback; ulangi MySQL. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-024"></a>

## RF-024 — Tambah/ubah cart web dan API menggandakan pricing serta cek stok

**Pemilik:** Anggota 2. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 10–16 jam-orang.

[app/Http/Controllers/Apps/TransactionController.php — TransactionController::addToCart(), 252–335](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/TransactionController.php#L252-L335). Bukti baris 272–278:

```php
272:         // Composite: check component stock
273:         if ($product->is_composite) {
274:             $product->load('components');
275:             foreach ($product->components as $component) {
276:                 $needed = (float) $component->pivot->qty * $validated['qty'];
277:                 $whProduct = $component->warehouses()->where('warehouse_id', $warehouseId)->first();
278:                 $avail = $whProduct?->pivot->stock ?? 0;
```

[app/Http/Controllers/Api/PosApiController.php — PosApiController::addToCart(), 269–351](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/PosApiController.php#L269-L351). Bukti baris 290–296:

```php
290: 
291:         if ($product->is_composite) {
292:             $product->load('components');
293:             foreach ($product->components as $component) {
294:                 $needed = (float) $component->pivot->qty * $validated['qty'];
295:                 $avail = (int) ($component->warehouses()->where('warehouse_id', $warehouseId)->first()?->pivot->stock ?? 0);
296:                 if ($avail < $needed) {
```

**Masalah:** Duplicate Business Logic; Large Controllers. Resolusi base unit, conversion, harga komposit, pivot stock dan upsert cart terjadi berulang. Beberapa validasi dan respons memang berbeda.

**Teknik dan alasan:** Extract Class; Parameterisasi perbedaan yang disengaja; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: CartLineResolver murni dari product/unit/qty/warehouse dan CartWriter; adapter mempertahankan aturan qty serta error masing-masing.

**Invarian dan kriteria khusus selesai:** Jangan diam-diam mengubah penggabungan item hanya per product menjadi per unit; numeric vs integer qty web/API dan error 302/422 tetap sampai bug fix terpisah. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-028 offline memakai pembuatan cart; buat kontrak sebelum menyentuh keduanya.

**Pengujian wajib:** T12: normal/composite, unit invalid, cart aktif/held, tambah berulang, stock limit, cart orang lain; bandingkan per adapter. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-025"></a>

## RF-025 — Lifecycle hold/resume berulang pada adapter POS

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 5–8 jam-orang.

[app/Http/Controllers/Apps/TransactionController.php — TransactionController::holdCart(), 434–468](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/TransactionController.php#L434-L468). Bukti baris 442–448:

```php
442:         // Get active cart items
443:         $activeCarts = Cart::where('cashier_id', $userId)
444:             ->active()
445:             ->get();
446: 
447:         if ($activeCarts->isEmpty()) {
448:             return response()->json([
```

[app/Http/Controllers/Api/PosApiController.php — PosApiController::holdCart(), 427–450](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/PosApiController.php#L427-L450). Bukti baris 433–439:

```php
433:         $userId = $request->user()->id;
434:         $activeCarts = Cart::where('cashier_id', $userId)->active()->get();
435: 
436:         if ($activeCarts->isEmpty()) {
437:             return $this->error('Keranjang kosong, tidak ada yang bisa ditahan.', 422);
438:         }
439: 
```

**Masalah:** Duplicate Query/Business Logic. Query active/held, penolakan cart kosong/aktif dan clearing hold fields diulang.

**Teknik dan alasan:** Extract Class kecil; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: HeldCartOperations untuk query/update; generator ID dapat dipasok agar web uniqid dan API random tidak disamakan tanpa keputusan.

**Invarian dan kriteria khusus selesai:** Label fallback, pola hold_id setiap adapter, batas 50, tidak menyentuh cart kasir lain, redirect vs JSON dan status tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tidak sejajar RF-024 karena file sama; boleh dipilih sesudah inti selesai.

**Pengujian wajib:** T12: hold kosong, resume saat active, holdId asing, resume ulang, delete hold tidak ada dan label kosong. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-026"></a>

## RF-026 — Pembuatan discount approval setelah checkout berulang

**Pemilik:** Anggota 2. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 5–8 jam-orang.

[app/Http/Controllers/Apps/TransactionController.php — TransactionController::store(), 594–733](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/TransactionController.php#L594-L733). Bukti baris 666–672:

```php
666: 
667:         if ($result->needsDiscountApproval) {
668:             $result->transaction->update([
669:                 'discount_approval_status' => 'pending',
670:                 'payment_status' => 'pending_approval',
671:             ]);
672: 
```

[app/Http/Controllers/Api/PosApiController.php — PosApiController::checkout(), 525–705](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/PosApiController.php#L525-L705). Bukti baris 625–631:

```php
625: 
626:         // Discount approval flow
627:         if ($transaction->discount > 0 && $transaction->needsDiscountApproval()) {
628:             $transaction->update([
629:                 'discount_approval_status' => 'pending',
630:                 'payment_status' => 'pending_approval',
631:             ]);
```

**Masalah:** Duplicate Business Logic; Temporal Coupling. Update status pending_approval dan DiscountApprovalLog berada di dua controller sesudah CheckoutService.

**Teknik dan alasan:** Extract Method/Class untuk menandai approval; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: satu action markAwaitingApproval; adapter tetap menentukan redirect vs 202. Jangan memindahnya ke boundary transaksi lain dalam commit yang sama.

**Invarian dan kriteria khusus selesai:** requested_discount pada tiap jalur mengikuti nilai baseline; gateway tidak dipanggil saat pending approval; tidak mengganti threshold global/outlet. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Sesudah RF-021 agar scope service jelas; changing atomicity termasuk pekerjaan arsitektur terpisah.

**Pengujian wajib:** T11/T12: di bawah/pada threshold, pay_later/cash/bank/split; count log satu dan 202/redirect persis. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-027"></a>

## RF-027 — Pembuatan pembayaran gateway setelah checkout diduplikasi

**Pemilik:** Anggota 2. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 8–14 jam-orang.

[app/Http/Controllers/Apps/TransactionController.php — TransactionController::store(), 594–733](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/TransactionController.php#L594-L733). Bukti baris 685–691:

```php
685:         if ($useTenders) {
686:             $gatewayTenders = $result->transaction->tenders()
687:                 ->whereIn('method', [TransactionTender::METHOD_MIDTRANS, TransactionTender::METHOD_XENDIT, TransactionTender::METHOD_QRIS])
688:                 ->where('payment_status', TransactionTender::STATUS_PENDING)
689:                 ->get();
690: 
691:             try {
```

[app/Http/Controllers/Api/PosApiController.php — PosApiController::checkout(), 525–705](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/PosApiController.php#L525-L705). Bukti baris 648–654:

```php
648:         if ($useTenders) {
649:             $gatewayTenders = $transaction->tenders()
650:                 ->whereIn('method', [TransactionTender::METHOD_MIDTRANS, TransactionTender::METHOD_XENDIT, TransactionTender::METHOD_QRIS])
651:                 ->where('payment_status', TransactionTender::STATUS_PENDING)
652:                 ->get();
653: 
654:             try {
```

**Masalah:** Duplicate Code; Integration di Controller. Loop tender gateway, payload reference/url/qr dan single-tender copy diulang; catch web dan API berbeda.

**Teknik dan alasan:** Extract Class/Method; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: action membuat pembayaran dan mengembalikan hasil; adapter mempertahankan kebijakan error masing-masing, termasuk API catch Throwable dan status pending saat gagal.

**Invarian dan kriteria khusus selesai:** Jumlah request provider, order reference, amount tender, pilihan QRIS, partial success dan persisted state saat exception tetap. Jangan mengubah retry/idempotency eksternal. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Setelah RF-026; manager gateway sudah ada, jangan membuat gateway framework baru.

**Pengujian wajib:** T11: Http::fake sukses/gagal/timeout, single/split, satu tender gateway, approval; snapshot payload provider dan transaction fields. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-028"></a>

## RF-028 — Offline sync memanggil controller checkout lewat synthetic Request

**Pemilik:** Anggota 2. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 12–20 jam-orang.

[app/Http/Controllers/Api/PosApiController.php — PosApiController::syncOne(), 768–920](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/PosApiController.php#L768-L920). Bukti baris 852–858:

```php
852: 
853:             $checkoutRequest = new Request(array_filter([
854:                 'customer_id' => $payload['customer_id'] ?? null,
855:                 'customer_voucher_id' => $payload['customer_voucher_id'] ?? null,
856:                 'discount' => $payload['discount'] ?? 0,
857:                 'shipping_cost' => $payload['shipping_cost'] ?? 0,
858:                 'redeem_points' => $payload['redeem_points'] ?? 0,
```

[app/Http/Controllers/Api/PosApiController.php — PosApiController::syncOne(), 768–920](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/PosApiController.php#L768-L920). Bukti baris 872–878:

```php
872:             try {
873:                 $response = $this->checkout($checkoutRequest, app(PaymentGatewayManager::class));
874:             } catch (UniqueConstraintViolationException $e) {
875:                 // Lost a race: another worker committed the same client_uuid
876:                 // between our pre-check and this checkout. Nothing was
877:                 // committed by us (the transaction rolled back atomically).
878:                 $existing = Transaction::where('client_uuid', $uuid)->first();
```

**Masalah:** Inappropriate Coupling; HTTP/Domain tercampur. syncOne 153 baris membuat cart sementara, Request sintetis dan decode response HTTP untuk operasi internal. Kegagalan cleanup mudah terlewat.

**Teknik dan alasan:** Move Method; Extract Application Operation; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: sync payload menjadi CheckoutContext melalui mapper yang bisa diuji, eksekusi use case lalu response mapper; cleanup ditangani menurut perilaku baseline, bug cleanup diperbaiki commit berbeda.

**Invarian dan kriteria khusus selesai:** Idempotency UUID/fingerprint, status duplicate/conflict/failed/pending_approval, live carts tidak termakan dan API batch contract sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Sesudah RF-024/027; jangan mengubah semantik retry dan race handling bersamaan.

**Pengujian wajib:** T15: exact retry, payload berbeda UUID sama, stock fail, shift tertutup, live cart existing dan per-item partial failure; assert temporary carts. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-029"></a>

## RF-029 — Sinkronisasi offline tertanam di komponen POS

**Pemilik:** Anggota 3. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 8–14 jam-orang.

[resources/js/Pages/Dashboard/Transactions/Index.jsx — Index/flushPendingTransactions, konteks 53–2081](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Transactions/Index.jsx#L53-L2081). Bukti baris 470–476:

```jsx
470: 
471:     const flushPendingTransactions = useCallback(async () => {
472:         if (!navigator.onLine) return;
473:         if (flushPromiseRef.current) return flushPromiseRef.current;
474: 
475:         flushPromiseRef.current = (async () => {
476:             const pending = await getPendingTransactions(offlineScopeKey);
```

[resources/js/Utils/offlineDb.js — getPendingTransactions, konteks 99–118](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Utils/offlineDb.js#L99-L118). Bukti baris 101–107:

```javascript
101:     const rows = await db.getAll("pending_transactions");
102:     const staleSyncCutoff = Date.now() - 5 * 60 * 1000;
103: 
104:     return rows
105:         .map(normalizeQueueRecord)
106:         .map((row) => {
107:             const lastAttempt = Date.parse(row.last_attempt_at || "");
```

**Masalah:** Large Component; Side-effect Coupling. IndexedDB, retry, status per record, HTTP batching, toast dan mount/reconnect listener berada di UI transaksi.

**Teknik dan alasan:** Extract Custom Hook dan pure result reducer; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: useOfflineTransactions menerima scopeKey serta transport; pisahkan mapping hasil sync untuk unit test, ref guard tetap pada instance hook.

**Invarian dan kriteria khusus selesai:** Failed retryable, conflict tidak auto-retry, stale syncing 5 menit, legacy null-scope terlihat; jangan memperbaiki scope key/cleanup bersama refactor. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-030 menyentuh page sama; tunda sampai bug setCarts dan key scope diputuskan terpisah.

**Pengujian wajib:** T15: offline/online, concurrent flush, hasil kurang, HTTP fail, conflict, legacy queue dan unmount. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-030"></a>

## RF-030 — Halaman POS menggabungkan shift, cart, payment dan UI responsif

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang–tinggi. **Estimasi:** 10–16 jam-orang.

[resources/js/Pages/Dashboard/Transactions/Index.jsx — Index, konteks 53–2081](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Transactions/Index.jsx#L53-L2081). Bukti baris 52–58:

```jsx
52: 
53: export default function Index({
54:     carts = [],
55:     carts_total = 0,
56:     heldCarts = [],
57:     customers = [],
58:     products = [],
```

[resources/js/Pages/Dashboard/Transactions/Index.jsx — Index, konteks 53–2081](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Transactions/Index.jsx#L53-L2081). Bukti baris 862–868:

```jsx
862: 
863:     if (!activeCashierShift) {
864:         return (
865:             <>
866:                 <Head title="Buka Shift Kasir" />
867: 
868:                 <div className="mx-auto flex min-h-[calc(100vh-8rem)] max-w-3xl items-center justify-center px-4 py-10">
```

**Masalah:** Large Component; Low Cohesion. 2083 baris dengan state shift, scanner, cart, promo, split, offline dan responsive markup; tracing perubahan UI sulit.

**Teknik dan alasan:** Extract Component bertahap; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN tahap kecil: OpenShiftPanel lalu PaymentSummary; parent tetap mengelola state/submit. PaymentPanel lama tidak otomatis dipakai karena kontraknya berbeda; bukan rewrite seluruh POS.

**Invarian dan kriteria khusus selesai:** Form shift, order_type/shipping reset, keyboard shortcut, status tombol dan tampilan mobile/desktop tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Jangan paralel RF-029/031/032 pada file POS; RF-032 inti membatasi edit hanya formatter.

**Pengujian wajib:** T12/T03: tanpa shift, buka shift, cart update, hold, split, delivery, mobile viewport dan hotkey; screenshot baseline tiap subpanel. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-031"></a>

## RF-031 — Payload checkout online/offline dan reset tersebar

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang–tinggi. **Estimasi:** 5–8 jam-orang.

[resources/js/Pages/Dashboard/Transactions/Index.jsx — Index/handleSubmitTransaction, konteks 53–2081](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Transactions/Index.jsx#L53-L2081). Bukti baris 739–745:

```jsx
739:         if (!navigator.onLine) {
740:             const payload = {
741:                 client_uuid: crypto.randomUUID(),
742:                 customer_id: selectedCustomer?.id ?? null,
743:                 discount,
744:                 redeem_points: Number(redeemPointsInput || 0),
745:                 customer_voucher_id: selectedVoucherId || null,
```

[resources/js/Pages/Dashboard/Transactions/Index.jsx — Index/handleSubmitTransaction, konteks 53–2081](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Transactions/Index.jsx#L53-L2081). Bukti baris 778–784:

```jsx
778:         router.post(
779:             route("transactions.store"),
780:             {
781:                 customer_id: selectedCustomer?.id ?? null,
782:                 discount,
783:                 redeem_points: Number(redeemPointsInput || 0),
784:                 customer_voucher_id: selectedVoucherId || null,
```

**Masalah:** Duplicate Data Mapping; Conditional Complexity. Payload memiliki banyak field sama, ternary metode bertingkat, tetapi channel offline/online sengaja berbeda.

**Teknik dan alasan:** Extract Function; Decompose Conditional; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: buildCommonCheckoutFields dan adapter buildOnline/buildOffline; resetPaymentForm kecil. Jangan mengganti seluruh state menjadi reducer sekaligus.

**Invarian dan kriteria khusus selesai:** Offline menyimpan items+UUID dan menolak split; online tenders[] serta payment_gateway tetap; null/undefined/angka dan field grand_total tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Sesudah BF-001 diputuskan; RF-029 dan RF-030 harus diintegrasikan lebih dulu jika ikut dikerjakan.

**Pengujian wajib:** T12/T15: payload snapshot cash/bank/pay_later/split, customer null, note kosong, shipping0 dan online success/error reset. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-032"></a>

## RF-032 — Formatter Rupiah identik tersebar pada halaman transaksi

**Pemilik:** Anggota 3. **Scope:** Inti. **Prioritas:** P1. **Risiko:** rendah. **Estimasi:** 3–5 jam-orang.

[resources/js/Pages/Dashboard/Transactions/Index.jsx — formatPrice, konteks 43–48](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Transactions/Index.jsx#L43-L48). Bukti baris 42–48:

```jsx
42: 
43: const formatPrice = (value = 0) =>
44:     Number(value || 0).toLocaleString("id-ID", {
45:         style: "currency",
46:         currency: "IDR",
47:         minimumFractionDigits: 0,
48:     });
```

[resources/js/Pages/Dashboard/Transactions/Print.jsx — Print/formatPrice, konteks 23–1045](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Transactions/Print.jsx#L23-L1045). Bukti baris 60–66:

```jsx
60: 
61:     const formatPrice = (price = 0) =>
62:         Number(price || 0).toLocaleString("id-ID", {
63:             style: "currency",
64:             currency: "IDR",
65:             minimumFractionDigits: 0,
66:         });
```

**Masalah:** Duplicate Transformation; Shotgun Surgery. Kedua fungsi memakai Number(value||0), locale id-ID, currency IDR dan nol digit desimal; perubahan format harus diulang.

**Teknik dan alasan:** Extract Function; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: formatRupiah utility murni dipakai dulu oleh dua lokasi identik ini; varian format lain di laporan tidak dipaksa sama.

**Invarian dan kriteria khusus selesai:** Termasuk nonbreaking space Intl, coercion nilai kosong, rounding dan default=0; locale tidak diubah mengikuti i18n. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-002 terpisah file. Pemilik tunggal POS/Print utility adalah Anggota 3; jangan bersamaan RF-030.

**Pengujian wajib:** T16: exact string 0/null/undefined/negatif/4496/pecahan/string numerik; browser sama sebelum/sesudah. UI total dan kembalian tetap; node --test baru masih rencana. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-033"></a>

## RF-033 — Halaman invoice menggabungkan polling, print dan rendering

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang–tinggi. **Estimasi:** 8–12 jam-orang.

[resources/js/Pages/Dashboard/Transactions/Print.jsx — Print, konteks 23–1045](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Transactions/Print.jsx#L23-L1045). Bukti baris 36–42:

```jsx
36: 
37:     // Poll QRIS payment status every 4s while pending
38:     useEffect(() => {
39:         if (!showQris) return undefined;
40:         const timer = setInterval(async () => {
41:             try {
42:                 const res = await fetch(
```

[resources/js/Pages/Dashboard/Transactions/Print.jsx — Print/handleThermalPrint, konteks 23–1045](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Transactions/Print.jsx#L23-L1045). Bukti baris 237–243:

```jsx
237: 
238:     const handleThermalPrint = async () => {
239:         const paperSize = printMode === "thermal58" ? "58mm" : "80mm";
240:         const fallbackWindow = window.open("", "_blank", "width=400,height=600");
241: 
242:         try {
243:             await printReceipt(receiptData, paperSize);
```

**Masalah:** Large Component; Divergent Change. 1045 baris menggabungkan status QRIS 4 detik, konfirmasi, receipt projection, ESC/POS, popup fallback dan invoice JSX.

**Teknik dan alasan:** Extract Hook; Extract Component; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: usePaymentStatusPolling, useReceiptPrinting dan InvoiceBody; polling dan auto-print diuji terpisah.

**Invarian dan kriteria khusus selesai:** Hanya poll saat QR pending, reload ketika paid, auto-print tidak membuka popup, cash drawer hanya cash; dependency effect dipertahankan dulu. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-032 selesai dahulu; RF-034 menyentuh data struk yang sama.

**Pengujian wajib:** T11/T17: pending→paid, transient network error, unmount, printer sukses/gagal, popup blocked, print sekali/berulang baseline. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-034"></a>

## RF-034 — Proyeksi struk dan label pembayaran tersebar lintas renderer

**Pemilik:** Anggota 3. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 10–18 jam-orang.

[resources/js/Pages/Dashboard/Transactions/Print.jsx — Print/receiptData, konteks 23–1045](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Transactions/Print.jsx#L23-L1045). Bukti baris 164–170:

```jsx
164: 
165:     const receiptData = useMemo(() => {
166:         const orderTypeLabels = {
167:             in_store: "Di Tempat",
168:             takeaway: "Bawa Pulang",
169:             delivery: "Diantar",
170:         };
```

[resources/js/Components/Receipt/ThermalReceipt.jsx — ThermalReceipt, konteks 13–275](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Components/Receipt/ThermalReceipt.jsx#L13-L275). Bukti baris 59–65:

```jsx
59: 
60:     const paymentLabels = {
61:         cash: "TUNAI",
62:         bank_transfer: "TRANSFER BANK",
63:         midtrans: "MIDTRANS",
64:         xendit: "XENDIT",
65:         qris: "QRIS",
```

[app/Services/ThermalPrintService.php — ThermalPrintService::generateReceiptText(), 12–102](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/ThermalPrintService.php#L12-L102). Bukti baris 48–54:

```php
48:         $lines[] = $this->line($maxWidth);
49:         $subtotal = ($transaction->grand_total ?? 0) + ($transaction->discount ?? 0) - ($transaction->shipping_cost ?? 0) - ($transaction->tax_total ?? 0);
50:         $lines[] = $this->leftRight('Subtotal', number_format($subtotal, 0, ',', '.'), $maxWidth);
51:         if (($transaction->discount ?? 0) > 0) {
52:             $lines[] = $this->leftRight('Diskon', '-'.number_format((int) $transaction->discount, 0, ',', '.'), $maxWidth);
53:         }
54:         if (($transaction->tax_total ?? 0) > 0) {
```

**Masalah:** Duplicate Domain Projection; Inconsistent Abstraction. React invoice/thermal, ESC/POS dan PHP receipt mengonstruksi subtotal/discount/payment sendiri. Rumus yang berbeda harus dibedakan dari duplikasi murni.

**Teknik dan alasan:** Extract pure receipt projection per runtime; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: extract shared projection hanya untuk renderer yang benar-benar setara; pertahankan perbedaan dulu. Formula subtotal yang salah masuk bug fix, bukan disamakan otomatis.

**Invarian dan kriteria khusus selesai:** Isi dan urutan cetak, width32/48, label, promo/loyalty/voucher/tax/shipping, fallback teks dan escaping identik per renderer. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tunda sampai kontrak receipt disepakati; RF-033/036 berkaitan. Tidak berbagi kode PHP dengan JS lewat abstraksi baru tanpa manfaat.

**Pengujian wajib:** T17: golden HTML/text/bytes, promo+voucher+redeem, split, panjang nama, karakter nonASCII dan width58/80. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-035"></a>

## RF-035 — Store profile/normalisasi URL logo dirakit ulang

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 4–7 jam-orang.

[app/Http/Controllers/DocumentController.php — DocumentController::storeProfile(), 36–66](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/DocumentController.php#L36-L66). Bukti baris 37–43:

```php
37:     {
38:         $logo = Setting::getForOutlet('store_logo', $outlet);
39:         if ($logo && ! str_starts_with($logo, 'http') && ! str_starts_with($logo, '/storage')) {
40:             $logo = asset('storage/'.ltrim($logo, '/'));
41:         }
42: 
43:         $logoData = null;
```

[app/Http/Middleware/HandleInertiaRequests.php — HandleInertiaRequests::share(), 33–290](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Middleware/HandleInertiaRequests.php#L33-L290). Bukti baris 226–232:

```php
226:         if (Schema::hasTable('settings')) {
227:             $logo = Setting::getForOutlet('store_logo', $outlet);
228:             if ($logo && ! str_starts_with($logo, 'http') && ! str_starts_with($logo, '/storage')) {
229:                 $logo = asset('storage/'.ltrim($logo, '/'));
230:             }
231: 
232:             $storeProfile = [
```

**Masalah:** Duplicate Transformation. Default toko dan URL logo berulang pada dokumen dan shared props; Document menambah logo_data yang khusus PDF.

**Teknik dan alasan:** Extract Method/Class; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: StoreProfileReader untuk field umum; Document menambahkan pembacaan local image base64 sendiri.

**Invarian dan kriteria khusus selesai:** Outlet dari transaksi vs active request tetap berbeda; default nama/field, http vs storage path, MIME behavior dan key city yang khusus middleware tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-044 memecah share; koordinasi pemilik sama, bukan prerequisite inti.

**Pengujian wajib:** T18/T17: global/outlet override, logo absolute/relative/null/file missing, guest serta private/public invoice. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-036"></a>

## RF-036 — Dua view receipt PDF menggandakan perhitungan dan markup

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 5–8 jam-orang.

[resources/views/pdf/receipt_58.blade.php — Blade receipt_58, konteks 32–39](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/views/pdf/receipt_58.blade.php#L32-L39). Bukti baris 32–38:

```php
32:             <span>No:</span>
33:             <span>{{ $transaction->invoice }}</span>
34:         </div>
35:         <div style="display:flex; justify-content:space-between;">
36:             <span>Tgl:</span>
37:             <span>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}</span>
38:         </div>
```

[resources/views/pdf/receipt_80.blade.php — Blade receipt_80, konteks 46–53](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/views/pdf/receipt_80.blade.php#L46-L53). Bukti baris 46–52:

```php
46:             <span>{{ $l('receipt.transaction_no') }}:</span>
47:             <span>{{ $transaction->invoice }}</span>
48:         </div>
49:         <div style="display:flex; justify-content:space-between;">
50:             <span>{{ $l('receipt.transaction_date') }}:</span>
51:             <span>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y H:i') }}</span>
52:         </div>
```

**Masalah:** Duplicate View/Presentation Logic. Lebar kertas berbeda tetapi banyak konten struk sama; tax/promo dan footer rawan berbeda saat diperbarui.

**Teknik dan alasan:** Extract Blade Partial; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: partial isi bersama dengan konfigurasi ukuran/font; pertahankan CSS spesifik 58 dan 80 secara eksplisit.

**Invarian dan kriteria khusus selesai:** Ukuran164.4/226.8pt, paper height, teks, escaping dan pemenggalan baris per ukuran tidak berubah. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Setelah RF-034 kontrak konten jelas; jangan menyamakan rumus yang berbeda tanpa bug ticket.

**Pengujian wajib:** T17: render PDF kedua ukuran dengan data sama, bandingkan text extraction dan screenshot page, panjang nama/alamat dan multi-item. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-037"></a>

## RF-037 — View hutang/piutang PDF menggandakan struktur dan CSS

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah–sedang. **Estimasi:** 3–5 jam-orang.

[resources/views/pdf/payable.blade.php — Blade payable, konteks 4–11](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/views/pdf/payable.blade.php#L4-L11). Bukti baris 4–10:

```php
4:     <meta charset="UTF-8">
5:     <style>
6:         @page { margin: 10mm; }
7:         body { font-family: 'Inter', 'Helvetica', 'Arial', sans-serif; margin:0; background:#f8fafc; color:#0f172a; }
8:         .sheet { max-width: 900px; margin: 0 auto; background:white; border:1px solid #e2e8f0; border-radius:14px; padding:18px 20px; }
9:         .header { display:flex; justify-content: space-between; align-items: flex-start; gap:16px; border-bottom:1px solid #e2e8f0; padding-bottom:12px; }
10:         .store { display:flex; gap:12px; align-items:center; flex:1; min-width:0; }
```

[resources/views/pdf/receivable.blade.php — Blade receivable, konteks 4–11](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/views/pdf/receivable.blade.php#L4-L11). Bukti baris 4–10:

```php
4:     <meta charset="UTF-8">
5:     <style>
6:         @page { margin: 10mm; }
7:         body { font-family: 'Inter', 'Helvetica', 'Arial', sans-serif; margin:0; background:#f8fafc; color:#0f172a; }
8:         .sheet { max-width: 900px; margin: 0 auto; background:white; border:1px solid #e2e8f0; border-radius:14px; padding:18px 20px; }
9:         .header { display:flex; justify-content: space-between; align-items: flex-start; gap:16px; border-bottom:1px solid #e2e8f0; padding-bottom:12px; }
10:         .store { display:flex; gap:12px; align-items:center; flex:1; min-width:0; }
```

**Masalah:** Duplicate CSS/Markup. jscpd mengonfirmasi clone styling dan tabel pembayaran; perubahan dokumen dilakukan berpasangan.

**Teknik dan alasan:** Extract Blade Partial; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: partial style/header/payment-table dengan label supplier/customer dan nomor invoice masing-masing.

**Invarian dan kriteria khusus selesai:** A5 portrait, kolom/rekening yang ditampilkan, format tanggal dan status per dokumen tetap; jangan mengganti semua CSS proyek. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Dapat mandiri; Anggota 3 pemilik semua view agar tidak konflik RF-036.

**Pengujian wajib:** T17: zero/partial/full payments, long party name, bank null; QA PDF kedua jenis. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-038"></a>

## RF-038 — Filter laporan penjualan dan profit identik

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah–sedang. **Estimasi:** 4–6 jam-orang.

[app/Http/Controllers/Reports/SalesReportController.php — SalesReportController::applyFilters(), 103–112](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Reports/SalesReportController.php#L103-L112). Bukti baris 104–110:

```php
104:     {
105:         return $query->when($warehouseIds !== null, fn ($q) => $q->whereIn('warehouse_id', $warehouseIds))
106:             ->when($filters['invoice'] ?? null, fn ($q, $invoice) => $q->where('invoice', 'like', '%'.$invoice.'%'))
107:             ->when($filters['cashier_id'] ?? null, fn ($q, $cashier) => $q->where('cashier_id', $cashier))
108:             ->when($filters['customer_id'] ?? null, fn ($q, $customer) => $q->where('customer_id', $customer))
109:             ->when($filters['start_date'] ?? null, fn ($q, $start) => $q->whereDate('created_at', '>=', $start))
110:             ->when($filters['end_date'] ?? null, fn ($q, $end) => $q->whereDate('created_at', '<=', $end))
```

[app/Http/Controllers/Reports/ProfitReportController.php — ProfitReportController::applyFilters(), 93–102](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Reports/ProfitReportController.php#L93-L102). Bukti baris 94–100:

```php
94:     {
95:         return $query->when($warehouseIds !== null, fn ($q) => $q->whereIn('warehouse_id', $warehouseIds))
96:             ->when($filters['invoice'] ?? null, fn ($q, $invoice) => $q->where('invoice', 'like', '%'.$invoice.'%'))
97:             ->when($filters['cashier_id'] ?? null, fn ($q, $cashier) => $q->where('cashier_id', $cashier))
98:             ->when($filters['customer_id'] ?? null, fn ($q, $customer) => $q->where('customer_id', $customer))
99:             ->when($filters['start_date'] ?? null, fn ($q, $start) => $q->whereDate('created_at', '>=', $start))
100:             ->when($filters['end_date'] ?? null, fn ($q, $end) => $q->whereDate('created_at', '<=', $end))
```

**Masalah:** Duplicate Query; Data Clumps. Enam filter dan resolusi allowed warehouses sama; query aggregate laporan berbeda dan sebaiknya tetap.

**Teknik dan alasan:** Extract Function/Query Scope; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: TransactionReportFilters menerima query, filter array dan IDs; adapter laporan menjaga query serta summary sendiri.

**Invarian dan kriteria khusus selesai:** Range tanggal inklusif, invalid requested warehouse diabaikan seperti baseline, user/outlet scope, paginate10 dan sort desc sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-039 dapat setelah ini; tidak memaksa filter history berbeda bernama date_from menjadi sama.

**Pengujian wajib:** T14: tanggal batas, invoice/cashier/customer, warehouse tak diizinkan, empty result dan API/web props. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-039"></a>

## RF-039 — Insights controller mencampur metrik sales, inventory, promo, loyalty dan CRM

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang–tinggi. **Estimasi:** 10–18 jam-orang.

[app/Http/Controllers/Reports/AdvancedSalesInsightsController.php — AdvancedSalesInsightsController::reportData(), 40–124](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Reports/AdvancedSalesInsightsController.php#L40-L124). Bukti baris 90–96:

```php
90:         $repeatCustomerMetrics = $this->repeatCustomerMetrics($filters);
91:         $stockCoverage = $this->stockCoverageAnalysis($filters);
92:         $promoMonitor = $this->promoMonitor($activeOutlet?->id);
93:         $loyaltyPerformance = $this->loyaltyPerformance($filters, $activeOutlet?->id);
94:         $crmOperations = $this->crmOperations($filters, $activeOutlet?->id);
95: 
96:         return [
```

[app/Http/Controllers/Reports/AdvancedSalesInsightsController.php — AdvancedSalesInsightsController::stockCoverageAnalysis(), 495–592](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Reports/AdvancedSalesInsightsController.php#L495-L592). Bukti baris 498–504:

```php
498: 
499:         $salesSubquery = $this->detailMetricsQuery($filters)
500:             ->selectRaw('
501:                 td.product_id,
502:                 SUM(td.qty) as qty_sold,
503:                 SUM(td.price) as revenue_total,
504:                 MAX(t.created_at) as last_sold_at
```

**Masalah:** Large Class; Divergent Change. 841 baris dengan query SQL, kebijakan legacy/outlet dan serializer lima domain. Perubahan domain berdampak pada laporan tunggal.

**Teknik dan alasan:** Extract Class bertahap; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: ambil satu kelompok misalnya StockCoverageQuery lalu LoyaltyMetricsQuery; reportData tetap orkestrasinya. Tidak memindah semua query sekaligus.

**Invarian dan kriteria khusus selesai:** Field props camelCase, ranking/limits, rumus margin, window tanggal, query DB khusus dan permission tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-040 query helper sebaiknya lebih dahulu bila dipilih; belum ada benchmark untuk optimasi query.

**Pengujian wajib:** T14: AdvancedSalesInsightsTest dan V1DashboardApiTest; fixtures multi-outlet, legacy, nol omzet, produk tidak bergerak dan waktu dibekukan. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-040"></a>

## RF-040 — Insights mengulang subquery subtotal dan ekspresi margin

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 5–8 jam-orang.

[app/Http/Controllers/Reports/AdvancedSalesInsightsController.php — AdvancedSalesInsightsController::topSellingProducts(), 171–210](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Reports/AdvancedSalesInsightsController.php#L171-L210). Bukti baris 182–188:

```php
182:                 SUM(td.price) as revenue_total,
183:                 SUM((td.price - ROUND((COALESCE(t.discount, 0) * td.price) / NULLIF(tx.subtotal_after_promo, 0))) - (p.buy_price * td.qty)) as profit_total,
184:                 MAX(t.created_at) as last_sold_at
185:             ')
186:             ->joinSub(
187:                 DB::table('transaction_details')
188:                     ->selectRaw('transaction_id, SUM(price) as subtotal_after_promo')
```

[app/Http/Controllers/Reports/AdvancedSalesInsightsController.php — AdvancedSalesInsightsController::lowPerformingProducts(), 212–272](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Reports/AdvancedSalesInsightsController.php#L212-L272). Bukti baris 218–224:

```php
218:                 SUM(td.price) as revenue_total,
219:                 SUM((td.price - ROUND((COALESCE(t.discount, 0) * td.price) / NULLIF(tx.subtotal_after_promo, 0))) - (p.buy_price * td.qty)) as profit_total,
220:                 MAX(t.created_at) as last_sold_at
221:             ')
222:             ->joinSub(
223:                 DB::table('transaction_details')
224:                     ->selectRaw('transaction_id, SUM(price) as subtotal_after_promo')
```

**Masalah:** Duplicate SQL; Shotgun Surgery. Perhitungan margin dan join subtotal transaction berulang di beberapa metric; perubahan formula mudah tidak konsisten.

**Teknik dan alasan:** Extract Method; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: subtotalSubquery dan marginExpression private yang dipakai beberapa metric; input alias ditentukan internal, bukan SQL bebas dari user.

**Invarian dan kriteria khusus selesai:** SQL ROUND/NULLIF/COALESCE, join type, NULL hasil dan group/order tetap; mengganti HPP sekarang dengan histori cost adalah perubahan perilaku. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-039 sesudah ini agar pemindahan class kecil; formula bug terpisah.

**Pengujian wajib:** T14: diskon teralokasi pecahan, subtotal nol, product cost berubah, multi-unit; bandingkan angka baseline di SQLite dan MySQL. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-041"></a>

## RF-041 — Insights React mencampur banyak panel laporan

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 8–12 jam-orang.

[resources/js/Pages/Dashboard/Reports/Insights.jsx — Insights, konteks 169–1298](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Reports/Insights.jsx#L169-L1298). Bukti baris 168–174:

```jsx
168: 
169: export default function Insights({
170:     filters,
171:     cashiers,
172:     customers,
173:     categories,
174:     summary,
```

[resources/js/Pages/Dashboard/Reports/Insights.jsx — Insights, konteks 169–1298](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Reports/Insights.jsx#L169-L1298). Bukti baris 184–190:

```jsx
184:     promoMonitor,
185:     loyaltyPerformance,
186:     crmOperations,
187: }) {
188:     const [showFilters, setShowFilters] = useState(false);
189:     const [marginView, setMarginView] = useState("product");
190:     const [filterData, setFilterData] = useState({
```

**Masalah:** Large Component; Long Parameter List props. 1300 baris dan banyak section domain; perubahan satu panel harus menavigasi seluruh halaman.

**Teknik dan alasan:** Extract Component; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: SalesTrendSection, StockCoverageSection, PromoLoyaltySection, CrmSection; bukan satu komponen generik dengan banyak flags.

**Invarian dan kriteria khusus selesai:** Urutan panel, filter state, table empty states, format angka dan chart config tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-042 memindah efek chart pada file yang sama; dikerjakan berurutan.

**Pengujian wajib:** T14 UI: data kosong/penuh, filter/reset, panel margin product/category dan mobile; bandingkan screenshot. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-042"></a>

## RF-042 — Lifecycle Chart.js berulang pada dua chart insights

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah–sedang. **Estimasi:** 3–5 jam-orang.

[resources/js/Pages/Dashboard/Reports/Insights.jsx — Insights/useEffect chart, konteks 169–1298](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Reports/Insights.jsx#L169-L1298). Bukti baris 241–247:

```jsx
241: 
242:         salesHourChart.current = new Chart(salesHourChartRef.current, {
243:             type: "bar",
244:             data: {
245:                 labels: hourChartData.map((item) => item.label),
246:                 datasets: [
247:                     {
```

[resources/js/Pages/Dashboard/Reports/Insights.jsx — Insights/useEffect chart, konteks 169–1298](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Reports/Insights.jsx#L169-L1298). Bukti baris 277–283:

```jsx
277: 
278:         salesDayChart.current = new Chart(salesDayChartRef.current, {
279:             type: "line",
280:             data: {
281:                 labels: dayChartData.map((item) => item.label),
282:                 datasets: [
283:                     {
```

**Masalah:** Duplicate Side-effect Pattern. Destroy, ref null-check, create chart dan cleanup diulang; detail bar vs line berbeda.

**Teknik dan alasan:** Extract Custom Hook; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: useChart(canvasRef,data,config) dengan config stabil; jangan mengubah dependency menyebabkan recreate tambahan.

**Invarian dan kriteria khusus selesai:** Chart lama selalu destroy, data kosong tidak create, label/warna/tension dan unit omzet sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-041 lebih dulu atau satu PR kecil untuk hook sebelum memecah section.

**Pengujian wajib:** T14: mock Chart constructor/destroy bila tooling ditambahkan; manual ganti filter berkali-kali dan navigate/unmount tanpa canvas leak. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-043"></a>

## RF-043 — Dashboard index melakukan seluruh query dan perakitan widget

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 6–10 jam-orang.

[app/Http/Controllers/DashboardController.php — DashboardController::index(), 22–248](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/DashboardController.php#L22-L248). Bukti baris 23–29:

```php
23:     {
24:         $warehouseIds = $outletAccessService->warehousesFor(request()->user())->pluck('id');
25:         $activeOutlet = $outletAccessService->activeOutlet(request());
26:         if ($activeOutlet) {
27:             $warehouseIds = $outletAccessService->warehousesFor(request()->user())
28:                 ->where('outlet_id', $activeOutlet->id)
29:                 ->pluck('id');
```

[app/Http/Controllers/DashboardController.php — DashboardController::index(), 22–248](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/DashboardController.php#L22-L248). Bukti baris 218–224:

```php
218: 
219:         return Inertia::render('Dashboard/Index', [
220:             'totalCategories' => $totalCategories,
221:             'totalProducts' => $totalProducts,
222:             'totalTransactions' => $totalTransactions,
223:             'totalCustomers' => $totalCustomers,
224:             'revenueTrend' => $revenueTrend,
```

**Masalah:** Long Method; Low Cohesion. index 227 baris menyiapkan summary, chart dan notifikasi onboarding dalam satu controller.

**Teknik dan alasan:** Extract Method/Query Class; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: pisah salesSummary, trendData dan setupChecklist; pertahankan satu orchestration sederhana, jangan repository generik.

**Invarian dan kriteria khusus selesai:** Periode tanggal, outlet aktif, active warehouse, default target serta shape semua props tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tidak berbagi file dengan RF-038; bisa backlog mandiri.

**Pengujian wajib:** T14/T18: dashboard setup selesai/belum, data kosong, dua outlet dan batas bulan/tahun; DashboardChecklistTest/V1DashboardApiTest. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-044"></a>

## RF-044 — Inertia share menggabungkan query notifikasi dan konfigurasi global

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang–tinggi. **Estimasi:** 8–14 jam-orang.

[app/Http/Middleware/HandleInertiaRequests.php — HandleInertiaRequests::share(), 33–290](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Middleware/HandleInertiaRequests.php#L33-L290). Bukti baris 85–91:

```php
85: 
86:             $lowStockNotifications = Product::query()
87:                 ->leftJoinSub(
88:                     DB::table('product_warehouse')
89:                         ->select('product_id', DB::raw('SUM(stock) as warehouse_stock'))
90:                         ->whereIn('warehouse_id', $warehouseIds)
91:                         ->groupBy('product_id'),
```

[app/Http/Middleware/HandleInertiaRequests.php — HandleInertiaRequests::share(), 33–290](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Middleware/HandleInertiaRequests.php#L33-L290). Bukti baris 115–121:

```php
115:                 ->map(function ($product) {
116:                     return [
117:                         'id' => $product->id,
118:                         'title' => $product->title,
119:                         'stock' => (int) $product->warehouse_stock,
120:                         'time' => optional($product->updated_at)->diffForHumans(),
121:                     ];
```

**Masalah:** Long Method; Mixed Responsibilities. share 258 baris mengurus lima jenis notifikasi, aging, shift, outlet, toko, printer, keamanan, locale dan auth.

**Teknik dan alasan:** Extract Method/Class; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: ekstrak NotificationPayloadBuilder dan StoreProfileReader, gunakan DI; jangan mengubah menjadi lazy/deferred props tanpa mengaudit timing dan biaya request.

**Invarian dan kriteria khusus selesai:** Semua shared keys, empty defaults guest, read-notification filter, accessible outlets, legacy stock fallback, shift lock dan step-up timestamp sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-035 berhubungan; menambah caching/lazy props adalah optimasi terpisah dengan bukti benchmark.

**Pengujian wajib:** T18: HandleInertiaRequestsTest untuk guest/super/cashier, notifications read/expired, empty allowed warehouse, shift aktif dan settings table belum tersedia. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-045"></a>

## RF-045 — Update profil toko/printer/WhatsApp diduplikasi web/API

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 6–10 jam-orang.

[app/Http/Controllers/Apps/SettingController.php — SettingController::updateStoreProfile(), 85–153](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/SettingController.php#L85-L153). Bukti baris 87–93:

```php
87:         $outlet = $this->outletAccessService->activeOutlet($request);
88:         $request->validate([
89:             'store_name' => 'required|string|max:255',
90:             'store_address' => 'required|string|max:500',
91:             'store_phone' => 'nullable|string|max:50',
92:             'store_email' => 'nullable|email|max:255',
93:             'store_website' => 'nullable|string|max:255',
```

[app/Http/Controllers/Api/V1/SettingController.php — SettingController::updateStoreProfile(), 78–146](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/SettingController.php#L78-L146). Bukti baris 80–86:

```php
80:         $outlet = $this->outletAccessService->activeOutlet($request);
81:         $request->validate([
82:             'store_name' => 'required|string|max:255',
83:             'store_address' => 'required|string|max:500',
84:             'store_phone' => 'nullable|string|max:50',
85:             'store_email' => 'nullable|email|max:255',
86:             'store_website' => 'nullable|string|max:255',
```

**Masalah:** Duplicate Validation/Persistence; Divergent Change. SettingController merangkap target, toko, printer, loyalty, WA; API menyalin transformasi payload dan file logo.

**Teknik dan alasan:** Extract Actions menurut operasi; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: UpdateStoreProfileAction dan rules provider printer/WA; adapter tetap menentukan response. Ekstrak satu operasi per commit.

**Invarian dan kriteria khusus selesai:** Setting global vs outlet, upload/delete logo dan keys string tetap; enable bool dan fallback URL tidak diubah. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-035 profile reader terpisah; Anggota 1 satu pemilik controller ini.

**Pengujian wajib:** T18/T19: outlet override vs global, file invalid, logo diganti, printer paper invalid, WA URL kosong/aktif dan permission. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-046"></a>

## RF-046 — Update konfigurasi payment mencampur secret, readiness dan audit

**Pemilik:** Anggota 1. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 8–14 jam-orang.

[app/Http/Controllers/Apps/PaymentSettingController.php — PaymentSettingController::update(), 82–217](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/PaymentSettingController.php#L82-L217). Bukti baris 105–111:

```php
105:         $xenditEnabled = (bool) ($data['xendit_enabled'] ?? false);
106:         $resolvedMidtransServerKey = $setting->secretManagedByEnvironment('midtrans_server_key')
107:             ? $setting->resolvedSecret('midtrans_server_key')
108:             : ($data['midtrans_server_key'] ?: $setting->getAttributeValue('midtrans_server_key'));
109:         $resolvedXenditSecretKey = $setting->secretManagedByEnvironment('xendit_secret_key')
110:             ? $setting->resolvedSecret('xendit_secret_key')
111:             : ($data['xendit_secret_key'] ?: $setting->getAttributeValue('xendit_secret_key'));
```

[app/Http/Controllers/Api/V1/PaymentSettingController.php — PaymentSettingController::updatePaymentSettings(), 66–189](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/PaymentSettingController.php#L66-L189). Bukti baris 89–95:

```php
89:         $xenditEnabled = (bool) ($data['xendit_enabled'] ?? false);
90:         $resolvedMidtransServerKey = $setting->secretManagedByEnvironment('midtrans_server_key')
91:             ? $setting->resolvedSecret('midtrans_server_key')
92:             : (($data['midtrans_server_key'] ?? null) ?: $setting->getAttributeValue('midtrans_server_key'));
93:         $resolvedXenditSecretKey = $setting->secretManagedByEnvironment('xendit_secret_key')
94:             ? $setting->resolvedSecret('xendit_secret_key')
95:             : (($data['xendit_secret_key'] ?? null) ?: $setting->getAttributeValue('xendit_secret_key'));
```

**Masalah:** Long Method; Duplicate Code; Conditional Complexity. Update web136/API124 baris mengatur environment-managed secret, encryption, gateway enable dan credentialState audit.

**Teknik dan alasan:** Extract Method/Class per operasi; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: PaymentSettingUpdater dengan normalizeSecrets, validateReadiness dan audit changes; response guard tetap di adapter.

**Invarian dan kriteria khusus selesai:** Blank input mempertahankan secret, env precedence, encrypted raw values tidak terenkripsi ulang, mask audit, timeout step_up dan error key sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tunda sampai characterization lengkap; validasi bank_transfer yang mencurigakan merupakan bug terpisah.

**Pengujian wajib:** T11/T01: secret env vs DB, empty update, enable/disable, callback token kosong, invalid gateway dan API ability/step-up; jangan log secret. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-047"></a>

## RF-047 — API V1 mewarisi controller web untuk reuse protected helper

**Pemilik:** Anggota 1. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 16–28 jam-orang.

[app/Http/Controllers/Api/V1/SalesReportController.php — sales, konteks 10–17](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/SalesReportController.php#L10-L17). Bukti baris 10–16:

```php
10: 
11: class SalesReportController extends ReportsSalesReportController
12: {
13:     use ApiResponder;
14: 
15:     /**
16:      * GET /api/v1/reports/sales
```

[app/Http/Controllers/Api/V1/ReceivableController.php — payReceivable, konteks 14–21](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/ReceivableController.php#L14-L21). Bukti baris 14–20:

```php
14: 
15: class ReceivableController extends AppsReceivableController
16: {
17:     use ApiResponder;
18: 
19:     /**
20:      * GET /api/v1/receivables
```

**Masalah:** Inappropriate Inheritance; High Coupling. Adapter JSON mewarisi controller rendering dan protected state/helper, sehingga perubahan web memengaruhi API; pemindahan semua modul sekaligus berisiko besar.

**Teknik dan alasan:** Replace Inheritance with Delegation secara per use case; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: setelah logic penting dipindah action/query, adapter API yang sudah tipis mewarisi Controller dasar dan inject query/action; lakukan satu modul, bukan migrasi semua controller.

**Invarian dan kriteria khusus selesai:** Route handler publik, auth/abilities, protected helper behavior, payload API, HTTP status dan error envelope tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-007/008/038/045/046 harus lebih dulu untuk modul terkait. Tidak diperlukan untuk paket inti.

**Pengujian wajib:** T01 serta semua V1*ApiTest dan kontrak Inertia modul yang dipindah; inventaris inherited route methods sebelum mengubah parent. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-048"></a>

## RF-048 — Operasi admin user web/API menggandakan update dan guard delete

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** tinggi. **Estimasi:** 6–10 jam-orang.

[app/Http/Controllers/UserController.php — UserController::destroy(), 188–255](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/UserController.php#L188-L255). Bukti baris 199–205:

```php
199: 
200:         // Prevent deleting your own account (avoids accidental self lockout).
201:         if ($ids->contains(auth()->id())) {
202:             $this->auditLogService->log(
203:                 event: 'user.delete_blocked',
204:                 module: 'users',
205:                 auditable: null,
```

[app/Http/Controllers/Api/V1/UserController.php — UserController::destroyUser(), 177–241](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/UserController.php#L177-L241). Bukti baris 178–184:

```php
178:     {
179:         $ids = collect(explode(',', (string) $id))
180:             ->map(fn ($value) => (int) trim($value))
181:             ->filter()
182:             ->unique()
183:             ->values();
184: 
```

**Masalah:** Duplicate Business Logic. Pemeriksaan self-delete/last-super-admin, parsing ID serta audit harus identik pada web/API.

**Teknik dan alasan:** Extract Action; Move Method; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: DeleteUsersAction menerima collection IDs+actor dan hasil domain; adapter memetakan hasil ke pesan/status lama.

**Invarian dan kriteria khusus selesai:** Batch ID filtering, self-protection, super-admin terakhir, urutan audit dan transaksi delete; tidak memperluas permission. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-047 dilakukan setelah action; jangan mengubah guard policy sambil memindah logika.

**Pengujian wajib:** T01: hapus diri, admin terakhir, batch campuran, ID invalid, cross abilities, failed deletion rollback; PrivilegeGuardTest/V1AdminApiTest. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-049"></a>

## RF-049 — Penomoran dokumen PO/GR/SR/ST memakai algoritma sama

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang–tinggi. **Estimasi:** 4–7 jam-orang.

[app/Services/PurchaseOrderService.php — PurchaseOrderService::generateDocumentNumber(), 19–29](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/PurchaseOrderService.php#L19-L29). Bukti baris 20–26:

```php
20:     {
21:         $prefix = 'PO-'.now()->format('Ymd').'-';
22:         $last = PurchaseOrder::where('document_number', 'like', $prefix.'%')
23:             ->orderByDesc('document_number')
24:             ->value('document_number');
25: 
26:         $next = $last ? (int) Str::afterLast($last, '-') + 1 : 1;
```

[app/Services/GoodsReceivingService.php — GoodsReceivingService::generateDocumentNumber(), 26–36](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/GoodsReceivingService.php#L26-L36). Bukti baris 27–33:

```php
27:     {
28:         $prefix = 'GR-'.now()->format('Ymd').'-';
29:         $last = GoodsReceiving::where('document_number', 'like', $prefix.'%')
30:             ->orderByDesc('document_number')
31:             ->value('document_number');
32: 
33:         $next = $last ? (int) Str::afterLast($last, '-') + 1 : 1;
```

[app/Services/SupplierReturnService.php — SupplierReturnService::generateDocumentNumber(), 23–33](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/SupplierReturnService.php#L23-L33). Bukti baris 24–30:

```php
24:     {
25:         $prefix = 'SR-'.now()->format('Ymd').'-';
26:         $last = SupplierReturn::where('document_number', 'like', $prefix.'%')
27:             ->orderByDesc('document_number')
28:             ->value('document_number');
29: 
30:         $next = $last ? (int) Str::afterLast($last, '-') + 1 : 1;
```

[app/Services/StockTransferService.php — StockTransferService::generateDocumentNumber(), 23–33](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/StockTransferService.php#L23-L33). Bukti baris 24–30:

```php
24:     {
25:         $prefix = 'ST-'.now()->format('Ymd').'-';
26:         $last = StockTransfer::where('document_number', 'like', $prefix.'%')
27:             ->orderByDesc('document_number')
28:             ->value('document_number');
29: 
30:         $next = $last ? (int) Str::afterLast($last, '-') + 1 : 1;
```

**Masalah:** Duplicate Code; Magic Prefix Strings. Pencarian nomor terakhir, ambil suffix, increment dan pad4 identik dengan model/prefix berbeda.

**Teknik dan alasan:** Extract Function; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: DocumentSequenceFormatter/helper menerima query model dan prefix; keep DB lookup dan retry di caller. Tidak mengubahnya menjadi sequence table.

**Invarian dan kriteria khusus selesai:** PO-/GR-/SR-/ST-, tanggal Ymd, padding4, custom document_number serta retry collision hanya pada caller yang sudah memilikinya. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Menambah concurrency-safe sequence adalah arsitektur/bugfix terpisah, bukan paket inti.

**Pengujian wajib:** T20: hari baru, suffix0099, nomor kosong, custom supplied dan collision existing; freeze time, compare sequence. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-050"></a>

## RF-050 — GoodsReceiving receive menyatukan penerimaan, stok, batch dan payable

**Pemilik:** Anggota 2. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 8–14 jam-orang.

[app/Services/GoodsReceivingService.php — GoodsReceivingService::receive(), 38–150](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/GoodsReceivingService.php#L38-L150). Bukti baris 64–70:

```php
64: 
65:                 foreach ($items as $item) {
66:                     $poItem = $order->items->firstWhere('id', $item['purchase_order_item_id']);
67:                     if (! $poItem) {
68:                         throw ValidationException::withMessages([
69:                             'items' => 'Item tidak ditemukan di PO.',
70:                         ]);
```

[app/Services/GoodsReceivingService.php — GoodsReceivingService::createOrUpdatePayable(), 166–203](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/GoodsReceivingService.php#L166-L203). Bukti baris 173–179:

```php
173: 
174:         $payable = Payable::updateOrCreate(
175:             ['purchase_order_id' => $order->id],
176:             [
177:                 'supplier_id' => $order->supplier_id,
178:                 'document_number' => $receiving->document_number,
179:                 'total' => $total,
```

**Masalah:** Long Method; Divergent Change. Receive 113 baris dan helper payable merangkum empat domain; perubahan batch atau utang berdampak pada workflow stock.

**Teknik dan alasan:** Extract Method; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: extract receiveLine dan createBatchIfPresent; payable operation tetap terpisah dengan perilaku lama, jangan mengubah updateOrCreate dalam PR refactoring.

**Invarian dan kriteria khusus selesai:** PO status ordered/partial, quantities, product/pivot increments, batch stock dan audit sama; reset payable bermasalah dicatat BF terpisah. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-049 membantu penomoran; BF-006 harus diberi test reproduksi sebelum optimasi/atomicity baru.

**Pengujian wajib:** T20: receiving parsial/lengkap, qty outstanding berlebih, item salah PO, batch metadata, supplier null dan failure rollback. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-051"></a>

## RF-051 — StockMutationService menggandakan payload audit before/after/meta

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 5–8 jam-orang.

[app/Services/StockMutationService.php — StockMutationService::recordStockOpnameAdjustment(), 98–154](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/StockMutationService.php#L98-L154). Bukti baris 127–133:

```php
127:             description: 'Stok produk disesuaikan melalui stock opname.',
128:             before: [
129:                 'product_id' => $product->id,
130:                 'stock_before' => $stockBefore,
131:                 'stock_after' => $stockBefore,
132:                 'difference' => 0,
133:                 'reason' => $reason,
```

[app/Services/StockMutationService.php — StockMutationService::recordSalesReturnRestock(), 156–212](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/StockMutationService.php#L156-L212). Bukti baris 185–191:

```php
185:             description: 'Stok produk bertambah dari restock retur penjualan.',
186:             before: [
187:                 'product_id' => $product->id,
188:                 'stock_before' => $stockBefore,
189:                 'stock_after' => $stockBefore,
190:                 'difference' => 0,
191:                 'reason' => $reason,
```

**Masalah:** Duplicate Transformation. Beberapa wrapper menyusun snapshot product/stock/difference/reference dan meta yang hampir sama; detail alasan dan key domain berbeda.

**Teknik dan alasan:** Extract Method; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: private buildStockSnapshot untuk key benar-benar sama; wrapper tetap menyuplai description/reason/reference dan metadata opname/return/GR masing-masing.

**Invarian dan kriteria khusus selesai:** Key audit, null reason pada before vs default reason after, userId, mutation type dan qty absolute tetap; jangan memotong variasi lewat generic flags besar. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tidak bersamaan RF-013/014 pada satu flow sebelum integration tests stabil.

**Pengujian wajib:** T07/T08/T20: golden array audit untuk initial/opname/sales return/inbound/supplier return, zero delta tidak membuat mutation. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-052"></a>

## RF-052 — recordMutation menerima sepuluh parameter primitif

**Pemilik:** Anggota 2. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** sedang–tinggi. **Estimasi:** 6–10 jam-orang.

[app/Services/StockMutationService.php — StockMutationService::recordMutation(), 18–42](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/StockMutationService.php#L18-L42). Bukti baris 17–23:

```php
17: 
18:     public function recordMutation(
19:         Product $product,
20:         ?int $warehouseId,
21:         string $referenceType,
22:         ?int $referenceId,
23:         string $mutationType,
```

**Masalah:** Long Parameter List; Data Clumps. Product, warehouse, reference, delta, before/after dan actor harus dibawa bersama di banyak caller; typo mudah terjadi walau named args membantu.

**Teknik dan alasan:** Introduce Parameter Object selektif; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: MutationRecord immutable mengelompokkan reference dan delta snapshot; migrasikan caller satu per satu hanya jika audit menunjukkan manfaat; named args saat ini sudah mengurangi risiko.

**Invarian dan kriteria khusus selesai:** Semua persisted fields dan casts sama; constructor tidak menambah invariant baru yang menolak data lama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tunda karena menyentuh checkout/dine/returns/purchasing sekaligus; RF-051 lebih kecil manfaatnya.

**Pengujian wajib:** T07/T12/T13/T20: kontrak semua caller, actor null dan legacy warehouse null; assert full stock mutation rows. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-053"></a>

## RF-053 — CRM automation menggabungkan audience, campaign, delivery dan template

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang–tinggi. **Estimasi:** 8–14 jam-orang.

[app/Services/CrmAutomationService.php — CrmAutomationService::processCampaign(), 132–176](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/CrmAutomationService.php#L132-L176). Bukti baris 146–152:

```php
146:         $outlet = $campaign->outlet_id ? Outlet::find($campaign->outlet_id) : null;
147:         $waAvailable = Setting::getBoolForOutlet('wa_enabled', $outlet, false)
148:             && Setting::getForOutlet('wa_service_url', $outlet)
149:             && ($this->whatsAppService?->status($outlet)['connected'] ?? false);
150: 
151:         foreach ($audience as $customer) {
152:             $payload = $this->buildCustomerPayload($campaign, $customer);
```

[app/Services/CrmAutomationService.php — CrmAutomationService::buildAudience(), 45–104](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/CrmAutomationService.php#L45-L104). Bukti baris 67–73:

```php
67: 
68:         return $query->get()->filter(function (Customer $customer) use ($filters, $at) {
69:             $voucherFilter = $filters['voucher_filter'] ?? 'all';
70:             $hasActiveVoucher = $customer->vouchers
71:                 ->filter(fn ($voucher) => $voucher->currentStatusLabel() === 'active')
72:                 ->isNotEmpty();
73:             if ($voucherFilter === 'has_active_voucher' && ! $hasActiveVoucher) {
```

**Masalah:** Large Service; Divergent Change. 488 baris mencampur seleksi audience, snapshot/log, pengiriman WhatsApp, reminder terjadwal dan template pesan.

**Teknik dan alasan:** Extract Class berdasarkan tanggung jawab yang nyata; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: AudienceSelector dan CampaignPayloadBuilder dulu; processCampaign tetap orkestrasi sinkron agar jumlah/urutan send tidak berubah.

**Invarian dan kriteria khusus selesai:** Audience sort nama, voucherStatus, pesan default, log delete/rebuild, markLog/status dan fallback wa-link tetap; queueing merupakan perubahan arsitektur. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tidak mengubah RF-054 reminder bersamaan; perlu BF-008 share token dipisah.

**Pengujian wajib:** T19: member/non-member, receivable/voucher filters, WA disabled/disconnected/send fail/success, template name/phone kosong; Http::fake dan assert tidak ada pesan sungguhan. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-054"></a>

## RF-054 — Pembuatan reminder due-soon dan overdue berulang

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 4–6 jam-orang.

[app/Services/CrmAutomationService.php — CrmAutomationService::generateDueSoonCampaign(), 312–337](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/CrmAutomationService.php#L312-L337). Bukti baris 314–320:

```php
314:         $contextKey = 'due-soon-'.$at->toDateString();
315:         $campaign = CustomerCampaign::query()->firstOrCreate(
316:             ['context_key' => $contextKey],
317:             [
318:                 'name' => 'Reminder Jatuh Tempo H-3 '.$at->format('d M Y'),
319:                 'type' => CustomerCampaign::TYPE_DUE_DATE_REMINDER,
320:                 'status' => CustomerCampaign::STATUS_READY,
```

[app/Services/CrmAutomationService.php — CrmAutomationService::generateOverdueCampaign(), 339–364](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/CrmAutomationService.php#L339-L364). Bukti baris 341–347:

```php
341:         $contextKey = 'overdue-'.$at->toDateString();
342:         $campaign = CustomerCampaign::query()->firstOrCreate(
343:             ['context_key' => $contextKey],
344:             [
345:                 'name' => 'Reminder Piutang Overdue '.$at->format('d M Y'),
346:                 'type' => CustomerCampaign::TYPE_DUE_DATE_REMINDER,
347:                 'status' => CustomerCampaign::STATUS_READY,
```

**Masalah:** Duplicate Code; Magic Business Values. Kedua method membuat campaign berdasarkan tanggal lalu mengisi reminder logs; beda query/tulisan mudah terkubur dalam boilerplate.

**Teknik dan alasan:** Extract Method; Parameter Object lokal kecil; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: helper ensureReceivableReminder menerima contextKey,name,template,query,reason; nilai H-3 diberi nama bila juga dipakai di audience.

**Invarian dan kriteria khusus selesai:** Context key idempotency per hari, hanya campaign baru dibuat logs, rentang due dan wording sama; tidak memperluas audience/outlet. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Setelah RF-053 bila sama-sama dipilih, satu pemilik file.

**Pengujian wajib:** T19: waktu dibekukan H-3/H0/overdue, run dua kali, paid excluded dan no due; snapshot messages dan logs count. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-055"></a>

## RF-055 — WhatsApp PHP wrapper mengulang try/request/fallback

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah–sedang. **Estimasi:** 3–5 jam-orang.

[app/Services/WhatsAppService.php — WhatsAppService::status(), 45–54](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/WhatsAppService.php#L45-L54). Bukti baris 46–52:

```php
46:     {
47:         try {
48:             $res = $this->request($outlet)->timeout(5)->get($this->baseUrl($outlet).'/status');
49: 
50:             return $res->successful() ? $res->json() : ['connected' => false, 'error' => 'unreachable'];
51:         } catch (\Exception $e) {
52:             return ['connected' => false, 'error' => $e->getMessage()];
```

[app/Services/WhatsAppService.php — WhatsAppService::start(), 56–65](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/WhatsAppService.php#L56-L65). Bukti baris 57–63:

```php
57:     {
58:         try {
59:             $res = $this->request($outlet)->timeout(10)->post($this->baseUrl($outlet).'/start');
60: 
61:             return $res->successful() ? $res->json() : ['status' => false];
62:         } catch (\Exception $e) {
63:             return ['status' => false, 'error' => $e->getMessage()];
```

**Masalah:** Duplicate Error Handling; Coupling config/transport. status/start/send/disconnect mengulang HTTP+catch dengan timeout dan return bentuk berbeda; pengujian transport bercampur dengan settings.

**Teknik dan alasan:** Extract Method yang menjaga perbedaan; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: performRequest untuk transport dan token, operasi tetap memetakan JSON/boolean/fallback sendiri; inject client jika diperlukan mock.

**Invarian dan kriteria khusus selesai:** Timeout status5/start10/send15/disconnect10, header X-Service-Token, error exposure dan baseUrl dari setting tetap; tidak mengganti ke env URL tanpa keputusan. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-053 memakai service ini; tambah test dahulu tanpa WhatsApp nyata.

**Pengujian wajib:** T19: Http::fake 2xx/4xx/5xx, malformed JSON baseline, connection exception, null URL dan token absent; assert timeout/payload. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-056"></a>

## RF-056 — Node gateway menggabungkan state perangkat, route dan bootstrap

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang–tinggi. **Estimasi:** 6–10 jam-orang.

[whatsapp-service/server.js — createClient, konteks 31–38](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/whatsapp-service/server.js#L31-L38). Bukti baris 31–37:

```javascript
31: 
32: let client = null;
33: let qrCodeData = null;
34: let isConnected = false;
35: let phoneNumber = null;
36: let isStarting = false;
37: 
```

[whatsapp-service/server.js — createClient, konteks 38–70](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/whatsapp-service/server.js#L38-L70). Bukti baris 37–43:

```javascript
37: 
38: function createClient() {
39:     if (client) {
40:         try { client.destroy(); } catch (e) {}
41:     }
42: 
43:     client = new Client({
```

**Masalah:** Module-level Mutable State; Mixed Responsibilities. Satu file memuat client lifecycle, lima state, otorisasi, HTTP routes, format nomor dan listen. Menguji route mengharuskan side effect bootstrap.

**Teknik dan alasan:** Extract Module; Dependency Injection sederhana; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: createApp({gateway,token}) diekspor untuk test, lifecycle gateway di modul sendiri, bootstrap listen tetap entry server.js. Jangan menambah state machine/library tanpa kebutuhan.

**Invarian dan kriteria khusus selesai:** HTTP503 saat token kosong/401 invalid, timingSafeEqual, localhost default, start/ready/disconnect events, format 0→62 dan response/error keys sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Paket inti tidak menyentuh service; tanpa koneksi WA sungguhan seluruh send harus fake.

**Pengujian wajib:** T19 dengan node:test dan fake Client/QRCode: start ulang, QR, ready, disconnected, send belum connect, missing payload dan send exception. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-057"></a>

## RF-057 — Setting getter/setter mengulang compatibility guard schema

**Pemilik:** Anggota 1. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** sedang–tinggi. **Estimasi:** 4–7 jam-orang.

[app/Models/Setting.php — Setting::getForOutlet(), 45–59](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Models/Setting.php#L45-L59). Bukti baris 47–53:

```php
47:         if ($outlet) {
48:             if (! Schema::hasColumn('settings', 'outlet_id')) {
49:                 return static::get($key, $default);
50:             }
51: 
52:             $value = static::where('key', $key)->where('outlet_id', $outlet->id)->value('value');
53:             if ($value !== null) {
```

[app/Models/Setting.php — Setting::setForOutlet(), 66–80](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Models/Setting.php#L66-L80). Bukti baris 71–77:

```php
71: 
72:         if (! Schema::hasColumn('settings', 'outlet_id')) {
73:             return static::set($key, $value, $description);
74:         }
75: 
76:         return static::updateOrCreate(
77:             ['key' => $key, 'outlet_id' => $outlet->id],
```

**Masalah:** Duplicate Conditional; Low-level concern di model accessor. Pengecekan kolom settings.outlet_id di get/set/outlet fallback berulang. Legacy schema adalah kontrak nyata, bukan dead code.

**Teknik dan alasan:** Extract private capability helper; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: hasOutletColumn dipanggil oleh operasi; jangan cache static antar test/migration yang schema-nya bisa berubah.

**Invarian dan kriteria khusus selesai:** Global fallback saat kolom belum ada, null value fallback, updateOrCreate keys, description dan query timing tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Menghapus guard atau request cache adalah optimasi/compatibility change, tunda dari tugas inti.

**Pengujian wajib:** T18: schema lama/current, global-only, override null/empty string dan setMany; migration/setup tests. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-058"></a>

## RF-058 — Fixture setup transaksi/produk berulang lintas test

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah–sedang. **Estimasi:** 4–7 jam-orang.

[tests/Feature/Products/BatchAllocationTest.php — BatchAllocationTest::setUp(), 33–61](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/tests/Feature/Products/BatchAllocationTest.php#L33-L61). Bukti baris 37–43:

```php
37:         $this->seed([
38:             PermissionSeeder::class,
39:             RoleSeeder::class,
40:             UserSeeder::class,
41:         ]);
42: 
43:         $this->cashier = User::where('email', 'cashier@gmail.com')->first();
```

[tests/Feature/Products/BatchFefoTest.php — BatchFefoTest::setUp(), 35–65](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/tests/Feature/Products/BatchFefoTest.php#L35-L65). Bukti baris 39–45:

```php
39:         $this->seed([
40:             PermissionSeeder::class,
41:             RoleSeeder::class,
42:             UserSeeder::class,
43:         ]);
44: 
45:         $this->admin = User::where('email', 'arya@gmail.com')->first();
```

**Masalah:** Duplicate Test Setup; Obscure Fixture. Banyak test menanam permission/role/user, warehouse, produk tax0 dan pivot/shift dengan blok sama. Arrange mendominasi dan drift data uji sulit terlihat.

**Teknik dan alasan:** Extract Test Builder/Factory State; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: helper fixture bernama productWithWarehouseStock dan cashierWithOpenShift; default eksplisit tax0/abilities, test tetap memuat angka inti yang menjelaskan kasus.

**Invarian dan kriteria khusus selesai:** Tidak menyembunyikan auth ability atau stok di global fixture, setiap test tetap isolated RefreshDatabase; assertion tidak dikurangi. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Opsional setelah test karakterisasi paket inti ditambahkan; bukan pengganti fitur testing anggota.

**Pengujian wajib:** T21: jalankan file yang memakai builder terpisah dan seluruh suite, seed dibuktikan tidak bergantung urutan. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-059"></a>

## RF-059 — Test laporan panjang karena arrange berbagai domain sekaligus

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah. **Estimasi:** 3–5 jam-orang.

[tests/Feature/Reports/AdvancedSalesInsightsTest.php — AdvancedSalesInsightsTest::test_wave_three_summaries_include_promo_loyalty_and_crm_operational_metrics(), 259–408](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/tests/Feature/Reports/AdvancedSalesInsightsTest.php#L259-L408). Bukti baris 258–264:

```php
258: 
259:     public function test_wave_three_summaries_include_promo_loyalty_and_crm_operational_metrics(): void
260:     {
261:         $user = $this->createUser();
262:         $category = Category::create([
263:             'name' => 'Promo Category',
264:             'description' => 'Testing',
```

**Masalah:** Long Test Method; Multiple Reasons to Fail. Satu test150 baris mempersiapkan promo, loyalty dan CRM sehingga failure asal domain kurang jelas.

**Teknik dan alasan:** Extract Fixture Builders; Split Test per behavior; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: pisah skenario per summary dengan helper data minimal; pertahankan satu integration scenario gabungan yang relevan, bukan duplikasi assertion.

**Invarian dan kriteria khusus selesai:** Semua assertion lama tetap terwakili dan fixtures bermakna; tidak mengubah query hanya untuk membuat test lolos. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-058 bila builder bersama dibuat lebih dulu.

**Pengujian wajib:** T21/T14: semua kasus asli harus tercakup; jalankan isolated dan suite, cek failure message menjelaskan domain. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-060"></a>

## RF-060 — Dependency dan parameter private sisa ekstraksi checkout

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah–sedang. **Estimasi:** 2–4 jam-orang.

[app/Services/CheckoutService.php — CheckoutService::__construct(), 21–29](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/CheckoutService.php#L21-L29). Bukti baris 25–31:

```php
25:         private readonly PriceListService $priceListService,
26:         private readonly BatchService $batchService,
27:         private readonly TransactionTenderService $tenderService,
28:         private readonly StockMutationService $stockMutationService,
29:     ) {}
30: 
31:     public function execute(CheckoutContext $ctx): CheckoutResult
```

[app/Http/Controllers/Apps/TransactionController.php — TransactionController::__construct(), 41–51](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/TransactionController.php#L41-L51). Bukti baris 46–52:

```php
46:         protected readonly PriceListService $priceListService,
47:         protected readonly BatchService $batchService,
48:         protected readonly TransactionTenderService $tenderService,
49:         protected readonly OutletAccessService $outletAccessService,
50:         protected readonly CheckoutService $checkoutService,
51:     ) {}
52: 
```

[app/Services/BatchService.php — BatchService::getAvailableBatches(), 11–22](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/BatchService.php#L11-L22). Bukti baris 10–16:

```php
10: {
11:     public function getAvailableBatches(int $productId, int $warehouseId, int $qtyNeeded): Collection
12:     {
13:         return ProductBatch::where('product_id', $productId)
14:             ->where('warehouse_id', $warehouseId)
15:             ->where('stock', '>', 0)
16:             ->where(function ($q) {
```

**Masalah:** Unused Dependency/Parameter — kandidat cleanup terbatas. Pencarian seluruh tracked source menunjukkan properti batchService hanya deklarasi di kedua class; qtyNeeded tidak dibaca dalam getAvailableBatches. Jangan menyamakan unused public API dengan dead code aman dihapus.

**Teknik dan alasan:** Remove unused private dependency; Rename/Remove Parameter setelah call graph; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: buang injection private yang tanpa pemakaian setelah cek constructor/DI tests; parameter public qtyNeeded hanya dihapus jika seluruh caller lokal, docs dan konsumen eksternal disepakati.

**Invarian dan kriteria khusus selesai:** Perilaku container resolution dan output alokasi tetap. Jangan hapus service/method public hanya karena rg tidak menemukan pemanggil. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-023 kelak dapat justru memakai BatchService; cleanup sebaiknya setelah keputusan ekstraksi, bukan sebelum.

**Pengujian wajib:** T12/T07: container resolves controller/service, checkout dan batch test; cari named args serta pemanggilan dinamis sebelum change. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-061"></a>

## RF-061 — LowStockAlerts lokal kosong dan PaymentPanel tidak terjangkau dari app

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah–sedang. **Estimasi:** 2–4 jam-orang.

[resources/js/Pages/Dashboard/Transactions/Index.jsx — Index/LowStockAlerts, konteks 53–2081](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Transactions/Index.jsx#L53-L2081). Bukti baris 169–175:

```jsx
169: 
170:     const LowStockAlerts = () => null;
171: 
172:     // Calculations
173:     const discount = useMemo(
174:         () => Math.max(0, Number(discountInput) || 0),
175:         [discountInput]
```

[resources/js/Components/POS/index.jsx — barrel exports, konteks 3–7](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Components/POS/index.jsx#L3-L7). Bukti baris 3–7:

```jsx
3: export { default as CartPanel } from "./CartPanel";
4: export { default as PaymentPanel } from "./PaymentPanel";
5: export { default as SearchBar } from "./SearchBar";
6: export { default as CustomerSelect } from "./CustomerSelect";
7: export { default as NumpadModal } from "./NumpadModal";
```

**Masalah:** Dead local symbol terkonfirmasi; unused component masih kandidat. LowStockAlerts=()=>null tidak direferensikan; PaymentPanel hanya diekspor barrel, tidak tampak import consumer. Komponen PaymentPanel besar tidak boleh dihapus hanya berdasarkan ukuran.

**Teknik dan alasan:** Remove Dead Code; verifikasi export graph dahulu; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: hapus local LowStockAlerts dan import/symbol yang benar-benar tak terpakai. Untuk PaymentPanel audit seluruh import alias/dynamic glob; bila publik/in-use, pertahankan atau dokumentasikan, jangan paksa dipakai di POS.

**Invarian dan kriteria khusus selesai:** Semua UI saat ini identik; barrel export bisa merupakan kontrak sehingga penghapusannya belum otomatis aman. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-030 memengaruhi page sama; Anggota 3 pemilik tunggal. Kesimpulan dead hanya berlaku local function yang tidak dipanggil.

**Pengujian wajib:** T03/T12: build, navigasi POS, search semua import dan import.meta.glob; smoke render tidak hilang. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-062"></a>

## RF-062 — Helper permission menggunakan hook tetapi bernama fungsi biasa

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 3–5 jam-orang.

[resources/js/Utils/Permission.jsx — hasAnyPermission, konteks 3–14](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Utils/Permission.jsx#L3-L14). Bukti baris 2–8:

```jsx
2: 
3: export default function hasAnyPermission(permissions, givenPermissions = null) {
4:     const { canAny, isSuperAdmin } = useAuthorization();
5: 
6:     if (givenPermissions) {
7:         return (
8:             isSuperAdmin() ||
```

[resources/js/Utils/authorization.js — useAuthorization, konteks 36–46](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Utils/authorization.js#L36-L46). Bukti baris 35–41:

```javascript
35: 
36: export function useAuthorization() {
37:     const { auth } = usePage().props;
38: 
39:     return {
40:         auth,
41:         can: (permission) => can(permission, auth),
```

**Masalah:** Misleading Naming; Hook Contract tersembunyi. hasAnyPermission memanggil useAuthorization; nama fungsi biasa menyembunyikan aturan hook dari pemanggil, sedangkan helper pure canAny sudah tersedia.

**Teknik dan alasan:** Rename; Separate pure helper dan hook; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: gunakan useAuthorization di component consumer; legacy hook diubah ke nama useHasAnyPermission, atau delegasikan helper pure ketika auth disuplai. Jangan mengubah super-admin semantics.

**Invarian dan kriteria khusus selesai:** givenPermissions cabang true ketat versus !! can tetap sesuai baseline; empty permissions tetap false, super override sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Periksa semua pemanggil sebelum rename; test framework frontend belum tersedia.

**Pengujian wajib:** T01/T16: auth missing, super, empty list, givenPermissions true/false/truthy; audit callsite di render, bukan loop kondisional. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-063"></a>

## RF-063 — Dua Modal menggandakan shell HeadlessUI

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah–sedang. **Estimasi:** 3–5 jam-orang.

[resources/js/Components/Modal.jsx — Modal, konteks 4–57](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Components/Modal.jsx#L4-L57). Bukti baris 10–16:

```jsx
10: 
11:     const maxWidthClass = {
12:         sm: 'sm:max-w-sm',
13:         md: 'sm:max-w-md',
14:         lg: 'sm:max-w-lg',
15:         xl: 'sm:max-w-xl',
16:         '2xl': 'sm:max-w-2xl',
```

[resources/js/Components/Dashboard/Modal.jsx — Modal, konteks 4–62](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Components/Dashboard/Modal.jsx#L4-L62). Bukti baris 10–16:

```jsx
10: 
11:     const maxWidthClass = {
12:         sm: 'sm:max-w-sm',
13:         md: 'sm:max-w-md',
14:         lg: 'sm:max-w-lg',
15:         xl: 'sm:max-w-xl',
16:         '2xl': 'sm:max-w-2xl',
```

**Masalah:** Duplicate Component. Transition/Dialog, closeable guard dan width mapping sama; dashboard menambah title/padding/dark mode dan leave duration berbeda.

**Teknik dan alasan:** Extract Component; wrapper adapters; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: ModalShell menerima presentational config yang memang berbeda; wrapper mempertahankan import path dan title.

**Invarian dan kriteria khusus selesai:** Escape/backdrop close, focus restore, maxWidth classes, 75/200 duration dan dark style tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Mandiri dari POS; jangan mengganti semua modal custom di aplikasi sekaligus.

**Pengujian wajib:** T03: open/close/Escape/backdrop, closeable=false, setiap maxWidth, dashboard title dan profile delete confirmation. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-064"></a>

## RF-064 — Editor item PO/transfer/retur supplier mengulang operasi baris

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 6–9 jam-orang.

[resources/js/Pages/Dashboard/PurchaseOrders/Create.jsx — Create, konteks 22–273](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/PurchaseOrders/Create.jsx#L22-L273). Bukti baris 37–43:

```jsx
37: 
38:     const addItem = (product) => {
39:         if (data.items.some((i) => i.product_id === product.id)) {
40:             toast.error("Produk sudah ada di daftar.");
41:             return;
42:         }
43:         setData("items", [
```

[resources/js/Pages/Dashboard/StockTransfers/Create.jsx — Create, konteks 8–168](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/StockTransfers/Create.jsx#L8-L168). Bukti baris 21–27:

```jsx
21: 
22:     const addItem = (product) => {
23:         if (data.items.some((i) => i.product_id === product.id)) {
24:             toast.error("Produk sudah ada di daftar.");
25:             return;
26:         }
27:         setData("items", [...data.items, { product_id: product.id, product_title: product.title, product_sku: product.sku || "-", qty: 1 }]);
```

[resources/js/Pages/Dashboard/SupplierReturns/Create.jsx — Create, konteks 20–347](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/SupplierReturns/Create.jsx#L20-L347). Bukti baris 40–46:

```jsx
40: 
41:     const addItemFromProduct = (product) => {
42:         if (data.items.some((i) => i.product_id === product.id)) {
43:             toast.error("Produk sudah ada di daftar.");
44:             return;
45:         }
46:         setData("items", [
```

**Masalah:** Duplicate Array Manipulation/JSX. Pemilihan produk, tambah/hapus row dan input qty berulang, tetapi qty_ordered/qty_returned serta harga/reason berbeda.

**Teknik dan alasan:** Extract Function/Presentational Row; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: ekstrak operasi array dan ProductPickerRow kecil; tiap form tetap mendefinisikan field domain, validasi dan submit.

**Invarian dan kriteria khusus selesai:** Field names, duplicate-product prevention, qty min, unit_price/reason dan warehouse constraints sama; jangan generic form engine. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Opsional; tidak menyentuh stok/backend, pemilik Anggota 3.

**Pengujian wajib:** T20/T03: add/remove/multi-row, product duplikat, qty invalid, data dari GR dan warehouse berbeda. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-065"></a>

## RF-065 — Panel konfirmasi pembayaran berulang di history dan invoice

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 4–6 jam-orang.

[resources/js/Pages/Dashboard/Transactions/History.jsx — History, konteks 37–762](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Transactions/History.jsx#L37-L762). Bukti baris 46–52:

```jsx
46:     const [showFilters, setShowFilters] = useState(false);
47:     const [confirmModal, setConfirmModal] = useState({
48:         open: false,
49:         transaction: null,
50:     });
51:     const [isConfirming, setIsConfirming] = useState(false);
52: 
```

[resources/js/Pages/Dashboard/Transactions/Print.jsx — Print, konteks 23–1045](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Transactions/Print.jsx#L23-L1045). Bukti baris 26–32:

```jsx
26:     const [printMode, setPrintMode] = useState("invoice"); // 'invoice' | 'thermal80' | 'thermal58'
27:     const [showConfirmModal, setShowConfirmModal] = useState(false);
28:     const [isConfirming, setIsConfirming] = useState(false);
29:     const [liveStatus, setLiveStatus] = useState(
30:         transaction?.payment_status || "pending"
31:     );
32:     const canConfirmPayment = can("transactions-confirm-payment");
```

**Masalah:** Duplicate JSX/Interaction. Modal konfirmasi bank serta state loading/error disalin antara history dan print; closing/reload mudah berbeda.

**Teknik dan alasan:** Extract Component; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: ConfirmBankPaymentDialog menerima transaction,show,onClose,onSuccess; request route dan callback per halaman tetap eksplisit.

**Invarian dan kriteria khusus selesai:** Permission canConfirmPayment, bank-only, disable selama submit, step_up redirect dan pesan error sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Sesudah RF-033 jika print dipecah; RF-032 hanya formatter tidak memengaruhi modal.

**Pengujian wajib:** T11/T03: no permission, bank paid/pending, submit sukses/error/423, dismiss dan confirmation ulang. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-066"></a>

## RF-066 — Tabel/log CRM campaign dan reminder memiliki blok berulang

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah–sedang. **Estimasi:** 4–6 jam-orang.

[resources/js/Pages/Dashboard/CrmCampaigns/Index.jsx — Index, konteks 10–19](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/CrmCampaigns/Index.jsx#L10-L19). Bukti baris 9–15:

```jsx
9: 
10: const statusBadge = (status) => {
11:     const classes = {
12:         draft: "bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300",
13:         ready: "bg-primary-100 text-primary-700 dark:bg-primary-950/30 dark:text-primary-300",
14:         processed: "bg-success-100 text-success-700 dark:bg-success-950/30 dark:text-success-400",
15:         cancelled: "bg-rose-100 text-rose-700 dark:bg-rose-950/30 dark:text-rose-300",
```

[resources/js/Pages/Dashboard/CrmReminders/Index.jsx — Index, konteks 8–108](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/CrmReminders/Index.jsx#L8-L108). Bukti baris 37–43:

```jsx
37:                         <select
38:                             value={filters.status || ""}
39:                             onChange={(event) => handleFilterChange("status", event.target.value)}
40:                             className="h-11 rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
41:                         >
42:                             <option value="">Semua Status</option>
43:                             <option value="draft">Draft</option>
```

**Masalah:** Duplicate JSX/Status Configuration. Kedua index mengonstruksi status, channel dan aksi terkait campaign yang sama dengan judul/filter berbeda.

**Teknik dan alasan:** Extract Component dan mapping lokal; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: CampaignRow/StatusBadge dipakai bersama, halaman tetap menentukan filters dan route.

**Invarian dan kriteria khusus selesai:** Teks status, warna, visibility action dan type reminder/campaign tetap; jangan menyatukan semua status aplikasi. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-053 backend tidak wajib, hindari format baru yang mengubah label.

**Pengujian wajib:** T19 UI: draft/ready/sent/cancelled, audience kosong, permission akses/action berbeda serta pagination. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-067"></a>

## RF-067 — Label status purchasing diulang di index/show

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah. **Estimasi:** 2–4 jam-orang.

[resources/js/Pages/Dashboard/PurchaseOrders/Index.jsx — Index, konteks 23–40](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/PurchaseOrders/Index.jsx#L23-L40). Bukti baris 22–28:

```jsx
22: 
23: const statusBadge = (status) => {
24:     const base = "inline-flex rounded-full px-2.5 py-1 text-xs font-semibold";
25:     const map = {
26:         draft: "bg-warning-100 text-warning-700 dark:bg-warning-950/30 dark:text-warning-400",
27:         ordered: "bg-primary-100 text-primary-700 dark:bg-primary-950/30 dark:text-primary-400",
28:         partial_received: "bg-accent-100 text-accent-700 dark:bg-accent-950/30 dark:text-accent-400",
```

[resources/js/Pages/Dashboard/PurchaseOrders/Show.jsx — Show, konteks 32–49](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/PurchaseOrders/Show.jsx#L32-L49). Bukti baris 31–37:

```jsx
31: 
32: const statusBadge = (status) => {
33:     const base = "inline-flex rounded-full px-2.5 py-1 text-xs font-semibold";
34:     const map = {
35:         draft: "bg-warning-100 text-warning-700 dark:bg-warning-950/30 dark:text-warning-400",
36:         ordered: "bg-primary-100 text-primary-700 dark:bg-primary-950/30 dark:text-primary-400",
37:         partial_received: "bg-accent-100 text-accent-700 dark:bg-accent-950/30 dark:text-accent-400",
```

[resources/js/Pages/Dashboard/SupplierReturns/Show.jsx — Show, konteks 30–43](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/SupplierReturns/Show.jsx#L30-L43). Bukti baris 29–35:

```jsx
29: 
30: const statusBadge = (status) => {
31:     const base = "inline-flex rounded-full px-2.5 py-1 text-xs font-semibold";
32:     const map = {
33:         draft: "bg-warning-100 text-warning-700 dark:bg-warning-950/30 dark:text-warning-400",
34:         completed: "bg-success-100 text-success-700 dark:bg-success-950/30 dark:text-success-400",
35:         cancelled: "bg-rose-100 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400",
```

**Masalah:** Duplicate Magic String Mappings. Label/style status dokumen ditulis ulang; salah satu halaman dapat tertinggal saat label yang sama berubah.

**Teknik dan alasan:** Extract Constants/Small Badge Component; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: mapping per domain PO dan supplier return; gunakan value map yang sama untuk index/show masing-masing tanpa membuat enum universal.

**Invarian dan kriteria khusus selesai:** Key draft/ordered/partial_received/completed/cancelled dan class CSS yang berbeda disengaja tetap; unknown fallback dipertahankan. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Mandiri, cocok opsional kecil setelah RF-002/032 selesai.

**Pengujian wajib:** T20/T16: setiap status dan unknown/null, snapshot label/class, izin tombol send/complete tidak berubah. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-068"></a>

## RF-068 — Seeder coverage/demo memiliki skenario bisnis sangat panjang

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 6–10 jam-orang.

[database/seeders/FeatureCoverageSeeder.php — FeatureCoverageSeeder::seedPurchaseFlow(), 265–521](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/database/seeders/FeatureCoverageSeeder.php#L265-L521). Bukti baris 264–270:

```php
264: 
265:     private function seedPurchaseFlow(
266:         User $admin,
267:         User $cashier,
268:         Collection $suppliers,
269:         Collection $products
270:     ): void {
```

[database/seeders/FeatureCoverageSeeder.php — FeatureCoverageSeeder::seedStockOpnames(), 523–663](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/database/seeders/FeatureCoverageSeeder.php#L523-L663). Bukti baris 522–528:

```php
522: 
523:     private function seedStockOpnames(User $admin, Collection $products): void
524:     {
525:         $stockMutationService = app(StockMutationService::class);
526:         $auditLogService = app(AuditLogService::class);
527:         $warehouseId = Warehouse::where('code', 'WH-MAL')->value('id');
528: 
```

**Masalah:** Long Method; Fixture Responsibilities. seedPurchaseFlow257 baris dan seedStockOpnames141 baris menggabungkan banyak skenario, insertion dan audit; perubahan demo mudah merusak aggregate.

**Teknik dan alasan:** Extract scenario methods/data builders; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: pisahkan completedPurchaseScenario, partialPurchaseScenario dan draft/finalizedOpnameScenario; urutan IDs/dependensi tetap. Data tabel statis tidak perlu dipecah hanya karena panjang.

**Invarian dan kriteria khusus selesai:** Demo tetap opt-in/reset nonproduction; accounts/outlets, reproducibility references, aggregate stock dan all seeded assertions sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Bukan inti; RF-058 builder test tidak otomatis cocok untuk seeders produksi.

**Pengujian wajib:** T21: SeedDemoCommandTest, run fresh local snapshot serta inventory reconcile/read-only; jangan menjalankan pada production. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-069"></a>

## RF-069 — ProductsImport model mencampur normalisasi row, kategori dan persist

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 4–7 jam-orang.

[app/Imports/ProductsImport.php — ProductsImport::model(), 17–44](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Imports/ProductsImport.php#L17-L44). Bukti baris 19–25:

```php
19:         $this->rowCount++;
20:         $categoryName = trim($row['kategori'] ?? 'Umum');
21:         $category = Category::firstOrCreate(
22:             ['name' => $categoryName],
23:             ['description' => '', 'image' => 'default.png']
24:         );
25: 
```

[app/Imports/ProductsImport.php — ProductsImport::model(), 17–44](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Imports/ProductsImport.php#L17-L44). Bukti baris 27–33:

```php
27: 
28:         return Product::updateOrCreate(
29:             ['barcode' => $barcode],
30:             [
31:                 'sku' => $row['sku'] ?? $barcode,
32:                 'title' => $row['nama'] ?? '',
33:                 'description' => $row['deskripsi'] ?? '',
```

**Masalah:** Mixed Responsibilities; Magic Header Strings. Callback model membuat kategori lalu upsert Product dan konversi kolom spreadsheet; pure mapping sulit diuji tanpa DB.

**Teknik dan alasan:** Extract Method/Mapper; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: mapProductRow murni setelah resolveCategory; header names dan rowCount tetap; jangan mengganti Maatwebsite contract dalam commit ini.

**Invarian dan kriteria khusus selesai:** Upsert berdasarkan barcode, fallback SKU/category, casting harga/stok, tax11 default, rowCount dan batch/chunk100 sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** BF-004 sinkronisasi stock adalah bug fix terpisah, tidak dimasukkan mapper refactor.

**Pengujian wajib:** T22: kategori baru/existing, barcode existing, blank/null numeric, invalid row, beberapa chunk; assert rowCount dan DB baseline. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-070"></a>

## RF-070 — Command expiry memuat workflow stok dan batch

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** tinggi. **Estimasi:** 8–12 jam-orang.

[app/Console/Commands/ExpirePendingTransactionsCommand.php — ExpirePendingTransactionsCommand::handle(), 16–112](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Console/Commands/ExpirePendingTransactionsCommand.php#L16-L112). Bukti baris 49–55:

```php
49:             try {
50:                 DB::transaction(function () use ($transaction, $stockMutationService, &$expired) {
51:                     $locked = Transaction::query()
52:                         ->lockForUpdate()
53:                         ->with(['details.product.components', 'details.batchAllocations'])
54:                         ->findOrFail($transaction->id);
55: 
```

[app/Console/Commands/ExpirePendingTransactionsCommand.php — ExpirePendingTransactionsCommand::restock(), 114–133](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Console/Commands/ExpirePendingTransactionsCommand.php#L114-L133). Bukti baris 115–121:

```php
115:     {
116:         if ($product->is_composite) {
117:             foreach ($product->components as $component) {
118:                 $componentQty = (int) round((float) $component->pivot->qty * $qty);
119:                 $this->restock($component, $warehouseId, $componentQty);
120:             }
121: 
```

**Masalah:** Long Method; Business Logic di CLI Adapter. handle97 baris menangani seleksi, dry-run, lock, restock recursive, allocation dan update failed sekaligus.

**Teknik dan alasan:** Extract Application Operation; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: ExpireTransactionAction menangani satu transaksi; command tetap selection/dry-run/count/error output.

**Invarian dan kriteria khusus selesai:** Cutoff hours clamp1, methods eligible, pending guard, note append, batch restore, stock mutations dan exception continue sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tidak mengubah race webhooks atau status machine; concurrency MySQL tetap perlu.

**Pengujian wajib:** T12/T07: dry-run no-write, paid changed sebelum lock, conversion>1, composite, allocations, satu item gagal dan retry. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-071"></a>

## RF-071 — Query perbandingan aggregate inventory berulang

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah–sedang. **Estimasi:** 3–5 jam-orang.

[app/Console/Commands/InventoryReconcileCommand.php — InventoryReconcileCommand::handle(), 15–59](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Console/Commands/InventoryReconcileCommand.php#L15-L59). Bukti baris 18–24:

```php
18:             ->select('products.id', 'products.title', 'products.stock')
19:             ->selectRaw('COALESCE(SUM(product_warehouse.stock), 0) AS pivot_total')
20:             ->leftJoin('product_warehouse', 'product_warehouse.product_id', '=', 'products.id')
21:             ->groupBy('products.id', 'products.title', 'products.stock')
22:             ->get();
23: 
24:         $mismatches = $rows->filter(fn ($row) => (int) $row->stock !== (int) $row->pivot_total);
```

[app/Console/Commands/AuditOutletCommand.php — AuditOutletCommand::auditStock(), 90–107](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Console/Commands/AuditOutletCommand.php#L90-L107). Bukti baris 95–101:

```php
95: 
96:         $mismatches = DB::table('products')
97:             ->leftJoin('product_warehouse', 'product_warehouse.product_id', '=', 'products.id')
98:             ->select('products.id')
99:             ->selectRaw('products.stock, COALESCE(SUM(product_warehouse.stock), 0) AS pivot_total')
100:             ->groupBy('products.id', 'products.stock')
101:             ->havingRaw('products.stock <> pivot_total')
```

[database/seeders/DemoDatabaseResetter.php — DemoDatabaseResetter::assertInventoryAggregate(), 138–155](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/database/seeders/DemoDatabaseResetter.php#L138-L155). Bukti baris 143–149:

```php
143: 
144:         $inconsistent = DB::table('products')
145:             ->leftJoin('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
146:             ->select('products.id', 'products.stock')
147:             ->selectRaw('COALESCE(SUM(product_warehouse.stock), 0) as warehouse_stock')
148:             ->groupBy('products.id', 'products.stock')
149:             ->havingRaw('products.stock != COALESCE(SUM(product_warehouse.stock), 0)')
```

**Masalah:** Duplicate Query dengan proyeksi berbeda. Tiga operasi membandingkan products.stock vs SUM pivot melalui join/group/having. Bug pada predicate harus dilacak di tiga tempat.

**Teknik dan alasan:** Extract Query Builder fungsi kecil; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: InventoryMismatchQuery mengembalikan builder; caller menentukan exists/count/list, query output dan fix sendiri.

**Invarian dan kriteria khusus selesai:** COALESCE untuk product tanpa pivot, table prefix/DB connection dan fix behavior tetap; query aggregate count harus dibuktikan ekuivalen. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tidak memanggil --fix pada database audit; bukan inti.

**Pengujian wajib:** T07/T21: produk tanpa pivot, satu/dua gudang, balanced/unbalanced; compare list/count/exists dan --fix terpisah hanya test DB. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-072"></a>

## RF-072 — Setup store menggabungkan validasi, user, outlet/gudang dan profil

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang–tinggi. **Estimasi:** 8–12 jam-orang.

[app/Http/Controllers/SetupController.php — SetupController::store(), 42–193](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/SetupController.php#L42-L193). Bukti baris 102–108:

```php
102: 
103:         DB::transaction(function () use ($validated) {
104:             $user = User::create([
105:                 'name' => $validated['user_name'],
106:                 'email' => $validated['user_email'],
107:                 'password' => Hash::make($validated['password']),
108:             ]);
```

[app/Http/Controllers/SetupController.php — SetupController::store(), 42–193](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/SetupController.php#L42-L193). Bukti baris 146–152:

```php
146: 
147:             foreach ($validated['branches'] as $i => $branch) {
148:                 $branchOutlet = Outlet::create([
149:                     'code' => strtoupper($branch['outlet_code']),
150:                     'name' => $branch['outlet_name'],
151:                     'is_active' => true,
152:                     'is_sales_enabled' => true,
```

**Masalah:** Long Method; Divergent Change. store152 baris merangkap onboarding bisnis, admin, gudang pusat, cabang, kategori dan setting; upload logo dilakukan sesudah transaksi.

**Teknik dan alasan:** Extract FormRequest; Extract Methods; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: SetupRequest dan SetupInstaller dengan createAdmin/setupCentralWarehouse/createBranches/saveSettings; boundary transaksi dan logo sesudah commit dipertahankan.

**Invarian dan kriteria khusus selesai:** PUSAT sales disabled, default outlet assignment, uppercase branch code, kategori unique, app_setup_completed serta redirect login sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Memindah upload masuk transaksi/cleanup merupakan perubahan perilaku; jangan lakukan dalam refactor.

**Pengujian wajib:** T18: SetupWizardTest/OutletWizardTest, minimal cabang, max10, duplicate code, logo, install ulang ditolak dan rollback DB. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-073"></a>

## RF-073 — Wizard React menyatukan validasi langkah dan seluruh panel setup

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 8–12 jam-orang.

[resources/js/Pages/Setup/Wizard.jsx — Wizard, konteks 27–940](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Setup/Wizard.jsx#L27-L940). Bukti baris 26–32:

```jsx
26: 
27: export default function Wizard({ businessTypes, primaryWarehouse }) {
28:     const { t, i18n } = useTranslation();
29:     const { props } = usePage();
30:     const [step, setStep] = useState(0);
31: 
32:     useEffect(() => {
```

[resources/js/Pages/Setup/Wizard.jsx — Wizard, konteks 27–940](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Setup/Wizard.jsx#L27-L940). Bukti baris 191–197:

```jsx
191:         event?.stopPropagation();
192:         if (validateStep()) setStep((currentStep) => currentStep + 1);
193:     };
194: 
195:     const submit = () => {
196:         if (!validateStep()) return;
197: 
```

**Masalah:** Large Component; UI/Validation Coupling. 979 baris memuat state multistep, profil/admin/kategori/gudang/cabang dan submit.

**Teknik dan alasan:** Extract Component dan pure step validation; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: StoreStep, AdminStep, BusinessStep dan BranchStep; parent tetap satu useForm serta navigasi. Nilai field jangan direset saat unmount step.

**Invarian dan kriteria khusus selesai:** Urutan langkah, skip/back, default suggested category, server errors mapping serta final multipart payload identik. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tidak bergantung RF-072 bila payload dijaga, tetapi jangan ubah kontrak di dua sisi serentak tanpa karakterisasi.

**Pengujian wajib:** T18/T03: maju/mundur, invalid step, tambah/hapus cabang, logo dan final error kembali ke field; mobile screenshot. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-074"></a>

## RF-074 — Form pricing rule memuat editor untuk empat kind promo

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang–tinggi. **Estimasi:** 8–12 jam-orang.

[resources/js/Pages/Dashboard/PricingRules/Form.jsx — Form, konteks 55–844](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/PricingRules/Form.jsx#L55-L844). Bukti baris 54–60:

```jsx
54: 
55: export default function Form({
56:     mode = "create",
57:     rule = null,
58:     products = [],
59:     categories = [],
60:     tierOptions = [],
```

[resources/js/Pages/Dashboard/PricingRules/Form.jsx — Form, konteks 55–844](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/PricingRules/Form.jsx#L55-L844). Bukti baris 104–110:

```jsx
104:               ],
105:         buy_get_items: rule?.buy_get_items?.length
106:             ? rule.buy_get_items.map((item) => ({
107:                   product_id: String(item.product_id),
108:                   role: item.role,
109:                   quantity: String(item.quantity),
110:                   sort_order: String(item.sort_order ?? 0),
```

**Masalah:** Large Component; Conditional JSX Complexity. 844 baris memuat target/scope, tier, qty-break, bundle/buy-get rows dan preview; sebagian JSX buy/get sejenis.

**Teknik dan alasan:** Extract Component; Decompose Conditional; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: QtyBreakEditor, BundleItemsEditor dan BuyGetItemsEditor dengan role eksplisit; pertahankan container payload+preview.

**Invarian dan kriteria khusus selesai:** Saat ganti kind relasi mana yang reset/tetap, sort_order, preview result, member tier dan field error sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Koordinasi RF-016; file Form.jsx pemilik tunggal Anggota 3.

**Pengujian wajib:** T09/T03: create/edit empat kind, ganti kind kembali, tambah/hapus rows, invalid preview, save, error server dan kosong. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-075"></a>

## RF-075 — Adapter purchasing menggandakan validasi dan query dokumen

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 6–10 jam-orang.

[app/Http/Controllers/Apps/GoodsReceivingController.php — GoodsReceivingController::store(), 70–121](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/GoodsReceivingController.php#L70-L121). Bukti baris 71–77:

```php
71:     {
72:         $data = $request->validate([
73:             'purchase_order_id' => ['required', 'exists:purchase_orders,id'],
74:             'notes' => ['nullable', 'string', 'max:1000'],
75:             'items' => ['required', 'array', 'min:1'],
76:             'items.*.purchase_order_item_id' => ['required', 'exists:purchase_order_items,id'],
77:             'items.*.qty_received' => ['required', 'integer', 'min:1'],
```

[app/Http/Controllers/Api/V1/GoodsReceivingController.php — GoodsReceivingController::storeReceiving(), 73–122](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/GoodsReceivingController.php#L73-L122). Bukti baris 74–80:

```php
74:     {
75:         $data = $request->validate([
76:             'purchase_order_id' => ['required', 'exists:purchase_orders,id'],
77:             'notes' => ['nullable', 'string', 'max:1000'],
78:             'items' => ['required', 'array', 'min:1'],
79:             'items.*.purchase_order_item_id' => ['required', 'exists:purchase_order_items,id'],
80:             'items.*.qty_received' => ['required', 'integer', 'min:1'],
```

[app/Http/Controllers/Apps/SupplierReturnController.php — SupplierReturnController::store(), 78–102](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/SupplierReturnController.php#L78-L102). Bukti baris 79–85:

```php
79:     {
80:         $data = $request->validate([
81:             'supplier_id' => ['nullable', 'exists:suppliers,id'],
82:             'goods_receiving_id' => ['nullable', 'exists:goods_receivings,id'],
83:             'payable_id' => ['nullable', 'exists:payables,id'],
84:             'notes' => ['nullable', 'string', 'max:1000'],
85:             'items' => ['required', 'array', 'min:1'],
```

**Masalah:** Duplicate Validation/Query. PO, receiving, supplier return dan transfer sudah memakai service domain, tetapi adapter web/API masih menyalin rules item dan query daftar.

**Teknik dan alasan:** Extract FormRequest/Query per modul; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: mulai StoreGoodsReceivingRequest dan query list dengan filters; lanjut modul lain dalam commit terpisah. Jangan membuat purchasing controller generik.

**Invarian dan kriteria khusus selesai:** Semua aturan product_id/qty/batch/expiry, warehouse authorization, status code, payload JSON dan redirect tetap per adapter. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-050 hanya service receiving, tidak perlu bersamaan; Anggota 2 pemilik kedua adapter.

**Pengujian wajib:** T20: PO ordered, GR item salah, qty berlebih, batch expired input, supplier return/transfer gudang sama dan permission; V1InventoryApiTest. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-076"></a>

## RF-076 — CRUD item stock opname web/API mengulang pemeriksaan dan audit

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 5–8 jam-orang.

[app/Http/Controllers/Apps/StockOpnameController.php — StockOpnameController::updateItem(), 181–215](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/StockOpnameController.php#L181-L215). Bukti baris 186–192:

```php
186:         $this->ensureWarehouseAccess($request->user(), $stockOpname->warehouse_id);
187:         $this->ensureDraft($stockOpname);
188:         $this->ensureItemBelongsToOpname($stockOpname, $item);
189: 
190:         $validated = $request->validated();
191:         $physicalStock = $validated['physical_stock'] ?? null;
192:         $difference = $physicalStock !== null
```

[app/Http/Controllers/Api/V1/StockOpnameController.php — StockOpnameController::updateOpnameItem(), 155–191](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/StockOpnameController.php#L155-L191). Bukti baris 160–166:

```php
160:         $this->ensureWarehouseAccess($request->user(), $stockOpname->warehouse_id);
161:         $this->ensureDraft($stockOpname);
162: 
163:         $item = StockOpnameItem::findOrFail($item);
164:         $this->ensureItemBelongsToOpname($stockOpname, $item);
165: 
166:         $validated = $request->validated();
```

**Masalah:** Duplicate Business Logic. Store/update item, ensure belongs-to opname, physical stock difference dan audit berulang di dua adapter, terpisah dari operasi finalize.

**Teknik dan alasan:** Extract Action per item; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: updateOpnameItem action menerima opname/item/physical/reason/actor; route request dan response tetap di adapter.

**Invarian dan kriteria khusus selesai:** Null physical_stock, system_stock snapshot, difference, draft guard, ownership dan audit payload tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-013 menyentuh finalize saja; integrasikan item action lebih dulu bila modul dipilih.

**Pengujian wajib:** T07: item opname lain, finalized, physical null/0/negatif, unchanged dan audit reason; feature StockOpnameTest dan V1InventoryApiTest. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-077"></a>

## RF-077 — Voucher daftar/persist web/API mengulang scoping dan audit

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 5–8 jam-orang.

[app/Http/Controllers/Apps/CustomerVoucherController.php — CustomerVoucherController::index(), 22–64](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/CustomerVoucherController.php#L22-L64). Bukti baris 29–35:

```php
29:         $outlet = $this->outletAccessService->activeOutlet($request);
30:         $vouchers = CustomerVoucher::query()
31:             ->where(function ($query) use ($outlet) {
32:                 $query->whereNull('outlet_id');
33:                 if ($outlet) {
34:                     $query->orWhere('outlet_id', $outlet->id);
35:                 }
```

[app/Http/Controllers/Api/V1/CustomerVoucherController.php — CustomerVoucherController::listVouchers(), 19–58](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/CustomerVoucherController.php#L19-L58). Bukti baris 26–32:

```php
26:         $outlet = $this->outletAccessService->activeOutlet($request);
27:         $vouchers = CustomerVoucher::query()
28:             ->where(function ($query) use ($outlet) {
29:                 $query->whereNull('outlet_id');
30:                 if ($outlet) {
31:                     $query->orWhere('outlet_id', $outlet->id);
32:                 }
```

**Masalah:** Duplicate Query/Persistence. Filtering code/customer/status dan scope outlet disalin, begitu pula lifecycle update/audit; validateVoucher protected sudah reuse dan tidak perlu digandakan.

**Teknik dan alasan:** Extract Query; Extract Action; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: VoucherListQuery dahulu; action save untuk payload+audit jika kedua jalur benar-benar identik.

**Invarian dan kriteria khusus selesai:** Active filter saat ini tidak identik currentStatusLabel; jangan memperbaiki filter tanggal secara terselubung. Code generation, nullable scope, single use dan masks tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-047 inheritance cleanup baru sesudah action/query; tidak menambah voucher fitur.

**Pengujian wajib:** T09/T19: global/outlet, used/expired/scheduled, search customer, duplicate code, percentage>100 dan used voucher update baseline. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-078"></a>

## RF-078 — Serializer detail dan membership segment web/API berulang

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 4–7 jam-orang.

[app/Http/Controllers/Apps/CustomerSegmentController.php — CustomerSegmentController::show(), 65–97](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/CustomerSegmentController.php#L65-L97). Bukti baris 74–80:

```php
74:                 'stats' => $this->segmentationService->segmentStats($customerSegment),
75:                 'memberships' => $customerSegment->memberships
76:                     ->sortByDesc('matched_at')
77:                     ->values()
78:                     ->map(fn ($membership) => [
79:                         'id' => $membership->id,
80:                         'source' => $membership->source,
```

[app/Http/Controllers/Api/V1/CustomerSegmentController.php — CustomerSegmentController::showSegment(), 59–91](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/CustomerSegmentController.php#L59-L91). Bukti baris 68–74:

```php
68:                 'stats' => $this->segmentationService->segmentStats($customerSegment),
69:                 'memberships' => $customerSegment->memberships
70:                     ->sortByDesc('matched_at')
71:                     ->values()
72:                     ->map(fn ($membership) => [
73:                         'id' => $membership->id,
74:                         'source' => $membership->source,
```

**Masalah:** Duplicate Transformation. Membership customer, tanggal matched_at, stats dan ordering disusun pada dua adapter. Aturan validateSegment sudah shared melalui inheritance.

**Teknik dan alasan:** Extract Presenter; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: SegmentDetailsPresenter memberi array detail identik; options customers dan response wrapper tetap caller. Membership add/remove dapat action kecil berikutnya bila diperlukan.

**Invarian dan kriteria khusus selesai:** Sort matched_at desc, nullable customer, membership source, timestamp ISO dan sync auto saat GET daftar tetap baseline; menghapus side effect GET adalah behavior change. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Sesudah RF-053 bila mengubah service yang sama, tidak wajib untuk paket inti.

**Pengujian wajib:** T19: manual/auto, customer terhapus, matched_at null, order, add duplicate dan reject manual changes to auto. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-079"></a>

## RF-079 — DineTable web/API dan store/update menggandakan aturan meja

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 5–8 jam-orang.

[app/Http/Controllers/Apps/DineTableController.php — DineTableController::store(), 54–73](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/DineTableController.php#L54-L73). Bukti baris 55–61:

```php
55:     {
56:         $validated = $request->validate([
57:             'dine_area_id' => ['nullable', 'exists:dine_areas,id'],
58:             'name' => ['required', 'string', 'max:100'],
59:             'capacity' => ['integer', 'min:0'],
60:             'pos_x' => ['integer', 'min:0', 'max:24'],
61:             'pos_y' => ['integer', 'min:0', 'max:14'],
```

[app/Http/Controllers/Apps/DineTableController.php — DineTableController::update(), 75–93](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/DineTableController.php#L75-L93). Bukti baris 77–83:

```php
77:         abort_unless($this->ownsTable($dineTable, $request), 404);
78:         $validated = $request->validate([
79:             'dine_area_id' => ['nullable', 'exists:dine_areas,id'],
80:             'name' => ['required', 'string', 'max:100'],
81:             'capacity' => ['integer', 'min:0'],
82:             'pos_x' => ['integer', 'min:0', 'max:24'],
83:             'pos_y' => ['integer', 'min:0', 'max:14'],
```

[app/Http/Controllers/Api/V1/DineTableController.php — DineTableController::storeTable(), 61–80](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/DineTableController.php#L61-L80). Bukti baris 62–68:

```php
62:     {
63:         $validated = $request->validate([
64:             'dine_area_id' => ['nullable', 'exists:dine_areas,id'],
65:             'name' => ['required', 'string', 'max:100'],
66:             'capacity' => ['integer', 'min:0'],
67:             'pos_x' => ['integer', 'min:0', 'max:24'],
68:             'pos_y' => ['integer', 'min:0', 'max:14'],
```

**Masalah:** Duplicate Validation; Magic Grid Bounds. Nama/capacity, pos_x0–24, pos_y0–14, shape, sort dan active disalin; query outlet/area juga berulang.

**Teknik dan alasan:** Extract Rule Provider; Query Helper; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: DiningTableRules untuk kedua adapter/method, named grid bounds lokal yang dapat dipakai UI setelah diverifikasi. Query scope diekstrak terpisah.

**Invarian dan kriteria khusus selesai:** Token UUID hanya create, ownTable/area guard dan QR png headers tetap; tidak mengubah UI grid size. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-023 membahas stock dine-order, tidak sama modul meja; imagick test belum dapat dijalankan di audit.

**Pengujian wajib:** T13: batas x/y, shape invalid, area asing/null, create/update token tetap dan QR png jika imagick tersedia; DineTableManagementTest/V1DineInApiTest. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-080"></a>

## RF-080 — Pembuatan outlet menggandakan warehouse dan assignment

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang–tinggi. **Estimasi:** 6–10 jam-orang.

[app/Http/Controllers/Apps/OutletController.php — OutletController::store(), 30–65](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/OutletController.php#L30-L65). Bukti baris 34–40:

```php
34:         DB::transaction(function () use ($request, $data): void {
35:             $outlet = Outlet::create([
36:                 'code' => strtoupper($data['code']),
37:                 'name' => $data['name'],
38:                 'is_active' => true,
39:                 'is_sales_enabled' => (bool) ($data['is_sales_enabled'] ?? true),
40:                 'address' => $data['address'] ?? null,
```

[app/Http/Controllers/Api/V1/OutletController.php — OutletController::storeOutlet(), 34–71](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/OutletController.php#L34-L71). Bukti baris 38–44:

```php
38:         $outlet = DB::transaction(function () use ($request, $data): Outlet {
39:             $outlet = Outlet::create([
40:                 'code' => strtoupper($data['code']),
41:                 'name' => $data['name'],
42:                 'is_active' => true,
43:                 'is_sales_enabled' => (bool) ($data['is_sales_enabled'] ?? true),
44:                 'address' => $data['address'] ?? null,
```

**Masalah:** Duplicate Business Logic. Satu operasi create outlet+warehouse+zero pivot seluruh product+assignment ditulis ulang; update/disable guards pun berulang.

**Teknik dan alasan:** Extract Action; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: CreateOutletAction mengembalikan outlet dalam transaksi; update/destroy dipisah bila scope ditambah, tetap response per adapter.

**Invarian dan kriteria khusus selesai:** Uppercase code, type branch/warehouse, stock0 pivots, super-admin tidak auto-assign, is_default=true non-super dan rollback tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-072 setup punya aturan pusat/cabang berbeda, jangan langsung memakai action sama sebelum kontrak dibuktikan.

**Pengujian wajib:** T01/T18: create admin/non-admin, duplicate code, warehouse fail rollback, open shift mencegah disable, PUSAT sales disabled; OutletTest/V1AdminApiTest. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-081"></a>

## RF-081 — Query audit log web/API menggandakan filter akses

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 3–5 jam-orang.

[app/Http/Controllers/Apps/AuditLogController.php — AuditLogController::index(), 19–66](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/AuditLogController.php#L19-L66). Bukti baris 20–26:

```php
20:     {
21:         $filters = [
22:             'user_id' => $request->input('user_id'),
23:             'module' => $request->input('module'),
24:             'event' => $request->input('event'),
25:             'date_from' => $request->input('date_from'),
26:             'date_to' => $request->input('date_to'),
```

[app/Http/Controllers/Api/V1/AuditLogController.php — AuditLogController::listAuditLogs(), 20–67](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/AuditLogController.php#L20-L67). Bukti baris 21–27:

```php
21:     {
22:         $filters = [
23:             'user_id' => $request->input('user_id'),
24:             'module' => $request->input('module'),
25:             'event' => $request->input('event'),
26:             'date_from' => $request->input('date_from'),
27:             'date_to' => $request->input('date_to'),
```

**Masalah:** Duplicate Query; Authorization Coupling. visibleUserIds, filter event/module/user/range/search dan pagination disalin meski presenter akhir berbeda.

**Teknik dan alasan:** Extract Query; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: AuditLogListQuery menerima filters dan visible IDs; masking/permission tetap pada helper domain yang ada.

**Invarian dan kriteria khusus selesai:** Search OR dikelompokkan, ID kosong vs null, tanggal inclusive, urutan latest dan limit summary/filter options tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Jangan mencampur privacy/akses policy baru dengan pemindahan query.

**Pengujian wajib:** T01/T18: super/cashier, outlet shared user, semua filter dan injection-like search literal; AuditLogTest/V1AdminApiTest. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-082"></a>

## RF-082 — Daftar pending approval berulang web/API/middleware

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang–tinggi. **Estimasi:** 4–7 jam-orang.

[app/Http/Controllers/Apps/DiscountApprovalController.php — DiscountApprovalController::pending(), 21–53](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/DiscountApprovalController.php#L21-L53). Bukti baris 25–31:

```php
25: 
26:         $pending = Transaction::where('discount_approval_status', 'pending')
27:             ->where(function ($query) use ($warehouseIds, $includeLegacy) {
28:                 if ($warehouseIds->isEmpty()) {
29:                     $query->whereRaw('1 = 0');
30:                 } else {
31:                     $query->whereIn('warehouse_id', $warehouseIds);
```

[app/Http/Controllers/Api/V1/DiscountApprovalController.php — DiscountApprovalController::listPending(), 20–52](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/DiscountApprovalController.php#L20-L52). Bukti baris 24–30:

```php
24: 
25:         $pending = Transaction::where('discount_approval_status', 'pending')
26:             ->where(function ($query) use ($warehouseIds, $includeLegacy) {
27:                 if ($warehouseIds->isEmpty()) {
28:                     $query->whereRaw('1 = 0');
29:                 } else {
30:                     $query->whereIn('warehouse_id', $warehouseIds);
```

[app/Http/Middleware/HandleInertiaRequests.php — HandleInertiaRequests::share(), 33–290](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Middleware/HandleInertiaRequests.php#L33-L290). Bukti baris 57–63:

```php
57:             if ($request->user()->can('discounts-approve')) {
58:                 $pendingApprovalCount = Transaction::query()
59:                     ->where('discount_approval_status', 'pending')
60:                     ->where(function ($query) use ($warehouseIds, $includeLegacy) {
61:                         if ($warehouseIds->isEmpty()) {
62:                             $query->whereRaw('1 = 0');
63:                         } else {
```

**Masalah:** Duplicate Authorization Query. Aturan pending dan allowed warehouses + legacy/no access disalin untuk list dan count; divergence membuat badge dan list berbeda.

**Teknik dan alasan:** Extract Query Scope; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: PendingDiscountApprovalsQuery menerima IDs dan includeLegacy; list/count memakai builder yang sama. Approval settlement tidak diubah.

**Invarian dan kriteria khusus selesai:** Tidak ada warehouse→no results, legacy hanya satu outlet, sorting list, badge permission discounts-approve dan key data tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-044 menyentuh middleware sama; Anggota 2 edit helper+controller, Anggota 1 memegang integrasi file middleware.

**Pengujian wajib:** T01/T11/T18: no permission, empty IDs, multi outlet, null warehouse, pending vs approved; assert badge count=list count sesuai filter. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-083"></a>

## RF-083 — Form user Create/Edit menggandakan field dan pilihan outlet/role

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 5–8 jam-orang.

[resources/js/Pages/Dashboard/Users/Create.jsx — Create, konteks 15–232](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Users/Create.jsx#L15-L232). Bukti baris 22–28:

```jsx
22:         password_confirmation: "",
23:         selectedRoles: [],
24:         avatar: null,
25:         outlet_ids: [],
26:         default_outlet_id: null,
27:     });
28: 
```

[resources/js/Pages/Dashboard/Users/Edit.jsx — Edit, konteks 15–237](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Users/Edit.jsx#L15-L237). Bukti baris 22–28:

```jsx
22:         password_confirmation: "",
23:         selectedRoles: user.roles.map((role) => role.name),
24:         avatar: null,
25:         _method: "PUT",
26:         outlet_ids: user.outlets?.map((outlet) => outlet.id) ?? [],
27:         default_outlet_id: user.outlets?.find((outlet) => outlet.pivot?.is_default)?.id ?? null,
28:     });
```

**Masalah:** Duplicate JSX; Data Clumps. Blok name/email/password/role dan outlet assignments sama dengan initial/default dan submit berbeda.

**Teknik dan alasan:** Extract Component; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: UserFields dan OutletAssignmentsFields; form parent tetap useForm serta route post/put.

**Invarian dan kriteria khusus selesai:** Password required create/optional edit, confirmation, default outlet subset, avatar dan selectedRoles strings tetap; guard permission bukan hanya UI. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Berbeda file dengan RF-002; optional setelah paket inti frontend selesai.

**Pengujian wajib:** T01/T03: create/edit valid, password kosong edit, default outlet invalid, role multi, file invalid dan step-up challenge. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-084"></a>

## RF-084 — Form kategori Create/Edit menggandakan field dan image preview

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah. **Estimasi:** 3–5 jam-orang.

[resources/js/Pages/Dashboard/Categories/Create.jsx — Create, konteks 14–137](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Categories/Create.jsx#L14-L137). Bukti baris 24–30:

```jsx
24: 
25:     const handleImageChange = (e) => {
26:         const file = e.target.files[0];
27:         if (file) {
28:             setData("image", file);
29:             setImagePreview(URL.createObjectURL(file));
30:         }
```

[resources/js/Pages/Dashboard/Categories/Edit.jsx — Edit, konteks 14–140](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Categories/Edit.jsx#L14-L140). Bukti baris 26–32:

```jsx
26: 
27:     const handleImageChange = (e) => {
28:         const file = e.target.files[0];
29:         if (file) {
30:             setData("image", file);
31:             setImagePreview(URL.createObjectURL(file));
32:         }
```

**Masalah:** Duplicate JSX/Handlers. Field nama/deskripsi/gambar serta preview upload sama; mode submit dan image awal berbeda.

**Teknik dan alasan:** Extract Component; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: CategoryFields menerima form/errors/preview; pertahankan URL image awal dan method override di Edit.

**Invarian dan kriteria khusus selesai:** Nullable image/description dan field errors, upload MIME/ukuran, toast serta route sama; cleanup objectURL adalah bug/resource fix terpisah. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tidak memaksa form kategori ke abstraction produk.

**Pengujian wajib:** T02/T03: create tanpa gambar, edit retain/change image, valid/invalid file, server validation dan form reset. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-085"></a>

## RF-085 — Filter/tabel laporan Sales dan Profit menggandakan UI

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** rendah–sedang. **Estimasi:** 4–7 jam-orang.

[resources/js/Pages/Dashboard/Reports/Sales.jsx — Sales, konteks 45–52](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Reports/Sales.jsx#L45-L52). Bukti baris 45–51:

```jsx
45: 
46: const defaultFilterState = {
47:     start_date: "",
48:     end_date: "",
49:     invoice: "",
50:     cashier_id: "",
51:     customer_id: "",
```

[resources/js/Pages/Dashboard/Reports/Profit.jsx — Profit, konteks 41–48](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Reports/Profit.jsx#L41-L48). Bukti baris 41–47:

```jsx
41: 
42: const defaultFilters = {
43:     start_date: "",
44:     end_date: "",
45:     invoice: "",
46:     cashier_id: "",
47:     customer_id: "",
```

**Masalah:** Duplicate JSX/Filter State. Filter tanggal, cashier/customer dan common table rows berulang; summary omzet dan profit berbeda dan tetap khusus.

**Teknik dan alasan:** Extract ReportFilter Component; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: ReportFilters menerima model filter+options+onSubmit/reset; pertahankan summary dan kolom khusus laporan.

**Invarian dan kriteria khusus selesai:** Route reports.sales/profits yang aktual, preserveState/scroll, clear controls, warehouse query dan format tanggal/angka sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-038 backend filters dapat paralel dengan kontrak parameter tetap.

**Pengujian wajib:** T14 UI: pilih/ganti/reset filter, empty pages, query-string reload, modal options dan izin laporan. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-086"></a>

## RF-086 — Form pembayaran hutang/piutang UI menggandakan panel

**Pemilik:** Anggota 3. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang. **Estimasi:** 5–8 jam-orang.

[resources/js/Pages/Dashboard/Payables/Show.jsx — Show, konteks 31–506](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Payables/Show.jsx#L31-L506). Bukti baris 40–46:

```jsx
40:         method: "cash",
41:         bank_account_id: "",
42:         note: "",
43:     });
44:     const canPayPayable = can("payables-pay");
45: 
46:     useEffect(() => {
```

[resources/js/Pages/Dashboard/Receivables/Show.jsx — Show, konteks 21–594](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Receivables/Show.jsx#L21-L594). Bukti baris 30–36:

```jsx
30:         method: "cash",
31:         bank_account_id: "",
32:         note: "",
33:     });
34:     const collectionNotesForm = useForm({
35:         collection_notes: receivable.collection_notes || "",
36:     });
```

**Masalah:** Duplicate JSX/Validation State. Input amount/date/method/bank/note, submit loading serta tabel payment sangat mirip, label hutang/piutang dan route berbeda.

**Teknik dan alasan:** Extract Component; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: DebtPaymentForm menerima route, balance, bank options dan labels; tabel history dapat ekstrak terpisah. Tidak menambah domain abstraction backend.

**Invarian dan kriteria khusus selesai:** Batas amount, date default, bank required sesuai metode baseline, processing disable, error key dan redirect tetap; note/report khusus piutang tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Sesudah RF-007/008 terintegrasi agar diagnosis regresi terpisah.

**Pengujian wajib:** T05/T03: partial/exact/overpay, invalid date, bank null/selected, server403/422, disabled paid; ulangi setelah RF-007/008. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-087"></a>

## RF-087 — CRUD bank account menggandakan rules upload dan audit web/API

**Pemilik:** Anggota 1. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang–tinggi. **Estimasi:** 5–8 jam-orang.

[app/Http/Controllers/Apps/BankAccountController.php — BankAccountController::store(), 59–95](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/BankAccountController.php#L59-L95). Bukti baris 66–72:

```php
66:             'bank_name' => 'required|string|max:100',
67:             'account_number' => 'required|string|max:50',
68:             'account_name' => 'required|string|max:100',
69:             'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:1024',
70:             'is_active' => 'nullable|boolean',
71:         ]);
72: 
```

[app/Http/Controllers/Apps/BankAccountController.php — BankAccountController::update(), 100–140](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/BankAccountController.php#L100-L140). Bukti baris 110–116:

```php
110:             'bank_name' => 'required|string|max:100',
111:             'account_number' => 'required|string|max:50',
112:             'account_name' => 'required|string|max:100',
113:             'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:1024',
114:             'is_active' => 'nullable|boolean',
115:         ]);
116: 
```

[app/Http/Controllers/Api/V1/BankAccountController.php — BankAccountController::updateBankAccount(), 77–121](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/V1/BankAccountController.php#L77-L121). Bukti baris 88–94:

```php
88:             'bank_name' => ['required', 'string', 'max:100'],
89:             'account_number' => ['required', 'string', 'max:50'],
90:             'account_name' => ['required', 'string', 'max:100'],
91:             'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:1024'],
92:             'is_active' => ['nullable', 'boolean'],
93:         ]);
94: 
```

**Masalah:** Duplicate Validation/Persistence. Rules name/number/logo/active dan replace file→persist→audit tersebar di empat operasi; response adapter berbeda tetapi operasi file/domain sama.

**Teknik dan alasan:** Extract Rule Provider; Extract Update Action; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: BankAccountRules lalu SaveBankAccount action dengan image optional; pertahankan create sort_order dan masked audit.

**Invarian dan kriteria khusus selesai:** Logo retained saat absent, disk public, delete-before-write yang ada, outlet scoping, is_active coercion, sort order dan anti-delete used account sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Sesudah RF-046 bila keduanya dipilih; file controller bank pemilik Anggota 1.

**Pengujian wajib:** T01/T11/T18: create/update file/null, inactive, bank used-in-transaction, reorder, wrong outlet dan secret masking; Storage::fake. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-088"></a>

## RF-088 — DineOrder accept menulis sale, profit dan stok di satu metode

**Pemilik:** Anggota 2. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 10–16 jam-orang.

[app/Services/DineOrderService.php — DineOrderService::accept(), 22–212](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/DineOrderService.php#L22-L212). Bukti baris 57–63:

```php
57:             // ponytail: accepting a dine order is a sale — record a Transaction so revenue, profit, stock ledger and stock mutations all stay in sync with the POS path
58:             $transaction = Transaction::create([
59:                 'cashier_id' => $userId,
60:                 'cashier_shift_id' => $shift->id,
61:                 'warehouse_id' => $warehouseId,
62:                 'customer_id' => $order->customer_id,
63:                 'invoice' => 'TRX-'.strtoupper(Str::random(10)),
```

[app/Services/DineOrderService.php — DineOrderService::accept(), 22–212](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/DineOrderService.php#L22-L212). Bukti baris 97–103:

```php
97: 
98:                 $transaction->profits()->create([
99:                     'transaction_id' => $transaction->id,
100:                     'total' => $lineTotal - (int) $totalBuyPrice,
101:                 ]);
102: 
103:                 if ($product->is_composite) {
```

**Masalah:** Long Method; Mixed Responsibilities. accept191 baris menggabungkan context shift/outlet, pembuatan sale/detail/profit, inventory komposit/normal/batch dan status order. RF-023 hanya membahas clone batch, belum memisahkan seluruh operasi.

**Teknik dan alasan:** Extract Method bertahap; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: createTransactionForOrder, persistOrderLine dan deductInventory; re-use helper FEFO RF-023 hanya sesudah kontraknya diuji. Jangan paksa memakai CheckoutService.execute yang mengonsumsi Cart/loyalty dengan perilaku berbeda.

**Invarian dan kriteria khusus selesai:** Grand total dari order.subtotal, unpaid cash, order_type in_store, reference_type dine_order, conversion serta outlet match dan order status tetap. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-023 dapat didahulukan; tidak memindahkan alur dine ke POS cart atau menambah pajak/promo baru.

**Pengujian wajib:** T13/T07: accept normal/composite/multi-unit/batch, shift missing/wrong outlet, insufficient stock, reject dan transaksi rollback; DineOrderTest/V1DineInApiTest. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-089"></a>

## RF-089 — Submit pesanan dine publik mencampur validasi dan persiapan sale items

**Pemilik:** Anggota 2. **Scope:** Opsional. **Prioritas:** P2. **Risiko:** sedang–tinggi. **Estimasi:** 6–10 jam-orang.

[app/Http/Controllers/DineOrderController.php — DineOrderController::store(), 20–100](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/DineOrderController.php#L20-L100). Bukti baris 48–54:

```php
48: 
49:         foreach ($items as $item) {
50:             $product = $productModels->get($item['product_id']);
51:             if (! $product) {
52:                 continue;
53:             }
54: 
```

[app/Http/Controllers/DineOrderController.php — DineOrderController::store(), 20–100](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/DineOrderController.php#L20-L100). Bukti baris 80–86:

```php
80: 
81:         $order = DineOrder::create([
82:             'dine_table_id' => $table->id,
83:             'access_token' => (string) Str::uuid(),
84:             'status' => DineOrder::STATUS_SUBMITTED,
85:             'submitted_at' => now(),
86:             'payment_option' => $validated['payment_option'],
```

**Masalah:** Long Method; Business Logic di HTTP Adapter. store mencampur validasi meja/items, resolve unit, available stock, preview harga, subtotal, persist order+items dan redirect. Helper availableStock sudah ada dan tidak perlu diduplikasi.

**Teknik dan alasan:** Extract Method/Action; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: prepareOrderItems dan SubmitDineOrderAction; pertahankan validasi pay_at_counter, pricing previewProducts dan boundary persist lama. Menambah transaksi DB untuk mengatasi partial creation merupakan perubahan terpisah.

**Invarian dan kriteria khusus selesai:** Server price dan unit fallback1, item_count, item notes, UUID token, stock belum berkurang sampai accept dan redirect status sama. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-088 mengubah accept sesudah submit; keduanya harus diuji flow bersama jika ikut dikerjakan.

**Pengujian wajib:** T13: meja inactive/unknown, product hilang, unit unrelated baseline, shortage, multi items, pay_online rejected dan failure persist; DineOrderTest/DineOrderStatusTest. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-090"></a>

## RF-090 — Webhook provider menggandakan settlement tender/transaction

**Pemilik:** Anggota 2. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 10–16 jam-orang.

[app/Http/Controllers/Api/PaymentWebhookController.php — PaymentWebhookController::midtrans(), 19–125](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/PaymentWebhookController.php#L19-L125). Bukti baris 87–93:

```php
87: 
88:             if ($tender) {
89:                 $tender->update([
90:                     'payment_status' => $newStatus,
91:                     'payment_reference' => $request->input('transaction_id') ?: $tender->payment_reference,
92:                     'paid_at' => $newStatus === 'paid' ? now() : $tender->paid_at,
93:                 ]);
```

[app/Http/Controllers/Api/PaymentWebhookController.php — PaymentWebhookController::xendit(), 131–246](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/PaymentWebhookController.php#L131-L246). Bukti baris 208–214:

```php
208: 
209:             if ($tender) {
210:                 $tender->update([
211:                     'payment_status' => $newStatus,
212:                     'payment_reference' => $paymentId ?: $tender->payment_reference,
213:                     'paid_at' => $newStatus === 'paid' ? now() : $tender->paid_at,
214:                 ]);
```

**Masalah:** Duplicate Business Logic; Long Methods; Conditional Complexity. Midtrans107/Xendit116 baris punya verifikasi berbeda tetapi terminal guard, pencarian tender, paid_at/reference, aggregateStatus dan update transaction sangat mirip.

**Teknik dan alasan:** Extract Method/Action setelah verification; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN: applyVerifiedPaymentStatus menerima transaction, tender, normalizedStatus dan reference; signature/token verification, payload checks dan provider log tetap masing-masing.

**Invarian dan kriteria khusus selesai:** Validasi dilakukan sebelum mutation, duplicate terminal paid/failed diabaikan, invoice suffix match, payment_reference fallback dan error status/message sama. Lock/idempotency redesign tidak diselipkan. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** Tunda sampai characterization provider+race MySQL siap; RF-027 outbound payment bukan prerequisite tetapi kontrak reference harus sama.

**Pengujian wajib:** T11: dua provider/tender vs legacy, invalid signature/token, unconfigured, unknown invoice, duplicate/late/pending→paid/failed dan malformed payload; PaymentWebhookTest/SplitPaymentApiTest. Status test tambahan/sesudah perubahan: **belum dijalankan**.


<a id="rf-091"></a>

## RF-091 — Data migration bergantung pada model aplikasi yang terus berubah

**Pemilik:** Anggota 1. **Scope:** Tunda. **Prioritas:** P3. **Risiko:** tinggi. **Estimasi:** 8–12 jam-orang.

[database/migrations/2026_06_22_000009_add_tax_settings.php — 2026_06_22_000009_add_tax_settings::up(), 8–13](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/database/migrations/2026_06_22_000009_add_tax_settings.php#L8-L13). Bukti baris 9–15:

```php
9:     {
10:         Setting::set('store_npwp', '', 'NPWP Toko');
11:         Setting::set('store_nib', '', 'NIB Toko');
12:         Setting::set('tax_default_rate', '11.00', 'Default tarif PPN (%)');
13:     }
14: 
15:     public function down(): void
```

[database/migrations/2026_09_14_000002_add_outlet_id_to_warehouses_table.php — 2026_09_14_000002_add_outlet_id_to_warehouses_table::up(), 11–39](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/database/migrations/2026_09_14_000002_add_outlet_id_to_warehouses_table.php#L11-L39). Bukti baris 18–24:

```php
18: 
19:         $pusat = Warehouse::where('code', 'PUSAT')->first();
20: 
21:         if (! $pusat) {
22:             return;
23:         }
24: 
```

**Masalah:** Temporal Coupling; Coupling schema history/runtime Model. Migration historis memanggil Setting::set dan model Outlet/Warehouse. Perubahan helper/casts/event model di masa depan dapat memengaruhi replay fresh install untuk schema lama.

**Teknik dan alasan:** Decouple data migration dari runtime model, hanya bila replay terbukti sama; memisahkan bagian yang berubah/berulang pada bukti tersebut, sambil membatasi jumlah caller yang harus berubah.

**Perubahan konkret:** USULAN terbatas: query builder eksplisit dan payload schema versi migration, pada branch eksperimen dengan pengujian fresh+upgrade+rollback. Untuk tugas akademik ini, pertahankan migration historis; rekomendasi utama dokumentasikan coupling dan terapkan pola pada migration baru.

**Invarian dan kriteria khusus selesai:** Nilai default, timestamps model, updateOrCreate/firstOrCreate semantics, table prefix, existing row, down dan urutan install sama; jangan sekadar mengganti Setting::set dengan insert yang mengubah perilaku. Kesetaraan harus ditunjukkan pada test yang dipetakan berikut.

**Modul/file terdampak:** semua lokasi di atas beserta caller yang tercantum dalam perubahan; nama komponen/action baru adalah USULAN, belum merupakan file pada repositori. **Dependensi:** RF-057 model Setting memengaruhi replay; alasan penundaan kuat karena historical migration bukan scope inti.

**Pengujian wajib:** T18/T21: fresh100 migrations, upgrade snapshot pra-outlet, existing settings, rollback dan migrate ulang di SQLite/MySQL; bandingkan schema/data. Baseline fresh install telah lulus, eksperimen refactor belum. Status test tambahan/sesudah perubahan: **belum dijalankan**.
