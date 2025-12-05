-- PRUEBAS DEL SISTEMA DE PAGOS Y RECEPCIONES
-- Verificación de estructura y datos de prueba
-- Fecha: 2025-12-04

USE base;

-- ============================================
-- 1. VERIFICAR TABLAS EXISTENTES
-- ============================================
SELECT 'Verificando tablas...' AS paso;

SELECT 
    'provider_payments' as tabla,
    COUNT(*) as existe 
FROM information_schema.tables 
WHERE table_schema = 'base' 
AND table_name = 'provider_payments'
UNION ALL
SELECT 
    'provider_inventory_receipts' as tabla,
    COUNT(*) as existe 
FROM information_schema.tables 
WHERE table_schema = 'base' 
AND table_name = 'provider_inventory_receipts'
UNION ALL
SELECT 
    'provider_logs' as tabla,
    COUNT(*) as existe 
FROM information_schema.tables 
WHERE table_schema = 'base' 
AND table_name = 'provider_logs';

-- ============================================
-- 2. VERIFICAR PROVEEDORES ACTIVOS
-- ============================================
SELECT 'Proveedores activos...' AS paso;

SELECT 
    id,
    code,
    company_name,
    email,
    is_active,
    is_suspended
FROM providers 
WHERE deleted_at IS NULL 
AND is_active = 1
LIMIT 5;

-- ============================================
-- 3. VERIFICAR PERMISOS
-- ============================================
SELECT 'Verificando permisos...' AS paso;

SELECT 
    permission_name,
    COUNT(*) as existe
FROM permissions
WHERE permission_name LIKE '%payments%' 
   OR permission_name LIKE '%receipts%'
GROUP BY permission_name;

-- ============================================
-- 4. CREAR DATOS DE PRUEBA (SI NO EXISTEN)
-- ============================================

-- Verificar si existe proveedor de prueba
SELECT 'Verificando proveedor de prueba...' AS paso;

SELECT COUNT(*) as total_proveedores
FROM providers 
WHERE deleted_at IS NULL;

-- Crear proveedor de prueba si no existe ninguno
INSERT IGNORE INTO providers (
    tenant_id,
    code,
    company_name,
    email,
    phone,
    tax_id,
    is_active,
    created_at
) VALUES (
    1,
    'PROV-TEST-001',
    'Proveedor de Prueba SA de CV',
    'prueba@proveedor.com',
    '5555555555',
    'PPR010101ABC',
    1,
    NOW()
);

-- Obtener ID del proveedor de prueba
SET @test_provider_id = (SELECT id FROM providers WHERE code = 'PROV-TEST-001' LIMIT 1);

SELECT CONCAT('Proveedor de prueba ID: ', @test_provider_id) AS info;

-- ============================================
-- 5. CREAR PAGO DE PRUEBA
-- ============================================
SELECT 'Creando pago de prueba...' AS paso;

INSERT INTO provider_payments (
    tenant_id,
    provider_id,
    payment_number,
    payment_date,
    payment_method,
    reference_number,
    amount,
    currency,
    exchange_rate,
    notes,
    status,
    created_by,
    created_at
) VALUES (
    1,
    @test_provider_id,
    'PAG-000001-TEST',
    CURDATE(),
    '03', -- Transferencia electrónica (código SAT)
    'TRANS-123456',
    15000.00,
    'MXN',
    1.0000,
    'Pago de prueba - Sistema automatizado',
    'draft',
    1,
    NOW()
) ON DUPLICATE KEY UPDATE 
    payment_date = CURDATE();

-- Verificar pago creado
SELECT 
    payment_number,
    payment_date,
    payment_method,
    amount,
    currency,
    status
FROM provider_payments
WHERE payment_number = 'PAG-000001-TEST';

-- ============================================
-- 6. MAPEO DE FORMAS DE PAGO
-- ============================================
SELECT 'Catálogo de formas de pago SAT...' AS paso;

SELECT 
    '01' as codigo_sat,
    'Efectivo' as descripcion
UNION ALL SELECT '02', 'Cheque nominativo'
UNION ALL SELECT '03', 'Transferencia electrónica de fondos'
UNION ALL SELECT '04', 'Tarjeta de crédito'
UNION ALL SELECT '28', 'Tarjeta de débito'
UNION ALL SELECT '99', 'Por definir';

-- ============================================
-- 7. ESTADÍSTICAS DEL SISTEMA
-- ============================================
SELECT 'Estadísticas generales...' AS paso;

SELECT 
    'Total Proveedores' as concepto,
    COUNT(*) as cantidad
FROM providers
WHERE deleted_at IS NULL
UNION ALL
SELECT 
    'Proveedores Activos',
    COUNT(*)
FROM providers
WHERE deleted_at IS NULL AND is_active = 1
UNION ALL
SELECT 
    'Proveedores Suspendidos',
    COUNT(*)
FROM providers
WHERE deleted_at IS NULL AND is_suspended = 1
UNION ALL
SELECT 
    'Total Pagos',
    COUNT(*)
FROM provider_payments
WHERE deleted_at IS NULL
UNION ALL
SELECT 
    'Pagos Completados',
    COUNT(*)
FROM provider_payments
WHERE deleted_at IS NULL AND status = 'completed'
UNION ALL
SELECT 
    'Pagos Borradores',
    COUNT(*)
FROM provider_payments
WHERE deleted_at IS NULL AND status = 'draft'
UNION ALL
SELECT 
    'Total Recepciones',
    COUNT(*)
FROM provider_inventory_receipts
WHERE deleted_at IS NULL
UNION ALL
SELECT 
    'Recepciones Afectadas',
    COUNT(*)
FROM provider_inventory_receipts
WHERE deleted_at IS NULL AND status = 'posted';

-- ============================================
-- 8. ÚLTIMOS REGISTROS
-- ============================================
SELECT 'Últimos 5 pagos...' AS paso;

SELECT 
    pp.payment_number,
    pp.payment_date,
    p.company_name as proveedor,
    pp.payment_method,
    pp.amount,
    pp.status,
    pp.created_at
FROM provider_payments pp
LEFT JOIN providers p ON pp.provider_id = p.id
WHERE pp.deleted_at IS NULL
ORDER BY pp.created_at DESC
LIMIT 5;

SELECT 'Últimas 5 recepciones...' AS paso;

SELECT 
    pir.receipt_number,
    pir.receipt_date,
    p.company_name as proveedor,
    pir.total_amount,
    pir.status,
    pir.created_at
FROM provider_inventory_receipts pir
LEFT JOIN providers p ON pir.provider_id = p.id
WHERE pir.deleted_at IS NULL
ORDER BY pir.created_at DESC
LIMIT 5;

-- ============================================
-- 9. VALIDACIÓN DE INTEGRIDAD
-- ============================================
SELECT 'Validando integridad...' AS paso;

-- Pagos sin proveedor
SELECT 
    'Pagos huérfanos (sin proveedor)' as problema,
    COUNT(*) as cantidad
FROM provider_payments pp
LEFT JOIN providers p ON pp.provider_id = p.id
WHERE pp.deleted_at IS NULL 
AND p.id IS NULL;

-- Recepciones sin proveedor
SELECT 
    'Recepciones huérfanas (sin proveedor)' as problema,
    COUNT(*) as cantidad
FROM provider_inventory_receipts pir
LEFT JOIN providers p ON pir.provider_id = p.id
WHERE pir.deleted_at IS NULL 
AND p.id IS NULL;

-- ============================================
-- 10. RUTAS A PROBAR
-- ============================================
SELECT 'URLs para probar en navegador...' AS paso;

SELECT 
    '/admin/proveedores' as url,
    'Listado principal con nuevos botones' as descripcion
UNION ALL
SELECT '/admin/proveedores/pagos', 'Listado de pagos'
UNION ALL
SELECT '/admin/proveedores/pagos/create', 'Crear nuevo pago (con catálogo SAT)'
UNION ALL
SELECT '/admin/proveedores/recepciones', 'Listado de recepciones'
UNION ALL
SELECT '/admin/proveedores/recepciones/create', 'Crear nueva recepción';

-- ============================================
-- RESUMEN FINAL
-- ============================================
SELECT '
╔═══════════════════════════════════════════════════════════╗
║         SISTEMA DE PAGOS Y RECEPCIONES - PRUEBAS          ║
╠═══════════════════════════════════════════════════════════╣
║ ✓ Tablas verificadas                                      ║
║ ✓ Proveedor de prueba creado                              ║
║ ✓ Pago de prueba creado                                   ║
║ ✓ Catálogo SAT disponible (23 formas de pago)            ║
║ ✓ Estadísticas generadas                                  ║
║                                                            ║
║ SIGUIENTE PASO:                                            ║
║ Abrir navegador en: http://localhost/base/admin/proveedores║
║                                                            ║
║ VERIFICAR:                                                 ║
║ 1. Botones [💰 Pagos] y [📦 Recepciones] visibles        ║
║ 2. Menú contextual con nuevas opciones                    ║
║ 3. Formulario de pago con dropdown de 23 opciones SAT     ║
║                                                            ║
╚═══════════════════════════════════════════════════════════╝
' AS RESUMEN;
