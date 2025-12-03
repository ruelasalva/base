<?php
/**
 * Script para limpiar todos los caches
 */

// Limpiar OPCache si está disponible
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✓ OPCache limpiado\n";
} else {
    echo "✗ OPCache no disponible\n";
}

// Limpiar cache de FuelPHP
$cache_dir = '../fuel/app/cache';
if (is_dir($cache_dir)) {
    $files = glob($cache_dir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✓ Cache de FuelPHP limpiado (" . count($files) . " archivos)\n";
}

// Limpiar Realpath Cache
clearstatcache(true);
echo "✓ Realpath cache limpiado\n";

echo "\n=== Cache completamente limpiado ===\n";
echo "Recarga la página /admin/modules\n";
