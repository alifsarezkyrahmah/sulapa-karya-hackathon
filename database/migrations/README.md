# Database Schema SulapaKarya — Versi Final (ERD v3)
# ====================================================


## CARA PAKAI FILE-FILE INI

### Langkah 1: Pastikan MySQL sudah jalan
```bash
# Cek MySQL sudah aktif
mysql -u root -p
# Buat database baru
CREATE DATABASE sulapakarya;
EXIT;
```

### Langkah 2: Setting .env di project Laravel
Buka file `.env` di root project Laravel, cari bagian DB, ubah jadi:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sulapakarya
DB_USERNAME=root
DB_PASSWORD=password_kamu_di_sini
```

### Langkah 3: Copy file-file ke project Laravel
```
File migration (7 file .php yang ada angka tanggal)
→ Copy ke: project-laravel/database/migrations/

InventorySeeder.php
→ Copy ke: project-laravel/database/seeders/

sulapakarya_config.php
→ Copy ke: project-laravel/config/sulapakarya.php
```

PENTING: Kalau di folder migrations sudah ada file `create_users_table.php` bawaan
Laravel, HAPUS dulu file bawaan itu, lalu ganti dengan file users dari sini.

### Langkah 4: Jalankan migration
```bash
php artisan migrate
```
Ini akan otomatis membuat 7 tabel + 3 tabel bawaan Laravel (password_resets, sessions, dll).

### Langkah 5: Jalankan seeder
```bash
php artisan db:seed --class=InventorySeeder
```
Ini mengisi tabel inventory_stocks dengan 5 baris awal (stok kosong, siap diisi).

### Langkah 6: Verifikasi
```bash
php artisan tinker
>>> \DB::table('users')->get();        # Harusnya kosong []
>>> \DB::table('inventory_stocks')->get();  # Harusnya ada 5 baris
```


## DAFTAR 7 TABEL

| No | Tabel              | Fungsi                                          | Siapa yang isi data    |
|----|--------------------|-------------------------------------------------|------------------------|
| 1  | users              | Akun login (user, admin, artisan)               | User saat register     |
| 2  | deposits           | Setoran sampah dari user                        | User submit form       |
| 3  | inventory_stocks   | Stok bahan baku di gudang                       | Otomatis dari logic    |
| 4  | artisans           | Extension data untuk user bertipe artisan       | Admin daftarkan        |
| 5  | memberships        | Riwayat langganan pengrajin (3 tier)            | Admin update           |
| 6  | material_requests  | Permintaan bahan baku oleh pengrajin (gratis)   | Pengrajin request      |
| 7  | products           | Katalog produk di marketplace                   | Admin input            |


## ALUR DATA LENGKAP

### Alur 1: User Setor Sampah
```
User isi form setor
  → INSERT ke deposits (status: pending)
  → deposit_code unik di-generate

User jadwalkan penjemputan
  → UPDATE deposits SET status = 'scheduled', pickup_date = '2026-06-20'

Petugas jemput
  → UPDATE deposits SET status = 'picked_up'

Admin scan QR → verifikasi
  → UPDATE deposits SET status = 'verified', actual_weight = 0.55

Admin approve reward
  → Hitung: 0.55 kg × Rp 1.600 × 10 = 8.800 poin
  → UPDATE deposits SET status = 'completed', points_earned = 8800
  → UPDATE users SET points_balance = points_balance + 8800
  → UPDATE inventory_stocks SET quantity_kg = quantity_kg + 0.55
    (WHERE category = 'plastik' AND sub_category = 'PET/HDPE')
```

### Alur 2: Pengrajin Ambil Bahan Baku
```
Pengrajin login → lihat stok tersedia di dashboard
  → SELECT * FROM inventory_stocks WHERE quantity_kg > 0

Pengrajin submit request: "Saya mau 5 kg Kain Perca"
  → INSERT material_requests (status: pending)

Admin review → approve
  → UPDATE material_requests SET status = 'approved', actual_quantity_kg = 5
  → UPDATE inventory_stocks SET quantity_kg = quantity_kg - 5
    (WHERE category = 'kain' AND sub_category = 'Kain Perca')

Pengrajin ambil fisik
  → UPDATE material_requests SET status = 'picked_up'
```

### Alur 3: Produk di Marketplace
```
Admin input produk baru
  → INSERT products (artisan_id = 1, name = 'Tote Bag', price = 35000, ...)

Pembeli lihat marketplace
  → SELECT * FROM products WHERE status = 'available'

Pembeli klik "Hubungi Pengrajin"
  → Sistem ambil: products.artisan_id → artisans.user_id → users.phone
  → Redirect ke: wa.me/628xxx?text=Halo saya mau beli Tote Bag ...

(Tidak ada transaksi di database — semua via WhatsApp)
```

### Alur 4: Membership Pengrajin
```
Pengrajin baru didaftarkan admin
  → INSERT users (role: artisan)
  → INSERT artisans (user_id: ..., status: active)
  → INSERT memberships (tier: pemula, price: 0, started_at: today, expires_at: today + 30)

Pengrajin upgrade ke Aktif
  → Bayar Rp 35.000 via transfer manual
  → Admin UPDATE memberships lama SET status = 'expired'
  → Admin INSERT memberships baru (tier: aktif, price: 35000)
```


## RUMUS PERHITUNGAN POIN

```
Rumus:
  nilai_rupiah = actual_weight × harga_per_kg
  poin = nilai_rupiah × 10

Contoh 1: 0.6 kg PET
  → 0.6 × 1.600 = Rp 960
  → 960 × 10 = 9.600 SulapaPoin

Contoh 2: 2.0 kg Kain Perca (pilih cash)
  → 2.0 × 10.000 = Rp 20.000 (dibayar tunai di tempat)

Aturan:
  - Di bawah 1 kg → hanya bisa pilih poin (cash disabled)
  - Di atas 1 kg  → bisa pilih cash ATAU poin
  - Penarikan tunai dari poin: 1.000.000 poin = Rp 100.000
```


## IMPACT TRACKER (Landing Page)
Tidak perlu tabel sendiri. Query langsung:
```sql
-- Total sampah diselamatkan
SELECT SUM(actual_weight) AS total_kg
FROM deposits WHERE status = 'completed';

-- Total produk terjual (jumlah produk di marketplace)
SELECT COUNT(*) AS total_products
FROM products WHERE status = 'available';

-- Total pengrajin aktif
SELECT COUNT(*) AS total_artisans
FROM artisans WHERE status = 'active';
```
