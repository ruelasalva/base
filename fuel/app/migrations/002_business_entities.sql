-- ============================================================================
-- SQL para Entidades de Negocio del ERP
-- FuelPHP ERP Multi-tenant
-- ============================================================================
-- 
-- DESCRIPCIÓN:
-- Este archivo contiene las tablas para las entidades principales del negocio:
-- - Categories (Categorías)
-- - Products (Productos)
-- - Product Details (Detalles de productos)
-- - Providers (Proveedores)
-- - Customers (Clientes)
-- - Orders (Pedidos)
-- - Order Items (Items de pedidos)
-- - Inventory (Inventario)
--
-- NOTA: Todas las tablas usan nombres en inglés como estándar internacional
--
-- ============================================================================

-- ============================================================================
-- CATEGORÍAS
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: categories
-- Stores product categories in hierarchical structure
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Parent category ID for hierarchy',
    `name` VARCHAR(100) NOT NULL COMMENT 'Category name',
    `slug` VARCHAR(100) NOT NULL COMMENT 'URL-friendly name',
    `description` TEXT DEFAULT NULL COMMENT 'Category description',
    `image` VARCHAR(255) DEFAULT NULL COMMENT 'Category image path',
    `sort_order` INT(11) NOT NULL DEFAULT 0 COMMENT 'Display order',
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_slug` (`slug`),
    KEY `idx_parent_id` (`parent_id`),
    KEY `idx_is_active` (`is_active`),
    KEY `idx_sort_order` (`sort_order`),
    CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Product categories';


-- ============================================================================
-- PROVEEDORES
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: providers
-- Stores provider/supplier information
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `providers` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(50) DEFAULT NULL COMMENT 'Unique provider code',
    `company_name` VARCHAR(255) NOT NULL COMMENT 'Company or business name',
    `contact_name` VARCHAR(100) DEFAULT NULL COMMENT 'Contact person name',
    `email` VARCHAR(255) DEFAULT NULL COMMENT 'Contact email',
    `phone` VARCHAR(20) DEFAULT NULL COMMENT 'Main phone number',
    `phone_secondary` VARCHAR(20) DEFAULT NULL COMMENT 'Secondary phone',
    `address` VARCHAR(255) DEFAULT NULL COMMENT 'Street address',
    `city` VARCHAR(100) DEFAULT NULL COMMENT 'City',
    `state` VARCHAR(100) DEFAULT NULL COMMENT 'State/Province',
    `postal_code` VARCHAR(20) DEFAULT NULL COMMENT 'Postal/ZIP code',
    `country` VARCHAR(100) DEFAULT NULL COMMENT 'Country',
    `tax_id` VARCHAR(50) DEFAULT NULL COMMENT 'Tax identification number',
    `website` VARCHAR(255) DEFAULT NULL COMMENT 'Website URL',
    `notes` TEXT DEFAULT NULL COMMENT 'Additional notes',
    `payment_terms` INT(11) UNSIGNED DEFAULT 30 COMMENT 'Payment terms in days',
    `credit_limit` DECIMAL(15,2) DEFAULT NULL COMMENT 'Credit limit amount',
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_code` (`code`),
    KEY `idx_company_name` (`company_name`),
    KEY `idx_email` (`email`),
    KEY `idx_is_active` (`is_active`),
    KEY `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Providers/Suppliers';


-- ============================================================================
-- PRODUCTOS
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: products
-- Stores product information
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `sku` VARCHAR(50) NOT NULL COMMENT 'Stock Keeping Unit - unique product code',
    `barcode` VARCHAR(50) DEFAULT NULL COMMENT 'Product barcode (EAN/UPC)',
    `name` VARCHAR(255) NOT NULL COMMENT 'Product name',
    `slug` VARCHAR(255) NOT NULL COMMENT 'URL-friendly name',
    `short_description` VARCHAR(500) DEFAULT NULL COMMENT 'Brief description',
    `description` TEXT DEFAULT NULL COMMENT 'Full product description',
    `category_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Primary category',
    `provider_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Main provider/supplier',
    `brand` VARCHAR(100) DEFAULT NULL COMMENT 'Brand name',
    `model` VARCHAR(100) DEFAULT NULL COMMENT 'Model number',
    `unit` VARCHAR(20) DEFAULT 'unit' COMMENT 'Unit of measure (unit, kg, liter, etc.)',
    `cost_price` DECIMAL(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'Purchase/cost price',
    `sale_price` DECIMAL(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'Sale price',
    `wholesale_price` DECIMAL(15,4) DEFAULT NULL COMMENT 'Wholesale price',
    `min_price` DECIMAL(15,4) DEFAULT NULL COMMENT 'Minimum allowed sale price',
    `tax_rate` DECIMAL(5,2) DEFAULT NULL COMMENT 'Tax rate percentage',
    `weight` DECIMAL(10,3) DEFAULT NULL COMMENT 'Weight in kg',
    `length` DECIMAL(10,2) DEFAULT NULL COMMENT 'Length in cm',
    `width` DECIMAL(10,2) DEFAULT NULL COMMENT 'Width in cm',
    `height` DECIMAL(10,2) DEFAULT NULL COMMENT 'Height in cm',
    `min_stock` INT(11) DEFAULT 0 COMMENT 'Minimum stock level alert',
    `max_stock` INT(11) DEFAULT NULL COMMENT 'Maximum stock level',
    `stock_quantity` INT(11) NOT NULL DEFAULT 0 COMMENT 'Current stock quantity',
    `is_featured` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1=featured product',
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
    `is_available` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=available for sale',
    `sort_order` INT(11) NOT NULL DEFAULT 0 COMMENT 'Display order',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_sku` (`sku`),
    UNIQUE KEY `idx_slug` (`slug`),
    KEY `idx_barcode` (`barcode`),
    KEY `idx_category_id` (`category_id`),
    KEY `idx_provider_id` (`provider_id`),
    KEY `idx_name` (`name`),
    KEY `idx_is_active` (`is_active`),
    KEY `idx_is_featured` (`is_featured`),
    KEY `idx_deleted_at` (`deleted_at`),
    CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_products_provider` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Products catalog';


-- -----------------------------------------------------------------------------
-- Table: product_images
-- Stores product images
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_images` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id` INT(11) UNSIGNED NOT NULL,
    `image_path` VARCHAR(255) NOT NULL COMMENT 'Image file path',
    `alt_text` VARCHAR(255) DEFAULT NULL COMMENT 'Alt text for accessibility',
    `sort_order` INT(11) NOT NULL DEFAULT 0 COMMENT 'Display order',
    `is_primary` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1=primary image',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_product_id` (`product_id`),
    KEY `idx_sort_order` (`sort_order`),
    CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Product images';


-- -----------------------------------------------------------------------------
-- Table: product_attributes
-- Stores product custom attributes (key-value pairs)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_attributes` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id` INT(11) UNSIGNED NOT NULL,
    `attribute_name` VARCHAR(100) NOT NULL COMMENT 'Attribute name (e.g., Color, Size)',
    `attribute_value` VARCHAR(255) NOT NULL COMMENT 'Attribute value',
    `sort_order` INT(11) NOT NULL DEFAULT 0 COMMENT 'Display order',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_product_attribute` (`product_id`, `attribute_name`),
    KEY `idx_product_id` (`product_id`),
    CONSTRAINT `fk_product_attributes_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Product custom attributes';


-- -----------------------------------------------------------------------------
-- Table: product_categories
-- Many-to-many relationship between products and categories
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_categories` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id` INT(11) UNSIGNED NOT NULL,
    `category_id` INT(11) UNSIGNED NOT NULL,
    `is_primary` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1=primary category',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_product_category` (`product_id`, `category_id`),
    KEY `idx_product_id` (`product_id`),
    KEY `idx_category_id` (`category_id`),
    CONSTRAINT `fk_product_categories_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_product_categories_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Product to category associations';


-- ============================================================================
-- CLIENTES
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: customers
-- Stores customer information
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `customers` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Link to users table if registered',
    `code` VARCHAR(50) DEFAULT NULL COMMENT 'Unique customer code',
    `customer_type` ENUM('individual', 'business') NOT NULL DEFAULT 'individual' COMMENT 'Type of customer',
    `company_name` VARCHAR(255) DEFAULT NULL COMMENT 'Company name (for business)',
    `first_name` VARCHAR(100) DEFAULT NULL COMMENT 'First name',
    `last_name` VARCHAR(100) DEFAULT NULL COMMENT 'Last name',
    `email` VARCHAR(255) DEFAULT NULL COMMENT 'Email address',
    `phone` VARCHAR(20) DEFAULT NULL COMMENT 'Primary phone',
    `phone_secondary` VARCHAR(20) DEFAULT NULL COMMENT 'Secondary phone',
    `tax_id` VARCHAR(50) DEFAULT NULL COMMENT 'Tax identification number',
    `credit_limit` DECIMAL(15,2) DEFAULT NULL COMMENT 'Credit limit',
    `balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Current balance',
    `notes` TEXT DEFAULT NULL COMMENT 'Additional notes',
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_code` (`code`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_email` (`email`),
    KEY `idx_company_name` (`company_name`),
    KEY `idx_is_active` (`is_active`),
    KEY `idx_deleted_at` (`deleted_at`),
    CONSTRAINT `fk_customers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Customer records';


-- -----------------------------------------------------------------------------
-- Table: customer_addresses
-- Stores customer shipping/billing addresses
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `customer_addresses` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `customer_id` INT(11) UNSIGNED NOT NULL,
    `address_type` ENUM('billing', 'shipping', 'both') NOT NULL DEFAULT 'both' COMMENT 'Address type',
    `is_default` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1=default address',
    `recipient_name` VARCHAR(100) DEFAULT NULL COMMENT 'Recipient name',
    `address_line1` VARCHAR(255) NOT NULL COMMENT 'Address line 1',
    `address_line2` VARCHAR(255) DEFAULT NULL COMMENT 'Address line 2',
    `city` VARCHAR(100) NOT NULL COMMENT 'City',
    `state` VARCHAR(100) DEFAULT NULL COMMENT 'State/Province',
    `postal_code` VARCHAR(20) DEFAULT NULL COMMENT 'Postal/ZIP code',
    `country` VARCHAR(100) NOT NULL DEFAULT 'Mexico' COMMENT 'Country',
    `phone` VARCHAR(20) DEFAULT NULL COMMENT 'Contact phone',
    `notes` TEXT DEFAULT NULL COMMENT 'Delivery instructions',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_customer_id` (`customer_id`),
    KEY `idx_address_type` (`address_type`),
    CONSTRAINT `fk_customer_addresses_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Customer addresses';


-- ============================================================================
-- PEDIDOS
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: orders
-- Stores order headers
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_number` VARCHAR(50) NOT NULL COMMENT 'Unique order number',
    `customer_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Customer ID',
    `user_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Sales person user ID',
    `order_type` ENUM('sale', 'quote', 'return') NOT NULL DEFAULT 'sale' COMMENT 'Order type',
    `status` ENUM('pending', 'processing', 'completed', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending' COMMENT 'Order status',
    `payment_status` ENUM('pending', 'partial', 'paid', 'refunded') NOT NULL DEFAULT 'pending' COMMENT 'Payment status',
    `shipping_status` ENUM('pending', 'shipped', 'delivered', 'returned') DEFAULT NULL COMMENT 'Shipping status',
    `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Subtotal before tax/discount',
    `discount_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Discount amount',
    `discount_percent` DECIMAL(5,2) DEFAULT NULL COMMENT 'Discount percentage',
    `tax_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Tax amount',
    `shipping_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Shipping cost',
    `total` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Grand total',
    `amount_paid` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount paid',
    `currency` VARCHAR(3) NOT NULL DEFAULT 'MXN' COMMENT 'Currency code',
    `billing_address_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Billing address',
    `shipping_address_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Shipping address',
    `notes` TEXT DEFAULT NULL COMMENT 'Order notes',
    `internal_notes` TEXT DEFAULT NULL COMMENT 'Internal staff notes',
    `ordered_at` DATETIME DEFAULT NULL COMMENT 'Order date',
    `shipped_at` DATETIME DEFAULT NULL COMMENT 'Ship date',
    `delivered_at` DATETIME DEFAULT NULL COMMENT 'Delivery date',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_order_number` (`order_number`),
    KEY `idx_customer_id` (`customer_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_status` (`status`),
    KEY `idx_payment_status` (`payment_status`),
    KEY `idx_ordered_at` (`ordered_at`),
    KEY `idx_deleted_at` (`deleted_at`),
    CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_orders_billing_address` FOREIGN KEY (`billing_address_id`) REFERENCES `customer_addresses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_orders_shipping_address` FOREIGN KEY (`shipping_address_id`) REFERENCES `customer_addresses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sales orders';


-- -----------------------------------------------------------------------------
-- Table: order_items
-- Stores order line items/details
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id` INT(11) UNSIGNED NOT NULL,
    `product_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Product ID',
    `sku` VARCHAR(50) DEFAULT NULL COMMENT 'Product SKU at time of order',
    `name` VARCHAR(255) NOT NULL COMMENT 'Product name at time of order',
    `description` TEXT DEFAULT NULL COMMENT 'Item description',
    `quantity` DECIMAL(15,4) NOT NULL DEFAULT 1.0000 COMMENT 'Quantity ordered',
    `unit_price` DECIMAL(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'Unit price',
    `cost_price` DECIMAL(15,4) DEFAULT NULL COMMENT 'Cost price at time of sale',
    `discount_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Line discount',
    `discount_percent` DECIMAL(5,2) DEFAULT NULL COMMENT 'Line discount percentage',
    `tax_rate` DECIMAL(5,2) DEFAULT NULL COMMENT 'Tax rate',
    `tax_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Line tax amount',
    `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Line subtotal',
    `total` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Line total',
    `notes` TEXT DEFAULT NULL COMMENT 'Item notes',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_order_id` (`order_id`),
    KEY `idx_product_id` (`product_id`),
    CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Order line items';


-- ============================================================================
-- INVENTARIO
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: warehouses
-- Stores warehouse locations
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `warehouses` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(50) NOT NULL COMMENT 'Unique warehouse code',
    `name` VARCHAR(100) NOT NULL COMMENT 'Warehouse name',
    `address` VARCHAR(255) DEFAULT NULL COMMENT 'Address',
    `city` VARCHAR(100) DEFAULT NULL COMMENT 'City',
    `phone` VARCHAR(20) DEFAULT NULL COMMENT 'Phone',
    `manager_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Manager user ID',
    `is_default` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1=default warehouse',
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_code` (`code`),
    KEY `idx_manager_id` (`manager_id`),
    KEY `idx_is_active` (`is_active`),
    CONSTRAINT `fk_warehouses_manager` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Warehouse locations';


-- -----------------------------------------------------------------------------
-- Table: inventory
-- Stores product inventory per warehouse
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `inventory` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id` INT(11) UNSIGNED NOT NULL,
    `warehouse_id` INT(11) UNSIGNED NOT NULL,
    `quantity` INT(11) NOT NULL DEFAULT 0 COMMENT 'Current quantity',
    `reserved` INT(11) NOT NULL DEFAULT 0 COMMENT 'Reserved/committed quantity',
    `available` INT(11) GENERATED ALWAYS AS (`quantity` - `reserved`) VIRTUAL COMMENT 'Available quantity',
    `location` VARCHAR(50) DEFAULT NULL COMMENT 'Location within warehouse (aisle, shelf, bin)',
    `last_counted_at` DATETIME DEFAULT NULL COMMENT 'Last inventory count date',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_product_warehouse` (`product_id`, `warehouse_id`),
    KEY `idx_product_id` (`product_id`),
    KEY `idx_warehouse_id` (`warehouse_id`),
    CONSTRAINT `fk_inventory_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_inventory_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Product inventory per warehouse';


-- -----------------------------------------------------------------------------
-- Table: inventory_movements
-- Stores inventory transaction history
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `inventory_movements` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id` INT(11) UNSIGNED NOT NULL,
    `warehouse_id` INT(11) UNSIGNED NOT NULL,
    `movement_type` ENUM('in', 'out', 'transfer', 'adjustment') NOT NULL COMMENT 'Type of movement',
    `reference_type` VARCHAR(50) DEFAULT NULL COMMENT 'Reference type (order, purchase, adjustment)',
    `reference_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Reference ID',
    `quantity` INT(11) NOT NULL COMMENT 'Quantity moved (positive for in, negative for out)',
    `quantity_before` INT(11) NOT NULL COMMENT 'Quantity before movement',
    `quantity_after` INT(11) NOT NULL COMMENT 'Quantity after movement',
    `unit_cost` DECIMAL(15,4) DEFAULT NULL COMMENT 'Unit cost at time of movement',
    `notes` TEXT DEFAULT NULL COMMENT 'Movement notes',
    `user_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'User who made the movement',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_product_id` (`product_id`),
    KEY `idx_warehouse_id` (`warehouse_id`),
    KEY `idx_movement_type` (`movement_type`),
    KEY `idx_reference` (`reference_type`, `reference_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_inventory_movements_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_inventory_movements_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_inventory_movements_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Inventory movement history';


-- ============================================================================
-- COMPRAS (PURCHASE ORDERS)
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: purchase_orders
-- Stores purchase order headers
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `purchase_orders` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `po_number` VARCHAR(50) NOT NULL COMMENT 'Unique purchase order number',
    `provider_id` INT(11) UNSIGNED NOT NULL COMMENT 'Provider ID',
    `warehouse_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Receiving warehouse',
    `user_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Created by user',
    `status` ENUM('draft', 'pending', 'approved', 'ordered', 'partial', 'received', 'cancelled') NOT NULL DEFAULT 'draft' COMMENT 'PO status',
    `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Subtotal',
    `discount_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Discount',
    `tax_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Tax',
    `shipping_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Shipping',
    `total` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Grand total',
    `currency` VARCHAR(3) NOT NULL DEFAULT 'MXN' COMMENT 'Currency',
    `expected_date` DATE DEFAULT NULL COMMENT 'Expected delivery date',
    `received_date` DATETIME DEFAULT NULL COMMENT 'Actual received date',
    `notes` TEXT DEFAULT NULL COMMENT 'Notes',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL COMMENT 'Soft delete',
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_po_number` (`po_number`),
    KEY `idx_provider_id` (`provider_id`),
    KEY `idx_warehouse_id` (`warehouse_id`),
    KEY `idx_status` (`status`),
    KEY `idx_deleted_at` (`deleted_at`),
    CONSTRAINT `fk_purchase_orders_provider` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_purchase_orders_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_purchase_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Purchase orders';


-- -----------------------------------------------------------------------------
-- Table: purchase_order_items
-- Stores purchase order line items
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `purchase_order_items` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `purchase_order_id` INT(11) UNSIGNED NOT NULL,
    `product_id` INT(11) UNSIGNED DEFAULT NULL,
    `sku` VARCHAR(50) DEFAULT NULL COMMENT 'Product SKU',
    `name` VARCHAR(255) NOT NULL COMMENT 'Product name',
    `quantity_ordered` DECIMAL(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'Quantity ordered',
    `quantity_received` DECIMAL(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'Quantity received',
    `unit_cost` DECIMAL(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'Unit cost',
    `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Line subtotal',
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_purchase_order_id` (`purchase_order_id`),
    KEY `idx_product_id` (`product_id`),
    CONSTRAINT `fk_purchase_order_items_po` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_purchase_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Purchase order line items';


-- ============================================================================
-- DATOS INICIALES
-- ============================================================================

-- Insert default warehouse
INSERT INTO `warehouses` (`code`, `name`, `is_default`, `is_active`) VALUES
('MAIN', 'Main Warehouse', 1, 1);

-- Insert default categories
INSERT INTO `categories` (`name`, `slug`, `description`, `sort_order`, `is_active`) VALUES
('General', 'general', 'General products category', 0, 1);
