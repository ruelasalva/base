<?php

/**
 * CONTROLADOR ADMIN_CATALOGO_MARCAS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Finanzas_Plancuentas extends Controller_Admin
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
	 * MUESTRA UNA LISTADO DE REGISTROS
	 *
	 * @access  public
	 * @return  Void
	 */
	/**
     * INDEX
     * Lista y busca cuentas del plan contable.
     */
    public function action_index($search = '')
    {
        // ====== PERMISOS ======
        if (!Helper_Permission::can('plan_cuentas', 'view')) {
            Session::set_flash('error', 'No tienes permiso para ver el plan de cuentas.');
            Response::redirect('admin');
        }

        Log::info('[PLAN][INDEX] Inicio de listado. search=' . $search);

        // ====== VARIABLES ======
        $data         = array();
        $accounts_arr = array();
        $per_page     = 100;

        // ====== CONSULTA BASE ======
        $query = Model_Accounts_Chart::query()->where('deleted', 0);

        // ====== FILTRO DE BÚSQUEDA ======
        if ($search != '')
        {
            $original_search = $search;
            $search = str_replace('+', ' ', rawurldecode($search));
            $search = str_replace(' ', '%', $search);

            $query->where_open()
                ->where(DB::expr("CONCAT(`t0`.`code`, ' ', `t0`.`name`)"), 'like', '%' . $search . '%')
            ->where_close();
        }

        // ====== FILTRO DE MONEDA (GET) ======
        $currency_id = Input::get('currency_id', null);
        if (!empty($currency_id)) {
            $query->where('currency_id', '=', (int) $currency_id);
        }

        // ====== PAGINACIÓN ======
        $config = array(
            'name'           => 'admin',
            'pagination_url' => Uri::current(),
            'total_items'    => $query->count(),
            'per_page'       => $per_page,
            'uri_segment'    => 'pagina',
        );
        $pagination = Pagination::forge('admin', $config);

        // ====== RESULTADOS ======
        $accounts = $query
            ->order_by('code', 'asc')
            ->rows_limit($pagination->per_page)
            ->rows_offset($pagination->offset)
            ->get();

        // ====== FORMATEO PARA VISTA ======
        foreach ($accounts as $acc)
        {
            $accounts_arr[] = array(
                'id'             => $acc->id,
                'code'           => $acc->code,
                'name'           => $acc->name,
                'type'           => $acc->type,
                'level'          => $acc->level,
                'currency'       => ($acc->currency) ? $acc->currency->code : '-',
                'is_active'      => $acc->is_active,
                'is_cash_account'=> $acc->is_cash_account,
                'parent_id'      => $acc->parent_id,
                'annex24_code'   => $acc->annex24_code,
                'account_class'  => $acc->account_class,
            );
        }

        // ====== LISTADO DE MONEDAS ======
        $currencies = Model_Currency::query()
            ->where('deleted', 0)
            ->order_by('code', 'asc')
            ->get();

        $currency_opts = array('' => 'Todas las monedas');
        foreach ($currencies as $c) {
            $currency_opts[$c->id] = $c->code . ' - ' . $c->name;
        }

        // ====== VARIABLES PARA LA VISTA ======
        $data['accounts']      = $accounts_arr;
        $data['search']        = str_replace('%', ' ', $search);
        $data['pagination']    = $pagination->render();
        $data['currency_opts'] = $currency_opts;
        $data['currency_id']   = $currency_id;

        // ====== CARGA VISTA ======
        $this->template->title   = 'Plan de Cuentas';
        $this->template->content = View::forge('admin/finanzas/plancuentas/index', $data, false);
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
    if(Input::method() == 'POST')
    {
        $data = array(
            'search' => ($_POST['search'] != '') ? $_POST['search'] : '',
        );

        $val = Validation::forge('search');
        $val->add_field('search', 'search', 'max_length[100]');

        if($val->run($data))
        {
            $search = str_replace(' ', '+', $val->validated('search'));
            $search = str_replace('*', '', $search);
            $search = ($val->validated('search') != '') ? $search : '';
            Response::redirect('admin/catalogo/generales/bancos/index/'.$search);
        }
        else
        {
            Response::redirect('admin/catalogo/generales/bancos');
        }
    }
    else
    {
        Response::redirect('admin/catalogo/generales/bancos');
    }
}



	/**
 * AGREGAR
 *
 * PERMITE AGREGAR UN REGISTRO A LA BASE DE DATOS
 *
 * @access  public
 * @return  Void
 */
public function action_agregar()
{
    #HELPER DE PERMISO PARA CREAR
    if (!Helper_Permission::can('plan_cuentas', 'create')) {
        Session::set_flash('error', 'No tienes permiso para crear cuentas.');
        Response::redirect('admin/finanzas/plancuentas');
    }

    # SE INICIALIZAN LAS VARIABLES
    $data    = array();
    $classes = array();
    $fields  = array('code', 'name', 'type', 'parent_id', 'level', 'currency_id', 'is_confidential', 'is_cash_account', 'is_active', 'annex24_code', 'account_class');

    foreach($fields as $field)
    {
        $classes[$field] = array (
            'form-group'   => null,
            'form-control' => null,
        );
    }

    # CARGA DE CATÁLOGOS
    $currencies = Model_Currency::query()
        ->where('deleted', 0)
        ->order_by('code', 'asc')
        ->get();

    $currency_opts = array('' => '-- Selecciona Moneda --');
    foreach ($currencies as $c) {
        $currency_opts[$c->id] = $c->code . ' - ' . $c->name;
    }

    $parents = Model_Accounts_Chart::query()
        ->where('deleted', 0)
        ->where('is_active', 1)
        ->order_by('code', 'asc')
        ->get();

    $parent_opts = array('' => '-- Ninguna (Cuenta principal) --');
    foreach ($parents as $p) {
        $parent_opts[$p->id] = $p->code . ' - ' . $p->name;
    }

    # SI SE UTILIZA EL METODO POST
    if(Input::method() == 'POST')
    {
        # SE CREA LA VALIDACION DE LOS CAMPOS
        $val = Validation::forge('account_chart');
        $val->add_callable('Rules');
        $val->add_field('code',           'código',        'required|min_length[1]|max_length[32]');
        $val->add_field('name',           'nombre',        'required|min_length[1]|max_length[128]');
        $val->add_field('type',           'tipo',          'required|min_length[1]|max_length[32]');
        $val->add_field('parent_id',      'cuenta padre',  'valid_string[numeric]');
        $val->add_field('level',          'nivel',         'required|valid_string[numeric]');
        $val->add_field('currency_id',    'moneda',        'valid_string[numeric]');
        $val->add_field('is_confidential','confidencial',  'valid_string[numeric]');
        $val->add_field('is_cash_account','efectivo',      'valid_string[numeric]');
        $val->add_field('is_active',      'activa',        'valid_string[numeric]');
        $val->add_field('annex24_code',   'anexo24',       'max_length[32]');
        $val->add_field('account_class',  'clase',         'max_length[32]');

        if($val->run())
        {
            try {
                $account = new Model_Accounts_Chart(array(
                    'code'            => $val->validated('code'),
                    'name'            => $val->validated('name'),
                    'type'            => $val->validated('type'),
                    'parent_id' => (Input::post('parent_id') == '' ? null : Input::post('parent_id')),

                    'level'           => $val->validated('level'),
                    'currency_id'     => Input::post('currency_id', null),
                    'is_confidential' => Input::post('is_confidential', 0),
                    'is_cash_account' => Input::post('is_cash_account', 0),
                    'is_active'       => Input::post('is_active', 1),
                    'annex24_code'    => Input::post('annex24_code', null),
                    'account_class'   => Input::post('account_class', null),
                    'created_at'      => time(),
                    'updated_at'      => time(),
                ));

                # CALCULA NIVEL AUTOMÁTICO SI TIENE PADRE
                $account->set_level_from_parent();

                if($account->save())
                {
                    Session::set_flash('success', 'Se agregó la cuenta <b>'.$val->validated('code').' - '.$val->validated('name').'</b> correctamente.');
                    Response::redirect('admin/finanzas/plancuentas');
                }

            } catch (Exception $e) {
                Log::error('[PLAN][AGREGAR] Error al guardar: ' . $e->getMessage());
                Session::set_flash('error', 'Error al guardar la cuenta.');
            }
        }
        else
        {
            Session::set_flash('error', 'Encontramos errores en el formulario, verifícalo.');

            $data['errors'] = $val->error();

            foreach($classes as $name => $class)
            {
                $classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
                $classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
                $data[$name] = Input::post($name);
            }
        }
    }

    # ENVÍO A VISTA
    $data['classes']       = $classes;
    $data['currency_opts'] = $currency_opts;
    $data['parent_opts']   = $parent_opts;

    $this->template->title   = 'Agregar cuenta';
    $this->template->content = View::forge('admin/finanzas/plancuentas/agregar', $data);
}





	/**
 * INFO
 *
 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
 *
 * @access  public
 * @return  Void
 */
public function action_info($account_id = 0)
{
    # PERMISO PARA VER
    if (!Helper_Permission::can('plan_cuentas', 'view')) {
        Session::set_flash('error', 'No tienes permiso para ver el plan de cuentas.');
        Response::redirect('admin/finanzas/plancuentas');
    }

    # VALIDACIÓN DE ID
    if ($account_id == 0 || !is_numeric($account_id)) {
        Response::redirect('admin/finanzas/plancuentas');
    }

    # OBTIENE CUENTA
    $account = Model_Accounts_Chart::query()
        ->related('currency')
        ->related('parent')
        ->where('id', $account_id)
        ->get_one();

    if (!$account) {
        Session::set_flash('error', 'No se encontró la cuenta solicitada.');
        Response::redirect('admin/finanzas/plancuentas');
    }

    # PREPARA DATOS PARA VISTA
    $data = array(
        'id'              => $account->id,
        'code'            => $account->code,
        'name'            => $account->name,
        'type'            => $account->type,
        'level'           => $account->level,
        'is_confidential' => $account->is_confidential,
        'is_cash_account' => $account->is_cash_account,
        'is_active'       => $account->is_active,
        'annex24_code'    => $account->annex24_code,
        'account_class'   => $account->account_class,
        'created_at'      => $account->created_at,
        'updated_at'      => $account->updated_at,
    );

    # MONEDA (usa relación)
    $data['currency'] = ($account->currency)
        ? $account->currency->code . ' - ' . $account->currency->name
        : '-';

    # CUENTA PADRE (usa relación)
    $data['parent'] = ($account->parent)
        ? [
            'id'   => $account->parent->id,
            'code' => $account->parent->code,
            'name' => $account->parent->name,
        ]
        : null;

    # CARGA LA VISTA
    $this->template->title   = 'Información de la cuenta';
    $this->template->content = View::forge('admin/finanzas/plancuentas/info', $data);
}






	/**
 * EDITAR
 *
 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
 *
 * @access  public
 * @return  Void
 */
public function action_editar($account_id = 0)
{
    # PERMISO PARA EDITAR
    if (!Helper_Permission::can('plan_cuentas', 'edit')) {
        Session::set_flash('error', 'No tienes permiso para editar cuentas.');
        Response::redirect('admin/finanzas/plancuentas');
    }

    # VALIDACIÓN DE ID
    if ($account_id == 0 || !is_numeric($account_id)) {
        Response::redirect('admin/finanzas/plancuentas');
    }

    # VARIABLES
    $data = array();
    $classes = array();
    $fields = array('code', 'name', 'type', 'parent_id', 'level', 'currency_id', 'is_confidential', 'is_cash_account', 'is_active', 'annex24_code', 'account_class');
    foreach ($fields as $field) {
        $classes[$field] = array('form-group' => null, 'form-control' => null);
    }

    # BUSCAR REGISTRO
    $account = Model_Accounts_Chart::find($account_id);
    if (!$account) {
        Session::set_flash('error', 'La cuenta no existe o fue eliminada.');
        Response::redirect('admin/finanzas/plancuentas');
    }

    # CARGAR MONEDAS Y CUENTAS PADRE
    $currencies = Model_Currency::query()->where('deleted', 0)->order_by('code', 'asc')->get();
    $currency_opts = array('' => '[Seleccione una moneda]');
    foreach ($currencies as $cur) {
        $currency_opts[$cur->id] = $cur->code . ' - ' . $cur->name;
    }

    $accounts = Model_Accounts_Chart::query()
        ->where('id', '!=', $account_id)
        ->where('deleted', 0)
        ->order_by('code', 'asc')
        ->get();
    $account_opts = array('' => '[Sin cuenta padre]');
    foreach ($accounts as $acc) {
        $account_opts[$acc->id] = $acc->code . ' - ' . $acc->name;
    }

    # DATOS EXISTENTES
    if (Input::method() != 'POST') {
        $data = array(
            'code'            => $account->code,
            'name'            => $account->name,
            'type'            => $account->type,
            'parent_id'       => $account->parent_id,
            'level'           => $account->level,
            'currency_id'     => $account->currency_id,
            'is_confidential' => $account->is_confidential,
            'is_cash_account' => $account->is_cash_account,
            'is_active'       => $account->is_active,
            'annex24_code'    => $account->annex24_code,
            'account_class'   => $account->account_class,
        );
    }

    # VALIDACIÓN POST
    if (Input::method() == 'POST') {
        $val = Validation::forge('account_chart');
        $val->add_callable('Rules');
        $val->add_field('code', 'código', 'required|min_length[1]|max_length[32]');
        $val->add_field('name', 'nombre', 'required|min_length[1]|max_length[128]');
        $val->add_field('type', 'tipo', 'required|min_length[1]|max_length[32]');
        $val->add_field('parent_id', 'cuenta padre', 'valid_string[numeric]');
        $val->add_field('level', 'nivel', 'required|valid_string[numeric]');
        $val->add_field('currency_id', 'moneda', 'valid_string[numeric]');
        $val->add_field('is_confidential', 'confidencial', 'valid_string[numeric]');
        $val->add_field('is_cash_account', 'efectivo', 'valid_string[numeric]');
        $val->add_field('is_active', 'activa', 'valid_string[numeric]');
        $val->add_field('annex24_code', 'anexo24', 'max_length[32]');
        $val->add_field('account_class', 'clase', 'max_length[32]');

        if ($val->run()) {
            # ASIGNAR NUEVOS VALORES
            $account->code            = $val->validated('code');
            $account->name            = $val->validated('name');
            $account->type            = $val->validated('type');
            $account->parent_id       = (Input::post('parent_id') == '' ? null : Input::post('parent_id'));
            $account->level           = $val->validated('level');
            $account->currency_id     = (Input::post('currency_id') == '' ? null : Input::post('currency_id'));
            $account->is_confidential = Input::post('is_confidential', 0);
            $account->is_cash_account = Input::post('is_cash_account', 0);
            $account->is_active       = Input::post('is_active', 1);
            $account->annex24_code    = Input::post('annex24_code', null);
            $account->account_class   = Input::post('account_class', null);
            $account->updated_at      = time();

            try {
                if ($account->save()) {
                    Session::set_flash('success', 'Se actualizó la cuenta <b>'.$account->code.' - '.$account->name.'</b> correctamente.');
                    Response::redirect('admin/finanzas/plancuentas/info/'.$account_id);
                }
            } catch (Database_Exception $e) {
                Log::error("[PLAN][EDITAR] Error al guardar: ".$e->getMessage());
                Session::set_flash('error', 'Error al actualizar la cuenta. Verifique las relaciones.');
            }
        } else {
            # ERRORES DE VALIDACIÓN
            Session::set_flash('error', 'Hay errores en el formulario. Verifícalo.');
            $data['errors'] = $val->error();
            foreach ($classes as $name => $class) {
                $classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
                $classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
                $data[$name] = Input::post($name);
            }
        }
    }

    # DATOS PARA LA VISTA
    $data['id']           = $account_id;
    $data['classes']      = $classes;
    $data['currency_opts'] = $currency_opts;
    $data['account_opts']  = $account_opts;

    $this->template->title   = 'Editar cuenta';
    $this->template->content = View::forge('admin/finanzas/plancuentas/editar', $data);
}


/* ============================================================
 * ARBOL (VISTA JERÁRQUICA)
 * ============================================================ */
public function action_arbol()
{
    if (!Helper_Permission::can('plan_cuentas', 'view')) {
        Session::set_flash('error', 'No tienes permiso para ver el plan de cuentas.');
        Response::redirect('admin/finanzas/plancuentas');
    }

    $accounts = Model_Accounts_Chart::query()
        ->where('deleted', 0)
        ->order_by('code', 'asc')
        ->get();

    $tree = $this->build_tree($accounts);

    $this->template->title   = 'Plan de Cuentas (Vista Jerárquica)';
    $this->template->content = View::forge('admin/finanzas/plancuentas/arbol', ['tree' => $tree]);
}

/* ============================================================
 * FUNCIÓN RECURSIVA PARA CONSTRUIR ÁRBOL
 * ============================================================ */
private function build_tree($accounts, $parent_id = null, $level = 0)
{
    $branch = [];
    foreach ($accounts as $account) {
        if ($account['parent_id'] == $parent_id) {
            $account['level'] = $level;
            $children = $this->build_tree($accounts, $account['id'], $level + 1);
            if ($children) $account['children'] = $children;
            $branch[] = $account;
        }
    }
    return $branch;
}


/* ============================================================
 * AJAX: CARGAR FORMULARIO DINÁMICO
 * ============================================================ */
public function post_get_form()
{
    if (!Input::is_ajax()) return;

    $action = Input::post('action', 'add');
    $parent_id = (int) Input::post('parent_id', 0);
    $id = (int) Input::post('id', 0);

    $account = ($action === 'edit')
        ? Model_Accounts_Chart::find($id)
        : new Model_Accounts_Chart(['parent_id' => $parent_id]);

    if (!$account) {
        echo '<div class="alert alert-danger">Cuenta no encontrada.</div>';
        return;
    }

    echo View::forge('admin/finanzas/plancuentas/form_partial', [
        'account' => $account,
        'action'  => $action
    ]);
}

/* ============================================================
 * AJAX: GUARDAR CAMBIOS (CREAR / EDITAR)
 * ============================================================ */
public function post_save_ajax()
{
    if (!Input::is_ajax()) return;

    $action = Input::post('action', 'add');
    $id = (int) Input::post('id', 0);
    $parent_id = (int) Input::post('parent_id', 0);

    $val = Validation::forge();
    $val->add_field('code', 'Código', 'required');
    $val->add_field('name', 'Nombre', 'required');

    if ($val->run()) {
        $account = ($action === 'edit')
            ? Model_Accounts_Chart::find($id)
            : new Model_Accounts_Chart();

        if (!$account) {
            echo json_encode(['success' => false, 'error' => 'Cuenta no encontrada.']);
            return;
        }

        $account->code            = Input::post('code');
        $account->name            = Input::post('name');
        $account->type            = Input::post('type');
        $account->level           = Input::post('level');
        $account->parent_id       = $parent_id ?: null;
        $account->currency_id     = Input::post('currency_id', null);
        $account->is_active       = Input::post('is_active', 1);
        $account->is_cash_account = Input::post('is_cash_account', 0);
        $account->is_confidential = Input::post('is_confidential', 0);
        $account->annex24_code    = Input::post('annex24_code', null);
        $account->account_class   = Input::post('account_class', null);
        $account->updated_at      = time();
        if ($action === 'add') $account->created_at = time();

        $account->save();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'errors' => $val->error()]);
    }
}


	/**
	 * ELIMINAR
	 *
	 * CAMBIA EL VALOR DEL CAMPO DELETED PARA UN BORRADO LOGICO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($account_id = 0)
{
    #HELPER DE PERMISO PARA ELIMINAR
    if (!Helper_Permission::can('plan_cuentas', 'delete')) {
        Session::set_flash('error', 'No tienes permiso para eliminar cuentas.');
        Response::redirect('admin/finanzas/plancuentas');
    }

    # VALIDACIÓN DE ID
    if($account_id == 0 || !is_numeric($account_id))
    {
        Response::redirect('admin/finanzas/plancuentas');
    }

    # BUSCA LA CUENTA
    $account = Model_Accounts_Chart::query()
        ->where('id', $account_id)
        ->get_one();

    if(!empty($account))
    {
        // --- Si usas borrado lógico: (asegúrate que tu tabla tiene el campo 'deleted') ---
        // $account->deleted = 1;
        // $account->save();
        
        // --- Si usas borrado físico ---
        if($account->delete())
        {
            Session::set_flash('success', 'Se eliminó la cuenta <b>'.$account->code.' - '.$account->name.'</b> correctamente.');
        }
        else
        {
            Session::set_flash('error', 'No se pudo eliminar la cuenta.');
        }
    }

    Response::redirect('admin/finanzas/plancuentas');
}

/**
 * ============================================================
 * IMPORTAR PLAN DE CUENTAS DESDE CSV (SAP B1)
 * ------------------------------------------------------------
 * - Usa los campos: code, name, type, parent_code, level,
 *   annex24_code, account_class, is_active, is_confidential,
 *   currency_code
 * - Crea padres vacíos si no existen
 * - Busca la moneda en base local según currency_code
 * - Incluye logs de detalle
 * ============================================================
 */
public function action_importar_csv()
{
    $file = Input::file('archivo');
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        return Response::forge(json_encode(['success' => false, 'msg' => 'Archivo inválido.']));
    }

    $path = $file['tmp_name'];
    $handle = fopen($path, 'r');
    if (!$handle) {
        return Response::forge(json_encode(['success' => false, 'msg' => 'No se pudo leer el archivo.']));
    }

    $header = fgetcsv($handle);
    $count  = 0;
    $creadas = 0;
    $actualizadas = 0;
    $padres_creados = 0;
    $errores = [];
    $creados_cache = [];

    while (($row = fgetcsv($handle)) !== false) {
        $data = array_combine($header, $row);
        if (!$data || empty($data['code'])) continue;

        try {
            $code        = trim($data['code']);
            $parent_code = trim($data['parent_code']);
            $name        = trim($data['name']);
            $currency    = trim($data['currency_code']);

            // ============================================================
            // BUSCAR O CREAR CUENTA PADRE
            // ============================================================
            $parent = null;
            if (!empty($parent_code)) {
                $parent = Model_Accounts_Chart::query()
                    ->where('code', $parent_code)
                    ->get_one();

                if (!$parent && !in_array($parent_code, $creados_cache)) {
                    $parent = Model_Accounts_Chart::forge([
                        'code'         => $parent_code,
                        'name'         => 'Cuenta padre automática',
                        'type'         => 'Sin Clasificar',
                        'account_class'=> 'Sin Clasificar',
                        'level'        => max(((int)$data['level'] - 1), 1),
                        'currency_id'  => $this->get_currency_id($currency),
                        'is_active'    => 1,
                        'deleted'      => 0,
                        'created_at'   => time(),
                        'updated_at'   => time(),
                    ]);
                    $parent->save();
                    $padres_creados++;
                    $creados_cache[] = $parent_code;
                    \Log::info("[IMPORT][PADRE NUEVO] Creada cuenta padre {$parent_code}");
                }
            }

            // ============================================================
            // BUSCAR O CREAR CUENTA PRINCIPAL
            // ============================================================
            $account = Model_Accounts_Chart::query()
                ->where('code', $code)
                ->get_one();

            $is_new = false;
            if (!$account) {
                $account = Model_Accounts_Chart::forge();
                $account->created_at = time();
                $is_new = true;
            }

            // ============================================================
// ASIGNAR DATOS TAL COMO VIENEN EN CSV (con normalización UTF-8)
// ============================================================
$name_clean = trim(mb_convert_encoding($name, 'UTF-8', 'auto'));
$type_clean = trim(mb_convert_encoding($data['type'] ?? '', 'UTF-8', 'auto'));
$class_clean = trim(mb_convert_encoding($data['account_class'] ?? '', 'UTF-8', 'auto'));

$account->code            = $code;
$account->name            = $name_clean ?: 'Sin nombre';
$account->type            = $type_clean !== '' ? $type_clean : 'Sin Clasificar';
$account->account_class   = $class_clean !== '' ? $class_clean : 'Sin Clasificar';
$account->parent_id       = $parent ? $parent->id : null;
$account->level           = (int) ($data['level'] ?: 1);
$account->currency_id     = $this->get_currency_id($currency);
$account->is_active       = (int) ($data['is_active'] ?: 1);
$account->is_confidential = (int) ($data['is_confidential'] ?: 0);
$account->annex24_code    = trim(mb_convert_encoding($data['annex24_code'] ?? '', 'UTF-8', 'auto'));
$account->deleted         = 0;
$account->updated_at      = time();

            // ============================================================
            // GUARDAR REGISTRO
            // ============================================================
            if ($account->save()) {
                $count++;
                $is_new ? $creadas++ : $actualizadas++;
            }

        } catch (Exception $e) {
            $errores[] = [
                'code' => $data['code'],
                'msg'  => $e->getMessage()
            ];
            \Log::error("[IMPORT][ERROR] {$data['code']} → " . $e->getMessage());
        }
    }

    fclose($handle);

    // ============================================================
    // LOG FINAL Y RESPUESTA JSON
    // ============================================================
    \Log::info("[PLAN_CUENTAS][IMPORT] Importadas: {$count}, Nuevas: {$creadas}, Actualizadas: {$actualizadas}, Padres creados: {$padres_creados}");
    return Response::forge(json_encode([
        'success' => true,
        'msg' => "Importadas {$count} cuentas ({$creadas} nuevas, {$actualizadas} actualizadas, {$padres_creados} padres creados).",
        'errores' => $errores
    ]))->set_header('Content-Type', 'application/json');
}

/**
 * Buscar ID de moneda según código (MXN, USD, etc.)
 */
private function get_currency_id($code)
{
    if (!$code) return null;

    $currency = Model_Currency::query()
        ->where('code', strtoupper($code))
        ->get_one();

    if (!$currency) {
        \Log::warning("[IMPORT][CURRENCY] Moneda no encontrada: {$code}");
        return null;
    }

    return $currency->id;
}




}
