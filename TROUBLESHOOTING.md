# 🔧 Troubleshooting OrindPOS

## Error yang Sering Terjadi & Solusinya

### ❌ Error 1: "command not found: npm"

**Penyebab**: Node.js dan NPM belum terinstall di sistem Anda.

**Solusi**:
```bash
# Install Node.js dengan Homebrew (Mac)
brew install node

# Atau download dari https://nodejs.org/
# Setelah install, verifikasi:
node --version
npm --version
```

---

### ❌ Error 2: "Session store not set on request"

**Penyebab**: Konfigurasi session tidak lengkap atau middleware session tidak aktif.

**Solusi**: Sudah diperbaiki! ✅
```bash
# Clear cache setelah fix
php artisan optimize:clear

# Pastikan folder session ada
chmod -R 775 storage/framework/sessions

# Restart server
php artisan serve
```

**Yang sudah diperbaiki:**
- ✅ config/session.php sudah lengkap
- ✅ Middleware session sudah aktif di Kernel.php
- ✅ Custom middleware sudah dibuat

---

### ❌ Error 3: "bootstrap/cache directory must be present"

**Penyebab**: Folder cache Laravel belum dibuat.

**Solusi**:
```bash
mkdir -p bootstrap/cache
chmod -R 775 bootstrap/cache
```

---

### ❌ Error 4: "No application encryption key has been specified"

**Penyebab**: APP_KEY di file .env masih kosong.

**Solusi**:
```bash
php artisan key:generate
```

---

### ❌ Error 5: "Call to undefined function str_slug()"

**Penyebab**: Fungsi Laravel lama yang sudah deprecated.

**Solusi**: Sudah diperbaiki di config/session.php ✅

---

### ❌ Error 6: "SQLSTATE[HY000] [1049] Unknown database"

**Penyebab**: Database 'orindpos' belum dibuat di MySQL.

**Solusi**:
```bash
# Buka MySQL
mysql -u root -p

# Buat database
CREATE DATABASE orindpos;
exit;

# Jalankan migrasi
php artisan migrate
```

---

### ❌ Error 7: "Access denied for user 'root'@'localhost'"

**Penyebab**: Password MySQL di .env tidak sesuai.

**Solusi**: Edit file `.env`:
```
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

---

### ❌ Error 8: Storage permission denied

**Penyebab**: Folder storage tidak memiliki permission write.

**Solusi**:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## ✅ Langkah Instalasi yang Benar

### 1. Install Requirements
```bash
# Check PHP version (minimum 8.1)
php --version

# Check Composer
composer --version

# Install Node.js jika belum ada
brew install node
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies (setelah Node.js terinstall)
npm install
```

### 3. Setup Environment
```bash
# Copy file .env
cp .env.example .env

# Generate app key
php artisan key:generate
```

### 4. Setup Database
```bash
# Buat database MySQL
mysql -u root -p
CREATE DATABASE orindpos;
exit;

# Edit .env sesuaikan password MySQL
# Jalankan migrasi
php artisan migrate

# (Opsional) Seed data awal
php artisan db:seed
```

### 5. Jalankan Aplikasi
```bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - Vite Dev Server (jika Node.js sudah terinstall)
npm run dev

# Atau build untuk production
npm run build
```

---

## 🌐 Akses Aplikasi

Setelah server berjalan, buka browser:
- **URL**: http://localhost:8000
- **Login Admin**: admin@orindpos.com / password
- **Login Kasir**: kasir@orindpos.com / password

---

## 📊 Status Checklist

Gunakan checklist ini untuk memastikan semua sudah siap:

- [ ] PHP >= 8.1 terinstall
- [ ] Composer terinstall
- [ ] MySQL/MariaDB running
- [ ] Node.js & NPM terinstall (opsional, tapi direkomendasikan)
- [ ] composer install berhasil
- [ ] npm install berhasil (jika Node.js ada)
- [ ] .env sudah dikonfigurasi
- [ ] APP_KEY sudah di-generate
- [ ] Database 'orindpos' sudah dibuat
- [ ] php artisan migrate berhasil
- [ ] php artisan serve running tanpa error

---

## 💡 Catatan Penting

### Jika tidak ada Node.js/NPM:
Aplikasi **tetap bisa berjalan** tanpa Node.js karena sudah menggunakan Tailwind CDN di template. Namun untuk production, sebaiknya install Node.js dan build assets dengan:
```bash
npm run build
```

### Jika ada Node.js:
Edit file `resources/views/layouts/app.blade.php` dan ganti:
```html
<script src="https://cdn.tailwindcss.com"></script>
```
Dengan:
```html
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

---

## 🆘 Masih Bermasalah?

1. Cek Laravel logs: `storage/logs/laravel.log`
2. Cek PHP error: `php -v`
3. Cek Composer: `composer diagnose`
4. Clear cache: `php artisan cache:clear`
5. Clear config: `php artisan config:clear`
6. Clear view: `php artisan view:clear`

---

## 📞 Command Berguna

```bash
# Clear semua cache
php artisan optimize:clear

# Recreate database (HATI-HATI: menghapus semua data)
php artisan migrate:fresh --seed

# Check routes
php artisan route:list

# Check database connection
php artisan db:show

# Generate IDE helper
composer require --dev barryvdh/laravel-ide-helper
php artisan ide-helper:generate
```
