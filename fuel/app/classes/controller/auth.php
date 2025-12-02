<?php
/**
 * Controlador de Autenticación
 *
 * Maneja el login, logout, registro y recuperación de contraseña.
 *
 * @package    App
 * @subpackage Controller
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

class Controller_Auth extends Controller
{
	/**
	 * @var string Template name
	 */
	public $template = 'template';

	/**
	 * Before action
	 */
	public function before()
	{
		parent::before();

		// Load Auth package
		\Package::load('auth');
	}

	/**
	 * Login action
	 */
	public function action_login()
	{
		// If already logged in, redirect to dashboard
		if (\Auth::check())
		{
			$redirect = \Session::get_flash('redirect_after_login', 'admin');
			\Response::redirect($redirect);
		}

		$data = array(
			'error' => null,
		);

		// Process login form
		if (\Input::method() === 'POST')
		{
			$username = \Input::post('username', '');
			$password = \Input::post('password', '');
			$remember = \Input::post('remember', false);

			// Validate input
			$val = \Validation::forge();
			$val->add('username', 'Usuario')->add_rule('required');
			$val->add('password', 'Contraseña')->add_rule('required');

			if ($val->run())
			{
				// Attempt login
				if (\Auth::login($username, $password))
				{
					// Set remember me cookie if requested
					if ($remember)
					{
						\Auth::remember_me();
					}

					// Log activity
					$this->log_activity('login', 'auth', null, null, 'Usuario inició sesión');

					// Redirect to intended page or dashboard
					$redirect = \Session::get_flash('redirect_after_login', $this->get_default_redirect());
					\Response::redirect($redirect);
				}
				else
				{
					$data['error'] = 'Usuario o contraseña incorrectos.';
				}
			}
			else
			{
				$data['error'] = implode('<br>', $val->error_message());
			}
		}

		$this->template = \View::forge('template');
		$this->template->title = 'Iniciar Sesión';
		$this->template->content = \View::forge('auth/login', $data);

		return $this->template;
	}

	/**
	 * Logout action
	 */
	public function action_logout()
	{
		if (\Auth::check())
		{
			// Log activity before logout
			$this->log_activity('logout', 'auth', null, null, 'Usuario cerró sesión');

			// Perform logout
			\Auth::logout();

			// Clear remember me cookie
			\Auth::dont_remember_me();
		}

		\Session::set_flash('success', 'Ha cerrado sesión correctamente.');
		\Response::redirect('auth/login');
	}

	/**
	 * Register action (if enabled)
	 */
	public function action_register()
	{
		// If already logged in, redirect
		if (\Auth::check())
		{
			\Response::redirect('admin');
		}

		$data = array(
			'error'   => null,
			'success' => null,
		);

		if (\Input::method() === 'POST')
		{
			$username   = \Input::post('username', '');
			$email      = \Input::post('email', '');
			$password   = \Input::post('password', '');
			$password2  = \Input::post('password_confirm', '');
			$first_name = \Input::post('first_name', '');
			$last_name  = \Input::post('last_name', '');

			// Validate input
			$val = \Validation::forge();
			$val->add('username', 'Usuario')
				->add_rule('required')
				->add_rule('min_length', 3)
				->add_rule('max_length', 50);
			$val->add('email', 'Email')
				->add_rule('required')
				->add_rule('valid_email');
			$val->add('password', 'Contraseña')
				->add_rule('required')
				->add_rule('min_length', 6);
			$val->add('password_confirm', 'Confirmar Contraseña')
				->add_rule('required')
				->add_rule('match_field', 'password');
			$val->add('first_name', 'Nombre')
				->add_rule('required');
			$val->add('last_name', 'Apellido')
				->add_rule('required');

			if ($val->run())
			{
				try
				{
					// Create user with default group (client = 10)
					$user_id = \Auth::create_user(
						$username,
						$password,
						$email,
						10, // group_id for clients
						array(
							'first_name' => $first_name,
							'last_name'  => $last_name,
						)
					);

					if ($user_id)
					{
						$data['success'] = 'Cuenta creada exitosamente. Ya puede iniciar sesión.';

						// Log activity
						$this->log_activity('register', 'auth', 'user', $user_id, 'Nuevo usuario registrado');
					}
					else
					{
						$data['error'] = 'Error al crear la cuenta. Intente de nuevo.';
					}
				}
				catch (\SimpleUserUpdateException $e)
				{
					$data['error'] = 'El usuario o email ya existe.';
				}
				catch (\Exception $e)
				{
					$data['error'] = 'Error al crear la cuenta: ' . $e->getMessage();
				}
			}
			else
			{
				$data['error'] = implode('<br>', $val->error_message());
			}
		}

		$this->template = \View::forge('template');
		$this->template->title = 'Registrarse';
		$this->template->content = \View::forge('auth/register', $data);

		return $this->template;
	}

	/**
	 * Forgot password action
	 */
	public function action_forgot()
	{
		$data = array(
			'error'   => null,
			'success' => null,
		);

		if (\Input::method() === 'POST')
		{
			$email = \Input::post('email', '');

			$val = \Validation::forge();
			$val->add('email', 'Email')
				->add_rule('required')
				->add_rule('valid_email');

			if ($val->run())
			{
				// Generate reset token
				$token = \Str::random('sha256');
				$expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

				// Save token to database
				try
				{
					\DB::insert('password_resets')->set(array(
						'email'      => $email,
						'token'      => $token,
						'expires_at' => $expires,
					))->execute();

					// TODO: Send email with reset link
					// For now, just show success message
					$data['success'] = 'Si el email existe, recibirá un enlace para restablecer su contraseña.';

					// Log activity
					$this->log_activity('password_reset_request', 'auth', null, null, 'Solicitud de reset de contraseña para: ' . $email);
				}
				catch (\Exception $e)
				{
					$data['error'] = 'Error al procesar la solicitud. Intente de nuevo.';
				}
			}
			else
			{
				$data['error'] = implode('<br>', $val->error_message());
			}
		}

		$this->template = \View::forge('template');
		$this->template->title = 'Recuperar Contraseña';
		$this->template->content = \View::forge('auth/forgot', $data);

		return $this->template;
	}

	/**
	 * Reset password action
	 */
	public function action_reset($token = null)
	{
		if (empty($token))
		{
			\Response::redirect('auth/forgot');
		}

		$data = array(
			'error'   => null,
			'success' => null,
			'token'   => $token,
		);

		// Verify token
		$reset = \DB::select()
			->from('password_resets')
			->where('token', $token)
			->where('expires_at', '>', date('Y-m-d H:i:s'))
			->where('used_at', null)
			->execute()
			->as_array();

		if (empty($reset))
		{
			$data['error'] = 'El enlace de recuperación es inválido o ha expirado.';
		}
		elseif (\Input::method() === 'POST')
		{
			$password  = \Input::post('password', '');
			$password2 = \Input::post('password_confirm', '');

			$val = \Validation::forge();
			$val->add('password', 'Contraseña')
				->add_rule('required')
				->add_rule('min_length', 6);
			$val->add('password_confirm', 'Confirmar Contraseña')
				->add_rule('required')
				->add_rule('match_field', 'password');

			if ($val->run())
			{
				try
				{
					$email = $reset[0]['email'];

					// Update password using Auth
					\Auth::reset_password(array('email' => $email), $password);

					// Mark token as used
					\DB::update('password_resets')
						->value('used_at', date('Y-m-d H:i:s'))
						->where('token', $token)
						->execute();

					$data['success'] = 'Contraseña actualizada correctamente. Ya puede iniciar sesión.';

					// Log activity
					$this->log_activity('password_reset', 'auth', null, null, 'Contraseña restablecida para: ' . $email);
				}
				catch (\Exception $e)
				{
					$data['error'] = 'Error al actualizar la contraseña: ' . $e->getMessage();
				}
			}
			else
			{
				$data['error'] = implode('<br>', $val->error_message());
			}
		}

		$this->template = \View::forge('template');
		$this->template->title = 'Restablecer Contraseña';
		$this->template->content = \View::forge('auth/reset', $data);

		return $this->template;
	}

	/**
	 * Get default redirect URL based on user group
	 */
	protected function get_default_redirect()
	{
		$groups = \Auth::get_groups();

		if (empty($groups))
		{
			return 'admin';
		}

		// Get group ID
		$group_id = isset($groups[0][1]) ? $groups[0][1] : 0;

		switch ($group_id)
		{
			case 100: // Super Admin
			case 50:  // Admin
			case 40:  // Manager
				return 'admin';

			case 30: // Seller
				return 'sellers';

			case 25: // Provider
				return 'providers';

			case 20: // Partner
				return 'partners';

			case 10: // Client
				return 'clients';

			default:
				return 'store';
		}
	}

	/**
	 * Log activity
	 */
	protected function log_activity($action, $module, $entity_type = null, $entity_id = null, $description = null)
	{
		try
		{
			$user_id = null;
			if (\Auth::check())
			{
				$user = \Auth::get_user_id();
				$user_id = isset($user[1]) ? $user[1] : null;
			}

			\DB::insert('activity_log')->set(array(
				'user_id'     => $user_id,
				'action'      => $action,
				'module'      => $module,
				'entity_type' => $entity_type,
				'entity_id'   => $entity_id,
				'description' => $description,
				'ip_address'  => \Input::real_ip(),
				'user_agent'  => \Input::user_agent(),
			))->execute();
		}
		catch (\Exception $e)
		{
			\Log::error('Error logging activity: ' . $e->getMessage());
		}
	}
}
