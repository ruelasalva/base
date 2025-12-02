<?php
/**
 * CONTROLADOR ADMIN_CATALOGO_UNIDADES (Generales)
 *
 * @package  app
 * @extends  Controller_Admin
 *
 * Permisos sugeridos:
 *  - catalogo_unidades:view
 *  - catalogo_unidades:create
 *  - catalogo_unidades:edit
 *  - catalogo_unidades:delete
 */

class Controller_Admin_Catalogo_Generales_Unidades extends Controller_Admin
{
    /**
     * BEFORE
     */
    public function before()
    {
        parent::before();

        if (!Auth::check()) {
            Session::set_flash('error', 'Debes iniciar sesión.');
            Response::redirect('admin/login');
        }
    }

    /**
     * INDEX
     *
     * @param string $search
     * @return void
     */
    public function action_index($search = '')
    {
        # Permiso
        if (!Helper_Permission::can('catalogo_unidades', 'view')) {
            Session::set_flash('error', 'No tienes permiso para ver unidades.');
            Response::redirect('admin');
        }

        # Vars
        $data         = array();
        $units_info   = array();
        $per_page     = 100;

        # Query base (muestra SAT e internas, sin borrados)
        $units = Model_Sat_Unit::query()->where('deleted', 0);

        # Búsqueda
        if ($search !== '') {
            $original_search = $search;
            $search = str_replace('+', ' ', rawurldecode($search));
            $search = str_replace(' ', '%', $search);

            $units = $units->where_open()
                ->where(DB::expr("CONCAT(`t0`.`code`, ' ', `t0`.`name`, ' ', IFNULL(`t0`.`abbreviation`, ''), ' ', IFNULL(`t0`.`description`, ''))"), 'like', '%'.$search.'%')
            ->where_close();
        }

        # Paginación
        $config = array(
            'name'           => 'admin',
            'pagination_url' => Uri::current(),
            'total_items'    => $units->count(),
            'per_page'       => $per_page,
            'uri_segment'    => 'pagina',
        );
        $pagination = Pagination::forge('sat_units', $config);

        $units = $units->order_by('name', 'asc')
            ->rows_limit($pagination->per_page)
            ->rows_offset($pagination->offset)
            ->get();

        if (!empty($units)) {
            foreach ($units as $u) {
                $units_info[] = array(
                    'id'                 => $u->id,
                    'code'               => $u->code,
                    'name'               => $u->name,
                    'abbreviation'       => $u->abbreviation,
                    'description'        => $u->description,
                    'conversion_factor'  => $u->conversion_factor,
                    'is_internal'        => (int)$u->is_internal,
                    'active'             => (int)$u->active,
                    'deleted'            => (int)$u->deleted,
                    'created_at'         => $u->created_at,
                    'updated_at'         => $u->updated_at,
                );
            }
        }

        $data['units']      = $units_info;
        $data['search']     = str_replace('%', ' ', $search);
        $data['pagination'] = $pagination->render();

        $this->template->title   = 'Unidades (SAT e internas)';
        $this->template->content = View::forge('admin/catalogo/generales/unidades/index', $data, false);
    }

    /**
     * BUSCAR
     */
    public function action_buscar()
    {
        if (Input::method() == 'POST') {
            $data = array(
                'search' => (Input::post('search') != '') ? Input::post('search') : '',
            );

            $val = Validation::forge('search');
            $val->add_field('search', 'search', 'max_length[100]');

            if ($val->run($data)) {
                $search = str_replace(' ', '+', $val->validated('search'));
                $search = str_replace('*', '', $search);
                $search = ($val->validated('search') != '') ? $search : '';
                Response::redirect('admin/catalogo/generales/unidades/index/'.$search);
            } else {
                Response::redirect('admin/catalogo/generales/unidades');
            }
        } else {
            Response::redirect('admin/catalogo/generales/unidades');
        }
    }

    /**
     * AGREGAR
     */
    public function action_agregar()
    {
        if (!Helper_Permission::can('catalogo_unidades', 'create')) {
            Session::set_flash('error', 'No tienes permiso para crear unidades.');
            Response::redirect('admin/catalogo/generales/unidades');
        }

        $data    = array();
        $classes = array();
        $fields  = array('code','name','abbreviation','description','conversion_factor','active','is_internal');

        foreach ($fields as $f) {
            $classes[$f] = array('form-group'=>null,'form-control'=>null);
        }

        if (Input::method() == 'POST') {
            $val = Validation::forge('unit');
            $val->add_callable('Rules');
            $val->add_field('code', 'código', 'required|min_length[1]|max_length[16]');
            $val->add_field('name', 'nombre', 'required|min_length[1]|max_length[128]');
            $val->add_field('abbreviation', 'abreviatura', 'max_length[16]');
            $val->add_field('description', 'descripción', 'max_length[255]');
            $val->add_field('conversion_factor', 'factor conversión', 'required|valid_number');

            if ($val->run()) {
                $unit = new Model_Sat_Unit(array(
                    'code'              => $val->validated('code'),
                    'name'              => $val->validated('name'),
                    'abbreviation'      => Input::post('abbreviation'),
                    'description'       => Input::post('description'),
                    'conversion_factor' => (float)Input::post('conversion_factor', 1),
                    'is_internal'       => 1,          # Marcamos internas creadas aquí
                    'active'            => (int)Input::post('active', 1),
                    'deleted'           => 0,
                    'created_at'        => time(),
                    'updated_at'        => time(),
                ));

                if ($unit->save()) {
                    Session::set_flash('success', 'Se agregó la unidad <b>'.$unit->name.'</b> correctamente.');
                    Response::redirect('admin/catalogo/generales/unidades');
                }
            } else {
                Session::set_flash('error', 'Errores en el formulario. Verifica por favor.');
                $data['errors'] = $val->error();

                foreach ($classes as $name => $class) {
                    $classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
                    $classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
                    $data[$name]                    = Input::post($name);
                }
            }
        }

        $data['classes'] = $classes;

        $this->template->title   = 'Agregar unidad';
        $this->template->content = View::forge('admin/catalogo/generales/unidades/agregar', $data);
    }

    /**
     * INFO
     */
    public function action_info($unit_id = 0)
    {
        if (!Helper_Permission::can('catalogo_unidades', 'view')) {
            Session::set_flash('error', 'No tienes permiso para ver unidades.');
            Response::redirect('admin');
        }

        if ($unit_id == 0 || !is_numeric($unit_id)) {
            Response::redirect('admin/catalogo/generales/unidades');
        }

        $data = array();

        $unit = Model_Sat_Unit::query()
            ->where('id', $unit_id)
            ->where('deleted', 0)
            ->get_one();

        if (!empty($unit)) {
            $data['id']                = $unit->id;
            $data['code']              = $unit->code;
            $data['name']              = $unit->name;
            $data['abbreviation']      = $unit->abbreviation;
            $data['description']       = $unit->description;
            $data['conversion_factor'] = $unit->conversion_factor;
            $data['is_internal']       = (int)$unit->is_internal;
            $data['active']            = (int)$unit->active;
            $data['created_at']        = $unit->created_at;
            $data['updated_at']        = $unit->updated_at;
        } else {
            Response::redirect('admin/catalogo/generales/unidades');
        }

        $this->template->title   = 'Información de la unidad';
        $this->template->content = View::forge('admin/catalogo/generales/unidades/info', $data);
    }

    /**
     * EDITAR
     */
    public function action_editar($unit_id = 0)
    {
        if (!Helper_Permission::can('catalogo_unidades', 'edit')) {
            Session::set_flash('error', 'No tienes permiso para editar unidades.');
            Response::redirect('admin/catalogo/generales/unidades');
        }

        if ($unit_id == 0 || !is_numeric($unit_id)) {
            Response::redirect('admin/catalogo/generales/unidades');
        }

        $data    = array();
        $classes = array();
        $fields  = array('code','name','abbreviation','description','conversion_factor','active');

        foreach ($fields as $f) {
            $classes[$f] = array('form-group'=>null,'form-control'=>null);
        }

        $unit = Model_Sat_Unit::query()
            ->where('id', $unit_id)
            ->where('deleted', 0)
            ->get_one();

        if (empty($unit)) {
            Response::redirect('admin/catalogo/generales/unidades');
        }

        # Carga inicial
        $data['code']              = $unit->code;
        $data['name']              = $unit->name;
        $data['abbreviation']      = $unit->abbreviation;
        $data['description']       = $unit->description;
        $data['conversion_factor'] = $unit->conversion_factor;
        $data['active']            = $unit->active;
        $data['is_internal']       = (int)$unit->is_internal;

        if (Input::method() == 'POST') {
            $val = Validation::forge('unit');
            $val->add_callable('Rules');
            $val->add_field('code', 'código', 'required|min_length[1]|max_length[16]');
            $val->add_field('name', 'nombre', 'required|min_length[1]|max_length[128]');
            $val->add_field('abbreviation', 'abreviatura', 'max_length[16]');
            $val->add_field('description', 'descripción', 'max_length[255]');
            $val->add_field('conversion_factor', 'factor conversión', 'required|valid_number');

            if ($val->run()) {
                # Si la unidad es SAT (is_internal = 0), permitimos actualizar solo flags y descripción; 
                # pero respetamos tu libertad: aquí habilitamos todo por consistencia interna.
                $unit->code              = $val->validated('code');
                $unit->name              = $val->validated('name');
                $unit->abbreviation      = Input::post('abbreviation');
                $unit->description       = Input::post('description');
                $unit->conversion_factor = (float) Input::post('conversion_factor', 1);
                $unit->active            = (int)Input::post('active', 1);
                $unit->updated_at        = time();

                if ($unit->save()) {
                    Session::set_flash('success', 'Se actualizó la información de la unidad <b>'.$unit->name.'</b> correctamente.');
                    Response::redirect('admin/catalogo/generales/unidades/editar/'.$unit_id);
                }
            } else {
                Session::set_flash('error', 'Errores en el formulario. Verifica por favor.');
                $data['errors'] = $val->error();

                foreach ($classes as $name => $class) {
                    $classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
                    $classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
                    $data[$name]                    = Input::post($name);
                }
            }
        }

        $data['id']      = $unit_id;
        $data['classes'] = $classes;

        $this->template->title   = 'Editar unidad';
        $this->template->content = View::forge('admin/catalogo/generales/unidades/editar', $data);
    }

    /**
     * ELIMINAR (borrado lógico)
     */
    public function action_eliminar($unit_id = 0)
    {
        if (!Helper_Permission::can('catalogo_unidades', 'delete')) {
            Session::set_flash('error', 'No tienes permiso para eliminar unidades.');
            Response::redirect('admin/catalogo/generales/unidades');
        }

        if ($unit_id == 0 || !is_numeric($unit_id)) {
            Response::redirect('admin/catalogo/generales/unidades');
        }

        $unit = Model_Sat_Unit::query()
            ->where('id', $unit_id)
            ->where('deleted', 0)
            ->get_one();

        if (!empty($unit)) {
            $unit->deleted = 1;
            if ($unit->save()) {
                Session::set_flash('success', 'Se eliminó la unidad <b>'.$unit->name.'</b> correctamente.');
            }
        }

        Response::redirect('admin/catalogo/generales/unidades');
    }
}
