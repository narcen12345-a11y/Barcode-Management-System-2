# Barcode Management System

A full-stack barcode management application built with Laravel 12 (backend) and React + Vite + Tailwind CSS (frontend). This system allows organizations to manage barcodes for materials across multiple sites, with role-based access control and audit logging.

## Features

- **Authentication & Authorization** — Login, logout, password management, role-based permissions
- **User Management** — CRUD users, verify, activate/deactivate, reset passwords
- **Role & Permission Management** — Create roles, assign permissions, manage access control
- **Site Management** — Manage multiple operational sites with location data
- **Material Management** — Manage material types, models, and individual materials
- **Barcode Management** — Generate, track, and manage barcodes with status (NEW/OLD)
- **Barcode History** — Full audit trail of all barcode changes
- **Activity & Audit Logs** — Comprehensive logging for security and compliance
- **Soft Deletes** — All major entities support soft deletion and restoration

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | Laravel 12, PHP 8.2+ |
| **Frontend** | React 19, Vite 5, Tailwind CSS 3 |
| **Database** | PostgreSQL 16 |
| **Authentication** | Laravel Sanctum (token-based) |
| **API** | RESTful JSON API |
| **UI Components** | shadcn/ui, Lucide Icons |
| **State Management** | TanStack React Query |
| **HTTP Client** | Axios |

## Requirements

- PHP 8.2 or higher
- Composer 2.5 or higher
- Node.js 20 or higher
- NPM 10 or higher
- PostgreSQL 15 or higher (or Docker)
- Docker (optional, for database)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/narcen12345-a11y/Barcode-Management-System-2.git
cd Barcode-Management-System-2
```

### 2. Database Setup

Choose one of the following methods:

**Option A: Using Docker (Recommended)**
```bash
docker compose up -d postgres
```

**Option B: Manual PostgreSQL Setup**
Create a PostgreSQL database and user, then update the `.env` file accordingly.

### 3. Backend Setup

```bash
cd backend

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Edit .env with your database credentials
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=barcode_management
# DB_USERNAME=postgres
# DB_PASSWORD=your_password

# Generate application key
php artisan key:generate

# Run database migrations and seeders
php artisan migrate --seed

# Create storage symlink
php artisan storage:link

# Start development server
php artisan serve
```

### 4. Frontend Setup

```bash
cd frontend

# Install Node.js dependencies
npm install

# Copy environment file (if needed)
cp .env.example .env

# Start development server
npm run dev
```

### 5. Access the Application

- **Frontend:** http://localhost:5173
- **Backend API:** http://localhost:8000/api
- **Health Check:** http://localhost:8000/api/health

## Default Login Credentials

| Username | Password | Role |
|----------|----------|------|
| `admin` | `admin123` | Super Admin (full access) |
| `manager` | `admin123` | Admin (master data management) |
| `operator` | `admin123` | User (read + barcode operations) |

## Project Structure

```
barcode-management-system/
├── backend/                    # Laravel 12 API
│   ├── app/
│   │   ├── Enums/              # PHP enums (BarcodeStatus, UserStatus, etc.)
│   │   ├── Http/
│   │   │   ├── Controllers/    # API controllers
│   │   │   ├── Middleware/     # CheckPermission middleware
│   │   │   ├── Requests/       # Form request validation
│   │   │   └── Resources/      # API resource transformers
│   │   ├── Interfaces/         # Repository interfaces
│   │   ├── Models/             # Eloquent models
│   │   ├── Repositories/       # Data access layer
│   │   ├── Services/           # Business logic layer
│   │   └── DTOs/               # Data transfer objects
│   ├── bootstrap/              # App bootstrap & middleware config
│   ├── config/                 # Laravel configuration
│   ├── database/
│   │   ├── migrations/         # Database migrations (12 files)
│   │   └── seeders/            # Database seeders
│   ├── routes/                 # API routes
│   └── public/                 # Public entry point
├── frontend/                   # React + Vite SPA
│   ├── src/
│   │   ├── api/                # Axios client configuration
│   │   ├── auth/               # Authentication storage
│   │   ├── components/         # Reusable UI components
│   │   ├── contexts/           # React contexts (Auth)
│   │   ├── hooks/              # Custom hooks (useCrud, useFormSubmit)
│   │   ├── layouts/            # App layouts (Sidebar, Topbar, MainLayout)
│   │   ├── pages/              # Page components
│   │   ├── routes/             # Route definitions
│   │   ├── services/           # API service classes
│   │   └── utils/              # Utility functions
│   └── docs/                   # Frontend documentation
├── docs/                       # Project documentation
│   ├── audit/                  # Audit and QA reports
│   ├── project/                # Project index
│   └── ... (various .md files)
├── docker-compose.yml          # Docker services configuration
└── README.md                   # This file
```

## Architecture

The application follows a **layered architecture** with clear separation of concerns:

- **Controller Layer** — Handles HTTP requests/responses
- **Service Layer** — Contains business logic
- **Repository Layer** — Data access and persistence
- **DTO Layer** — Data transfer objects for type safety
- **Form Request Layer** — Input validation
- **API Resource Layer** — Response transformation

### API Design

- RESTful JSON API with consistent response format
- Token-based authentication via Laravel Sanctum
- Permission-based middleware for access control
- Pagination, filtering, and search support
- Soft deletes with restore endpoints

## Deployment

For production deployment instructions, see [DEPLOYMENT.md](docs/DEPLOYMENT.md).

For the pre-deployment checklist, see [PRODUCTION_CHECKLIST.md](docs/PRODUCTION_CHECKLIST.md).

### Production Build

```bash
# Backend
cd backend
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Frontend
cd frontend
npm ci
npm run build
```

## License

This project is proprietary software. All rights reserved.
