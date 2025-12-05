-- =============================================
-- MÓDULO: RECEPCIONES DE MERCANCÍA (Purchase Receipts)
-- Descripción: Sistema de recepción física de mercancía al almacén
-- Diferencia con Contrarecibos: Este módulo maneja el ingreso físico
-- al inventario con ubicaciones y condiciones, mientras que contrarecibos
-- es solo verificación documental de la entrega
-- =============================================

CREATE TABLE IF NOT EXISTS `purchase_receipts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) NOT NULL COMMENT 'Código único: REC-YYYYMM-####',
  `purchase_order_id` INT(11) NOT NULL COMMENT 'Orden de compra origen',
  `provider_id` INT(11) UNSIGNED NOT NULL COMMENT 'Proveedor',
  `almacen_id` INT(11) NULL COMMENT 'Almacén destino (si existe en el sistema)',
  `almacen_name` VARCHAR(100) NULL COMMENT 'Nombre del almacén (manual si no existe tabla)',
  `receipt_date` DATE NOT NULL COMMENT 'Fecha de recepción programada',
  `received_date` DATETIME NULL COMMENT 'Fecha/hora real de recepción',
  `received_by` INT(11) UNSIGNED NULL COMMENT 'Usuario que recibió',
  `verified_by` INT(11) UNSIGNED NULL COMMENT 'Usuario que verificó',
  `verified_date` DATETIME NULL COMMENT 'Fecha de verificación',
  `status` ENUM('pending','received','verified','discrepancy','cancelled') NOT NULL DEFAULT 'pending' COMMENT 'Estado del proceso',
  `total_items` INT NOT NULL DEFAULT 0 COMMENT 'Total de líneas de productos',
  `total_quantity_expected` DECIMAL(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'Cantidad total esperada',
  `total_quantity_received` DECIMAL(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'Cantidad total recibida',
  `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor total de la recepción',
  `has_discrepancy` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Tiene discrepancias (1=Sí, 0=No)',
  `discrepancy_notes` TEXT NULL COMMENT 'Notas sobre discrepancias encontradas',
  `notes` TEXT NULL COMMENT 'Notas generales de la recepción',
  `created_by` INT(11) UNSIGNED NULL COMMENT 'Usuario creador',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL COMMENT 'Soft delete',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_receipt_code` (`code`),
  KEY `idx_receipt_po` (`purchase_order_id`),
  KEY `idx_receipt_provider` (`provider_id`),
  KEY `idx_receipt_status` (`status`),
  KEY `idx_receipt_date` (`receipt_date`),
  KEY `idx_receipt_deleted` (`deleted_at`),
  CONSTRAINT `fk_receipt_po` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`),
  CONSTRAINT `fk_receipt_provider` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Recepciones físicas de mercancía al almacén';

CREATE TABLE IF NOT EXISTS `purchase_receipt_items` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_receipt_id` INT(11) UNSIGNED NOT NULL COMMENT 'Recepción padre',
  `purchase_order_item_id` INT(11) NOT NULL COMMENT 'Item de la orden de compra',
  `product_id` INT(11) UNSIGNED NOT NULL COMMENT 'Producto',
  `location` VARCHAR(100) NULL COMMENT 'Ubicación en almacén (pasillo, rack, nivel)',
  `quantity_ordered` DECIMAL(15,4) NOT NULL COMMENT 'Cantidad pedida en OC',
  `quantity_received` DECIMAL(15,4) NOT NULL COMMENT 'Cantidad realmente recibida',
  `unit_cost` DECIMAL(15,4) NOT NULL COMMENT 'Costo unitario',
  `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Subtotal = quantity_received * unit_cost',
  `condition` ENUM('good','damaged','defective','expired') NOT NULL DEFAULT 'good' COMMENT 'Condición del producto recibido',
  `batch_number` VARCHAR(50) NULL COMMENT 'Número de lote',
  `expiry_date` DATE NULL COMMENT 'Fecha de caducidad',
  `notes` TEXT NULL COMMENT 'Notas sobre este item',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_receiptitem_receipt` (`purchase_receipt_id`),
  KEY `idx_receiptitem_poitem` (`purchase_order_item_id`),
  KEY `idx_receiptitem_product` (`product_id`),
  KEY `idx_receiptitem_condition` (`condition`),
  CONSTRAINT `fk_receiptitem_receipt` FOREIGN KEY (`purchase_receipt_id`) REFERENCES `purchase_receipts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_receiptitem_poitem` FOREIGN KEY (`purchase_order_item_id`) REFERENCES `purchase_order_items` (`id`),
  CONSTRAINT `fk_receiptitem_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Detalle de productos recibidos con condiciones';

-- Índices adicionales para reportes
CREATE INDEX idx_receipt_has_discrepancy ON purchase_receipts(has_discrepancy);
CREATE INDEX idx_receipt_received_date ON purchase_receipts(received_date);
CREATE INDEX idx_receiptitem_batch ON purchase_receipt_items(batch_number);
CREATE INDEX idx_receiptitem_expiry ON purchase_receipt_items(expiry_date);
