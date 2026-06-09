# GARDA 5 (Gerakan Sadar Dosis Garam)

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="GARDA 5">
</p>

## Tentang GARDA 5

GARDA 5 (Gerakan Sadar Dosis Garam) adalah aplikasi berbasis Laravel yang dirancang untuk membantu edukasi dan pemantauan konsumsi garam masyarakat melalui sistem yang terintegrasi.

---

# Persyaratan Sistem

Pastikan perangkat Anda telah menginstal:

| Software        | Versi Minimum          |
| --------------- | ---------------------- |
| PHP             | 8.3                    |
| Composer        | Terbaru                |
| Node.js         | 20+                    |
| NPM             | Terbaru                |
| MySQL / MariaDB | MySQL 8+ / MariaDB 10+ |
| Git             | Terbaru (opsional)     |

---

# Cara Mendapatkan Source Code

## Opsi 1 (Disarankan) - Clone Repository

```bash
git clone https://github.com/DoctorZeee/GARDA5.git
cd GARDA5
```

---

## Opsi 2 - Download ZIP

1. Buka repository GitHub.
2. Klik tombol **Code**.
3. Pilih **Download ZIP**.
4. Ekstrak file ZIP.
5. Masuk ke folder project.

Contoh:

```bash
cd GARDA5
```

---

# Instalasi Project

## 1. Install Dependency PHP

```bash
composer install
```

Jika project sudah menyertakan folder vendor dan ingin memastikan semua package terbaru:

```bash
composer update
```

---

## 2. Install Dependency Frontend

```bash
npm install
```

atau

```bash
npm ci
```

---

## 3. Membuat File Environment

Salin file environment:

### Linux / macOS

```bash
cp .env.example .env
```

### Windows CMD

```cmd
copy .env.example .env
```

### Windows PowerShell

```powershell
Copy-Item .env.example .env
```

---

## 4. Generate Laravel APP_KEY

```bash
php artisan key:generate
```

---

# Konfigurasi Database

Buat database baru, misalnya:

```text
garda5
```

Kemudian buka file:

```text
.env
```

Ubah bagian berikut:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=garda5
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan dengan konfigurasi database Anda.

---

# Menjalankan Migration

Jalankan:

```bash
php artisan migrate
```

Jika project memiliki seeder:

```bash
php artisan db:seed
```

atau

```bash
php artisan migrate --seed
```

---

# Membuat Symbolic Link Storage

```bash
php artisan storage:link
```

---

# Build Asset Frontend

## Mode Development

```bash
npm run dev
```

## Mode Production

```bash
npm run build
```

---

# Menjalankan Server Laravel

```bash
php artisan serve
```

Secara default aplikasi dapat diakses melalui:

```text
http://127.0.0.1:8000
```

---

# Menjalankan Queue (Jika Digunakan)

```bash
php artisan queue:work
```

---

# Membersihkan Cache Laravel

Jika terjadi error konfigurasi atau perubahan environment:

```bash
php artisan optimize:clear
```

Atau secara manual:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear
```

---

# Menjalankan Project di Berbagai Sistem Operasi

## Windows

Gunakan salah satu:

* Laragon
* XAMPP
* WAMP
* PHP Native

Kemudian jalankan:

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
php artisan serve
```

---

## Linux

Install:

* PHP
* Composer
* MySQL/MariaDB
* Node.js
* NPM

Lalu jalankan:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
php artisan serve
```

---

## macOS

Pastikan Homebrew telah terpasang.

Install dependency yang diperlukan kemudian jalankan:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
php artisan serve
```

---

# Update Source Code

Jika menggunakan Git:

```bash
git pull origin main
composer install
npm install
php artisan migrate
npm run build
```

---

# Struktur Project

```text
app/
bootstrap/
config/
database/
public/
resources/
routes/
storage/
tests/
artisan
composer.json
package.json
```

---

# Troubleshooting

## Error Composer

```bash
composer install
composer dump-autoload
```

---

## Error APP_KEY

```bash
php artisan key:generate
```

---

## Error Permission (Linux/macOS)

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## Error Vite

```bash
npm install
npm run dev
```

---

## Error Cache

```bash
php artisan optimize:clear
```

---

# Mode Production (Disarankan)

Set pada file `.env`:

```env
APP_ENV=production
APP_DEBUG=false
```

Kemudian jalankan:

```bash
php artisan optimize
npm run build
```

---

# Kontribusi

1. Fork repository
2. Buat branch baru

```bash
git checkout -b feature/nama-fitur
```

3. Commit perubahan

```bash
git commit -m "Menambahkan fitur baru"
```

4. Push

```bash
git push origin feature/nama-fitur
```

5. Buat Pull Request.

---

# License

Project GARDA 5 dikembangkan untuk kebutuhan edukasi dan pelayanan kesehatan masyarakat.

© GARDA 5 Project.
