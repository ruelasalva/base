<?php
/**
 * Controller_Admin_Sat
 * 
 * Controlador para el módulo SAT - Gestión fiscal y descarga de CFDIs
 * 
 * @package    Fuel
 * @subpackage Controllers
 * @category   Admin
 */

class Controller_Admin_Sat extends Controller_Admin
{
    /**
     * Index - Dashboard del módulo SAT
     */
    public function action_index()
    {
        // Verificar permisos
        if (!Helper_Permission::can('sat', 'view')) {
            Session::set_flash('error', 'No tienes permisos para acceder al módulo SAT');
            Response::redirect('admin');
        }
        
        $tenant_id = Session::get('tenant_id', 1);
        
        // Obtener estadísticas
        $data['stats'] = Helper_SAT::get_statistics($tenant_id);
        
        // Obtener credenciales configuradas
        $data['credentials'] = Helper_SAT::get_credentials($tenant_id);
        
        // Obtener últimas descargas
        $data['recent_downloads'] = DB::select('*')
            ->from('sat_downloads')
            ->where('tenant_id', $tenant_id)
            ->order_by('created_at', 'DESC')
            ->limit(10)
            ->execute()
            ->as_array();
        
        // Últimos CFDIs descargados
        $data['recent_cfdis'] = DB::select('*')
            ->from('sat_cfdis')
            ->where('tenant_id', $tenant_id)
            ->order_by('created_at', 'DESC')
            ->limit(10)
            ->execute()
            ->as_array();
        
        $data['title'] = 'SAT - Dashboard';
        $data['content'] = View::forge('admin/sat/index', $data);
        
        return View::forge(Helper_Template::get_template_file(), $data);
    }
    
    /**
     * Credentials - Gestión de credenciales SAT
     */
    public function action_credentials()
    {
        if (!Helper_Permission::can('sat', 'credentials')) {
            Session::set_flash('error', 'No tienes permisos para gestionar credenciales SAT');
            Response::redirect('admin/sat');
        }
        
        $tenant_id = Session::get('tenant_id', 1);
        
        if (Input::method() === 'POST') {
            // Guardar credenciales
            $data = [
                'rfc' => Input::post('rfc'),
                'password' => Input::post('password'),
            ];
            
            // Procesar archivos CSD
            if (Upload::is_valid('csd_cer') && Upload::is_valid('csd_key')) {
                Upload::process([
                    'path' => APPPATH . 'tmp',
                    'auto_rename' => true,
                ]);
                
                if (Upload::is_valid()) {
                    $files = Upload::get_files();
                    
                    foreach ($files as $file) {
                        if ($file['field'] === 'csd_cer') {
                            $data['csd_cer'] = $file['saved_to'] . $file['saved_as'];
                        }
                        if ($file['field'] === 'csd_key') {
                            $data['csd_key'] = $file['saved_to'] . $file['saved_as'];
                        }
                    }
                    
                    $data['csd_password'] = Input::post('csd_password');
                }
            }
            
            // Procesar archivos FIEL
            if (Upload::is_valid('fiel_cer') && Upload::is_valid('fiel_key')) {
                Upload::process([
                    'path' => APPPATH . 'tmp',
                    'auto_rename' => true,
                ]);
                
                if (Upload::is_valid()) {
                    $files = Upload::get_files();
                    
                    foreach ($files as $file) {
                        if ($file['field'] === 'fiel_cer') {
                            $data['fiel_cer'] = $file['saved_to'] . $file['saved_as'];
                        }
                        if ($file['field'] === 'fiel_key') {
                            $data['fiel_key'] = $file['saved_to'] . $file['saved_as'];
                        }
                    }
                    
                    $data['fiel_password'] = Input::post('fiel_password');
                }
            }
            
            $result = Helper_SAT::save_credentials($data, $tenant_id);
            
            if ($result['success']) {
                Session::set_flash('success', $result['message']);
                
                // Limpiar archivos temporales
                if (isset($data['csd_cer'])) File::delete($data['csd_cer']);
                if (isset($data['csd_key'])) File::delete($data['csd_key']);
                if (isset($data['fiel_cer'])) File::delete($data['fiel_cer']);
                if (isset($data['fiel_key'])) File::delete($data['fiel_key']);
                
                Response::redirect('admin/sat');
            } else {
                Session::set_flash('error', $result['message']);
            }
        }
        
        // Obtener credenciales actuales
        $data['credentials'] = Helper_SAT::get_credentials($tenant_id);
        $data['title'] = 'Configurar Credenciales SAT';
        $data['content'] = View::forge('admin/sat/credentials', $data);
        
        return View::forge(Helper_Template::get_template_file(), $data);
    }
    
    /**
     * Download - Descargar CFDIs desde el SAT
     */
    public function action_download()
    {
        if (!Helper_Permission::can('sat', 'download')) {
            Session::set_flash('error', 'No tienes permisos para descargar CFDIs');
            Response::redirect('admin/sat');
        }
        
        $tenant_id = Session::get('tenant_id', 1);
        
        if (Input::method() === 'POST') {
            $params = [
                'date_from' => Input::post('date_from'),
                'date_to' => Input::post('date_to'),
                'type' => Input::post('type', 'recibidos')
            ];
            
            $result = Helper_SAT::start_download($params, $tenant_id);
            
            if ($result['success']) {
                Session::set_flash('success', $result['message']);
                Response::redirect('admin/sat/download/' . $result['download_id']);
            } else {
                Session::set_flash('error', $result['message']);
            }
        }
        
        $data['title'] = 'Descargar CFDIs';
        $data['content'] = View::forge('admin/sat/download', $data);
        
        return View::forge(Helper_Template::get_template_file(), $data);
    }
    
    /**
     * CFDIs - Listado de CFDIs descargados
     */
    public function action_cfdis()
    {
        if (!Helper_Permission::can('sat', 'view')) {
            Session::set_flash('error', 'No tienes permisos para ver CFDIs');
            Response::redirect('admin/sat');
        }
        
        $tenant_id = Session::get('tenant_id', 1);
        
        // Obtener filtros
        $filters = [
            'uuid' => Input::get('uuid'),
            'rfc_emisor' => Input::get('rfc_emisor'),
            'rfc_receptor' => Input::get('rfc_receptor'),
            'tipo_comprobante' => Input::get('tipo_comprobante'),
            'fecha_desde' => Input::get('fecha_desde'),
            'fecha_hasta' => Input::get('fecha_hasta'),
            'estado_sat' => Input::get('estado_sat'),
        ];
        
        // Paginación
        $page = Input::get('page', 1);
        $per_page = 50;
        $offset = ($page - 1) * $per_page;
        
        // Buscar CFDIs
        $data['cfdis'] = Helper_SAT::search_cfdis($filters, $tenant_id, $per_page, $offset);
        
        // Contar total para paginación
        $query = DB::select(DB::expr('COUNT(*) as total'))
            ->from('sat_cfdis')
            ->where('tenant_id', $tenant_id);
        
        if (!empty($filters['uuid'])) {
            $query->where('uuid', 'LIKE', '%' . $filters['uuid'] . '%');
        }
        
        $data['total_count'] = $query->execute()->get('total');
        $data['filters'] = $filters;
        $data['page'] = $page;
        $data['per_page'] = $per_page;
        $data['total_pages'] = ceil($data['total_count'] / $per_page);
        
        $data['title'] = 'CFDIs Descargados';
        $data['content'] = View::forge('admin/sat/cfdis', $data);
        
        return View::forge(Helper_Template::get_template_file(), $data);
    }
    
    /**
     * View CFDI - Ver detalle de un CFDI
     */
    public function action_view($cfdi_id = null)
    {
        if (!Helper_Permission::can('sat', 'view')) {
            Session::set_flash('error', 'No tienes permisos para ver CFDIs');
            Response::redirect('admin/sat');
        }
        
        if (!$cfdi_id) {
            Session::set_flash('error', 'ID de CFDI no especificado');
            Response::redirect('admin/sat/cfdis');
        }
        
        $tenant_id = Session::get('tenant_id', 1);
        
        // Obtener CFDI
        $data['cfdi'] = DB::select('*')
            ->from('sat_cfdis')
            ->where('id', $cfdi_id)
            ->where('tenant_id', $tenant_id)
            ->execute()
            ->current();
        
        if (!$data['cfdi']) {
            Session::set_flash('error', 'CFDI no encontrado');
            Response::redirect('admin/sat/cfdis');
        }
        
        // Decodificar JSON
        $data['cfdi']['conceptos'] = json_decode($data['cfdi']['conceptos'], true);
        $data['cfdi']['complementos'] = json_decode($data['cfdi']['complementos'], true);
        
        // Obtener validaciones
        $data['validations'] = DB::select('*')
            ->from('sat_validations')
            ->where('cfdi_id', $cfdi_id)
            ->order_by('validated_at', 'DESC')
            ->execute()
            ->as_array();
        
        $data['title'] = 'Detalle de CFDI';
        $data['content'] = View::forge('admin/sat/view_cfdi', $data);
        
        return View::forge(Helper_Template::get_template_file(), $data);
    }
    
    /**
     * Validate - Validar un CFDI ante el SAT
     */
    public function action_validate($cfdi_id = null)
    {
        if (!Helper_Permission::can('sat', 'validate')) {
            Session::set_flash('error', 'No tienes permisos para validar CFDIs');
            Response::redirect('admin/sat');
        }
        
        if (!$cfdi_id) {
            Session::set_flash('error', 'ID de CFDI no especificado');
            Response::redirect('admin/sat/cfdis');
        }
        
        $tenant_id = Session::get('tenant_id', 1);
        
        // Obtener CFDI
        $cfdi = DB::select('*')
            ->from('sat_cfdis')
            ->where('id', $cfdi_id)
            ->where('tenant_id', $tenant_id)
            ->execute()
            ->current();
        
        if (!$cfdi) {
            Session::set_flash('error', 'CFDI no encontrado');
            Response::redirect('admin/sat/cfdis');
        }
        
        // Validar estado ante el SAT
        $result = Helper_SAT::validate_cfdi_status(
            $cfdi['uuid'],
            $cfdi['rfc_emisor'],
            $cfdi['rfc_receptor'],
            $cfdi['total']
        );
        
        // Guardar resultado de validación
        DB::insert('sat_validations')
            ->set([
                'tenant_id' => $tenant_id,
                'cfdi_id' => $cfdi_id,
                'uuid' => $cfdi['uuid'],
                'validation_type' => 'estado_sat',
                'is_valid' => ($result['estado'] === 'vigente') ? 1 : 0,
                'validation_message' => $result['message'],
                'validated_at' => DB::expr('NOW()'),
                'created_by' => Auth::get('id', 1)
            ])
            ->execute();
        
        // Actualizar estado en la tabla de CFDIs
        if ($result['success']) {
            DB::update('sat_cfdis')
                ->set(['estado_sat' => $result['estado']])
                ->where('id', $cfdi_id)
                ->execute();
        }
        
        Session::set_flash('success', 'Validación completada: ' . $result['message']);
        Response::redirect('admin/sat/view/' . $cfdi_id);
    }
    
    /**
     * Upload XML - Subir XML manualmente
     */
    public function action_upload()
    {
        if (!Helper_Permission::can('sat', 'download')) {
            Session::set_flash('error', 'No tienes permisos para subir CFDIs');
            Response::redirect('admin/sat');
        }
        
        $tenant_id = Session::get('tenant_id', 1);
        
        if (Input::method() === 'POST' && Upload::is_valid()) {
            Upload::process([
                'path' => APPPATH . 'tmp',
                'ext_whitelist' => ['xml'],
            ]);
            
            if (Upload::is_valid()) {
                $files = Upload::get_files();
                $success_count = 0;
                $error_count = 0;
                
                foreach ($files as $file) {
                    try {
                        $xml_content = file_get_contents($file['saved_to'] . $file['saved_as']);
                        $cfdi_data = Helper_SAT::parse_cfdi_xml($xml_content);
                        $result = Helper_SAT::save_cfdi($cfdi_data, $tenant_id);
                        
                        if ($result['success']) {
                            $success_count++;
                        } else {
                            $error_count++;
                        }
                        
                        // Eliminar archivo temporal
                        File::delete($file['saved_to'] . $file['saved_as']);
                        
                    } catch (Exception $e) {
                        $error_count++;
                    }
                }
                
                Session::set_flash('success', "XMLs procesados: $success_count exitosos, $error_count errores");
                Response::redirect('admin/sat/cfdis');
            } else {
                Session::set_flash('error', 'Error al subir archivos: ' . Upload::get_errors());
            }
        }
        
        $data['title'] = 'Subir XMLs';
        $data['content'] = View::forge('admin/sat/upload', $data);
        
        return View::forge(Helper_Template::get_template_file(), $data);
    }
    
    /**
     * Reports - Generar reportes fiscales
     */
    public function action_reports()
    {
        if (!Helper_Permission::can('sat', 'reports')) {
            Session::set_flash('error', 'No tienes permisos para generar reportes');
            Response::redirect('admin/sat');
        }
        
        $data['title'] = 'Reportes Fiscales';
        $data['content'] = View::forge('admin/sat/reports', $data);
        
        return View::forge(Helper_Template::get_template_file(), $data);
    }
    
    /**
     * Export - Exportar CFDIs a Excel/CSV
     */
    public function action_export()
    {
        if (!Helper_Permission::can('sat', 'reports')) {
            Session::set_flash('error', 'No tienes permisos para exportar');
            Response::redirect('admin/sat');
        }
        
        $tenant_id = Session::get('tenant_id', 1);
        $format = Input::get('format', 'csv'); // csv o excel
        
        // TODO: Implementar exportación a Excel/CSV
        
        Session::set_flash('info', 'Función de exportación en desarrollo');
        Response::redirect('admin/sat/cfdis');
    }
}
