<?php
/**
 * HELPER PARA EXTRACCIÓN Y VALIDACIÓN DE XML CFDI DE PROVEEDORES
 * USO: Admin, Proveedor, Socio, Proceso batch, etc.
 * Compatible con CFDI 3.3 / 4.0 / Notas de Crédito / Carta Porte / Complementos
 * Incluye validación contra Config (empresa) y consulta SAT.
 *
 * v2025-10-06
 */

class Helper_Invoicexml
{
    /**
     * EXTRAER TODOS LOS DATOS DEL XML CFDI (versión robusta y segura)
     * @param string $xml_content Contenido del XML (string)
     * @return array|null
     */
    public static function extract_invoice_data_from_xml($xml_content)
    {
        try {
            // ================= LOG INICIO DEL PROCESO =================
            \Log::info('[XML HELPER] INICIANDO EXTRACCIÓN DE DATOS CFDI');

            // ================= VALIDACIÓN INICIAL =================
            if (empty($xml_content)) {
                \Log::error('[XML HELPER] CONTENIDO VACÍO O NULO');
                return null;
            }

            // ================= SEGURIDAD: Manejo de entidades externas =================
            // En PHP < 8.0 se usa libxml_disable_entity_loader(true) para mitigar XXE.
            // En PHP 8+ está deprecated y SimpleXML ya mitiga XXE por defecto.
            $prev_loader = null;
            if (PHP_VERSION_ID < 80000 && function_exists('libxml_disable_entity_loader')) {
                $prev_loader = libxml_disable_entity_loader(true);
            }

            // ================= PARSEO XML =================
            libxml_use_internal_errors(true);
            $xml = @simplexml_load_string($xml_content);
            if (!$xml) {
                \Log::error('[XML HELPER] ERROR AL PARSEAR XML: ' . print_r(libxml_get_errors(), true));
                if (PHP_VERSION_ID < 80000 && function_exists('libxml_disable_entity_loader')) {
                    libxml_disable_entity_loader($prev_loader);
                }
                return null;
            }

            // Namespaces
            $namespaces = $xml->getNamespaces(true);
            if (!isset($namespaces['cfdi'])) {
                // Fallback típico para CFDI 4.0
                $namespaces['cfdi'] = 'http://www.sat.gob.mx/cfd/4';
            }
            $xml->registerXPathNamespace('cfdi', $namespaces['cfdi']);
            \Log::info('[XML HELPER] NAMESPACES REGISTRADOS: ' . json_encode(array_keys($namespaces)));

            // ================= ESTRUCTURA BASE =================
            $data = [
                // GENERALES
                'uuid'              => null,
                'version'           => null,
                'serie'             => null,
                'folio'             => null,
                'fecha'             => null,
                'tipo_cambio'       => null,
                'descuento'         => null,
                'moneda'            => null,
                'subtotal'          => null,
                'total'             => null,
                'forma_pago'        => null,
                'metodo_pago'       => null,
                'condiciones_pago'  => null,
                'exportacion'       => null,
                'tipo_comprobante'  => null,
                'lugar_expedicion'  => null,
                'no_certificado'    => null,

                // EMISOR
                'emisor_nombre'         => null,
                'emisor_rfc'            => null,
                'emisor_regimen_fiscal' => null,

                // RECEPTOR
                'receptor_nombre'        => null,
                'receptor_rfc'           => null,
                'receptor_regimen_fiscal'=> null,
                'receptor_uso_cfdi'      => null,

                // IMPUESTOS GLOBALES
                'total_impuestos_trasladados'   => null,
                'total_impuestos_retenidos'     => null,
                'traslados_globales'            => [],
                'retenciones_globales'          => [],

                // PRODUCTOS/CONCEPTOS
                'productos'     => [],

                // TIMBRE FISCAL
                'timbre'        => [],

                // CARTA PORTE (OPCIONAL)
                'carta_porte'   => [],

                // --- NOTAS DE CRÉDITO / CFDI RELACIONADOS ---
                'cfdi_relacionados' => [],
                'nota_credito'       => [],

                // --- COMPLEMENTOS DETECTADOS (nombres) ---
                'complementos'      => [],
            ];

            // ================= COMPROBANTE =================
            $comprobante = $xml->xpath('//cfdi:Comprobante');
            if (!empty($comprobante)) {
                $c = $comprobante[0];
                $data['version']            = (string) ($c['Version'] ?? null);
                $data['total']              = isset($c['Total']) ? (float) $c['Total'] : null;
                $data['subtotal']           = isset($c['SubTotal']) ? (float) $c['SubTotal'] : null;
                $data['serie']              = (string) ($c['Serie'] ?? null);
                $data['folio']              = (string) ($c['Folio'] ?? null);
                $data['moneda']             = (string) ($c['Moneda'] ?? null);
                $data['forma_pago']         = (string) ($c['FormaPago'] ?? null);
                $data['metodo_pago']        = (string) ($c['MetodoPago'] ?? null);
                $data['condiciones_pago']   = (string) ($c['CondicionesDePago'] ?? null);
                $data['exportacion']        = (string) ($c['Exportacion'] ?? null);
                $data['fecha']              = (string) ($c['Fecha'] ?? null);
                $data['lugar_expedicion']   = (string) ($c['LugarExpedicion'] ?? null);
                $data['no_certificado']     = (string) ($c['NoCertificado'] ?? null);
                $data['tipo_comprobante']   = (string) ($c['TipoDeComprobante'] ?? null);
                $data['descuento']          = isset($c['Descuento']) ? (float) $c['Descuento'] : null;
                $data['tipo_cambio']        = isset($c['TipoCambio']) ? (float) $c['TipoCambio'] : null;

                \Log::info('[XML HELPER] COMPROBANTE EXTRAÍDO - FOLIO: ' . ($data['folio'] ?? 'N/A') . ' TOTAL: ' . ($data['total'] ?? 'N/A'));
            }

            // ================= EMISOR =================
            $emisor = $xml->xpath('//cfdi:Emisor');
            if (!empty($emisor)) {
                $e = $emisor[0];
                $data['emisor_nombre']          = (string) ($e['Nombre'] ?? null);
                $data['emisor_rfc']             = (string) ($e['Rfc'] ?? null);
                $data['emisor_regimen_fiscal']  = (string) ($e['RegimenFiscal'] ?? null);
                \Log::debug('[XML HELPER] EMISOR: ' . $data['emisor_rfc']);
            }

            // ================= RECEPTOR =================
            $receptor = $xml->xpath('//cfdi:Receptor');
            if (!empty($receptor)) {
                $r = $receptor[0];
                $data['receptor_nombre']         = (string) ($r['Nombre'] ?? null);
                $data['receptor_rfc']            = (string) ($r['Rfc'] ?? null);
                $data['receptor_regimen_fiscal'] = (string) ($r['RegimenFiscalReceptor'] ?? $r['RegimenFiscal'] ?? null);
                $data['receptor_uso_cfdi']       = (string) ($r['UsoCFDI'] ?? null);
                \Log::debug('[XML HELPER] RECEPTOR: ' . $data['receptor_rfc']);
            }

            // ================= CFDI RELACIONADOS / NOTAS DE CRÉDITO =================
            $relacionados_nodes = $xml->xpath('//cfdi:CfdiRelacionados');
            if (!empty($relacionados_nodes)) {
                $node = $relacionados_nodes[0];
                $uuids_relacionados = [];
                foreach ($node->xpath('cfdi:CfdiRelacionado') as $rel) {
                    $uuids_relacionados[] = (string)$rel['UUID'];
                }
                $data['cfdi_relacionados'] = [
                    'tipo_relacion' => (string)$node['TipoRelacion'],
                    'uuids'         => $uuids_relacionados,
                ];
                \Log::info('[XML HELPER] CfdiRelacionados encontrados: ' . count($uuids_relacionados));

                // Si es nota de crédito
                if ($data['tipo_comprobante'] === 'E') {
                    $data['nota_credito'] = [
                        'uuid_factura_origen' => $uuids_relacionados[0] ?? null,
                        'tipo_relacion'       => (string)$node['TipoRelacion'] ?? null,
                    ];
                }
            }

            // ================= IMPUESTOS GLOBALES =================
            $impuestos = $xml->xpath('//cfdi:Comprobante/cfdi:Impuestos');
            if (!empty($impuestos)) {
                $imp = $impuestos[0];
                $data['total_impuestos_trasladados'] = isset($imp['TotalImpuestosTrasladados']) ? (float)$imp['TotalImpuestosTrasladados'] : null;
                $data['total_impuestos_retenidos']   = isset($imp['TotalImpuestosRetenidos']) ? (float)$imp['TotalImpuestosRetenidos'] : null;

                // Traslados globales
                $traslados = $xml->xpath('//cfdi:Comprobante/cfdi:Impuestos/cfdi:Traslados/cfdi:Traslado');
                foreach ($traslados as $t) {
                    $data['traslados_globales'][] = [
                        'impuesto'    => (string)($t['Impuesto'] ?? null),
                        'tasaocuota'  => (string)($t['TasaOCuota'] ?? null),
                        'importe'     => isset($t['Importe']) ? (float)$t['Importe'] : null,
                        'tipo_factor' => (string)($t['TipoFactor'] ?? null),
                        'base'        => isset($t['Base']) ? (float)$t['Base'] : null,
                    ];
                }

                // Retenciones globales
                $retenciones = $xml->xpath('//cfdi:Comprobante/cfdi:Impuestos/cfdi:Retenciones/cfdi:Retencion');
                foreach ($retenciones as $r) {
                    $data['retenciones_globales'][] = [
                        'impuesto'    => (string)($r['Impuesto'] ?? null),
                        'importe'     => isset($r['Importe']) ? (float)$r['Importe'] : null,
                        'tasaocuota'  => (string)($r['TasaOCuota'] ?? null),
                        'tipo_factor' => (string)($r['TipoFactor'] ?? null),
                        'base'        => isset($r['Base']) ? (float)$r['Base'] : null,
                    ];
                }
            }

            // ================= CONCEPTOS / PRODUCTOS =================
            $conceptos = $xml->xpath('//cfdi:Conceptos/cfdi:Concepto');
            foreach ($conceptos as $index => $p) {
                $concepto = [
                    'clave_prod_serv' => (string)($p['ClaveProdServ'] ?? null),
                    'clave_unidad'    => (string)($p['ClaveUnidad'] ?? null),
                    'unidad'          => (string)($p['Unidad'] ?? null),
                    'descripcion'     => (string)($p['Descripcion'] ?? null),
                    'cantidad'        => isset($p['Cantidad']) ? (float)$p['Cantidad'] : null,
                    'valor_unitario'  => isset($p['ValorUnitario']) ? (float)$p['ValorUnitario'] : null,
                    'importe'         => isset($p['Importe']) ? (float)$p['Importe'] : null,
                    'noidentificacion'=> (string)($p['NoIdentificacion'] ?? null),
                    'iva'             => 0.0,
                    'retencion_total' => 0.0,
                    'traslados'       => [],
                    'retenciones'     => [],
                ];

                // Impuestos por concepto
                $imp_concepto = $p->xpath('cfdi:Impuestos');
                if (!empty($imp_concepto)) {
                    // Traslados
                    $traslados = $p->xpath('cfdi:Impuestos/cfdi:Traslados/cfdi:Traslado');
                    foreach ($traslados as $t) {
                        $tras = [
                            'impuesto'   => (string)($t['Impuesto'] ?? null),
                            'importe'    => isset($t['Importe']) ? (float)$t['Importe'] : 0.0,
                            'tasaocuota' => (string)($t['TasaOCuota'] ?? null),
                        ];
                        if ($tras['impuesto'] === '002') {
                            $concepto['iva'] += $tras['importe'];
                        }
                        $concepto['traslados'][] = $tras;
                    }

                    // Retenciones
                    $rets = $p->xpath('cfdi:Impuestos/cfdi:Retenciones/cfdi:Retencion');
                    foreach ($rets as $r) {
                        $rtn = [
                            'impuesto' => (string)($r['Impuesto'] ?? null),
                            'importe'  => isset($r['Importe']) ? (float)$r['Importe'] : 0.0,
                        ];
                        $concepto['retencion_total'] += $rtn['importe'];
                        $concepto['retenciones'][] = $rtn;
                    }
                }

                $data['productos'][] = $concepto;
            }

            // ================= VALIDACIÓN DE SUBTOTAL CALCULADO =================
            $subtotal_calc = array_sum(array_column($data['productos'], 'importe'));
            if (abs($subtotal_calc - (float)$data['subtotal']) > 0.1) {
                \Log::warning("[XML HELPER] Desajuste en subtotal: XML={$data['subtotal']} / Calculado={$subtotal_calc}");
            }

            // ================= COMPLEMENTOS DETECTADOS =================
            $complemento_node = $xml->xpath('//cfdi:Complemento');
            if (!empty($complemento_node)) {
                $detected = [];
                foreach ($namespaces as $prefix => $uri) {
                    $children = $complemento_node[0]->children($uri);
                    foreach ($children as $child) {
                        $name = $child->getName();
                        $prefixed = ($prefix ? $prefix . ':' : '') . $name;
                        if (!in_array($prefixed, ['tfd:TimbreFiscalDigital', 'cartaporte31:CartaPorte'])) {
                            $detected[] = $prefixed;
                        }
                    }
                }
                $data['complementos'] = array_values(array_unique($detected));
                if (!empty($detected)) {
                    \Log::info('[XML HELPER] COMPLEMENTOS DETECTADOS: ' . implode(', ', $detected));
                }
            }

            // ================= CARTA PORTE 3.1 =================
            if (isset($namespaces['cartaporte31'])) {
                $xml->registerXPathNamespace('cartaporte31', $namespaces['cartaporte31']);
                $carta = $xml->xpath('//cfdi:Complemento//cartaporte31:CartaPorte');
                if (!empty($carta)) {
                    $cp = $carta[0];
                    $data['carta_porte'] = [
                        'version'         => (string)($cp['Version'] ?? null),
                        'transp_internac' => (string)($cp['TranspInternac'] ?? null),
                        'idccp'           => (string)($cp['IdCCP'] ?? null),
                        'total_dist_rec'  => (string)($cp['TotalDistRec'] ?? null),
                    ];
                }
            }

            // ================= TIMBRE FISCAL =================
            if (isset($namespaces['tfd'])) {
                $xml->registerXPathNamespace('tfd', $namespaces['tfd']);
                $timbre = $xml->xpath('//cfdi:Complemento//tfd:TimbreFiscalDigital');
                if (!empty($timbre)) {
                    $t = $timbre[0];
                    $data['uuid'] = (string)($t['UUID'] ?? null);
                    $data['timbre'] = [
                        'uuid'            => (string)($t['UUID'] ?? null),
                        'fecha_timbrado'  => (string)($t['FechaTimbrado'] ?? null),
                        'no_certificado_sat' => (string)($t['NoCertificadoSAT'] ?? null),
                        'sello_cfd'       => (string)($t['SelloCFD'] ?? null),
                        'sello_sat'       => (string)($t['SelloSAT'] ?? null),
                        'rfc_prov_certif' => (string)($t['RfcProvCertif'] ?? null),
                        'version'         => (string)($t['Version'] ?? null),
                    ];
                }
            }

            // ================= ETIQUETAS HUMANAS =================
            $data['cfdi_version_label'] = match($data['version']) {
                '3.3' => 'CFDI 3.3',
                '4.0' => 'CFDI 4.0',
                default => 'Versión desconocida',
            };

            $data['tipo_comprobante_label'] = match($data['tipo_comprobante']) {
                'I' => 'Ingreso (Factura)',
                'E' => 'Egreso (Nota de Crédito)',
                'P' => 'Pago (REP)',
                'T' => 'Traslado',
                'N' => 'Nómina',
                default => 'Desconocido',
            };

            // ================= VALIDACIÓN FINAL =================
            if (empty($data['uuid']) || is_null($data['total'])) {
                \Log::error('[XML HELPER] VALIDACIÓN FALLIDA - UUID o TOTAL faltantes');
                if (PHP_VERSION_ID < 80000 && function_exists('libxml_disable_entity_loader')) {
                    libxml_disable_entity_loader($prev_loader);
                }
                return null;
            }

            // Restaurar loader solo si lo cambiamos
            if (PHP_VERSION_ID < 80000 && function_exists('libxml_disable_entity_loader')) {
                libxml_disable_entity_loader($prev_loader);
            }

            \Log::info('[XML HELPER] EXTRACCIÓN COMPLETADA - UUID: ' . $data['uuid'] . ' | CONCEPTOS: ' . count($data['productos']));
            return $data;

        } catch (Exception $e) {
            \Log::error('[XML HELPER] ERROR FATAL: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return null;
        }
    }


    /**
     * Convierte un SimpleXMLElement (o valor único) a arreglo para forzar foreach.
     * @param SimpleXMLElement|array|mixed $element
     * @return array
     */
    private static function toArray($element)
    {
        if (!is_array($element)) {
            return [$element];
        }
        return $element;
    }

    /**
     * VALIDAR ESTRUCTURA BÁSICA DE CFDI
     * @param string $xml_content
     * @return bool
     */
    public static function validate_cfdi_structure($xml_content)
    {
        try {
            \Log::info('[XML HELPER] INICIANDO VALIDACIÓN DE ESTRUCTURA CFDI');

            if (empty($xml_content)) {
                \Log::error('[XML HELPER] XML CONTENT VACÍO PARA VALIDACIÓN');
                return false;
            }

            libxml_use_internal_errors(true);
            $xml = @simplexml_load_string($xml_content);
            if (!$xml) {
                \Log::error('[XML HELPER] ERROR AL PARSEAR XML EN VALIDACIÓN: ' . print_r(libxml_get_errors(), true));
                return false;
            }

            $namespaces = $xml->getNamespaces(true);
            if (!isset($namespaces['cfdi'])) {
                \Log::error('[XML HELPER] NAMESPACE CFDI NO ENCONTRADO EN VALIDACIÓN');
                return false;
            }

            $xml->registerXPathNamespace('cfdi', $namespaces['cfdi']);
            $comprobante = $xml->xpath('//cfdi:Comprobante');
            if (empty($comprobante)) {
                \Log::error('[XML HELPER] NODO COMPROBANTE NO ENCONTRADO EN VALIDACIÓN');
                return false;
            }

            \Log::info('[XML HELPER] ESTRUCTURA CFDI VALIDADA CORRECTAMENTE');
            return true;

        } catch (Exception $e) {
            \Log::error('[XML HELPER] ERROR EN VALIDACIÓN DE ESTRUCTURA CFDI: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * VALIDAR DATOS DEL XML CONTRA LA EMPRESA (CONFIG)
     * @param array        $invoice_data
     * @param Model_Config $config
     * @return array ['success' => bool, 'mensaje' => string]
     */
    public static function validate_against_config($invoice_data, $config)
    {
        if (!$config) {
            return ['success' => false, 'mensaje' => 'No se encontró la configuración de la empresa.'];
        }

        $rfc_empresa = trim($config->rfc);
        $rfc_xml     = trim($invoice_data['receptor_rfc']);
        if (strcasecmp($rfc_empresa, $rfc_xml) !== 0) {
            return [
                'success' => false,
                'mensaje' => "El RFC del receptor en el XML ({$rfc_xml}) no coincide con el de la empresa ({$rfc_empresa})."
            ];
        }

        $name_empresa = trim($config->name);
        $nombre_xml   = trim($invoice_data['receptor_nombre']);
        if (strcasecmp($name_empresa, $nombre_xml) !== 0) {
            similar_text($name_empresa, $nombre_xml, $percentage);
            if ($percentage < 85) {
                return [
                    'success' => false,
                    'mensaje' => "El nombre del receptor en el XML ({$nombre_xml}) no coincide con el de la empresa ({$name_empresa}). Similitud: " . round($percentage, 2) . "%"
                ];
            }
        }

        return ['success' => true, 'mensaje' => 'Validación exitosa contra datos de empresa.'];
    }

    /**
     * VALIDAR VIGENCIA DEL CFDI EN EL SAT (maneja CAPTCHA)
     * @param string $uuid
     * @param string $rfc_emisor
     * @param string $rfc_receptor
     * @param float  $total
     * @return array
     */
    public static function validate_cfdi_sat($uuid, $rfc_emisor, $rfc_receptor, $total)
    {
        // 1) Total con formato exacto (17 dígitos, 6 decimales)
        $tt = number_format((float)$total, 6, '.', '');
        $tt = str_pad($tt, 17, '0', STR_PAD_LEFT);

        // 2) URL del SAT
        $sat_url = "https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx";
        $url = "{$sat_url}?&id={$uuid}&re={$rfc_emisor}&rr={$rfc_receptor}&tt={$tt}";

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code != 200 || !$response) {
                return ['success' => false, 'estatus_sat' => 'ERROR', 'mensaje' => 'No se pudo validar con el SAT. Intente más tarde.'];
            }

            // Detección de captcha / validación humana
            if (
                stripos($response, 'captcha') !== false ||
                stripos($response, 'demuestre que es un humano') !== false ||
                stripos($response, 'Escriba los caracteres de la imagen') !== false ||
                stripos($response, 'validaci&oacute;n autom&aacute;tica') !== false
            ) {
                return ['success' => false, 'estatus_sat' => 'CAPTCHA', 'mensaje' => 'El SAT solicita validación manual (captcha). Intenta después.'];
            }

            // Estatus comunes
            if (stripos($response, 'Este comprobante es VIGENTE') !== false) {
                return ['success' => true, 'estatus_sat' => 'VIGENTE', 'mensaje' => 'El comprobante es válido y vigente según el SAT.'];
            }
            if (stripos($response, 'cancelado') !== false) {
                return ['success' => true, 'estatus_sat' => 'CANCELADO', 'mensaje' => 'El comprobante aparece como CANCELADO ante el SAT.'];
            }
            if (stripos($response, 'no se encuentra registrado') !== false) {
                return ['success' => true, 'estatus_sat' => 'NO ENCONTRADO', 'mensaje' => 'El comprobante no se encuentra registrado en el SAT.'];
            }

            return ['success' => false, 'estatus_sat' => 'NO_DETERMINADO', 'mensaje' => 'No se pudo determinar el estatus en el SAT.'];

        } catch (Exception $e) {
            return ['success' => false, 'estatus_sat' => 'ERROR', 'mensaje' => 'Error al consultar el SAT: ' . $e->getMessage()];
        }
    }

    /**
     * EXTRAER SOLO EL UUID DEL XML
     * @param string $xml_content
     * @return string|null
     */
    public static function extract_uuid_from_xml($xml_content)
    {
        try {
            libxml_use_internal_errors(true);
            $xml = @simplexml_load_string($xml_content);
            if (!$xml) {
                return null;
            }
            $namespaces = $xml->getNamespaces(true);
            if (isset($namespaces['cfdi']) && isset($namespaces['tfd'])) {
                $xml->registerXPathNamespace('cfdi', $namespaces['cfdi']);
                $xml->registerXPathNamespace('tfd', $namespaces['tfd']);
                $uuid_nodes = $xml->xpath('//cfdi:Complemento//tfd:TimbreFiscalDigital/@UUID');
                if (!empty($uuid_nodes)) {
                    return (string) $uuid_nodes[0];
                }
            }
        } catch (Exception $e) {
            \Log::error('[XML HELPER] Error al extraer UUID: ' . $e->getMessage());
            return null;
        }
        return null;
    }

    /**
     * Extrae datos del XML de un REP (Recibo Electrónico de Pago)
     * Retorna: uuid del REP, fecha de pago, total del REP, y facturas pagadas (uuid/importe)
     * @param string $xml_file_path
     * @return array
     */
    public static function get_rep_data($xml_file_path)
    {
        $out = [
            'uuid_rep'     => null,
            'payment_date' => null,
            'rep_total'    => 0,
            'facturas'     => [],
        ];

        if (!file_exists($xml_file_path)) {
            return $out;
        }

        libxml_use_internal_errors(true);
        $xml = @simplexml_load_file($xml_file_path);
        if (!$xml) {
            return $out;
        }

        $namespaces = $xml->getNamespaces(true);
        if (isset($namespaces['cfdi']))  $xml->registerXPathNamespace('cfdi', $namespaces['cfdi']);
        if (isset($namespaces['pago20'])) $xml->registerXPathNamespace('pago20', $namespaces['pago20']);
        if (isset($namespaces['tfd']))   $xml->registerXPathNamespace('tfd', $namespaces['tfd']);

        // UUID del REP
        $uuidNode = $xml->xpath('//cfdi:Complemento//tfd:TimbreFiscalDigital/@UUID');
        if ($uuidNode && count($uuidNode)) {
            $out['uuid_rep'] = (string) $uuidNode[0];
        }

        // Fecha y montos
        $pagos = $xml->xpath('//cfdi:Complemento//pago20:Pagos');
        if (!empty($pagos)) {
            $pagos_node = $pagos[0];
            $paymentNodes = $pagos_node->xpath('.//pago20:Pago');
            foreach ($paymentNodes as $pago) {
                $fecha_pago = isset($pago['FechaPago']) ? (string)$pago['FechaPago'] : null;
                $out['payment_date'] = $fecha_pago;

                $docs = $pago->xpath('.//pago20:DoctoRelacionado');
                foreach ($docs as $doc) {
                    $uuid_fact = isset($doc['IdDocumento']) ? (string)$doc['IdDocumento'] : null;
                    $monto_pagado = isset($doc['ImpPagado']) ? (float)$doc['ImpPagado'] : 0;
                    $out['rep_total'] += $monto_pagado;
                    $out['facturas'][] = [
                        'uuid'       => $uuid_fact,
                        'monto_rep'  => $monto_pagado,
                    ];
                }
            }
        }

        return $out;
    }

    /**
     * Determina si un XML es un REP (Tipo P o presencia de Pagos 2.0)
     * @param string $xml_file_path
     * @return bool
     */
    public static function is_rep($xml_file_path)
    {
        if (!file_exists($xml_file_path)) return false;

        libxml_use_internal_errors(true);
        $xml = @simplexml_load_file($xml_file_path);
        if (!$xml) return false;

        $namespaces = $xml->getNamespaces(true);
        if (isset($namespaces['cfdi'])) {
            $xml->registerXPathNamespace('cfdi', $namespaces['cfdi']);
        }

        // 1) Tipo de comprobante = P
        $tipo = $xml->xpath('//cfdi:Comprobante');
        if ($tipo && isset($tipo[0]['TipoDeComprobante']) && (string)$tipo[0]['TipoDeComprobante'] === 'P') {
            return true;
        }

        // 2) Namespace de pagos presente
        if (isset($namespaces['pago20'])) return true;

        // 3) Nodo de pagos 2.0
        if (!isset($namespaces['pago20'])) {
            // Registrar si existe, para xpath
            if (isset($namespaces['pago20'])) {
                $xml->registerXPathNamespace('pago20', $namespaces['pago20']);
            }
        }
        $pagos = $xml->xpath('//cfdi:Complemento//pago20:Pagos');
        if (!empty($pagos)) return true;

        return false;
    }

        /**
     * DESCRIPCIÓN RESUMIDA DEL CFDI
     * Genera una cadena legible para logs, pantallas o alertas automáticas.
     * Ejemplo:
     * "Factura de tipo Egreso (Nota de Crédito) CFDI 4.0 emitida por ABC010203XYZ el 2025-09-30 con total $1,234.50"
     *
     * @param array $data Datos procesados del XML (resultado de extract_invoice_data_from_xml)
     * @return string
     */
    public static function describe_invoice_summary($data)
    {
        // ================= VALIDACIÓN =================
        if (empty($data) || !is_array($data)) {
            return '[XML HELPER] Sin datos de CFDI disponibles.';
        }

        // ================= VARIABLES BASE =================
        $tipo_label   = $data['tipo_comprobante_label'] ?? 'Desconocido';
        $version      = $data['cfdi_version_label'] ?? 'Versión desconocida';
        $rfc_emisor   = $data['emisor_rfc'] ?? 'N/A';
        $nombre_emisor= $data['emisor_nombre'] ?? '';
        $fecha        = $data['fecha'] ?? 'Sin fecha';
        $total        = isset($data['total']) ? '$' . number_format($data['total'], 2, '.', ',') : 'Sin total';
        $uuid         = $data['uuid'] ?? 'Sin UUID';

        // ================= CONSTRUCCIÓN DE MENSAJE =================
        $desc = "{$tipo_label} {$version} emitida por {$rfc_emisor}";
        if (!empty($nombre_emisor)) {
            $desc .= " ({$nombre_emisor})";
        }
        $desc .= " el {$fecha} con total {$total}. UUID: {$uuid}";

        // ================= DATOS ADICIONALES =================
        if (!empty($data['nota_credito']) && isset($data['nota_credito']['uuid_factura_origen'])) {
            $desc .= " (Nota de crédito relacionada con factura UUID {$data['nota_credito']['uuid_factura_origen']})";
        }

        if (!empty($data['carta_porte'])) {
            $desc .= " [Incluye complemento Carta Porte]";
        }

        if (!empty($data['complementos'])) {
            $desc .= " [Complementos detectados: " . implode(', ', $data['complementos']) . "]";
        }

        return $desc;
    }

}
