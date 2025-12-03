-- =====================================================
-- SCRIPT DE CORRECCIÓN DE CHARSET Y COLLATION
-- Para módulo de Proveedores
-- Fecha: 03/12/2025
-- =====================================================

USE base;

-- Configurar base de datos
ALTER DATABASE base CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- =====================================================
-- TABLAS DE PROVEEDORES
-- =====================================================

-- providers (ya está en utf8mb4_unicode_ci - verificar columnas)
ALTER TABLE providers CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_accounts
ALTER TABLE providers_accounts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_action_logs (ya está en utf8mb4_unicode_ci)
ALTER TABLE providers_action_logs CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_addresses
ALTER TABLE providers_addresses CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_billing_config (ya está en utf8mb4_unicode_ci)
ALTER TABLE providers_billing_config CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_bills
ALTER TABLE providers_bills CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_bills_details
ALTER TABLE providers_bills_details CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_bills_rep
ALTER TABLE providers_bills_rep CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_contacts
ALTER TABLE providers_contacts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_creditnote_bills
ALTER TABLE providers_creditnote_bills CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_credit_notes
ALTER TABLE providers_credit_notes CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_delivery
ALTER TABLE providers_delivery CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_email_confirmations
ALTER TABLE providers_email_confirmations CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_login_attempts
ALTER TABLE providers_login_attempts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_logs
ALTER TABLE providers_logs CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_orders
ALTER TABLE providers_orders CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_orders_details
ALTER TABLE providers_orders_details CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_orders_status_log
ALTER TABLE providers_orders_status_log CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_purchases
ALTER TABLE providers_purchases CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_receipts
ALTER TABLE providers_receipts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_receipts_details
ALTER TABLE providers_receipts_details CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_tax_data
ALTER TABLE providers_tax_data CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_tickets
ALTER TABLE providers_tickets CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- providers_tickets_messages
ALTER TABLE providers_tickets_messages CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- =====================================================
-- TABLAS DEL SISTEMA (módulos relacionados)
-- =====================================================

-- system_modules
ALTER TABLE system_modules CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- tenant_modules
ALTER TABLE tenant_modules CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================

SELECT 
    table_name,
    table_collation,
    CASE 
        WHEN table_collation = 'utf8mb4_unicode_ci' THEN '✓ OK'
        ELSE '✗ REVISAR'
    END AS status
FROM information_schema.tables 
WHERE table_schema = 'base' 
AND table_name LIKE '%provider%'
ORDER BY table_name;

-- =====================================================
-- MENSAJE FINAL
-- =====================================================

SELECT 'Corrección de charset completada. Todas las tablas de proveedores ahora usan utf8mb4_unicode_ci' AS mensaje;
