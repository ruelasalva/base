<?php
/**
 * Helper_InvoiceValidator
 * 
 * Validaciones críticas de facturas XML con integración SAT
 * - Validación de UUID único
 * - Validación de RFC coincidente
 * - Validación de estado SAT en tiempo real
 * - Parseo de XML CFDI 3.3 y 4.0
 * 
 * @package    Base
 * @category   Helpers
 * @author     Sistema Proveedores
 * @version    1.0
 */
class Helper_InvoiceValidator
{
    /**
     * Valida un XML de factura completo
     * 
     * @param string $xml_path Ruta al archivo XML
     * @param int $provider_id ID del proveedor que sube la factura
     * @param array $options Opciones adicionales ['skip_sat' => bool, 'skip_uuid_check' => bool]
     * @return array ['valid' => bool, 'errors' => array, 'data' => array, 'warnings' => array]
     */
    public static function validate_xml($xml_path, $provider_id, array $options = [])
    {
        $errors = [];
        $warnings = [];
        $data = [];
        
        try {
            // 1. Verificar que el archivo existe
            if (!file_exists($xml_path)) {
                return [
                    'valid' => false,
                    'errors' => ['El archivo XML no existe'],
                    'data' => [],
                    'warnings' => []
                ];
            }
            
            // 2. Cargar y parsear XML
            $xml_content = file_get_contents($xml_path);
            $parse_result = self::parse_cfdi_xml($xml_content);
            
            if (!$parse_result['success']) {
                return [
                    'valid' => false,
                    'errors' => $parse_result['errors'],
                    'data' => [],
                    'warnings' => []
                ];
            }
            
            $data = $parse_result['data'];
            
            // 3. Obtener datos del proveedor
            $provider = \DB::select('id', 'company_name', 'tax_id', 'email')
                ->from('providers')
                ->where('id', $provider_id)
                ->where('deleted_at', null)
                ->execute()
                ->current();
            
            if (!$provider) {
                $errors[] = 'Proveedor no encontrado';
                return ['valid' => false, 'errors' => $errors, 'data' => $data, 'warnings' => $warnings];
            }
            
            // 4. Validar UUID único (si no está en opciones de skip)
            if (empty($options['skip_uuid_check'])) {
                $uuid_result = self::validate_uuid_unique($data['uuid'], $provider_id);
                if (!$uuid_result['valid']) {
                    $errors[] = $uuid_result['error'];
                }
            }
            
            // 5. Validar RFC coincidente
            $rfc_result = self::validate_rfc_match($data['rfc_emisor'], $provider['tax_id']);
            if (!$rfc_result['valid']) {
                $errors[] = $rfc_result['error'];
            } else if (!empty($rfc_result['warning'])) {
                $warnings[] = $rfc_result['warning'];
            }
            
            // 6. Validar estado SAT (si no está en opciones de skip)
            if (empty($options['skip_sat'])) {
                $sat_result = self::validate_sat_status(
                    $data['uuid'],
                    $data['rfc_emisor'],
                    $data['rfc_receptor'],
                    $data['total']
                );
                
                if ($sat_result['success']) {
                    $data['sat_status'] = $sat_result['status'];
                    $data['sat_validated_at'] = date('Y-m-d H:i:s');
                    
                    if ($sat_result['status'] !== 'vigente') {
                        $warnings[] = sprintf('CFDI con estatus SAT: %s', strtoupper($sat_result['status']));
                    }
                } else {
                    $warnings[] = 'No se pudo validar con SAT: ' . $sat_result['error'];
                    $data['sat_status'] = 'no_encontrado';
                }
            }
            
            // 7. Validar monto vs OC (si está configurado)
            $config = \DB::select()
                ->from('providers_billing_config')
                ->where('tenant_id', 1)
                ->execute()
                ->current();
            
            if ($config && $config['require_purchase_order']) {
                $max_without_po = (float)$config['max_amount_without_po'];
                
                if ($data['total'] > $max_without_po) {
                    // Verificar si existe OC
                    $has_order = \DB::select(\DB::expr('COUNT(*) as count'))
                        ->from('providers_orders')
                        ->where('provider_id', $provider_id)
                        ->where('deleted', 0)
                        ->where('status', '>=', 2) // Aprobada
                        ->execute()
                        ->get('count');
                    
                    if ($has_order == 0) {
                        $errors[] = sprintf(
                            'Factura de $%.2f requiere Orden de Compra (monto máximo sin OC: $%.2f)',
                            $data['total'],
                            $max_without_po
                        );
                    }
                }
            }
            
            // 8. Calcular hash del XML
            $data['xml_hash'] = hash('sha256', $xml_content);
            $data['xml_content'] = $xml_content;
            $data['provider_id'] = $provider_id;
            $data['upload_ip'] = \Input::real_ip();
            
            // 9. Determinar si es válido
            $valid = empty($errors);
            
            return [
                'valid' => $valid,
                'errors' => $errors,
                'data' => $data,
                'warnings' => $warnings
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error validando XML: ' . $e->getMessage());
            return [
                'valid' => false,
                'errors' => ['Error interno al validar: ' . $e->getMessage()],
                'data' => $data,
                'warnings' => $warnings
            ];
        }
    }
    
    /**
     * Valida que el UUID sea único en la base de datos
     * 
     * @param string $uuid
     * @param int $provider_id ID del proveedor (para mensaje más específico)
     * @return array ['valid' => bool, 'error' => string]
     */
    public static function validate_uuid_unique($uuid, $provider_id = null)
    {
        try {
            $existing = \DB::select('id', 'provider_id', 'created_at')
                ->from('providers_bills')
                ->where('uuid', $uuid)
                ->where('deleted', 0)
                ->execute()
                ->current();
            
            if ($existing) {
                $same_provider = ($existing['provider_id'] == $provider_id);
                
                $error = sprintf(
                    'UUID duplicado: %s ya existe en factura #%d %s (subida: %s)',
                    $uuid,
                    $existing['id'],
                    $same_provider ? 'del mismo proveedor' : 'de otro proveedor',
                    date('d/m/Y H:i', $existing['created_at'])
                );
                
                return ['valid' => false, 'error' => $error];
            }
            
            return ['valid' => true, 'error' => null];
            
        } catch (\Exception $e) {
            return ['valid' => false, 'error' => 'Error verificando UUID: ' . $e->getMessage()];
        }
    }
    
    /**
     * Valida que el RFC del emisor coincida con el RFC del proveedor
     * 
     * @param string $rfc_emisor RFC del CFDI
     * @param string $rfc_provider RFC registrado del proveedor
     * @return array ['valid' => bool, 'error' => string, 'warning' => string]
     */
    public static function validate_rfc_match($rfc_emisor, $rfc_provider)
    {
        $rfc_emisor = strtoupper(trim($rfc_emisor));
        $rfc_provider = strtoupper(trim($rfc_provider));
        
        if (empty($rfc_provider)) {
            return [
                'valid' => true,
                'error' => null,
                'warning' => 'Proveedor sin RFC registrado, no se puede validar coincidencia'
            ];
        }
        
        if ($rfc_emisor !== $rfc_provider) {
            return [
                'valid' => false,
                'error' => sprintf(
                    'RFC no coincide: CFDI emisor "%s" vs Proveedor registrado "%s"',
                    $rfc_emisor,
                    $rfc_provider
                ),
                'warning' => null
            ];
        }
        
        return ['valid' => true, 'error' => null, 'warning' => null];
    }
    
    /**
     * Valida el estado del CFDI contra el webservice del SAT
     * 
     * @param string $uuid UUID del CFDI
     * @param string $rfc_emisor RFC del emisor
     * @param string $rfc_receptor RFC del receptor
     * @param float $total Monto total del CFDI
     * @return array ['success' => bool, 'status' => string, 'error' => string]
     */
    public static function validate_sat_status($uuid, $rfc_emisor, $rfc_receptor, $total)
    {
        try {
            // URL del webservice SAT para consulta de CFDI
            $url = 'https://consultaqr.facturaelectronica.sat.gob.mx/ConsultaCFDIService.svc';
            
            // Construir expresión de consulta (formato SAT)
            // https://verificacfdi.facturaelectronica.sat.gob.mx/?[params]
            $query_url = sprintf(
                'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?&id=%s&re=%s&rr=%s&tt=%s',
                $uuid,
                urlencode($rfc_emisor),
                urlencode($rfc_receptor),
                number_format($total, 6, '.', '')
            );
            
            // Intentar consulta con SOAP (método oficial)
            $soap_result = self::validate_sat_soap($uuid, $rfc_emisor, $rfc_receptor, $total);
            
            if ($soap_result['success']) {
                return $soap_result;
            }
            
            // Fallback: Intentar con scraping de página web (menos confiable)
            $web_result = self::validate_sat_web($uuid, $rfc_emisor, $rfc_receptor, $total);
            
            return $web_result;
            
        } catch (\Exception $e) {
            \Log::error('Error validando estado SAT: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 'no_encontrado',
                'error' => 'Error al consultar SAT: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Valida estado SAT usando webservice SOAP
     * 
     * @param string $uuid
     * @param string $rfc_emisor
     * @param string $rfc_receptor
     * @param float $total
     * @return array
     */
    private static function validate_sat_soap($uuid, $rfc_emisor, $rfc_receptor, $total)
    {
        try {
            // SOAP cliente para SAT
            $wsdl = 'https://consultaqr.facturaelectronica.sat.gob.mx/ConsultaCFDIService.svc?wsdl';
            
            $options = [
                'soap_version' => SOAP_1_1,
                'exceptions' => true,
                'trace' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'connection_timeout' => 10,
            ];
            
            $client = new \SoapClient($wsdl, $options);
            
            // Parámetros de consulta
            $params = [
                'expresionImpresa' => sprintf(
                    '?re=%s&rr=%s&tt=%s&id=%s',
                    $rfc_emisor,
                    $rfc_receptor,
                    number_format($total, 6, '.', ''),
                    $uuid
                )
            ];
            
            $response = $client->Consulta($params);
            
            // Interpretar respuesta
            if (isset($response->ConsultaResult)) {
                $result = $response->ConsultaResult;
                
                // Códigos de respuesta SAT
                // S - Cancelable Sin Aceptación
                // N - Cancelable Con Aceptación
                // - - No Cancelable
                
                if (stripos($result->Estado, 'Cancelado') !== false) {
                    return ['success' => true, 'status' => 'cancelado', 'error' => null];
                } else if (stripos($result->Estado, 'Vigente') !== false) {
                    return ['success' => true, 'status' => 'vigente', 'error' => null];
                } else {
                    return ['success' => true, 'status' => 'no_encontrado', 'error' => null];
                }
            }
            
            return [
                'success' => false,
                'status' => 'no_encontrado',
                'error' => 'Respuesta SAT inválida'
            ];
            
        } catch (\SoapFault $e) {
            \Log::warning('SOAP SAT error: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 'no_encontrado',
                'error' => 'Servicio SAT no disponible: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Valida estado SAT usando página web (fallback)
     * 
     * @param string $uuid
     * @param string $rfc_emisor
     * @param string $rfc_receptor
     * @param float $total
     * @return array
     */
    private static function validate_sat_web($uuid, $rfc_emisor, $rfc_receptor, $total)
    {
        try {
            $query_url = sprintf(
                'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?&id=%s&re=%s&rr=%s&tt=%s',
                $uuid,
                urlencode($rfc_emisor),
                urlencode($rfc_receptor),
                number_format($total, 6, '.', '')
            );
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $query_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            
            $html = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code !== 200) {
                return [
                    'success' => false,
                    'status' => 'no_encontrado',
                    'error' => 'Página SAT no disponible'
                ];
            }
            
            // Buscar indicadores en HTML
            if (stripos($html, 'Estado del CFDI: Vigente') !== false || 
                stripos($html, 'Estado: Vigente') !== false) {
                return ['success' => true, 'status' => 'vigente', 'error' => null];
            }
            
            if (stripos($html, 'Estado del CFDI: Cancelado') !== false || 
                stripos($html, 'Estado: Cancelado') !== false) {
                return ['success' => true, 'status' => 'cancelado', 'error' => null];
            }
            
            // Si no encuentra nada claro
            return [
                'success' => false,
                'status' => 'no_encontrado',
                'error' => 'No se pudo determinar el estado del CFDI'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 'no_encontrado',
                'error' => 'Error consultando web SAT: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Parsea un XML de CFDI y extrae los datos principales
     * 
     * @param string $xml_content Contenido del XML
     * @return array ['success' => bool, 'data' => array, 'errors' => array]
     */
    public static function parse_cfdi_xml($xml_content)
    {
        try {
            // Cargar XML
            $xml = simplexml_load_string($xml_content);
            
            if ($xml === false) {
                return [
                    'success' => false,
                    'data' => [],
                    'errors' => ['XML inválido o mal formado']
                ];
            }
            
            // Registrar namespaces
            $namespaces = $xml->getNamespaces(true);
            
            // Detectar versión CFDI
            $version = (string)$xml['Version'] ?: (string)$xml['version'];
            
            // Extraer datos principales
            $data = [
                'version' => $version,
                'uuid' => null,
                'serie' => (string)$xml['Serie'] ?: (string)$xml['serie'],
                'folio' => (string)$xml['Folio'] ?: (string)$xml['folio'],
                'fecha' => (string)$xml['Fecha'] ?: (string)$xml['fecha'],
                'forma_pago' => (string)$xml['FormaPago'] ?: (string)$xml['formaPago'],
                'metodo_pago' => (string)$xml['MetodoPago'] ?: (string)$xml['metodoPago'],
                'subtotal' => (float)($xml['SubTotal'] ?: $xml['subTotal']),
                'descuento' => (float)($xml['Descuento'] ?: $xml['descuento'] ?: 0),
                'total' => (float)($xml['Total'] ?: $xml['total']),
                'moneda' => (string)($xml['Moneda'] ?: $xml['moneda'] ?: 'MXN'),
                'tipo_comprobante' => (string)($xml['TipoDeComprobante'] ?: $xml['tipoDeComprobante']),
            ];
            
            // Extraer Emisor
            $emisor = $xml->xpath('//cfdi:Emisor')[0] ?? $xml->Emisor;
            if ($emisor) {
                $data['rfc_emisor'] = (string)($emisor['Rfc'] ?: $emisor['rfc']);
                $data['nombre_emisor'] = (string)($emisor['Nombre'] ?: $emisor['nombre']);
                $data['regimen_fiscal_emisor'] = (string)($emisor['RegimenFiscal'] ?: $emisor['regimenFiscal']);
            }
            
            // Extraer Receptor
            $receptor = $xml->xpath('//cfdi:Receptor')[0] ?? $xml->Receptor;
            if ($receptor) {
                $data['rfc_receptor'] = (string)($receptor['Rfc'] ?: $receptor['rfc']);
                $data['nombre_receptor'] = (string)($receptor['Nombre'] ?: $receptor['nombre']);
                $data['uso_cfdi'] = (string)($receptor['UsoCFDI'] ?: $receptor['usoCFDI']);
            }
            
            // Extraer UUID del Complemento TimbreFiscalDigital
            $tfd = $xml->xpath('//tfd:TimbreFiscalDigital')[0];
            if ($tfd) {
                $data['uuid'] = (string)($tfd['UUID'] ?: $tfd['uuid']);
                $data['fecha_timbrado'] = (string)($tfd['FechaTimbrado'] ?: $tfd['fechaTimbrado']);
            }
            
            // Extraer Impuestos
            $impuestos = $xml->Impuestos ?? $xml->impuestos;
            if ($impuestos) {
                $data['total_impuestos_trasladados'] = (float)($impuestos['TotalImpuestosTrasladados'] ?: $impuestos['totalImpuestosTrasladados'] ?: 0);
                $data['total_impuestos_retenidos'] = (float)($impuestos['TotalImpuestosRetenidos'] ?: $impuestos['totalImpuestosRetenidos'] ?: 0);
            }
            
            // Validar que tenemos los datos mínimos
            if (empty($data['uuid'])) {
                return [
                    'success' => false,
                    'data' => $data,
                    'errors' => ['No se encontró UUID en el TimbreFiscalDigital']
                ];
            }
            
            if (empty($data['rfc_emisor'])) {
                return [
                    'success' => false,
                    'data' => $data,
                    'errors' => ['No se encontró RFC del Emisor']
                ];
            }
            
            return [
                'success' => true,
                'data' => $data,
                'errors' => []
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error parseando CFDI XML: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => [],
                'errors' => ['Error parseando XML: ' . $e->getMessage()]
            ];
        }
    }
    
    /**
     * Guarda una factura validada en la base de datos
     * 
     * @param array $data Datos de la factura (resultado de validate_xml)
     * @param int $status Estado inicial (1=pendiente, 2=aceptada, 3=rechazada)
     * @return array ['success' => bool, 'bill_id' => int, 'error' => string]
     */
    public static function save_bill(array $data, $status = 1)
    {
        try {
            // Preparar datos para inserción
            $bill_data = [
                'provider_id' => $data['provider_id'],
                'uuid' => $data['uuid'],
                'xml_content' => $data['xml_content'],
                'xml_hash' => $data['xml_hash'],
                'total' => $data['total'],
                'status' => $status,
                'sat_status' => $data['sat_status'] ?? 'vigente',
                'sat_validated_at' => isset($data['sat_validated_at']) ? strtotime($data['sat_validated_at']) : null,
                'upload_ip' => $data['upload_ip'],
                'invoice_date' => isset($data['fecha']) ? strtotime($data['fecha']) : time(),
                'invoice_data' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'deleted' => 0,
                'created_at' => time(),
                'updated_at' => time()
            ];
            
            // Insertar factura
            list($bill_id, $rows) = \DB::insert('providers_bills')
                ->set($bill_data)
                ->execute();
            
            // Registrar en log
            Helper_ProviderLog::log_bill_upload(
                $bill_id,
                $data['uuid'],
                $data['provider_id'],
                $data['total']
            );
            
            return [
                'success' => true,
                'bill_id' => $bill_id,
                'error' => null
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error guardando factura: ' . $e->getMessage());
            return [
                'success' => false,
                'bill_id' => null,
                'error' => 'Error al guardar: ' . $e->getMessage()
            ];
        }
    }
}
