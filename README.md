# SwanFlow - Personal Finance PWA

**SwanFlow** adalah aplikasi Progressive Web App (PWA) pencatatan dan pengelolaan keuangan pribadi modern, cepat, dan terintegrasi yang dirancang khusus dengan fokus pada pengalaman *mobile-first*.

---

## Fitur Utama

- **Dashboard Keuangan Real-Time**:
  - Saldo bersih (*Net Worth*) konsolidasi dari seluruh dompet/rekening.
  - Ringkasan pemasukan dan pengeluaran bulanan dengan kalkulasi komparasi bulan lalu.
  - Pintasan cepat transaksi, laporan, dompet, kalkulator, dan aktivitas.
- **Manajemen Multi-Dompet (*Wallets*)**:
  - Dukungan berbagai tipe dompet: Kas/Tunai, Rekening Bank, dan E-Wallet (GoPay, OVO, ShopeePay, DANA).
  - Transfer saldo antar dompet secara atomik dengan riwayat tercatat rapi.
- **Pencatatan Transaksi Komprehensif**:
  - Kategori dinamis dengan palet warna dan ikon khas.
  - Filter rentang waktu (hari ini, minggu ini, bulan ini, tahun ini, atau kustom).
  - Integrasi nota/deskripsi transaksi.
- **Kalkulator & Simulasi Finansial**:
  - **Simulasi Skenario**: Proyeksi saldo akhir berdasarkan rencana pos pemasukan/pengeluaran sebelum benar-benar dicatat.
  - **Hitung Patungan (*Split Bill*)**: Perhitungan bagi rata tagihan lengkap dengan pajak (PPN), *service charge*, diskon/promo, pembulatan rupiah, dan format salin teks siap kirim ke WhatsApp.
- **Aktivitas & To-Do List Finansial**:
  - Pencatatan tugas/deadline keuangan dengan prioritas dan tanggal jatuh tempo.
- **Anggaran Bulanan (*Budgets*)**:
  - Penetapan batas anggaran per kategori belanja dengan *progress bar* visual dan alert batas.
- **Langganan & Tagihan Rutin (*Subscriptions*)**:
  - Pelacakan tagihan berkala (bulanan/tahunan) dengan estimasi pengeluaran dan status pelunasan 1-tap.
- **Pencatatan Hutang & Piutang (*Debts & Receivables*)**:
  - Tracking jatuh tempo, pencatatan pembayaran parsial/lunas yang otomatis menyesuaikan saldo dompet.
- **Laporan & Analitik Keuangan**:
  - Visualisasi grafik arus kas dan distribusi pengeluaran per kategori.
- **Keamanan Berlapis**:
  - Autentikasi sandi, 6-Digit PIN cepat, dan otentikasi biometrik **Face ID / Touch ID (WebAuthn)**.

---

## Teknologi & Arsitektur

- **Backend**: [Laravel 12](https://laravel.com) (PHP 8.3+)
- **Database**: SQLite dengan WAL (*Write-Ahead Logging*) mode & composite indexing untuk konkurensi tinggi
- **Frontend**: Blade Templates, Tailwind CSS v4, Vanilla JavaScript modern
- **PWA**: Web App Manifest & Service Worker untuk pengalaman aplikasi mandiri (*standalone*)
- **Code Quality**: Laravel Pint formatter & PHPUnit Feature/Unit test suite (113 tests, 100% passed)

---

## Panduan Instalasi Lokal

### Prasyarat
- PHP >= 8.3 dengan ekstensi `pdo_sqlite`, `openssl`, `mbstring`
- Composer
- Node.js (v18+) & NPM

### Langkah-langkah
1. **Clone repositori**:
   ```bash
   git clone https://github.com/GustiSwandana/swanflow.git
   cd swanflow
   ```

2. **Install dependensi**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi Database & Seed Data**:
   ```bash
   touch database/database.sqlite
   php artisan migrate --seed
   ```

5. **Build Asset**:
   ```bash
   npm run build
   ```

6. **Jalankan Server**:
   ```bash
   php artisan serve
   ```
   Buka browser di `http://localhost:8000`.

---

## Lisensi

Aplikasi ini dikembangkan secara eksklusif dan dilisensikan di bawah lisensi personal.
