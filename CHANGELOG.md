# Changelog

All notable changes to the Barcode Management System are documented in this file.

## [1.0.0] - 2026-07-15

### Added
- **Authentication System**
  - Login with username or email
  - Logout with token revocation
  - Get authenticated user profile (/me)
  - Change password with current password verification
  - Rate limiting (5 attempts per minute) on login endpoint
  - Security headers middleware (CSP, HSTS, X-Frame-Options, etc.)

- **User Management**
  - CRUD operations with soft delete and restore
  - User activation/deactivation
  - Password reset with cryptographically secure random generation
  - User verification workflow
  - Role and permission assignment

- **Role-Based Access Control (RBAC)**
  - Role CRUD with soft delete and restore
  - Permission CRUD with soft delete and restore
  - Granular permission checking via middleware
  - Super admin role with full access

- **Master Data Management**
  - Sites CRUD with soft delete and restore
  - Material Types CRUD with soft delete and restore
  - Material Models CRUD with soft delete and restore
  - Materials CRUD with soft delete and restore
  - All master data supports pagination, search, and filtering

- **Barcode Management**
  - Barcode CRUD with soft delete and restore
  - Automatic barcode number generation
  - Barcode status workflow (active → printed → used/damaged/lost/scrapped)
  - Barcode printing support
  - Barcode history tracking with audit trail
  - Status change reasons (for damaged, lost, scrapped)

- **Frontend (React + Vite)**
  - Login page with form validation
  - Dashboard page
  - CRUD engine with reusable components
  - Data tables with pagination, search, and filters
  - Form pages with validation
  - Detail pages
  - Confirmation dialogs for delete/restore
  - Loading states and error handling
  - Protected routes with permission-based rendering
  - Responsive sidebar navigation

- **Documentation**
  - Comprehensive API documentation
  - Database design and ERD
  - Business rules and validation rules
  - UI documentation for all pages
  - Deployment guide and production checklist
  - Coding standards and folder structure
  - Permission matrix

### Performance
- Fixed N+1 queries in UserRepository (eager load roles)
- Fixed N+1 queries in RoleRepository (eager load permissions)
- Removed unused imports in BarcodeRepository
- Removed unused variables in AuthenticationService
- Removed redundant load() calls in BarcodeController
- Added database indexes for performance optimization
- Generated performance optimization audit report

### Security
- Removed password exposure from resetPassword API response
- Replaced str_shuffle with random_bytes for CSPRNG password generation
- Added rate limiting (throttle:5,1) on login endpoint
- Created SecurityHeaders middleware (CSP, HSTS, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy)
- Registered SecurityHeaders middleware in API group
- Generated comprehensive security audit report

### Testing
- Feature tests for Authentication (login, logout, me, change password)
- Feature tests for Users (CRUD, soft delete, restore, activate, deactivate, reset password, verify)
- Feature tests for Roles (CRUD, soft delete, restore)
- Feature tests for Permissions (CRUD, soft delete, restore)
- Feature tests for Sites (CRUD, soft delete, restore)
- Feature tests for Material Types (CRUD, soft delete, restore)
- Feature tests for Material Models (CRUD, soft delete, restore)
- Feature tests for Materials (CRUD, soft delete, restore)
- Feature tests for Barcodes (CRUD, soft delete, restore, status transitions, history, print)
- Feature tests for Barcode History (list, detail)
- Tests cover: authentication, authorization, validation, HTTP status, JSON structure, database changes

### Architecture
- Repository pattern for data access abstraction
- Service layer for business logic
- DTOs for data transfer
- Form Requests for validation
- API Resources for response formatting
- Enum-based status management
- Soft delete support across all entities
- Sanctum-based API authentication
- PostgreSQL database with SQLite testing support
