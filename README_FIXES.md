# GARDA5 — Catatan Fix Production

## Langkah Wajib Setelah Menerima File Ini

### 1. Generate ulang APP_KEY (WAJIB — key lama sudah terekspos di ZIP)
```bash
php artisan key:generate --force
```

### 2. Jalankan migration
```bash
php artisan migrate --force
```

### 3. Jalankan seeder (opsional, untuk data awal)
```bash
php artisan db:seed
```

### 4. Build assets
```bash
npm install
npm run build
```

### 5. Clear semua cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## Ringkasan Semua Fix

| # | File | Masalah | Fix |
|---|------|---------|-----|
| 1 | `.env` | `APP_DEBUG=true` di production + komentar inline tidak valid | `APP_DEBUG=false`, hapus komentar inline |
| 2 | `.env` | `LOG_LEVEL=debug` | `LOG_LEVEL=error` |
| 3 | `RewardController` | Race condition checkin | `DB::transaction + lockForUpdate` |
| 4 | `RewardController` | Klaim video ∞ tanpa cek duplikat | Guard via `UserVideoClaim` + unique DB constraint |
| 5 | `Admin/UserController` | `AuditLog::create()` langsung | Ganti semua dengan `AuditLogger::log()` |
| 6 | `AuditLogger` | Tidak ada try-catch | Tambah try-catch agar tidak crash request |
| 7 | `AuditLog` model | `fillable` tidak ada `route/method/user_agent` | Ditambahkan |
| 8 | `RoleMiddleware` | Hanya satu role, tidak support `role:admin,puskesmas` | `in_array` dengan explode |
| 9 | `SecurityHeaders` | `unsafe-inline` & `unsafe-eval` di CSP | Nonce-based CSP |
| 10 | `SecurityHeaders` | Nonce di-set SETELAH `$next()` — terlambat untuk view | Nonce di-set SEBELUM `$next()` |
| 11 | Blade views | `<script src>` external tanpa nonce | Semua external script pakai `nonce="{{ csp_nonce() }}"` |
| 12 | `AppServiceProvider` | `@vite` tidak pakai nonce → diblokir CSP | `Vite::useCspNonce()` terhubung ke `csp_nonce()` |
| 13 | `RegisterRequest` | Password tanpa `confirmed` rule | Tambah `confirmed` + `password_confirmation` field |
| 14 | `StoreUserRequest` | Password tanpa `confirmed` rule | Tambah `confirmed` rule |
| 15 | `RewardTest` | `Video::factory()` & `UserPoint::factory()` tidak ada | Buat `VideoFactory` & `UserPointFactory` |
| 16 | `Video` / `UserPoint` model | `HasFactory` tidak ada | Tambah `use HasFactory` |
| 17 | `RewardTest` | Test audit_log kirim form tidak lengkap → validasi gagal | Fix test dengan semua required fields |
| 18 | `routes/web.php` | POST `/register` tidak ada throttle | Tambah `throttle:10,1` |
| 19 | `HealthLogController` | Dua `increment()` terpisah → dua DB round trip | Satu `update()` dengan `DB::raw` |
| 20 | `composer.json` | `laravel/pao` typo (package tidak ada) | Hapus dari `require-dev` |
