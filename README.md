# OrindPOS - Point of Sale System

OrindPOS adalah sistem Point of Sale (POS) berbasis Laravel dengan Tailwind CSS.

## Requirement
- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js & NPM

## Instalasi

1. Clone repository ini
2. Install dependencies PHP:
```bash
composer install
```

3. Install dependencies Node.js:
```bash
npm install
```

4. Copy file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Buat database MySQL dengan nama `orindpos`

7. Konfigurasi database di file `.env`:
```
DB_DATABASE=orindpos
DB_USERNAME=root
DB_PASSWORD=
```

8. Jalankan migrasi database:
```bash
php artisan migrate
```

9. (Opsional) Seed data awal:
```bash
php artisan db:seed
```

10. Build assets:
```bash
npm run build
```

## Menjalankan Aplikasi

### Development
```bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - Vite Dev Server  
npm run dev
```

Aplikasi akan berjalan di `http://localhost:8000`

### Production
```bash
npm run build
php artisan serve
```

## Fitur

- ✅ Dashboard
- ✅ Manajemen Transaksi
- ✅ Master Data Barang
- ✅ Master Data Kategori
- ✅ Laporan Harian
- ✅ Laporan Bulanan
- ✅ Manajemen User
- ✅ Autentikasi & Otorisasi

## Struktur Folder

```
capstone/
├── app/
│   ├── Http/Controllers/
│   └── Models/
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
│   ├── api.php
│   └── web.php
└── storage/
```

## Teknologi

- Laravel 10
- Tailwind CSS 3
- MySQL
- Vite

## License

MIT License
