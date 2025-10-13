# 📦 Database OrindPOS

## 📄 File Database

File: `orindpos.sql`  
Size: ~9.2 KB  
Tables: 7 tables (users, kategoris, barangs, transaksis, transaksi_details, personal_access_tokens, migrations)

---

## 🔧 Cara Import Database

### **Metode 1: Via phpMyAdmin (XAMPP)**

1. Buka **phpMyAdmin**: http://localhost/phpmyadmin
2. Klik **New** di sidebar kiri
3. Buat database baru dengan nama: `orindpos`
4. Pilih database `orindpos` yang baru dibuat
5. Klik tab **Import**
6. Klik **Choose File** → Pilih file `orindpos.sql`
7. Scroll ke bawah, klik **Go/Import**
8. ✅ Database berhasil di-import!

---

### **Metode 2: Via Terminal (Command Line)**

#### **Untuk XAMPP:**

```bash
# Masuk ke folder project
cd /path/to/capstone

# Import database
/Applications/XAMPP/xamppfiles/bin/mysql -u root -e "CREATE DATABASE IF NOT EXISTS orindpos;"
/Applications/XAMPP/xamppfiles/bin/mysql -u root orindpos < database/orindpos.sql

# Verifikasi
/Applications/XAMPP/xamppfiles/bin/mysql -u root orindpos -e "SHOW TABLES;"
```

#### **Untuk MySQL Standalone (Homebrew):**

```bash
# Masuk ke folder project
cd /path/to/capstone

# Import database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS orindpos;"
mysql -u root orindpos < database/orindpos.sql

# Verifikasi
mysql -u root orindpos -e "SHOW TABLES;"
```

---

### **Metode 3: Via Laravel Migration (Recommended for Development)**

Jika ingin menggunakan migration Laravel (lebih flexible):

```bash
# 1. Setup database connection di .env
# Pastikan DB_DATABASE=orindpos

# 2. Jalankan migration
php artisan migrate:fresh

# 3. Jalankan seeder untuk data awal
php artisan db:seed
```

---

## 📊 Isi Database

### **1. Table: users**
- Total: 2 users (1 admin, 1 kasir)
- Columns: id, name, email, password, role, created_at, updated_at

**Default Accounts:**
| Email | Password | Role |
|---|---|---|
| admin@orindpos.com | password | admin |
| kasir@orindpos.com | password | kasir |

---

### **2. Table: kategoris**
- Total: 5 categories
- Columns: id, nama, deskripsi, created_at, updated_at

**Data:**
1. Makanan
2. Minuman
3. Snack
4. Elektronik
5. Alat Tulis

---

### **3. Table: barangs**
- Columns: id, kategori_id, kode, nama, harga, stok, created_at, updated_at
- Status: Empty (bisa diisi via aplikasi)

---

### **4. Table: transaksis**
- Columns: id, user_id, kode_transaksi, total, tanggal, created_at, updated_at
- Status: Empty (untuk transaksi penjualan)

---

### **5. Table: transaksi_details**
- Columns: id, transaksi_id, barang_id, jumlah, harga, subtotal, created_at, updated_at
- Status: Empty (detail item per transaksi)

---

### **6. Table: personal_access_tokens**
- Untuk Laravel Sanctum API tokens
- Status: Empty

---

### **7. Table: migrations**
- Track migration history
- Total: 5 migrations executed

---

## 🔄 Update Database di GitHub

Jika ada perubahan database dan ingin update file SQL:

```bash
# Export database terbaru
/Applications/XAMPP/xamppfiles/bin/mysqldump -u root orindpos > database/orindpos.sql

# Atau untuk MySQL Homebrew
mysqldump -u root orindpos > database/orindpos.sql

# Commit dan push
git add database/orindpos.sql
git commit -m "Update database export"
git push origin main
```

---

## ⚠️ Catatan Penting

1. **Password** di file SQL sudah di-hash dengan bcrypt
2. **Jangan ubah** struktur tabel secara manual, gunakan migration Laravel
3. File SQL ini untuk **development/testing**, bukan production
4. **Backup** database secara berkala

---

## 🔐 Security Notes

- ✅ Password sudah di-hash (bcrypt)
- ✅ File .env tidak di-commit (berisi kredensial)
- ✅ Database export aman untuk di-share di repository
- ⚠️ Ganti password default setelah deployment

---

## 📝 Quick Commands

```bash
# Lihat semua tables
mysql -u root orindpos -e "SHOW TABLES;"

# Lihat data users
mysql -u root orindpos -e "SELECT id, name, email, role FROM users;"

# Lihat data kategoris
mysql -u root orindpos -e "SELECT * FROM kategoris;"

# Count semua data
mysql -u root orindpos -e "
SELECT 
  (SELECT COUNT(*) FROM users) as total_users,
  (SELECT COUNT(*) FROM kategoris) as total_kategoris,
  (SELECT COUNT(*) FROM barangs) as total_barangs,
  (SELECT COUNT(*) FROM transaksis) as total_transaksis;
"

# Backup database
mysqldump -u root orindpos > backup_$(date +%Y%m%d_%H%M%S).sql
```

---

## 🆘 Troubleshooting

### **Error: Database already exists**
```bash
# Hapus database lama
mysql -u root -e "DROP DATABASE orindpos;"

# Import ulang
mysql -u root -e "CREATE DATABASE orindpos;"
mysql -u root orindpos < database/orindpos.sql
```

### **Error: Access denied**
```bash
# Pastikan MySQL sudah running
# Untuk XAMPP: buka XAMPP Control Panel, start MySQL

# Test koneksi
mysql -u root -e "SELECT 1;"
```

### **Error: Command not found: mysql**
```bash
# Gunakan full path
/Applications/XAMPP/xamppfiles/bin/mysql -u root orindpos < database/orindpos.sql
```

---

**Last Updated:** October 13, 2025  
**Database Version:** 1.0.0  
**Laravel Version:** 10.x  
**MySQL Version:** MariaDB 10.4.28 / MySQL 8.x
