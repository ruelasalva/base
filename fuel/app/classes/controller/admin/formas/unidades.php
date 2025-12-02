<?php

/**
 * CONTROLADOR ADMIN_FORMAS_UNIDADES (SAT)
 *
 * Catálogo SAT de Unidades de Medida.
 * - Lista SAT + internas (ambas), con filtros y paginación
 * - CRUD básico
 * - Importar CSV con log en assets/catalogo
 * - Exportar CSV
 * - Descargar plantilla CSV (se genera si no existe)
 *
 * Permisos sugeridos:
 *  - sat_unidades: view, create, edit, delete, import, export
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Formas_Unidades extends Controller_Admin
{
    /** Ruta base para archivos del catálogo */
    private $catalog_dir;
    private $template_path;

    public function before()
    {
        parent::before();

        if (!Auth::check()) {
            Session::set_flash('error', 'Debes iniciar sesión.');
            Response::redirect('admin/login');
        }

        $this->catalog_dir   = DOCROOT . 'assets/catalogo/';
        $this->template_path = $this->catalog_dir . 'sat_unidades_template.csv';

        if (!is_dir($this->catalog_dir)) {
            @mkdir($this->catalog_dir, 0777, true);
        }
        if (!is_dir($this->catalog_dir.'imports')) {
            @mkdir($this->catalog_dir.'imports', 0777, true);
        }
        if (!is_dir($this->catalog_dir.'logs')) {
            @mkdir($this->catalog_dir.'logs', 0777, true);
        }
    }

    /**
     * INDEX
     * Lista unidades SAT e internas, permite búsqueda.
     */
    public function action_index($search = '')
    {
        if (!Helper_Permission::can('sat_unidades', 'view')) {
            Session::set_flash('error', 'No tienes permiso para ver Unidades SAT.');
            Response::redirect('admin');
        }

        $data       = array();
        $per_page   = 100;
        $units_info = array();

        $units = Model_Sat_Unit::query()
            ->where('deleted', 0);

        if ($search != '') {
            $original = $search;
            $search   = str_replace('+', ' ', rawurldecode($search));
            $search   = str_replace(' ', '%', $search);

            $units = $units->where_open()
                ->where('code', 'like', '%'.$search.'%')
                ->or_where('name', 'like', '%'.$search.'%')
                ->or_where('abbreviation', 'like', '%'.$search.'%')
                ->or_where('description', 'like', '%'.$search.'%')
            ->where_close();
        }

        $config = array(
            'name'           => 'admin',
            'pagination_url' => Uri::current(),
            'total_items'    => $units->count(),
            'per_page'       => $per_page,
            'uri_segment'    => 'pagina',
            'show_first'     => true,
            'show_last'      => true,
        );
        $pagination = Pagination::forge('sat_units', $config);

        $units = $units
            ->order_by('is_internal', 'asc') // primero SAT, luego internas
            ->order_by('name', 'asc')
            ->rows_limit($pagination->per_page)
            ->rows_offset($pagination->offset)
            ->get();

        if (!empty($units)) {
            foreach ($units as $u) {
                $units_info[] = array(
                    'id'                => $u->id,
                    'code'              => $u->code,
                    'name'              => $u->name,
                    'abbreviation'      => $u->abbreviation,
                    'description'       => $u->description,
                    'conversion_factor' => $u->conversion_factor,
                    'is_internal'       => (int)$u->is_internal,
                    'active'            => (int)$u->active,
                    'deleted'           => (int)$u->deleted,
                    'created_at'        => $u->created_at,
                    'updated_at'        => $u->updated_at,
                );
            }
        }

        $data['units']      = $units_info;
        $data['search']     = str_replace('%', ' ', $search);
        $data['pagination'] = $pagination->render();

        $this->template->title   = 'SAT · Unidades de Medida';
        $this->template->content = View::forge('admin/unidades/index', $data, false);
    }

    /**
     * BUSCAR → redirige con parámetro en URL
     */
    public function action_buscar()
    {
        if (Input::method() == 'POST') {
            $data = array('search' => ($_POST['search'] != '') ? $_POST['search'] : '');
            $val  = Validation::forge('search');
            $val->add_field('search', 'search', 'max_length[120]');

            if ($val->run($data)) {
                $search = str_replace(' ', '+', $val->validated('search'));
                $search = str_replace('*', '', $search);
                $search = ($val->validated('search') != '') ? $search : '';
                Response::redirect('admin/formas/unidades/index/'.$search);
            } else {
                Response::redirect('admin/formas/unidades');
            }
        } else {
            Response::redirect('admin/formas/unidades');
        }
    }

    /**
     * AGREGAR
     * Crea una unidad. Por default se considera SAT (=0) si no se marca como interna.
     */
    public function action_agregar()
    {
        if (!Helper_Permission::can('sat_unidades', 'create')) {
            Session::set_flash('error', 'No tienes permiso para crear Unidades SAT.');
            Response::redirect('admin/formas/unidades');
        }

        $data    = array();
        $classes = array();
        $fields  = array('code','name','abbreviation','description','conversion_factor','is_internal','active');

        foreach ($fields as $f) {
            $classes[$f] = array('form-group'=>null, 'form-control'=>null);
        }

        if (Input::method() == 'POST') {
            $val = Validation::forge('sat_unit');
            $val->add_callable('Rules');
            $val->add_field('code', 'clave', 'required|max_length[10]');
            $val->add_field('name', 'nombre', 'required|max_length[128]');
            $val->add_field('abbreviation', 'abreviatura', 'max_length[16]');
            $val->add_field('conversion_factor', 'factor', 'valid_number');

            if ($val->run()) {
                // Unicidad por code no eliminado
                $exists = Model_Sat_Unit::query()
                    ->where('code', $val->validated('code'))
                    ->where('deleted', 0)
                    ->get_one();
                if ($exists) {
                    Session::set_flash('error', 'Ya existe una unidad con la clave <b>'.$val->validated('code').'</b>.');
                } else {
                    $unit = new Model_Sat_Unit(array(
                        'code'              => strtoupper(trim($val->validated('code'))),
                        'name'              => trim($val->validated('name')),
                        'abbreviation'      => trim(Input::post('abbreviation')),
                        'description'       => trim(Input::post('description')),
                        'conversion_factor' => (Input::post('conversion_factor') !== '') ? (float)Input::post('conversion_factor') : 1,
                        'is_internal'       => (int)Input::post('is_internal', 0), // 0 SAT, 1 interna
                        'active'            => (int)Input::post('active', 1),
                        'deleted'           => 0,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                    ));

                    if ($unit->save()) {
                        Session::set_flash('success', 'Se agregó la unidad <b>'.$unit->name.'</b> correctamente.');
                        Response::redirect('admin/formas/unidades');
                    }
                }
            } else {
                Session::set_flash('error', 'Verifica el formulario.');
                $data['errors'] = $val->error();
                foreach ($classes as $name => $class) {
                    $classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
                    $classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
                    $data[$name] = Input::post($name);
                }
            }
        }

        $data['classes'] = $classes;

        $this->template->title   = 'SAT · Agregar Unidad';
        $this->template->content = View::forge('admin/unidades/agregar', $data);
    }

    /**
     * INFO
     */
    public function action_info($unit_id = 0)
    {
        if (!Helper_Permission::can('sat_unidades', 'view')) {
            Session::set_flash('error', 'No tienes permiso para ver Unidades SAT.');
            Response::redirect('admin');
        }

        if ($unit_id == 0 || !is_numeric($unit_id)) {
            Response::redirect('admin/formas/unidades');
        }

        $u = Model_Sat_Unit::query()
            ->where('id', $unit_id)
            ->where('deleted', 0)
            ->get_one();

        if (!$u) {
            Response::redirect('admin/formas/unidades');
        }

        $data = array(
            'id'                => $u->id,
            'code'              => $u->code,
            'name'              => $u->name,
            'abbreviation'      => $u->abbreviation,
            'description'       => $u->description,
            'conversion_factor' => $u->conversion_factor,
            'is_internal'       => (int)$u->is_internal,
            'active'            => (int)$u->active,
            'created_at'        => $u->created_at,
            'updated_at'        => $u->updated_at,
        );

        $this->template->title   = 'SAT · Unidad';
        $this->template->content = View::forge('admin/unidades/info', $data);
    }

    /**
     * EDITAR
     */
    public function action_editar($unit_id = 0)
    {
        if (!Helper_Permission::can('sat_unidades', 'edit')) {
            Session::set_flash('error', 'No tienes permiso para editar Unidades SAT.');
            Response::redirect('admin/formas/unidades');
        }

        if ($unit_id == 0 || !is_numeric($unit_id)) {
            Response::redirect('admin/formas/unidades');
        }

        $data    = array();
        $classes = array();
        $fields  = array('code','name','abbreviation','description','conversion_factor','is_internal','active');

        foreach ($fields as $f) {
            $classes[$f] = array('form-group'=>null, 'form-control'=>null);
        }

        $u = Model_Sat_Unit::query()
            ->where('id', $unit_id)
            ->where('deleted', 0)
            ->get_one();

        if (!$u) {
            Response::redirect('admin/formas/unidades');
        }

        // Datos por defecto a la vista
        $data['code']              = $u->code;
        $data['name']              = $u->name;
        $data['abbreviation']      = $u->abbreviation;
        $data['description']       = $u->description;
        $data['conversion_factor'] = $u->conversion_factor;
        $data['is_internal']       = (int)$u->is_internal;
        $data['active']            = (int)$u->active;

        if (Input::method() == 'POST') {
            $val = Validation::forge('sat_unit');
            $val->add_callable('Rules');
            $val->add_field('code', 'clave', 'required|max_length[10]');
            $val->add_field('name', 'nombre', 'required|max_length[128]');
            $val->add_field('abbreviation', 'abreviatura', 'max_length[16]');
            $val->add_field('conversion_factor', 'factor', 'valid_number');

            if ($val->run()) {
                // si cambian code validar duplicado
                $new_code = strtoupper(trim($val->validated('code')));
                if ($new_code !== $u->code) {
                    $dup = Model_Sat_Unit::query()
                        ->where('code', $new_code)
                        ->where('deleted', 0)
                        ->get_one();
                    if ($dup) {
                        Session::set_flash('error', 'Ya existe una unidad con la clave <b>'.$new_code.'</b>.');
                        Response::redirect('admin/formas/unidades/editar/'.$unit_id);
                    }
                }

                $u->code              = $new_code;
                $u->name              = trim($val->validated('name'));
                $u->abbreviation      = trim(Input::post('abbreviation'));
                $u->description       = trim(Input::post('description'));
                $u->conversion_factor = (Input::post('conversion_factor') !== '') ? (float)Input::post('conversion_factor') : 1;
                $u->is_internal       = (int)Input::post('is_internal', 0);
                $u->active            = (int)Input::post('active', 1);
                $u->updated_at        = time();

                if ($u->save()) {
                    Session::set_flash('success', 'Se actualizó la unidad <b>'.$u->name.'</b> correctamente.');
                    Response::redirect('admin/formas/unidades/editar/'.$unit_id);
                }
            } else {
                Session::set_flash('error', 'Verifica el formulario.');
                $data['errors'] = $val->error();
                foreach ($classes as $name => $class) {
                    $classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
                    $classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
                    $data[$name] = Input::post($name);
                }
            }
        }

        $data['id']      = $unit_id;
        $data['classes'] = $classes;

        $this->template->title   = 'SAT · Editar Unidad';
        $this->template->content = View::forge('admin/unidades/editar', $data);
    }

    /**
     * ELIMINAR (borrado lógico)
     */
    public function action_eliminar($unit_id = 0)
    {
        if (!Helper_Permission::can('sat_unidades', 'delete')) {
            Session::set_flash('error', 'No tienes permiso para eliminar Unidades SAT.');
            Response::redirect('admin/formas/unidades');
        }

        if ($unit_id == 0 || !is_numeric($unit_id)) {
            Response::redirect('admin/formas/unidades');
        }

        $u = Model_Sat_Unit::query()
            ->where('id', $unit_id)
            ->where('deleted', 0)
            ->get_one();

        if (!empty($u)) {
            $u->deleted    = 1;
            $u->updated_at = time();
            if ($u->save()) {
                Session::set_flash('success', 'Se eliminó la unidad <b>'.$u->name.'</b> correctamente.');
            }
        }

        Response::redirect('admin/formas/unidades');
    }

    /**
     * IMPORTAR CSV (SAT)
     * Encabezados esperados:
     *  ClaveUnidad,Nombre,Descripción,Abreviatura
     * Reglas:
     *  - Upsert por ClaveUnidad sobre registros deleted=0
     *  - Marca como SAT (is_internal = 0)
     *  - active=1 por defecto
     * Log:
     *  assets/catalogo/logs/sat_unidades_import_log_YYYYmmdd_His.csv
     */
    public function action_importar()
    {
        if (!Helper_Permission::can('sat_unidades', 'import')) {
            Session::set_flash('error', 'No tienes permiso para importar Unidades SAT.');
            Response::redirect('admin/formas/unidades');
        }

        if (Input::method() != 'POST') {
            Response::redirect('admin/formas/unidades');
        }

        try {
            if (empty($_FILES['csv_file']['name'])) {
                throw new \RuntimeException('No se subió archivo.');
            }

            $upload_cfg = array(
                'path'          => $this->catalog_dir.'imports/',
                'randomize'     => false,
                'ext_whitelist' => array('csv'),
                'auto_rename'   => true,
            );

            \Upload::process($upload_cfg);

            if (!\Upload::is_valid()) {
                $errors = \Upload::get_errors(0);
                $msgs   = array();
                if (isset($errors['errors'])) {
                    foreach ($errors['errors'] as $e) {
                        $msgs[] = $e['message'];
                    }
                }
                throw new \RuntimeException('Error de carga: '.implode(', ', $msgs));
            }

            \Upload::save();
            $file = \Upload::get_files(0);
            $src  = $file['saved_to'].$file['saved_as'];

            // Mover a nombre con timestamp
            $ts      = date('Ymd_His');
            $newName = 'sat_unidades_import_'.$ts.'.csv';
            $dst     = $this->catalog_dir.'imports/'.$newName;
            if (!@rename($src, $dst)) {
                $dst = $src;
            }

            // Log file
            $log_path = $this->catalog_dir.'logs/sat_unidades_import_log_'.$ts.'.csv';
            $log_fp   = fopen($log_path, 'w');
            fputcsv($log_fp, array('accion','clave','nombre','resultado','detalle'));

            // Leer CSV
            $handle = fopen($dst, 'r');
            if (!$handle) {
                throw new \RuntimeException('No se pudo abrir el CSV para lectura.');
            }

            $header = fgetcsv($handle);
            if (!$header) {
                throw new \RuntimeException('El CSV está vacío.');
            }

            // Normalizar encabezados
            $map = $this->map_headers($header); // devuelve indices para cada campo esperado

            $created = 0; $updated = 0; $skipped = 0;

            while (($row = fgetcsv($handle)) !== false) {
                $code = $this->cell($row, $map['ClaveUnidad']);
                $name = $this->cell($row, $map['Nombre']);
                $desc = $this->cell($row, $map['Descripción']);
                $abbr = $this->cell($row, $map['Abreviatura']);

                $code = strtoupper(trim($code));
                if ($code === '') {
                    $skipped++;
                    fputcsv($log_fp, array('skip','', '', 'sin_accion', 'Clave vacía'));
                    continue;
                }

                // Upsert por code
                $u = Model_Sat_Unit::query()
                    ->where('code', $code)
                    ->where('deleted', 0)
                    ->get_one();

                if ($u) {
                    $u->name              = ($name !== '') ? $name : $u->name;
                    $u->abbreviation      = ($abbr !== '') ? $abbr : $u->abbreviation;
                    $u->description       = ($desc !== '') ? $desc : $u->description;
                    $u->is_internal       = 0;  // SAT
                    $u->active            = 1;
                    $u->updated_at        = time();
                    $u->save();
                    $updated++;
                    fputcsv($log_fp, array('update',$code,$name,'ok','Actualizado'));
                } else {
                    $unit = new Model_Sat_Unit(array(
                        'code'              => $code,
                        'name'              => ($name !== '') ? $name : $code,
                        'abbreviation'      => $abbr,
                        'description'       => $desc,
                        'conversion_factor' => 1,
                        'is_internal'       => 0,
                        'active'            => 1,
                        'deleted'           => 0,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                    ));
                    $unit->save();
                    $created++;
                    fputcsv($log_fp, array('create',$code,$name,'ok','Creado'));
                }
            }

            fclose($handle);
            fclose($log_fp);

            Session::set_flash('success',
                'Importación completada. Creados: '.$created.' · Actualizados: '.$updated.' · Omitidos: '.$skipped.
                '<br>Log: <a href="'.Uri::create(str_replace(DOCROOT,'',$log_path)).'" target="_blank">descargar</a>'
            );
            Response::redirect('admin/formas/unidades');
        }
        catch (\Exception $e) {
            \Log::error('[SAT_UNIDADES][IMPORT] '.$e->getMessage());
            Session::set_flash('error', 'Error importando CSV: '.$e->getMessage());
            Response::redirect('admin/formas/unidades');
        }
    }

    /**
     * EXPORTAR CSV
     */
    public function action_exportar()
    {
        if (!Helper_Permission::can('sat_unidades', 'export')) {
            Session::set_flash('error', 'No tienes permiso para exportar Unidades SAT.');
            Response::redirect('admin/formas/unidades');
        }

        $rows = Model_Sat_Unit::query()
            ->where('deleted', 0)
            ->order_by('is_internal','asc')
            ->order_by('name','asc')
            ->get();

        $csv = fopen('php://temp', 'w+');
        fputcsv($csv, array('ClaveUnidad','Nombre','Descripción','Abreviatura','Factor','Origen','Activo'));

        foreach ($rows as $r) {
            fputcsv($csv, array(
                $r->code,
                $r->name,
                $r->description,
                $r->abbreviation,
                $r->conversion_factor,
                $r->is_internal ? 'Interna' : 'SAT',
                $r->active ? '1' : '0',
            ));
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        $filename = 'sat_unidades_'.date('Ymd_His').'.csv';
        return Response::forge($content, 200)
            ->set_header('Content-Type', 'text/csv; charset=utf-8')
            ->set_header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    /**
     * TEMPLATE CSV
     * Si no existe, lo genera. Si existe, lo entrega.
     */
    public function action_template()
    {
        $exists = is_file($this->template_path);
        if (!$exists) {
            $csv = fopen($this->template_path, 'w');
            // Encabezados oficiales
            fputcsv($csv, array('ClaveUnidad','Nombre','Descripcion','Abreviatura'));
            // Ejemplos mínimos
            fputcsv($csv, array('H87','Pieza','Unidad de pieza','pza'));
            fputcsv($csv, array('KGM','Kilogramo','Unidad de masa','kg'));
            fputcsv($csv, array('LTR','Litro','Unidad de volumen','l'));
            fclose($csv);
        }

        $content = @file_get_contents($this->template_path);
        if ($content === false) {
            Session::set_flash('error', 'No se pudo leer la plantilla.');
            Response::redirect('admin/formas/unidades');
        }

        return Response::forge($content, 200)
            ->set_header('Content-Type', 'text/csv; charset=utf-8')
            ->set_header('Content-Disposition', 'attachment; filename="sat_unidades_template.csv"');
    }

    /* ==========================
       Helpers privados
       ========================== */

    /** Mapea encabezados del CSV a índices esperados */
    private function map_headers(array $header)
    {
        $expected = array(
            'ClaveUnidad'  => null,
            'Nombre'       => null,
            'Descripcion'  => null,
            'Abreviatura'  => null,
        );

        // normalizar para match flexible
        $norm = array();
        foreach ($header as $i => $h) {
            $h = trim($h);
            $h = str_replace(array("\xEF\xBB\xBF"), '', $h); // BOM
            $hlow = mb_strtolower($h, 'UTF-8');
            $norm[$hlow] = $i;
        }

        $alias = array(
            'claveunidad' => 'ClaveUnidad',
            'clave_unidad'=> 'ClaveUnidad',
            'nombre'      => 'Nombre',
            'descripción' => 'Descripción',
            'descripcion' => 'Descripción',
            'abreviatura' => 'Abreviatura',
            'abrev'       => 'Abreviatura',
        );

        foreach ($alias as $k => $target) {
            if (isset($norm[$k])) {
                $expected[$target] = $norm[$k];
            }
        }

        // Validación mínima: clave y nombre
        if ($expected['ClaveUnidad'] === null || $expected['Nombre'] === null) {
            throw new \RuntimeException('Encabezados requeridos no encontrados. Se esperan: ClaveUnidad, Nombre, Descripción, Abreviatura.');
        }

        return $expected;
    }

    /** Obtiene celda segura por índice */
    private function cell(array $row, $idx)
    {
        if ($idx === null) return '';
        return isset($row[$idx]) ? trim($row[$idx]) : '';
    }
}
