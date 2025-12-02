<?php

class Model_UserThemePreference extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'user_id',
		'theme_id',
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

	protected static $_table_name = 'user_theme_preference';

	protected static $_belongs_to = array(
		'user' => array(
			'key_from' => 'user_id',
			'model_to' => 'Model_User',
			'key_to' => 'id',
		),
		'theme' => array(
			'key_from' => 'theme_id',
			'model_to' => 'Model_TenantTheme',
			'key_to' => 'id',
		)
	);

	/**
	 * Guardar o actualizar la preferencia de tema de un usuario
	 */
	public static function set_user_theme($user_id, $theme_id)
	{
		$preference = static::query()
			->where('user_id', $user_id)
			->get_one();

		if ($preference) {
			$preference->theme_id = $theme_id;
			$preference->save();
		} else {
			$preference = static::forge(array(
				'user_id' => $user_id,
				'theme_id' => $theme_id,
			));
			$preference->save();
		}

		return $preference;
	}
}
