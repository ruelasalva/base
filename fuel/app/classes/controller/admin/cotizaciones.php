<?php

/**
* CONTROLADOR COTIZACINES
*
* @package  app
* @extends  Controller_Admin
*/
class Controller_Admin_Cotizaciones extends Controller_Admin
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
		if (!Auth::check()) {
            Session::set_flash('error', 'Debes iniciar sesión.');
            Response::redirect('admin/login');
        }
	}


	/**
	* INDEX
	*
	* MUESTRA UNA LISTADO DE REGISTROS
	*
	* @access  public
	* @return  Void
	*/
	public function action_index($search = '')
	{
        
        if (!Helper_Permission::can('ventas_cotizaciones', 'view')) {
            Session::set_flash('error', 'No tienes permiso para ver cotizaciones.');
            Response::redirect('admin');
        }

		# SE INICIALIZAN LAS VARIABLES
		$data           = array();
		$quotes_info    = array();
		$per_page       = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$quotes = Model_Quote::query()
		->related('partner')
		->where('status', '>=', 0);

		# SI HAY UNA BUSQUEDA
		if($search != '')
		{
			# SE ALMACENA LA BUSQUEDA ORIGINAL
			$original_search = $search;

			# SE LIMPIA LA CADENA DE BUSQUEDA
			$search = str_replace('+', ' ', rawurldecode($search));

			# SE REEMPLAZA LOS ESPACIOS POR PORCENTAJES
			$search = str_replace(' ', '%', $search);

			# SE AGREGA LA CLAUSULA
			$quotes = $quotes->where(DB::expr("CONCAT(`t0`.`id`, ' ', `t1`.`name`, ' ', `t1`.`code_sap`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $quotes->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
			'show_first'     => true,
    		'show_last'      => true,
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('quotes', $config);

		# SE EJECUTA EL QUERY
		$quotes = $quotes->order_by('created_at', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($quotes))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($quotes as $quote)
			{
				# SE INICIALIZA LA VARIABLE
				$status = '';

				# DEPENDIENDO DEL ESTATUS
				switch($quote->status)
				{
					case 0:
					$status = 'Borrador';
					break;
					case 1:
					$status = 'Activo';
					break;
					case 2:
					$status = 'En proceso';
					break;
                    case 3:
					$status = 'Procesado';
					break;
                    case 99:
					$status = 'Cancelada';
					break;
				}

                // Determinar el vendedor asignado a la cotización (NO al partner)
                $vendedor_asignado = '';
                if (!empty($quote->seller_asig_id)) {
                    $vendedor_model = Model_Employee::find($quote->seller_asig_id);
                    $vendedor_asignado = $vendedor_model ? $vendedor_model->name.' '.$vendedor_model->last_name : '';
                }
                // Si no tiene vendedor asignado en la cotización, mostrar el del partner
                if (empty($vendedor_asignado) && !empty($quote->partner) && !empty($quote->partner->employee_id)) {
                    $partner_vendedor_model = Model_Employee::find($quote->partner->employee_id);
                    $vendedor_asignado = $partner_vendedor_model ? $partner_vendedor_model->name.' '.$partner_vendedor_model->last_name : '';
                }
                // Si sigue vacío, poner No asignado
                if (empty($vendedor_asignado)) {
                    $vendedor_asignado = 'No asignado';
                }

                // Capturado por (employee_id en quotes)
                $capturada_por = '';
                if (!empty($quote->employee_id)) {
                    $capturista_model = Model_Employee::find($quote->employee_id);
                    $capturada_por = $capturista_model ? $capturista_model->name.' '.$capturista_model->last_name : '';
                }
                if (empty($capturada_por)) {
                    $capturada_por = 'Desconocido';
                }



				# SE ALMACENA LA INFORMACION
				$quotes_info[] = array(
					'id'            => $quote->id,
					'code_sap'      => $quote->partner->code_sap,
					'partner'       => $quote->partner->name,
                    'employee_id'   => $vendedor_asignado, // Vendedor asignado (partners)
                    'user'          => $capturada_por,     // Capturado por (quotes)
					'email'         => $quote->partner->email,
					'type'          => $quote->payment->token,
					'subtotal'      => '$'.number_format($quote->total, '2', '.', ','),
					'iva'           => '$'.number_format($quote->total * 0.16, '2', '.', ','),
					'total'         => '$'.number_format($quote->total * 1.16, '2', '.', ','),
					'valid_date'    => date('d/m/Y', $quote->valid_date),
					'status'        => $status,
					'status_num'    => $quote->status,
					'docnum'        => $quote->docnum
				);
			}
		}

        # AGREGAR COTIZACIONES PENDIENTES DE CLIENTES (SOCIOS)
    # OBTIENE COTIZACIONES PENDIENTES ENVIADAS POR CLIENTES (SOCIOS)
    $pending_quotes = Model_Quotes_Partner::query()
        ->where('status', 0) // O el status que definas como pendiente
        ->order_by('created_at', 'desc')
        ->get();

    $pending_quotes_info = [];
    if (!empty($pending_quotes)) {
        foreach ($pending_quotes as $pq) {
            // --- RELACIONAR USER_ID (QUE ESTÁ GUARDADO EN partner_id) CON EL PARTNER REAL ---
            $user_id = $pq->partner_id; // OJO: Aquí partner_id es realmente el user_id por tu estructura original
            $partner = Model_Partner::query()->where('user_id', $user_id)->get_one();

            if (!$partner) {
                \Log::error("[CotizacionesPendientes] No se encontró el partner con user_id: $user_id para la cotización pendiente ID: $pq->id");
            }

            // --- DESERIALIZAR Y ARMAR INFO DE PRODUCTOS ---
            $products_serialized = @unserialize($pq->quote); // Puede traer false si falla
            $products_info = [];

            if (!empty($products_serialized) && is_array($products_serialized)) {
                foreach ($products_serialized as $product_id => $item) {
                    $product = Model_Product::find($product_id);
                    $products_info[] = [
                        'id'          => $product_id,
                        'name'        => $product ? $product->name : 'Producto eliminado',
                        'quantity'    => $item['quantity'] ?? 0,
                        'description' => $product ? $product->description : '',
                        'image'       => $product ? $product->image : '',
                    ];
                }
            }

            // --- ARMAR INFO PARA LA VISTA ---
            $pending_quotes_info[] = [
                'id'         => $pq->id,
                'partner'    => $partner ? $partner->name : 'Desconocido',
                'created_at' => date('d/m/Y H:i', $pq->created_at),
                'products'   => $products_info,
                'status'     => 'Pendiente',
            ];
        }
    }

    $data['pending_quotes'] = $pending_quotes_info;



		# SE ALMACENA LA INFORMACION PARA LA VISTA
		
		$data['quotes']      = $quotes_info;
		$data['search']      = str_replace('%', ' ', $search);
		$data['pagination']  = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Cotizaciones';
		$this->template->content = View::forge('admin/cotizaciones/index', $data, false);
	}


	/**
	* BUSCAR
	*
	* REDIRECCIONA A LA URL DE BUSCAR REGISTROS
	*
	* @access  public
	* @return  Void
	*/
	public function action_buscar()
	{
        if (!Helper_Permission::can('ventas_cotizaciones', 'view')) {
            Session::set_flash('error', 'No tienes permiso para ver cotizaciones.');
            Response::redirect('admin');
        }

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE OBTIENEN LOS VALORES
			$data = array(
				'search' => ($_POST['search'] != '') ? $_POST['search'] : '',
			);

			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('search');
			$val->add_callable('Rules');
			$val->add_field('search', 'search', 'max_length[100]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run($data))
			{
				# SE REMPLAZAN ALGUNOS CARACTERES
				$search = str_replace(' ', '+', $val->validated('search'));
				$search = str_replace('*', '', $search);

				# SE ALMACENA LA CADENA DE BUSQUEDA
				$search = ($val->validated('search') != '') ? $search : '';

				# SE REDIRECCIONA A BUSCAR
				Response::redirect('admin/cotizaciones/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/cotizaciones');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/cotizaciones');
		}
	}


	/**
	* INFO
	*
	* MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
    public function action_info($quote_id = 0)
    {
        
        if (!Helper_Permission::can('ventas_cotizaciones', 'view')) {
            Session::set_flash('error', 'No tienes permiso para ver cotizaciones.');
            Response::redirect('admin');
        }

        # Validar ID
        if ($quote_id == 0 || !is_numeric($quote_id)) {
            Response::redirect('admin/cotizaciones');
        }

        # Inicializar variables
        $data = [];
        $bill_flag = false;

        # Buscar la cotización
        $quote = Model_Quote::query()
            ->where('id', $quote_id)
            ->get_one();

       

        # Validar que exista
        if (!empty($quote))
        {
            # Procesar productos
            if (!empty($quote->products)) {
                $products_info = []; // Limpiar el array para evitar duplicados
                foreach ($quote->products as $product) {
                    // ✅ Filtrar aquí: Solo procesar productos no eliminados
                    if ($product->deleted == 0) { 
                        $products_info[] = [
                            'id'            => $product->id,
                            'code'          => $product->product->code ?? '-',
                            'image'         => 'thumb_' . ($product->product->image ?? 'no_image.png'),
                            'name'          => Str::truncate($product->product->name ?? '-', 60, '...'),
                            'name_complete' => $product->product->name ?? '-',
                            'quantity'      => $product->quantity,
                            'discount'      => '% ' . number_format($product->discount, 2, '.', ','),
                            'price'         => '$' . number_format($product->price, 2, '.', ','),
                            'total'         => '$' . number_format($product->total, 2, '.', ',')
                        ];
                    }
                }

                if (empty($products_info)) {
                    $products_info = 'Esta cotización no tiene productos registrados.';
                }
            } else {
                $products_info = 'Esta cotización no tiene productos registrados.';
            }

            # Procesar datos fiscales
            if (!empty($quote->tax_data)) {
                $bill_flag = true;
            }

            if (!empty($quote->seller_asig_id)) {
                // Busca el empleado asignado a la cotización (vendedor asignado)
                $empleado_vendedor = Model_Employee::find($quote->seller_asig_id);
                $data['seller_asig_id'] = $empleado_vendedor ? $empleado_vendedor->name . ' ' . $empleado_vendedor->last_name : '-';
            } else {
                $data['seller_asig_id'] = '-';
            }

            # DEPENDIENDO DEL ESTATUS
				switch($quote->status)
				{
					case 0:
					$statusd = 'Borrador';
					break;
					case 1:
					$statusd = 'Activo';
					break;
					case 2:
					$statusd = 'En proceso';
					break;
                    case 3:
					$statusd = 'Procesado';
					break;
                    case 99:
					$statusd = 'Cancelada';
					break;
				}


            # Armar data para la vista
            $data['id']             = $quote_id;
            $data['partner']        = $quote->partner->name ?? 'Sin nombre';
            $data['email']          = $quote->partner->user->email ?? 'Sin correo';
            $data['reference']      = $quote->reference ?? '-';
            $data['status']         = $quote->status;
            $data['statusd']        = $statusd;
            $data['valid_date']     = ($quote->valid_date) ? date('d/m/Y', $quote->valid_date) : '-';
            $data['comments']       = $quote->comments ?? '-';
            $data['discount']       = '$ ' . number_format($quote->discount, 2, '.', ',');
            $data['subtotal']       = '$ ' . number_format($quote->total, 2, '.', ',');
            $data['iva']            = '$ ' . number_format($quote->total * 0.16, 2, '.', ',');
            $data['total']          = '$ ' . number_format($quote->total * 1.16, 2, '.', ',');
            $data['payment_type']   = $quote->payment->type->name ?? 'Sin tipo';
            $data['products']       = $products_info;
            
            # Dirección
            $data['address_flag'] = (!empty($quote->address));
            if ($data['address_flag']) {
                $data['street']             = $quote->address->street;
                $data['number']             = $quote->address->number;
                $data['internal_number']    = $quote->address->internal_number;
                $data['colony']             = $quote->address->colony;
                $data['zipcode']            = $quote->address->zipcode;
                $data['city']               = $quote->address->city;
                $data['state']              = $quote->address->state->name ?? '-';
                $data['details']            = $quote->address->details;
            }
            
            # Datos fiscales
            $data['bill_flag'] = $bill_flag;
            if ($bill_flag) {
                $data['business_name']              = $quote->tax_data->business_name;
                $data['rfc']                        = $quote->tax_data->rfc;
                $data['tax_data_street']            = $quote->tax_data->street;
                $data['tax_data_number']            = $quote->tax_data->number;
                $data['tax_data_internal_number']   = $quote->tax_data->internal_number;
                $data['tax_data_colony']            = $quote->tax_data->colony;
                $data['tax_data_zipcode']           = $quote->tax_data->zipcode;
                $data['tax_data_city']              = $quote->tax_data->city;
                $data['tax_data_state']             = $quote->tax_data->state->name ?? '-';
                $data['payment_method']             = $quote->tax_data->payment_method->code.' '.$quote->tax_data->payment_method->name;
                $data['cfdi']                       = $quote->tax_data->cfdi->code.' '.$quote->tax_data->cfdi->name;
                $data['sat_tax_regime']             = $quote->tax_data->sat_tax_regime->code.' '.$quote->tax_data->sat_tax_regime->name;
                $data['csf']                        = $quote->tax_data->csf;
            }
        }
        else {
            Response::redirect('admin/cotizaciones');
        }

        # Renderizar vista
        $this->template->title = 'Información de la Cotización';
        $this->template->content = View::forge('admin/cotizaciones/info', $data);
    }

    /**
	* CANCELACION
	*
	* PARA CANCELAR UNA COTIZACION
	*
	* @access  public
	* @return  Void
	*/
    public function action_cancelar($quote_id = 0)
    {
        if (!Helper_Permission::can('ventas_cotizaciones', 'delete')) {
            Session::set_flash('error', 'No tienes permiso para eliminar cotizaciones.');
            Response::redirect('admin/cotizaciones');
        }

        # SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($quote_id == 0 || !is_numeric($quote_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/cotizaciones');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$quote = Model_Quote::query()
		->where('id', $quote_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($quote))
		{
			$quote->status = 99;
            $quote->save();

			{
				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se canceló la cotizaciones correctamente.');
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/cotizaciones');

    }

    /**
	* EN FIRNE
	*
	* PARA PONER EN FIRME UNA COTIZACION
	*
	* @access  public
	* @return  Void
	*/
    public function action_enfirme($quote_id = 0)
    {
        if (!Helper_Permission::can('ventas_cotizaciones', 'edit')) {
            Session::set_flash('error', 'No tienes permiso para editar cotizaciones.');
            Response::redirect('admin/cotizaciones');
        }

        # SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($quote_id == 0 || !is_numeric($quote_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/cotizaciones');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$quote = Model_Quote::query()
		->where('id', $quote_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($quote))
		{
			$quote->status = 1;
            $quote->save();

			{
				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se pone en firme la cotizacion correctamente.');
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/cotizaciones');

    }

	/**
	* EDITAR COTIZACION	*
	* PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
     /**
     * Carga y prepara los datos de una cotización para su edición.
     *
     * @param int $quote_id El ID de la cotización a editar.
     */
   public function action_editar($quote_id = 0)
{
    // 1. VALIDACIÓN DE PERMISOS
    if (!Helper_Permission::can('ventas_cotizaciones', 'edit')) {
        Session::set_flash('error', 'No tienes permiso para editar cotizaciones.');
        Response::redirect('admin/cotizaciones');
    }

    // 2. VALIDACIÓN DEL ID
    if ($quote_id == 0 || !is_numeric($quote_id)) {
        Session::set_flash('error', 'ID de cotización inválido.');
        Response::redirect('admin/cotizaciones');
    }

    // === LOG DEBUG ===
    \Log::debug("[Editar Cotización] Cargando cotización con ID: {$quote_id}");

    // 3. OBTENER LA COTIZACIÓN Y SUS RELACIONES
    $quote = Model_Quote::query()
        ->where('id', $quote_id)
        ->related(['partner', 'products' => ['product'], 'quotes_address'])
        ->where('products.deleted', 0)
        ->get_one();

    if (!$quote) {
        Session::set_flash('error', 'Cotización no encontrada.');
        Response::redirect('admin/cotizaciones');
    }

    // 4. OBTENER CONTACTOS DEL SOCIO
    $partner_contacts = Model_Partners_Contact::query()
        ->where('partner_id', $quote->partner_id)
        ->get();

    // 5. PREPARAR DATOS PARA LA VISTA VUE
    $cotizacionEdicion = [
        'id'                 => (int)$quote->id,
        'partner_id'         => (int)$quote->partner_id,
        'partner_contact_id' => (int)($quote->partner_contact_id ?? 0),
        'reference'          => $quote->reference,
        'valid_date'         => date('Y-m-d', $quote->valid_date),
        'address_id'         => (int)($quote->address_id ?? 0),
        'payment_id'         => (int)($quote->payment_id ?? 0),
        'employee_id'        => (int)($quote->employee_id ?? 0),
        'seller_asig_id'     => (int)($quote->seller_asig_id ?? 0),
        'comments'           => $quote->comments ?? '',
        'tax_general'        => (int)($quote->tax_general ?? 0),
        'retention_general'  => (int)($quote->retention_general ?? 0),
        'moneda_id'          => (int)($quote->moneda_id ?? 0),
        'discount_general_id'=> (int)($quote->discount_general_id ?? 0),
    ];

    // 5.5. CARGAR CATÁLOGO DE EMPLEADOS (AGREGAR ESTE BLOQUE)

    // === NUEVO BLOQUE: EMPLEADO QUE CAPTURÓ LA COTIZACIÓN ===
    if ($quote->employee_id) {
        $employee = Model_Employee::find($quote->employee_id);
        if ($employee) {
            $cotizacionEdicion['employee'] = [
                'id'   => $employee->id,
                'name' => $employee->name
            ];
        }
    }

    // Vendedor asignado
    if ($quote->seller_asig_id) {
        $seller = Model_Employee::find($quote->seller_asig_id);
        if ($seller) {
            $cotizacionEdicion['seller_asig'] = [
                'id'   => $seller->id,
                'name' => $seller->name
            ];
        }
    }

    $employees = Model_Employee::query()
        ->order_by('name', 'asc')
        ->get();



    // Partner info
    if ($quote->partner) {
        $cotizacionEdicion['partner'] = $quote->partner->to_array();
    }

    // Contactos
    $cotizacionEdicion['partner_contacts'] = [];
    foreach ($partner_contacts as $contact) {
        $cotizacionEdicion['partner_contacts'][] = $contact->to_array();
    }

    // Productos
    $cotizacionEdicion['partidas'] = [];
    foreach ($quote->products as $p) {
        $cotizacionEdicion['partidas'][] = [
            'quote_product_id' => $p->id,               // ID en quotes_products (partida)
            'product_id'       => (int)$p->product_id, // ID real del producto
            'code'             => $p->product->code ?? '',
            'name'             => $p->product->name ?? 'Sin nombre',
            'quantity'         => (int)$p->quantity,
            'unit_price'       => (float)$p->price,
            'discount'         => (float)($p->discount ?? 0),
            'image'            => $p->product->image ?? 'no_image.png',
            'tax_id'           => (int)($p->tax_id ?? 0),
            'retention_id'     => (int)($p->retention_id ?? 0),
        ];
    }


    // =========================================================
    // >>> NUEVA LÍNEA: AÑADE EL OBJETO quotes_address SI EXISTE
    // =========================================================
    if ($quote->quotes_address) {
        $cotizacionEdicion['quotes_address'] = $quote->quotes_address->to_array();
    }


   // 6. PREPARAR DATOS FINALES PARA LA VISTA
    $data = [
        'quote_id'          => $quote_id,
        'usuario_id'        => Auth::get('id'),
        'modoEdicion'       => true,
        
        // ✅ CORRECCIÓN: Usamos la clave 'cotizacionEdicion' para que coincida con la vista
        'cotizacionEdicion' => $cotizacionEdicion,
    
        
        'tax_options'       => Model_Tax::find('all'),
        'retention_options' => Model_Retention::find('all'),
        'employees'         => array_map(function ($emp) {
            return [
                'id'   => $emp->id,
                'name' => $emp->name,
            ];
        }, $employees),
        
    ];


    // === LOG DEBUG ===
    \Log::debug("[Editar Cotización] Datos cargados correctamente para ID: {$quote_id}");

    // 7. CARGAR LA VISTA
    $this->template->title   = 'Editar cotización';
    $this->template->content = View::forge('admin/cotizaciones/editar', $data);
}



	/**
	 * REENVIAR CORRE DE VENTA
	 *
	 * REENVIA EL CORREO DE LA VENTA
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_reenviar_correo($quote_id = 0)
	{
        
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($quote_id == 0 || !is_numeric($quote_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/cotizaciones');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$quote = Model_Quote::query()
		->where('id', $quote_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($quote))
		{
				# SE ENVIA EL CORREO AL USUARIO
				$this->send_user_mail($quote->id);

				# SE ENVIA EL CORREO AL ADMINISTRADOR
				$this->send_admin_mail($quote->id);

				# SE ENVIA EL CORREO AL DESARROLLADOR
				$this->send_dev_mail($quote->id, 'Reenvio de correro', 'Realizado');

				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se reenvia a solicitud del cliente la venta No. <b>'.$quote->id.'</b> para su información');
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/cotizaciones/info/'.$quote_id);
	}

	/**
	 * AGREGAR COTIZACION AL CLIENTE
	 *
	 * PARA AGREGAR UNA VENTA AL CLIENTE
	 *
	 * @access  private
     * @return  Boolean
	 */
	public function action_agregar_cotizacion()
{
    // Permiso para crear cotizaciones
    if (!Helper_Permission::can('ventas_cotizaciones', 'create')) {
        Session::set_flash('error', 'No tienes permiso para crear cotizaciones.');
        Response::redirect('admin/cotizaciones');
    }

    // No necesitas cargar grandes catálogos, solo el id del empleado actual si lo deseas
    $user_id = Auth::get('id');
    $logged_employee = Model_Employee::query()
        ->where('user_id', $user_id)
        ->where('deleted', 0)
        ->get_one();
    $default_seller_id = $logged_employee ? $logged_employee->id : 0;

    // Pasa solo los datos estrictamente necesarios
    $data = [
        'default_seller_id' => $default_seller_id,
        // Puedes pasar otros valores por default que sí necesites en la vista
    ];

    $this->template->title = 'Agregar cotización';
    $this->template->content = View::forge('admin/cotizaciones/agregar_cotizacion', $data);
}



    /**
	 * AGREGAR CONTACTO AL CLIENTE
	 *
	 * PARA AGREGAR UNA VENTA AL CLIENTE
	 *
	 * @access  private
     * @return  Boolean
	 */
    public function action_get_contact_modal()
    {
        $msg  = 'error';
        $html = '';

        if (Input::is_ajax()) {
            $val = Validation::forge();
            $val->add_field('access_id', 'ID', 'required|valid_string[numeric]|numeric_min[1]');
            $val->add_field('access_token', 'Token', 'required|min_length[1]');
            $val->add_field('partner_id', 'Partner', 'required|valid_string[numeric]|numeric_min[1]');

            if ($val->run()) {
                $user = Model_User::find($val->validated('access_id'));
                if ($user && md5($user->login_hash) === $val->validated('access_token')) {
                    $partner = Model_Partner::find($val->validated('partner_id'));

                    if ($partner) {
                        $html = View::forge('admin/socios/contacto_modal', ['partner' => $partner])->render();
                        $msg = 'ok';
                    } else {
                        $msg = 'Socio no encontrado.';
                    }
                } else {
                    $msg = 'Credenciales inválidas.';
                }
            } else {
                $msg = 'Validación fallida.';
            }
        } else {
            $msg = 'La petición no es AJAX.';
        }

        return $this->response([
            'msg'  => $msg,
            'html' => $html
        ]);
    }


    /**
     * IMPRIMIR
     *
     * Muestra la cotización en formato imprimible con opción a imprimir o guardar como PDF
     *
     * @param int $id ID de la cotización
     * @return Response
     */
    public function action_imprimir($id = null)
    {
        if (!Helper_Permission::can('ventas_cotizaciones', 'view')) {
            Session::set_flash('error', 'No tienes permiso para imprimir cotizaciones.');
            Response::redirect('admin');
        }

        is_null($id) and \Response::redirect('admin/cotizaciones');

        $quote = \Model_Quote::find($id, [
            'related' => ['partner', 'products', 'products.product', 'employee', 'contact', 'address', 'payment']
        ]);

        // Filtrar los productos eliminados manualmente
        if ($quote && !empty($quote->products)) {
            $quote->products = array_filter($quote->products, function($p) {
                return empty($p->deleted) || $p->deleted == 0;
            });
        }


        if (!$quote) {
            \Session::set_flash('error', 'Cotización no encontrada.');
            \Response::redirect('admin/cotizaciones');
        }

        $data['quote'] = $quote;

        return \Response::forge(\View::forge('admin/cotizaciones/imprimir', $data));
    }

      /**
     * DESCARGAR PDF
     *
     * Genera la cotización como un archivo PDF y fuerza su descarga.
     *
     * @param int $id ID de la cotización
     * @return Response (o simplemente exit())
     */
     public function action_descargar_pdf($id = null)
    {
        // 1. Verificación de Permisos (sin cambios)
        if (!Helper_Permission::can('ventas_cotizaciones', 'view')) {
            \Session::set_flash('error', 'No tienes permiso para descargar PDFs de cotizaciones.');
            \Response::redirect('admin');
        }

        // 2. Redirección si no hay ID (sin cambios)
        is_null($id) and \Response::redirect('admin/cotizaciones');

        // 3. Carga de la Cotización y Relaciones (sin cambios)
        $quote = \Model_Quote::find($id, [
            'related' => ['partner', 'products', 'products.product', 'employee', 'contact', 'address', 'payment']
        ]);

        // Filtrar los productos eliminados manualmente
        if ($quote && !empty($quote->products)) {
            $quote->products = array_filter($quote->products, function($p) {
                return empty($p->deleted) || $p->deleted == 0;
            });
        }

        // 4. Verificación de Cotización Existente (sin cambios)
        if (!$quote) {
            \Session::set_flash('error', 'Cotización no encontrada.');
            \Response::redirect('admin/cotizaciones');
        }

        // 5. Preparación de Datos para la Vista
        $data['quote'] = $quote;
        // ***** AÑADE ESTA LÍNEA CLAVE *****
        $data['is_pdf_export'] = true; // Indicamos a la vista que estamos generando un PDF
        // **********************************

        // 6. OBTENER EL CONTENIDO HTML DE LA VISTA
        // La vista 'admin/cotizaciones/imprimir' ahora usará $is_pdf_export
        $html = \View::forge('admin/cotizaciones/imprimir', $data)->render();

        // 7. INSTANCIAR DOMPDF (sin cambios)
        $dompdf = new \Dompdf\Dompdf();

        // 8. CONFIGURAR OPCIONES (sin cambios)
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true); // CRUCIAL para imágenes con \Uri::base()
        $options->set('defaultFont', 'sans-serif');
        $dompdf->setOptions($options);

        // 9. CARGAR EL HTML EN DOMPDF (se carga el HTML ya sin botones)
        $dompdf->loadHtml($html);

        // 10. RENDERIZAR EL HTML A PDF (sin cambios, con try-catch para depuración)
        $dompdf->setPaper('A4', 'portrait');
        try {
            $dompdf->render();
        } catch (\Exception $e) {
            \Log::error('Dompdf render error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            \Session::set_flash('error', 'Ocurrió un error al generar el PDF. Por favor, contacta a soporte.');
            \Response::redirect('admin/cotizaciones');
        }

        // 11. SALIDA DEL PDF AL NAVEGADOR PARA DESCARGA (sin cambios)
        $filename = 'cotizacion_' . $quote->id . '.pdf';
        $dompdf->stream($filename, ["Attachment" => 1]);

        // 12. Finalizar ejecución (sin cambios)
        exit();
    }

    public function action_test_ajax()
{
    return View::forge('admin/cotizaciones/test_ajax');
}



	/**
     * SEND USER MAIL
     *
     * ENVIA POR EMAIL UN MENSAJE DEL PEDIDO AL USUARIO
     *
     * @access  private
     * @return  Boolean
     */
    private function send_quote_mail($quote_id = 0)
    {
        # INICIALIZAR VARIABLES
        $data          = array();
        $address_html  = '';
        $products_html = '';
        $totals_html   = '';

        # BUSCAR INFORMACIÓN DE LA COTIZACIÓN
        $quote = Model_Quote::query()
            ->related('products')
            ->related('partner')
            ->related('tax_data')
            ->where('id', $quote_id)
            ->get_one();

        # SI SE OBTIENE INFORMACIÓN
        if (!empty($quote))
        {
            # RECORRER PRODUCTOS
            foreach ($quote->products as $product)
            {
                $imagePath = 'thumb_' . $product->product->image;
                $imageSrc = file_exists(DOCROOT . $imagePath) ? Asset::img($imagePath, array('alt' => $product->product->name)) : Asset::img('thumb_no_image.png', array('alt' => 'No Imagen'));

                $products_html .= $imageSrc . '
                <strong style="display: block; margin-bottom: 15px">' . $product->product->name . '</strong>
                <strong style="display: block;">Precio unitario:</strong>
                <span style="display: block; margin-bottom: 15px; color: #ee3530">$' . number_format($product->price, 2, '.', ',') . '</span>
                <strong style="display: block;">Cantidad:</strong>
                <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $product->quantity . '</span>
                <strong style="display: block;">Total:</strong>
                <span style="display: block; margin-bottom: 15px; color: #ee3530">$' . number_format($product->total, 2, '.', ',') . '</span>
                ';
            }

            # SI EXISTE DIRECCIÓN DE ENTREGA
            if ($quote->address_id != 0)
            {
                $address_html .= '<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Dirección de entrega</h1>
                <p>
                    <strong style="display: block;">Calle:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->street . '</span>
                    <strong style="display: block;">Número:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->number . '</span>
                    <strong style="display: block;">Número interior:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->internal_number . '</span>
                    <strong style="display: block;">Colonia:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->colony . '</span>
                    <strong style="display: block;">Código Postal:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->zipcode . '</span>
                    <strong style="display: block;">Ciudad:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->city . '</span>
                    <strong style="display: block;">Estado:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->state->name . '</span>
                    <strong style="display: block;">Detalles:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->details . '</span>
                    <hr>
                </p>';
            }

            # TOTAL DE LA COTIZACIÓN
            $totals_html .= '<strong style="display: block;">Total:</strong>
            <span style="display: block; margin-bottom: 15px; color: #ee3530">$' . number_format($quote->total, 2, '.', ',') . '</span>';

            # CREAR BODY DEL EMAIL
            $data['body'] = '
            <tr>
                <td style="background-color: #ffffff;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                                <h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">¡Gracias por solicitar una cotización en Distribuidora Sajor!</h1>
                                <p style="margin: 0;">Este mensaje ha sido enviado automáticamente desde la página web de <strong>Distribuidora Sajor</strong>, para confirmar tu cotización, a continuación te dejamos la información registrada:</p>
                                <p>
                                    <strong style="display: block;">Nombre:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->partner->name . ' ' . $quote->partner->last_name . '</span>
                                    <strong style="display: block;">Correo electrónico:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->partner->user->email . '</span>
                                    <strong style="display: block;">Cotización:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">#' . $quote->id . '</span>
                                    ' . $totals_html . '
                                    <hr>
                                </p>
                                ' . $address_html . '
                                <h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Productos cotizados</h1>
                                <p>' . $products_html . '</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>';

            # ENVIAR EMAIL
            $email = Email::forge();
            $email->from('cotizaciones@sajor.com.mx', 'Distribuidora Sajor');
            $email->reply_to('cotizaciones@sajor.com.mx', 'Distribuidora Sajor');
            $email->to([
                $quote->partner->user->email => $quote->partner->name . ' ' . $quote->partner->last_name,
                //'ventas@sajor.mx' => 'Distribuidora Sajor',
            ]);
            $email->subject('Distribuidora Sajor - Cotización #' . $quote->id);
            $email->html_body(View::forge('email_templates/default', $data, false), false);

            try
            {
                if ($email->send())
                {
                    return true;
                }
            }
            catch (\EmailSendingFailedException $e)
            {
                return false;
            }
            catch (\EmailValidationFailedException $e)
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }



	/**
     * SEND ADMIN MAIL
     *
     * ENVIA POR EMAIL UN MENSAJE DEL PEDIDO AL ADMINISTRADOR
     *
     * @access  private
     * @return  Boolean
     */
    private function send_quote_admin_mail($quote_id = 0)
    {
        # INICIALIZAR VARIABLES
        $data          = array();
        $address_html  = '';
        $products_html = '';
        $totals_html   = '';

        # BUSCAR INFORMACIÓN DE LA COTIZACIÓN
        $quote = Model_Quote::query()
            ->related('products')
            ->related('partner')
            ->related('tax_data')
            ->where('id', $quote_id)
            ->get_one();

        # SI SE OBTIENE INFORMACIÓN
        if (!empty($quote))
        {
            # RECORRER PRODUCTOS
            foreach ($quote->products as $product)
            {
                $imagePath = 'thumb_' . $product->product->image;
                $imageSrc = file_exists(DOCROOT . $imagePath) ? Asset::img($imagePath, array('alt' => $product->product->name)) : Asset::img('thumb_no_image.png', array('alt' => 'No Imagen'));

                $products_html .= $imageSrc . '
                <strong style="display: block; margin-bottom: 15px">' . $product->product->name . '</strong>
                <strong style="display: block;">Precio unitario:</strong>
                <span style="display: block; margin-bottom: 15px; color: #ee3530">$' . number_format($product->price, 2, '.', ',') . '</span>
                <strong style="display: block;">Cantidad:</strong>
                <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $product->quantity . '</span>
                <strong style="display: block;">Total:</strong>
                <span style="display: block; margin-bottom: 15px; color: #ee3530">$' . number_format($product->total, 2, '.', ',') . '</span>
                ';
            }

            # DIRECCIÓN DE ENTREGA
            if ($quote->address_id != 0)
            {
                $address_html .= '<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Dirección de entrega</h1>
                <p>
                    <strong style="display: block;">Calle:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->street . '</span>
                    <strong style="display: block;">Número:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->number . '</span>
                    <strong style="display: block;">Número interior:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->internal_number . '</span>
                    <strong style="display: block;">Colonia:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->colony . '</span>
                    <strong style="display: block;">Código Postal:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->zipcode . '</span>
                    <strong style="display: block;">Ciudad:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->city . '</span>
                    <strong style="display: block;">Estado:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->state->name . '</span>
                    <strong style="display: block;">Detalles:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->details . '</span>
                    <hr>
                </p>';
            }

            # TOTAL
            $totals_html .= '<strong style="display: block;">Total:</strong>
            <span style="display: block; margin-bottom: 15px; color: #ee3530">$' . number_format($quote->total, 2, '.', ',') . '</span>';

            # CREAR BODY DEL EMAIL
            $data['body'] = '
            <tr>
                <td style="background-color: #ffffff;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                                <h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Nueva cotización solicitada.</h1>
                                <p style="margin: 0;">Este mensaje ha sido enviado desde la página web de <strong>Distribuidora Sajor</strong> con la siguiente información:</p>
                                <p>
                                    <strong style="display: block;">Nombre:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->partner->name . ' ' . $quote->partner->last_name . '</span>
                                    <strong style="display: block;">Correo electrónico:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->partner->user->email . '</span>
                                    <strong style="display: block;">Cotización:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">#' . $quote->id . '</span>
                                    ' . $totals_html . '
                                    <hr>
                                </p>
                                ' . $address_html . '
                                <h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Productos cotizados</h1>
                                <p>' . $products_html . '</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>';

            # ENVIAR EMAIL
            $email = Email::forge();
            $email->from('cotizaciones@sajor.com.mx', 'Distribuidora Sajor');
            $email->reply_to($quote->partner->user->email, $quote->partner->name . ' ' . $quote->partner->last_name);
            $email->to([
                'sistemas@sajor.mx' => 'Distribuidora Sajor',
                //'ventas@sajor.mx' => 'Distribuidora Sajor',
            ]);
            $email->subject('Distribuidora Sajor - Nueva Cotización #' . $quote->id);
            $email->html_body(View::forge('email_templates/default', $data, false), false);

            try
            {
                if ($email->send())
                {
                    return true;
                }
            }
            catch (\EmailSendingFailedException $e)
            {
                return false;
            }
            catch (\EmailValidationFailedException $e)
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }



    /**
     * SEND PARTNER MAIL
     *
     * ENVIA POR EMAIL UN MENSAJE DEL PEDIDO AL SOCIO
     *
     * @access  private
     * @return  Boolean
     */	
    private function send_quote_notification_user_mail($quote_id = 0)
    {
        # INICIALIZAR VARIABLES
        $data          = array();
        $address_html  = '';
        $products_html = '';
        $totals_html   = '';

        # BUSCAR INFORMACIÓN DE LA COTIZACIÓN
        $quote = Model_Quote::query()
            ->related('products')
            ->related('partner')
            ->related('tax_data')
            ->related('address')
            ->where('id', $quote_id)
            ->get_one();

        # SI SE OBTIENE INFORMACIÓN
        if (!empty($quote))
        {
            # RECORRER PRODUCTOS
            foreach ($quote->products as $product)
            {
                $imagePath = 'thumb_' . $product->product->image;
                $imageSrc = file_exists(DOCROOT . $imagePath) ? Asset::img($imagePath, array('alt' => $product->product->name)) : Asset::img('thumb_no_image.png', array('alt' => 'No Imagen'));

                $products_html .= $imageSrc . '
                <strong style="display: block; margin-bottom: 15px">' . $product->product->name . '</strong>
                <strong style="display: block;">Precio unitario:</strong>
                <span style="display: block; margin-bottom: 15px; color: #ee3530">$' . number_format($product->price, 2, '.', ',') . '</span>
                <strong style="display: block;">Cantidad:</strong>
                <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $product->quantity . '</span>
                <strong style="display: block;">Total:</strong>
                <span style="display: block; margin-bottom: 15px; color: #ee3530">$' . number_format($product->total, 2, '.', ',') . '</span>
                ';
            }

            # DIRECCIÓN DE ENTREGA
            if ($quote->address_id != 0)
            {
                $address_html .= '<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Dirección de entrega</h1>
                <p>
                    <strong style="display: block;">Calle:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->street . '</span>
                    <strong style="display: block;">Número:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->number . '</span>
                    <strong style="display: block;">Número interior:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->internal_number . '</span>
                    <strong style="display: block;">Colonia:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->colony . '</span>
                    <strong style="display: block;">Código Postal:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->zipcode . '</span>
                    <strong style="display: block;">Ciudad:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->city . '</span>
                    <strong style="display: block;">Estado:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->state->name . '</span>
                    <strong style="display: block;">Detalles:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->address->details . '</span>
                    <hr>
                </p>';
            }

            # TOTAL
            $totals_html .= '<strong style="display: block;">Total:</strong>
            <span style="display: block; margin-bottom: 15px; color: #ee3530">$' . number_format($quote->total, 2, '.', ',') . '</span>';

            # CREAR BODY DEL EMAIL
            $data['body'] = '
            <tr>
                <td style="background-color: #ffffff;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                                <h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Actualización de tu cotización</h1>
                                <p style="margin: 0;">Este mensaje ha sido enviado automáticamente desde la página web de <strong>Distribuidora Sajor</strong> para informarte de una actualización en tu cotización. También puedes consultar los cambios en tu portal de <a href="https://www.sajor.com.mx/mi-cuenta">Mi-Cuenta</a>.</p>
                                <p>
                                    <strong style="display: block;">Nombre:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->partner->name . ' ' . $quote->partner->last_name . '</span>
                                    <strong style="display: block;">Correo electrónico:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">' . $quote->partner->user->email . '</span>
                                    <strong style="display: block;">Cotización:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">#' . $quote->id . '</span>
                                    ' . $totals_html . '
                                    <hr>
                                </p>
                                ' . $address_html . '
                                <h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Productos cotizados</h1>
                                <p>' . $products_html . '</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>';

            # ENVIAR EMAIL
            $email = Email::forge();
            $email->from('cotizaciones@sajor.com.mx', 'Distribuidora Sajor');
            $email->reply_to('cotizaciones@sajor.com.mx', 'Distribuidora Sajor');
            $email->to([
                $quote->partner->user->email => $quote->partner->name . ' ' . $quote->partner->last_name,
            ]);
            $email->subject('Distribuidora Sajor - Actualización de Cotización #' . $quote->id);
            $email->html_body(View::forge('email_templates/default', $data, false), false);

            try
            {
                if ($email->send())
                {
                    return true;
                }
            }
            catch (\EmailSendingFailedException $e)
            {
                return false;
            }
            catch (\EmailValidationFailedException $e)
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }



}
