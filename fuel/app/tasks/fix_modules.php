<?php
/**
 * Tarea para corregir encoding de módulos
 */
namespace Fuel\Tasks;

class Fix_modules
{
	public static function run()
	{
		\Cli::write('=== CORRECCIÓN DE ENCODING EN SYSTEM_MODULES ===', 'yellow');
		\Cli::write('');

		$correcciones = [
			4 => 'Auditoría',
			6 => 'Configuración',
			14 => 'Órdenes de Compra',
			18 => 'Almacén'
		];

		foreach ($correcciones as $id => $nombre_correcto) {
			try {
				// Obtener nombre actual
				$actual = \DB::select('display_name')
					->from('system_modules')
					->where('id', $id)
					->execute()
					->get('display_name');
				
				\Cli::write("ID $id:", 'blue');
				\Cli::write("  Antes: $actual");
				
				// Actualizar
				\DB::update('system_modules')
					->set(['display_name' => $nombre_correcto])
					->where('id', $id)
					->execute();
				
				// Verificar
				$nuevo = \DB::select('display_name')
					->from('system_modules')
					->where('id', $id)
					->execute()
					->get('display_name');
				
				\Cli::write("  Después: $nuevo");
				
				if ($nuevo === $nombre_correcto) {
					\Cli::write("  Estado: ✓ CORREGIDO", 'green');
				} else {
					\Cli::write("  Estado: ✗ ERROR", 'red');
				}
				
				\Cli::write('');
				
			} catch (\Exception $e) {
				\Cli::write("  Error: " . $e->getMessage(), 'red');
				\Cli::write('');
			}
		}

		\Cli::write('=== FIN ===', 'yellow');
	}
}
