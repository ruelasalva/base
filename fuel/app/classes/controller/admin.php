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

		# SI EL USUARIO NO ES ADMINISTRADOR Y NO ESTÁ EN LOGIN
		if (Auth::check() and Request::active()->action != 'login')
		{
			$user = Model_User::find(Auth::get('id'));
			
			if (!$user || !in_array($user->group_id, [100, 50, 30, 25, 20]))
			{
				# SE DESTRUYE SU SESION
				Auth::logout();
				Session::set_flash('error', 'No tienes permisos de administrador.');
				Response::redirect('admin/login');
			}

			// Cargar permisos de grupo en sesión
			Helper_Permission::refresh_session_group_permissions($user->group_id);

			// Cargar tema del usuario
			$theme = Model_TenantTheme::get_user_theme($user->id);
			if ($theme) {
				$this->template = $theme->template_file;
				$this->theme = $theme;
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

			# VALIDAR reCAPTCHA SI ESTÁ HABILITADO
			$site_config = Model_SiteConfig::get_config(1); // TODO: tenant_id dinámico
			if ($site_config && $site_config->recaptcha_enabled)
			{
				$recaptcha_response = Input::post('g-recaptcha-response');
				
				if (empty($recaptcha_response))
				{
					Session::set_flash('error', '<p>Por favor completa la verificación reCAPTCHA.</p>');
					$data['username'] = $username;
					return View::forge('admin/login', array('data' => $data));
				}
				
				if (!$site_config->verify_recaptcha($recaptcha_response))
				{
					Session::set_flash('error', '<p>Verificación reCAPTCHA fallida. Intenta de nuevo.</p>');
					$data['username'] = $username;
					return View::forge('admin/login', array('data' => $data));
				}
			}

			# SE INTENTA HACER LOGIN CON SIMPLEAUTH DE FUELPHP
			if (Auth::login($username, $password))
			{
				# VERIFICAR SI EL USUARIO ES ADMINISTRADOR (grupo 100, 50, 30, 25, 20)
				$user = Model_User::find(Auth::get('id'));
				
				if ($user && in_array($user->group_id, [100, 50, 30, 25, 20]))
				{
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
					# NO ES ADMINISTRADOR, CERRAR SESION
					Auth::logout();
					Session::set_flash('error', '<p>Este usuario no tiene permisos de administrador.</p>');
				}
			}
			else
			{
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

		 $group_id = Auth::get_groups()[0][1];

		if (Auth::check() && ($group_id == 20 || $group_id == 30)) {
			Response::redirect('admin/crm/ticket/index');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data        = array();
		$days        = array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
		$months      = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
		$slides_info = array();
		$sales_info  = array();
		$admins_info = array();
		$users_info  = array();

		# SE ALMACENA LA FECHA DE HOY
		$date = $days[date('w', time())].' '.date('d', time()).' de '.$months[date('n', time())-1]. ' del '.date('Y', time());

		# SE OBTIENE LA INFORMACION DE LOS MODELOS
		$sales_count  = Model_Sale::query()->where('status', '>=', 1);
		$users_count  = Model_User::query()->where('group', '=', 1);
		$admins_count = Model_User::query()->where('group', 100)->or_where('group', 50)->or_where('group', 30)->or_where('group', 25);
		$slides_count = Model_Slide::query();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$sales = Model_Sale::query()
		->related('customer')
		->where('status', '>=', 1)
		->order_by('sale_date', 'desc')
		->limit(5)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($sales))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($sales as $sale)
			{

				# SE INICIALIZA LA VARIABLE
				$status = '';

				# DEPENDIENDO DEL ESTATUS
				switch($sale->status)
				{
					case 1:
					$status = 'Pagado';
					break;
					case 2:
					$status = 'Por revisar';
					break;
					case 3:
					$status = 'Cancelada';
					break;
				}

				# DEPENDIENDO LA ORDEN
				$order = $sale->order_id;
				if ($order <= 0){
					$order = 'En espera de asignación';
				}else{
					$order = $sale->order->name;
				}

				# SE ALMACENA LA INFORMACION
				$sales_info[] = array(
					'id'       => $sale->id,
					'customer' => $sale->customer->name.' '.$sale->customer->last_name,
					'email'    => $sale->customer->user->email,
                    'type'     => ($sale->status == 2) ? $sale->payment->type->name.' (Por revisar)' : $sale->payment->type->name,
					'total'    => '$'.number_format($sale->total, '2', '.', ','),
					'status'   => $status,
					'order'    => $order,
					'sale_date' => date('d/m/Y - H:i', $sale->sale_date)
				);
			}
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$admins = Model_User::query()
		->where_open()
		->where('group', 100)
		->or_where('group', 50)
		->or_where('group', 30)
		->or_where('group', 25)
		->where_close()
		->order_by('id', 'desc')
		->limit(5)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($admins))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($admins as $admin)
			{
				# SE DESERIALIZAN LOS CAMPOS EXTRAS
				$status = unserialize($admin->profile_fields);

				# SE ALMACENA LA INFORMACION
				$admins_info[] = array(
					'full_name' => $status['full_name'],
					'email'     => $admin->email
				);
			}
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$slides = Model_Slide::query()
		->order_by('id', 'desc')
		->limit(5)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($slides))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($slides as $slide)
			{
				# SE ALMACENA LA INFORMACION
				$slides_info[] = array(
					'title' => $slide->url,
					'type'  => 'Index'
				);
			}
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$users = Model_User::query()
		->where_open()
		->where('group', 1)
		->where_close()
		->order_by('id', 'desc')
		->limit(7)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($users))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($users as $user)
			{
				# SE DESERIALIZAN LOS CAMPOS EXTRAS
				$status = unserialize($user->profile_fields);

				# SE ALMACENA LA INFORMACION
				$users_info[] = array(
					'username' => $user->username,
					'connected' => ($status['connected']) ? 'Conectado' : 'Desconectado',
					'email'     => $user->email,
					'updated_at' => date('d/m/Y - H:i', $user->updated_at)
				);
			}
		}

		# SE OBTIENE EL TOTAL DE SOCIOS (grupo 15)
		$total_partners = Model_User::query()
			->where('group', 15)
			->count();

		# SE OBTIENEN LOS SOCIOS ACTUALIZADOS EN LA SEMANA (updated > created)
		$updated_partners_week = Model_Partner::query()
			//->where('updated_at', '>=', strtotime('-7 days'))
			->where(DB::expr('updated_at'), '>', DB::expr('created_at'))
			->count();


		# SE PASA A LA VISTA
		$data['total_partners']         = $total_partners;
		$data['updated_partners_week']  = $updated_partners_week;




		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['date']         = $date;
		$data['sales_count']  = $sales_count->count();
		$data['admins_count'] = $admins_count->count();
		$data['users_count']  = $users_count->count();
		$data['slides_count'] = $slides_count->count();
		$data['admins']       = $admins_info;
		$data['users']        = $users_info;
		$data['sales']        = $sales_info;
		$data['slides']       = $slides_info;

		# SE CARGA LA VISTA
		$this->template->title   = 'Dashboard';
		$this->template->content = View::forge('admin/dashboard', $data);
	}


	/**
	 * LOGOUT
	 *
	 * CIERRA LA SESION DEL ADMINISTRADOR
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_logout()
	{
		# SE CIERRA LA SESION CON AUTH DE FUELPHP
		Auth::logout();

		# SE REDIRECCIONA AL LOGIN
		Response::redirect('admin/login');
	}

	/**
	 * CAMBIAR TEMA
	 *
	 * Permite al usuario cambiar el tema del admin
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_change_theme()
	{
		if (!Auth::check()) {
			Response::redirect('admin/login');
		}

		$user_id = Auth::get('id');
		$theme_id = Input::post('theme_id');

		if ($theme_id) {
			$theme = Model_TenantTheme::find($theme_id);
			
			if ($theme && $theme->is_active) {
				Model_UserThemePreference::set_user_theme($user_id, $theme_id);
				Session::set_flash('success', 'Tema cambiado exitosamente a: ' . $theme->name);
			} else {
				Session::set_flash('error', 'Tema no válido');
			}
		}

		Response::redirect('admin');
	}

	/**
	 * CONFIGURACIÓN DE TEMAS
	 *
	 * Muestra los temas disponibles para seleccionar
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_themes()
	{
		if (!Auth::check()) {
			Response::redirect('admin/login');
		}

		$data = array();
		$data['themes'] = Model_TenantTheme::get_active_themes();
		$data['current_theme'] = Model_TenantTheme::get_user_theme(Auth::get('id'));

		$this->template->title = 'Seleccionar Tema';
		$this->template->content = View::forge('admin/themes', $data);
	}
}
