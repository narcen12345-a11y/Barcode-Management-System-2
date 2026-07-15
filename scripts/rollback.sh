#!/usr/bin/env bash
# ============================================================
# rollback.sh — Barcode Management System Rollback
# ============================================================
# Supports Ubuntu 24.04 LTS
# Usage: sudo ./rollback.sh [--tag=<tag>] [--steps=<count>]
# ============================================================

set -euo pipefail

# ── Colors ──
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# ── Configuration ──
APP_DIR="/var/www/barcode"
TAG=""
STEPS=1
PHP_VERSION="8.2"

# ── Parse arguments ──
for arg in "$@"; do
    case $arg in
        --tag=*)
            TAG="${arg#*=}"
            shift
            ;;
        --steps=*)
            STEPS="${arg#*=}"
            shift
            ;;
        --help)
            echo "Usage: $0 [--tag=<tag>] [--steps=<count>]"
            echo ""
            echo "Options:"
            echo "  --tag=<tag>      Rollback to specific git tag"
            echo "  --steps=<count>  Rollback N commits (default: 1)"
            echo "  --help           Show this help message"
            exit 0
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

rollback_git() {
    cd "$APP_DIR"

    if [[ -n "$TAG" ]]; then
        log_info "Rolling back to tag: $TAG"
        git fetch --tags
        git checkout "$TAG"
    else
        log_info "Rolling back $STEPS commit(s)..."
        git pull origin main
        git reset --hard HEAD~$STEPS
    fi

    log_success "Git rollback complete."
}

rollback_database() {
    log_info "Rolling back database migrations..."
    cd "$APP_DIR/backend"
    php artisan migrate:rollback --force
    log_success "Database rollback complete."
}

rebuild_backend() {
    log_info "Rebuilding backend..."

    cd "$APP_DIR/backend"

    # Restore previous composer.lock if available
    if git show HEAD:composer.lock > /dev/null 2>&1; then
        git show HEAD:composer.lock > composer.lock
    fi

    composer install --no-dev --optimize-autoloader --no-interaction

    # Clear and re-cache
    php artisan optimize:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache

    # Permissions
    chown -R www-data:www-data storage bootstrap/cache
    chmod -R 775 storage bootstrap/cache

    log_success "Backend rebuild complete."
}

rebuild_frontend() {
    log_info "Rebuilding frontend..."

    cd "$APP_DIR/frontend"

    # Restore previous package-lock.json if available
    if git show HEAD:package-lock.json > /dev/null 2>&1; then
        git show HEAD:package-lock.json > package-lock.json
    fi

    npm ci
    npm run build

    log_success "Frontend rebuild complete."
}

restart_services() {
    log_info "Restarting services..."

    systemctl reload nginx
    systemctl reload php${PHP_VERSION}-fpm
    supervisorctl restart barcode-queue:*

    log_success "Services restarted."
}

print_summary() {
    echo ""
    echo "============================================"
    echo "  Rollback Complete!"
    echo "============================================"
    echo ""
    if [[ -n "$TAG" ]]; then
        echo "  Rolled back to tag: $TAG"
    else
        echo "  Rolled back $STEPS commit(s)"
    fi
    echo "  Current commit: $(cd $APP_DIR && git log --oneline -1)"
    echo ""
    echo "============================================"
}

# ── Main ──
main() {
    echo "============================================"
    echo "  Barcode Management System — Rollback"
    echo "============================================"
    echo ""

    check_root

    if [[ ! -d "$APP_DIR" ]]; then
        log_error "Application directory $APP_DIR not found."
        exit 1
    fi

    enable_maintenance_mode
    rollback_git
    rollback_database
    rebuild_backend
    rebuild_frontend
    restart_services
    disable_maintenance_mode
    print_summary
}

main "$@"
