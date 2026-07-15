#!/usr/bin/env bash
# ============================================================
# backup.sh — Barcode Management System Backup
# ============================================================
# Supports Ubuntu 24.04 LTS
# Usage: sudo ./backup.sh [--s3] [--retention=<days>]
# ============================================================

set -euo pipefail

# ── Colors ──
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# ── Configuration ──
BACKUP_DIR="/var/backups/barcode"
DB_NAME="${DB_DATABASE:-barcode_management}"
DB_USER="${DB_USERNAME:-barcode_user}"
DB_PASS="${DB_PASSWORD:-barcode_password}"
DB_HOST="${DB_HOST:-localhost}"
DB_PORT="${DB_PORT:-5432}"
APP_DIR="/var/www/barcode"
RETENTION_DAYS=30
USE_S3=false
S3_BUCKET=""
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="${BACKUP_DIR}/barcode_${TIMESTAMP}.sql.gz"
ENV_FILE="${BACKUP_DIR}/env_${TIMESTAMP}.tar.gz"

# ── Parse arguments ──
for arg in "$@"; do
    case $arg in
        --s3)
            USE_S3=true
            shift
            ;;
        --retention=*)
            RETENTION_DAYS="${arg#*=}"
            shift
            ;;
        --help)
            echo "Usage: $0 [--s3] [--retention=<days>]"
            echo ""
            echo "Options:"
            echo "  --s3              Upload backup to S3-compatible storage"
            echo "  --retention=<n>   Days to keep local backups (default: 30)"
            echo "  --help            Show this help message"
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

setup_backup_dir() {
    mkdir -p "$BACKUP_DIR"
    log_info "Backup directory: $BACKUP_DIR"
}

backup_database() {
    log_info "Backing up database: $DB_NAME..."

    PGPASSWORD="$DB_PASS" pg_dump \
        -h "$DB_HOST" \
        -p "$DB_PORT" \
        -U "$DB_USER" \
        -d "$DB_NAME" \
        --clean \
        --if-exists \
        --no-owner \
        --no-privileges \
        | gzip > "$BACKUP_FILE"

    log_success "Database backup saved: $BACKUP_FILE"
    log_info "Backup size: $(du -h "$BACKUP_FILE" | cut -f1)"
}

backup_env() {
    log_info "Backing up environment files..."

    tar -czf "$ENV_FILE" \
        -C "$APP_DIR" \
        backend/.env \
        frontend/.env \
        2>/dev/null || true

    log_success "Environment backup saved: $ENV_FILE"
}

upload_to_s3() {
    if [[ "$USE_S3" == false ]]; then
        return
    fi

    if ! command -v aws &> /dev/null; then
        log_warning "AWS CLI not installed. Skipping S3 upload."
        return
    fi

    log_info "Uploading to S3 bucket: $S3_BUCKET..."

    aws s3 cp "$BACKUP_FILE" "s3://${S3_BUCKET}/database/"
    aws s3 cp "$ENV_FILE" "s3://${S3_BUCKET}/env/"

    log_success "Backup uploaded to S3."
}

cleanup_old_backups() {
    log_info "Cleaning up backups older than $RETENTION_DAYS days..."

    find "$BACKUP_DIR" -name "barcode_*.sql.gz" -type f -mtime "+$RETENTION_DAYS" -delete
    find "$BACKUP_DIR" -name "env_*.tar.gz" -type f -mtime "+$RETENTION_DAYS" -delete

    log_success "Old backups cleaned."
}

print_summary() {
    echo ""
    echo "============================================"
    echo "  Backup Complete!"
    echo "============================================"
    echo ""
    echo "  Database: $BACKUP_FILE"
    echo "  Env:      $ENV_FILE"
    echo "  S3 Sync:  $USE_S3"
    echo "  Retention: $RETENTION_DAYS days"
    echo ""
    echo "============================================"
}

# ── Main ──
main() {
    echo "============================================"
    echo "  Barcode Management System — Backup"
    echo "============================================"
    echo ""

    check_root
    setup_backup_dir
    backup_database
    backup_env
    upload_to_s3
    cleanup_old_backups
    print_summary
}

main "$@"
