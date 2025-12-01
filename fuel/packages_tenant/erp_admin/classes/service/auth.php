<?php
/**
 * ERP Admin Module - Auth Service
 *
 * Authentication and authorization service.
 *
 * @package    ERP_Admin
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Admin;

/**
 * Auth Service
 *
 * Handles authentication and authorization for the ERP.
 */
class Service_Auth
{
	/**
	 * @var Model_User|null Current user
	 */
	protected static $current_user = null;

	/**
	 * Attempt to login user
	 *
	 * @param string $username Username or email
	 * @param string $password Password
	 * @return bool
	 */
	public static function login($username, $password)
	{
		$user = Model_User::query()
			->where_open()
			->where('username', $username)
			->or_where('email', $username)
			->where_close()
			->where('is_active', 1)
			->get_one();

		if ( ! $user)
		{
			return false;
		}

		if ( ! password_verify($password, $user->password))
		{
			return false;
		}

		// Update last login
		$user->last_login = date('Y-m-d H:i:s');
		$user->save();

		// Set session
		\Session::set('user_id', $user->id);
		\Session::set('user_role', $user->role_id);

		static::$current_user = $user;

		return true;
	}

	/**
	 * Logout user
	 *
	 * @return void
	 */
	public static function logout()
	{
		\Session::delete('user_id');
		\Session::delete('user_role');
		static::$current_user = null;
	}

	/**
	 * Check if user is logged in
	 *
	 * @return bool
	 */
	public static function check()
	{
		return \Session::get('user_id') !== null;
	}

	/**
	 * Get current user
	 *
	 * @return Model_User|null
	 */
	public static function user()
	{
		if (static::$current_user !== null)
		{
			return static::$current_user;
		}

		$user_id = \Session::get('user_id');

		if ($user_id === null)
		{
			return null;
		}

		static::$current_user = Model_User::find($user_id);

		return static::$current_user;
	}

	/**
	 * Check if user has permission
	 *
	 * @param string $permission Permission to check
	 * @return bool
	 */
	public static function has_permission($permission)
	{
		$user = static::user();

		if ( ! $user || ! $user->role)
		{
			return false;
		}

		return $user->role->has_permission($permission);
	}

	/**
	 * Hash password
	 *
	 * @param string $password Plain password
	 * @return string Hashed password
	 */
	public static function hash_password($password)
	{
		return password_hash($password, PASSWORD_DEFAULT);
	}
}
