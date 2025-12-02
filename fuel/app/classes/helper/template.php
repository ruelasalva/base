<?php

/**
 * HELPER TEMPLATE
 * 
 * Helper para gestión de templates/temas del admin
 * - Obtener template actual del usuario
 * - Cambiar template
 * - Obtener ruta del template
 * 
 * @package  app
 * @author   Base Multi-Tenant System
 */
class Helper_Template
{
	/**
	 * TEMPLATES DISPONIBLES
	 */
	const TEMPLATES = [
		'coreui' => [
			'name' => 'CoreUI',
			'description' => 'Moderno, limpio y minimalista',
			'file' => 'admin/template_coreui',
			'preview' => '/assets/img/templates/coreui.png'
		],
		'adminlte' => [
			'name' => 'AdminLTE',
			'description' => 'Clásico y rico en funcionalidades',
			'file' => 'admin/template_adminlte',
			'preview' => '/assets/img/templates/adminlte.png'
		],
		'argon' => [
			'name' => 'Argon Dashboard',
			'description' => 'Hermoso y moderno con degradados',
			'file' => 'admin/template_argon',
			'preview' => '/assets/img/templates/argon.png'
		]
	];

	/**
	 * TEMPLATE POR DEFECTO
	 */
	const DEFAULT_TEMPLATE = 'coreui';

	/**
	 * OBTENER TEMPLATE ACTUAL DEL USUARIO
	 * 
	 * @param int $user_id
	 * @param int $tenant_id
	 * @return string Nombre del template (coreui, adminlte, argon)
	 */
	public static function get_current_template($user_id = null, $tenant_id = null)
	{
		if ($user_id === null && Auth::check())
		{
			$user_id = Auth::get('id');
		}

		if ($tenant_id === null)
		{
			$tenant_id = Session::get('tenant_id', 1);
		}

		if (!$user_id)
		{
			return self::DEFAULT_TEMPLATE;
		}

		// Buscar preferencia del usuario
		$pref = DB::select('template_theme')
			->from('user_preferences')
			->where('user_id', $user_id)
			->where('tenant_id', $tenant_id)
			->execute()
			->current();

		return $pref ? $pref['template_theme'] : self::DEFAULT_TEMPLATE;
	}

	/**
	 * OBTENER RUTA DEL ARCHIVO DE TEMPLATE
	 * 
	 * @param string $template_name Nombre del template
	 * @return string Ruta del view
	 */
	public static function get_template_file($template_name = null)
	{
		if ($template_name === null)
		{
			$template_name = self::get_current_template();
		}

		if (isset(self::TEMPLATES[$template_name]))
		{
			return self::TEMPLATES[$template_name]['file'];
		}

		return self::TEMPLATES[self::DEFAULT_TEMPLATE]['file'];
	}

	/**
	 * CAMBIAR TEMPLATE DEL USUARIO
	 * 
	 * @param int $user_id
	 * @param int $tenant_id
	 * @param string $template_name
	 * @return bool
	 */
	public static function set_template($user_id, $tenant_id, $template_name)
	{
		// Validar que el template existe
		if (!isset(self::TEMPLATES[$template_name]))
		{
			return false;
		}

		try
		{
			// Verificar si ya existe preferencia
			$existing = DB::select('id')
				->from('user_preferences')
				->where('user_id', $user_id)
				->where('tenant_id', $tenant_id)
				->execute()
				->current();

			if ($existing)
			{
				// Actualizar
				DB::update('user_preferences')
					->set(['template_theme' => $template_name])
					->where('id', $existing['id'])
					->execute();
			}
			else
			{
				// Insertar
				DB::insert('user_preferences')
					->set([
						'user_id' => $user_id,
						'tenant_id' => $tenant_id,
						'template_theme' => $template_name
					])
					->execute();
			}

			return true;
		}
		catch (Exception $e)
		{
			\Log::error('Error cambiando template: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * OBTENER TODOS LOS TEMPLATES DISPONIBLES
	 * 
	 * @return array
	 */
	public static function get_available_templates()
	{
		return self::TEMPLATES;
	}

	/**
	 * OBTENER INFORMACIÓN DE UN TEMPLATE
	 * 
	 * @param string $template_name
	 * @return array|null
	 */
	public static function get_template_info($template_name)
	{
		return isset(self::TEMPLATES[$template_name]) ? self::TEMPLATES[$template_name] : null;
	}
}
