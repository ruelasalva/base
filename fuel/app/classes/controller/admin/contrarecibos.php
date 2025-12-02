<?php

/**
 * CONTROLADOR ADMIN_COMPRAS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Contrarecibos extends Controller_Admin
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
		if(!Auth::member(100))
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			Session::set_flash('error', 'No tienes los permisos para acceder a esta sección.');

			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin');
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
        # Verificar si el usuario es administrador
        if (!Auth::check() || Auth::get('group') < 50) {
            Session::set_flash('error', 'No tienes permisos para acceder a esta sección.');
            Response::redirect('admin');
        }

        # SE INICIALIZAN LAS VARIABLES
		$data        = array();
		$contrarecibos_info = array();
		$per_page    = 100;

        # Obtener todos los contrarecibos generados
        $contrarecibos = Model_Providers_Receipt::query();


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
			$contrarecibos = $contrarecibos->where(DB::expr("CONCAT(`t0`.`name`)"), 'like', '%'.$search.'%');
		}

        # SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $contrarecibos->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('contrarecibos', $config);

        # SE EJECUTA EL QUERY
		$contrarecibos = $contrarecibos->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

        # SI NO HAY RESULTADOS
        if (!empty($contrarecibos)) 
        {
        
            # SE RECORREN LOS CONTRARECIBOS
        foreach ($contrarecibos as $receipt) 
        {
            #SE ALMACENA LA INFORMACIÓN DE CADA CONTRARECIBO
            $contrarecibos_info[] = array(
                'id'           => $receipt->id,
                'uuid'         => $receipt->uuid,
                'provider'     => $receipt->provider->name,
                'total'        => '$' . number_format($receipt->total, 2),
                'status'       => $receipt->status,
                'payment_date' => ($receipt->payment_date) ? date('d/m/Y', strtotime($receipt->payment_date)) : 'Pendiente',
                'created_at'   => date('d/m/Y H:i', strtotime($receipt->created_at)),
        );
         }
        }

        #SE ALMACENA LA INFORMACIÓN DE LOS CONTRARECIBOS
        $data['contrarecibos']  = $contrarecibos_info;
        $data['pagination']     = $pagination->render();
        $data['search']         =  str_replace('%', ' ', $search);

        # Cargar vista
        $this->template->title = 'Contrarecibos de Facturas';
        $this->template->content = View::forge('admin/compras/contrarecibos/index', $data, false);
    }


    /**
     * OBTENER ESTADO DE LA FACTURA
     *
     * Esta función recibe el estado de la factura y devuelve una cadena descriptiva.
     *
     * @param int $status El estado de la factura (0: Pendiente, 1: En revisión, 2: Pagada, 3: Cancelada)
     * @return string Descripción del estado de la factura
     */
    # FUNCIÓN PARA OBTENER EL ESTADO DE LA FACTURA
    private function get_factura_status($status)
    {
        switch ($status) {
            case 0:
                return 'Pendiente';
            case 1:
                return 'En revisión';
            case 2:
                return 'Pagada';
            case 3:
                return 'Cancelada';
            default:
                return 'Desconocido';
        }
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
				Response::redirect('admin/compras/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/compras');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/compras');
		}
	}

	
	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACIÓN DE UNA FACTURA DE PROVEEDOR
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($factura_id = 0)
    {
        # VERIFICAR SI EL USUARIO ESTÁ AUTENTICADO Y ES ADMINISTRADOR
        if (!Auth::check() || Auth::get('group') < 50) {
            Session::set_flash('error', 'No tienes permisos para acceder a esta sección.');
            Response::redirect('admin/compras');
        }

        # SE INICIALIZAN VARIABLES
        $data    = array();
        $classes = array();
        $fields  = array('status','message', 'payment_date');
        $errors  = array();

        foreach ($fields as $field) {
            $classes[$field] = array('form-group' => null, 'form-control' => null);
        }

        # OBTENER LA FACTURA Y SU PROVEEDOR
        $factura = Model_Providers_Bill::query()
            ->where('id', $factura_id)
            ->related('provider') # Relacionar con la tabla de proveedores
            ->get_one();

        # DEFINIR ESTADOS Y COLORES
        $status_labels = [
            '0' => 'Pendiente',
            '1' => 'En revisión',
            '2' => 'Pagada',
            '3' => 'Cancelada'
        ];

        $status_colors = [
            '0' => 'warning',  # Amarillo
            '1' => 'info',     # Azul
            '2' => 'success',  # Verde
            '3' => 'danger'    # Rojo
        ];

        # VALIDAR SI LA FACTURA EXISTE
        if (!$factura) {
            Session::set_flash('error', 'La factura no existe o fue eliminada.');
            Response::redirect('admin/compras');
        }

        # OBTENER DATOS DEL PROVEEDOR
        $provider_name = isset($factura->provider) ? $factura->provider->name : 'Desconocido';

        # DESERIALIZAR DATOS DEL XML
        $invoice_data = !empty($factura->invoice_data) ? unserialize($factura->invoice_data) : [];

        # SI SE ENVÍA EL FORMULARIO PARA ACTUALIZAR EL ESTADO
        if (Input::method() == 'POST') {
            try {
                # OBTENER EL NUEVO ESTADO
                $new_status     = Input::post('status');
                $message        = Input::post('message', ''); # Campo opcional
                $payment_date   = Input::post('payment_date', null); # Campo opcional

                # VALIDAR QUE EL ESTADO NO ESTÉ VACÍO Y SEA UN VALOR VÁLIDO
                if (!is_numeric($new_status) || $new_status < 0 || $new_status > 3) {
                    $errors['status'] = 'Selecciona un estado válido.';
                    $classes['status']['form-group'] = 'has-danger';
                    $classes['status']['form-control'] = 'is-invalid';
                }

                # VALIDAR FORMATO DE FECHA SI SE LLENÓ
                if (!empty($payment_date) && !strtotime($payment_date)) {
                    $errors['payment_date'] = 'El formato de fecha es inválido.';
                    $classes['payment_date']['form-group'] = 'has-danger';
                    $classes['payment_date']['form-control'] = 'is-invalid';
                }

                # SI HAY ERRORES, MOSTRARLOS EN LA VISTA
                if (!empty($errors)) {
                    $data['errors'] = $errors;
                    $data['classes'] = $classes;
                } else {
                    # ACTUALIZAR EL ESTADO DE LA FACTURA
                    $factura->status = $new_status;
                    $factura->message      = !empty($message) ? $message : null;

                    # SOLO ACTUALIZAR FECHA SI SE SELECCIONA UNA
                    if (!empty($payment_date)) {
                        $factura->payment_date = strtotime($payment_date);
                    }
                    
                    if (!$factura->save()) {
                        throw new Exception('Hubo un problema al actualizar el estado.');
                    }

                    \Log::debug("✅ Estado de la factura ID {$factura->id} actualizado a '{$new_status}'.");

                    # MENSAJE DE ÉXITO
                    Session::set_flash('success', 'Estado actualizado correctamente.');
                    Response::redirect('admin/compras/info/' . $factura_id);
                }
            } catch (Exception $e) {
                \Log::error('Error al actualizar estado: ' . $e->getMessage());
                Session::set_flash('error', $e->getMessage());
            }
        }

        # SE INICIALIZA EL ARRAY DE PRODUCTOS Y SE VALIDA CADA CLAVE
        $productos = [];
        if (!empty($invoice_data['productos'])) {
            foreach ($invoice_data['productos'] as $producto) {
                $productos[] = [
                    'noidentificacion'      => isset($producto['noidentificacion']) ? $producto['noidentificacion'] : 'N/A',
                    'descripcion'           => isset($producto['descripcion']) ? $producto['descripcion'] : 'N/A',
                    'cantidad'              => isset($producto['cantidad']) ? $producto['cantidad'] : 'N/A',
                    'clave_unidad'          => isset($producto['clave_unidad']) ? $producto['clave_unidad'] : 'N/A',
                    'valor_unitarioo'       => isset($producto['valor_unitario']) ? number_format($producto['valor_unitario'], 2) : '0.00',
                    'importe'               => isset($producto['importe']) ? number_format($producto['importe'], 2) : '0.00',
                ];
            }
        }

        # OBTENER EL ESTADO Y EL COLOR
        $estado_actual = isset($status_labels[$factura->status]) ? $status_labels[$factura->status] : 'Desconocido';
        $badge_color = isset($status_colors[$factura->status]) ? $status_colors[$factura->status] : 'secondary';


        # PASAR DATOS A LA VISTA
        $data['estado_actual'] = $estado_actual; 
        $data['badge_color']   = $badge_color;   
        $data['factura']       = $factura;
        $data['provider_name'] = $provider_name;
        $data['invoice_data']  = $invoice_data;
        $data['productos']     = $productos;
        $data['classes']       = $classes;
        $data['errors']        = $errors;

        # CARGAR VISTA
        $this->template->title = 'Información de la Factura';
        $this->template->content = View::forge('admin/compras/facturas/info', $data);
    }


    /**
	 * INFO
	 *
	 * MUESTRA LA INFORMACIÓN DE UNA FACTURA DE PROVEEDOR
	 *
	 * @access  public
	 * @return  Void
	 */
    public function action_eliminar($factura_id)
    {
        # VERIFICAR SI EL USUARIO ESTÁ AUTENTICADO Y TIENE PERMISOS DE ADMINISTRADOR
        if (!Auth::check() || Auth::get('group') < 50) {
            Session::set_flash('error', 'No tienes permisos para realizar esta acción.');
            Response::redirect('admin/compras');
        }

        # OBTENER LA FACTURA
        $factura = Model_Providers_Bill::query()
            ->where('id', $factura_id)
            ->get_one();

        # VALIDAR SI LA FACTURA EXISTE
        if (!$factura) {
            Session::set_flash('error', 'La factura no existe o ya fue eliminada.');
            Response::redirect('admin/compras');
        }

        try {
            # ACTUALIZAR EL ESTADO A CANCELADA (3) Y MARCAR COMO ELIMINADA (deleted = 1)
            $factura->status = 3;
            $factura->deleted = 1;

            if (!$factura->save()) {
                throw new Exception('Error al eliminar la factura.');
            }

            # REGISTRAR EN LOGS
            \Log::info("🗑️ Factura ID {$factura->id} marcada como eliminada.");

            # MENSAJE DE ÉXITO
            Session::set_flash('success', 'Factura eliminada correctamente.');
            Response::redirect('admin/compras');

        } catch (Exception $e) {
            \Log::error('Error al eliminar factura: ' . $e->getMessage());
            Session::set_flash('error', 'Hubo un problema al eliminar la factura.');
            Response::redirect('admin/compras/info/' . $factura_id);
        }
    }






	


}
