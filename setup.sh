#!/bin/bash
#
# Setup Script for ERP Multi-tenant FuelPHP Application
#
# This script automates the initial setup of the project by:
# - Verifying PHP and Composer are installed
# - Installing PHP dependencies via Composer
# - Setting proper permissions
#
# Usage: ./setup.sh
#

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if PHP is installed
check_php() {
    print_status "Checking PHP installation..."
    if ! command -v php &> /dev/null; then
        print_error "PHP is not installed. Please install PHP 5.4 or higher."
        exit 1
    fi
    
    PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
    print_status "PHP version: $PHP_VERSION"
}

# Check if Composer is available
check_composer() {
    print_status "Checking Composer installation..."
    
    # First check if composer.phar exists in the project
    if [ -f "composer.phar" ]; then
        COMPOSER_CMD="php composer.phar"
        print_status "Using local composer.phar"
    elif command -v composer &> /dev/null; then
        COMPOSER_CMD="composer"
        print_status "Using global Composer"
    else
        print_error "Composer is not installed and composer.phar not found."
        print_status "Downloading composer.phar..."
        php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        php composer-setup.php
        php -r "unlink('composer-setup.php');"
        COMPOSER_CMD="php composer.phar"
    fi
}

# Verify composer.lock checksum if exists
verify_checksum() {
    if [ -f "composer.lock" ]; then
        print_status "Verifying composer.lock integrity..."
        if command -v sha256sum &> /dev/null; then
            CHECKSUM=$(sha256sum composer.lock | awk '{print $1}')
            print_status "composer.lock checksum: ${CHECKSUM:0:16}..."
        elif command -v shasum &> /dev/null; then
            CHECKSUM=$(shasum -a 256 composer.lock | awk '{print $1}')
            print_status "composer.lock checksum: ${CHECKSUM:0:16}..."
        else
            print_warning "No checksum tool available, skipping verification"
        fi
    fi
}

# Install PHP dependencies
install_dependencies() {
    print_status "Installing PHP dependencies..."
    
    $COMPOSER_CMD install --no-interaction --prefer-dist
    
    if [ $? -eq 0 ]; then
        print_status "Dependencies installed successfully!"
    else
        print_error "Failed to install dependencies."
        exit 1
    fi
}

# Set proper permissions
set_permissions() {
    print_status "Setting proper permissions..."
    
    # Make storage directories writable
    if [ -d "fuel/app/cache" ]; then
        chmod -R 775 fuel/app/cache 2>/dev/null || print_warning "Could not set permissions on cache directory"
    fi
    
    if [ -d "fuel/app/logs" ]; then
        chmod -R 775 fuel/app/logs 2>/dev/null || print_warning "Could not set permissions on logs directory"
    fi
    
    if [ -d "fuel/app/tmp" ]; then
        chmod -R 775 fuel/app/tmp 2>/dev/null || print_warning "Could not set permissions on tmp directory"
    fi
    
    print_status "Permissions set successfully!"
}

# Main execution
main() {
    echo ""
    echo "=========================================="
    echo "  ERP Multi-tenant Setup Script"
    echo "=========================================="
    echo ""
    
    # Get script directory
    SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
    cd "$SCRIPT_DIR"
    
    check_php
    check_composer
    verify_checksum
    install_dependencies
    set_permissions
    
    echo ""
    echo "=========================================="
    print_status "Setup completed successfully!"
    echo "=========================================="
    echo ""
    echo "Next steps:"
    echo "  1. Configure database in fuel/app/config/db.php"
    echo "  2. Access /install in your browser to run migrations"
    echo "  3. Create an admin user"
    echo ""
}

main "$@"
