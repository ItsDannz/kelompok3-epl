# Bukti menjalankan baseline lokal

Repositori https://github.com/aryadwiputra/point-of-sales.git; branch main; commit81a91b8fc0ef479be9e0c751c0d11370d89dd8a2. Audit15 November2026 WIB. `git status --short` dan `git diff --exit-code` setelah setup/UI/test menghasilkan working tree tracked bersih. Tidak ada source refactoring/bug fix/commit implementasi.

## Lingkungan

- Host Fedora44, Node22.23.1, npm10.9.8, Python3.
- PHP/Composer awalnya tidak tersedia. PHP8.5.12 dari paket Fedora diekstrak ke /tmp untuk percobaan tanpa instalasi sistem. Composer menolak PhpSpreadsheet1.30.6 yang mensyaratkan PHP<8.5.
- Runtime final PHP8.4.15 CLI dari static-php-cli, Composer2.11.2 resmi getcomposer.org. Runtime berada di /tmp/pos-runtime; wrapper /tmp/pos-runtime/bin/php. Ini runtime audit lokal, bukan file project dan dapat hilang setelah /tmp dibersihkan.
- Ekstensi SQLite/PDO, mbstring, xml/dom, gd, zip, iconv dan bcmath tersedia. imagick tidak tersedia; satu test PNG QR dilewati.
- Backend terkunci: Laravel13.20.0, inertia-laravel3.1.1, Spatie Permission8.3.0, PHPUnit12.5.31, PhpSpreadsheet1.30.6. Detail backend-locked.json.
- Frontend lock: React19.2.7, @inertiajs/react3.6.1, Vite5.4.21. Resolusi instalasi aktual pada frontend-installed.json.
- WhatsApp lock: express4.22.2, whatsapp-web.js1.34.7. Tidak mengunduh browser Puppeteer; tidak menghubungkan perangkat atau mengirim pesan.
- .env lokal (ignored) menyalin .env.example, DB_CONNECTION=sqlite, DB_DATABASE=/home/zdann/Documents/ChatGPT/epl/tmp/pos-audit.sqlite, APP_URL=http://127.0.0.1:8000, INERTIA_SSR_ENABLED=false. APP_KEY dibuat lokal. Semua gateway/API key dibiarkan kosong.
- Test memakai DB in-memory sesuai phpunit.xml, array cache/session dan queue sync. Tidak memakai database demo untuk PHPUnit.

## Perintah dan hasil aktual

Clone memerlukan akses jaringan setelah percobaan sandbox gagal DNS; clone berikut berhasil. Tidak ada kode yang diambil hanya dari README.

```bash
composer install --no-interaction --prefer-dist
PUPPETEER_SKIP_DOWNLOAD=true npm ci --no-audit --no-fund
```

Composer berhasil dengan runtime PHP8.4.15. `npm ci` gagal exit1:

```text
npm ci can only install packages when your package.json and package-lock.json are in sync
Missing: concurrently@10.0.7 from lock file
Missing: driver.js@1.14.0 from lock file
Missing: chalk@5.6.2 from lock file
Missing: rxjs@7.8.2 from lock file
Missing: shell-quote@1.12.0 from lock file
Missing: supports-color@10.2.2 from lock file
Missing: tree-kill@1.2.2 from lock file
```

Workaround audit yang benar-benar dijalankan, tidak menulis lockfile/manifest:

```bash
PUPPETEER_SKIP_DOWNLOAD=true npm install --package-lock=false --no-audit --no-fund
npm run build
php artisan key:generate --no-interaction
php artisan migrate --seed --no-interaction
php artisan storage:link
php artisan seed:demo --force
php artisan serve --host=127.0.0.1 --port=8000
```

DB audit adalah SQLite baru yang dibuat khusus di workspace tmp. Demo seeder menghapus/mengisi ulang data hanya pada DB ini; tidak ada DB produksi/remote yang digunakan. `composer run dev` tidak dipakai sebagai bukti semua worker berjalan; runtime audit memakai built assets+artisan serve. Queue worker/pail/scheduler/SSR tidak diuji secara live. Jadwal dibaca dari routes/console.php.

Test baseline membutuhkan memory_limit diwariskan ke subprocess:

```bash
PHPRC=/tmp/pos-runtime/runtime84.ini PATH=/tmp/pos-runtime/bin:$PATH \
  php artisan test --log-junit ../output/audit-pos/phpunit-baseline.xml
```

runtime84.ini berisi `memory_limit=1G`. Hasil580 passed,1 skipped,2452 assertions,36.30s. Upaya awal128M dan upaya memperbesar parent saja kehabisan memori, tercatat pada log terpisah. Ini kendala runtime audit, bukan bukti memory leak project.

## Smoke UI yang diamati

1. Halaman awal tampil di browser lokal.
2. Login akun demo `arya@gmail.com` / `password` (akun disposable bawaan demo, bukan kredensial pengguna nyata).
3. Halaman POS meminta buka shift; pilih WH-MAL, modal awal100000.
4. Buka shift berhasil. Tutup guided tour, tambahkan Aqua Botol600ml qty1.
5. Harga dasar4500, promo450, line4050, PPN446, total4496. Tanpa customer tombol Pilih Pelanggan disabled (dicatat BF-002).
6. Pilih Fitri Lestari (data demo), masukkan tunai5000; kembalian504.
7. Selesaikan transaksi; invoice TRX-PWOAHTTS1G tampil Lunas/Tunai dengan angka sama.
8. SQLite menunjukkan mutation transaction qty1 stock_before50→stock_after49, product aggregate=SUM(pivot), mismatch0.

Halaman produk/edit, piutang, hutang, insights dan dine tables juga berhasil dibuka tanpa submit CRUD tambahan; bukti ui-product-edit.*, ui-receivables.*, ui-payables.*, ui-insights.*, ui-dine-tables.*. Ini smoke rendering, bukan validasi seluruh operasi.

Bukti: ui-pos-baseline.png, ui-invoice-baseline.png dan ui-db-evidence.json. Gambar adalah runtime aplikasi lokal, bukan screenshot README. Data contoh dan total ini membuktikan satu alur berhasil, bukan semua modul sudah diuji manual.

## WhatsApp service

```bash
cd whatsapp-service
PUPPETEER_SKIP_DOWNLOAD=true npm ci --no-audit --no-fund
npm start
curl -i http://127.0.0.1:3001/status
```

Instalasi berhasil300 packages; server localhost3001 berjalan. GET /status tanpa WA_SERVICE_TOKEN mengembalikan HTTP503 dan `{"status":false,"reason":"service token not configured"}`. Tidak memanggil /start,/send,/disconnect terhadap perangkat nyata. Auth invalid401, QR/client lifecycle dan delivery belum diuji; direncanakan fake di T19.

## Membuka ulang pada lingkungan audit ini

Server dapat dijalankan lagi dari checkout dengan PATH wrapper di atas; port8000 untuk Laravel dan3001 untuk Node. Server dihentikan setelah pengambilan bukti agar tidak meninggalkan proses audit. Runtime /tmp bukan setup portable final. Untuk sistem hasil tugas gunakan PHP8.3/8.4+ekstensi, Composer/npm yang terdokumentasi, lockfile konsisten, DB lokal sendiri, .env.example dan prosedur README yang telah diverifikasi; jangan menyalin .env atau database berisi kredensial nyata.

## Baseline source

Arsip `baseline-81a91b8.tar.gz` dibuat dengan git archive HEAD, tidak berisi .env/vendor/node_modules/database audit/build. SHA256:

```text
4a8a3d13ff2d2d9ab93ef9eb5fab50cabd771dea6c98634840beaa699e803fa5
```

Belum ada versi source sesudah refactoring. Simpan hash commit baseline untuk membandingkan diff, bukan hanya membandingkan dua folder yang bisa berubah tanpa riwayat.
