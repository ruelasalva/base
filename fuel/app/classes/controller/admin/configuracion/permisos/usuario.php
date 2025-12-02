<?php

/**
* CONTROLADOR PERMISOS POR USUARIO
*
* @package  app
* @extends  Controller_Admin
*/
class Controller_Admin_Configuracion_Permisos_Usuario extends Controller_Admin
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
		if(!Auth::member(100) && !Auth::member(50))
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
     * MUESTRA LA CONFIGURACIÓN GENERAL SI EXISTE
     *
     * @access  public
     * @return  Void
     */
    public function action_index($search = '')
    {
        $data      = [];
        $per_page  = 50;
            $modules = [

            // Principales
            'dashboard'      => ['name' => 'Dashboard'],
            'sala_juntas'    => ['name' => 'Sala de Juntas'],

            // Bloque: Gestión
            'gestion' => [
                'name' => 'Gestión',
                'children' => [
                    'slides' => 'Slides',
                    'banners_productos' => 'Banners Productos',
                    'banners_laterales' => 'Banners Laterales',
                    'blog_categorias'   => 'Blog Categorías',
                    'blog_etiquetas'    => 'Blog Etiquetas',
                    'blog_publicacion'  => 'Blog Publicaciones',
                    'editor_diseno'     => 'Editor de Diseño',
                    'legal_documentos'  => 'Documentos Legales',
                    'legal_consents'    => 'Consentimientos',
                    'legal_cookies'     => 'Cookies',
                    'apariencia_footer'     => 'Apariencia Footer',
                    'apariencia_header'     => 'Apariencia Header',
                ]
            ],

            // Bloque: Plataformas
            'plataformas' => [
                'name' => 'Plataformas',
                'children' => [
                    'plataformas_ml'         => 'Mercado Libre',
                    'plataformas_amazon'     => 'Amazon',
                    'plataformas_walmart'    => 'Walmart',
                    'plataformas_tiktok'     => 'TikTok Shop',
                    'plataformas_shopify'    => 'Shopify',
                    'plataformas_temu'       => 'Temu',
                    'plataformas_aliexpress' => 'AliExpress',
                    'plataformas_logs'       => 'Logs de Sincronización',
                    'plataformas_errores'    => 'Errores',
                ],
            ],


            // Bloque: Catálogo de Productos
            'catalogo_productos' => [
                'name' => 'Catálogo de Productos',
                'children' => [
                    'catalogo_productos'     => 'Productos',
                    'catalogo_marcas'        => 'Marcas',
                    'catalogo_categorias'    => 'Categorías',
                    'catalogo_subcategorias' => 'Subcategorías',
                    'catalogo_montos'        => 'Montos',
                ]
            ],

            // Bloque: Catálogos Generales
            'catalogos_generales' => [
                'name' => 'Catálogos Generales',
                'children' => [
                    'catalogo_monedas'            => 'Monedas',
                    'catalogo_tipodecambio'       => 'Tipo de Cambio',
                    'catalogo_bancos'             => 'Bancos',
                    'catalogo_cuentas_bancarias'  => 'Cuentas Bancarias',
                    'catalogo_impuestos'          => 'Impuestos',
                    'catalogo_retenciones'        => 'Retenciones',
                    'catalogo_descuentos'         => 'Descuentos',
                    'catalogo_unidades'           => 'Unidades de Medida',
                    'catalogo_condiciones_pago'   => 'Condiciones de Pago',
                    'catalogo_tipodedocumento'    => 'Tipos de Documento',
                ]
            ],

            // Bloque: Ventas y Clientes
            'ventas_clientes' => [
                'name' => 'Ventas y Clientes',
                'children' => [
                    'ventas_precotizacion' => 'Pre Cotizaciones',
                    'ventas_cotizaciones'  => 'Cotizaciones',
                    'ventas_ventas'        => 'Ventas',
                    'ventas_abandonados'   => 'Carritos',
                    'ventas_deseados'      => 'Deseados',
                    'ventas_cupones'       => 'Cupones',
                    'config_clientes_web'  => 'Clientes Web',
                    'config_socios'        => 'Clientes SAP',
                ]
            ],

            // Bloque: Compras y Proveedores
            'compras_proveedores' => [
                'name' => 'Compras y Proveedores',
                'children' => [
                    'compras_dashboard'         => 'Dashboard de Compra',
                    'compras_ordenes'           => 'Órdenes de Compra',
                    'compras_facturas'          => 'Facturas Compra',
                    'compras_rep'               => 'Rep de Pago',
                    'compras_por_proveedor'     => 'Por Proveedor',
                    'compras_contrarecibos'     => 'Contrarecibos',
                    'compras_notasdecredito'    => 'Notas de Credito',
                    'config_proveedores'        => 'Proveedores',
                ]
            ],

            // Bloque: Logística
            'logistica' => [
                'name' => 'Logística',
                'children' => [
                    'logistica_orders'      => 'Estatus Pedido',
                    'logistica_paqueterias' => 'Paqueterías',
                ]
            ],

            // Bloque: Bancos y Finanzas (Catálogo)
            'bancos_catalogo' => [
                'name' => 'Bancos y Finanzas (Catálogo)',
                'children' => [
                    'banco_bbva'           => 'BBVA',
                    'banco_logs'           => 'Logs',
                    'banco_datos_transf'   => 'Datos Transferencia',
                    'config_procesadores'  => 'Procesadores de Pago',
                    'config_listas_precios'=> 'Listas de Precios',
                ]
            ],

            // Bloque: Bancos y Finanzas (Operación)
            'bancos_operacion' => [
                'name' => 'Bancos y Finanzas (Operación)',
                'children' => [
                    'mov_bancarios'        => 'Movimientos Bancarios',
                    'plan_cuentas'         => 'Plan de Cuentas',
                    'conciliacion'         => 'Conciliaciones',
                    'cuentas_pagar'        => 'Cuentas por Pagar',
                    'cuentas_cobrar'       => 'Cuentas por Cobrar',
                    'reportes_financieros' => 'Reportes Financieros',
                    'cajas_fondos'         => 'Cajas y Fondos',
                ]
            ],

            // Bloque: Datos Fiscales (SAT)
            'datos_fiscales' => [
                'name' => 'Datos Fiscales',
                'children' => [
                    'sat_formas_pago'      => 'Formas de Pago',
                    'sat_usos_cfdi'        => 'Usos CFDI',
                    'sat_regimen_fiscal'   => 'Régimen Fiscal',
                    'sat_retenciones'      => 'Retenciones',
                    'sat_unidades'         => 'Unidades de Medida',
                ]
            ],

            // Bloque: Recursos Humanos
            'recursos_humanos' => [
                'name' => 'Recursos Humanos',
                'children' => [
                    'config_empleados'      => 'Empleados',
                    'config_departamento'   => 'Departamentos',
                    'rrhh_asistencia'       => 'Asistencia',
                    'rrhh_nominas'          => 'Nóminas',
                    'rrhh_reportes'         => 'Reportes',
                    // futuros: vacaciones, incidencias, etc.
                ]
            ],

            // Bloque: Configuración
            'configuracion' => [
                'name' => 'Configuración',
                'children' => [
                    'config_general'           => 'General',
                    'config_usuarios'          => 'Usuarios Acceso',
                    'config_notificaciones'    => 'Notificaciones',
                    'config_permisos_grupo'    => 'Permisos por Grupo',
                    'config_permisos_usuario'  => 'Permisos por Usuario',
                    'config_correos'           => 'Confdiguración de Correos',
                ]
            ],

            // Bloque: CRM
            'crm' => [
                'name' => 'CRM',
                'children' => [
                    'crm_encuestas'        => 'Encuestas',
                    'crm_reportes_diarios' => 'Reportes Diarios',
                    'crm_tareas'           => 'Tareas',
                    'crm_tickets'          => 'Tickets',
                    //'crm_mis_asignados'    => 'Mis Asignados',
                    'crm_tickets_socios'   => 'Tickets Socios',
                    'crm_rastreo_local'    => 'Rastreo Local',
                    'crm_corte'            => 'Calculadora de Corte',
                ]
            ],

            // Bloque: Helpdesk
            'helpdesk' => [
                'name' => 'Helpdesk',
                'children' => [
                    'helpdesk_reportes_crm'   => 'Reportes CRM',
                    'helpdesk_tareas'         => 'Tareas Pendientes',
                    'helpdesk_tickets'        => 'Tickets',
                    'helpdesk_mis_asignados'  => 'Mis Asignados',
                    'helpdesk_incidencias'    => 'Tipos de Incidencia',
                    'helpdesk_tipos_ticket'   => 'Tipos de Ticket',
                ]
            ],

            // Bloque: Reportes Generales
            'reportes_generales' => [
                'name' => 'Reportes Generales',
                'children' => [
                    'reportes_generales'                => 'Módulo de Reportes',
                    'reportes_generales_departamento'   => 'Por Departamento',
                    'reportes_generales_financieros'    => 'Financieros',
                    'reportes_generales_operativos'     => 'Operativos',
                ]
            ],

        ];



    // 1. QUERY BASE
        $usuarios = Model_User::query()
            ->where_open()
                ->where('group', 100)
                ->or_where('group', 50)
                ->or_where('group', 30)
                ->or_where('group', 25)
                ->or_where('group', 20)
            ->where_close();

        // 2. BUSQUEDA
        if($search != '') {
            $original_search = $search;
            $search = str_replace('+', ' ', rawurldecode($search));
            $search = str_replace(' ', '%', $search);

            $usuarios = $usuarios->where(
                DB::expr("CONCAT(`t0`.`username`, ' ', `t0`.`email`)"), 'like', '%'.$search.'%'
            );
            $data['search'] = $original_search;
        } else {
            $data['search'] = '';
        }

        // 3. OBTENER TODOS LOS USUARIOS
        $usuarios = $usuarios->order_by('id', 'desc')->get();

        // 4. FILTRA LOS NO BANEADOS Y ARMA INFO LIMPIA
        foreach($usuarios as $user) {
            $fields = unserialize($user->profile_fields);
            if (empty($fields['banned'])) {
                $users_info[] = array(
                    'id'       => $user->id,
                    'username' => $user->username,
                    'email'    => $user->email,
                    'group'    => $user->group,
                    'full_name'=> $fields['full_name'] ?? '',
                    'connected'=> $fields['connected'] ?? '',
                );
            }
        }

        // 5. PAGINACION EN PHP SOBRE $users_info
        $total_items = count($users_info);
        $config = array(
            'name'           => 'admin',
            'pagination_url' => Uri::current(),
            'total_items'    => $total_items,
            'per_page'       => $per_page,
            'uri_segment'    => 'pagina',
            'show_first'     => true,
            'show_last'      => true,
        );
        $pagination = Pagination::forge('admin', $config);

        $users_info_paginated = array_slice($users_info, $pagination->offset, $pagination->per_page);

        // 6. PASA A LA VISTA SOLO EL ARRAY YA FORMATEADO
        $data['usuarios']   = $users_info_paginated;
        $data['pagination'] = $pagination->render();
        $data['search']     = str_replace('%', ' ', $search);

        $this->template->title   = 'Permisos por usuario';
        $this->template->content = View::forge('admin/configuracion/permisos/usuario/index', $data, false);
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
				Response::redirect('admin/configuracion/permisos/usuario/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/configuracion/permisos/usuario');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/configuracion/permisos/usuario');
		}
	}


    /**
     * EDITAR
     *
     * PERMITE AGREGAR O MODIFICAR LOS DATOS GENERALES DE LA EMPRESA
     *
     * @access  public
     * @return  Void
     */
    public function action_editar($user_id)
    {
        // ARMAR MÓDULOS Y SUBMÓDULOS
             $modules = [

            // Principales
            'dashboard'      => ['name' => 'Dashboard'],
            'sala_juntas'    => ['name' => 'Sala de Juntas'],

            // Bloque: Gestión
            'gestion' => [
                'name' => 'Gestión',
                'children' => [
                    'slides' => 'Slides',
                    'banners_productos' => 'Banners Productos',
                    'banners_laterales' => 'Banners Laterales',
                    'blog_categorias'   => 'Blog Categorías',
                    'blog_etiquetas'    => 'Blog Etiquetas',
                    'blog_publicacion'  => 'Blog Publicaciones',
                    'editor_diseno'     => 'Editor de Diseño',
                    'legal_documentos'  => 'Documentos Legales',
                    'legal_consents'    => 'Consentimientos',
                    'legal_cookies'     => 'Cookies',
                    'apariencia_footer'     => 'Apariencia Footer',
                    'apariencia_header'     => 'Apariencia Header',
                ]
            ],

            // Bloque: Plataformas
            'plataformas' => [
                'name' => 'Plataformas',
                'children' => [
                    'plataformas_ml'         => 'Mercado Libre',
                    'plataformas_amazon'     => 'Amazon',
                    'plataformas_walmart'    => 'Walmart',
                    'plataformas_tiktok'     => 'TikTok Shop',
                    'plataformas_shopify'    => 'Shopify',
                    'plataformas_temu'       => 'Temu',
                    'plataformas_aliexpress' => 'AliExpress',
                    'plataformas_logs'       => 'Logs de Sincronización',
                    'plataformas_errores'    => 'Errores',
                ],
            ],


            // Bloque: Catálogo de Productos
            'catalogo_productos' => [
                'name' => 'Catálogo de Productos',
                'children' => [
                    'catalogo_productos'     => 'Productos',
                    'catalogo_marcas'        => 'Marcas',
                    'catalogo_categorias'    => 'Categorías',
                    'catalogo_subcategorias' => 'Subcategorías',
                    'catalogo_montos'        => 'Montos',
                ]
            ],

            // Bloque: Catálogos Generales
            'catalogos_generales' => [
                'name' => 'Catálogos Generales',
                'children' => [
                    'catalogo_monedas'            => 'Monedas',
                    'catalogo_tipodecambio'       => 'Tipo de Cambio',
                    'catalogo_bancos'             => 'Bancos',
                    'catalogo_cuentas_bancarias'  => 'Cuentas Bancarias',
                    'catalogo_impuestos'          => 'Impuestos',
                    'catalogo_retenciones'        => 'Retenciones',
                    'catalogo_descuentos'         => 'Descuentos',
                    'catalogo_unidades'           => 'Unidades de Medida',
                    'catalogo_condiciones_pago'   => 'Condiciones de Pago',
                    'catalogo_tipodedocumento'    => 'Tipos de Documento',
                ]
            ],

            // Bloque: Ventas y Clientes
            'ventas_clientes' => [
                'name' => 'Ventas y Clientes',
                'children' => [
                    'ventas_precotizacion' => 'Pre Cotizaciones',
                    'ventas_cotizaciones'  => 'Cotizaciones',
                    'ventas_ventas'        => 'Ventas',
                    'ventas_abandonados'   => 'Carritos',
                    'ventas_deseados'      => 'Deseados',
                    'ventas_cupones'       => 'Cupones',
                    'config_clientes_web'  => 'Clientes Web',
                    'config_socios'        => 'Clientes SAP',
                ]
            ],

            // Bloque: Compras y Proveedores
            'compras_proveedores' => [
                'name' => 'Compras y Proveedores',
                'children' => [
                    'compras_dashboard'         => 'Dashboard de Compra',
                    'compras_ordenes'           => 'Órdenes de Compra',
                    'compras_facturas'          => 'Facturas Compra',
                    'compras_rep'               => 'Rep de Pago',
                    'compras_por_proveedor'     => 'Por Proveedor',
                    'compras_contrarecibos'     => 'Contrarecibos',
                    'compras_notasdecredito'    => 'Notas de Credito',
                    'config_proveedores'        => 'Proveedores',
                ]
            ],

            // Bloque: Logística
            'logistica' => [
                'name' => 'Logística',
                'children' => [
                    'logistica_orders'      => 'Estatus Pedido',
                    'logistica_paqueterias' => 'Paqueterías',
                ]
            ],

            // Bloque: Bancos y Finanzas (Catálogo)
            'bancos_catalogo' => [
                'name' => 'Bancos y Finanzas (Catálogo)',
                'children' => [
                    'banco_bbva'           => 'BBVA',
                    'banco_logs'           => 'Logs',
                    'banco_datos_transf'   => 'Datos Transferencia',
                    'config_procesadores'  => 'Procesadores de Pago',
                    'config_listas_precios'=> 'Listas de Precios',
                ]
            ],

            // Bloque: Bancos y Finanzas (Operación)
            'bancos_operacion' => [
                'name' => 'Bancos y Finanzas (Operación)',
                'children' => [
                    'mov_bancarios'        => 'Movimientos Bancarios',
                    'plan_cuentas'         => 'Plan de Cuentas',
                    'conciliacion'         => 'Conciliaciones',
                    'cuentas_pagar'        => 'Cuentas por Pagar',
                    'cuentas_cobrar'       => 'Cuentas por Cobrar',
                    'reportes_financieros' => 'Reportes Financieros',
                    'cajas_fondos'         => 'Cajas y Fondos',
                ]
            ],

            // Bloque: Datos Fiscales (SAT)
            'datos_fiscales' => [
                'name' => 'Datos Fiscales',
                'children' => [
                    'sat_formas_pago'      => 'Formas de Pago',
                    'sat_usos_cfdi'        => 'Usos CFDI',
                    'sat_regimen_fiscal'   => 'Régimen Fiscal',
                    'sat_retenciones'      => 'Retenciones',
                    'sat_unidades'         => 'Unidades de Medida',
                ]
            ],

            // Bloque: Recursos Humanos
            'recursos_humanos' => [
                'name' => 'Recursos Humanos',
                'children' => [
                    'config_empleados'      => 'Empleados',
                    'config_departamento'   => 'Departamentos',
                    'rrhh_asistencia'       => 'Asistencia',
                    'rrhh_nominas'          => 'Nóminas',
                    'rrhh_reportes'         => 'Reportes',
                    // futuros: vacaciones, incidencias, etc.
                ]
            ],

            // Bloque: Configuración
            'configuracion' => [
                'name' => 'Configuración',
                'children' => [
                    'config_general'           => 'General',
                    'config_usuarios'          => 'Usuarios Acceso',
                    'config_notificaciones'    => 'Notificaciones',
                    'config_permisos_grupo'    => 'Permisos por Grupo',
                    'config_permisos_usuario'  => 'Permisos por Usuario',
                    'config_correos'           => 'Confdiguración de Correos',
                ]
            ],

            // Bloque: CRM
            'crm' => [
                'name' => 'CRM',
                'children' => [
                    'crm_encuestas'        => 'Encuestas',
                    'crm_reportes_diarios' => 'Reportes Diarios',
                    'crm_tareas'           => 'Tareas',
                    'crm_tickets'          => 'Tickets',
                    //'crm_mis_asignados'    => 'Mis Asignados',
                    'crm_tickets_socios'   => 'Tickets Socios',
                    'crm_rastreo_local'    => 'Rastreo Local',
                    'crm_corte'            => 'Calculadora de Corte',
                ]
            ],

            // Bloque: Helpdesk
            'helpdesk' => [
                'name' => 'Helpdesk',
                'children' => [
                    'helpdesk_reportes_crm'   => 'Reportes CRM',
                    'helpdesk_tareas'         => 'Tareas Pendientes',
                    'helpdesk_tickets'        => 'Tickets',
                    'helpdesk_mis_asignados'  => 'Mis Asignados',
                    'helpdesk_incidencias'    => 'Tipos de Incidencia',
                    'helpdesk_tipos_ticket'   => 'Tipos de Ticket',
                ]
            ],

            // Bloque: Reportes Generales
            'reportes_generales' => [
                'name' => 'Reportes Generales',
                'children' => [
                    'reportes_generales'                => 'Módulo de Reportes',
                    'reportes_generales_departamento'   => 'Por Departamento',
                    'reportes_generales_financieros'    => 'Financieros',
                    'reportes_generales_operativos'     => 'Operativos',
                ]
            ],

        ];


        // 1. BUSCA USUARIO
        $usuario = Model_User::find($user_id);
        if (!$usuario) {
            Session::set_flash('error', 'Usuario no encontrado.');
            Response::redirect('admin/configuracion/permisos/usuario');
        }

        // 2. OBTIENE PERMISOS DE ESE USUARIO
        $perms = [];
        $permissions = Model_Permission::query()->where('user_id', $user_id)->get();
        foreach ($permissions as $perm) {
            $perms[$perm->resource] = [
                'view'   => (int)$perm->can_view,
                'edit'   => (int)$perm->can_edit,
                'delete' => (int)$perm->can_delete,
                'create' => (int)$perm->can_create,
            ];
        }

        // 3. PROCESA FORMULARIO
        if (Input::method() == 'POST') {
            $perms_post = Input::post('perm', []);
            foreach ($modules as $mod_key => $mod) {
                if (!empty($mod['children'])) {
                    foreach ($mod['children'] as $child_key => $child_name) {
                        $permission = Model_Permission::query()
                            ->where('user_id', $user_id)
                            ->where('resource', $child_key)
                            ->get_one();
                        if (!$permission) {
                            $permission = Model_Permission::forge([
                                'user_id'  => $user_id,
                                'resource' => $child_key,
                            ]);
                        }
                        $permission->can_view   = isset($perms_post[$child_key]['view'])   ? 1 : 0;
                        $permission->can_edit   = isset($perms_post[$child_key]['edit'])   ? 1 : 0;
                        $permission->can_delete = isset($perms_post[$child_key]['delete']) ? 1 : 0;
                        $permission->can_create = isset($perms_post[$child_key]['create']) ? 1 : 0;
                        $permission->save();
                    }
                } else {
                    $permission = Model_Permission::query()
                        ->where('user_id', $user_id)
                        ->where('resource', $mod_key)
                        ->get_one();
                    if (!$permission) {
                        $permission = Model_Permission::forge([
                            'user_id'  => $user_id,
                            'resource' => $mod_key,
                        ]);
                    }
                    $permission->can_view   = isset($perms_post[$mod_key]['view'])   ? 1 : 0;
                    $permission->can_edit   = isset($perms_post[$mod_key]['edit'])   ? 1 : 0;
                    $permission->can_delete = isset($perms_post[$mod_key]['delete']) ? 1 : 0;
                    $permission->can_create = isset($perms_post[$mod_key]['create']) ? 1 : 0;
                    $permission->save();
                }
            }
            Session::set_flash('success', 'Permisos actualizados correctamente.');
            Helper_Permission::refresh_session_permissions($user_id);
            Response::redirect('admin/configuracion/permisos/usuario/editar/'.$user_id);
        }

        // 4. PASA DATOS A LA VISTA
        $data['usuario'] = $usuario;
        $data['modules'] = $modules;
        $data['perms']   = $perms;
        $this->template->title   = 'Editar Permisos: '.$usuario->username;
        $this->template->content = View::forge('admin/configuracion/permisos/usuario/editar', $data, false);
    }



    /**
     * GENERAL
     *
     * PERMITE VER LA VISTA GENERAL DE LOS PERMISOS
     *
     * @access  public
     * @return  Void
     */
    public function action_general()
    {
        $grupos = [
            20 => 'Empleados',
            25 => 'Vendedores',
            30 => 'Externo',
            50 => 'Moderador',
            100 => 'Administrador'
        ];

     
        // Colores únicos por grupo
        $colores_grupo = [
            20 => 'badge-info',        // Empleados
            25 => 'badge-success',     // Vendedores
            30 => 'badge-warning',     // Externo
            50 => 'badge-primary',     // Moderador
            100 => 'badge-danger',     // Administrador
            0   => 'badge-secondary',  // Sin grupo
        ];



             $modules = [

            // Principales
            'dashboard'      => ['name' => 'Dashboard'],
            'sala_juntas'    => ['name' => 'Sala de Juntas'],

            // Bloque: Gestión
            'gestion' => [
                'name' => 'Gestión',
                'children' => [
                    'slides' => 'Slides',
                    'banners_productos' => 'Banners Productos',
                    'banners_laterales' => 'Banners Laterales',
                    'blog_categorias'   => 'Blog Categorías',
                    'blog_etiquetas'    => 'Blog Etiquetas',
                    'blog_publicacion'  => 'Blog Publicaciones',
                    'editor_diseno'     => 'Editor de Diseño',
                    'legal_documentos'  => 'Documentos Legales',
                    'legal_consents'    => 'Consentimientos',
                    'legal_cookies'     => 'Cookies',
                    'apariencia_footer'     => 'Apariencia Footer',
                    'apariencia_header'     => 'Apariencia Header',
                ]
            ],

            // Bloque: Plataformas
            'plataformas' => [
                'name' => 'Plataformas',
                'children' => [
                    'plataformas_ml'         => 'Mercado Libre',
                    'plataformas_amazon'     => 'Amazon',
                    'plataformas_walmart'    => 'Walmart',
                    'plataformas_tiktok'     => 'TikTok Shop',
                    'plataformas_shopify'    => 'Shopify',
                    'plataformas_temu'       => 'Temu',
                    'plataformas_aliexpress' => 'AliExpress',
                    'plataformas_logs'       => 'Logs de Sincronización',
                    'plataformas_errores'    => 'Errores',
                ],
            ],


            // Bloque: Catálogo de Productos
            'catalogo_productos' => [
                'name' => 'Catálogo de Productos',
                'children' => [
                    'catalogo_productos'     => 'Productos',
                    'catalogo_marcas'        => 'Marcas',
                    'catalogo_categorias'    => 'Categorías',
                    'catalogo_subcategorias' => 'Subcategorías',
                    'catalogo_montos'        => 'Montos',
                ]
            ],

            // Bloque: Catálogos Generales
            'catalogos_generales' => [
                'name' => 'Catálogos Generales',
                'children' => [
                    'catalogo_monedas'            => 'Monedas',
                    'catalogo_tipodecambio'       => 'Tipo de Cambio',
                    'catalogo_bancos'             => 'Bancos',
                    'catalogo_cuentas_bancarias'  => 'Cuentas Bancarias',
                    'catalogo_impuestos'          => 'Impuestos',
                    'catalogo_retenciones'        => 'Retenciones',
                    'catalogo_descuentos'         => 'Descuentos',
                    'catalogo_unidades'           => 'Unidades de Medida',
                    'catalogo_condiciones_pago'   => 'Condiciones de Pago',
                    'catalogo_tipodedocumento'    => 'Tipos de Documento',
                ]
            ],

            // Bloque: Ventas y Clientes
            'ventas_clientes' => [
                'name' => 'Ventas y Clientes',
                'children' => [
                    'ventas_precotizacion' => 'Pre Cotizaciones',
                    'ventas_cotizaciones'  => 'Cotizaciones',
                    'ventas_ventas'        => 'Ventas',
                    'ventas_abandonados'   => 'Carritos',
                    'ventas_deseados'      => 'Deseados',
                    'ventas_cupones'       => 'Cupones',
                    'config_clientes_web'  => 'Clientes Web',
                    'config_socios'        => 'Clientes SAP',
                ]
            ],

            // Bloque: Compras y Proveedores
            'compras_proveedores' => [
                'name' => 'Compras y Proveedores',
                'children' => [
                    'compras_dashboard'         => 'Dashboard de Compra',
                    'compras_ordenes'           => 'Órdenes de Compra',
                    'compras_facturas'          => 'Facturas Compra',
                    'compras_rep'               => 'Rep de Pago',
                    'compras_por_proveedor'     => 'Por Proveedor',
                    'compras_contrarecibos'     => 'Contrarecibos',
                    'compras_notasdecredito'    => 'Notas de Credito',
                    'config_proveedores'        => 'Proveedores',
                ]
            ],

            // Bloque: Logística
            'logistica' => [
                'name' => 'Logística',
                'children' => [
                    'logistica_orders'      => 'Estatus Pedido',
                    'logistica_paqueterias' => 'Paqueterías',
                ]
            ],

            // Bloque: Bancos y Finanzas (Catálogo)
            'bancos_catalogo' => [
                'name' => 'Bancos y Finanzas (Catálogo)',
                'children' => [
                    'banco_bbva'           => 'BBVA',
                    'banco_logs'           => 'Logs',
                    'banco_datos_transf'   => 'Datos Transferencia',
                    'config_procesadores'  => 'Procesadores de Pago',
                    'config_listas_precios'=> 'Listas de Precios',
                ]
            ],

            // Bloque: Bancos y Finanzas (Operación)
            'bancos_operacion' => [
                'name' => 'Bancos y Finanzas (Operación)',
                'children' => [
                    'mov_bancarios'        => 'Movimientos Bancarios',
                    'plan_cuentas'         => 'Plan de Cuentas',
                    'conciliacion'         => 'Conciliaciones',
                    'cuentas_pagar'        => 'Cuentas por Pagar',
                    'cuentas_cobrar'       => 'Cuentas por Cobrar',
                    'reportes_financieros' => 'Reportes Financieros',
                    'cajas_fondos'         => 'Cajas y Fondos',
                ]
            ],

            // Bloque: Datos Fiscales (SAT)
            'datos_fiscales' => [
                'name' => 'Datos Fiscales',
                'children' => [
                    'sat_formas_pago'      => 'Formas de Pago',
                    'sat_usos_cfdi'        => 'Usos CFDI',
                    'sat_regimen_fiscal'   => 'Régimen Fiscal',
                    'sat_retenciones'      => 'Retenciones',
                    'sat_unidades'         => 'Unidades de Medida',
                ]
            ],

            // Bloque: Recursos Humanos
            'recursos_humanos' => [
                'name' => 'Recursos Humanos',
                'children' => [
                    'config_empleados'      => 'Empleados',
                    'config_departamento'   => 'Departamentos',
                    'rrhh_asistencia'       => 'Asistencia',
                    'rrhh_nominas'          => 'Nóminas',
                    'rrhh_reportes'         => 'Reportes',
                    // futuros: vacaciones, incidencias, etc.
                ]
            ],

            // Bloque: Configuración
            'configuracion' => [
                'name' => 'Configuración',
                'children' => [
                    'config_general'           => 'General',
                    'config_usuarios'          => 'Usuarios Acceso',
                    'config_notificaciones'    => 'Notificaciones',
                    'config_permisos_grupo'    => 'Permisos por Grupo',
                    'config_permisos_usuario'  => 'Permisos por Usuario',
                    'config_correos'           => 'Confdiguración de Correos',
                ]
            ],

            // Bloque: CRM
            'crm' => [
                'name' => 'CRM',
                'children' => [
                    'crm_encuestas'        => 'Encuestas',
                    'crm_reportes_diarios' => 'Reportes Diarios',
                    'crm_tareas'           => 'Tareas',
                    'crm_tickets'          => 'Tickets',
                    //'crm_mis_asignados'    => 'Mis Asignados',
                    'crm_tickets_socios'   => 'Tickets Socios',
                    'crm_rastreo_local'    => 'Rastreo Local',
                    'crm_corte'            => 'Calculadora de Corte',
                ]
            ],

            // Bloque: Helpdesk
            'helpdesk' => [
                'name' => 'Helpdesk',
                'children' => [
                    'helpdesk_reportes_crm'   => 'Reportes CRM',
                    'helpdesk_tareas'         => 'Tareas Pendientes',
                    'helpdesk_tickets'        => 'Tickets',
                    'helpdesk_mis_asignados'  => 'Mis Asignados',
                    'helpdesk_incidencias'    => 'Tipos de Incidencia',
                    'helpdesk_tipos_ticket'   => 'Tipos de Ticket',
                ]
            ],

            // Bloque: Reportes Generales
            'reportes_generales' => [
                'name' => 'Reportes Generales',
                'children' => [
                    'reportes_generales'                => 'Módulo de Reportes',
                    'reportes_generales_departamento'   => 'Por Departamento',
                    'reportes_generales_financieros'    => 'Financieros',
                    'reportes_generales_operativos'     => 'Operativos',
                ]
            ],

        ];


        // 2. Obtiene todos los usuarios de los grupos válidos
        $usuarios = Model_User::query()
            ->where_open()
                ->where('group', 100)
                ->or_where('group', 50)
                ->or_where('group', 30)
                ->or_where('group', 25)
                ->or_where('group', 20)
            ->where_close()
            ->order_by('group', 'asc')
            ->order_by('username', 'asc')
            ->get();

        // 3. Obtiene permisos individuales de usuario
        $perms_usuario = [];
        foreach (Model_Permission::query()->get() as $perm) {
            $perms_usuario[$perm->user_id][$perm->resource] = [
                'view'   => (int) $perm->can_view,
                'edit'   => (int) $perm->can_edit,
                'delete' => (int) $perm->can_delete,
                'create' => (int) $perm->can_create,
            ];
        }

        // 4. Obtiene permisos por grupo
        $perms_grupo = [];
        foreach (Model_Permission_Group::query()->get() as $perm) {
            $perms_grupo[$perm->group_id][$perm->resource] = [
                'view'   => (int) $perm->can_view,
                'edit'   => (int) $perm->can_edit,
                'delete' => (int) $perm->can_delete,
                'create' => (int) $perm->can_create,
            ];
        }

        // 5. Pasa todo a la vista
        $data['modules']        = $modules;
        $data['grupos']         = $grupos;
        $data['colores_grupo']  = $colores_grupo;
        $data['usuarios']       = $usuarios;
        $data['perms_usuario']  = $perms_usuario;
        $data['perms_grupo']    = $perms_grupo;

        $this->template->title   = 'Permisos Generales por Usuario';
        $this->template->content = View::forge('admin/configuracion/permisos/usuario/general', $data, false);
    }





}
