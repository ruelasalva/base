<?php

/**
 * Model_SiteConfig
 * 
 * Modelo para gestionar la configuración del sitio multi-tenant.
 * Incluye configuración general, SEO, tracking scripts, cookies, etc.
 *
 * @package  app
 * @extends  \Orm\Model
 */
class Model_SiteConfig extends \Orm\Model
{
	/**
	 * Propiedades del modelo
	 */
	protected static $_properties = array(
		'id',
		'tenant_id',
		// Información General
		'site_name',
		'site_tagline',
		'contact_email',
		'contact_phone',
		'address',
		// Logos y Favicons
		'logo_url',
		'logo_alt_url',
		'favicon_16',
		'favicon_32',
		'favicon_57',
		'favicon_72',
		'favicon_114',
		'favicon_144',
		// SEO
		'meta_description',
		'meta_keywords',
		'meta_author',
		'og_image',
		'theme_color',
		// Google Analytics
		'ga_enabled',
		'ga_tracking_id',
		'ga_script',
		// Google Tag Manager
		'gtm_enabled',
		'gtm_container_id',
		// Facebook Pixel
		'fb_pixel_enabled',
		'fb_pixel_id',
		// reCAPTCHA
		'recaptcha_enabled',
		'recaptcha_site_key',
		'recaptcha_secret_key',
		'recaptcha_version',
		// Cookies y Privacidad
		'cookie_consent_enabled',
		'cookie_message',
		'privacy_policy_url',
		'terms_conditions_url',
		// Scripts personalizados
		'custom_head_scripts',
		'custom_body_scripts',
		// Timestamps
		'created_at',
		'updated_at',
	);

	/**
	 * Configuración de timestamps
	 */
	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events'          => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events'          => array('before_update'),
			'mysql_timestamp' => false,
		),
	);

	/**
	 * Nombre de la tabla
	 */
	protected static $_table_name = 'tenant_site_config';

	/**
	 * Obtener configuración del sitio para un tenant
	 *
	 * @param  int  $tenant_id  ID del tenant (por defecto 1)
	 * @return Model_SiteConfig|null
	 */
	public static function get_config($tenant_id = 1)
	{
		try {
			$config = static::query()
				->where('tenant_id', $tenant_id)
				->get_one();

			// Si no existe configuración, crear una por defecto
			if (!$config) {
				$config = static::forge(array(
					'tenant_id' => $tenant_id,
					'site_name' => 'Panel Admin',
					'theme_color' => '#008ad5',
					'cookie_consent_enabled' => 1,
				));
				$config->save();
			}

			return $config;
		} catch (Exception $e) {
			\Log::error('Error al obtener configuración del sitio: ' . $e->getMessage());
			return null;
		}
	}

	/**
	 * Actualizar configuración del sitio
	 *
	 * @param  int    $tenant_id  ID del tenant
	 * @param  array  $data       Datos a actualizar
	 * @return bool
	 */
	public static function update_config($tenant_id, $data)
	{
		try {
			$config = static::get_config($tenant_id);
			
			if (!$config) {
				return false;
			}

			// Actualizar solo los campos permitidos
			$allowed_fields = array(
				'site_name', 'site_tagline', 'contact_email', 'contact_phone', 'address',
				'logo_url', 'logo_alt_url',
				'favicon_16', 'favicon_32', 'favicon_57', 'favicon_72', 'favicon_114', 'favicon_144',
				'meta_description', 'meta_keywords', 'meta_author', 'og_image', 'theme_color',
				'ga_enabled', 'ga_tracking_id', 'ga_script',
				'gtm_enabled', 'gtm_container_id',
				'fb_pixel_enabled', 'fb_pixel_id',
				'recaptcha_enabled', 'recaptcha_site_key', 'recaptcha_secret_key', 'recaptcha_version',
				'cookie_consent_enabled', 'cookie_message', 'privacy_policy_url', 'terms_conditions_url',
				'custom_head_scripts', 'custom_body_scripts'
			);

			foreach ($data as $key => $value) {
				if (in_array($key, $allowed_fields)) {
					$config->{$key} = $value;
				}
			}

			return $config->save();
		} catch (Exception $e) {
			\Log::error('Error al actualizar configuración del sitio: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Generar script de Google Analytics
	 *
	 * @return string|null
	 */
	public function get_ga_script()
	{
		if (!$this->ga_enabled || empty($this->ga_tracking_id)) {
			return null;
		}

		// Si hay script personalizado, usarlo
		if (!empty($this->ga_script)) {
			return $this->ga_script;
		}

		// Generar script por defecto
		return <<<HTML
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$this->ga_tracking_id}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{$this->ga_tracking_id}');
</script>
HTML;
	}

	/**
	 * Generar script de Google Tag Manager (HEAD)
	 *
	 * @return string|null
	 */
	public function get_gtm_head_script()
	{
		if (!$this->gtm_enabled || empty($this->gtm_container_id)) {
			return null;
		}

		return <<<HTML
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$this->gtm_container_id}');</script>
<!-- End Google Tag Manager -->
HTML;
	}

	/**
	 * Generar script de Google Tag Manager (BODY)
	 *
	 * @return string|null
	 */
	public function get_gtm_body_script()
	{
		if (!$this->gtm_enabled || empty($this->gtm_container_id)) {
			return null;
		}

		return <<<HTML
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$this->gtm_container_id}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
HTML;
	}

	/**
	 * Generar script de Facebook Pixel
	 *
	 * @return string|null
	 */
	public function get_fb_pixel_script()
	{
		if (!$this->fb_pixel_enabled || empty($this->fb_pixel_id)) {
			return null;
		}

		return <<<HTML
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{$this->fb_pixel_id}');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={$this->fb_pixel_id}&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->
HTML;
	}

	/**
	 * Generar script de reCAPTCHA
	 *
	 * @return string|null
	 */
	public function get_recaptcha_script()
	{
		if (!$this->recaptcha_enabled || empty($this->recaptcha_site_key)) {
			return null;
		}

		if ($this->recaptcha_version === 'v3') {
			return "<script src=\"https://www.google.com/recaptcha/api.js?render={$this->recaptcha_site_key}\"></script>";
		}

		return "<script src=\"https://www.google.com/recaptcha/api.js\" async defer></script>";
	}

	/**
	 * Generar HTML del widget de reCAPTCHA
	 *
	 * @return string|null
	 */
	public function get_recaptcha_widget()
	{
		if (!$this->recaptcha_enabled || empty($this->recaptcha_site_key)) {
			return null;
		}

		if ($this->recaptcha_version === 'v3') {
			// v3 es invisible, solo retorna el site key para usar en JS
			return $this->recaptcha_site_key;
		}

		// v2 muestra widget visible
		return "<div class=\"g-recaptcha\" data-sitekey=\"{$this->recaptcha_site_key}\"></div>";
	}

	/**
	 * Validar reCAPTCHA en el servidor
	 *
	 * @param  string  $response  Token de respuesta de reCAPTCHA
	 * @return bool
	 */
	public function verify_recaptcha($response)
	{
		if (!$this->recaptcha_enabled || empty($this->recaptcha_secret_key)) {
			return true; // Si no está habilitado, permitir
		}

		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$data = array(
			'secret'   => $this->recaptcha_secret_key,
			'response' => $response,
			'remoteip' => \Input::real_ip()
		);

		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data)
			)
		);

		try {
			$context  = stream_context_create($options);
			$result   = file_get_contents($url, false, $context);
			$response_data = json_decode($result);

			return isset($response_data->success) && $response_data->success === true;
		} catch (Exception $e) {
			\Log::error('Error al verificar reCAPTCHA: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Obtener todos los scripts de tracking para incluir en <head>
	 *
	 * @return string
	 */
	public function get_all_head_scripts()
	{
		$scripts = array();

		// Google Analytics
		if ($ga_script = $this->get_ga_script()) {
			$scripts[] = $ga_script;
		}

		// Google Tag Manager
		if ($gtm_script = $this->get_gtm_head_script()) {
			$scripts[] = $gtm_script;
		}

		// Facebook Pixel
		if ($fb_script = $this->get_fb_pixel_script()) {
			$scripts[] = $fb_script;
		}

		// reCAPTCHA
		if ($recaptcha_script = $this->get_recaptcha_script()) {
			$scripts[] = $recaptcha_script;
		}

		// Scripts personalizados
		if (!empty($this->custom_head_scripts)) {
			$scripts[] = $this->custom_head_scripts;
		}

		return implode("\n\n", $scripts);
	}

	/**
	 * Obtener todos los scripts para incluir al inicio de <body>
	 *
	 * @return string
	 */
	public function get_all_body_scripts()
	{
		$scripts = array();

		// Google Tag Manager body
		if ($gtm_body = $this->get_gtm_body_script()) {
			$scripts[] = $gtm_body;
		}

		// Scripts personalizados
		if (!empty($this->custom_body_scripts)) {
			$scripts[] = $this->custom_body_scripts;
		}

		return implode("\n\n", $scripts);
	}
}
