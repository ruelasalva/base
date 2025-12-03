<?php

/**
 * CONTROLLER ADMIN CONFIGURACION
 * 
 * Configuración completa del sitio
 * - SEO (meta tags, OG, Twitter Cards)
 * - Analytics (GA4, GTM, FB Pixel)
 * - SMTP (email)
 * - Social Media
 * - Logo y Favicon
 * - reCAPTCHA
 * 
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Configuracion extends Controller_Admin
{
	/**
	 * INDEX - DASHBOARD DE CONFIGURACIÓN
	 */
	public function action_index()
	{
		// Verificar permisos
		if (!Helper_Permission::can('config', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver configuración');
			Response::redirect('admin');
		}

		$tenant_id = Session::get('tenant_id', 1);
		$active_tab = Input::get('tab', 'general');

		// Obtener todas las configuraciones del tenant
		$settings = DB::select('*')
			->from('system_settings')
			->where('tenant_id', $tenant_id)
			->order_by('category', 'ASC')
			->order_by('setting_key', 'ASC')
			->execute()
			->as_array();

		// Organizar por categoría
		$settings_by_category = [];
		foreach ($settings as $setting)
		{
			$settings_by_category[$setting['category']][$setting['setting_key']] = $setting;
		}

		// Obtener estadísticas
		$stats = [
			'total' => count($settings),
			'categories' => count($settings_by_category),
			'last_updated' => DB::select(DB::expr('MAX(updated_at) as last'))
				->from('system_settings')
				->where('tenant_id', $tenant_id)
				->execute()
				->get('last')
		];

		$data = [
			'title' => 'Configuración del Sistema',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'settings_by_category' => $settings_by_category,
			'active_tab' => $active_tab,
			'stats' => $stats,
			'can_edit' => Helper_Permission::can('config', 'edit')
		];

		$data['content'] = View::forge('admin/configuracion/index', $data);
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}

	/**
	 * GENERAL - CONFIGURACIÓN GENERAL
	 */
	public function action_general()
	{
		if (!Helper_Permission::can('config', 'edit'))
		{
			Session::set_flash('error', 'No tienes permisos para editar configuración');
			Response::redirect('admin/configuracion');
		}

		$tenant_id = Session::get('tenant_id', 1);

		if (Input::method() == 'POST')
		{
			try
			{
				$settings = [
					['key' => 'app_name', 'value' => Input::post('app_name'), 'type' => 'string'],
					['key' => 'app_description', 'value' => Input::post('app_description'), 'type' => 'string'],
					['key' => 'app_url', 'value' => Input::post('app_url'), 'type' => 'string'],
					['key' => 'timezone', 'value' => Input::post('timezone'), 'type' => 'string'],
					['key' => 'date_format', 'value' => Input::post('date_format'), 'type' => 'string'],
					['key' => 'time_format', 'value' => Input::post('time_format'), 'type' => 'string'],
					['key' => 'language', 'value' => Input::post('language'), 'type' => 'string'],
					['key' => 'items_per_page', 'value' => Input::post('items_per_page'), 'type' => 'integer'],
					['key' => 'maintenance_mode', 'value' => Input::post('maintenance_mode', 0), 'type' => 'boolean'],
				];

				foreach ($settings as $setting)
				{
					$this->save_setting('general', $setting['key'], $setting['value'], $setting['type']);
				}

				Session::set_flash('success', 'Configuración general guardada exitosamente.');
				Response::redirect('admin/configuracion?tab=general');
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error al guardar: ' . $e->getMessage());
			}
		}

		$current_settings = $this->get_settings_by_category('general');

		$data = [
			'title' => 'Configuración General',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'settings' => $current_settings
		];

		$data['content'] = View::forge('admin/configuracion/general', $data);
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}

	/**
	 * EMAIL - CONFIGURACIÓN DE EMAIL
	 */
	public function action_email()
	{
		if (!Helper_Permission::can('config', 'edit'))
		{
			Session::set_flash('error', 'No tienes permisos para editar configuración');
			Response::redirect('admin/configuracion');
		}

		$tenant_id = Session::get('tenant_id', 1);

		if (Input::method() == 'POST')
		{
			try
			{
				$settings = [
					['key' => 'email_from_address', 'value' => Input::post('email_from_address'), 'type' => 'string'],
					['key' => 'email_from_name', 'value' => Input::post('email_from_name'), 'type' => 'string'],
					['key' => 'smtp_host', 'value' => Input::post('smtp_host'), 'type' => 'string'],
					['key' => 'smtp_port', 'value' => Input::post('smtp_port'), 'type' => 'integer'],
					['key' => 'smtp_username', 'value' => Input::post('smtp_username'), 'type' => 'string'],
					['key' => 'smtp_password', 'value' => Input::post('smtp_password'), 'type' => 'string'],
					['key' => 'smtp_encryption', 'value' => Input::post('smtp_encryption'), 'type' => 'string'],
					['key' => 'email_enabled', 'value' => Input::post('email_enabled', 0), 'type' => 'boolean'],
				];

				foreach ($settings as $setting)
				{
					$this->save_setting('email', $setting['key'], $setting['value'], $setting['type']);
				}

				Session::set_flash('success', 'Configuración de email guardada exitosamente.');
				Response::redirect('admin/configuracion?tab=email');
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error al guardar: ' . $e->getMessage());
			}
		}

		$current_settings = $this->get_settings_by_category('email');

		$data = [
			'title' => 'Configuración de Email',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'settings' => $current_settings
		];

		$data['content'] = View::forge('admin/configuracion/email', $data);
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}

	/**
	 * FACTURACIÓN - CONFIGURACIÓN DE FACTURACIÓN
	 */
	public function action_facturacion()
	{
		if (!Helper_Permission::can('config', 'edit'))
		{
			Session::set_flash('error', 'No tienes permisos para editar configuración');
			Response::redirect('admin/configuracion');
		}

		$tenant_id = Session::get('tenant_id', 1);

		if (Input::method() == 'POST')
		{
			try
			{
				$settings = [
					['key' => 'billing_days_to_upload', 'value' => Input::post('billing_days_to_upload'), 'type' => 'integer'],
					['key' => 'billing_upload_deadline', 'value' => Input::post('billing_upload_deadline'), 'type' => 'string'],
					['key' => 'billing_payment_terms', 'value' => Input::post('billing_payment_terms'), 'type' => 'integer'],
					['key' => 'billing_payment_days', 'value' => Input::post('billing_payment_days'), 'type' => 'string'],
					['key' => 'billing_holidays', 'value' => Input::post('billing_holidays'), 'type' => 'json'],
					['key' => 'billing_auto_receipt', 'value' => Input::post('billing_auto_receipt', 0), 'type' => 'boolean'],
					['key' => 'billing_require_sat_validation', 'value' => Input::post('billing_require_sat_validation', 0), 'type' => 'boolean'],
					['key' => 'billing_max_file_size', 'value' => Input::post('billing_max_file_size'), 'type' => 'integer'],
				];

				foreach ($settings as $setting)
				{
					$this->save_setting('facturacion', $setting['key'], $setting['value'], $setting['type']);
				}

				Session::set_flash('success', 'Configuración de facturación guardada exitosamente.');
				Response::redirect('admin/configuracion?tab=facturacion');
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error al guardar: ' . $e->getMessage());
			}
		}

		$current_settings = $this->get_settings_by_category('facturacion');

		$data = [
			'title' => 'Configuración de Facturación',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'settings' => $current_settings
		];

		$data['content'] = View::forge('admin/configuracion/facturacion', $data);
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}

	/**
	 * NOTIFICACIONES - CONFIGURACIÓN DE NOTIFICACIONES
	 */
	public function action_notificaciones()
	{
		if (!Helper_Permission::can('config', 'edit'))
		{
			Session::set_flash('error', 'No tienes permisos para editar configuración');
			Response::redirect('admin/configuracion');
		}

		$tenant_id = Session::get('tenant_id', 1);

		if (Input::method() == 'POST')
		{
			try
			{
				$settings = [
					['key' => 'notifications_enabled', 'value' => Input::post('notifications_enabled', 0), 'type' => 'boolean'],
					['key' => 'notifications_email', 'value' => Input::post('notifications_email', 0), 'type' => 'boolean'],
					['key' => 'notifications_sms', 'value' => Input::post('notifications_sms', 0), 'type' => 'boolean'],
					['key' => 'notifications_push', 'value' => Input::post('notifications_push', 0), 'type' => 'boolean'],
					['key' => 'notifications_frequency', 'value' => Input::post('notifications_frequency'), 'type' => 'string'],
					['key' => 'notifications_quiet_hours_start', 'value' => Input::post('notifications_quiet_hours_start'), 'type' => 'string'],
					['key' => 'notifications_quiet_hours_end', 'value' => Input::post('notifications_quiet_hours_end'), 'type' => 'string'],
				];

				foreach ($settings as $setting)
				{
					$this->save_setting('notificaciones', $setting['key'], $setting['value'], $setting['type']);
				}

				Session::set_flash('success', 'Configuración de notificaciones guardada exitosamente.');
				Response::redirect('admin/configuracion?tab=notificaciones');
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error al guardar: ' . $e->getMessage());
			}
		}

		$current_settings = $this->get_settings_by_category('notificaciones');

		$data = [
			'title' => 'Configuración de Notificaciones',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'settings' => $current_settings
		];

		$data['content'] = View::forge('admin/configuracion/notificaciones', $data);
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}

	/**
	 * SEGURIDAD - CONFIGURACIÓN DE SEGURIDAD
	 */
	public function action_seguridad()
	{
		if (!Helper_Permission::can('config', 'edit'))
		{
			Session::set_flash('error', 'No tienes permisos para editar configuración');
			Response::redirect('admin/configuracion');
		}

		$tenant_id = Session::get('tenant_id', 1);

		if (Input::method() == 'POST')
		{
			try
			{
				$settings = [
					['key' => 'session_timeout', 'value' => Input::post('session_timeout'), 'type' => 'integer'],
					['key' => 'max_login_attempts', 'value' => Input::post('max_login_attempts'), 'type' => 'integer'],
					['key' => 'lockout_duration', 'value' => Input::post('lockout_duration'), 'type' => 'integer'],
					['key' => 'password_min_length', 'value' => Input::post('password_min_length'), 'type' => 'integer'],
					['key' => 'password_require_uppercase', 'value' => Input::post('password_require_uppercase', 0), 'type' => 'boolean'],
					['key' => 'password_require_numbers', 'value' => Input::post('password_require_numbers', 0), 'type' => 'boolean'],
					['key' => 'password_require_special', 'value' => Input::post('password_require_special', 0), 'type' => 'boolean'],
					['key' => 'captcha_enabled', 'value' => Input::post('captcha_enabled', 0), 'type' => 'boolean'],
					['key' => 'captcha_site_key', 'value' => Input::post('captcha_site_key'), 'type' => 'string'],
					['key' => 'captcha_secret_key', 'value' => Input::post('captcha_secret_key'), 'type' => 'string'],
					['key' => 'two_factor_enabled', 'value' => Input::post('two_factor_enabled', 0), 'type' => 'boolean'],
				];

				foreach ($settings as $setting)
				{
					$this->save_setting('seguridad', $setting['key'], $setting['value'], $setting['type']);
				}

				Session::set_flash('success', 'Configuración de seguridad guardada exitosamente.');
				Response::redirect('admin/configuracion?tab=seguridad');
			}
			catch (Exception $e)
			{
				Session::set_flash('error', 'Error al guardar: ' . $e->getMessage());
			}
		}

		$current_settings = $this->get_settings_by_category('seguridad');

		$data = [
			'title' => 'Configuración de Seguridad',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'settings' => $current_settings
		];

		$data['content'] = View::forge('admin/configuracion/seguridad', $data);
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}

	/**
	 * SAVE SETTING - Guardar o actualizar configuración
	 */
	private function save_setting($category, $key, $value, $type = 'string')
	{
		$tenant_id = Session::get('tenant_id', 1);

		$exists = DB::select(DB::expr('COUNT(*) as count'))
			->from('system_settings')
			->where('tenant_id', $tenant_id)
			->where('category', $category)
			->where('setting_key', $key)
			->execute()
			->get('count');

		if ($exists > 0)
		{
			DB::update('system_settings')
				->set([
					'setting_value' => $value,
					'setting_type' => $type,
					'updated_at' => DB::expr('CURRENT_TIMESTAMP')
				])
				->where('tenant_id', $tenant_id)
				->where('category', $category)
				->where('setting_key', $key)
				->execute();
		}
		else
		{
			DB::insert('system_settings')
				->set([
					'tenant_id' => $tenant_id,
					'category' => $category,
					'setting_key' => $key,
					'setting_value' => $value,
					'setting_type' => $type,
					'is_public' => 0
				])
				->execute();
		}
	}

	/**
	 * GET SETTINGS BY CATEGORY - Obtener configuraciones por categoría
	 */
	private function get_settings_by_category($category)
	{
		$tenant_id = Session::get('tenant_id', 1);

		$settings = DB::select('*')
			->from('system_settings')
			->where('tenant_id', $tenant_id)
			->where('category', $category)
			->execute()
			->as_array();

		$result = [];
		foreach ($settings as $setting)
		{
			$result[$setting['setting_key']] = $setting['setting_value'];
		}

		return $result;
	}

	/**
	 * SAVE - GUARDAR CONFIGURACIÓN (MÉTODO LEGACY - MANTENER COMPATIBILIDAD)
	 */
	public function action_save()
	{
		// Verificar permisos
		if (!Helper_Permission::can('config', 'edit'))
		{
			Session::set_flash('error', 'No tienes permisos para editar configuración');
			Response::redirect('admin/configuracion');
		}

		$tenant_id = Session::get('tenant_id', 1);

		try
		{
			// Preparar datos
			$data = [
				// General
				'site_name' => Input::post('site_name'),
				'site_logo' => Input::post('site_logo'),
				'site_favicon' => Input::post('site_favicon'),

				// SEO
				'seo_enabled' => Input::post('seo_enabled', 0),
				'seo_title' => Input::post('seo_title'),
				'seo_description' => Input::post('seo_description'),
				'seo_keywords' => Input::post('seo_keywords'),
				'seo_og_image' => Input::post('seo_og_image'),

				// Analytics
				'ga_enabled' => Input::post('ga_enabled', 0),
				'ga_tracking_id' => Input::post('ga_tracking_id'),
				'gtm_enabled' => Input::post('gtm_enabled', 0),
				'gtm_container_id' => Input::post('gtm_container_id'),
				'fb_pixel_enabled' => Input::post('fb_pixel_enabled', 0),
				'fb_pixel_id' => Input::post('fb_pixel_id'),

				// reCAPTCHA
				'recaptcha_enabled' => Input::post('recaptcha_enabled', 0),
				'recaptcha_site_key' => Input::post('recaptcha_site_key'),
				'recaptcha_secret_key' => Input::post('recaptcha_secret_key'),

				// SMTP
				'smtp_enabled' => Input::post('smtp_enabled', 0),
				'smtp_host' => Input::post('smtp_host'),
				'smtp_port' => Input::post('smtp_port', 587),
				'smtp_user' => Input::post('smtp_user'),
				'smtp_password' => Input::post('smtp_password'),
				'smtp_encryption' => Input::post('smtp_encryption', 'tls'),
				'smtp_from_email' => Input::post('smtp_from_email'),
				'smtp_from_name' => Input::post('smtp_from_name'),

				// Social Media
				'social_facebook' => Input::post('social_facebook'),
				'social_twitter' => Input::post('social_twitter'),
				'social_instagram' => Input::post('social_instagram'),
				'social_linkedin' => Input::post('social_linkedin')
			];

			// Actualizar
			DB::update('tenant_site_config')
				->set($data)
				->where('tenant_id', $tenant_id)
				->execute();

			Session::set_flash('success', 'Configuración guardada correctamente');
		}
		catch (Exception $e)
		{
			\Log::error('Error guardando configuración: ' . $e->getMessage());
			Session::set_flash('error', 'Error al guardar la configuración');
		}

		Response::redirect('admin/configuracion');
	}

	/**
	 * TEMPLATES - SELECTOR DE TEMPLATES
	 */
	public function action_templates()
	{
		$tenant_id = Session::get('tenant_id', 1);
		$user_id = Auth::get('id');

		// Obtener template actual
		$current_template = Helper_Template::get_current_template();
		$available_templates = Helper_Template::get_available_templates();

		$data = [
			'title' => 'Seleccionar Template',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'current_template' => $current_template,
			'templates' => $available_templates
		];

		$data['content'] = View::forge('admin/configuracion/templates', $data);
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}

	/**
	 * SET_TEMPLATE - CAMBIAR TEMPLATE VÍA AJAX
	 */
	public function action_set_template()
	{
		if (!Input::is_ajax())
		{
			Response::redirect('admin/configuracion/templates');
		}

		$template_name = Input::post('template');
		$user_id = Auth::get('id');
		$tenant_id = Session::get('tenant_id', 1);

		$result = Helper_Template::set_template($user_id, $tenant_id, $template_name);

		if ($result)
		{
			return Response::forge(json_encode([
				'success' => true,
				'message' => 'Template cambiado correctamente'
			]), 200, ['Content-Type' => 'application/json']);
		}
		else
		{
			return Response::forge(json_encode([
				'success' => false,
				'message' => 'Error al cambiar template'
			]), 400, ['Content-Type' => 'application/json']);
		}
	}
}
