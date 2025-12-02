<?php

class Model_TenantTheme extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'name',
		'slug',
		'description',
		'template_file',
		'css_files',
		'js_files',
		'preview_image',
		'is_active',
		'is_default',
		'sort_order',
		'created_at',
		'updated_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'tenant_theme';

	protected static $_has_many = array(
		'user_preferences' => array(
			'key_from' => 'id',
			'model_to' => 'Model_UserThemePreference',
			'key_to' => 'theme_id',
		)
	);

	/**
	 * Obtener el tema activo del usuario o el tema por defecto
	 */
	public static function get_user_theme($user_id = null)
	{
		if ($user_id) {
			$preference = Model_UserThemePreference::query()
				->where('user_id', $user_id)
				->get_one();
			
			if ($preference && $preference->theme) {
				return $preference->theme;
			}
		}

		// Retornar el tema por defecto
		return static::query()
			->where('is_default', 1)
			->where('is_active', 1)
			->get_one();
	}

	/**
	 * Obtener todos los temas activos
	 */
	public static function get_active_themes()
	{
		return static::query()
			->where('is_active', 1)
			->order_by('sort_order', 'asc')
			->get();
	}

	/**
	 * Decodificar CSS files de JSON
	 */
	public function get_css_array()
	{
		return json_decode($this->css_files, true) ?: array();
	}

	/**
	 * Decodificar JS files de JSON
	 */
	public function get_js_array()
	{
		return json_decode($this->js_files, true) ?: array();
	}
}
