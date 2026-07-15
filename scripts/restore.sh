#!/usr/bin/env bash
# ============================================================
# restore.sh — Barcode Management System Restore
# ============================================================
# Supports Ubuntu 24.04 LTS
# Usage: sudo ./restore.sh <backup_file.sql.gz> [--env=<env_file.tar.gz>]
# ============================================================

set -euo pipefail

# ── Colors ──
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# ── Configuration ──
DB_NAME="${DB_DATABASE:-barcode_management}"
DB_USER="${DB_USERNAME:-barcode_user}"
DB_PASS="${DB_PASSWORD:-barcode_password}"
DB_HOST="${DB_HOST:-localhost}"
DB_PORT="${DB_PORT:-5432}"
APP_DIR="/var/www/barcode"
BACKUP_FILE=""
ENV_FILE=""

# ── Parse arguments ──
for arg in "$@"; do
    case $arg in
        --env=*)
            ENV_FILE="${arg#*=}"
            shift
            ;;
        --help)
            echo "Usage: $0 <backup_file.sql.gz> [--env=<env_file.tar.gz>]"
            echo ""
            echo "Arguments:"
            echo "  <backup_file.sql.gz>   Path to database backup file"
            echo ""
            echo "Options:"
            echo "  --env=<file.tar.gz>    Restore environment files"
            echo "  --help                 Show this help message"
            exit 0
            ;;
        *)
            BACKUP_FILE="$arg"
            shift
            ;;
    esac
done

# ── Helper Functions ──
log_info() { echo -e "${BLUE}[INFO]${NC} $1"; }
log_success() { echo -e "${GREEN}[SUCCESS]${NC} $1"; }
log_warning() { echo -e "${YELLOW}[WARNING]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }

check_root() {
    if [[ $EUID -ne 0 ]]; then
        log_error "This script must be run as root (sudo)."
        exit 1
    fi
}

validate_backup() {
    if [[ -z "$BACKUP_FILE" ]]; then
        log_error "No backup file specified."
        echo "Usage: $0 <backup_file.sql.gz> [--env=<env_file.tar.gz>]"
        exit 1
    fi

    if [[ ! -f "$BACKUP_FILE" ]]; then
        log_error "Backup file not found: $BACKUP_FILE"
        exit 1
    fi

    log_info "Backup file: $BACKUP_FILE ($(du -h "$BACKUP_FILE" | cut -f1))"
}

enable_maintenance_mode() {
    log_info "Enabling maintenance mode..."
    cd "$APP_DIR/backend"
    php artisan down --retry=60
    log_success "Maintenance mode enabled."
}

disable_maintenance_mode() {
    log_info "Disabling maintenance mode..."
    cd "$APP_DIR/backend"
    php artisan up
    log_success "Maintenance mode disabled."
}

restore_database() {
    log_info "Restoring database: $DB_NAME..."

    # Drop and recreate database
    PGPASSWORD="$DB_PASS" psql \
        -h "$DB_HOST" \
        -p "$DB_PORT" \
        -U "$DB_USER" \
        -d postgres \
        -c "DROP DATABASE IF EXISTS \"$DB_NAME\";" \
        -c "CREATE DATABASE \"$DB_NAME\" OWNER \"$DB_USER\";"

    # Restore from backup
    gunzip -c "$BACKUP_FILE" | PGPASSWORD="$DB_PASS" psql \
        -h "$DB_HOST" \
        -p "$DB_PORT" \
        -U "$DB_USER" \
        -d "$DB_NAME"

    log_success "Database restored from: $BACKUP_FILE"
}

restore_env() {
    if [[ -z "$ENV_FILE" ]]; then
        log_info "No env file specified. Skipping environment restore."
        return
    fi

    if [[ ! -f "$ENV_FILE" ]]; then
        log_warning "Env file not found: $ENV_FILE. Skipping."
        return
    fi

    log_info "Restoring environment files..."
    tar -xzf "$ENV_FILE" -C "$APP_DIR"
    log_success "Environment files restored."
}

clear_cache() {
    log_info "Clearing application cache..."

    cd "$APP_DIR/backend"
    php artisan optimize:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache

    log_success "Cache cleared and rebuilt."
}

print_summary() {
    echo ""
    echo "============================================"
    echo "  Restore Complete!"
    echo "============================================"
    echo ""
    echo "  Restored from: $BACKUP_FILE"
    if [[ -n "$ENV_FILE" ]]; then
        echo "  Env restored:  $ENV_FILE"
    fi
    echo ""
    echo "============================================"
}

# ── Main ──
main() {
    echo "============================================"
    echo "  Barcode Management System — Restore"
    echo "============================================"
    echo ""

    check_root
    validate_backup
    enable_maintenance_mode
    restore_database
    restore_env
    clear_cache
    disable_maintenance_mode
    print_summary
}

main "$@"
