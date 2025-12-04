<?php
/**
 * Test de módulos - acceder via web: http://localhost/base/test_modules.php
 */
// Bootstrap de Fuel
define('DOCROOT', __DIR__.DIRECTORY_SEPARATOR);
define('APPPATH', realpath(__DIR__.'/fuel/app/').DIRECTORY_SEPARATOR);
define('PKGPATH', realpath(__DIR__.'/fuel/packages/').DIRECTORY_SEPARATOR);
define('COREPATH', realpath(__DIR__.'/fuel/core/').DIRECTORY_SEPARATOR);

// Cargar composer autoload primero
require APPPATH.'../vendor/autoload.php';

// Cargar bootstrap
require APPPATH.'bootstrap.php';

// Inicializar Fuel
Package::load('auth');
Package::load('orm');

header('Content-Type: text/html; charset=utf-8');
echo '<pre>';
echo "=== TEST DE MÓDULOS ===\n\n";

$tenant_id = 1;
$modules = Helper_Module::get_all_modules($tenant_id);

echo "Total módulos encontrados: " . count($modules) . "\n\n";

echo "Primeros 5 módulos:\n";
foreach (array_slice($modules, 0, 5) as $mod) {
    echo "ID: {$mod['id']} | Nombre: {$mod['name']}\n";
    echo "Campos disponibles: " . implode(', ', array_keys($mod)) . "\n";
    echo "is_core existe? " . (array_key_exists('is_core', $mod) ? 'SÍ => ' . $mod['is_core'] : 'NO') . "\n\n";
}

echo "\n=== Módulos nuevos (56-66) ===\n";
foreach ($modules as $mod) {
    if ($mod['id'] >= 56 && $mod['id'] <= 66) {
        $is_core = array_key_exists('is_core', $mod) ? $mod['is_core'] : 'NO EXISTE';
        echo "ID: {$mod['id']} | {$mod['display_name']} | is_core: {$is_core} | activo: {$mod['is_tenant_active']}\n";
    }
}

echo '</pre>';

