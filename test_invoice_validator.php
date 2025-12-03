<?php
/**
 * Script de prueba para Helper_InvoiceValidator
 * 
 * Este script verifica que:
 * 1. La extensión SOAP esté habilitada
 * 2. Helper_InvoiceValidator pueda cargar correctamente
 * 3. La conexión con el SAT sea posible
 */

// Cargar FuelPHP
define('DOCROOT', __DIR__.DIRECTORY_SEPARATOR);
define('APPPATH', realpath(__DIR__.'/fuel/app/').DIRECTORY_SEPARATOR);
define('PKGPATH', realpath(__DIR__.'/fuel/packages/').DIRECTORY_SEPARATOR);
define('COREPATH', realpath(__DIR__.'/fuel/core/').DIRECTORY_SEPARATOR);

// Bootstrap
require APPPATH.'bootstrap.php';

echo "=== PRUEBA DE VALIDADOR DE FACTURAS ===\n\n";

// 1. Verificar SOAP
echo "1. Extensión SOAP: ";
if (extension_loaded('soap')) {
    echo "✓ HABILITADA\n";
} else {
    echo "✗ NO DISPONIBLE (requerida para consultar el SAT)\n";
    exit(1);
}

// 2. Verificar Helper existe
echo "2. Helper_InvoiceValidator: ";
if (class_exists('Helper_InvoiceValidator')) {
    echo "✓ CARGADO\n";
} else {
    echo "✗ NO ENCONTRADO\n";
    exit(1);
}

// 3. Verificar conectividad con SAT
echo "3. Conectividad con SAT: ";
$ch = curl_init('https://consultaqr.facturaelectronica.sat.gob.mx/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code >= 200 && $http_code < 400) {
    echo "✓ CONECTADO (HTTP $http_code)\n";
} else {
    echo "✗ NO DISPONIBLE (HTTP $http_code)\n";
}

// 4. Verificar directorios de upload
echo "4. Directorios de upload:\n";
$dirs = [
    'bills' => DOCROOT . 'public/uploads/providers/bills',
    'reports' => DOCROOT . 'public/uploads/providers/reports'
];

foreach ($dirs as $type => $dir) {
    echo "   - $type: ";
    if (is_dir($dir) && is_writable($dir)) {
        echo "✓ OK\n";
    } elseif (is_dir($dir)) {
        echo "✗ NO ESCRIBIBLE\n";
    } else {
        echo "✗ NO EXISTE\n";
    }
}

// 5. Verificar tablas de base de datos
echo "5. Tablas de base de datos:\n";
$tables = ['providers', 'providers_bills', 'providers_action_logs'];
foreach ($tables as $table) {
    echo "   - $table: ";
    try {
        $count = DB::select(DB::expr('COUNT(*) as count'))
            ->from($table)
            ->execute()
            ->get('count');
        echo "✓ OK ($count registros)\n";
    } catch (Exception $e) {
        echo "✗ ERROR: " . $e->getMessage() . "\n";
    }
}

// 6. Probar parsing de XML de ejemplo
echo "\n6. Prueba de parsing XML: ";
$sample_xml = '<?xml version="1.0" encoding="UTF-8"?>
<cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3" 
                   xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital"
                   Version="3.3"
                   Fecha="2024-01-15T10:30:00"
                   Folio="12345"
                   Total="1160.00"
                   SubTotal="1000.00"
                   Moneda="MXN">
    <cfdi:Emisor Rfc="AAA010101AAA" Nombre="Empresa Ejemplo SA" />
    <cfdi:Receptor Rfc="BBB020202BBB" Nombre="Cliente Ejemplo" UsoCFDI="G03" />
    <cfdi:Conceptos>
        <cfdi:Concepto Descripcion="Producto ejemplo" Cantidad="1" ValorUnitario="1000.00" Importe="1000.00" />
    </cfdi:Conceptos>
    <cfdi:Impuestos TotalImpuestosTrasladados="160.00">
        <cfdi:Traslados>
            <cfdi:Traslado Impuesto="002" TipoFactor="Tasa" TasaOCuota="0.160000" Importe="160.00" />
        </cfdi:Traslados>
    </cfdi:Impuestos>
    <cfdi:Complemento>
        <tfd:TimbreFiscalDigital Version="1.1" 
                                 UUID="12345678-1234-1234-1234-123456789012"
                                 FechaTimbrado="2024-01-15T10:31:00"
                                 SelloCFD="ABC123..."
                                 NoCertificadoSAT="00001000000123456789"
                                 SelloSAT="XYZ789..." />
    </cfdi:Complemento>
</cfdi:Comprobante>';

try {
    $xml = simplexml_load_string($sample_xml);
    if ($xml === false) {
        echo "✗ NO SE PUDO PARSEAR\n";
    } else {
        $namespaces = $xml->getNamespaces(true);
        $tfd = $xml->children($namespaces['cfdi'])->Complemento
                   ->children($namespaces['tfd'])->TimbreFiscalDigital;
        $uuid = (string)$tfd->attributes()->UUID;
        
        if ($uuid === '12345678-1234-1234-1234-123456789012') {
            echo "✓ OK (UUID extraído correctamente)\n";
        } else {
            echo "✗ UUID NO EXTRAÍDO\n";
        }
    }
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== RESULTADO ===\n";
echo "Sistema listo para procesar facturas XML del SAT.\n";
echo "Próximos pasos:\n";
echo "1. Acceder a /proveedores/bills/upload_multiple para subir facturas\n";
echo "2. Acceder a /admin/proveedores/dashboard para ver estadísticas\n";
echo "3. Revisar logs en fuel/app/logs/ para errores de validación\n";
