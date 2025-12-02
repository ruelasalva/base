-- =====================================================
-- Migración 005: Configuración del Sitio Multi-tenant
-- =====================================================
-- Tabla para almacenar configuración general del sitio por tenant:
-- - Información general (nombre, logos, favicons)
-- - SEO (meta tags, description, keywords)
-- - Scripts de tracking (Google Analytics, Facebook Pixel, GTM)
-- - reCAPTCHA (site key, secret key)
-- - Cookies y privacidad
-- =====================================================

USE base;

-- Tabla principal de configuración del sitio
CREATE TABLE IF NOT EXISTS `tenant_site_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` int(11) unsigned NOT NULL DEFAULT 1 COMMENT 'ID del tenant (multi-tenancy)',
  
  -- Información General
  `site_name` varchar(255) DEFAULT 'Mi Sitio' COMMENT 'Nombre del sitio',
  `site_tagline` varchar(500) DEFAULT NULL COMMENT 'Eslogan o descripción corta',
  `contact_email` varchar(255) DEFAULT NULL COMMENT 'Email de contacto',
  `contact_phone` varchar(50) DEFAULT NULL COMMENT 'Teléfono de contacto',
  `address` text DEFAULT NULL COMMENT 'Dirección física',
  
  -- Logos y Favicons
  `logo_url` varchar(500) DEFAULT NULL COMMENT 'URL del logo principal',
  `logo_alt_url` varchar(500) DEFAULT NULL COMMENT 'URL del logo alternativo (blanco/dark)',
  `favicon_16` varchar(500) DEFAULT NULL COMMENT 'Favicon 16x16',
  `favicon_32` varchar(500) DEFAULT NULL COMMENT 'Favicon 32x32',
  `favicon_57` varchar(500) DEFAULT NULL COMMENT 'Apple touch icon 57x57',
  `favicon_72` varchar(500) DEFAULT NULL COMMENT 'Apple touch icon 72x72',
  `favicon_114` varchar(500) DEFAULT NULL COMMENT 'Apple touch icon 114x114',
  `favicon_144` varchar(500) DEFAULT NULL COMMENT 'Apple touch icon 144x144',
  
  -- SEO
  `meta_description` text DEFAULT NULL COMMENT 'Meta description para SEO',
  `meta_keywords` text DEFAULT NULL COMMENT 'Meta keywords (separadas por comas)',
  `meta_author` varchar(255) DEFAULT NULL COMMENT 'Meta author',
  `og_image` varchar(500) DEFAULT NULL COMMENT 'Imagen para Open Graph (Facebook/Twitter)',
  `theme_color` varchar(7) DEFAULT '#008ad5' COMMENT 'Color del tema (hex)',
  
  -- Google Analytics
  `ga_enabled` tinyint(1) DEFAULT 0 COMMENT '¿Google Analytics activado?',
  `ga_tracking_id` varchar(50) DEFAULT NULL COMMENT 'ID de seguimiento GA (G-XXXXXXXXXX)',
  `ga_script` text DEFAULT NULL COMMENT 'Script completo de GA (opcional)',
  
  -- Google Tag Manager
  `gtm_enabled` tinyint(1) DEFAULT 0 COMMENT '¿Google Tag Manager activado?',
  `gtm_container_id` varchar(50) DEFAULT NULL COMMENT 'ID del contenedor GTM (GTM-XXXXXXX)',
  
  -- Facebook Pixel
  `fb_pixel_enabled` tinyint(1) DEFAULT 0 COMMENT '¿Facebook Pixel activado?',
  `fb_pixel_id` varchar(50) DEFAULT NULL COMMENT 'ID del pixel de Facebook',
  
  -- reCAPTCHA
  `recaptcha_enabled` tinyint(1) DEFAULT 0 COMMENT '¿reCAPTCHA activado?',
  `recaptcha_site_key` varchar(100) DEFAULT NULL COMMENT 'Site key de reCAPTCHA',
  `recaptcha_secret_key` varchar(100) DEFAULT NULL COMMENT 'Secret key de reCAPTCHA',
  `recaptcha_version` enum('v2','v3') DEFAULT 'v2' COMMENT 'Versión de reCAPTCHA',
  
  -- Cookies y Privacidad
  `cookie_consent_enabled` tinyint(1) DEFAULT 1 COMMENT '¿Mostrar aviso de cookies?',
  `cookie_message` text DEFAULT 'Este sitio utiliza cookies para mejorar tu experiencia.' COMMENT 'Mensaje del banner de cookies',
  `privacy_policy_url` varchar(500) DEFAULT NULL COMMENT 'URL de la política de privacidad',
  `terms_conditions_url` varchar(500) DEFAULT NULL COMMENT 'URL de términos y condiciones',
  
  -- Scripts personalizados
  `custom_head_scripts` text DEFAULT NULL COMMENT 'Scripts personalizados en <head>',
  `custom_body_scripts` text DEFAULT NULL COMMENT 'Scripts personalizados antes de </body>',
  
  -- Timestamps
  `created_at` int(11) unsigned DEFAULT 0,
  `updated_at` int(11) unsigned DEFAULT 0,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenant_id` (`tenant_id`),
  KEY `idx_tenant` (`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuración del sitio por tenant';

-- Insertar configuración por defecto para tenant 1
INSERT INTO `tenant_site_config` (
  `tenant_id`,
  `site_name`,
  `site_tagline`,
  `meta_description`,
  `meta_author`,
  `theme_color`,
  `cookie_consent_enabled`,
  `created_at`,
  `updated_at`
) VALUES (
  1,
  'Panel Admin',
  'Sistema Multi-tenant ERP',
  'Sistema de gestión empresarial multi-tenant con módulos de administración, ventas, compras e inventario.',
  'ERP Development Team',
  '#008ad5',
  1,
  UNIX_TIMESTAMP(),
  UNIX_TIMESTAMP()
);

-- =====================================================
-- Fin de la migración
-- =====================================================
