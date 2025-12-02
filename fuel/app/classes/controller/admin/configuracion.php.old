<?php

/**
 * Controller_Admin_Configuracion
 * 
 * Controlador para gestionar la configuración del sitio multi-tenant.
 * Incluye: General, SEO, Tracking Scripts, Cookies y Privacidad.
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Configuracion extends Controller_Admin
{
	/**
	 * BEFORE
	 */
	public function before()
	{
		parent::before();

		// Verificar autenticación
		if (!Auth::check()) {
			Session::set_flash('error', 'Debes iniciar sesión.');
			Response::redirect('admin/login');
		}
	}

	/**
	 * INDEX - Vista principal de configuración con tabs
	 */
	public function action_index()
	{
		// Verificar permiso
		if (!Helper_Permission::can('config_site', 'view')) {
			Session::set_flash('error', 'No tienes permiso para ver la configuración del sitio.');
			Response::redirect('admin');
		}

		// Obtener configuración actual
		$config = Model_SiteConfig::get_config(1); // TODO: tenant_id dinámico

		$data = array(
			'config' => $config,
			'active_tab' => Input::get('tab', 'general'),
		);

		$this->template->title   = 'Configuración del Sitio';
		$this->template->content = View::forge('admin/configuracion/index', $data);
	}

	/**
	 * ACTUALIZAR - Procesar formulario de actualización
	 */
	public function action_actualizar()
	{
		// Verificar permiso
		if (!Helper_Permission::can('config_site', 'edit')) {
			Session::set_flash('error', 'No tienes permiso para editar la configuración del sitio.');
			Response::redirect('admin/configuracion');
		}

		if (Input::method() !== 'POST') {
			Response::redirect('admin/configuracion');
		}

		$tab = Input::post('active_tab', 'general');

		try {
			// Preparar datos según el tab activo
			$data = array();

			switch ($tab) {
				case 'general':
					$data = array(
						'site_name'       => Input::post('site_name'),
						'site_tagline'    => Input::post('site_tagline'),
						'contact_email'   => Input::post('contact_email'),
						'contact_phone'   => Input::post('contact_phone'),
						'address'         => Input::post('address'),
						'logo_url'        => Input::post('logo_url'),
						'logo_alt_url'    => Input::post('logo_alt_url'),
					);
					break;

				case 'seo':
					$data = array(
						'meta_description' => Input::post('meta_description'),
						'meta_keywords'    => Input::post('meta_keywords'),
						'meta_author'      => Input::post('meta_author'),
						'og_image'         => Input::post('og_image'),
						'theme_color'      => Input::post('theme_color'),
					);
					break;

				case 'tracking':
					$data = array(
						// Google Analytics
						'ga_enabled'      => Input::post('ga_enabled') ? 1 : 0,
						'ga_tracking_id'  => Input::post('ga_tracking_id'),
						'ga_script'       => Input::post('ga_script'),
						// Google Tag Manager
						'gtm_enabled'     => Input::post('gtm_enabled') ? 1 : 0,
						'gtm_container_id'=> Input::post('gtm_container_id'),
						// Facebook Pixel
						'fb_pixel_enabled'=> Input::post('fb_pixel_enabled') ? 1 : 0,
						'fb_pixel_id'     => Input::post('fb_pixel_id'),
						// reCAPTCHA
						'recaptcha_enabled'     => Input::post('recaptcha_enabled') ? 1 : 0,
						'recaptcha_site_key'    => Input::post('recaptcha_site_key'),
						'recaptcha_secret_key'  => Input::post('recaptcha_secret_key'),
						'recaptcha_version'     => Input::post('recaptcha_version', 'v2'),
					);
					break;

				case 'cookies':
					$data = array(
						'cookie_consent_enabled' => Input::post('cookie_consent_enabled') ? 1 : 0,
						'cookie_message'         => Input::post('cookie_message'),
						'privacy_policy_url'     => Input::post('privacy_policy_url'),
						'terms_conditions_url'   => Input::post('terms_conditions_url'),
					);
					break;

				case 'favicons':
					$data = array(
						'favicon_16'  => Input::post('favicon_16'),
						'favicon_32'  => Input::post('favicon_32'),
						'favicon_57'  => Input::post('favicon_57'),
						'favicon_72'  => Input::post('favicon_72'),
						'favicon_114' => Input::post('favicon_114'),
						'favicon_144' => Input::post('favicon_144'),
					);
					break;

				case 'scripts':
					$data = array(
						'custom_head_scripts' => Input::post('custom_head_scripts'),
						'custom_body_scripts' => Input::post('custom_body_scripts'),
					);
					break;
			}

			// Actualizar configuración
			if (Model_SiteConfig::update_config(1, $data)) { // TODO: tenant_id dinámico
				Session::set_flash('success', 'Configuración actualizada correctamente.');
			} else {
				Session::set_flash('error', 'Error al actualizar la configuración.');
			}

		} catch (Exception $e) {
			Log::error('Error al actualizar configuración: ' . $e->getMessage());
			Session::set_flash('error', 'Error al guardar la configuración: ' . $e->getMessage());
		}

		Response::redirect('admin/configuracion?tab=' . $tab);
	}

	/**
	 * UPLOAD - Subir archivo (logo, favicon, etc.)
	 */
	public function action_upload()
	{
		// Verificar permiso
		if (!Helper_Permission::can('config_site', 'edit')) {
			return $this->response(array(
				'success' => false,
				'message' => 'No tienes permiso para subir archivos.'
			));
		}

		if (Input::method() !== 'POST') {
			return $this->response(array(
				'success' => false,
				'message' => 'Método no permitido.'
			));
		}

		try {
			// Configurar Upload
			Upload::process(array(
				'path'          => DOCROOT . 'assets/img/config/',
				'create_path'   => true,
				'ext_whitelist' => array('img', 'jpg', 'jpeg', 'gif', 'png', 'ico', 'svg'),
				'max_size'      => 5 * 1024 * 1024, // 5MB
				'normalize'     => true,
				'auto_rename'   => false,
				'overwrite'     => true,
			));

			if (Upload::is_valid()) {
				Upload::save();
				$file = Upload::get_files(0);

				$url = Uri::base(false) . 'assets/img/config/' . $file['saved_as'];

				return $this->response(array(
					'success'  => true,
					'message'  => 'Archivo subido correctamente.',
					'url'      => $url,
					'filename' => $file['saved_as'],
				));
			} else {
				$errors = Upload::get_errors();
				return $this->response(array(
					'success' => false,
					'message' => 'Error al subir archivo: ' . implode(', ', $errors[0]['errors'])
				));
			}

		} catch (Exception $e) {
			Log::error('Error al subir archivo: ' . $e->getMessage());
			return $this->response(array(
				'success' => false,
				'message' => 'Error al subir archivo: ' . $e->getMessage()
			));
		}
	}

	/**
	 * TEST_RECAPTCHA - Probar validación de reCAPTCHA
	 */
	public function action_test_recaptcha()
	{
		if (Input::method() !== 'POST') {
			Response::redirect('admin/configuracion?tab=tracking');
		}

		$response = Input::post('g-recaptcha-response') ?: Input::post('recaptcha_token');

		if (empty($response)) {
			Session::set_flash('error', 'No se recibió respuesta de reCAPTCHA.');
			Response::redirect('admin/configuracion?tab=tracking');
		}

		$config = Model_SiteConfig::get_config(1);

		if ($config->verify_recaptcha($response)) {
			Session::set_flash('success', '✅ reCAPTCHA verificado correctamente.');
		} else {
			Session::set_flash('error', '❌ Error al verificar reCAPTCHA. Verifica las claves.');
		}

		Response::redirect('admin/configuracion?tab=tracking');
	}

	/**
	 * Respuesta JSON
	 */
	private function response($data)
	{
		return Response::forge(json_encode($data), 200, array(
			'Content-Type' => 'application/json'
		));
	}
}
