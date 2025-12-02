<?php
/**
 * DIAGNÓSTICO MULTI-TENANT
 * 
 * Script para diagnosticar problemas con el sistema multi-tenant.
 * Acceder vía: http://localhost/base/diagnostico
 */

class Controller_Diagnostico extends Controller
{
	public function action_index()
	{
		$diagnostico = array();
		
		// 1. Verificar constantes
		$diagnostico['constantes'] = array(
			'TENANT_PKGPATH definido' => defined('TENANT_PKGPATH') ? '✓' : '✗',
			'TENANT_PKGPATH valor' => defined('TENANT_PKGPATH') ? TENANT_PKGPATH : 'No definido',
			'TENANT_PKGPATH existe' => (defined('TENANT_PKGPATH') && is_dir(TENANT_PKGPATH)) ? '✓' : '✗',
			'TENANT_ACTIVE_MODULES definido' => defined('TENANT_ACTIVE_MODULES') ? '✓' : '✗',
			'TENANT_ACTIVE_MODULES valor' => defined('TENANT_ACTIVE_MODULES') ? TENANT_ACTIVE_MODULES : 'No definido',
		);
		
		// 2. Verificar módulos activos
		if (defined('TENANT_ACTIVE_MODULES'))
		{
			$active_modules = @unserialize(TENANT_ACTIVE_MODULES);
			$diagnostico['modulos_activos'] = is_array($active_modules) ? $active_modules : array('Error deserializando');
		}
		else
		{
			$diagnostico['modulos_activos'] = array('TENANT_ACTIVE_MODULES no está definido');
		}
		
		// 3. Verificar packages tenant disponibles
		if (defined('TENANT_PKGPATH') && is_dir(TENANT_PKGPATH))
		{
			$packages = glob(TENANT_PKGPATH.'*', GLOB_ONLYDIR);
			$diagnostico['packages_tenant_disponibles'] = array();
			
			foreach ($packages as $package_path)
			{
				$package_name = basename($package_path);
				$bootstrap_exists = file_exists($package_path.DIRECTORY_SEPARATOR.'bootstrap.php');
				
				$diagnostico['packages_tenant_disponibles'][$package_name] = array(
					'ruta' => $package_path,
					'bootstrap' => $bootstrap_exists ? '✓' : '✗',
				);
			}
		}
		else
		{
			$diagnostico['packages_tenant_disponibles'] = 'Directorio packages_tenant no existe';
		}
		
		// 4. Verificar routes
		$diagnostico['routes'] = \Router::$routes;
		
		// 5. Verificar tenant actual
		$diagnostico['tenant_actual'] = array(
			'HTTP_HOST' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'No definido',
			'Tenant resuelto' => class_exists('Tenant_Resolver') ? \Tenant_Resolver::get_tenant() : 'Clase no existe',
		);
		
		// 6. Verificar base de datos
		try
		{
			$db_config = \Config::get('db.default');
			$diagnostico['base_datos'] = array(
				'Conexión configurada' => $db_config ? '✓' : '✗',
				'DSN' => isset($db_config['connection']['dsn']) ? $db_config['connection']['dsn'] : 'No definido',
			);
		}
		catch (\Exception $e)
		{
			$diagnostico['base_datos'] = 'Error: ' . $e->getMessage();
		}
		
		// 7. Verificar packages cargados
		$diagnostico['packages_cargados'] = \Package::loaded();
		
		// 8. Verificar environment
		$diagnostico['environment'] = array(
			'FUEL_ENV' => \Fuel::$env,
			'PHP Version' => PHP_VERSION,
			'FuelPHP Version' => \Fuel::VERSION,
		);
		
		// Mostrar resultados
		echo '<html><head><meta charset="UTF-8"><title>Diagnóstico Multi-Tenant</title>';
		echo '<style>
			body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
			h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
			h2 { color: #007bff; margin-top: 30px; }
			pre { background: #fff; padding: 15px; border-radius: 5px; overflow-x: auto; }
			.ok { color: green; font-weight: bold; }
			.error { color: red; font-weight: bold; }
		</style></head><body>';
		
		echo '<h1>🔍 Diagnóstico Sistema Multi-Tenant ERP</h1>';
		
		foreach ($diagnostico as $seccion => $datos)
		{
			echo '<h2>' . ucfirst(str_replace('_', ' ', $seccion)) . '</h2>';
			echo '<pre>';
			print_r($datos);
			echo '</pre>';
		}
		
		echo '</body></html>';
		exit;
	}
}
