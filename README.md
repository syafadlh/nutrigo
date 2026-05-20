# 🥗 NutriGo

> Platform rekomendasi menu makanan berbasis kalori harian, wilayah, dan kondisi kesehatan pengguna.
> **Bukan program diet** — tapi gaya hidup makan sehat yang menyenangkan!

![Laravel](https://img.shields.io/badge/Laravel-11-red?logo=laravel)
![Tailwind](https://img.shields.io/badge/TailwindCSS-3-blue?logo=tailwindcss)
![PHP](https://img.shields.io/badge/PHP-8.2+-purple?logo=php)

---

## 🚀 Fitur Utama

- ✅ Hitung kalori harian otomatis (rumus Mifflin-St Jeor)
- ✅ Rekomendasi menu 3x sehari sesuai kalori & wilayah
- ✅ Filter alergi makanan otomatis
- ✅ Variasi menu (anti repeat dalam 3 hari)
- ✅ Pengingat makan (meal reminder)
- ✅ Riwayat makan + grafik kalori
- ✅ Artikel kesehatan & nutrisi
- ✅ Admin panel lengkap
- ✅ Onboarding 5 langkah untuk user baru

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 11 |
| Frontend | Blade + Tailwind CSS + Alpine.js |
| Database | MySQL |
| Charts | Chart.js |
| Build | Vite |

---

## 📦 Instalasi

### Prasyarat
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/USERNAME/nutrigo.git
cd nutrigo

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
# DB_DATABASE=nutrigo
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Konfigurasi admin emails di .env
# NUTRIGO_ADMIN_EMAILS="admin@nutrigo.id,email@lain.com"

# 6. Migrate & seed
php artisan migrate
php artisan db:seed

# 7. Import data makanan dari CSV
php artisan nutrigo:import-foods

# 8. Build assets
npm run build

# 9. Jalankan server
php artisan serve
```

### Akun Default

| Role | Email | Password |
|---|---|---|
| Admin | admin@nutrigo.id | Admin@123 |
| User | user@nutrigo.id | User@123 |

> User default digunakan untuk testing onboarding, rekomendasi menu, reminder, dan history makanan.

---

## 🗂️ Struktur Database