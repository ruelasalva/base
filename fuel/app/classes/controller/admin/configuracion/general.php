<?php

/**
* CONTROLADOR ADMIN_ABANDONADOS
*
* @package  app
* @extends  Controller_Admin
*/
class Controller_Admin_Configuracion_General extends Controller_Admin
{
	/**
	* BEFORE
	*
	* @return Void
	*/
	public function before()
	{
		# REQUERIDA PARA EL TEMPLATING
		parent::before();

		# SI EL USUARIO NO TIENE PERMISOS
			# SE VERIFICA QUE EL USUARIO ESTA LOGUEADO
			if (!Auth::check()) {
			# SE MANDA MENSAJE SI NO	
			Session::set_flash('error', 'Debes iniciar sesión.');
			# Y SE REDIRECIONA A QUE SE LOGUEE
			Response::redirect('admin/login');
		}
	}

    /**
     * INDEX
     *
     * MUESTRA LA CONFIGURACIÓN GENERAL SI EXISTE
     *
     * @access  public
     * @return  Void
     */
    public function action_index()
    {
        #HELPER DE PERMISO PARA VISTA
		if (!Helper_Permission::can('config_general', 'view')) {
			#SI NO TIENE PERMISO SE DA EL ERROR
			Session::set_flash('error', 'No tienes permiso para ver la configuracion.');
			#SI FUERA POR ENLACEN SE REDIRECIONA AL ADMIN
			Response::redirect('admin');
		}

        # SE INICIALIZAN LAS VARIABLES
        $data   = array();
        $config = null;

        # SE BUSCA LA CONFIGURACIÓN A TRAVÉS DEL MODELO
        $config = Model_Config::query()
            ->related('sat_tax_regime')
            ->get_one();

        # SE ASIGNAN TODOS LOS CAMPOS PARA LA VISTA
        if (!empty($config))
        {
            $data['id']                       = $config->id;
            $data['name']                     = ($config->name != '') ? $config->name : 'No capturado';
            $data['rfc']                      = ($config->rfc != '') ? $config->rfc : 'No capturado';
            $data['cp']                       = ($config->cp != '') ? $config->cp : 'No capturado';
            $data['id_sat_tax_regimes']       = $config->id_sat_tax_regimes;
            $data['sat_tax_regime_name']      = $config->sat_tax_regime->name ?? 'Por capturar';
            $data['updated_at']               = ($config->updated_at > 0) ? date('d/m/Y H:i', $config->updated_at) : '';
            $data['invoice_receive_days']     = $config->invoice_receive_days ?? '';
            $data['invoice_receive_limit_time']= $config->invoice_receive_limit_time ?? '';
            $data['payment_days']             = $config->payment_days ?? '';
            $data['payment_terms_days']       = $config->payment_terms_days ?? '';
            $data['contact_email']            = $config->contact_email ?? '';
            $data['contact_phone']            = $config->contact_phone ?? '';
            $data['announcement_message']     = $config->announcement_message ?? '';
            $data['blocked_reception']        = $config->blocked_reception ?? 0;
            $data['holidays']                 = $config->holidays ?? '';
            $data['policy_file']              = $config->policy_file ?? '';
            $data['payment_frequency']        = isset($config->payment_frequency) ? $config->payment_frequency : '';
            $data['payment_days_of_month']    = isset($config->payment_days_of_month) ? $config->payment_days_of_month : '';
        }
        else
        {
            # NO HAY CONFIGURACIÓN, SE MUESTRA TODO VACÍO EN LA VISTA
            $data = array(
                'id'                        => '',
                'name'                      => '',
                'rfc'                       => '',
                'cp'                        => '',
                'id_sat_tax_regimes'        => '',
                'sat_tax_regime_name'       => '',
                'updated_at'                => '',
                'invoice_receive_days'      => '',
                'invoice_receive_limit_time'=> '',
                'payment_days'              => '',
                'payment_terms_days'        => '',
                'contact_email'             => '',
                'contact_phone'             => '',
                'announcement_message'      => '',
                'blocked_reception'         => 0,
                'holidays'                  => '',
                'policy_file'               => '',
                'payment_frequency'         => '',
                'payment_days_of_month'     => '',
            );
        }

        # SE CARGA LA VISTA
        $this->template->title   = 'Configuración General de la Empresa';
        $this->template->content = View::forge('admin/configuracion/general/info', $data);
    }






    /**
     * EDITAR
     *
     * PERMITE AGREGAR O MODIFICAR LOS DATOS GENERALES DE LA EMPRESA
     *
     * @access  public
     * @return  Void
     */
    public function action_editar($id = 0)
    {
        #HELPER DE PERMISO PARA CREAR
		if (!Helper_Permission::can('config_general', 'edit')) {
			Session::set_flash('error', 'No tienes permiso para editar la confiracion.');
			Response::redirect('admin/configuracion/general/info');	
    	}
        # SE INICIALIZAN LAS VARIABLES
        $data    = array();
        $classes = array();
        $fields  = array(
            'name', 'rfc', 'cp', 'id_sat_tax_regimes',
            'invoice_receive_days', 'invoice_receive_limit_time',
            'payment_days', 'payment_terms_days',
            'contact_email', 'contact_phone', 'announcement_message',
            'blocked_reception', 'holidays','policy_file'
        );
        $errors  = array();

        # SE RECORREN LOS CAMPOS PARA INICIALIZAR LAS CLASES
        foreach($fields as $field)
        {
            $classes[$field] = array(
                'form-group'   => null,
                'form-control' => null,
            );
        }

        # SE BUSCA LA CONFIGURACIÓN EXISTENTE O SE CREA UNA NUEVA
        $config = ($id > 0) 
            ? Model_Config::query()->where('id', $id)->get_one()
            : Model_Config::query()->get_one();

        if (!$config)
        {
            $config = Model_Config::forge(array(
                'name'                       => '',
                'rfc'                        => '',
                'cp'                         => '',
                'id_sat_tax_regimes'         => null,
                'invoice_receive_days'       => '',
                'invoice_receive_limit_time' => '',
                'payment_days'               => '',
                'payment_terms_days'         => '',
                'contact_email'              => '',
                'contact_phone'              => '',
                'announcement_message'       => '',
                'blocked_reception'          => 0,
                'holidays'                   => '',
                'policy_file'                => '',
                'created_at'                 => time(),
                'updated_at'                 => time()
            ));
        }

        # SI SE ENVÍA EL FORMULARIO
        if (Input::method() == 'POST')
        {
        // LOS CAMPOS QUE SON CHECKBOX MULTIPLE LLEGAN COMO ARRAY, CONVIÉRTELOS EN STRING
        $rcv_days_post = Input::post('invoice_receive_days', []);
        $config->invoice_receive_days = is_array($rcv_days_post) ? implode(',', $rcv_days_post) : '';

        $pay_days_post = Input::post('payment_days', []);
        $config->payment_days = is_array($pay_days_post) ? implode(',', $pay_days_post) : '';

        // LOS DEMÁS CAMPOS IGUAL QUE TENÍAS
        $config->name                    = trim(Input::post('name'));
        $config->rfc                     = strtoupper(trim(Input::post('rfc')));
        $config->cp                      = trim(Input::post('cp'));
        $config->id_sat_tax_regimes      = Input::post('id_sat_tax_regimes');
        $config->invoice_receive_limit_time = trim(Input::post('invoice_receive_limit_time'));
        $config->payment_terms_days      = Input::post('payment_terms_days');
        $config->contact_email           = trim(Input::post('contact_email'));
        $config->contact_phone           = trim(Input::post('contact_phone'));
        $config->announcement_message    = trim(Input::post('announcement_message'));
        $config->blocked_reception       = Input::post('blocked_reception', 0);
        $config->holidays                = trim(Input::post('holidays'));
        $config->updated_at              = time();

        // SI USAS ESTOS CAMPOS:
        $config->payment_frequency       = Input::post('payment_frequency', '');
        $config->payment_days_of_month   = trim(Input::post('payment_days_of_month'));

            # VALIDACIONES
            if (empty($config->name)) {
                $errors['name'] = 'El nombre de la empresa es obligatorio.';
                $classes['name']['form-group']   = 'has-danger';
                $classes['name']['form-control'] = 'is-invalid';
            }
            if (empty($config->rfc)) {
                $errors['rfc'] = 'El RFC es obligatorio.';
                $classes['rfc']['form-group']   = 'has-danger';
                $classes['rfc']['form-control'] = 'is-invalid';
            }
            if (empty($config->cp)) {
                $errors['cp'] = 'El CP es obligatorio.';
                $classes['cp']['form-group']   = 'has-danger';
                $classes['cp']['form-control'] = 'is-invalid';
            }
            if (empty($config->id_sat_tax_regimes) || !is_numeric($config->id_sat_tax_regimes)) {
                $errors['id_sat_tax_regimes'] = 'Debes seleccionar un régimen fiscal.';
                $classes['id_sat_tax_regimes']['form-group']   = 'has-danger';
                $classes['id_sat_tax_regimes']['form-control'] = 'is-invalid';
            }
            // Validación extra: correo si lo deseas
            if (!empty($config->contact_email) && !filter_var($config->contact_email, FILTER_VALIDATE_EMAIL)) {
                $errors['contact_email'] = 'El correo de contacto no es válido.';
                $classes['contact_email']['form-group'] = 'has-danger';
                $classes['contact_email']['form-control'] = 'is-invalid';
            }

            # MANEJO DEL ARCHIVO DE POLÍTICAS (PDF)
            if (isset($_FILES['policy_file']) && $_FILES['policy_file']['size'] > 0) {
                $file = $_FILES['policy_file'];
                $filename = uniqid('policy_').'.pdf';
                $target_dir = DOCROOT.'uploads/config/';
                if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
                if (move_uploaded_file($file['tmp_name'], $target_dir.$filename)) {
                    $config->policy_file = $filename;
                    // Puedes agregar aquí logs si lo necesitas
                } else {
                    $errors['policy_file'] = 'Error al subir el archivo de políticas.';
                }
            }

            # SI NO HAY ERRORES, SE GUARDA
            if (empty($errors))
            {
                if ($config->save()) {
                    Session::set_flash('success', 'Datos guardados correctamente.');
                    Response::redirect('admin/configuracion/general');
                } else {
                    Session::set_flash('error', 'Ocurrió un error al guardar los datos.');
                }
            }
            else
            {
                Session::set_flash('error', 'Corrige los errores marcados en el formulario.');
                $data['errors'] = $errors;
            }
        }

        # CARGAR LOS DATOS PARA LA VISTA
        foreach($fields as $field) {
            $data[$field] = $config->$field;
        }
        $data['policy_file']         = $config->policy_file;
        $data['classes']             = $classes;
        $data['id']                  = $config->id;
        $data['regimen_opts']        = Model_Sat_Tax_Regime::get_for_input();

        # SE CARGA LA VISTA
        $this->template->title   = 'Editar configuración general';
        $this->template->content = View::forge('admin/configuracion/general/editar', $data);
    }



}
