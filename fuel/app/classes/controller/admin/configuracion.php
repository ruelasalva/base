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
	 * INDEX - CONFIGURACIÓN GENERAL
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

		// Obtener configuración actual
		$config = DB::select()->from('tenant_site_config')
			->where('tenant_id', $tenant_id)
			->execute()
			->current();

		// Si no existe, crear una entrada por defecto
		if (!$config)
		{
			DB::insert('tenant_site_config')
				->set([
					'tenant_id' => $tenant_id,
					'site_name' => 'Mi Sitio',
					'seo_enabled' => 0,
					'ga_enabled' => 0,
					'gtm_enabled' => 0,
					'fb_pixel_enabled' => 0,
					'recaptcha_enabled' => 0,
					'smtp_enabled' => 0
				])
				->execute();

			$config = DB::select()->from('tenant_site_config')
				->where('tenant_id', $tenant_id)
				->execute()
				->current();
		}

		$data = [
			'title' => 'Configuración del Sitio',
			'username' => Auth::get('username'),
			'email' => Auth::get('email'),
			'tenant_id' => $tenant_id,
			'is_super_admin' => Helper_Permission::is_super_admin(),
			'is_admin' => Helper_Permission::is_admin(),
			'config' => $config,
			'can_edit' => Helper_Permission::can('config', 'edit')
		];

		$data['content'] = View::forge('admin/configuracion/index', $data);
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}

	/**
	 * SAVE - GUARDAR CONFIGURACIÓN
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
