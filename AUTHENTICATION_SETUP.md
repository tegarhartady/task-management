# Sistem Login dan Dashboard dengan Role Management

Dokumentasi lengkap untuk sistem login per user hingga masuk dashboard berdasarkan roles.

## 🎯 Fitur Utama

✅ **Authentication System**

- Login per user dengan email & password
- Registrasi user baru
- Remember me functionality
- Logout

✅ **Role Management**

- 4 tipe role: Admin, Supervisor, Manager, Karyawan
- Middleware untuk proteksi route berdasarkan role
- Dashboard berbeda untuk setiap role

✅ **Role-Based Dashboards**

- **Admin Dashboard**: Statistik user, overview sistem, user management
- **Supervisor Dashboard**: Monitoring tim, task management, reimbursement tracking
- **Manager Dashboard**: Team performance, subordinate management, financial tracking
- **Karyawan Dashboard**: Personal tasks, briefs, reimbursement requests

---

## 🚀 Setup & Instalasi

### 1. Buat Database

```bash
# Di terminal, masuk ke direktori project
cd /path/to/project

# Buat database baru (sesuaikan dengan nama database Anda)
mysql -u root -p
CREATE DATABASE vuexy_admin;
EXIT;
```

### 2. Konfigurasi `.env`

```bash
# Copy .env.example ke .env
cp .env.example .env

# Generate app key
php artisan key:generate
```

Ubah database configuration di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vuexy_admin
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Jalankan Migrations & Seeder

```bash
# Jalankan semua migrations
php artisan migrate

# Jalankan seeder untuk populate user dengan roles
php artisan db:seed --class=UserSeeder
```

### 4. Jalankan Server

```bash
# Terminal 1: Laravel Development Server
php artisan serve

# Terminal 2: Frontend Build (jika menggunakan webpack)
npm run watch
```

Server akan berjalan di `http://localhost:8000`

---

## 👥 Test User Credentials

Setelah seeding, Anda bisa login dengan akun berikut:

### Admin

- **Email**: `admin@example.com`
- **Password**: `password123`
- **Role**: Admin - Akses penuh ke semua fitur

### Supervisor

- **Email**: `supervisor@example.com`
- **Password**: `password123`
- **Role**: Supervisor - Monitoring tim & task management

### Manager

- **Email**: `manager@example.com`
- **Password**: `password123`
- **Role**: Manager - Team & financial management

### Karyawan

- **Email**: `dila@example.com`
- **Password**: `password123`
- **Role**: Karyawan - Personal dashboard

---

## 📁 File Structure

### Controllers

```
app/Http/Controllers/
├── Auth/
│   └── AuthController.php          # Login, Register, Logout
└── Dashboard/
    └── DashboardController.php     # Dashboard per role
```

### Middleware

```
app/Http/Middleware/
└── CheckRole.php                   # Role verification middleware
```

### Models

```
app/Models/
└── User.php                        # User model dengan role methods
```

### Views

```
resources/views/
├── auth/
│   ├── login.blade.php            # Login form
│   └── register.blade.php         # Register form
└── content/dashboards/
    ├── admin-dashboard.blade.php
    ├── supervisor-dashboard.blade.php
    ├── manager-dashboard.blade.php
    └── karyawan-dashboard.blade.php
```

### Routes

```
routes/
└── web.php                         # Route configuration
```

### Migrations

```
database/migrations/
└── 2026_04_14_000000_add_role_to_users_table.php
```

### Seeders

```
database/seeders/
├── UserSeeder.php                 # User dengan roles
└── DatabaseSeeder.php
```

---

## 🔐 Flow Authentication

```
┌─────────────────────────────────────────┐
│  User Akses Halaman                    │
└─────────────────────────────────────────┘
                    ↓
        ┌───────────────────────┐
        │ Middleware 'auth'     │
        │ (Protect Routes)      │
        └───────────────────────┘
                    ↓
        ┌──────────────────────────────┐
        │ User Sudah Login?             │
        └──────────────────────────────┘
         YES ↓                    ↓ NO
            ↓                 Redirect ke Login
       ┌──────────────────────────┐
       │ Check Role Middleware    │
       │ 'role:admin,supervisor'  │
       └──────────────────────────┘
            ↓
       ┌──────────────────────────┐
       │ User punya role?          │
       └──────────────────────────┘
        YES ↓                ↓ NO
           ↓            Abort 403
       ┌──────────────────────┐
       │ Load Dashboard       │
       │ Sesuai Role          │
       └──────────────────────┘
```

---

## 🛣️ Routes Definition

### Public Routes (Tanpa Login)

```php
GET  /login                 # Login form
POST /login                 # Submit login
GET  /register              # Register form
POST /register              # Submit register
```

### Protected Routes (Harus Login)

```php
GET  /dashboard             # Redirect ke dashboard sesuai role
POST /logout                # Logout user
```

### Admin Only

```php
GET / admin - dashboard; # Admin dashboard (middleware: role:admin)
```

### Supervisor Only

```php
GET / supervisor - dashboard; # Supervisor dashboard (middleware: role:supervisor,admin)
```

### Manager Only

```php
GET / manager - dashboard; # Manager dashboard (middleware: role:manager,admin)
```

### All Authenticated Users

```php
GET  /                      # Home page
GET  /pages-tasks           # Task page
GET  /pages-content         # Content page
GET  /pages-performance     # Performance page
GET  /pages-reimburs        # Reimbursement page
```

---

## 🎨 View Layout

### Login Page

```
┌────────────────────────────────┐
│      🔐 Vuexy Admin             │
├────────────────────────────────┤
│ Email Address: [input]         │
│ Password: [input]              │
│ [☐] Remember me                │
│ [Sign In Button]               │
├────────────────────────────────┤
│ Don't have account? Create one  │
└────────────────────────────────┘
```

### Dashboard Navigation

Setelah login, menu navigasi akan menampilkan:

- Dashboard (sesuai role)
- Task
- Content
- My Performance
- Reimbursement
- Logout

---

## 🔧 Modifikasi & Customization

### Menambah Role Baru

**1. Update Migration:**

```php
// database/migrations/xxxx_add_role_to_users_table.php
$table
  ->enum('role', ['admin', 'supervisor', 'manager', 'karyawan', 'new_role'])
  ->default('karyawan')
  ->after('email');
```

**2. Update User Model:**

```php
// app/Models/User.php
public function isNewRole()
{
    return $this->role === 'new_role';
}
```

**3. Create Dashboard View:**

```php
// resources/views/content/dashboards/new-role-dashboard.blade.php
```

**4. Add Route:**

```php
// routes/web.php
Route::get('/new-role-dashboard', function () {
  return view('content.dashboards.new-role-dashboard');
})
  ->middleware('role:new_role')
  ->name('new.role.dashboard');
```

### Mengubah Default Role

Edit file `app/Http/Controllers/Auth/AuthController.php` di method `register()`:

```php
// Ubah ini:
'role' => 'karyawan',  // Default role saat registrasi
```

### Menambah User Manual

Via Tinker:

```bash
php artisan tinker

# Di dalam tinker:
$user = App\Models\User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => Hash::make('password123'),
    'role' => 'manager',
    'is_active' => true,
]);
```

---

## 📊 Database Schema

### Users Table

```sql
id              BIGINT PRIMARY KEY
name            VARCHAR(255)
email           VARCHAR(255) UNIQUE
password        VARCHAR(255)
role            ENUM('admin','supervisor','manager','karyawan')
is_active       BOOLEAN DEFAULT true
remember_token  VARCHAR(100) NULLABLE
email_verified_at TIMESTAMP NULLABLE
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

---

## 🐛 Troubleshooting

### Problem: "Unauthorized - Anda tidak memiliki akses"

**Solution**: Pastikan user sudah login dan memiliki role yang tepat. Cek middleware configuration.

### Problem: "Target class does not exist"

**Solution**: Jalankan `composer dump-autoload` atau clear cache dengan `php artisan cache:clear`

### Problem: Login form tidak muncul

**Solution**: Pastikan routes sudah ter-load. Cek dengan `php artisan route:list`

### Problem: Database migration gagal

**Solution**:

```bash
# Reset database
php artisan migrate:refresh

# Atau fresh migrate
php artisan migrate:fresh --seed
```

---

## 🚀 Deployment Checklist

- [ ] Database sudah ter-migrate
- [ ] Seeder sudah ter-jalankan
- [ ] `.env` sudah dikonfigurasi dengan benar
- [ ] Storage permissions sudah diset
- [ ] APP_DEBUG di `.env` di-set ke `false`
- [ ] APP_KEY di `.env` sudah ter-generate

---

## 📞 Support

Untuk pertanyaan atau issue, silakan hubungi tim development.

---

**Created**: April 14, 2026  
**Version**: 1.0.0  
**Status**: Production Ready
