# Sistem Login dan Role Management - Quick Start Guide

## 🚀 Quick Setup (5 menit)

### Pilihan 1: Automated Setup Script

```bash
# Buat file executable
chmod +x setup-auth.sh

# Jalankan script
./setup-auth.sh
```

### Pilihan 2: Manual Setup

```bash
# 1. Generate APP_KEY
php artisan key:generate

# 2. Setup Database
# - Buat database baru di MySQL
# - Update DB credentials di .env

# 3. Run Migrations & Seeding
php artisan migrate
php artisan db:seed --class=UserSeeder

# 4. Clear Cache
php artisan cache:clear
php artisan route:clear
php artisan config:clear

# 5. Start Server
php artisan serve
```

---

## 📝 Test Accounts

Setelah setup, gunakan akun ini untuk testing:

### 1️⃣ Admin Account

```
URL: http://localhost:8000/login
Email: admin@example.com
Password: password123
Dashboard: http://localhost:8000/admin-dashboard
```

**Akses**: Penuh ke semua fitur sistem

### 2️⃣ Supervisor Account

```
Email: supervisor@example.com
Password: password123
Dashboard: http://localhost:8000/supervisor-dashboard
```

**Akses**: Monitoring tim, task management, reimbursement

### 3️⃣ Manager Account

```
Email: manager@example.com
Password: password123
Dashboard: http://localhost:8000/manager-dashboard
```

**Akses**: Team performance, financial tracking

### 4️⃣ Karyawan Account

```
Email: dila@example.com
Password: password123
Dashboard: http://localhost:8000/
```

**Akses**: Personal dashboard, tasks, content

---

## 🔑 Key Files

### Authentication

- `app/Http/Controllers/Auth/AuthController.php` - Login logic
- `app/Http/Middleware/CheckRole.php` - Role validation
- `routes/web.php` - Route definitions

### Models & Database

- `app/Models/User.php` - User model dengan role methods
- `database/migrations/2026_04_14_000000_add_role_to_users_table.php`
- `database/seeders/UserSeeder.php` - Populate demo users

### Views

- `resources/views/auth/login.blade.php` - Login form
- `resources/views/auth/register.blade.php` - Register form
- `resources/views/content/dashboards/*-dashboard.blade.php` - Dashboards

---

## 🎯 Flow Diagram

```
┌──────────────────┐
│  User Login      │
│  /login          │
└────────┬─────────┘
         │
         ▼
┌──────────────────────────┐
│ Validate Email & Password│
│ Check if Active          │
└────────┬─────────────────┘
         │
         ▼ Success
┌──────────────────────┐
│ Create Session       │
│ Redirect /dashboard  │
└────────┬─────────────┘
         │
         ▼
┌─────────────────────────────────┐
│ Check User Role                 │
└────┬──────┬──────┬──────────────┘
     │      │      │
    ▼      ▼      ▼
Admin   Super   Manager  Karyawan
 │       │       │        │
 ▼       ▼       ▼        ▼
Admin   Sup.   Manager  Home
Dash.   Dash.  Dash.    Dashboard
```

---

## ⚙️ Konfigurasi

### Environment Variables (.env)

```env
APP_NAME="Vuexy Admin"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vuexy_admin
DB_USERNAME=root
DB_PASSWORD=

# Session
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
```

### User Model Methods

```php
// Check role
auth()
  ->user()
  ->hasRole('admin');
auth()
  ->user()
  ->hasAnyRole(['admin', 'supervisor']);

// Helper methods
auth()
  ->user()
  ->isAdmin();
auth()
  ->user()
  ->isSupervisor();
auth()
  ->user()
  ->isManager();
auth()
  ->user()
  ->isKaryawan();
```

### Middleware Usage

```php
// Single role
Route::get('/admin', function () {
  // ...
})->middleware('role:admin');

// Multiple roles
Route::get('/dashboard', function () {
  // ...
})->middleware('role:admin,supervisor');
```

---

## 🧪 Testing Routes

```bash
# Test without authentication
curl http://localhost:8000/login

# Test protected route (should redirect)
curl http://localhost:8000/dashboard

# Test admin route (should 403 if not admin)
curl http://localhost:8000/admin-dashboard
```

---

## 📊 Database Info

### Users Table

```sql
Column      | Type
------------|------------------------
id          | bigint (PRIMARY KEY)
name        | varchar(255)
email       | varchar(255) UNIQUE
password    | varchar(255)
role        | enum(4 options)
is_active   | boolean
created_at  | timestamp
updated_at  | timestamp
```

### Roles

- `admin` - Full access
- `supervisor` - Team management
- `manager` - Financial & team management
- `karyawan` - Personal dashboard only

---

## 🐛 Troubleshooting

| Problem                       | Solution                           |
| ----------------------------- | ---------------------------------- |
| "Target class does not exist" | `composer dump-autoload`           |
| "Invalid credentials"         | Check email/password, run seeder   |
| "Unauthorized"                | Verify user role, check middleware |
| "Database not connected"      | Check .env database config         |
| "500 Error"                   | `php artisan config:cache`         |

---

## 📝 Common Commands

```bash
# User Management
php artisan tinker
# Di tinker:
$user = User::find(1);
$user->role = 'admin';
$user->save();

# Clear Everything
php artisan optimize:clear

# Fresh Database
php artisan migrate:refresh --seed

# Generate User Password Hash
php artisan tinker
Hash::make('password123')

# View All Routes
php artisan route:list
```

---

## 🔐 Security Tips

1. **Change Passwords** - Update seeder password untuk production
2. **Update .env** - Use strong APP_KEY & database password
3. **Disable Debugging** - Set APP_DEBUG=false di production
4. **Use HTTPS** - Wajib untuk production
5. **Rate Limiting** - Add throttle middleware ke login
6. **CSRF Protection** - Already enabled by default

---

## 📚 Documentation

- Full Setup Guide: `AUTHENTICATION_SETUP.md`
- Laravel Docs: https://laravel.com/docs
- Vuexy Docs: Check template documentation

---

**Last Updated**: April 14, 2026  
**Version**: 1.0.0  
**Status**: ✅ Production Ready
