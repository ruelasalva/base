-- Tabla de contrarecibos (delivery notes / goods receipt notes)
CREATE TABLE IF NOT EXISTS `delivery_notes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) NOT NULL COMMENT 'Código único CR-YYYYMM-####',
  `purchase_id` INT UNSIGNED NULL COMMENT 'FK a tabla purchases (factura asociada)',
  `purchase_order_id` INT UNSIGNED NULL COMMENT 'FK a tabla purchase_orders (orden asociada)',
  `provider_id` INT UNSIGNED NOT NULL COMMENT 'FK a tabla providers',
  `delivery_date` DATE NOT NULL COMMENT 'Fecha de entrega del proveedor',
  `received_date` DATE NULL COMMENT 'Fecha de recepción en almacén',
  `received_by` INT UNSIGNED NULL COMMENT 'FK a users - quien recibió',
  `status` ENUM('pending','partial','completed','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `notes` TEXT NULL COMMENT 'Notas generales del contrarecibo',
  `created_by` INT UNSIGNED NOT NULL COMMENT 'FK a users - quien creó',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `deleted_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `purchase_id` (`purchase_id`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `provider_id` (`provider_id`),
  KEY `received_by` (`received_by`),
  KEY `created_by` (`created_by`),
  KEY `status` (`status`),
  KEY `delivery_date` (`delivery_date`),
  KEY `deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de líneas de contrarecibos (productos recibidos)
CREATE TABLE IF NOT EXISTS `delivery_note_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `delivery_note_id` INT UNSIGNED NOT NULL COMMENT 'FK a delivery_notes',
  `product_id` INT UNSIGNED NOT NULL COMMENT 'FK a products',
  `quantity_ordered` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Cantidad ordenada',
  `quantity_received` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Cantidad recibida',
  `unit_price` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Precio unitario',
  `notes` TEXT NULL COMMENT 'Notas sobre esta línea (diferencias, daños, etc)',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_note_id` (`delivery_note_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_dni_delivery_note` FOREIGN KEY (`delivery_note_id`) REFERENCES `delivery_notes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
