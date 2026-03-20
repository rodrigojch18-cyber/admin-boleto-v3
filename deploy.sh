#!/bin/bash
# ═══════════════════════════════════════════════════════════════
# Boleto Admin Pro — DigitalOcean Deployment Script
# Run as root on an Ubuntu 22.04+ Droplet
# ═══════════════════════════════════════════════════════════════

set -e

# ─── Configuration ─────────────────────────────────────────────
REPO_URL="https://github.com/rodrigojch18-cyber/admin-boleto-v3.git"
DOMAIN="boleto-admin.example.com"   # Change to your domain or Droplet IP
WEB_DIR="/var/www/boleto-admin"
APACHE_CONF="/etc/apache2/sites-available/boleto-admin.conf"

echo "═══════════════════════════════════════════════════════════"
echo "  Boleto Admin Pro — Deployment"
echo "═══════════════════════════════════════════════════════════"
echo ""

# ─── 1. System Update ───────────────────────────────────────────
echo "[1/6] Updating system packages..."
apt update -y && apt upgrade -y

# ─── 2. Install PHP 8+ and Apache ──────────────────────────────
echo "[2/6] Installing Apache and PHP 8..."
apt install -y apache2 \
    php8.1 php8.1-cli php8.1-common php8.1-curl php8.1-mbstring php8.1-xml \
    libapache2-mod-php8.1 \
    git curl unzip

# Enable required Apache modules
a2enmod rewrite headers

# ─── 3. Clone Repository ──────────────────────────────────────
echo "[3/6] Cloning repository..."
if [ -d "$WEB_DIR" ]; then
    echo "  → Updating existing installation..."
    cd "$WEB_DIR"
    git pull origin main
else
    git clone "$REPO_URL" "$WEB_DIR"
fi

# Set permissions
chown -R www-data:www-data "$WEB_DIR"
chmod -R 755 "$WEB_DIR"

# ─── 4. Apache Configuration ──────────────────────────────────
echo "[4/6] Configuring Apache..."
cat > "$APACHE_CONF" << VHOST
<VirtualHost *:80>
    ServerName $DOMAIN
    DocumentRoot $WEB_DIR

    <Directory $WEB_DIR>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>

    # Security: Block access to config files
    <FilesMatch "^(config\.php|\.env|\.git)">
        Require all denied
    </FilesMatch>

    ErrorLog \${APACHE_LOG_DIR}/boleto-admin-error.log
    CustomLog \${APACHE_LOG_DIR}/boleto-admin-access.log combined
</VirtualHost>
VHOST

# Enable site and disable default
a2ensite boleto-admin.conf
a2dissite 000-default.conf 2>/dev/null || true

# ─── 5. Restart Apache ────────────────────────────────────────
echo "[5/6] Restarting Apache..."
systemctl restart apache2
systemctl enable apache2

# ─── 6. Verification ──────────────────────────────────────────
echo "[6/6] Verifying installation..."
echo ""

# Check Apache status
if systemctl is-active --quiet apache2; then
    echo "  ✓ Apache is running"
else
    echo "  ✗ Apache failed to start"
    systemctl status apache2
    exit 1
fi

# Check PHP version
PHP_VER=$(php -v | head -n 1)
echo "  ✓ $PHP_VER"

# Check files
if [ -f "$WEB_DIR/index.php" ]; then
    echo "  ✓ Application files deployed"
else
    echo "  ✗ Application files not found"
    exit 1
fi

echo ""
echo "═══════════════════════════════════════════════════════════"
echo "  ✓ Deployment Complete!"
echo "═══════════════════════════════════════════════════════════"
echo ""
echo "  Next steps:"
echo "  1. Edit $WEB_DIR/config.php with your Supabase credentials"
echo "  2. Open http://$DOMAIN in your browser"
echo "  3. (Optional) Set up SSL with: certbot --apache -d $DOMAIN"
echo ""
echo "  IMPORTANT: Replace SUPABASE_URL and SUPABASE_ANON_KEY in config.php"
echo ""
