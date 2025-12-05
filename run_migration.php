<?php
/**
 * Script para ejecutar migración de productos de inventario
 */

// Configurar error reporting
error_reporting(-1);
ini_set('display_errors', 1);

// Definir constantes
define('DOCROOT', __DIR__.DIRECTORY_SEPARATOR);
define('APPPATH', realpath(__DIR__.'/fuel/app/').DIRECTORY_SEPARATOR);
define('PKGPATH', realpath(__DIR__.'/fuel/packages/').DIRECTORY_SEPARATOR);
define('COREPATH', realpath(__DIR__.'/fuel/core/').DIRECTORY_SEPARATOR);

// Get the start time and memory for use later
defined('FUEL_START_TIME') or define('FUEL_START_TIME', microtime(true));
defined('FUEL_START_MEM') or define('FUEL_START_MEM', memory_get_usage());

// Load in the Fuel autoloader
require COREPATH.'classes'.DIRECTORY_SEPARATOR.'autoloader.php';
class_alias('Fuel\\Core\\Autoloader', 'Autoloader');

// Boot the app
require APPPATH.'bootstrap.php';

// Ejecutar migración
try
{
	echo "Ejecutando migración de productos de inventario...\n";
	
	\Migrate::latest();
	
	echo "✓ Migración completada exitosamente!\n";
	echo "\nTablas creadas:\n";
	echo "- inventory_product_categories\n";
	echo "- inventory_products\n";
	echo "- inventory_product_logs\n";
}
catch (\Exception $e)
{
	echo "✗ Error en migración: " . $e->getMessage() . "\n";
	exit(1);
}
