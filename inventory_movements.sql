-- =============================================
-- MÓDULO: INVENTARIO - MOVIMIENTOS Y UBICACIONES
-- Descripción: Sistema completo de control de inventario con:
--   - Movimientos (entradas, salidas, traspasos, ajustes)
--   - Ubicaciones en almacén (zonas, pasillos, racks)
--   - Asignación de productos a ubicaciones
-- =============================================

-- ========== TABLA 1: ZONAS DE ALMACÉN ==========
CREATE TABLE IF NOT EXISTS `warehouse_zones` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `warehouse_id` INT(11) UNSIGNED NOT NULL COMMENT 'Almacén al que pertenece',
  `code` VARCHAR(20) NOT NULL COMMENT 'Código de zona: A, B, C',
  `name` VARCHAR(100) NOT NULL COMMENT 'Nombre descriptivo',
  `type` ENUM('storage','picking','receiving','shipping','cold','hazardous') DEFAULT 'storage' COMMENT 'Tipo de zona',
  `description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_zone_code` (`warehouse_id`, `code`),
  KEY `idx_zone_warehouse` (`warehouse_id`),
  KEY `idx_zone_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Zonas/áreas dentro de cada almacén';

-- ========== TABLA 2: UBICACIONES ESPECÍFICAS ==========
CREATE TABLE IF NOT EXISTS `warehouse_locations` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `warehouse_id` INT(11) UNSIGNED NOT NULL COMMENT 'Almacén',
  `zone_id` INT(11) UNSIGNED NULL COMMENT 'Zona del almacén',
  `code` VARCHAR(50) NOT NULL COMMENT 'Código: A1-R2-N3 (Pasillo-Rack-Nivel)',
  `aisle` VARCHAR(10) NULL COMMENT 'Pasillo',
  `rack` VARCHAR(10) NULL COMMENT 'Rack/Estante',
  `level` VARCHAR(10) NULL COMMENT 'Nivel/Altura',
  `bin` VARCHAR(10) NULL COMMENT 'Compartimento',
  `capacity` DECIMAL(10,2) NULL COMMENT 'Capacidad máxima',
  `current_usage` DECIMAL(10,2) DEFAULT 0 COMMENT 'Uso actual',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `notes` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_location_code` (`warehouse_id`, `code`),
  KEY `idx_location_warehouse` (`warehouse_id`),
  KEY `idx_location_zone` (`zone_id`),
  KEY `idx_location_active` (`is_active`),
  CONSTRAINT `fk_location_zone` FOREIGN KEY (`zone_id`) REFERENCES `warehouse_zones` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Ubicaciones específicas en almacén';

-- ========== TABLA 3: ASIGNACIÓN PRODUCTO-UBICACIÓN ==========
CREATE TABLE IF NOT EXISTS `inventory_locations` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) UNSIGNED NOT NULL COMMENT 'Producto',
  `warehouse_id` INT(11) UNSIGNED NOT NULL COMMENT 'Almacén',
  `location_id` INT(11) UNSIGNED NULL COMMENT 'Ubicación específica',
  `quantity` DECIMAL(15,4) NOT NULL DEFAULT 0 COMMENT 'Cantidad en esta ubicación',
  `is_primary` TINYINT(1) DEFAULT 0 COMMENT 'Ubicación principal del producto',
  `batch_number` VARCHAR(50) NULL COMMENT 'Lote',
  `expiry_date` DATE NULL COMMENT 'Fecha de caducidad',
  `notes` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_product_location` (`product_id`, `warehouse_id`, `location_id`, `batch_number`),
  KEY `idx_invloc_product` (`product_id`),
  KEY `idx_invloc_warehouse` (`warehouse_id`),
  KEY `idx_invloc_location` (`location_id`),
  KEY `idx_invloc_batch` (`batch_number`),
  CONSTRAINT `fk_invloc_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_invloc_location` FOREIGN KEY (`location_id`) REFERENCES `warehouse_locations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Asignación de productos a ubicaciones específicas';

-- ========== TABLA 4: MOVIMIENTOS DE INVENTARIO ==========
CREATE TABLE IF NOT EXISTS `inventory_movements` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) NOT NULL COMMENT 'Código único: ENT-YYYYMM-####',
  `type` ENUM('entry','exit','transfer','adjustment','relocation') NOT NULL COMMENT 'Tipo de movimiento',
  `subtype` VARCHAR(50) NULL COMMENT 'Subtipo: compra, venta, devolucion, merma, etc',
  `warehouse_id` INT(11) UNSIGNED NOT NULL COMMENT 'Almacén origen',
  `warehouse_to_id` INT(11) UNSIGNED NULL COMMENT 'Almacén destino (solo para traspasos)',
  `reference_type` VARCHAR(50) NULL COMMENT 'Tipo de documento origen',
  `reference_id` INT(11) UNSIGNED NULL COMMENT 'ID del documento origen',
  `reference_code` VARCHAR(50) NULL COMMENT 'Código del documento origen',
  `movement_date` DATE NOT NULL COMMENT 'Fecha del movimiento',
  `status` ENUM('draft','pending','approved','applied','cancelled') NOT NULL DEFAULT 'draft',
  `total_items` INT NOT NULL DEFAULT 0,
  `total_quantity` DECIMAL(15,4) NOT NULL DEFAULT 0,
  `total_cost` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `notes` TEXT NULL,
  `reason` TEXT NULL COMMENT 'Motivo del movimiento',
  `approved_by` INT(11) UNSIGNED NULL,
  `approved_at` DATETIME NULL,
  `applied_by` INT(11) UNSIGNED NULL,
  `applied_at` DATETIME NULL,
  `created_by` INT(11) UNSIGNED NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_movement_code` (`code`),
  KEY `idx_movement_type` (`type`),
  KEY `idx_movement_warehouse` (`warehouse_id`),
  KEY `idx_movement_warehouse_to` (`warehouse_to_id`),
  KEY `idx_movement_status` (`status`),
  KEY `idx_movement_date` (`movement_date`),
  KEY `idx_movement_reference` (`reference_type`, `reference_id`),
  KEY `idx_movement_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Movimientos de inventario (entradas, salidas, traspasos, ajustes)';

-- ========== TABLA 5: DETALLE DE MOVIMIENTOS ==========
CREATE TABLE IF NOT EXISTS `inventory_movement_items` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `movement_id` INT(11) UNSIGNED NOT NULL COMMENT 'Movimiento padre',
  `product_id` INT(11) UNSIGNED NOT NULL COMMENT 'Producto',
  `location_from_id` INT(11) UNSIGNED NULL COMMENT 'Ubicación origen',
  `location_to_id` INT(11) UNSIGNED NULL COMMENT 'Ubicación destino',
  `quantity` DECIMAL(15,4) NOT NULL COMMENT 'Cantidad',
  `unit_cost` DECIMAL(15,4) NOT NULL DEFAULT 0 COMMENT 'Costo unitario',
  `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Subtotal = quantity * unit_cost',
  `batch_number` VARCHAR(50) NULL COMMENT 'Lote',
  `expiry_date` DATE NULL COMMENT 'Fecha de caducidad',
  `notes` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_movitem_movement` (`movement_id`),
  KEY `idx_movitem_product` (`product_id`),
  KEY `idx_movitem_location_from` (`location_from_id`),
  KEY `idx_movitem_location_to` (`location_to_id`),
  CONSTRAINT `fk_movitem_movement` FOREIGN KEY (`movement_id`) REFERENCES `inventory_movements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_movitem_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_movitem_location_from` FOREIGN KEY (`location_from_id`) REFERENCES `warehouse_locations` (`id`),
  CONSTRAINT `fk_movitem_location_to` FOREIGN KEY (`location_to_id`) REFERENCES `warehouse_locations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Detalle de productos en movimientos de inventario';

-- ========== ÍNDICES ADICIONALES ==========
CREATE INDEX idx_invloc_expiry ON inventory_locations(expiry_date);
CREATE INDEX idx_invloc_primary ON inventory_locations(is_primary);
CREATE INDEX idx_movement_approved ON inventory_movements(approved_by);
CREATE INDEX idx_movement_applied ON inventory_movements(applied_by);
CREATE INDEX idx_movitem_batch ON inventory_movement_items(batch_number);

-- ========== DATOS INICIALES: ZONAS EJEMPLO ==========
-- Se pueden agregar zonas predeterminadas si existe un almacén con id=1
-- INSERT INTO warehouse_zones (warehouse_id, code, name, type) VALUES
-- (1, 'A', 'Zona A - Almacenamiento General', 'storage'),
-- (1, 'B', 'Zona B - Picking', 'picking'),
-- (1, 'C', 'Zona C - Recepción', 'receiving');
