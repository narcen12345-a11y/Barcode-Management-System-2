#!/usr/bin/env bash
# ============================================================
# deploy.sh — Barcode Management System Production Deployment
# ============================================================
# Supports Ubuntu 24.04 LTS
# Usage: sudo ./deploy.sh [--docker] [--branch=<branch>]
# ============================================================

set -euo pipefail

# ── Colors ──
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# ── Configuration ──
REPO_URL="https://github.com/narcen12345-a11y/Barcode-Management-System-2.git"
APP_DIR="/var/www/barcode"
BRANCH="main"
USE_DOCKER=false
PHP_VERSION="8.2"
NODE_VERSION="20"

# ── Parse arguments ──
for arg in "$@"; do
    case $arg in
        --docker)
            USE_DOCKER=true
            shift
            ;;
        --branch=*)
            BRANCH="${arg#*=}"
            shift
            ;;
        --help)
            echo "Usage: $0 [--docker] [--branch=<branch>]"
            echo ""
            echo "Options:"
            echo "  --docker         Deploy using Docker (default: bare-metal)"
            echo "  --branch=<name>  Git branch to deploy (default: main)"
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

install_system_deps() {
    log_info "Updating system packages..."
    apt-get update -qq
    apt-get upgrade -y -qq

    log_info "Installing system dependencies..."
    apt-get install -y -qq \
        curl \
        wget \
        git \
        unzip \
        zip \
        gnupg \
        ca-certificates \
        lsb-release \
        software-properties-common \
        nginx \
        supervisor \
        postgresql-client \
        jq

    log_success "System dependencies installed."
}

install_php() {
    log_info "Installing PHP $PHP_VERSION..."

    add-apt-repository -y ppa:ondrej/php
    apt-get update -qq

    apt-get install -y -qq \
        php${PHP_VERSION}-fpm \
        php${PHP_VERSION}-cli \
        php${PHP_VERSION}-pgsql \
        php${PHP_VERSION}-mbstring \
        php${PHP_VERSION}-xml \
        php${PHP_VERSION}-curl \
        php${PHP_VERSION}-bcmath \
        php${PHP_VERSION}-zip \
        php${PHP_VERSION}-opcache \
        php${PHP_VERSION}-redis

    # Configure PHP for production
    sed -i 's/display_errors = On/display_errors = Off/' /etc/php/${PHP_VERSION}/fpm/php.ini
    sed -i 's/display_errors = On/display_errors = Off/' /etc/php/${PHP_VERSION}/cli/php.ini
    sed -i 's/memory_limit = 128M/memory_limit = 256M/' /etc/php/${PHP_VERSION}/fpm/php.ini
    sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 20M/' /etc/php/${PHP_VERSION}/fpm/php.ini
    sed -i 's/post_max_size = 8M/post_max_size = 20M/' /etc/php/${PHP_VERSION}/fpm/php.ini
    sed -i 's/max_execution_time = 30/max_execution_time = 180/' /etc/php/${PHP_VERSION}/fpm/php.ini

    # OPcache
    cat > /etc/php/${PHP_VERSION}/cli/conf.d/99-opcache.ini << 'OPCACHE'
[opcache]
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
OPCACHE

    systemctl restart php${PHP_VERSION}-fpm
    log_success "PHP $PHP_VERSION installed and configured."
}

install_composer() {
    log_info "Installing Composer..."
    EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

    if [[ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]]; then
        log_error "Composer installer checksum mismatch."
        rm composer-setup.php
        exit 1
    fi

    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    rm composer-setup.php
    log_success "Composer installed."
}

install_node() {
    log_info "Installing Node.js $NODE_VERSION..."
    curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash -
    apt-get install -y -qq nodejs
    log_success "Node.js $(node --version) installed."
}

clone_repository() {
    log_info "Cloning repository (branch: $BRANCH)..."

    if [[ -d "$APP_DIR" ]]; then
        log_info "Directory $APP_DIR exists. Pulling latest changes..."
        cd "$APP_DIR"
        git fetch origin
        git checkout "$BRANCH"
        git pull origin "$BRANCH"
    else
        git clone --branch "$BRANCH" --depth 1 "$REPO_URL" "$APP_DIR"
        cd "$APP_DIR"
    fi

    log_success "Repository cloned at $APP_DIR"
}

setup_backend() {
    log_info "Setting up backend..."

    cd "$APP_DIR/backend"

    # Create .env if not exists
    if [[ ! -f .env ]]; then
        cp .env.example .env
        log_info "Created .env file — please update with production values."
    fi

    # Install PHP dependencies
    composer install --no-dev --optimize-autoloader --no-interaction

    # Generate app key if not set
    if ! grep -q "APP_KEY=base64" .env; then
        php artisan key:generate --force
    fi

    # Create storage link
    php artisan storage:link --force

    # Cache for production
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache

    # Set permissions
    chown -R www-data:www-data storage bootstrap/cache
    chmod -R 775 storage bootstrap/cache

    log_success "Backend setup complete."
}

setup_frontend() {
    log_info "Setting up frontend..."

    cd "$APP_DIR/frontend"

    # Create .env if not exists
    if [[ ! -f .env ]]; then
        cp .env.example .env
        log_info "Created frontend .env file — please update with production API URL."
    fi

    # Install dependencies
    npm ci

    # Build for production
    npm run build

    log_success "Frontend build complete."
}

setup_nginx() {
    log_info "Configuring Nginx..."

    # Backend API
    cat > /etc/nginx/sites-available/barcode-backend << 'NGINX_BACKEND'
server {
    listen 80;
    server_name _;
    root /var/www/barcode/backend/public;

    index index.php;
    charset utf-8;

    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=(), interest-cohort=()" always;
    add_header X-XSS-Protection "0" always;

    client_max_body_size 20M;

    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml application/json application/javascript application/xml+rss application/atom+xml image/svg+xml;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 180;
    }

    location ~ /\.(?!well-known).* { deny all; }
    location ~ ^/(storage/app|storage/framework|storage/logs|bootstrap/cache|\.env) { deny all; }
}
NGINX_BACKEND

    # Frontend SPA
    cat > /etc/nginx/sites-available/barcode-frontend << 'NGINX_FRONTEND'
server {
    listen 80;
    server_name _;
    root /var/www/barcode/frontend/dist;

    index index.html;
    charset utf-8;

    add_header X-Frame-Options "DENY" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=(), interest-cohort=()" always;
    add_header X-XSS-Protection "0" always;

    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml application/json application/javascript application/xml+rss application/atom+xml image/svg+xml;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires max;
        add_header Cache-Control "public, immutable";
        log_not_found off;
    }

    location = /health {
        access_log off;
        return 200 '{"status":"ok"}';
        add_header Content-Type application/json;
    }

    location ~ /\. { deny all; }
}
NGINX_FRONTEND

    # Enable sites
    ln -sf /etc/nginx/sites-available/barcode-backend /etc/nginx/sites-enabled/
    ln -sf /etc/nginx/sites-available/barcode-frontend /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default

    # Test and reload
    nginx -t && systemctl reload nginx

    log_success "Nginx configured."
}

setup_supervisor() {
    log_info "Configuring Supervisor for queue worker..."

    cat > /etc/supervisor/conf.d/barcode-queue.conf << 'SUPERVISOR'
[program:barcode-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/barcode/backend/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/barcode/backend/storage/logs/queue-worker.log
stopwaitsecs=3600
SUPERVISOR

    supervisorctl reread
    supervisorctl update
    supervisorctl start barcode-queue:*

    log_success "Supervisor configured."
}

setup_cron() {
    log_info "Setting up Laravel scheduler cron..."

    # Add cron entry for Laravel scheduler
    (crontab -l 2>/dev/null | grep -v "artisan schedule:run"; echo "* * * * * cd $APP_DIR/backend && php artisan schedule:run >> /dev/null 2>&1") | crontab -

    log_success "Cron configured."
}

setup_docker_deploy() {
    log_info "Deploying with Docker..."

    cd "$APP_DIR"

    # Create .env if not exists
    if [[ ! -f backend/.env ]]; then
        cp backend/.env.example backend/.env
        log_info "Created backend .env — please update with production values."
    fi

    # Build and start containers
    docker compose build --no-cache
    docker compose up -d

    # Run migrations
    docker compose exec -T backend php artisan migrate --force

    log_success "Docker deployment complete."
}

run_migrations() {
    log_info "Running database migrations..."

    cd "$APP_DIR/backend"
    php artisan migrate --force

    log_success "Migrations complete."
}

print_summary() {
    echo ""
    echo "============================================"
    echo "  Deployment Complete!"
    echo "============================================"
    echo ""
    echo "  Backend API:  http://localhost:8000"
    echo "  Frontend App: http://localhost"
    echo "  Health Check: http://localhost:8000/api/health"
    echo ""
    echo "  Next Steps:"
    echo "  1. Configure SSL (certbot)"
    echo "  2. Update .env with production values"
    echo "  3. Set up database backups"
    echo "  4. Configure monitoring"
    echo ""
    echo "============================================"
}

# ── Main ──
main() {
    echo "============================================"
    echo "  Barcode Management System — Deploy Script"
    echo "============================================"
    echo ""

    check_root

    if [[ "$USE_DOCKER" == true ]]; then
        install_system_deps
        clone_repository
        setup_docker_deploy
    else
        install_system_deps
        install_php
        install_composer
        install_node
        clone_repository
        setup_backend
        setup_frontend
        setup_nginx
        setup_supervisor
        setup_cron
        run_migrations
    fi

    print_summary
}

main "$@"
