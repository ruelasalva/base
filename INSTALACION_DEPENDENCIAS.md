# Comandos de Instalación para el Módulo de Nómina

## Ejecutar estos comandos en orden desde la raíz del proyecto

```bash
# 1. Asegurarse de estar en el directorio correcto
cd C:\xampp\htdocs\base

# 2. Instalar PHPSpreadsheet (Para Excel)
composer require phpoffice/phpspreadsheet

# 3. Instalar TCPDF (Para PDFs)
composer require tecnickcom/tcpdf

# 4. Instalar Twilio SDK (Para SMS y WhatsApp) - OPCIONAL
composer require twilio/sdk

# 5. Instalar Nexmo/Vonage SDK (Para SMS alternativo) - OPCIONAL
composer require vonage/client

# 6. Regenerar autoload
composer dump-autoload -o

# 7. Verificar instalaciones
composer show phpoffice/phpspreadsheet
composer show tecnickcom/tcpdf
```

## Notas Importantes

### PHPSpreadsheet
- Versión mínima: ^1.28
- Requiere: PHP 7.4+, ext-zip, ext-xml
- Tamaño: ~10MB
- Uso: Exportación de nómina a Excel con formato

### TCPDF
- Versión mínima: ^6.6
- Requiere: PHP 7.4+, ext-gd
- Tamaño: ~5MB
- Uso: Generación de recibos de nómina en PDF

### Twilio SDK (Opcional)
- Solo si se usará SMS, WhatsApp o llamadas
- Requiere cuenta en Twilio.com
- Costo: Variable según uso
- Uso: Notificaciones por SMS y WhatsApp

### Vonage/Nexmo (Alternativa a Twilio)
- Solo si se prefiere Nexmo sobre Twilio
- Requiere cuenta en Vonage.com
- Costo: Variable según uso
- Uso: Notificaciones por SMS

## Si ocurre algún error

### Error: composer no reconocido
```bash
# Agregar composer al PATH o usar ruta completa
C:\ProgramData\ComposerSetup\bin\composer.exe require phpoffice/phpspreadsheet
```

### Error: memoria insuficiente
```bash
# Aumentar memoria para composer
php -d memory_limit=-1 C:\ProgramData\ComposerSetup\bin\composer.phar require phpoffice/phpspreadsheet
```

### Error: extension no habilitada
```bash
# Editar php.ini y habilitar:
extension=zip
extension=xml
extension=gd
extension=soap

# Reiniciar Apache
```

## Verificación de Instalación

### Crear archivo de prueba: test_dependencies.php

```php
<?php
// Probar PHPSpreadsheet
try {
    require 'vendor/autoload.php';
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    echo "✓ PHPSpreadsheet: OK\n";
} catch (Exception $e) {
    echo "✗ PHPSpreadsheet: ERROR - " . $e->getMessage() . "\n";
}

// Probar TCPDF
try {
    $pdf = new \TCPDF();
    echo "✓ TCPDF: OK\n";
} catch (Exception $e) {
    echo "✗ TCPDF: ERROR - " . $e->getMessage() . "\n";
}

// Probar Twilio (si está instalado)
try {
    if (class_exists('\Twilio\Rest\Client')) {
        echo "✓ Twilio SDK: OK\n";
    } else {
        echo "⚠ Twilio SDK: No instalado (opcional)\n";
    }
} catch (Exception $e) {
    echo "⚠ Twilio SDK: No instalado (opcional)\n";
}

echo "\nTodas las dependencias necesarias están listas!\n";
```

### Ejecutar prueba:
```bash
php test_dependencies.php
```

## Actualización de composer.json

Tu archivo `composer.json` debe incluir:

```json
{
    "require": {
        "php": ">=7.4",
        "phpoffice/phpspreadsheet": "^1.28",
        "tecnickcom/tcpdf": "^6.6"
    },
    "require-dev": {
        "phpunit/phpunit": "^9.0"
    }
}
```

### Para SMS y WhatsApp (opcional):
```json
{
    "require": {
        "php": ">=7.4",
        "phpoffice/phpspreadsheet": "^1.28",
        "tecnickcom/tcpdf": "^6.6",
        "twilio/sdk": "^7.0",
        "vonage/client": "^3.0"
    }
}
```

## Comandos útiles de Composer

```bash
# Ver todas las dependencias instaladas
composer show

# Ver información de una dependencia específica
composer show phpoffice/phpspreadsheet

# Actualizar una dependencia específica
composer update phpoffice/phpspreadsheet

# Actualizar todas las dependencias
composer update

# Limpiar caché de composer
composer clear-cache

# Validar composer.json
composer validate

# Optimizar autoload para producción
composer dump-autoload -o --no-dev
```

## Instalación Manual (Si composer falla)

### PHPSpreadsheet
1. Descargar desde: https://github.com/PHPOffice/PhpSpreadsheet/releases
2. Extraer en `vendor/phpoffice/phpspreadsheet/`
3. Agregar require en código: `require 'vendor/phpoffice/phpspreadsheet/src/Bootstrap.php';`

### TCPDF
1. Descargar desde: https://github.com/tecnickcom/TCPDF/releases
2. Extraer en `vendor/tecnickcom/tcpdf/`
3. Agregar require en código: `require 'vendor/tecnickcom/tcpdf/tcpdf.php';`

## Tamaño Estimado de Instalación

- PHPSpreadsheet: ~10 MB
- TCPDF: ~5 MB
- Twilio SDK: ~2 MB (opcional)
- Vonage Client: ~1.5 MB (opcional)
- Total mínimo: ~15 MB
- Total con opcionales: ~18.5 MB

## Siguientes Pasos

Después de instalar las dependencias:

1. ✅ Configurar email: `fuel/app/config/email.php`
2. ✅ Configurar notificaciones: `fuel/app/config/notifications.php`
3. ✅ Configurar CFDI: `fuel/app/config/cfdi.php`
4. ✅ Probar exportación Excel: `/admin/nomina/export-excel/{period_id}`
5. ✅ Probar generación PDF: `/admin/nomina/generate-pdf/{receipt_id}`
6. ✅ Probar notificaciones: `/admin/nomina/notify/{receipt_id}`
7. ✅ Probar timbrado CFDI: `/admin/nomina/stamp-cfdi/{receipt_id}`

¡Listo para usar el sistema completo de nómina!
