<?php
/**
 * Helper_SAT
 * 
 * Gestión de conexión y descarga de CFDIs desde el portal SAT
 * Incluye autenticación, descarga masiva, validación y parse de XMLs
 * 
 * @package    Fuel
 * @subpackage Helpers
 * @category   SAT
 * @author     ERP System
 */

class Helper_SAT
{
    /**
     * URLs del SAT
     */
    const SAT_AUTH_URL = 'https://portalcfdi.facturaelectronica.sat.gob.mx/';
    const SAT_DOWNLOAD_URL = 'https://verificacfdi.facturaelectronica.sat.gob.mx/';
    const SAT_VALIDATION_URL = 'https://consultaqr.facturaelectronica.sat.gob.mx/';
    
    /**
     * Encripta una contraseña para almacenarla
     */
    public static function encrypt_password($password)
    {
        $encryption_key = \Config::get('security.encryption_key', 'default_key_change_this');
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($password, 'aes-256-cbc', $encryption_key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }
    
    /**
     * Desencripta una contraseña almacenada
     */
    public static function decrypt_password($encrypted_data)
    {
        $encryption_key = \Config::get('security.encryption_key', 'default_key_change_this');
        list($encrypted, $iv) = explode('::', base64_decode($encrypted_data), 2);
        return openssl_decrypt($encrypted, 'aes-256-cbc', $encryption_key, 0, $iv);
    }
    
    /**
     * Guarda o actualiza credenciales SAT
     * 
     * @param array $data Array con rfc, password, archivos CSD/FIEL
     * @param int $tenant_id ID del tenant
     * @return array ['success' => bool, 'credential_id' => int, 'message' => string]
     */
    public static function save_credentials($data, $tenant_id = null)
    {
        if ($tenant_id === null) {
            $tenant_id = Session::get('tenant_id', 1);
        }
        
        try {
            // Validar RFC
            if (empty($data['rfc']) || strlen($data['rfc']) < 12) {
                return ['success' => false, 'message' => 'RFC inválido'];
            }
            
            // Verificar si ya existen credenciales
            $existing = DB::select('id')
                ->from('sat_credentials')
                ->where('tenant_id', $tenant_id)
                ->where('rfc', strtoupper($data['rfc']))
                ->execute()
                ->as_array();
            
            $credential_data = [
                'rfc' => strtoupper($data['rfc']),
                'password_encrypted' => self::encrypt_password($data['password']),
                'is_active' => 1,
                'updated_at' => DB::expr('NOW()')
            ];
            
            // Procesar archivos CSD si existen
            if (!empty($data['csd_cer']) && !empty($data['csd_key'])) {
                $credential_data['csd_cer'] = file_get_contents($data['csd_cer']);
                $credential_data['csd_key'] = file_get_contents($data['csd_key']);
                $credential_data['csd_password_encrypted'] = self::encrypt_password($data['csd_password'] ?? '');
            }
            
            // Procesar archivos FIEL si existen
            if (!empty($data['fiel_cer']) && !empty($data['fiel_key'])) {
                $credential_data['fiel_cer'] = file_get_contents($data['fiel_cer']);
                $credential_data['fiel_key'] = file_get_contents($data['fiel_key']);
                $credential_data['fiel_password_encrypted'] = self::encrypt_password($data['fiel_password'] ?? '');
            }
            
            if (!empty($existing)) {
                // Actualizar
                $credential_id = $existing[0]['id'];
                DB::update('sat_credentials')
                    ->set($credential_data)
                    ->where('id', $credential_id)
                    ->execute();
                    
                return [
                    'success' => true,
                    'credential_id' => $credential_id,
                    'message' => 'Credenciales actualizadas correctamente'
                ];
            } else {
                // Insertar
                $credential_data['tenant_id'] = $tenant_id;
                $credential_data['created_at'] = DB::expr('NOW()');
                
                list($insert_id, $rows) = DB::insert('sat_credentials')
                    ->set($credential_data)
                    ->execute();
                    
                return [
                    'success' => true,
                    'credential_id' => $insert_id,
                    'message' => 'Credenciales guardadas correctamente'
                ];
            }
            
        } catch (Exception $e) {
            Helper_Log::record('sat', 'error', 'Error al guardar credenciales SAT', [
                'rfc' => $data['rfc'] ?? 'N/A',
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error al guardar credenciales: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtiene las credenciales activas del tenant
     */
    public static function get_credentials($tenant_id = null)
    {
        if ($tenant_id === null) {
            $tenant_id = Session::get('tenant_id', 1);
        }
        
        try {
            $credentials = DB::select('*')
                ->from('sat_credentials')
                ->where('tenant_id', $tenant_id)
                ->where('is_active', 1)
                ->execute()
                ->as_array();
                
            return !empty($credentials) ? $credentials[0] : null;
            
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Inicia una descarga masiva de CFDIs
     * 
     * @param array $params ['date_from', 'date_to', 'type' => 'emitidos'|'recibidos'|'ambos']
     * @param int $tenant_id
     * @return array ['success' => bool, 'download_id' => int, 'message' => string]
     */
    public static function start_download($params, $tenant_id = null)
    {
        if ($tenant_id === null) {
            $tenant_id = Session::get('tenant_id', 1);
        }
        
        try {
            // Obtener credenciales
            $credentials = self::get_credentials($tenant_id);
            if (!$credentials) {
                return [
                    'success' => false,
                    'message' => 'No hay credenciales configuradas. Configure primero su RFC y contraseña.'
                ];
            }
            
            // Validar fechas
            $date_from = date('Y-m-d', strtotime($params['date_from']));
            $date_to = date('Y-m-d', strtotime($params['date_to']));
            
            if ($date_from > $date_to) {
                return ['success' => false, 'message' => 'La fecha inicial no puede ser mayor a la fecha final'];
            }
            
            // Crear registro de descarga
            list($download_id, $rows) = DB::insert('sat_downloads')
                ->set([
                    'tenant_id' => $tenant_id,
                    'credential_id' => $credentials['id'],
                    'download_type' => $params['type'] ?? 'recibidos',
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'status' => 'pending',
                    'created_by' => Auth::get('id', 1),
                    'created_at' => DB::expr('NOW()')
                ])
                ->execute();
            
            // TODO: Aquí iría la lógica de descarga real usando web scraping o API del SAT
            // Por ahora solo retornamos el ID para procesamiento posterior
            
            return [
                'success' => true,
                'download_id' => $download_id,
                'message' => 'Descarga iniciada. Se procesará en segundo plano.'
            ];
            
        } catch (Exception $e) {
            Helper_Log::record('sat', 'error', 'Error al iniciar descarga', [
                'params' => $params,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error al iniciar descarga: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Parsea un XML de CFDI y extrae los datos principales
     * 
     * @param string $xml_content Contenido del XML
     * @return array Datos parseados del CFDI
     */
    public static function parse_cfdi_xml($xml_content)
    {
        try {
            $xml = simplexml_load_string($xml_content);
            if (!$xml) {
                throw new Exception('XML inválido');
            }
            
            // Registrar namespaces
            $xml->registerXPathNamespace('cfdi', 'http://www.sat.gob.mx/cfd/4');
            $xml->registerXPathNamespace('tfd', 'http://www.sat.gob.mx/TimbreFiscalDigital');
            
            // Obtener TimbreFiscalDigital
            $tfd = $xml->xpath('//tfd:TimbreFiscalDigital');
            $uuid = (string)($tfd[0]['UUID'] ?? '');
            
            // Extraer datos del comprobante
            $data = [
                'uuid' => $uuid,
                'version' => (string)$xml['Version'],
                'serie' => (string)$xml['Serie'],
                'folio' => (string)$xml['Folio'],
                'fecha_emision' => date('Y-m-d H:i:s', strtotime((string)$xml['Fecha'])),
                'fecha_certificacion' => isset($tfd[0]['FechaTimbrado']) ? date('Y-m-d H:i:s', strtotime((string)$tfd[0]['FechaTimbrado'])) : null,
                'tipo_comprobante' => (string)$xml['TipoDeComprobante'],
                'forma_pago' => (string)$xml['FormaPago'],
                'metodo_pago' => (string)$xml['MetodoPago'],
                'moneda' => (string)$xml['Moneda'],
                'tipo_cambio' => (float)($xml['TipoCambio'] ?? 1),
                'subtotal' => (float)$xml['SubTotal'],
                'descuento' => (float)($xml['Descuento'] ?? 0),
                'total' => (float)$xml['Total'],
            ];
            
            // Emisor
            $emisor = $xml->Emisor;
            $data['rfc_emisor'] = (string)$emisor['Rfc'];
            $data['nombre_emisor'] = (string)$emisor['Nombre'];
            
            // Receptor
            $receptor = $xml->Receptor;
            $data['rfc_receptor'] = (string)$receptor['Rfc'];
            $data['nombre_receptor'] = (string)$receptor['Nombre'];
            $data['uso_cfdi'] = (string)$receptor['UsoCFDI'];
            
            // Impuestos
            if (isset($xml->Impuestos)) {
                $data['impuestos_trasladados'] = (float)($xml->Impuestos['TotalImpuestosTrasladados'] ?? 0);
                $data['impuestos_retenidos'] = (float)($xml->Impuestos['TotalImpuestosRetenidos'] ?? 0);
            } else {
                $data['impuestos_trasladados'] = 0;
                $data['impuestos_retenidos'] = 0;
            }
            
            // Conceptos
            $conceptos = [];
            foreach ($xml->Conceptos->Concepto as $concepto) {
                $conceptos[] = [
                    'clave_prod_serv' => (string)$concepto['ClaveProdServ'],
                    'no_identificacion' => (string)$concepto['NoIdentificacion'],
                    'cantidad' => (float)$concepto['Cantidad'],
                    'clave_unidad' => (string)$concepto['ClaveUnidad'],
                    'unidad' => (string)$concepto['Unidad'],
                    'descripcion' => (string)$concepto['Descripcion'],
                    'valor_unitario' => (float)$concepto['ValorUnitario'],
                    'importe' => (float)$concepto['Importe'],
                    'descuento' => (float)($concepto['Descuento'] ?? 0),
                ];
            }
            $data['conceptos'] = json_encode($conceptos);
            
            // XML completo y hash
            $data['xml_content'] = $xml_content;
            $data['xml_hash'] = hash('sha256', $xml_content);
            
            return $data;
            
        } catch (Exception $e) {
            throw new Exception('Error al parsear XML: ' . $e->getMessage());
        }
    }
    
    /**
     * Guarda un CFDI parseado en la base de datos
     */
    public static function save_cfdi($cfdi_data, $tenant_id = null, $download_id = null)
    {
        if ($tenant_id === null) {
            $tenant_id = Session::get('tenant_id', 1);
        }
        
        try {
            // Verificar si ya existe por UUID
            $existing = DB::select('id')
                ->from('sat_cfdis')
                ->where('uuid', $cfdi_data['uuid'])
                ->where('tenant_id', $tenant_id)
                ->execute()
                ->as_array();
            
            if (!empty($existing)) {
                return [
                    'success' => false,
                    'message' => 'El CFDI ya existe en la base de datos',
                    'cfdi_id' => $existing[0]['id']
                ];
            }
            
            $cfdi_data['tenant_id'] = $tenant_id;
            $cfdi_data['download_id'] = $download_id;
            $cfdi_data['estado_sat'] = 'vigente'; // Por defecto, se debe validar después
            $cfdi_data['created_at'] = DB::expr('NOW()');
            
            list($cfdi_id, $rows) = DB::insert('sat_cfdis')
                ->set($cfdi_data)
                ->execute();
            
            return [
                'success' => true,
                'message' => 'CFDI guardado correctamente',
                'cfdi_id' => $cfdi_id
            ];
            
        } catch (Exception $e) {
            Helper_Log::record('sat', 'error', 'Error al guardar CFDI', [
                'uuid' => $cfdi_data['uuid'] ?? 'N/A',
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error al guardar CFDI: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Valida el estado de un CFDI ante el SAT (vigente o cancelado)
     * 
     * @param string $uuid
     * @param string $rfc_emisor
     * @param string $rfc_receptor
     * @param float $total
     * @return array ['success' => bool, 'estado' => 'vigente'|'cancelado', 'message' => string]
     */
    public static function validate_cfdi_status($uuid, $rfc_emisor, $rfc_receptor, $total)
    {
        try {
            // URL del servicio de validación del SAT
            $url = self::SAT_VALIDATION_URL . 'consultacfdiservice.svc';
            
            // TODO: Implementar llamada SOAP al servicio del SAT
            // Por ahora retornamos un placeholder
            
            // Ejemplo de lógica:
            // 1. Crear cliente SOAP
            // 2. Llamar al método de consulta con los parámetros
            // 3. Parsear respuesta XML
            // 4. Retornar estado
            
            return [
                'success' => true,
                'estado' => 'vigente', // Placeholder
                'message' => 'Validación pendiente de implementación con servicio SAT real'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'estado' => 'no_encontrado',
                'message' => 'Error al validar: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtiene estadísticas de CFDIs del tenant
     */
    public static function get_statistics($tenant_id = null)
    {
        if ($tenant_id === null) {
            $tenant_id = Session::get('tenant_id', 1);
        }
        
        try {
            $stats = [];
            
            // Total de CFDIs
            $stats['total_cfdis'] = DB::select(DB::expr('COUNT(*) as total'))
                ->from('sat_cfdis')
                ->where('tenant_id', $tenant_id)
                ->execute()
                ->get('total');
            
            // CFDIs por tipo
            $by_type = DB::select('tipo_comprobante', DB::expr('COUNT(*) as total'))
                ->from('sat_cfdis')
                ->where('tenant_id', $tenant_id)
                ->group_by('tipo_comprobante')
                ->execute()
                ->as_array();
            
            $stats['by_type'] = [];
            foreach ($by_type as $row) {
                $type_name = [
                    'I' => 'Ingreso',
                    'E' => 'Egreso',
                    'T' => 'Traslado',
                    'N' => 'Nómina',
                    'P' => 'Pago'
                ];
                $stats['by_type'][$type_name[$row['tipo_comprobante']] ?? $row['tipo_comprobante']] = $row['total'];
            }
            
            // Totales por mes (últimos 12 meses)
            $stats['monthly'] = DB::select(
                    DB::expr('DATE_FORMAT(fecha_emision, "%Y-%m") as mes'),
                    DB::expr('COUNT(*) as cantidad'),
                    DB::expr('SUM(total) as monto_total')
                )
                ->from('sat_cfdis')
                ->where('tenant_id', $tenant_id)
                ->where('fecha_emision', '>=', DB::expr('DATE_SUB(NOW(), INTERVAL 12 MONTH)'))
                ->group_by('mes')
                ->order_by('mes', 'DESC')
                ->execute()
                ->as_array();
            
            // Estado SAT
            $by_status = DB::select('estado_sat', DB::expr('COUNT(*) as total'))
                ->from('sat_cfdis')
                ->where('tenant_id', $tenant_id)
                ->group_by('estado_sat')
                ->execute()
                ->as_array();
            
            $stats['by_status'] = [];
            foreach ($by_status as $row) {
                $stats['by_status'][$row['estado_sat']] = $row['total'];
            }
            
            return $stats;
            
        } catch (Exception $e) {
            Helper_Log::record('sat', 'error', 'Error al obtener estadísticas', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
    
    /**
     * Busca CFDIs con filtros
     */
    public static function search_cfdis($filters = [], $tenant_id = null, $limit = 50, $offset = 0)
    {
        if ($tenant_id === null) {
            $tenant_id = Session::get('tenant_id', 1);
        }
        
        try {
            $query = DB::select('*')
                ->from('sat_cfdis')
                ->where('tenant_id', $tenant_id);
            
            // Aplicar filtros
            if (!empty($filters['uuid'])) {
                $query->where('uuid', 'LIKE', '%' . $filters['uuid'] . '%');
            }
            
            if (!empty($filters['rfc_emisor'])) {
                $query->where('rfc_emisor', $filters['rfc_emisor']);
            }
            
            if (!empty($filters['rfc_receptor'])) {
                $query->where('rfc_receptor', $filters['rfc_receptor']);
            }
            
            if (!empty($filters['tipo_comprobante'])) {
                $query->where('tipo_comprobante', $filters['tipo_comprobante']);
            }
            
            if (!empty($filters['fecha_desde'])) {
                $query->where('fecha_emision', '>=', $filters['fecha_desde']);
            }
            
            if (!empty($filters['fecha_hasta'])) {
                $query->where('fecha_emision', '<=', $filters['fecha_hasta']);
            }
            
            if (!empty($filters['estado_sat'])) {
                $query->where('estado_sat', $filters['estado_sat']);
            }
            
            // Orden y paginación
            $query->order_by('fecha_emision', 'DESC')
                  ->limit($limit)
                  ->offset($offset);
            
            return $query->execute()->as_array();
            
        } catch (Exception $e) {
            Helper_Log::record('sat', 'error', 'Error al buscar CFDIs', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
    
    /**
     * Catálogo de formas de pago del SAT (c_FormaPago)
     * Anexo 20 - Catálogos para CFDI 4.0
     * 
     * @return array Formas de pago oficiales
     */
    public static function get_formas_pago()
    {
        return array(
            '01' => '01 - Efectivo',
            '02' => '02 - Cheque nominativo',
            '03' => '03 - Transferencia electrónica de fondos',
            '04' => '04 - Tarjeta de crédito',
            '05' => '05 - Monedero electrónico',
            '06' => '06 - Dinero electrónico',
            '08' => '08 - Vales de despensa',
            '12' => '12 - Dación en pago',
            '13' => '13 - Pago por subrogación',
            '14' => '14 - Pago por consignación',
            '15' => '15 - Condonación',
            '17' => '17 - Compensación',
            '23' => '23 - Novación',
            '24' => '24 - Confusión',
            '25' => '25 - Remisión de deuda',
            '26' => '26 - Prescripción o caducidad',
            '27' => '27 - A satisfacción del acreedor',
            '28' => '28 - Tarjeta de débito',
            '29' => '29 - Tarjeta de servicios',
            '30' => '30 - Aplicación de anticipos',
            '31' => '31 - Intermediario pagos',
            '99' => '99 - Por definir',
        );
    }
    
    /**
     * Obtener descripción de forma de pago por código
     * 
     * @param string $codigo Código SAT
     * @return string Descripción
     */
    public static function get_forma_pago_descripcion($codigo)
    {
        $formas = self::get_formas_pago();
        return isset($formas[$codigo]) ? $formas[$codigo] : $codigo . ' - Código no válido';
    }
    
    /**
     * Mapeo de métodos antiguos del sistema a códigos SAT
     * Para compatibilidad con registros existentes
     * 
     * @param string $old_method Método antiguo del sistema
     * @return string Código SAT
     */
    public static function map_old_payment_to_sat($old_method)
    {
        $mapping = array(
            'transferencia' => '03', // Transferencia electrónica
            'efectivo'      => '01', // Efectivo
            'cheque'        => '02', // Cheque nominativo
            'tarjeta'       => '04', // Tarjeta de crédito
            'debito'        => '28', // Tarjeta de débito
            'otro'          => '99', // Por definir
        );
        
        return isset($mapping[$old_method]) ? $mapping[$old_method] : '99';
    }
}
