# Uji Kompetensi Programmer - Jobhun

Project Name : Perpustakaan
Project description : A Laravel project using Vue.js and Inertia.

## Prerequisites

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/MariaDB/SQLite

## Installation Steps

### 1. Clone the Repository

```bash
git clone <repository-url>
cd uji-sertifikasi
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Install Node.js dependencies
```bash
npm install
```

### 4. Environtment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure Database
# Note: use SQLite or MySQL for migration "trigger stok buku" compabilities

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

### 6. Run Migration
```bash
npm run dev
```

### 7. Start Development Server
```bash
npm run dev # vite dev server
npm run build # vite build
php artisan serve # start laravel server
```