# Sulapa Karya Hackathon

Sistem manajemen bank sampah dan poin berbasis Laravel yang dikembangkan untuk Sulapa Karya Hackathon.

---

## Prasyarat

Pastikan software berikut sudah terinstall:

### 1. Git
Cek instalasi:

```bash
git --version
```

### 2. PHP
Minimal sesuai versi yang digunakan project.

```bash
php -v
```

### 3. Composer

```bash
composer --version
```

### 4. Node.js & NPM

```bash
node -v
npm -v
```

### 5. MySQL

Dapat menggunakan:

- XAMPP
- Laragon
- MySQL Community Server

---

# Clone Repository

Clone langsung branch development yang digunakan tim:

```bash
git clone -b feature/auth-supabase https://github.com/alifsarezkyrahmah/sulapa-karya-hackathon.git
```

Masuk ke folder project:

```bash
cd sulapa-karya-hackathon
```

---

# Pastikan Branch Aktif

```bash
git branch
```

Output:

```bash
* feature/auth-supabase
```

Jika belum berada di branch tersebut:

```bash
git checkout feature/auth-supabase
```

atau

```bash
git switch feature/auth-supabase
```

---

# Install Dependency Backend

Install seluruh package Laravel:

```bash
composer install
```

---

# Install Dependency Frontend

```bash
npm install
```

---

# Setup Environment

Buat file `.env`:

### Windows

```bash
copy .env.example .env
```

### Linux / MacOS

```bash
cp .env.example .env
```

---

# Konfigurasi Database

Buat database baru di MySQL:

```sql
CREATE DATABASE sulapa_db;
```

Buka file `.env` dan sesuaikan konfigurasi:

```env
APP_NAME="Sulapa Karya"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sulapa_db
DB_USERNAME=root
DB_PASSWORD=
```

---

# Konfigurasi Supabase

Minta nilai berikut kepada Project Manager atau anggota tim yang memiliki akses:

```env
SUPABASE_URL=
SUPABASE_ANON_KEY=
```

**Jangan pernah mengupload file `.env` ke GitHub.**

---

# Generate Application Key

```bash
php artisan key:generate
```

Output:

```bash
Application key set successfully.
```

---

# Migrasi Database

Jalankan migration:

```bash
php artisan migrate
```

Jika tersedia seeder:

```bash
php artisan migrate --seed
```

---

# Storage Link

Jika project menggunakan upload gambar atau file:

```bash
php artisan storage:link
```

---

# Menjalankan Project

## Terminal 1 - Laravel

```bash
php artisan serve
```

Laravel akan berjalan di:

```text
http://127.0.0.1:8000
```

---

## Terminal 2 - Vite

```bash
npm run dev
```

Biarkan terminal ini tetap berjalan selama development.

---

# Update Kode dari Repository

Sebelum mulai bekerja setiap hari:

```bash
git checkout feature/auth-supabase
git pull origin feature/auth-supabase
```

---

# Workflow Development

## Menambahkan Perubahan

```bash
git add .
```

## Commit

```bash
git commit -m "feat: deskripsi perubahan"
```

Contoh:

```bash
git commit -m "feat: add transfer point feature"
```

## Push ke Repository

```bash
git push origin feature/auth-supabase
```

---

# Troubleshooting

## Vendor Tidak Ditemukan

```bash
composer install
```

---

## Error APP_KEY

```bash
php artisan key:generate
```

---

## Error Database

Pastikan:

- Database sudah dibuat
- Konfigurasi `.env` sudah benar
- MySQL sedang berjalan

---

## Error Vite Manifest

```bash
npm install
npm run dev
```

---

## Error Storage

```bash
php artisan storage:link
```

---

# Struktur Branch

```text
main
│
├── develop
│
└── feature/auth-supabase
```

Branch aktif pengembangan saat ini:

```text
feature/auth-supabase
```

---

# Kontak Tim

Jika mengalami kendala saat setup:

- Pastikan seluruh dependency sudah terinstall
- Pastikan file `.env` sudah dikonfigurasi dengan benar
- Hubungi anggota tim untuk mendapatkan kredensial Supabase dan konfigurasi tambahan yang diperlukan

---
Happy Coding 🚀
