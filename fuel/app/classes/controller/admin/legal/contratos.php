<?php
/**
 * CONTROLADOR ADMIN – CONTRATOS LEGALES
 * 
 * Gestiona los contratos legales (proveedores, empleados, clientes, etc.)
 * Usa el modelo: Model_Legal_Contract
 * 
 * Estructura:
 *  - index(): listado con filtros y paginación
 *  - agregar(): crear contrato con PDF opcional
 *  - info(): detalle completo con vista previa
 *  - editar(): actualizar datos o archivo
 *  - eliminar(): borrado lógico
 */

class Controller_Admin_Legal_Contratos extends Controller_Admin
{
    /**
     * INDEX
     * Listado general con filtros por búsqueda, categoría y estado
     */
    public function action_index()
    {
        if (!in_array(Auth::get('group'), [20,25,50,100])) Response::redirect('admin');

        $search    = Input::get('search', '');
        $category  = Input::get('category', '');
        $status    = Input::get('status', '');
        $per_page  = 50;

        $query = Model_Legal_Contract::query()
            ->related('user')
            ->related('type')
            ->related('document')
            ->where('deleted', 0)
            ->order_by('id', 'desc');

        if ($search) {
            $query->where_open()
                ->where('title', 'like', "%{$search}%")
                ->or_where('code', 'like', "%{$search}%")
                ->or_where('user.username', 'like', "%{$search}%")
            ->where_close();
        }

        if ($category) $query->where('category', $category);
        if ($status !== '') $query->where('status', (int) $status);

        $config = [
            'pagination_url' => Uri::current(),
            'total_items'    => $query->count(),
            'per_page'       => $per_page,
            'uri_segment'    => 'pagina',
            'show_first'     => true,
            'show_last'      => true,
        ];
        $pagination = Pagination::forge('contratos', $config);

        $contratos = $query
            ->rows_limit($pagination->per_page)
            ->rows_offset($pagination->offset)
            ->get();

        $data = [
            'contratos'  => $contratos,
            'search'     => $search,
            'category'   => $category,
            'status'     => $status,
            'pagination' => $pagination->render(),
        ];

        $this->template->title   = 'Contratos Legales';
        $this->template->content = View::forge('admin/legal/contratos/index', $data, false);
    }

    /**
     * AGREGAR CONTRATO LEGAL
     */
    public function action_agregar()
    {
        if (!in_array(Auth::get('group'), [20,25,50,100])) Response::redirect('admin');

        if (Input::method() == 'POST')
        {
            try {
                // ===========================
                // VALIDACIÓN
                // ===========================
                $val = Validation::forge();
                $val->add('title', 'Título')->add_rule('required');
                $val->add('category', 'Categoría')->add_rule('required');
                $val->add('user_id', 'Usuario')->add_rule('required');

                if (!$val->run()) {
                    throw new Exception('Faltan campos obligatorios.');
                }

                $start = Input::post('start_date');
                $end   = Input::post('end_date');
                if ($start && $end && strtotime($end) < strtotime($start)) {
                    throw new Exception('La fecha final no puede ser anterior a la inicial.');
                }

                // ===========================
                // CREAR REGISTRO
                // ===========================
                $contract = Model_Legal_Contract::forge([
                    'title'             => Input::post('title'),
                    'code'              => Input::post('code'),
                    'category'          => Input::post('category'),
                    'user_id'           => Input::post('user_id'),
                    'legal_document_id' => Input::post('legal_document_id') ?: null,
                    'document_type_id'  => Input::post('document_type_id') ?: null,
                    'start_date'        => $start,
                    'end_date'          => $end,
                    'status'            => (int) Input::post('status', 0),
                    'description'       => Input::post('description'),
                    'authorized_by'     => Auth::get('id'),
                    'is_global'         => (int) Input::post('is_global', 0),
                    'deleted'           => 0,
                    'created_at'        => time(),
                    'updated_at'        => time(),
                ]);

                // ===========================
                // ARCHIVO PDF
                // ===========================
                if (!empty($_FILES['contract_file']['name']))
                {
                    $upload_path = DOCROOT.'assets/uploads/legal/contracts/';
                    if (!is_dir($upload_path)) mkdir($upload_path, 0777, true);

                    $upload_config = [
                        'path'          => $upload_path,
                        'ext_whitelist' => ['pdf'],
                        'randomize'     => false,
                        'auto_rename'   => true,
                    ];
                    \Upload::process($upload_config);

                    if (\Upload::is_valid()) {
                        \Upload::save();
                        $file = \Upload::get_files(0);

                        $slug_title = preg_replace('/[^a-z0-9]+/i', '_', strtolower($contract->title));
                        $date_str   = date('Ymd_His');
                        $new_name   = 'contrato_'.$slug_title.'_'.$date_str.'.pdf';
                        $old_path   = $upload_path.$file['saved_as'];
                        $new_path   = $upload_path.$new_name;

                        @rename($old_path, $new_path);
                        $contract->file_path = 'assets/uploads/legal/contracts/'.$new_name;

                        // EXTRAER TEXTO PDF (opcional)
                        try {
                            if (class_exists('\Smalot\PdfParser\Parser')) {
                                $parser = new \Smalot\PdfParser\Parser();
                                $pdf    = $parser->parseFile($new_path);
                                $text   = $pdf->getText();
                                if (empty($contract->description)) {
                                    $contract->description = nl2br($text);
                                }
                            }
                        } catch (Exception $e) {
                            \Log::warning('[CONTRATOS][PDF] '.$e->getMessage());
                        }
                    }
                    else {
                        $errors = array_column(\Upload::get_errors(0)['errors'], 'message');
                        throw new Exception('Error al subir el archivo: '.implode(', ', $errors));
                    }
                }

                $contract->save();
                \Log::info("[CONTRATOS][ADD] ID={$contract->id}, Usuario={$contract->user_id}");
                Session::set_flash('success', 'Contrato agregado correctamente.');
                Response::redirect('admin/legal/contratos');
            }
            catch (Exception $e) {
                \Log::error('[CONTRATOS][ERROR] '.$e->getMessage());
                Session::set_flash('error', $e->getMessage());
            }
        }

        $data = [
            'usuarios'   => Model_User::find('all'),
            'documentos' => Model_Legal_Document::find('all', ['where' => ['deleted' => 0]]),
            'tipos'      => Model_Document_Type::find('all', ['where' => ['deleted' => 0]]),
        ];

        $this->template->title   = 'Agregar Contrato';
        $this->template->content = View::forge('admin/legal/contratos/agregar', $data, false);
    }

    /**
     * INFO
     * Muestra detalle del contrato
     */
    public function action_info($id = null)
    {
        if (!in_array(Auth::get('group'), [20,25,50,100])) Response::redirect('admin');

        $contract = Model_Legal_Contract::find($id, ['related' => ['user', 'type', 'document', 'authorizer']]);
        if (!$contract) {
            Session::set_flash('error', 'Contrato no encontrado.');
            Response::redirect('admin/legal/contratos');
        }

        $this->template->title   = 'Detalle de Contrato';
        $this->template->content = View::forge('admin/legal/contratos/info', ['contract' => $contract], false);
    }

    /**
     * EDITAR
     */
    public function action_editar($id = null)
    {
        if (!in_array(Auth::get('group'), [20,25,50,100])) Response::redirect('admin');

        $contract = Model_Legal_Contract::find($id);
        if (!$contract) {
            Session::set_flash('error', 'Contrato no encontrado.');
            Response::redirect('admin/legal/contratos');
        }

        if (Input::method() == 'POST')
        {
            try {
                $contract->title         = Input::post('title');
                $contract->code          = Input::post('code');
                $contract->category      = Input::post('category');
                $contract->user_id       = Input::post('user_id');
                $contract->start_date    = Input::post('start_date');
                $contract->end_date      = Input::post('end_date');
                $contract->status        = (int) Input::post('status', 0);
                $contract->description   = Input::post('description');
                $contract->is_global     = (int) Input::post('is_global', 0);
                $contract->authorized_by = Auth::get('id');
                $contract->updated_at    = time();

                if ($contract->end_date && $contract->start_date && strtotime($contract->end_date) < strtotime($contract->start_date)) {
                    throw new Exception('La fecha final no puede ser anterior a la inicial.');
                }

                // REEMPLAZAR ARCHIVO
                if (!empty($_FILES['contract_file']['name'])) {
                    $dir = DOCROOT.'assets/uploads/legal/contracts/';
                    if (!is_dir($dir)) mkdir($dir, 0777, true);
                    $ext = strtolower(pathinfo($_FILES['contract_file']['name'], PATHINFO_EXTENSION));
                    if ($ext !== 'pdf') throw new Exception('Solo se permiten archivos PDF.');
                    $new_name = 'contrato_'.$contract->id.'_'.date('Ymd_His').'.pdf';
                    $new_path = $dir.$new_name;
                    move_uploaded_file($_FILES['contract_file']['tmp_name'], $new_path);
                    $contract->file_path = 'assets/uploads/legal/contracts/'.$new_name;
                }

                $contract->save();
                Session::set_flash('success', 'Contrato actualizado correctamente.');
                Response::redirect('admin/legal/contratos/info/'.$id);
            }
            catch (Exception $e) {
                \Log::error('[CONTRATOS][EDITAR][ERROR] '.$e->getMessage());
                Session::set_flash('error', $e->getMessage());
            }
        }

        $data = [
            'contract'   => $contract,
            'usuarios'   => Model_User::find('all'),
            'documentos' => Model_Legal_Document::find('all', ['where' => ['deleted' => 0]]),
            'tipos'      => Model_Document_Type::find('all', ['where' => ['deleted' => 0]]),
        ];

        $this->template->title   = 'Editar Contrato';
        $this->template->content = View::forge('admin/legal/contratos/editar', $data, false);
    }

    /**
     * ELIMINAR (borrado lógico)
     */
    public function action_eliminar($id = null)
    {
        if (!in_array(Auth::get('group'), [20,25,50,100])) Response::redirect('admin');

        $contract = Model_Legal_Contract::find($id);
        if (!$contract) {
            Session::set_flash('error', 'Contrato no encontrado.');
        } else {
            $contract->deleted = 1;
            $contract->save();
            Session::set_flash('success', 'Contrato eliminado correctamente.');
        }
        Response::redirect('admin/legal/contratos');
    }
}
