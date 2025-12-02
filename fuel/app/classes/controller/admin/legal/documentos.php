<?php
/**
 * ADMIN LEGAL
 *
 * MÓDULO DE ADMINISTRACIÓN DE DOCUMENTOS LEGALES
 *
 * ACCIONES: index, buscar, info, agregar, editar, eliminar
 */
class Controller_Admin_Legal_Documentos extends Controller_Admin
{

    /**
     * INDEX
     *
     * MUESTRA EL LISTADO DE DOCUMENTOS LEGALES
     *
     * @access  public
     * @return  Void
     */
    public function action_index($search = '')
    {
        # VARIABLES
        $data      = [];
        $documents = [];
        $per_page  = 50;

        # QUERY BASE
        $query = Model_Legal_Document::query()->where('id', '>=', 0);

        # FILTRO DE BÚSQUEDA LIBRE
        if ($search != '') {
            $original_search = $search;
            $search = str_replace('+', ' ', rawurldecode($search));
            $search = str_replace(' ', '%', $search);

            $query->where(DB::expr("CONCAT(`t0`.`title`, ' ', `t0`.`category`, ' ', `t0`.`type`, ' ', `t0`.`shortcode`)"), 'like', '%'.$search.'%');
        }

        # FILTROS AVANZADOS (GET)
        $filters = [
            'title'    => Input::get('title'),
            'type'     => Input::get('type'),
            'category' => Input::get('category'),
            'active'   => Input::get('active'),
            'has_file' => Input::get('has_file'),
        ];

        if (!empty($filters['title'])) {
            $query->where('title', 'like', '%'.$filters['title'].'%');
        }
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if ($filters['active'] !== null && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }
        if ($filters['has_file'] !== null && $filters['has_file'] !== '') {
            if ($filters['has_file'] == '1') {
                $query->where('upload_path', '!=', '');
            } else {
                $query->where_open();
                $query->where('upload_path', 'IS', null);
                $query->or_where('upload_path', '=', '');
                $query->where_close();
            }
        }

        # PAGINACIÓN
        $config = [
            'name'           => 'admin',
            'pagination_url' => Uri::current(),
            'total_items'    => $query->count(),
            'per_page'       => $per_page,
            'uri_segment'    => 'pagina',
            'show_first'     => true,
            'show_last'      => true,
        ];
        $pagination = Pagination::forge('admin', $config);

        # EJECUTAR QUERY
        $results = $query->order_by('updated_at', 'desc')
            ->rows_limit($pagination->per_page)
            ->rows_offset($pagination->offset)
            ->get();

        if (!empty($results)) {
            foreach ($results as $doc) {
                $documents[] = [
                    'id'            => $doc->id,
                    'title'         => $doc->title,
                    'category'      => ucfirst($doc->category),
                    'type'          => str_replace('_',' ', ucfirst($doc->type)),
                    'version'       => $doc->version,
                    'active'        => $doc->active,
                    'allow_edit'    => $doc->allow_edit,
                    'allow_download'=> $doc->allow_download,
                    'required'      => $doc->required,
                    'upload_path'   => $doc->upload_path,
                    'updated_at'    => $doc->updated_at ?  date('d/m/Y - H:i', is_numeric($doc->updated_at) ? $doc->updated_at : strtotime($doc->updated_at))  : 'No ha sido actualizado',
                ];
            }
        }

        # PASAR DATOS A VISTA
        $data['filters']    = $filters;
        $data['documents']  = $documents;
        $data['search']     = str_replace('%', ' ', $search);
        $data['pagination'] = $pagination->render();

        $this->template->title   = 'Documentos Legales';
        $this->template->content = View::forge('admin/legal/documentos/index', $data, false);
    }


    /**
     * BUSCAR
     *
     * REDIRECCIONA A LA URL DE BUSCAR DOCUMENTOS
     *
     * @access  public
     * @return  Void
     */
    public function action_buscar()
    {
        # SI SE UTILIZÓ EL MÉTODO POST
        if (Input::method() == 'POST')
        {
            # SE OBTIENEN LOS VALORES
            $data = array(
                'search' => ($_POST['search'] != '') ? $_POST['search'] : '',
            );

            # SE CREA LA VALIDACIÓN DE LOS CAMPOS
            $val = Validation::forge('search');
            $val->add_callable('Rules');
            $val->add_field('search', 'search', 'max_length[100]');

            # SI NO HAY NINGÚN PROBLEMA CON LA VALIDACIÓN
            if ($val->run($data))
            {
                # SE REEMPLAZAN ALGUNOS CARACTERES
                $search = str_replace(' ', '+', $val->validated('search'));
                $search = str_replace('*', '', $search);

                # SE ALMACENA LA CADENA DE BÚSQUEDA
                $search = ($val->validated('search') != '') ? $search : '';

                # SE REDIRECCIONA A INDEX CON LA BÚSQUEDA
                Response::redirect('admin/legal/documentos/index/'.$search);
            }
            else
            {
                # SE REDIRECCIONA AL LISTADO GENERAL
                Response::redirect('admin/legal/documentos');
            }
        }
        else
        {
            # SE REDIRECCIONA AL LISTADO GENERAL
            Response::redirect('admin/legal/documentos');
        }
    }




    /**
     * INFO
     * VISTA DETALLE DEL DOCUMENTO
     */
    public function action_info($id = null)
    {
        $doc = Model_Legal_Document::find($id);

        if (!$doc) {
            \Session::set_flash('error','Documento no encontrado.');
            \Response::redirect('admin/legal/documentos/index');
        }

        // Histórico
        $versions = \DB::select()
            ->from('legal_documents_versions')
            ->where('document_id', $doc->id)
            ->order_by('updated_at', 'desc')
            ->execute()
            ->as_array();

        // Última acción = la más reciente del histórico
        $last_change = !empty($versions) ? $versions[0] : null;

        $data['doc'] = $doc;
        $data['versions'] = $versions;
        $data['last_change'] = $last_change;

        $this->template->title   = 'Gestión - Detalle Documento Legal';
        $this->template->content = \View::forge('admin/legal/documentos/info', $data);
    }



    /**
     * AGREGAR DOCUMENTO LEGAL
     */
    public function action_agregar()
    {
        if (Input::method() == 'POST')
        {
            $doc = Model_Legal_Document::forge();
            $doc->title          = Input::post('title');
            $doc->category       = Input::post('category');
            $doc->type           = Input::post('type');
            $doc->content        = Input::post('content');
            $doc->shortcode      = Input::post('shortcode');
            $doc->allow_edit     = Input::post('allow_edit', 0);
            $doc->allow_download = Input::post('allow_download', 0);
            $doc->active         = Input::post('active', 0);
            $doc->version        = '1.0';
            $doc->required       = '0';
            $doc->created_at     = time();
            $doc->updated_at     = time();

            # SUBIDA DE ARCHIVO
    if (!empty($_FILES['upload_path']['name']))
    {
        $upload_config = [
            'path'          => DOCROOT.'assets/uploads/legal/',
            'randomize'     => false, // NO aleatorio
            'ext_whitelist' => ['pdf','doc','docx'],
        ];
        \Upload::process($upload_config);

        if (\Upload::is_valid()) {
            \Upload::save();
            $file = \Upload::get_files(0);

            # Nombre más descriptivo: titulo + fecha
            $ext        = strtolower(pathinfo($file['saved_as'], PATHINFO_EXTENSION));
            $slug_title = preg_replace('/[^a-z0-9]+/i', '_', strtolower($doc->title));
            $date_str   = date('Ymd_His');
            $new_name   = $slug_title.'_'.$date_str.'.'.$ext;

            # Renombrar archivo con validación
            $old_path = DOCROOT.'assets/uploads/legal/'.$file['saved_as'];
            $new_path = DOCROOT.'assets/uploads/legal/'.$new_name;
            if (!@rename($old_path, $new_path)) {
                \Log::error("[LEGAL] No se pudo renombrar archivo: {$old_path} → {$new_path}");
            } else {
                $doc->upload_path = 'assets/uploads/legal/'.$new_name;

                // ================================================
                // PROCESAR WORD
                // ================================================
                if (in_array($ext, ['doc','docx'])) {
                    try {
                        // INTENTO 1: convertir a HTML con PhpWord
                        $phpWord    = \PhpOffice\PhpWord\IOFactory::load($new_path);
                        $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');

                        ob_start();
                        $htmlWriter->save("php://output");
                        $htmlContent = ob_get_clean();

                        if (empty($doc->content)) {
                            $doc->content = $htmlContent;
                        }

                        \Log::info("[LEGAL] Word procesado como HTML: {$new_name}");
                    } catch (\Exception $e) {
                        \Log::warning("[LEGAL] Error procesando Word a HTML ({$new_name}): ".$e->getMessage());

                        // INTENTO 2: fallback a texto plano con ZipArchive
                        if (class_exists('ZipArchive')) {
                            $zip = new \ZipArchive;
                            if ($zip->open($new_path) === true) {
                                $xml = $zip->getFromName("word/document.xml");
                                $zip->close();

                                if ($xml) {
                                    $plainText = strip_tags($xml);
                                    if (empty($doc->content)) {
                                        $doc->content = nl2br($plainText);
                                    }
                                    \Log::info("[LEGAL] Word fallback a texto plano: {$new_name}");
                                } else {
                                    \Log::error("[LEGAL] No se encontró document.xml en {$new_name}");
                                }
                            } else {
                                \Log::error("[LEGAL] No se pudo abrir DOCX como ZIP: {$new_name}");
                            }
                        }
                    }
                }

                // ================================================
                // PROCESAR PDF
                // ================================================
                else if ($ext === 'pdf') {
                    try {
                        if (class_exists('\Smalot\PdfParser\Parser')) {
                            $parser = new \Smalot\PdfParser\Parser();
                            $pdf    = $parser->parseFile($new_path);
                            $text   = $pdf->getText();

                            if (empty($doc->content)) {
                                $doc->content = nl2br($text);
                            }
                            \Log::info("[LEGAL] PDF procesado: {$new_name}");
                        } else {
                            \Log::error("[LEGAL] Librería Smalot\PdfParser no disponible.");
                        }
                    } catch (\Exception $e) {
                        \Log::error("[LEGAL] Error PDF: ".$e->getMessage());

                        // Fallback: abrir como texto plano
                        $raw = @file_get_contents($new_path);
                        if ($raw) {
                            $plainText = strip_tags($raw);
                            if (empty($doc->content)) {
                                $doc->content = nl2br($plainText);
                            }
                            \Log::warning("[LEGAL] PDF fallback a texto plano: {$new_name}");
                        }
                    }
                }
            }
        }
    }


            if ($doc->save())
            {
                \Log::info("[LEGAL] Documento creado ID={$doc->id}, {$doc->title}, version={$doc->version}");
                \Session::set_flash('success','Documento legal creado correctamente (Versión '.$doc->version.').');
                \Response::redirect('admin/legal/documentos/index');
            }
            else
            {
                \Session::set_flash('error','No se pudo guardar el documento.');
            }
        }

        $this->template->title   = 'Gestión - Agregar Documento Legal';
        $this->template->content = \View::forge('admin/legal/documentos/agregar');
    }



    /**
     * EDITAR DOCUMENTO LEGAL
     */
    /**
     * EDITAR DOCUMENTO LEGAL
     */
    public function action_editar($id = null)
    {
        $doc = Model_Legal_Document::find($id);

        if (!$doc) {
            \Session::set_flash('error','Documento no encontrado.');
            \Response::redirect('admin/legal/documentos/index');
        }

        if (Input::method() == 'POST')
        {
            # ===================================================
            # 1. DETECTAR SI HAY ARCHIVO NUEVO
            # ===================================================
            $has_new_file = !empty($_FILES['upload_path']['name']);

            # ===================================================
            # 2. CALCULAR NUEVA VERSIÓN
            # ===================================================
            $old_version = $doc->version ?: "1.0";
            $version_parts = explode('.', $old_version);
            $major = (int) $version_parts[0];
            $minor = isset($version_parts[1]) ? (int) $version_parts[1] : 0;

            if ($has_new_file) {
                $major++;
                $minor = 0;
            } else {
                if ($minor < 9) {
                    $minor++;
                } else {
                    $major++;
                    $minor = 0;
                }
            }

            $new_version = $major.'.'.$minor;

            # ===================================================
            # 3. GUARDAR VERSIÓN ANTERIOR EN HISTÓRICO
            # ===================================================
            $version_record = Model_Legal_Document_Version::forge([
                'document_id' => $doc->id,
                'change_type' => $has_new_file ? 'archivo' : 'edicion',
                'version'     => $old_version, // guardamos la versión vieja
                'title'       => $doc->title,
                'category'    => $doc->category,
                'type'        => $doc->type,
                'content'     => $doc->content,
                'shortcode'   => $doc->shortcode,
                'upload_path' => $doc->upload_path,
                'created_at'  => $doc->created_at,
                'updated_at'  => $doc->updated_at,
            ]);
            $version_record->save();

            # ===================================================
            # 4. ACTUALIZAR CAMPOS DEL DOCUMENTO
            # ===================================================
            $doc->title          = Input::post('title');
            $doc->category       = Input::post('category');
            $doc->type           = Input::post('type');
            $doc->shortcode      = Input::post('shortcode');
            $doc->allow_edit     = Input::post('allow_edit', 0);
            $doc->allow_download = Input::post('allow_download', 0);
            $doc->active         = Input::post('active', 0);
            $doc->content        = Input::post('content');
            $doc->updated_at     = time();
            $doc->version        = $new_version; // NUEVA VERSIÓN

            # ===================================================
    # 5. PROCESAR ARCHIVO NUEVO (si aplica)
    # ===================================================
    if ($has_new_file)
    {
        $upload_config = [
            'path'          => DOCROOT.'assets/uploads/legal/',
            'randomize'     => false,
            'ext_whitelist' => ['pdf','doc','docx'],
        ];
        \Upload::process($upload_config);

        if (\Upload::is_valid()) {
            \Upload::save();
            $file = \Upload::get_files(0);

            $ext        = strtolower(pathinfo($file['saved_as'], PATHINFO_EXTENSION));
            $slug_title = preg_replace('/[^a-z0-9]+/i', '_', strtolower($doc->title));
            $date_str   = date('Ymd_His');
            $new_name   = $slug_title.'_v'.$doc->version.'_'.$date_str.'.'.$ext;

            $old_path = DOCROOT.'assets/uploads/legal/'.$file['saved_as'];
            $new_path = DOCROOT.'assets/uploads/legal/'.$new_name;

            if (!@rename($old_path, $new_path)) {
                \Log::error("[LEGAL] No se pudo renombrar archivo: {$old_path} → {$new_path}");
            } else {
                $doc->upload_path = 'assets/uploads/legal/'.$new_name;

                // ================================================
                // PROCESAR WORD
                // ================================================
                if (in_array($ext, ['doc','docx'])) {
                    try {
                        // INTENTO 1: convertir a HTML con PhpWord
                        $phpWord    = \PhpOffice\PhpWord\IOFactory::load($new_path);
                        $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');

                        ob_start();
                        $htmlWriter->save("php://output");
                        $htmlContent = ob_get_clean();

                        if (empty($doc->content)) {
                            $doc->content = $htmlContent;
                        }

                        \Log::info("[LEGAL] Word procesado como HTML (edición): {$new_name}");
                    } catch (\Exception $e) {
                        \Log::warning("[LEGAL] Error Word a HTML en {$new_name}: ".$e->getMessage());

                        // INTENTO 2: fallback a texto plano con ZipArchive
                        if (class_exists('ZipArchive')) {
                            $zip = new \ZipArchive;
                            if ($zip->open($new_path) === true) {
                                $xml = $zip->getFromName("word/document.xml");
                                $zip->close();

                                if ($xml) {
                                    $plainText = strip_tags($xml);
                                    if (empty($doc->content)) {
                                        $doc->content = nl2br($plainText);
                                    }
                                    \Log::info("[LEGAL] Fallback Word a texto plano: {$new_name}");
                                } else {
                                    \Log::error("[LEGAL] document.xml no encontrado en {$new_name}");
                                }
                            } else {
                                \Log::error("[LEGAL] No se pudo abrir DOCX como ZIP: {$new_name}");
                            }
                        }
                    }
                }

                // ================================================
                // PROCESAR PDF
                // ================================================
                else if ($ext === 'pdf') {
                    try {
                        if (class_exists('\Smalot\PdfParser\Parser')) {
                            $parser = new \Smalot\PdfParser\Parser();
                            $pdf    = $parser->parseFile($new_path);
                            $text   = $pdf->getText();

                            if (empty($doc->content)) {
                                $doc->content = nl2br($text);
                            }
                            \Log::info("[LEGAL] PDF procesado en edición: {$new_name}");
                        } else {
                            \Log::error("[LEGAL] Librería Smalot\PdfParser no disponible.");
                        }
                    } catch (\Exception $e) {
                        \Log::error("[LEGAL] Error PDF edición: ".$e->getMessage());

                        // Fallback: abrir como texto plano
                        $raw = @file_get_contents($new_path);
                        if ($raw) {
                            $plainText = strip_tags($raw);
                            if (empty($doc->content)) {
                                $doc->content = nl2br($plainText);
                            }
                            \Log::warning("[LEGAL] PDF fallback a texto plano: {$new_name}");
                        }
                    }
                }
            }
        }
    }


            # ===================================================
            # 6. GUARDAR DOCUMENTO
            # ===================================================
            if ($doc->save()) {
                \Log::info("[LEGAL] Documento actualizado ID={$doc->id}, nueva versión={$doc->version}");
                \Session::set_flash('success','Documento actualizado correctamente (Versión '.$doc->version.').');
                \Response::redirect('admin/legal/documentos/info/'.$doc->id);
            } else {
                \Session::set_flash('error','No se pudo actualizar el documento.');
            }
        }

        $data['doc'] = $doc;
        $this->template->title   = 'Gestión - Editar Documento Legal';
        $this->template->content = \View::forge('admin/legal/documentos/editar', $data);
    }




    /**
     * ELIMINAR (LÓGICO)
     */
    public function action_eliminar($id = null)
    {
        $doc = Model_Legal_Document::find($id);
        if ($doc) {
            $doc->active = 1; // 1 = inactivo
            $doc->save();
            \Log::info("[LEGAL] Documento inactivado ID={$doc->id}, {$doc->title}");
            Session::set_flash('success','Documento legal inactivado.');
        } else {
            Session::set_flash('error','Documento no encontrado.');
        }
        Response::redirect('admin/legal/documentos/index');
    }

    /**
     * DESCARGAR DOCUMENTO LEGAL (PDF)
     *
     * @param int|null $id
     * @return void
     */
    public function action_download($id = null)
    {
        is_null($id) and \Response::redirect('admin/legal/documentos');

        $doc = Model_Legal_Document::find($id);
        if (!$doc) {
            \Session::set_flash('error','Documento no encontrado.');
            \Response::redirect('admin/legal/documentos');
        }

        // Revisar modo (download = forzar descarga, preview = abrir en navegador)
        $mode = Input::get('mode', 'download');
        $forceDownload = ($mode === 'download');

        Helper_Legal::export_pdf($doc, null, $forceDownload);
    }

    /**
     * VER VERSIÓN HISTÓRICA DE UN DOCUMENTO LEGAL
     */
    public function action_version($id = null)
    {
        $version = Model_Legal_Document_Version::find($id);

        if (!$version) {
            \Session::set_flash('error','Versión no encontrada.');
            \Response::redirect('admin/legal/documentos/index');
        }

        $data['version'] = $version;

        $this->template->title   = 'Gestión - Versión Documento Legal';
        $this->template->content = \View::forge('admin/legal/documentos/version', $data);
    }


    /**
     * DESCARGAR ARCHIVO ORIGINAL
     *
     * @param int|null $id
     * @return void
     */
    public function action_file($id = null)
    {
        // Validar ID
        if (is_null($id)) {
            \Session::set_flash('error', 'Documento no especificado.');
            \Response::redirect('admin/legal/documentos');
        }

        // Buscar documento
        $doc = \Model_Legal_Document::find($id);

        if (!$doc || empty($doc->upload_path)) {
            \Session::set_flash('error', 'Archivo no disponible para este documento.');
            \Response::redirect('admin/legal/documentos/info/'.$id);
        }

        // Ruta física
        $file_path = DOCROOT . $doc->upload_path;

        if (!file_exists($file_path)) {
            \Log::error("[LEGAL] Archivo no encontrado en disco: {$file_path}");
            \Session::set_flash('error', 'El archivo ya no existe en el servidor.');
            \Response::redirect('admin/legal/documentos/info/'.$id);
        }

        // Nombre de salida (más limpio)
        $filename = basename($file_path);

        // Headers seguros
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));

        // Limpieza de buffer y salida
        flush();
        readfile($file_path);
        exit;
    }




}
