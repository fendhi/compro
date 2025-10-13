# Panduan Instalasi OrindPOS

## Langkah-langkah Instalasi

### 1. Install Dependencies

Install Composer dependencies:
```bash
composer install
```

Install NPM dependencies:
```bash
npm install
```

### 2. Konfigurasi Environment

Copy file environment:
```bash
cp .env.example .env
```

Generate application key:
```bash
php artisan key:generate
```

### 3. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=orindpos
DB_USERNAME=root
DB_PASSWORD=
```

Buat database baru di MySQL:
```sql
CREATE DATABASE orindpos;
```

### 4. Migrasi Database

Jalankan migrasi:
```bash
php artisan migrate
```

Seed data awal (opsional):
```bash
php artisan db:seed
```

Data seed default:
- Admin: admin@orindpos.com / password
- Kasir: kasir@orindpos.com / password

### 5. Build Assets

Development:
```bash
npm run dev
```

Production:
```bash
npm run build
```

### 6. Jalankan Server

```bash
php artisan serve
```

Buka browser dan akses: http://localhost:8000

## Troubleshooting

### Error: Key not set
```bash
php artisan key:generate
```

### Error: Storage permission
```bash
chmod -R 775 storage bootstrap/cache
```

### Error: Composer dependencies
```bash
composer update
```

### Error: NPM dependencies
```bash
rm -rf node_modules package-lock.json
npm install
```

## Struktur Project

```
capstone/
├── app/
│   ├── Console/          # Console commands
│   ├── Exceptions/       # Exception handlers
│   ├── Http/
│   │   └── Controllers/  # Application controllers
│   └── Models/           # Eloquent models
├── bootstrap/            # Framework bootstrap
├── config/               # Configuration files
├── database/
│   ├── migrations/       # Database migrations
│   └── seeders/          # Database seeders
├── public/               # Public assets
├── resources/
│   ├── css/             # Stylesheets
│   ├── js/              # JavaScript
│   └── views/           # Blade templates
├── routes/
│   ├── api.php          # API routes
│   ├── console.php      # Console routes
│   └── web.php          # Web routes
├── storage/             # Storage files
├── .env                 # Environment configuration
├── composer.json        # PHP dependencies
├── package.json         # Node dependencies
└── README.md           # Documentation
```

## Fitur Aplikasi

✅ Login & Logout
✅ Dashboard
✅ Transaksi Penjualan
✅ Master Data Barang
✅ Master Data Kategori
✅ Laporan Harian
✅ Laporan Bulanan
✅ Manajemen User

## Teknologi

- **Backend**: Laravel 10
- **Frontend**: Tailwind CSS 3
- **Database**: MySQL
- **Build Tool**: Vite

## Catatan Penting

1. Pastikan PHP versi 8.1 atau lebih tinggi
2. Pastikan MySQL server sudah berjalan
3. Untuk development, jalankan `npm run dev` di terminal terpisah
4. Untuk production, build dengan `npm run build` sebelum deploy
5. Jangan lupa setting permission untuk folder `storage` dan `bootstrap/cache`
