<?php

/**
 * CONTROLADOR ADMIN
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin extends Controller_Baseadmin
{
	public $template = 'admin/template';

	/**
	 * BEFORE
	 *
	 * REVISA SI EL USUARIO TIENE UNA SESION,
	 * SI NO EXISTE REDIRECCIONA AL LOGIN
	 *
	 * @return Void
	 */
	public function before()
	{
		# REQUERIDA PARA EL TEMPLATING
		parent::before();

		# SI NO ESTÁ LOGUEADO Y NO ESTÁ EN LA ACCIÓN DE LOGIN, REDIRIGIR A LOGIN
		if (!Auth::check() and Request::active()->action != 'login')
		{
			Response::redirect('admin/login');
		}

		# SI ESTÁ LOGUEADO Y NO ESTÁ EN LOGIN, VERIFICAR PERMISOS
		if (Auth::check() and Request::active()->action != 'login')
		{
			# VERIFICAR QUE TENGA PERMISO DE ACCESO AL DASHBOARD
			if (!Helper_Permission::can('dashboard', 'view'))
			{
				Auth::logout();
				Session::set_flash('error', 'No tienes permisos de administrador.');
				Response::redirect('admin/login');
			}

			# ASEGURAR QUE EXISTE TENANT_ID EN SESIÓN
			if (!Session::get('tenant_id'))
			{
				Session::set('tenant_id', 1); // Default tenant
			}
		}
	}


	// /**
	//  * LOGIN
	//  *
	//  * COMPRUEBA EL NOMBRE DE USUARIO Y LA CONTRASEÑA COTEJANDO EN LA BASE DE DATOS,
	//  * SI EXISTE CREA LA SESION DEL ADMINISTRADOR Y REDIRECCIONA AL DASHBOARD
	//  *
	//  * @access  public
	//  * @return  Void
	//  */
	//	public function action_login()
	//	{
	//		# SI ESTÁ LOGUEADO SE REDIRECCIONA AL DASHBOARD
	//		Auth::check() and Response::redirect('admin');
//
//			# SE INICIALIZA EL ARREGLO DATA
//			$data = array();
//
//			# SE INICIALIZA LA VARIABLE SECRET_KEY SAJOR.COM.MX
//			$secret_key = '6Les_vkSAAAAAMZwY4amtm1_KjdAn63XeugrnNJk';
//
//			# SE INICIALIZA LA VARIABLE USERNAME
//			$username = '';
//
//			# SI SE UTILIZÓ EL MÉTODO POST
//			if(Input::method() == 'POST')
//			{
//				# SE OBTIENEN LOS DATOS ENVIADOS POR POST
//				$username    = Input::post('username');
//				$password    = Input::post('password');
//				$rememberme  = Input::post('rememberme');
//				$g_recaptcha = Input::post('g-recaptcha-response');
//
//				# SI EL RECAPTCHA NO ESTÁ VACÍO
//				if($g_recaptcha != '')
//				{
//					# SE INICIALIZA cURL
//					$ch = curl_init();
//					curl_setopt($ch, CURLOPT_URL, 'https://google.com/recaptcha/api/siteverify');
//					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
//					curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
//						'secret'   => $secret_key,
//						'response' => $g_recaptcha,
//						'remoteip' => Input::ip()
//					]));
//
//					# SE EJECUTA LA PETICIÓN Y SE DECODIFICA
//					$response_recaptcha = curl_exec($ch);
//					$recaptcha = json_decode($response_recaptcha, true);
//					curl_close($ch);
//
//					# SI EL RECAPTCHA ES VÁLIDO
//					if($recaptcha['success'])
//					{
//						# SE EJECUTA LOGIN DEL MÓDULO DE USER
//						$response = Request::forge('sectorweb/admin/login', false)
//							->execute(array($username, $password, $rememberme))
//							->response
//							->body;
//
//						# DEPENDIENDO DE LA RESPUESTA
//						switch($response)
//						{
//							case 'ok':
//								Response::redirect('admin');
//							break;
//
//							case 'ivalid_user':
//								Session::set_flash('error', '<p>Este usuario no es válido.</p>');
//							break;
//
//							case 'invalid_credentials':
//								Session::set_flash('error', '<p>Nombre de usuario o contraseña incorrectos.</p>');
//							break;
//
//							default:
//								Session::set_flash('error', '<p>Algo inesperado ha ocurrido, por favor recarga la página.</p>');
//							break;
//						}
//					}
//					else
//					{
//						Session::set_flash('error', '<p>No se pudo validar el captcha, por favor vuelve a intentarlo.</p>');
//					}
//				}
//				else
//				{
//					Session::set_flash('error', '<p>Es necesario validar que no eres un robot.</p>');
//				}
//			}
//
//			# SE ALMACENA LA INFORMACIÓN DEL FORMULARIO
//			$data['username'] = $username;
//
//			# SE CARGA LA VISTA
//			return View::forge('admin/login', array('data' => $data));
//		}



	/**
	 * LOGIN
	 *
	 * COMPRUEBA EL NOMBRE DE USUARIO Y LA CONTRASEÑA COTEJANDO EN LA BASE DE DATOS,
	 * SI EXISTE CREA LA SESION DEL ADMINISTRADOR Y REDIRECCIONA AL DASHBOARD
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_login()
	{
		# SI ESTA LOGUEADO SE REDIRECCIONA AL DASHBOARD
		Auth::check() and Response::redirect('admin');

		# SE INICIALIZA EL ARREGLO DATA
		$data = array();

		# SE INICIALIZA LA VARIABLE USERNAME
		$username = '';

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
            # SE OBTIENEN LOS DATOS ENVIADOS POR POST
            $username   = Input::post('username');
            $password   = Input::post('password');
			$rememberme = Input::post('rememberme');

			# DEBUG: Log intento de login
			\Log::info('=== INTENTO DE LOGIN ===');
			\Log::info('Usuario: ' . $username);
			\Log::info('Password length: ' . strlen($password));
			\Log::info('Password: ' . $password); // TEMPORAL para debug
			
			# VALIDAR reCAPTCHA SI ESTÁ HABILITADO
			$site_config = Model_SiteConfig::get_config(1); // TODO: tenant_id dinámico
			\Log::info('reCAPTCHA enabled: ' . ($site_config ? ($site_config->recaptcha_enabled ? 'YES' : 'NO') : 'NULL'));
			
			if ($site_config && $site_config->recaptcha_enabled)
			{
				$recaptcha_response = Input::post('g-recaptcha-response');
				
				if (empty($recaptcha_response))
				{
					\Log::info('reCAPTCHA: Respuesta vacía');
					Session::set_flash('error', '<p>Por favor completa la verificación reCAPTCHA.</p>');
					$data['username'] = $username;
					return View::forge('admin/login', array('data' => $data));
				}
				
				if (!$site_config->verify_recaptcha($recaptcha_response))
				{
					\Log::info('reCAPTCHA: Verificación fallida');
					Session::set_flash('error', '<p>Verificación reCAPTCHA fallida. Intenta de nuevo.</p>');
					$data['username'] = $username;
					return View::forge('admin/login', array('data' => $data));
				}
			}

			# SE INTENTA HACER LOGIN CON SIMPLEAUTH DE FUELPHP
			\Log::info('Intentando Auth::login()...');
			$login_result = Auth::login($username, $password);
			\Log::info('Auth::login() result: ' . ($login_result ? 'TRUE' : 'FALSE'));
			
			if ($login_result)
			{
				# LOGIN EXITOSO - VERIFICAR PERMISOS CON RBAC
				$user_id = Auth::get('id');
				\Log::info('Usuario logueado ID: ' . $user_id);
				
				# VERIFICAR QUE TENGA PERMISO DE DASHBOARD
				if (Helper_Permission::can('dashboard', 'view'))
				{
					\Log::info('Usuario tiene permiso de dashboard.');
					
					# ESTABLECER TENANT POR DEFECTO
					$default_tenant = DB::select('tenant_id')
						->from('user_tenants')
						->where('user_id', '=', $user_id)
						->where('is_default', '=', 1)
						->where('is_active', '=', 1)
						->execute()
						->current();
					
					if ($default_tenant)
					{
						Session::set('tenant_id', $default_tenant['tenant_id']);
						\Log::info('Tenant establecido: ' . $default_tenant['tenant_id']);
					}
					else
					{
						# Si no tiene tenant por defecto, usar el primero disponible
						$first_tenant = DB::select('tenant_id')
							->from('user_tenants')
							->where('user_id', '=', $user_id)
							->where('is_active', '=', 1)
							->limit(1)
							->execute()
							->current();
						
						if ($first_tenant)
						{
							Session::set('tenant_id', $first_tenant['tenant_id']);
							\Log::info('Tenant establecido (primero disponible): ' . $first_tenant['tenant_id']);
						}
						else
						{
							# Fallback: tenant 1
							Session::set('tenant_id', 1);
							\Log::info('Tenant establecido (fallback): 1');
						}
					}
					
					# SE ESTABLECE LA SESION SI SE SOLICITO RECORDAR
					if ($rememberme)
					{
						Auth::remember_me();
					}
					
					# SE REDIRECCIONA AL DASHBOARD
					Session::set_flash('success', '¡Bienvenido al panel de administración!');
					Response::redirect('admin');
				}
				else
				{
					\Log::info('Usuario NO tiene permiso de dashboard. Cerrando sesión.');
					# NO TIENE PERMISOS, CERRAR SESION
					Auth::logout();
					Session::set_flash('error', '<p>Este usuario no tiene permisos de administrador.</p>');
				}
			}
			else
			{
				\Log::info('Auth::login() FALLÓ - Credenciales incorrectas');
				# CREDENCIALES INCORRECTAS
				Session::set_flash('error', '<p>Nombre de usuario o contraseña incorrectos.</p>');
			}
		}

		# SE ALMACENA LA INFORMACION DEL FORMULARIO
        $data['username'] = $username;

		# SE CARGA LA VISTA
		return View::forge('admin/login', array('data' => $data));
	}


	/**
	 * INDEX
	 *
	 * CARGA LA VISTA DEL DASHBOARD
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_index()
	{
		# DATOS BÁSICOS
		$data = [];
		$data['title'] = 'Dashboard';
		$data['tenant_id'] = Session::get('tenant_id', 1);
		$data['username'] = Auth::get('username');
		$data['email'] = Auth::get('email');
		
		# PERMISOS DEL USUARIO
		$data['is_super_admin'] = Helper_Permission::is_super_admin();
		$data['is_admin'] = Helper_Permission::is_admin();
		
		# MENSAJE DE BIENVENIDA
		$days = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
		$months = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
		$data['date'] = $days[date('w')] . ' ' . date('d') . ' de ' . $months[date('n')-1] . ' del ' . date('Y');
		
		# ESTADÍSTICAS BÁSICAS (TODO: implementar cuando tengamos los módulos)
		$data['stats'] = [
			'users' => 0,
			'products' => 0,
			'sales' => 0,
			'customers' => 0,
		];

		# RENDERIZAR VISTA
		$data['content'] = View::forge('admin/index', $data);
		
		// Obtener template según preferencias del usuario
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}

	/**
	 * LOGOUT
	 *
	 * DESTRUYE LA SESION Y REDIRIGE AL LOGIN
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_logout()
	{
		# LIMPIAR CACHE DE PERMISOS
		Helper_Permission::clear_cache();
		
		# CERRAR SESIÓN
		Auth::logout();
		
		# LIMPIAR TENANT DE SESIÓN
		Session::delete('tenant_id');

		# REDIRECCIONAR AL LOGIN
		Response::redirect('admin/login');
	}
}
