-- ============================================================================
-- SQL para M\u00f3dulos Extendidos del ERP - Estructura Completa  
-- Migrado desde base de datos existente
-- FuelPHP ERP Multi-tenant
-- ============================================================================
--
-- NOTA: Esta migración contiene todas las tablas del Sistema ERP original
-- Total de tablas: 156
--
-- ============================================================================

SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_chart_id` int(11) NOT NULL COMMENT 'FK a accounts_chart.id (cuenta del plan contable)',
  `partner_id` int(11) DEFAULT NULL COMMENT 'Socio de negocio relacionado (cliente/proveedor)',
  `code` varchar(50) NOT NULL COMMENT 'C├│digo interno de la cuenta operativa',
  `name` varchar(150) NOT NULL COMMENT 'Nombre o descripci├│n de la cuenta',
  `currency_id` int(11) DEFAULT NULL COMMENT 'Moneda principal de la cuenta',
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Saldo actual',
  `limit_amount` decimal(15,2) DEFAULT NULL COMMENT 'L├¡mite o tope de saldo permitido',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Cuenta activa',
  `is_cash_account` tinyint(1) NOT NULL DEFAULT 0 COMMENT '┬┐Es cuenta de efectivo o banco?',
  `deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Borrado l├│gico',
  `created_at` int(11) NOT NULL COMMENT 'Fecha de creaci├│n (timestamp UNIX)',
  `updated_at` int(11) NOT NULL COMMENT 'Fecha de actualizaci├│n (timestamp UNIX)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_accounts_code` (`code`),
  KEY `idx_accounts_chart` (`account_chart_id`),
  KEY `idx_accounts_partner` (`partner_id`),
  KEY `idx_accounts_currency` (`currency_id`),
  CONSTRAINT `fk_accounts_chart` FOREIGN KEY (`account_chart_id`) REFERENCES `accounts_chart` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_accounts_currency` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Cuentas operativas del ERP ligadas al plan contable';
CREATE TABLE `accounts_chart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL COMMENT 'C├│digo contable (ej. 1001-01-0001)',
  `name` varchar(150) NOT NULL COMMENT 'Nombre de la cuenta',
  `type` varchar(50) NOT NULL COMMENT 'Tipo de cuenta (Activo, Pasivo, Ingreso, etc.)',
  `parent_id` int(11) DEFAULT NULL COMMENT 'Cuenta padre (jerarqu├¡a)',
  `level` int(11) NOT NULL DEFAULT 1 COMMENT 'Nivel jer├írquico',
  `currency_id` int(11) DEFAULT NULL COMMENT 'FK a tabla currencies',
  `is_confidential` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Cuenta confidencial',
  `is_cash_account` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Cuenta de efectivo',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Cuenta activa',
  `annex24_code` varchar(50) DEFAULT NULL COMMENT 'C├│digo Anexo 24 SAT',
  `account_class` varchar(50) DEFAULT NULL COMMENT 'Clase de cuenta',
  `deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Borrado l├│gico',
  `created_at` int(11) NOT NULL COMMENT 'Fecha creaci├│n (timestamp UNIX)',
  `updated_at` int(11) NOT NULL COMMENT 'Fecha actualizaci├│n (timestamp UNIX)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_accounts_chart_code` (`code`),
  KEY `idx_accounts_chart_parent` (`parent_id`),
  KEY `idx_accounts_chart_currency` (`currency_id`),
  CONSTRAINT `fk_accounts_chart_currency` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_accounts_chart_parent` FOREIGN KEY (`parent_id`) REFERENCES `accounts_chart` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1607 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Cat├ílogo maestro del plan de cuentas contable';
CREATE TABLE `activitys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `act_num` varchar(255) NOT NULL,
  `customer` varchar(250) NOT NULL,
  `company` varchar(250) NOT NULL,
  `user_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `hour` varchar(255) NOT NULL,
  `invoice` int(11) NOT NULL,
  `foreing` int(11) NOT NULL,
  `time_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `total` int(11) DEFAULT NULL,
  `comments` text NOT NULL,
  `global_date` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `activitys_methods_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `activitys_nums` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `act_num` varchar(255) NOT NULL,
  `date` int(11) NOT NULL,
  `completed` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `activitys_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `activitys_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `activitys_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `amounts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `anonymous_cookies_accepts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(64) NOT NULL COMMENT 'Identificador ├║nico para an├│nimo',
  `necessary` tinyint(4) DEFAULT 0 COMMENT 'Siempre 0 = obligatorio',
  `analytics` tinyint(4) DEFAULT 1 COMMENT '0 = acepta, 1 = rechaza',
  `marketing` tinyint(4) DEFAULT 1 COMMENT '0 = acepta, 1 = rechaza',
  `personalization` tinyint(4) DEFAULT 1 COMMENT '0 = acepta, 1 = rechaza',
  `accepted_at` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `appearance_footer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logo_main` varchar(255) DEFAULT NULL,
  `logo_secondary` varchar(255) DEFAULT NULL,
  `customer_service` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `office_hours_week` varchar(100) DEFAULT NULL,
  `office_hours_weekend` varchar(100) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `tiktok` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `telegram` varchar(255) DEFAULT NULL,
  `pinterest` varchar(255) DEFAULT NULL,
  `snapchat` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `appearance_footer_badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `footer_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `footer_id` (`footer_id`),
  CONSTRAINT `appearance_footer_badges_ibfk_1` FOREIGN KEY (`footer_id`) REFERENCES `appearance_footer` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `appearance_footer_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `footer_id` int(11) NOT NULL,
  `legal_id` int(11) DEFAULT NULL,
  `type` enum('sitemap','legal') NOT NULL,
  `title` varchar(100) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `footer_id` (`footer_id`),
  CONSTRAINT `appearance_footer_links_ibfk_1` FOREIGN KEY (`footer_id`) REFERENCES `appearance_footer` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `banks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `order` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
CREATE TABLE `banners_sides` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `order` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `bills` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `pdf` varchar(255) NOT NULL,
  `xml` varchar(255) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=220 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `brands` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` int(3) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `deleted` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` int(3) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `deleted` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `cfdis` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `rfc` varchar(15) NOT NULL,
  `cp` int(11) NOT NULL,
  `id_sat_tax_regimes` int(11) NOT NULL,
  `invoice_receive_days` varchar(100) DEFAULT NULL,
  `invoice_receive_limit_time` time DEFAULT NULL,
  `payment_days` varchar(100) DEFAULT NULL,
  `payment_terms_days` int(11) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(30) DEFAULT NULL,
  `announcement_message` text DEFAULT NULL,
  `blocked_reception` tinyint(1) NOT NULL DEFAULT 0,
  `holidays` varchar(255) DEFAULT NULL,
  `policy_file` varchar(100) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `coupons` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `discount` float(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `available` int(11) NOT NULL,
  `minimum` int(11) NOT NULL,
  `total_minimum` float(10,2) NOT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `coupons_codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `used` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `name` varchar(250) NOT NULL,
  `symbol` varchar(250) NOT NULL,
  `type_exchange` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `customers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `type_id` int(11) NOT NULL,
  `sap_code` varchar(7) NOT NULL,
  `name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `require_bill` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3561 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `customers_addresses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `internal_number` varchar(255) NOT NULL,
  `colony` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `details` mediumtext NOT NULL,
  `default` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=625 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `customers_fe_control` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) unsigned NOT NULL,
  `fe_control_id` int(11) unsigned NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `customers_tax_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `cfdi_id` int(11) NOT NULL,
  `sat_tax_regime_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `rfc` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `internal_number` varchar(255) NOT NULL,
  `colony` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `csf` varchar(255) NOT NULL,
  `default` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `customers_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `discounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `structure` varchar(32) NOT NULL,
  `type` enum('simple','compuesto') NOT NULL,
  `final_effective` int(11) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 1,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
CREATE TABLE `document_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `scope` enum('provider','customer','internal','general') DEFAULT 'general',
  `active` tinyint(1) DEFAULT 1,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `email_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(50) NOT NULL,
  `from_email` varchar(150) NOT NULL,
  `from_name` varchar(150) NOT NULL,
  `reply_to_email` varchar(150) DEFAULT NULL,
  `reply_to_name` varchar(150) DEFAULT NULL,
  `to_emails` text NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `email_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `role` varchar(50) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `view` varchar(150) NOT NULL,
  `content` mediumtext DEFAULT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `codigo` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `code_seller` int(11) DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `department_id` int(11) NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `employees_departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `deleted` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `exchange_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) NOT NULL,
  `rate` decimal(16,6) NOT NULL,
  `date` date NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `currency_date` (`currency_id`,`date`),
  CONSTRAINT `fk_exchange_currency` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `fe_control` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `docentry` int(11) NOT NULL,
  `objtype` int(11) NOT NULL,
  `fechahorasat` int(11) NOT NULL,
  `serie` varchar(300) DEFAULT NULL,
  `folio` int(11) DEFAULT NULL,
  `uuid` varchar(100) DEFAULT NULL,
  `series` int(11) DEFAULT NULL,
  `total` decimal(11,2) NOT NULL,
  `certificado` varchar(300) DEFAULT NULL,
  `respuesta` varchar(300) DEFAULT NULL,
  `docnum` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `ligaxmlct` varchar(300) DEFAULT NULL,
  `ligapdfct` varchar(300) DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `legal_contracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `legal_document_id` int(11) DEFAULT NULL,
  `document_type_id` int(11) DEFAULT NULL,
  `category` enum('employee','provider','customer','external') DEFAULT 'provider',
  `title` varchar(150) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `start_date` int(11) DEFAULT NULL,
  `end_date` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0 COMMENT '0=Borrador,1=Activo,2=Vencido,3=Cancelado',
  `file_path` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `authorized_by` int(11) DEFAULT NULL,
  `is_global` tinyint(1) DEFAULT 0 COMMENT '0=Individual,1=Global para categor├¡a',
  `deleted` tinyint(1) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_legal_document_id` (`legal_document_id`),
  KEY `idx_document_type_id` (`document_type_id`),
  CONSTRAINT `fk_contract_document` FOREIGN KEY (`legal_document_id`) REFERENCES `legal_documents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_contract_document_type` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_contract_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `legal_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` enum('cliente','proveedor','socio','empleado','visitante','general') NOT NULL,
  `type` enum('aviso_privacidad','terminos','politicas','cookies','newsletter','medidas','codigo','otros') NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `upload_path` varchar(255) DEFAULT NULL,
  `shortcode` varchar(50) NOT NULL,
  `version` varchar(10) DEFAULT NULL,
  `allow_edit` tinyint(1) DEFAULT 0,
  `allow_download` tinyint(1) DEFAULT 0,
  `valid_from` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `active` tinyint(1) DEFAULT 0,
  `required` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `legal_documents_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `change_type` enum('edicion','archivo') NOT NULL DEFAULT 'edicion',
  `version` varchar(10) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `shortcode` varchar(100) DEFAULT NULL,
  `upload_path` varchar(255) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `document_id` (`document_id`),
  CONSTRAINT `legal_documents_versions_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `legal_documents` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `migration` (
  `type` varchar(25) NOT NULL,
  `name` varchar(50) NOT NULL,
  `migration` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `notification_events_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_key` varchar(64) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `url_pattern` varchar(255) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `priority` tinyint(4) DEFAULT 1,
  `active` tinyint(4) DEFAULT 1,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_event_key` (`event_key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `notification_events_config_targets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `config_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `group_id` int(10) unsigned DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_config_target` (`config_id`,`user_id`,`group_id`),
  CONSTRAINT `fk_config` FOREIGN KEY (`config_id`) REFERENCES `notification_events_config` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `notification_recipients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `notification_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_group_id` int(10) unsigned DEFAULT NULL,
  `status` tinyint(4) DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_notification_user` (`notification_id`,`user_id`),
  CONSTRAINT `fk_notification` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(32) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `icon` varchar(64) DEFAULT NULL,
  `priority` tinyint(4) DEFAULT 1,
  `params` text DEFAULT NULL,
  `active` tinyint(4) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `expires_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
CREATE TABLE `packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
CREATE TABLE `partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_sap` varchar(7) NOT NULL,
  `name` varchar(300) NOT NULL,
  `email` varchar(250) NOT NULL,
  `rfc` varchar(15) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `payment_terms_id` int(11) DEFAULT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1152 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `partners_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `bank_id` int(11) NOT NULL,
  `account_number` int(11) NOT NULL,
  `clabe` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `pay_days` varchar(255) NOT NULL,
  `name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `default` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `partners_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `street` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `internal_number` varchar(255) DEFAULT NULL,
  `colony` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `details` mediumtext DEFAULT NULL,
  `default` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `partners_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idcontact` varchar(100) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `partner_delivery_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `last_name` varchar(250) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `cel` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `departments` varchar(250) NOT NULL,
  `default` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `partners_delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `iddelivery` varchar(100) NOT NULL,
  `street` varchar(255) NOT NULL,
  `number` varchar(50) NOT NULL,
  `internal_number` varchar(50) NOT NULL,
  `colony` varchar(255) NOT NULL,
  `zipcode` varchar(10) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state_id` int(11) NOT NULL,
  `municipality` varchar(255) NOT NULL,
  `reception_hours` varchar(100) NOT NULL,
  `delivery_notes` text NOT NULL,
  `default` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `partners_purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `days_to_receive_invoice` varchar(100) NOT NULL,
  `purchase_conditions` varchar(250) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `partners_tax_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `cfdi_id` int(11) NOT NULL,
  `sat_tax_regime_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `email` varchar(250) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `rfc` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `internal_number` varchar(255) NOT NULL,
  `colony` varchar(255) NOT NULL,
  `municipality` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `csf` varchar(255) NOT NULL,
  `default` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `partners_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `subject` varchar(250) NOT NULL,
  `message` text NOT NULL,
  `asig_user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `partners_tickets_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `payments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `total` float(10,2) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=343 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `payments_methods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `payments_processors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `payments_terms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(16) NOT NULL,
  `name` varchar(128) NOT NULL,
  `base_date_type` int(11) DEFAULT NULL,
  `start_offset_days` int(11) DEFAULT NULL,
  `days_tolerance` int(11) DEFAULT NULL,
  `extra_months` int(11) DEFAULT NULL,
  `installment_count` int(11) DEFAULT NULL,
  `open_on_receive` tinyint(1) DEFAULT NULL,
  `total_discount` decimal(10,4) DEFAULT NULL,
  `credit_interest` decimal(10,4) DEFAULT NULL,
  `price_list_id` int(11) DEFAULT NULL,
  `credit_limit` decimal(15,2) DEFAULT NULL,
  `committed_limit` decimal(15,2) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `invoice_receive_days` int(11) DEFAULT NULL,
  `invoice_receive_limit_time` time DEFAULT NULL,
  `payment_frequency` varchar(20) DEFAULT NULL,
  `payment_days_of_month` varchar(50) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `payments_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `resource` varchar(50) NOT NULL,
  `can_view` tinyint(1) DEFAULT 0,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `can_create` tinyint(1) DEFAULT 0,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_resource` (`user_id`,`resource`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `permissions_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `resource` varchar(50) NOT NULL,
  `can_view` tinyint(1) DEFAULT 0,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `can_create` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_resource` (`group_id`,`resource`)
) ENGINE=InnoDB AUTO_INCREMENT=561 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_aliexpress_configurations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `app_key` varchar(255) NOT NULL,
  `app_secret` varchar(255) NOT NULL,
  `access_token` text DEFAULT NULL,
  `refresh_token` text DEFAULT NULL,
  `token_expires_at` int(10) unsigned DEFAULT NULL,
  `seller_id` varchar(100) DEFAULT NULL,
  `mode` enum('production','sandbox') NOT NULL DEFAULT 'production',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `last_sync_catalog` int(10) unsigned DEFAULT NULL,
  `last_sync_orders` int(10) unsigned DEFAULT NULL,
  `last_sync_promotions` int(10) unsigned DEFAULT NULL,
  `last_sync_webhooks` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_mode` (`mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_amazon_configurations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `seller_id` varchar(100) NOT NULL,
  `marketplace_id` varchar(50) NOT NULL,
  `region` varchar(50) NOT NULL DEFAULT 'NA',
  `client_id` varchar(255) NOT NULL,
  `client_secret` varchar(255) NOT NULL,
  `access_token` text DEFAULT NULL,
  `refresh_token` text DEFAULT NULL,
  `token_expires_at` int(10) unsigned DEFAULT NULL,
  `mode` enum('production','sandbox') NOT NULL DEFAULT 'production',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `last_sync_catalog` int(10) unsigned DEFAULT NULL,
  `last_sync_orders` int(10) unsigned DEFAULT NULL,
  `last_sync_promotions` int(10) unsigned DEFAULT NULL,
  `last_sync_webhooks` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_mode` (`mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_amazon_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `configuration_id` int(10) unsigned NOT NULL,
  `amazon_sku` varchar(100) DEFAULT NULL,
  `asin` varchar(20) DEFAULT NULL,
  `amazon_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `title_override` varchar(200) DEFAULT NULL,
  `description_template_id` int(10) unsigned DEFAULT NULL,
  `price_override` decimal(10,2) DEFAULT NULL,
  `stock_override` int(11) DEFAULT NULL,
  `fulfillment_channel` varchar(50) DEFAULT NULL,
  `status_override` varchar(50) DEFAULT NULL,
  `last_sync_at` int(10) unsigned DEFAULT NULL,
  `last_error_at` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_config` (`configuration_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_amazon_stores` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `configuration_id` int(10) unsigned NOT NULL,
  `store_name` varchar(200) NOT NULL,
  `store_logo_url` varchar(500) DEFAULT NULL,
  `default_currency` varchar(10) NOT NULL DEFAULT 'MXN',
  `default_listing_type` varchar(50) NOT NULL DEFAULT 'standard',
  `fulfillment_mode` varchar(50) NOT NULL DEFAULT 'mfn',
  `default_warranty` varchar(500) DEFAULT NULL,
  `return_policy` varchar(500) DEFAULT NULL,
  `notifications_url` varchar(500) DEFAULT NULL,
  `auto_sync_prices` tinyint(1) NOT NULL DEFAULT 1,
  `auto_sync_stock` tinyint(1) NOT NULL DEFAULT 1,
  `auto_sync_orders` tinyint(1) NOT NULL DEFAULT 1,
  `auto_invoice_on_paid` tinyint(1) NOT NULL DEFAULT 0,
  `auto_publish_new_products` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_config` (`configuration_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_ml_attributes_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_attribute_id` int(11) NOT NULL,
  `ml_value_id` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `raw_json` mediumtext DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cat_attr` (`category_attribute_id`),
  CONSTRAINT `fk_values_category_attr` FOREIGN KEY (`category_attribute_id`) REFERENCES `plataforma_ml_categories_attributes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=212 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `plataforma_ml_categories_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` varchar(50) NOT NULL,
  `ml_attribute_id` varchar(50) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value_type` varchar(50) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_catalog_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_variation` tinyint(1) NOT NULL DEFAULT 0,
  `raw_tags` text DEFAULT NULL,
  `raw_json` mediumtext DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_category_attr` (`category_id`,`ml_attribute_id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `plataforma_ml_categories_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `internal_category_id` int(10) unsigned NOT NULL,
  `ml_category_id` varchar(50) NOT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_ml_map` (`internal_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_ml_configurations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `client_secret` varchar(255) NOT NULL,
  `access_token` text DEFAULT NULL,
  `refresh_token` text DEFAULT NULL,
  `token_expires_at` int(10) unsigned DEFAULT NULL,
  `user_id_ml` bigint(20) unsigned DEFAULT NULL,
  `redirect_uri` varchar(255) NOT NULL,
  `mode` enum('production','sandbox') NOT NULL DEFAULT 'production',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `expires_in_last` int(11) DEFAULT NULL,
  `account_email` varchar(150) DEFAULT NULL,
  `last_sync_catalog` int(10) unsigned DEFAULT NULL,
  `last_sync_orders` int(10) unsigned DEFAULT NULL,
  `last_sync_promotions` int(10) unsigned DEFAULT NULL,
  `last_sync_webhooks` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_mode` (`mode`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_ml_description_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `configuration_id` int(10) unsigned NOT NULL,
  `name` varchar(120) NOT NULL,
  `description_html` mediumtext NOT NULL,
  `variables_json` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_ml_errors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `configuration_id` int(10) unsigned DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `ml_item_id` varchar(50) DEFAULT NULL,
  `error_code` varchar(50) DEFAULT NULL,
  `error_message` text NOT NULL,
  `origin` varchar(50) NOT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_config` (`configuration_id`),
  KEY `idx_product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_ml_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `configuration_id` int(10) unsigned DEFAULT NULL,
  `resource` varchar(50) NOT NULL,
  `resource_id` varchar(100) NOT NULL,
  `operation` varchar(50) NOT NULL,
  `status` enum('OK','ERROR') NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_resource` (`resource`,`resource_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_ml_product_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ml_product_id` int(11) NOT NULL,
  `product_image_id` int(11) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `source` enum('system','manual') DEFAULT 'system',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ml_product` (`ml_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_ml_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `configuration_id` int(10) unsigned NOT NULL,
  `ml_item_id` varchar(50) DEFAULT NULL,
  `ml_category_id` varchar(50) DEFAULT NULL,
  `ml_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `ml_title_override` varchar(200) DEFAULT NULL,
  `ml_description_template_id` int(10) unsigned DEFAULT NULL,
  `ml_price_override` decimal(10,2) DEFAULT NULL,
  `ml_stock_override` int(11) DEFAULT NULL,
  `ml_listing_type_override` varchar(50) DEFAULT NULL,
  `ml_status_override` varchar(50) DEFAULT NULL,
  `last_sync_at` int(10) unsigned DEFAULT NULL,
  `last_error_at` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_config` (`configuration_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_ml_products_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ml_product_id` int(11) NOT NULL,
  `category_attribute_id` int(11) NOT NULL,
  `ml_value_id` varchar(50) DEFAULT NULL,
  `value_name` varchar(255) DEFAULT NULL,
  `source` varchar(20) NOT NULL DEFAULT 'manual',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_ml_product_attr` (`ml_product_id`,`category_attribute_id`),
  KEY `fk_prodattr_category` (`category_attribute_id`),
  CONSTRAINT `fk_prodattr_category` FOREIGN KEY (`category_attribute_id`) REFERENCES `plataforma_ml_categories_attributes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `plataforma_ml_stores` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `configuration_id` int(10) unsigned NOT NULL,
  `store_name` varchar(200) NOT NULL,
  `store_logo_url` varchar(500) DEFAULT NULL,
  `default_site_id` varchar(10) NOT NULL DEFAULT 'MLM',
  `default_currency` varchar(10) NOT NULL DEFAULT 'MXN',
  `default_listing_type` varchar(50) NOT NULL DEFAULT 'gold_pro',
  `shipping_mode` varchar(50) NOT NULL DEFAULT 'me1',
  `default_warranty` varchar(500) DEFAULT NULL,
  `return_policy` varchar(500) DEFAULT NULL,
  `notifications_url` varchar(500) DEFAULT NULL,
  `auto_sync_prices` tinyint(1) NOT NULL DEFAULT 1,
  `auto_sync_stock` tinyint(1) NOT NULL DEFAULT 1,
  `auto_sync_orders` tinyint(1) NOT NULL DEFAULT 1,
  `auto_invoice_on_paid` tinyint(1) NOT NULL DEFAULT 0,
  `auto_publish_new_products` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_config` (`configuration_id`),
  CONSTRAINT `fk_ml_store_config` FOREIGN KEY (`configuration_id`) REFERENCES `plataforma_ml_configurations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_ml_webhooks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `topic` varchar(50) NOT NULL,
  `resource` varchar(200) NOT NULL,
  `user_id_ml` bigint(20) unsigned DEFAULT NULL,
  `configuration_id` int(10) unsigned DEFAULT NULL,
  `payload` text NOT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT 0,
  `processed_at` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_topic` (`topic`),
  KEY `idx_processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_shopify_configurations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `shop_domain` varchar(255) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `api_secret` varchar(255) NOT NULL,
  `access_token` text DEFAULT NULL,
  `mode` enum('production','sandbox') NOT NULL DEFAULT 'production',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `last_sync_catalog` int(10) unsigned DEFAULT NULL,
  `last_sync_orders` int(10) unsigned DEFAULT NULL,
  `last_sync_promotions` int(10) unsigned DEFAULT NULL,
  `last_sync_webhooks` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_mode` (`mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_shopify_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `configuration_id` int(10) unsigned NOT NULL,
  `shopify_product_id` bigint(20) unsigned DEFAULT NULL,
  `shopify_variant_id` bigint(20) unsigned DEFAULT NULL,
  `shopify_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `title_override` varchar(200) DEFAULT NULL,
  `description_template_id` int(10) unsigned DEFAULT NULL,
  `price_override` decimal(10,2) DEFAULT NULL,
  `stock_override` int(11) DEFAULT NULL,
  `status_override` varchar(50) DEFAULT NULL,
  `last_sync_at` int(10) unsigned DEFAULT NULL,
  `last_error_at` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_config` (`configuration_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_temu_configurations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `client_secret` varchar(255) NOT NULL,
  `access_token` text DEFAULT NULL,
  `refresh_token` text DEFAULT NULL,
  `token_expires_at` int(10) unsigned DEFAULT NULL,
  `seller_id` varchar(100) DEFAULT NULL,
  `mode` enum('production','sandbox') NOT NULL DEFAULT 'production',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `last_sync_catalog` int(10) unsigned DEFAULT NULL,
  `last_sync_orders` int(10) unsigned DEFAULT NULL,
  `last_sync_promotions` int(10) unsigned DEFAULT NULL,
  `last_sync_webhooks` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_mode` (`mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_tiktok_configurations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `app_key` varchar(255) NOT NULL,
  `app_secret` varchar(255) NOT NULL,
  `access_token` text DEFAULT NULL,
  `refresh_token` text DEFAULT NULL,
  `token_expires_at` int(10) unsigned DEFAULT NULL,
  `shop_id` varchar(100) DEFAULT NULL,
  `business_center_id` varchar(100) DEFAULT NULL,
  `mode` enum('production','sandbox') NOT NULL DEFAULT 'production',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `last_sync_catalog` int(10) unsigned DEFAULT NULL,
  `last_sync_orders` int(10) unsigned DEFAULT NULL,
  `last_sync_promotions` int(10) unsigned DEFAULT NULL,
  `last_sync_webhooks` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_mode` (`mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `plataforma_walmart_configurations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `client_secret` varchar(255) NOT NULL,
  `access_token` text DEFAULT NULL,
  `refresh_token` text DEFAULT NULL,
  `token_expires_at` int(10) unsigned DEFAULT NULL,
  `partner_id` varchar(100) DEFAULT NULL,
  `mode` enum('production','sandbox') NOT NULL DEFAULT 'production',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `last_sync_catalog` int(10) unsigned DEFAULT NULL,
  `last_sync_orders` int(10) unsigned DEFAULT NULL,
  `last_sync_promotions` int(10) unsigned DEFAULT NULL,
  `last_sync_webhooks` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_mode` (`mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `intro` mediumtext NOT NULL,
  `content` mediumtext NOT NULL,
  `publication_date` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `posts_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `posts_labels` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `posts_labels_relations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `label_id` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `prices_amounts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `amount_id` int(11) NOT NULL,
  `min_amount` float(10,2) NOT NULL,
  `max_amount` float(10,2) NOT NULL,
  `percentage` float(10,2) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_order` varchar(250) DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `code_order` varchar(255) DEFAULT NULL,
  `sku` varchar(100) NOT NULL,
  `claveprodserv` int(11) NOT NULL,
  `claveunidad` varchar(11) DEFAULT NULL,
  `codebar` int(13) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `original_price` float(10,2) NOT NULL,
  `available` int(11) NOT NULL,
  `factor` int(11) NOT NULL,
  `purchase_unit_id` int(11) DEFAULT NULL,
  `sale_unit_id` int(11) DEFAULT NULL,
  `minimum_sale` int(11) DEFAULT NULL,
  `minimum_order` int(11) DEFAULT NULL,
  `weight` float(10,3) NOT NULL,
  `price_per` varchar(255) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `status_index` int(11) NOT NULL,
  `newproduct` int(11) NOT NULL DEFAULT 0,
  `soon` int(11) NOT NULL,
  `temporarily_sold_out` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1738 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `products_file_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `products_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `file_type_id` int(11) NOT NULL,
  `file_name` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `order` int(11) NOT NULL,
  `downloads` int(11) DEFAULT 0,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `products_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `order` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `products_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `code_order` varchar(50) NOT NULL,
  `date_order` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `currency` varchar(5) NOT NULL,
  `status` int(11) NOT NULL,
  `notes` text NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `products_prices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` float(10,2) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2108 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `products_prices_amounts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `amount_id` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `products_prices_wholesales` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `min_quantity` int(11) NOT NULL,
  `max_quantity` int(11) NOT NULL,
  `price` float(10,2) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `products_tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `providers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `code_sap` varchar(20) DEFAULT NULL,
  `rfc` varchar(14) NOT NULL,
  `user_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `payment_terms_id` int(11) NOT NULL,
  `require_purchase` int(11) NOT NULL,
  `csf` varchar(250) DEFAULT NULL,
  `origin` tinyint(1) DEFAULT 0 COMMENT '0=Nacional, 1=Extranjero',
  `provider_type` tinyint(1) DEFAULT 0 COMMENT '0=Servicio, 1=Mercanc├¡a',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_providers_rfc` (`rfc`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `bank_id` int(11) NOT NULL,
  `account_number` int(11) NOT NULL,
  `clabe` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `pay_days` varchar(255) DEFAULT NULL,
  `name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `bank_cover` varchar(255) DEFAULT NULL,
  `default` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `street` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `internal_number` varchar(255) DEFAULT NULL,
  `municipality` varchar(100) NOT NULL,
  `colony` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `details` mediumtext DEFAULT NULL,
  `default` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_bills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `pdf` varchar(250) NOT NULL,
  `xml` varchar(250) NOT NULL,
  `uuid` varchar(250) NOT NULL,
  `require_rep` int(11) NOT NULL,
  `payment_method` varchar(10) DEFAULT NULL,
  `invoice_date` int(11) DEFAULT NULL,
  `validated_by` int(11) DEFAULT NULL,
  `validated_at` int(11) DEFAULT NULL,
  `is_duplicate` tinyint(1) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL,
  `message` mediumtext DEFAULT NULL,
  `total` decimal(11,6) NOT NULL,
  `purchase` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `payment_date` int(11) DEFAULT NULL,
  `invoice_data` text NOT NULL,
  `estatus_sat` varchar(250) NOT NULL,
  `mensaje_sat` varchar(250) NOT NULL,
  `deleted` int(11) NOT NULL,
  `recive_document_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pb_uuid` (`uuid`),
  UNIQUE KEY `idx_uuid_unique` (`uuid`),
  KEY `idx_pb_status_created` (`status`,`created_at`),
  KEY `fk_pb_provider` (`provider_id`),
  KEY `fk_pb_order` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_bills_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bill_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `order_detail_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `accounts_chart_id` int(11) DEFAULT NULL,
  `code_product` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `iva` decimal(12,2) DEFAULT NULL,
  `retencion` int(11) DEFAULT NULL,
  `total` decimal(12,2) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_bills_rep` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bill_id` int(11) DEFAULT NULL,
  `rep_uuid` varchar(50) DEFAULT NULL,
  `rep_xml` varchar(255) DEFAULT NULL,
  `rep_pdf` varchar(255) DEFAULT NULL,
  `uploaded_at` int(11) DEFAULT NULL,
  `provider_bill_id` int(11) NOT NULL,
  `uuid` varchar(50) NOT NULL,
  `payment_date` int(11) NOT NULL,
  `amount_paid` decimal(12,2) NOT NULL,
  `xml_file` varchar(255) NOT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `uploaded_by` int(10) unsigned NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_bill_unique` (`bill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `providers_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idcontact` varchar(100) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `provider_delivery_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `last_name` varchar(250) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `cel` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `departments` varchar(250) NOT NULL,
  `default` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_credit_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `purchase_order_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `uuid` varchar(100) NOT NULL,
  `serie` varchar(50) DEFAULT NULL,
  `folio` varchar(50) DEFAULT NULL,
  `xml_file` varchar(255) NOT NULL,
  `pdf_file` varchar(255) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` tinyint(4) DEFAULT 0,
  `requires_rep` tinyint(4) DEFAULT 0,
  `observations` text DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_creditnote_bills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creditnote_id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_creditnote` (`creditnote_id`),
  KEY `fk_bill` (`bill_id`),
  CONSTRAINT `fk_bill` FOREIGN KEY (`bill_id`) REFERENCES `providers_bills` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_creditnote` FOREIGN KEY (`creditnote_id`) REFERENCES `providers_credit_notes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `providers_delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `iddelivery` varchar(100) NOT NULL,
  `street` varchar(255) NOT NULL,
  `number` varchar(50) NOT NULL,
  `internal_number` varchar(50) NOT NULL,
  `colony` varchar(255) NOT NULL,
  `zipcode` varchar(10) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state_id` int(11) NOT NULL,
  `municipality` varchar(255) NOT NULL,
  `reception_hours` varchar(100) NOT NULL,
  `delivery_notes` text NOT NULL,
  `default` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `employees_department_id` int(11) NOT NULL,
  `main` tinyint(1) DEFAULT 0,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT 0,
  `updated_at` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`),
  KEY `employees_department_id` (`employees_department_id`),
  CONSTRAINT `providers_departments_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`),
  CONSTRAINT `providers_departments_ibfk_2` FOREIGN KEY (`employees_department_id`) REFERENCES `employees_departments` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `provider` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `transaction` varchar(255) NOT NULL,
  `response` mediumtext NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=242 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `providers_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `origin` int(11) NOT NULL,
  `code_order` varchar(50) NOT NULL,
  `date_order` int(11) NOT NULL,
  `payment_date` int(11) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `iva` decimal(12,2) NOT NULL,
  `retencion` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `invoiced_total` decimal(15,2) DEFAULT NULL,
  `balance_total` decimal(15,2) DEFAULT NULL,
  `retention` decimal(12,2) DEFAULT NULL,
  `currency_id` varchar(50) NOT NULL,
  `tax_id` int(11) NOT NULL,
  `document_type_id` int(11) DEFAULT NULL,
  `has_invoice` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `authorized_at` int(11) DEFAULT NULL,
  `authorized_by` int(11) DEFAULT NULL,
  `notes` text NOT NULL,
  `uuid` varchar(100) DEFAULT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_po_uuid` (`uuid`),
  KEY `idx_po_status` (`status`),
  KEY `idx_po_code_order` (`code_order`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_orders_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `code_product` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `iva` decimal(12,2) NOT NULL,
  `retencion` int(11) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `delivered` decimal(12,2) NOT NULL,
  `invoiced` decimal(12,2) NOT NULL,
  `tax_id` int(11) NOT NULL,
  `retention_id` int(11) NOT NULL DEFAULT 0,
  `currency_id` int(11) NOT NULL,
  `accounts_chart_id` int(11) DEFAULT NULL,
  `cost_center_id` int(11) DEFAULT NULL,
  `deleted` int(11) NOT NULL,
  `received_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_orders_status_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status_old` varchar(255) NOT NULL,
  `status_new` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `days_to_receive_invoice` varchar(100) NOT NULL,
  `purchase_conditions` varchar(250) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `receipt_number` varchar(50) DEFAULT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `receipt_date` int(11) DEFAULT NULL,
  `payment_date` int(11) DEFAULT NULL,
  `payment_date_actual` int(11) DEFAULT NULL,
  `generated_by` int(11) DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_receipts_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receipt_id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_tax_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `cfdi_id` int(11) NOT NULL,
  `sat_tax_regime_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `email` varchar(250) NOT NULL,
  `business_name` varchar(250) NOT NULL,
  `rfc` varchar(250) NOT NULL,
  `street` varchar(250) NOT NULL,
  `number` varchar(50) NOT NULL,
  `internal_number` varchar(50) NOT NULL,
  `colony` varchar(250) NOT NULL,
  `municipality` varchar(250) NOT NULL,
  `zipcode` varchar(50) NOT NULL,
  `city` varchar(255) NOT NULL,
  `csf` varchar(255) NOT NULL,
  `opc` varchar(255) NOT NULL,
  `default` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `subject` varchar(250) NOT NULL,
  `message` text NOT NULL,
  `asig_user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `providers_tickets_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `quotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `seller_asig_id` int(11) NOT NULL,
  `partner_contact_id` int(11) NOT NULL,
  `admin_updated` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `valid_date` int(11) NOT NULL,
  `reference` mediumtext NOT NULL,
  `total` float(10,2) NOT NULL,
  `discount` float(10,2) NOT NULL,
  `comments` mediumtext NOT NULL,
  `status` int(11) NOT NULL,
  `docnum` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `quotes_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `internal_number` varchar(255) NOT NULL,
  `colony` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `municipality` varchar(255) NOT NULL,
  `details` mediumtext NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `quotes_partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `quote` mediumtext NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `quotes_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `total` float(10,2) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `quotes_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` float(10,2) NOT NULL,
  `discount` float(10,2) DEFAULT NULL,
  `retention` float(10,2) DEFAULT NULL,
  `total` float(10,2) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `quotes_providers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `quote` mediumtext NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `quotes_tax_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `cfdi_id` int(11) NOT NULL,
  `sat_tax_regime_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `rfc` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `internal_number` varchar(255) NOT NULL,
  `colony` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `csf` varchar(255) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `reports_parameters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `query_id` int(11) NOT NULL,
  `param_name` varchar(100) NOT NULL,
  `param_label` varchar(100) DEFAULT NULL,
  `param_type` enum('date','text','number','dropdown') DEFAULT 'text',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `query_id` (`query_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `reports_queries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `query_name` varchar(150) NOT NULL,
  `query_sql` text NOT NULL,
  `description` text DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `version` int(11) DEFAULT 1,
  `deleted` tinyint(1) DEFAULT 0,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `reservations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date_start` int(11) NOT NULL,
  `date_end` int(11) NOT NULL,
  `description` mediumtext NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `retentions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(8) NOT NULL,
  `description` varchar(128) NOT NULL,
  `type` varchar(32) NOT NULL,
  `category` varchar(32) DEFAULT NULL,
  `valid_from` date DEFAULT NULL,
  `base_type` varchar(16) DEFAULT NULL,
  `rate` decimal(10,5) DEFAULT NULL,
  `account` varchar(32) DEFAULT NULL,
  `factor_type` varchar(16) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `sales` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `total` float(10,2) NOT NULL,
  `discount` float(10,2) NOT NULL,
  `transaction` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `ordersap` int(11) NOT NULL,
  `factsap` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `guide` varchar(255) NOT NULL,
  `voucher` varchar(255) NOT NULL,
  `sale_date` int(11) NOT NULL,
  `admin_updated` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3870 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `sales_addresses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `state_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `internal_number` varchar(255) NOT NULL,
  `colony` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `details` mediumtext NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=431 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `sales_products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` float(10,2) NOT NULL,
  `total` float(10,2) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9273 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `sales_tax_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `cfdi_id` int(11) NOT NULL,
  `sat_tax_regime_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `rfc` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `internal_number` varchar(255) NOT NULL,
  `colony` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `csf` varchar(255) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `sat_tax_regimes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `sat_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT NULL COMMENT 'Clave SAT (puede ser NULL para unidades internas)',
  `name` varchar(150) NOT NULL COMMENT 'Nombre de la unidad',
  `abbreviation` varchar(20) DEFAULT NULL COMMENT 'Abreviaci├│n corta',
  `description` varchar(255) DEFAULT NULL COMMENT 'Descripci├│n extendida o uso',
  `is_internal` tinyint(1) DEFAULT 0 COMMENT '1=Unidad creada internamente, 0=Unidad SAT oficial',
  `conversion_factor` decimal(10,4) DEFAULT 1.0000 COMMENT 'Factor de conversi├│n interno',
  `active` tinyint(1) DEFAULT 1,
  `deleted` tinyint(1) DEFAULT 0,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `sessions` (
  `session_id` varchar(40) NOT NULL,
  `previous_id` varchar(40) NOT NULL,
  `user_agent` text NOT NULL,
  `ip_hash` char(32) NOT NULL DEFAULT '',
  `created` int(10) unsigned NOT NULL DEFAULT 0,
  `updated` int(10) unsigned NOT NULL DEFAULT 0,
  `payload` longtext NOT NULL,
  PRIMARY KEY (`session_id`),
  UNIQUE KEY `PREVIOUS` (`previous_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `slides` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `order` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `states` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `subcategories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` int(3) NOT NULL,
  `category_id` int(11) NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `deleted` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=92 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
CREATE TABLE `surveys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(40) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `survey_code` varchar(100) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `ratingventa` int(11) NOT NULL,
  `ratingsurtido` int(11) NOT NULL,
  `ratingentrega` int(11) NOT NULL,
  `recomienda` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` varchar(20) NOT NULL,
  `module` varchar(100) NOT NULL,
  `action` varchar(50) NOT NULL,
  `entity` varchar(100) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `user_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `deleted` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `commitment_at` int(11) DEFAULT NULL,
  `finish_at` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `comments` text DEFAULT NULL,
  `status_id` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `taxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(16) NOT NULL,
  `name` varchar(128) NOT NULL,
  `type_factor` varchar(16) NOT NULL,
  `rate` decimal(10,5) NOT NULL,
  `clave_sat` varchar(8) DEFAULT NULL,
  `tipo_sat` varchar(32) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `theme_layouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `html` longtext DEFAULT NULL,
  `css` longtext DEFAULT NULL,
  `components` longtext DEFAULT NULL,
  `styles` longtext DEFAULT NULL,
  `preview` mediumtext DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `status_id` int(11) NOT NULL,
  `priority_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `asig_user_id` int(11) DEFAULT NULL,
  `solution` text DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `start_date` int(11) DEFAULT NULL,
  `finish_date` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2034 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
CREATE TABLE `tickets_incidents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
CREATE TABLE `tickets_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `date` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=484 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `tickets_priority` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
CREATE TABLE `tickets_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
CREATE TABLE `tickets_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
CREATE TABLE `transfer_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `info` mediumtext NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `user_consents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `document_id` int(11) DEFAULT NULL,
  `version` varchar(10) NOT NULL,
  `accepted` tinyint(1) NOT NULL DEFAULT 1,
  `accepted_at` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `channel` varchar(50) DEFAULT 'web',
  `extra` text DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `document_id` (`document_id`),
  CONSTRAINT `user_consents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_consents_ibfk_2` FOREIGN KEY (`document_id`) REFERENCES `legal_documents` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `user_cookies_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `necessary` tinyint(1) NOT NULL DEFAULT 0,
  `analytics` tinyint(1) NOT NULL DEFAULT 1,
  `marketing` tinyint(1) NOT NULL DEFAULT 1,
  `personalization` tinyint(1) NOT NULL DEFAULT 1,
  `accepted_at` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_cookies_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `group` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `last_login` int(11) NOT NULL,
  `login_hash` varchar(255) NOT NULL,
  `profile_fields` text NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8785 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=COMPACT;
CREATE TABLE `wishlists` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=300 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
CREATE TABLE `wishlists_products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wishlist_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1088 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;



SET FOREIGN_KEY_CHECKS=1;

-- ============================================================================
-- FIN DE LA MIGRACIÓN - Sistema ERP Completo
-- ============================================================================

