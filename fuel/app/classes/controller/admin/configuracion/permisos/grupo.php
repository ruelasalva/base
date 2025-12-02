<?php

/**
* CONTROLADOR PERMISOS POR GURPO
*
* @package  app
* @extends  Controller_Admin
*/
class Controller_Admin_Configuracion_Permisos_Grupo extends Controller_Admin
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
    public function action_index()
    {
        // 1. DEFINIR LOS GRUPOS ADMIN QUE VAS A MOSTRAR (AJUSTA A TU SISTEMA)
        $grupos = [
            20   => 'Empleados',
            25   => 'Vendedores',
            30   => 'Externo',
            50   => 'Moderators',
            100  => 'Administrators',
            // ... agrega los que quieras mostrar
        ];

        // 2. DEFINE LOS MODULOS (RESOURCE)
        $modules = [

            // Principales
            'dashboard'      => ['name' => 'Dashboard'],
            'sala_juntas'    => ['name' => 'Sala de Juntas'],

            // Bloque: Gestión
            'gestion' => [
                'name' => 'Gestión',
                'children' => [
                    'slides' => 'Slides',
                    'banners_productos'     => 'Banners Productos',
                    'banners_laterales'     => 'Banners Laterales',
                    'blog_categorias'       => 'Blog Categorías',
                    'blog_etiquetas'        => 'Blog Etiquetas',
                    'blog_publicacion'      => 'Blog Publicaciones',
                    'editor_diseno'         => 'Editor de Diseño',
                    'legal_documentos'      => 'Documentos Legales',
                    'legal_consents'        => 'Consentimientos',
                    'legal_cookies'         => 'Cookies',
                    'legal_contratos'       => 'Contratos Legales',
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


        // Agrega más módulos según sea necesario
        // Puedes agregar más módulos y sus permisos aquí
        // Asegúrate de que las claves coincidan con los recursos en tu base de datos
        // Ejemplo: 'nuevo_modulo' => ['name' => 'Nuevo Módulo', 'children' => []],
        // Si no hay hijos, simplemente usa: 'nuevo_modulo' => ['name' => 'Nuevo Módulo'],

    // Cambia por los de tu sistema

        // --- 3. OBTIENE PERMISOS EXISTENTES DE LA TABLA DE GRUPOS
        $perms = [];
        $permissions = Model_Permission_Group::query()->get();
        foreach ($permissions as $perm) {
            $perms[$perm->group_id][$perm->resource] = [
                'view'   => (int)$perm->can_view,
                'edit'   => (int)$perm->can_edit,
                'delete' => (int)$perm->can_delete,
                'create' => (int)$perm->can_create,
            ];
        }

        // --- 4. PROCESA FORMULARIO
        if (Input::method() == 'POST') {
            $perms_post = Input::post('perm', []);
            foreach ($grupos as $group_id => $group_name) {
                foreach ($modules as $mod_key => $mod) {
                    if (!empty($mod['children'])) {
                        foreach ($mod['children'] as $child_key => $child_name) {
                            $permission = Model_Permission_Group::query()
                                ->where('group_id', $group_id)
                                ->where('resource', $child_key)
                                ->get_one();
                            if (!$permission) {
                                $permission = Model_Permission_Group::forge([
                                    'group_id'  => $group_id,
                                    'resource' => $child_key,
                                ]);
                            }
                            $permission->can_view   = isset($perms_post[$group_id][$child_key]['view'])   ? 1 : 0;
                            $permission->can_edit   = isset($perms_post[$group_id][$child_key]['edit'])   ? 1 : 0;
                            $permission->can_delete = isset($perms_post[$group_id][$child_key]['delete']) ? 1 : 0;
                            $permission->can_create = isset($perms_post[$group_id][$child_key]['create']) ? 1 : 0;
                            $permission->save();
                        }
                    } else {
                        $permission = Model_Permission_Group::query()
                            ->where('group_id', $group_id)
                            ->where('resource', $mod_key)
                            ->get_one();
                        if (!$permission) {
                            $permission = Model_Permission_Group::forge([
                                'group_id'  => $group_id,
                                'resource' => $mod_key,
                            ]);
                        }
                        $permission->can_view   = isset($perms_post[$group_id][$mod_key]['view'])   ? 1 : 0;
                        $permission->can_edit   = isset($perms_post[$group_id][$mod_key]['edit'])   ? 1 : 0;
                        $permission->can_delete = isset($perms_post[$group_id][$mod_key]['delete']) ? 1 : 0;
                        $permission->can_create = isset($perms_post[$group_id][$mod_key]['create']) ? 1 : 0;
                        $permission->save();
                    }
                }
            }
            Session::set_flash('success', 'Permisos de grupo actualizados correctamente.');
            Response::redirect(Uri::current());
        }

        // --- 5. PASA DATOS A LA VISTA ---
        $data['grupos']  = $grupos;
        $data['modules'] = $modules;
        $data['perms']   = $perms;

        $this->template->title   = 'Permisos por Grupo';
        $this->template->content = View::forge('admin/configuracion/permisos/grupo/index', $data, false);
    }





}
