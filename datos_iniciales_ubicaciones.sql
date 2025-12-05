-- =============================================
-- DATOS INICIALES: ZONAS Y UBICACIONES PARA ALMACÉN PRINCIPAL
-- =============================================

-- ========== ZONAS DE ALMACÉN ==========
INSERT INTO warehouse_zones (warehouse_id, code, name, type, description) VALUES
(1, 'A', 'Zona A - Almacenamiento General', 'storage', 'Área principal de almacenamiento de productos'),
(1, 'B', 'Zona B - Picking', 'picking', 'Área de preparación de pedidos'),
(1, 'C', 'Zona C - Recepción', 'receiving', 'Área de recepción de mercancías');

-- ========== UBICACIONES ESPECÍFICAS ==========
-- Ubicaciones básicas sin zona asignada (para empezar)
INSERT INTO warehouse_locations (warehouse_id, zone_id, code, aisle, rack, level, bin, capacity) VALUES
-- Pasillo A
(1, 1, 'A1-R1-N1', 'A1', 'R1', 'N1', NULL, 100),
(1, 1, 'A1-R1-N2', 'A1', 'R1', 'N2', NULL, 100),
(1, 1, 'A1-R2-N1', 'A1', 'R2', 'N1', NULL, 100),
(1, 1, 'A1-R2-N2', 'A1', 'R2', 'N2', NULL, 100),

-- Pasillo A2
(1, 1, 'A2-R1-N1', 'A2', 'R1', 'N1', NULL, 100),
(1, 1, 'A2-R1-N2', 'A2', 'R1', 'N2', NULL, 100),

-- Zona de Picking
(1, 2, 'B1-R1-N1', 'B1', 'R1', 'N1', NULL, 50),
(1, 2, 'B1-R2-N1', 'B1', 'R2', 'N1', NULL, 50),

-- Zona de Recepción (temporal)
(1, 3, 'C1-TEMP', 'C1', 'TEMP', 'N1', NULL, 200),
(1, 3, 'C2-TEMP', 'C2', 'TEMP', 'N1', NULL, 200);

-- Ubicación genérica (sin ubicación específica)
INSERT INTO warehouse_locations (warehouse_id, zone_id, code, aisle, capacity, notes) VALUES
(1, NULL, 'GENERAL', 'GEN', 1000, 'Ubicación general sin asignar');

-- ========== VERIFICACIÓN ==========
SELECT 'Zonas creadas:' as info;
SELECT id, code, name, type FROM warehouse_zones;

SELECT 'Ubicaciones creadas:' as info;
SELECT id, warehouse_id, code, capacity FROM warehouse_locations;
