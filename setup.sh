#!/bin/bash
# =============================================================================
# SETUP SCRIPT - ERP Multi-tenant
# =============================================================================
# 
# Este script configura automáticamente el sistema ERP después de clonarlo
# o desplegarlo en un nuevo servidor/dominio.
#
# USO:
#   chmod +x setup.sh
#   ./setup.sh
#
# =============================================================================

set -e

echo "============================================="
echo "   ERP Multi-tenant - Script de Instalación"
echo "============================================="
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para mostrar mensajes
print_status() {
    echo -e "${GREEN}[OK]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[ADVERTENCIA]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Verificar PHP
echo "Verificando requisitos del sistema..."
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
    print_status "PHP instalado: versión $PHP_VERSION"
else
    print_error "PHP no está instalado. Por favor instálelo primero."
    exit 1
fi

# Verificar extensiones PHP requeridas
echo ""
echo "Verificando extensiones PHP..."
extensions=("pdo" "pdo_mysql" "json" "mbstring" "openssl" "curl" "gd")
for ext in "${extensions[@]}"; do
    if php -m | grep -qi "^$ext$"; then
        print_status "Extensión $ext: instalada"
    else
        print_warning "Extensión $ext: NO instalada (puede ser necesaria)"
    fi
done

# Verificar Composer
echo ""
echo "Verificando Composer..."
if command -v composer &> /dev/null; then
    COMPOSER_CMD="composer"
    print_status "Composer instalado globalmente"
elif [ -f "composer.phar" ]; then
    COMPOSER_CMD="php composer.phar"
    print_status "Composer encontrado localmente (composer.phar)"
else
    print_warning "Composer no encontrado. Intentando descargar..."
    curl -sS https://getcomposer.org/installer | php
    COMPOSER_CMD="php composer.phar"
    print_status "Composer descargado"
fi

# Instalar dependencias de PHP (FuelPHP)
echo ""
echo "Instalando dependencias PHP (FuelPHP)..."
$COMPOSER_CMD install --no-interaction --prefer-dist

if [ $? -eq 0 ]; then
    print_status "Dependencias PHP instaladas correctamente"
else
    print_error "Error al instalar dependencias PHP"
    exit 1
fi

# Verificar Node.js y npm (opcional para assets)
echo ""
echo "Verificando Node.js y npm..."
if command -v npm &> /dev/null; then
    NPM_VERSION=$(npm -v)
    print_status "npm instalado: versión $NPM_VERSION"
    
    echo ""
    echo "Instalando dependencias JavaScript..."
    npm install --silent
    print_status "Dependencias JavaScript instaladas"
else
    print_warning "npm no instalado. Las dependencias JavaScript no se instalarán automáticamente."
fi

# Crear directorios necesarios
echo ""
echo "Creando directorios necesarios..."
mkdir -p fuel/app/logs
mkdir -p fuel/app/cache
mkdir -p fuel/app/tmp
chmod -R 777 fuel/app/logs fuel/app/cache fuel/app/tmp 2>/dev/null || print_warning "No se pudieron establecer permisos (ejecute como root si es necesario)"
print_status "Directorios creados"

# Verificar que fuel/core existe
echo ""
if [ -d "fuel/core" ]; then
    print_status "FuelPHP Core instalado correctamente"
else
    print_error "FuelPHP Core no se instaló correctamente"
    exit 1
fi

# Mostrar siguiente paso
echo ""
echo "============================================="
echo "   Instalación Completada"
echo "============================================="
echo ""
echo "Próximos pasos:"
echo "  1. Configure su servidor web (Apache/Nginx) para apuntar a la carpeta 'public'"
echo "  2. Acceda a su dominio/localhost y vaya a /install"
echo "  3. Configure la base de datos y ejecute las migraciones"
echo "  4. Cree el usuario administrador"
echo ""
echo "Para más información, consulte el archivo README.md"
echo ""
