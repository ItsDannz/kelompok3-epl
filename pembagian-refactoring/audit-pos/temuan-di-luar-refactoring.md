# Temuan di luar refactoring murni

Semua item ini terpisah dari RF-001–RF-091. Hanya status yang menyebut observasi runtime merupakan hasil uji aktual. Indikasi statik belum membuktikan frekuensi atau dampak pada data produksi; test reproduksi yang diusulkan belum dijalankan. Setiap item punya satu pemilik triase. Tidak ada perbaikan yang diterapkan.

## ENV-001 — Manifest dan lockfile frontend tidak sinkron

**Jenis/status:** Reproducibility/setup fix. **Pemilik triase:** Anggota 3.

npm ci benar-benar gagal dengan Missing concurrently@10.0.7, driver.js@1.14.0 dan transitives. Install audit memakai --package-lock=false; kondisi dependency tidak sepenuhnya reproducible dari lock saja. Tidak ada manifest/lock yang diperbaiki.

[package.json — scripts/dependencies, konteks 14–21](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/package.json#L14-L21). Bukti baris 14–20:

```php
14:         "axios": "^1.6.4",
15:         "concurrently": "^10.0.3",
16:         "laravel-vite-plugin": "^1.0",
17:         "postcss": "^8.4.31",
18:         "react": "^19.0",
19:         "react-dom": "^19.0",
20:         "tailwindcss": "^3.2.1",
```

**Validasi/keputusan berikutnya:** Commit maintenance tersendiri kelak: regenerate package-lock sesuai npm yang disepakati, review perubahan package, lalu clean npm ci+build+suite. Jangan menyebutnya refactoring source.

## BF-001 — setCarts tidak dideklarasikan dalam halaman POS

**Jenis/status:** Bug statik terkonfirmasi; cabang offline belum dieksekusi. **Pemilik triase:** Anggota 3.

Callback queueTransaction memanggil setCarts, sedangkan carts berasal dari props dan tidak ada deklarasi setter di seluruh file. Saat callback ini berjalan akan terjadi ReferenceError setelah queue tersimpan.

[resources/js/Pages/Dashboard/Transactions/Index.jsx — Index/handleSubmitTransaction, konteks 53–2081](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Transactions/Index.jsx#L53-L2081). Bukti baris 769–775:

```jsx
769:                 refreshPendingCount();
770:                 setCarts([]);
771:                 setPricingPreview(initialPricingPreview);
772:                 toast.success("Transaksi disimpan offline. Akan dikirim saat online.");
773:             });
774:             setIsSubmitting(false);
775:             return;
```

**Validasi/keputusan berikutnya:** T15: queue berhasil lalu pastikan UI/antrean; reproduksi offline dahulu. Tentukan cara clear server/local cart tanpa kehilangan sale; jangan campurkan koreksi dengan RF-029/031.

## BF-002 — UI menolak walk-in walau backend mengizinkan

**Jenis/status:** Bug ketidaksesuaian UI/API; kondisi tombol diamati langsung. **Pemilik triase:** Anggota 3.

UI menampilkan tombol disabled Pilih Pelanggan untuk cart tanpa customer, juga F2 mensyaratkan customer. Backend dan WalkInCustomerTest mengizinkan customer null untuk pembayaran biasa.

[resources/js/Pages/Dashboard/Transactions/Index.jsx — Index/submit disabled, konteks 53–2081](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Transactions/Index.jsx#L53-L2081). Bukti baris 1984–1990:

```jsx
1984:                                 !carts.length ||
1985:                                 !selectedCustomer ||
1986:                                 (!payLater &&
1987:                                     paymentMethod === "cash" &&
1988:                                     cash < payable) ||
1989:                                 isLoadingPricing ||
1990:                                 isSubmitting
```

[tests/Feature/Transactions/WalkInCustomerTest.php — WalkInCustomerTest::test_checkout_without_customer_succeeds(), 33–63](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/tests/Feature/Transactions/WalkInCustomerTest.php#L33-L63). Bukti baris 32–38:

```php
32: 
33:     public function test_checkout_without_customer_succeeds(): void
34:     {
35:         $cashier = $this->createCashier();
36:         $this->openShiftFor($cashier);
37:         $product = $this->createProduct();
38: 
```

**Validasi/keputusan berikutnya:** T12: cash tanpa customer via UI, bandingkan API; pay_later tetap wajib customer. Perubahan enable button/hotkey adalah bug fix, bukan RF-030.

## BF-003 — Scope walk_in price list cocok tanpa memeriksa customer

**Jenis/status:** Indikasi bug bisnis dari kode; belum direproduksi. **Pemilik triase:** Anggota 1.

Cabang walk_in mengembalikan list tanpa kondisi $customer===null, sementara registered/member memeriksa customer. Potensi list walk-in menimpa harga customer terdaftar.

[app/Services/PriceListService.php — PriceListService::getApplicablePriceList(), 13–45](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/PriceListService.php#L13-L45). Bukti baris 27–33:

```php
27:             }
28:             if ($list->customer_scope === 'walk_in') {
29:                 return $list;
30:             }
31:             if ($list->customer_scope === 'registered' && $customer) {
32:                 return $list;
33:             }
```

**Validasi/keputusan berikutnya:** T09: list walk-in prioritas tinggi dan customer registered/member, bandingkan hasil dengan aturan yang disepakati. Koreksi predicate mengubah harga sehingga harus commit BF terpisah.

## BF-004 — Import produk menulis products.stock tanpa pivot

**Jenis/status:** Risiko pelanggaran invariant stok; belum direproduksi lewat file import. **Pemilik triase:** Anggota 1.

ProductsImport::model upsert stock aggregate tanpa menulis product_warehouse/mutation. Invariant operational pivot dapat berbeda, terutama produk existing.

[app/Imports/ProductsImport.php — ProductsImport::model(), 17–44](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Imports/ProductsImport.php#L17-L44). Bukti baris 36–42:

```php
36:                 'sell_price' => (int) ($row['harga_jual'] ?? 0),
37:                 'stock' => (int) ($row['stok'] ?? 0),
38:                 'min_stock' => (int) ($row['min_stok'] ?? 0),
39:                 'max_stock' => (int) ($row['max_stok'] ?? 0),
40:                 'tax_type' => $row['tipe_pajak'] ?? 'exclusive',
41:                 'tax_rate' => (float) ($row['tarif_pajak'] ?? 11.00),
42:             ]
```

**Validasi/keputusan berikutnya:** T22: import stock baru/existing, jalankan audit tanpa --fix; tentukan warehouse dan audit policy dulu. Memperkenalkan pivot write/warehouse input mengubah efek DB, bukan RF-069.

## BF-005 — API update produk menulis aggregate stock langsung

**Jenis/status:** Risiko integritas inventory; belum direproduksi. **Pemilik triase:** Anggota 1.

Rules update mengizinkan stock dan kemudian Product::update; tidak ada sinkronisasi pivot atau mutasi dalam method. Web update tidak mengizinkan update stok seperti jalur ini.

[app/Http/Controllers/Api/ProductController.php — ProductController::update(), 126–148](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/ProductController.php#L126-L148). Bukti baris 144–150:

```php
144: 
145:         $product->update($validated);
146: 
147:         return $this->ok(new ProductResource($product->load('category')), 'Produk berhasil diperbarui');
148:     }
149: 
150:     /**
```

**Validasi/keputusan berikutnya:** T02/T07: update API stock pada dua gudang, baca pivot/aggregate/mutation; tentukan kontrak adjustment sebelum fix. Tidak menyamakan validasi web/API dalam refactor.

## BF-006 — Receiving berikutnya dapat mereset saldo payable

**Jenis/status:** Indikasi bug finansial dari kode; belum direproduksi. **Pemilik triase:** Anggota 2.

updateOrCreate memakai purchase_order_id, total receipt saat ini dan paid=0/status unpaid. Receiving kedua pada PO yang sudah punya pembayaran berpotensi menghilangkan akumulasi total/paid.

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

**Validasi/keputusan berikutnya:** T20: receive4, bayar sebagian, receive6; assert expected cumulative ledger sesuai keputusan domain. Jangan menganggap RF-050 sudah memperbaiki hal ini.

## BF-007 — Permissions-Policy memblokir kamera dan WebUSB

**Jenis/status:** Konfigurasi berpotensi menghambat fitur; belum diuji dengan perangkat. **Pemilik triase:** Anggota 3.

Header camera=() dan usb=() diset pada semua response web, sedangkan scanner kamera dan thermal WebUSB dipakai frontend. Mengubah policy adalah perubahan perilaku/security configuration.

[app/Http/Middleware/SecureHeaders.php — SecureHeaders::handle(), 11–33](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Middleware/SecureHeaders.php#L11-L33). Bukti baris 19–25:

```php
19:             'Permissions-Policy',
20:             'camera=(), microphone=(), geolocation=(), payment=(), usb=(), accelerometer=(), gyroscope=()'
21:         );
22: 
23:         // HSTS only over HTTPS in production to avoid locking out plain-HTTP dev.
24:         if ($request->secure() && app()->environment('production')) {
25:             $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
```

**Validasi/keputusan berikutnya:** T17/T12: Chromium pada origin lokal, izin fitur kamera/printer serta header aktual; tetapkan allowlist sesuai kebutuhan. Bukan alasan menurunkan semua header keamanan saat refactoring.

## BF-008 — CRM membuat public invoice link tanpa token akses

**Jenis/status:** Indikasi bug link dari implementasi; belum diuji pengiriman. **Pemilik triase:** Anggota 1.

Pesan invoice memakai route transactions.public(invoice) tanpa ?token, sedangkan publicInvoice memfilter access_token. Link dapat berakhir404. Tidak ada pesan sungguhan dikirim dalam audit.

[app/Services/CrmAutomationService.php — CrmAutomationService::createInvoiceShareCampaignForTransaction(), 201–239](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/CrmAutomationService.php#L201-L239). Bukti baris 230–236:

```php
230:                 'payload' => [
231:                     'message' => 'Invoice '.$transaction->invoice.': '.route('transactions.public', $transaction->invoice, true),
232:                     'whatsapp_url' => 'https://wa.me/?text='.urlencode('Invoice '.$transaction->invoice.': '.route('transactions.public', $transaction->invoice, true)),
233:                     'invoice' => $transaction->invoice,
234:                 ],
235:             ]
236:         );
```

[app/Http/Controllers/DocumentController.php — DocumentController::publicInvoice(), 97–115](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/DocumentController.php#L97-L115). Bukti baris 102–108:

```php
102:             ->where('invoice', $invoice)
103:             ->where('access_token', $request->query('token'))
104:             ->firstOrFail();
105: 
106:         $pdf = Pdf::loadView('pdf.invoice', [
107:             'transaction' => $transaction,
108:             'store' => $this->storeProfile($transaction->warehouse?->outlet),
```

**Validasi/keputusan berikutnya:** T19/T17: bangun payload campaign di fake, buka link fixture valid/invalid. Tambahkan token hanya pada tujuan yang tepat dan jangan menghapus proteksi token.

## BF-009 — Beberapa failure sync dapat meninggalkan cart sementara

**Jenis/status:** Indikasi bug cleanup dari control flow; race belum diuji. **Pemilik triase:** Anggota 2.

syncOne menghapus createdCartIds pada response checkout gagal, tetapi return dari unique-constraint catch dan outer Throwable catch tidak selalu melewati deleteCarts. Cart dibuat sebelum transaksi checkout.

[app/Http/Controllers/Api/PosApiController.php — PosApiController::syncOne(), 768–920](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/PosApiController.php#L768-L920). Bukti baris 873–879:

```php
873:                 $response = $this->checkout($checkoutRequest, app(PaymentGatewayManager::class));
874:             } catch (UniqueConstraintViolationException $e) {
875:                 // Lost a race: another worker committed the same client_uuid
876:                 // between our pre-check and this checkout. Nothing was
877:                 // committed by us (the transaction rolled back atomically).
878:                 $existing = Transaction::where('client_uuid', $uuid)->first();
879: 
```

[app/Http/Controllers/Api/PosApiController.php — PosApiController::syncOne(), 768–920](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/PosApiController.php#L768-L920). Bukti baris 914–920:

```php
914:             return ['status' => 'failed', 'reason' => data_get($json, 'message', 'Checkout gagal.')];
915:         } catch (\Throwable $e) {
916:             report($e);
917: 
918:             return ['status' => 'failed', 'reason' => $e->getMessage()];
919:         }
920:     }
```

**Validasi/keputusan berikutnya:** T15: paksa exception setelah cart pertama dan unique collision, assert temporary carts dan live carts. Menambahkan finally/rollback akan memperbaiki efek kegagalan; commit terpisah dari RF-028.

## BF-010 — Offline scope key membaca warehouse_id yang tidak disediakan summary

**Jenis/status:** Indikasi bug isolasi queue dari kontrak field; belum diuji perpindahan. **Pemilik triase:** Anggota 3.

POS membaca activeCashierShift.warehouse_id, tetapi summarizeForDisplay menyediakan nested warehouse.id tanpa top-level warehouse_id. Scope bisa menjadi user:none pada gudang berbeda.

[resources/js/Pages/Dashboard/Transactions/Index.jsx — Index, konteks 53–2081](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Dashboard/Transactions/Index.jsx#L53-L2081). Bukti baris 73–79:

```jsx
73:     } = usePage().props;
74:     const offlineScopeKey = `${auth?.user?.id ?? "guest"}:${activeCashierShift?.warehouse_id ?? "none"}`;
75:     const { can } = useAuthorization();
76:     const canOpenShift = can("cashier-shifts-open");
77: 
78:     // State
79:     const [searchQuery, setSearchQuery] = useState("");
```

[app/Services/CashierShiftService.php — CashierShiftService::summarizeForDisplay(), 219–244](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/CashierShiftService.php#L219-L244). Bukti baris 232–238:

```php
232:             'notes' => $shift->notes,
233:             'warehouse' => $shift->warehouse ? [
234:                 'id' => $shift->warehouse->id,
235:                 'code' => $shift->warehouse->code,
236:                 'name' => $shift->warehouse->name,
237:             ] : null,
238:             'user' => $shift->user ? [
```

**Validasi/keputusan berikutnya:** T15: user sama shiftA laluB, assert antrean terisolasi; legacy null scope harus tetap diputuskan. Mengubah key serta migrasi existing queues merupakan bug/data compatibility fix.

## BF-011 — Tax inclusive berpotensi ditambahkan kembali dalam grand total

**Jenis/status:** Indikasi bug perhitungan; belum diuji matriks kombinasi. **Pemilik triase:** Anggota 1.

TaxService menghitung bagian pajak yang sudah termasuk lineTotal, lalu loyalty preview menambahkan total tax ke baseGrandTotal. Perlu membedakan tax included vs tax to add sebelum menyatakan aturan total benar.

[app/Services/TaxService.php — TaxService::calculateLineItem(), 15–40](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/TaxService.php#L15-L40). Bukti baris 25–31:

```php
25: 
26:         if ($taxType === 'inclusive') {
27:             $taxAmount = (int) round($lineTotal - ($lineTotal / (1 + $taxRate / 100)));
28:             $beforeTax = $lineTotal - $taxAmount;
29:         } else {
30:             $taxAmount = (int) round($lineTotal * $taxRate / 100);
31:             $beforeTax = $lineTotal;
```

[app/Services/LoyaltyService.php — LoyaltyService::previewCheckout(), 173–286](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/LoyaltyService.php#L173-L286). Bukti baris 240–246:

```php
240: 
241:         $grandTotal = $baseGrandTotal + $taxTotal;
242:         $pointsEarnedPreview = $this->calculateEarnPoints(
243:             $customer,
244:             max(0, $grandTotal - $shippingCost),
245:             $settings
246:         );
```

**Validasi/keputusan berikutnya:** T10: satu item inclusive11100 tax11 tanpa diskon/ongkir serta exclusive10000, lalu kombinasi; sepakati expected invoice11100 untuk inclusive bila kontrak bisnis itu berlaku. Koreksi rumus bukan RF-019.

## BF-012 — Summary kas split memakai cash_received tanpa mengurangi change

**Jenis/status:** Indikasi bug selisih kas; belum direproduksi. **Pemilik triase:** Anggota 2.

Cash tender untuk split dijumlah dari cash_received dan langsung menambah expectedCash, sedangkan change pada tender tidak dikurangkan dalam method. Cash received100000 untuk tender60000 dengan change40000 berpotensi melebihkan saldo laci.

[app/Services/CashierShiftService.php — CashierShiftService::calculateSummary(), 77–141](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/CashierShiftService.php#L77-L141). Bukti baris 98–104:

```php
98:             ->where('method', TransactionTender::METHOD_CASH)
99:             ->sum('cash_received');
100: 
101:         $cashSalesTotal += $splitCashTenderTotal;
102: 
103:         $nonCashSalesTotal = (int) (clone $transactions)
104:             ->where('payment_method', '!=', 'cash')
```

[app/Services/CashierShiftService.php — CashierShiftService::calculateSummary(), 77–141](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Services/CashierShiftService.php#L77-L141). Bukti baris 127–133:

```php
127: 
128:         $expectedCash = (int) $shift->opening_cash + $cashSalesTotal - $cashRefundTotal + $cashInTotal - $cashOutTotal;
129: 
130:         return [
131:             'cash_sales_total' => $cashSalesTotal,
132:             'non_cash_sales_total' => $nonCashSalesTotal,
133:             'cash_refund_total' => $cashRefundTotal,
```

**Validasi/keputusan berikutnya:** T11: total100000 = cash60000+bank40000, cash_received100000/change40000; compare drawer expected net60000 dengan baseline. Perlakukan sebagai perbaikan hitungan, bukan pemindahan summary.

## BF-013 — Cart add menggabungkan produk tanpa mempertimbangkan unit

**Jenis/status:** Indikasi bug multiunit; belum direproduksi kombinasi PCS/BOX. **Pemilik triase:** Anggota 2.

Pencarian cart existing memakai product_id+cashier_id+active tanpa unit_id; penambahan unit lain dapat menaikkan qty/harga pada baris dengan unit/conversion lama.

[app/Http/Controllers/Apps/TransactionController.php — TransactionController::addToCart(), 252–335](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Apps/TransactionController.php#L252-L335). Bukti baris 311–317:

```php
311: 
312:         $cart = Cart::with('product')
313:             ->where('product_id', $validated['product_id'])
314:             ->where('cashier_id', auth()->user()->id)
315:             ->active()
316:             ->first();
317: 
```

[app/Http/Controllers/Api/PosApiController.php — PosApiController::addToCart(), 269–351](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/app/Http/Controllers/Api/PosApiController.php#L269-L351). Bukti baris 324–330:

```php
324: 
325:         $cart = Cart::with('product')
326:             ->where('product_id', $validated['product_id'])
327:             ->where('cashier_id', $request->user()->id)
328:             ->active()
329:             ->first();
330: 
```

**Validasi/keputusan berikutnya:** T12: add1PCS lalu1BOX conversion12 untuk produk sama; capture qty/unit/price/checkout stock sebelum mengusulkan aturan merge. Mengubah key penggabungan adalah bug fix, bukan RF-024.

## DOC-001 — Teks lisensi landing page berbeda dengan manifest/README/LICENSE

**Jenis/status:** Inkonsistensi dokumentasi produk; bukan refactoring. **Pemilik triase:** Anggota 3.

Landing page lokal menampilkan MIT, sedangkan composer.json menyatakan AGPL-3.0-only dan README/LICENSE memakai GNU AGPL. Ini observasi konsistensi konten, bukan penafsiran hukum lisensi.

[resources/js/Pages/Welcome.jsx — Welcome/marketing, konteks 29–36](https://github.com/aryadwiputra/point-of-sales/blob/81a91b8fc0ef479be9e0c751c0d11370d89dd8a2/resources/js/Pages/Welcome.jsx#L29-L36). Bukti baris 29–35:

```jsx
29:     { value: "44+", label: "Fitur Lengkap" },
30:     { value: "MIT", label: "Open Source" },
31:     { value: "8", label: "Modul Terintegrasi" },
32: ];
33: 
34: const features = [
35:     {
```

**Validasi/keputusan berikutnya:** Pemilik repo menetapkan teks yang benar lalu update konten/dokumentasi sebagai commit terpisah; jangan mengganti LICENSE hanya untuk menyamakan UI.

## QA-001 — Frontend dan Node belum punya suite test khusus

package.json root hanya dev/build; whatsapp-service hanya start; tidak ada file test JS/JSX yang ditrack. PHPUnit melindungi backend dan props HTTP, bukan perilaku state/effect/IndexedDB/printer. Penambahan characterization test/Node test pada utility yang diekstrak adalah pekerjaan testing pendukung RF, bukan refactoring kode itu sendiri. Pemilik: Anggota 3; setiap pemilik tetap menguji modulnya.

## OPT-001 — Optimasi query harus dipisahkan dari restrukturisasi

Potensi N+1 tampak pada PriceListService.getApplicablePriceList/getProductPrice per produk, PricingService.calculateProductPrice memanggil basePriceFor beberapa kali, PosApiController.products membaca stock warehouse per item; shared props membaca settings berulang. Belum ada pengukuran query count/latency di audit, sehingga tidak diklaim besaran dampak. Pemilik triase: Anggota 1 untuk pricing/settings, Anggota 2 untuk POS. Ambil DB query log pada fixture10/100 produk, bandingkan eager load/batching/caching dengan output dan invalidation yang sama sebelum menjadikannya proyek optimasi.

## FIT-001 — Fitur baru dan perubahan arsitektur

Dine-in pay_online saat ini ditolak oleh rules; mendukungnya adalah fitur baru. Pindah campaign ke queue/outbox, mengganti seluruh status string ke enum, repository universal, menyatukan frontend/backend receipt contract atau sequence table dokumen adalah perubahan arsitektur/kontrak yang memerlukan keputusan dan test berbeda. Tidak masuk paket inti. Pemilik triase: Anggota 2 (payment/sequence), Anggota 1 (campaign), Anggota 3 (receipt UI). Tidak ada fitur baru yang dibuat pada audit ini.
