-- Migración: Sistema de Temas Múltiples para Admin
-- Fecha: 2024-12-02

-- Tabla de temas disponibles
CREATE TABLE IF NOT EXISTS `tenant_theme` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Nombre del tema',
  `slug` varchar(50) NOT NULL COMMENT 'Identificador único',
  `description` varchar(255) DEFAULT NULL COMMENT 'Descripción del tema',
  `template_file` varchar(100) NOT NULL COMMENT 'Archivo de template',
  `css_files` text DEFAULT NULL COMMENT 'JSON con rutas CSS',
  `js_files` text DEFAULT NULL COMMENT 'JSON con rutas JS',
  `preview_image` varchar(255) DEFAULT NULL COMMENT 'Imagen de preview',
  `is_active` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Tema activo',
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Tema por defecto',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Orden de visualización',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `is_active` (`is_active`),
  KEY `is_default` (`is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar los 3 temas iniciales
INSERT INTO `tenant_theme` (`name`, `slug`, `description`, `template_file`, `css_files`, `js_files`, `is_active`, `is_default`, `sort_order`, `created_at`, `updated_at`) VALUES
('Argon Dashboard', 'argon', 'Template moderno y elegante con diseño limpio', 'admin/template', '[\"https://demo.themesberg.com/argon-dashboard/assets/vendor/nucleo/css/nucleo.css\",\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css\",\"admin/animate.css/animate.min.css\",\"https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css\",\"https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css\",\"https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css\",\"admin/argon.css\",\"admin/main.css\",\"admin/add.css\"]', '[\"admin/bootstrap/dist/js/bootstrap.bundle.min.js\",\"admin/argon.js\"]', 1, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('AdminLTE', 'adminlte', 'Template clásico y completo con múltiples componentes', 'admin/template_adminlte', '[\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css\",\"https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css\"]', '[\"https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js\"]', 1, 0, 2, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('CoreUI', 'coreui', 'Template moderno basado en Bootstrap 5', 'admin/template_coreui', '[\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css\",\"https://cdn.jsdelivr.net/npm/@coreui/coreui@5.1.0/dist/css/coreui.min.css\"]', '[\"https://cdn.jsdelivr.net/npm/@coreui/coreui@5.1.0/dist/js/coreui.bundle.min.js\"]', 1, 0, 3, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- Tabla de preferencias de tema por usuario
CREATE TABLE IF NOT EXISTS `user_theme_preference` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `theme_id` int(11) unsigned NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `theme_id` (`theme_id`),
  CONSTRAINT `fk_user_theme_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_theme_theme` FOREIGN KEY (`theme_id`) REFERENCES `tenant_theme` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
