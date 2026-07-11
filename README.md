# Barcode Management System

## Struktur project

- backend/: scaffold Laravel 12
- frontend/: scaffold React + Vite + Tailwind
- docker-compose.yml: konfigurasi PostgreSQL dan layanan aplikasi

## Cara menjalankan

1. Jalankan database:
   docker compose up -d postgres

2. Jalankan backend:
   cd backend
   cp .env.example .env
   composer install
   php artisan serve

3. Jalankan frontend:
   cd frontend
   npm install
   npm run dev

## Catatan

Struktur ini dibuat sebagai baseline untuk pengembangan tahap berikutnya.