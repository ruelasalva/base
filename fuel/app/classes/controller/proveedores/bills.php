<?php
/**
 * Controller_Proveedores_Bills
 * 
 * Gestión de facturas para proveedores:
 * - Subida individual y masiva de XMLs
 * - Validación automática con SAT
 * - Generación de reportes de errores
 * 
 * @package    Base
 * @category   Controllers
 */
class Controller_Proveedores_Bills extends Controller
{
    /**
     * Before - Configuración inicial
     */
    public function before()
    {
        parent::before();
        
        // Configurar encoding UTF-8
        header('Content-Type: text/html; charset=utf-8');
    }
    
    /**
     * Subida masiva de XMLs con validación y reporte
     */
    public function action_upload_multiple()
    {
        // Verificar autenticación
        if (!\Auth::check()) {
            \Session::set_flash('error', 'Debe iniciar sesión para subir facturas');
            \Response::redirect('proveedores/login');
        }
        
        $provider_id = \Auth::get_user_id()[1];
        
        // Verificar que la cuenta esté activa
        $provider = \DB::select('is_suspended', 'company_name')
            ->from('providers')
            ->where('id', $provider_id)
            ->execute()
            ->current();
        
        if (!$provider || $provider['is_suspended']) {
            \Session::set_flash('error', 'Su cuenta está suspendida. Contacte al administrador.');
            \Response::redirect('proveedores/dashboard');
        }
        
        $data = [
            'provider_name' => $provider['company_name']
        ];
        
        // Si es POST, procesar archivos
        if (\Input::method() === 'POST') {
            $result = $this->process_multiple_uploads($provider_id);
            $data = array_merge($data, $result);
        }
        
        return \View::forge('proveedores/bills/upload_multiple', $data);
    }
    
    /**
     * Procesa múltiples archivos XML subidos
     * 
     * @param int $provider_id
     * @return array Datos para la vista con resultados
     */
    private function process_multiple_uploads($provider_id)
    {
        $success_bills = [];
        $failed_bills = [];
        $warnings = [];
        
        try {
            // Configurar upload
            $config = [
                'path' => DOCROOT . 'uploads/providers/bills/' . $provider_id . '/',
                'randomize' => false,
                'ext_whitelist' => ['xml'],
                'max_size' => 5242880, // 5MB por archivo
                'auto_rename' => true,
                'overwrite' => false,
            ];
            
            // Crear directorio si no existe
            if (!is_dir($config['path'])) {
                mkdir($config['path'], 0755, true);
            }
            
            \Upload::process($config);
            
            // Verificar si hay archivos
            if (\Upload::is_valid()) {
                \Upload::save();
                
                // Procesar cada archivo
                foreach (\Upload::get_files() as $file) {
                    $file_path = $file['saved_to'] . $file['saved_as'];
                    $original_name = $file['name'];
                    
                    // Validar XML
                    $validation_result = Helper_InvoiceValidator::validate_xml($file_path, $provider_id);
                    
                    if ($validation_result['valid']) {
                        // Guardar factura en BD
                        $save_result = Helper_InvoiceValidator::save_bill($validation_result['data'], 1); // 1 = pendiente
                        
                        if ($save_result['success']) {
                            $success_bills[] = [
                                'filename' => $original_name,
                                'uuid' => $validation_result['data']['uuid'],
                                'total' => $validation_result['data']['total'],
                                'rfc_emisor' => $validation_result['data']['rfc_emisor'],
                                'fecha' => $validation_result['data']['fecha'],
                                'sat_status' => $validation_result['data']['sat_status'] ?? 'vigente',
                                'bill_id' => $save_result['bill_id'],
                                'warnings' => $validation_result['warnings']
                            ];
                            
                            // Si hay warnings, agregarlos
                            if (!empty($validation_result['warnings'])) {
                                foreach ($validation_result['warnings'] as $warning) {
                                    $warnings[] = sprintf('%s: %s', $original_name, $warning);
                                }
                            }
                        } else {
                            $failed_bills[] = [
                                'filename' => $original_name,
                                'errors' => [$save_result['error']],
                                'data' => $validation_result['data']
                            ];
                        }
                    } else {
                        // Validación falló
                        $failed_bills[] = [
                            'filename' => $original_name,
                            'errors' => $validation_result['errors'],
                            'data' => $validation_result['data']
                        ];
                    }
                }
            } else {
                // Errores de upload
                foreach (\Upload::get_errors() as $file) {
                    $failed_bills[] = [
                        'filename' => $file['name'],
                        'errors' => [$file['errors'][0]['message']],
                        'data' => []
                    ];
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('Error en subida masiva: ' . $e->getMessage());
            \Session::set_flash('error', 'Error al procesar archivos: ' . $e->getMessage());
        }
        
        // Generar reporte CSV si hay errores
        $csv_path = null;
        if (!empty($failed_bills)) {
            $csv_path = $this->generate_error_report($failed_bills, $provider_id);
        }
        
        return [
            'success_bills' => $success_bills,
            'failed_bills' => $failed_bills,
            'warnings' => $warnings,
            'csv_path' => $csv_path,
            'total_success' => count($success_bills),
            'total_failed' => count($failed_bills),
            'processed' => true
        ];
    }
    
    /**
     * Genera un reporte CSV de errores
     * 
     * @param array $failed_bills
     * @param int $provider_id
     * @return string Ruta al archivo CSV
     */
    private function generate_error_report(array $failed_bills, $provider_id)
    {
        try {
            $filename = sprintf('errores_facturas_%s_%s.csv', $provider_id, date('YmdHis'));
            $path = DOCROOT . 'uploads/providers/reports/' . $provider_id . '/';
            
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
            
            $filepath = $path . $filename;
            $fp = fopen($filepath, 'w');
            
            // BOM para Excel UTF-8
            fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados
            fputcsv($fp, [
                'Archivo',
                'UUID',
                'RFC Emisor',
                'Total',
                'Errores',
                'Fecha Intento'
            ]);
            
            // Datos
            foreach ($failed_bills as $bill) {
                fputcsv($fp, [
                    $bill['filename'],
                    $bill['data']['uuid'] ?? 'N/A',
                    $bill['data']['rfc_emisor'] ?? 'N/A',
                    isset($bill['data']['total']) ? '$' . number_format($bill['data']['total'], 2) : 'N/A',
                    implode('; ', $bill['errors']),
                    date('Y-m-d H:i:s')
                ]);
            }
            
            fclose($fp);
            
            return '/uploads/providers/reports/' . $provider_id . '/' . $filename;
            
        } catch (\Exception $e) {
            \Log::error('Error generando reporte CSV: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Subida individual de XML (AJAX)
     */
    public function action_upload_single()
    {
        if (!\Auth::check()) {
            return $this->response(['error' => 'No autenticado'], 401);
        }
        
        if (\Input::method() !== 'POST') {
            return $this->response(['error' => 'Método no permitido'], 405);
        }
        
        $provider_id = \Auth::get_user_id()[1];
        
        try {
            $config = [
                'path' => DOCROOT . 'uploads/providers/bills/' . $provider_id . '/',
                'randomize' => false,
                'ext_whitelist' => ['xml'],
                'max_size' => 5242880,
                'auto_rename' => true,
            ];
            
            if (!is_dir($config['path'])) {
                mkdir($config['path'], 0755, true);
            }
            
            \Upload::process($config);
            
            if (\Upload::is_valid()) {
                \Upload::save();
                $file = \Upload::get_files()[0];
                $file_path = $file['saved_to'] . $file['saved_as'];
                
                // Validar
                $validation = Helper_InvoiceValidator::validate_xml($file_path, $provider_id);
                
                if ($validation['valid']) {
                    // Guardar
                    $save_result = Helper_InvoiceValidator::save_bill($validation['data'], 1);
                    
                    if ($save_result['success']) {
                        return $this->response([
                            'success' => true,
                            'message' => 'Factura subida correctamente',
                            'bill_id' => $save_result['bill_id'],
                            'uuid' => $validation['data']['uuid'],
                            'warnings' => $validation['warnings']
                        ]);
                    } else {
                        return $this->response([
                            'success' => false,
                            'errors' => [$save_result['error']]
                        ], 500);
                    }
                } else {
                    return $this->response([
                        'success' => false,
                        'errors' => $validation['errors']
                    ], 400);
                }
            } else {
                $errors = [];
                foreach (\Upload::get_errors() as $file) {
                    $errors[] = $file['errors'][0]['message'];
                }
                
                return $this->response([
                    'success' => false,
                    'errors' => $errors
                ], 400);
            }
            
        } catch (\Exception $e) {
            \Log::error('Error en subida individual: ' . $e->getMessage());
            return $this->response([
                'success' => false,
                'errors' => ['Error interno: ' . $e->getMessage()]
            ], 500);
        }
    }
    
    /**
     * Lista de facturas del proveedor
     */
    public function action_index()
    {
        if (!\Auth::check()) {
            \Response::redirect('proveedores/login');
        }
        
        $provider_id = \Auth::get_user_id()[1];
        
        // Filtros
        $status = \Input::get('status', null);
        $sat_status = \Input::get('sat_status', null);
        $date_from = \Input::get('date_from', null);
        $date_to = \Input::get('date_to', null);
        
        // Query base
        $query = \DB::select()
            ->from('providers_bills')
            ->where('provider_id', $provider_id)
            ->where('deleted', 0)
            ->order_by('created_at', 'DESC');
        
        // Aplicar filtros
        if ($status !== null) {
            $query->where('status', $status);
        }
        
        if ($sat_status !== null) {
            $query->where('sat_status', $sat_status);
        }
        
        if ($date_from) {
            $query->where('created_at', '>=', strtotime($date_from));
        }
        
        if ($date_to) {
            $query->where('created_at', '<=', strtotime($date_to . ' 23:59:59'));
        }
        
        $bills = $query->execute()->as_array();
        
        // Estadísticas
        $stats = [
            'total' => count($bills),
            'pending' => 0,
            'accepted' => 0,
            'rejected' => 0,
            'total_amount' => 0
        ];
        
        foreach ($bills as $bill) {
            $stats['total_amount'] += $bill['total'];
            
            switch ($bill['status']) {
                case 1: $stats['pending']++; break;
                case 2: $stats['accepted']++; break;
                case 3: $stats['rejected']++; break;
            }
        }
        
        $data = [
            'bills' => $bills,
            'stats' => $stats,
            'filters' => [
                'status' => $status,
                'sat_status' => $sat_status,
                'date_from' => $date_from,
                'date_to' => $date_to
            ]
        ];
        
        return \View::forge('proveedores/bills/index', $data);
    }
    
    /**
     * Helper para respuestas JSON
     */
    private function response($data, $status = 200)
    {
        return \Response::forge(json_encode($data), $status, [
            'Content-Type' => 'application/json'
        ]);
    }
}
