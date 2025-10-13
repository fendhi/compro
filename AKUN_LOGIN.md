# 🔐 AKUN LOGIN ORINDPOS

## ✅ Akun Default Yang Sudah Ada:

### **1. Admin (Full Access)**
```
Email    : admin@orindpos.com
Password : password
Role     : admin
Akses    : Semua fitur termasuk User Management
```

### **2. Kasir (Terbatas)**
```
Email    : kasir@orindpos.com
Password : password
Role     : kasir
Akses    : Dashboard, Transaksi, Master Data, Laporan
```

---

## 🌐 Cara Login:

1. **Buka Browser**
2. **Akses:** http://localhost:8001/login
3. **Masukkan:**
   - Email: `admin@orindpos.com`
   - Password: `password`
4. **Klik Login**

---

## ➕ Cara Menambah Akun Baru

### **Metode 1: Via Web (Setelah Login sebagai Admin)**

1. Login sebagai admin
2. Klik menu **Management** → **Manajemen User**
3. Klik tombol **+ Tambah User**
4. Isi form:
   - Nama
   - Email
   - Password
   - Role (Admin/Kasir)
5. Klik **Simpan**

---

### **Metode 2: Via Terminal (Cepat)**

#### **Membuat 1 User:**

```bash
php artisan tinker
```

Kemudian ketik:

```php
// Buat Admin
\App\Models\User::create([
    'name' => 'Admin Baru',
    'email' => 'adminbaru@orindpos.com',
    'password' => bcrypt('password123'),
    'role' => 'admin'
]);

// Buat Kasir
\App\Models\User::create([
    'name' => 'Kasir Baru',
    'email' => 'kasir2@orindpos.com',
    'password' => bcrypt('password123'),
    'role' => 'kasir'
]);

// Keluar
exit;
```

#### **Membuat Multiple Users Sekaligus:**

```bash
php artisan tinker
```

```php
$users = [
    ['name' => 'Kasir 2', 'email' => 'kasir2@orindpos.com', 'password' => bcrypt('password'), 'role' => 'kasir'],
    ['name' => 'Kasir 3', 'email' => 'kasir3@orindpos.com', 'password' => bcrypt('password'), 'role' => 'kasir'],
    ['name' => 'Manager', 'email' => 'manager@orindpos.com', 'password' => bcrypt('password'), 'role' => 'admin'],
];

foreach ($users as $userData) {
    \App\Models\User::create($userData);
}

exit;
```

---

### **Metode 3: Via MySQL Direct**

```bash
mysql -u root orindpos
```

```sql
-- Buat user baru (password sudah di-hash dengan bcrypt)
INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES 
('Admin Baru', 'newadmin@orindpos.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW());

-- Password di atas adalah 'password'
```

**Note:** Hash password `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi` = `password`

---

## 📋 Daftar Semua User Saat Ini

```bash
mysql -u root orindpos -e "SELECT id, name, email, role FROM users;"
```

Output:
```
+----+---------------+--------------------+-------+
| id | name          | email              | role  |
+----+---------------+--------------------+-------+
|  1 | Administrator | admin@orindpos.com | admin |
|  2 | Kasir 1       | kasir@orindpos.com | kasir |
+----+---------------+--------------------+-------+
```

---

## 🔒 Ganti Password User

### **Via Tinker:**

```bash
php artisan tinker
```

```php
// Ganti password user dengan email tertentu
$user = \App\Models\User::where('email', 'admin@orindpos.com')->first();
$user->password = bcrypt('passwordbaru123');
$user->save();

echo "Password berhasil diganti!";
exit;
```

### **Via MySQL:**

```bash
mysql -u root orindpos
```

```sql
-- Ganti password (hash untuk 'newpassword')
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE email = 'admin@orindpos.com';
```

---

## 🗑️ Hapus User

### **Via Tinker:**

```bash
php artisan tinker
```

```php
\App\Models\User::where('email', 'kasir@orindpos.com')->delete();
exit;
```

### **Via MySQL:**

```sql
DELETE FROM users WHERE email = 'kasir@orindpos.com';
```

---

## 🎯 Role & Permission

### **Admin:**
- ✅ Dashboard
- ✅ Transaksi
- ✅ Master Data (Barang & Kategori)
- ✅ Laporan (Harian & Bulanan)
- ✅ **Management User** (CRUD User)

### **Kasir:**
- ✅ Dashboard
- ✅ Transaksi
- ✅ Master Data (Barang & Kategori)
- ✅ Laporan (Harian & Bulanan)
- ❌ Management User (Tidak Ada Akses)

---

## 📝 Template Create User

Copy-paste script ini untuk membuat user baru dengan cepat:

```bash
php artisan tinker --execute="
\App\Models\User::create([
    'name' => 'NAMA_USER',
    'email' => 'EMAIL_USER@orindpos.com',
    'password' => bcrypt('PASSWORD_USER'),
    'role' => 'admin'  // atau 'kasir'
]);
echo 'User berhasil dibuat!';
"
```

Ganti:
- `NAMA_USER` → Nama yang diinginkan
- `EMAIL_USER` → Email yang diinginkan
- `PASSWORD_USER` → Password yang diinginkan
- `admin` atau `kasir` → Role yang diinginkan

---

## 🔐 Password Hash yang Bisa Digunakan

Untuk testing, gunakan hash ini (password = 'password'):
```
$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
```

Atau generate hash baru:
```bash
php artisan tinker --execute="echo bcrypt('password_anda');"
```

---

## ⚠️ Tips Keamanan

1. **Ganti password default** setelah login pertama kali
2. **Gunakan password kuat** untuk production
3. **Jangan share password** admin
4. **Hapus akun yang tidak digunakan**
5. **Audit log** siapa saja yang login

---

## 📞 Quick Commands

```bash
# Lihat semua user
mysql -u root orindpos -e "SELECT id, name, email, role FROM users;"

# Count total user
mysql -u root orindpos -e "SELECT role, COUNT(*) as total FROM users GROUP BY role;"

# Buat user cepat
php artisan tinker --execute="\App\Models\User::create(['name'=>'Test User','email'=>'test@test.com','password'=>bcrypt('test123'),'role'=>'kasir']);"

# Hapus user by email
php artisan tinker --execute="\App\Models\User::where('email','test@test.com')->delete();"
```

---

## 🎉 Kesimpulan

**Akun sudah siap digunakan!**

Login sekarang di: **http://localhost:8001/login**

- Email: `admin@orindpos.com`
- Password: `password`

Selamat menggunakan OrindPOS! 🚀
