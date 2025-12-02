<?php
/**
 * Configuración del Sistema de Actualización
 *
 * Este archivo configura cómo el sistema ERP puede actualizarse
 * desde el repositorio principal.
 *
 * @package    app
 */

return array(
	/**
	 * -------------------------------------------------------------------------
	 *  Repositorio Principal (Upstream)
	 * -------------------------------------------------------------------------
	 *
	 *  Configuración del repositorio desde donde se obtendrán las actualizaciones.
	 *  Este es el repositorio "base" que contiene el código central del ERP.
	 *
	 */
	'repository' => array(
		// URL del repositorio Git (HTTPS o SSH)
		'url' => 'https://github.com/ruelasalva/base.git',

		// Rama principal para actualizaciones
		'branch' => 'main',

		// Nombre del remote en git (por defecto 'upstream')
		'remote_name' => 'upstream',
	),

	/**
	 * -------------------------------------------------------------------------
	 *  Habilitar/Deshabilitar Actualizaciones
	 * -------------------------------------------------------------------------
	 *
	 *  Controla si el sistema puede buscar y aplicar actualizaciones.
	 *
	 */
	'enabled' => true,

	/**
	 * -------------------------------------------------------------------------
	 *  Modo de Actualización
	 * -------------------------------------------------------------------------
	 *
	 *  Define cómo se aplicarán las actualizaciones:
	 *  - 'manual': Solo notifica, las actualizaciones deben aplicarse manualmente
	 *  - 'automatic': Descarga automáticamente pero requiere confirmación
	 *  - 'full_auto': Descarga y aplica automáticamente (no recomendado en producción)
	 *
	 */
	'mode' => 'manual',

	/**
	 * -------------------------------------------------------------------------
	 *  Frecuencia de Verificación
	 * -------------------------------------------------------------------------
	 *
	 *  Cada cuánto tiempo verificar si hay actualizaciones (en segundos).
	 *  0 = Solo verificar manualmente
	 *
	 */
	'check_interval' => 86400, // 24 horas

	/**
	 * -------------------------------------------------------------------------
	 *  Notificaciones
	 * -------------------------------------------------------------------------
	 */
	'notifications' => array(
		// Habilitar notificaciones por email
		'email_enabled' => false,

		// Dirección de email para notificaciones
		'email_to' => '',

		// Mostrar notificación en el panel de admin
		'admin_panel' => true,
	),

	/**
	 * -------------------------------------------------------------------------
	 *  Archivos y Directorios Excluidos
	 * -------------------------------------------------------------------------
	 *
	 *  Lista de archivos y directorios que NO deben actualizarse.
	 *  Esto protege configuraciones y personalizaciones locales.
	 *
	 */
	'exclude' => array(
		// Configuraciones específicas del tenant
		'fuel/app/config/development/',
		'fuel/app/config/production/',
		'fuel/app/config/staging/',
		'fuel/app/config/.installed',

		// Módulos personalizados del tenant
		'fuel/packages_tenant/',

		// Archivos de entorno
		'.env',
		'.env.local',

		// Logs y cache
		'fuel/app/logs/',
		'fuel/app/cache/',
		'fuel/app/tmp/',
	),

	/**
	 * -------------------------------------------------------------------------
	 *  Archivos que Siempre se Actualizan
	 * -------------------------------------------------------------------------
	 *
	 *  Lista de archivos que siempre deben actualizarse (core del sistema).
	 *
	 */
	'include_always' => array(
		'fuel/app/classes/controller/base.php',
		'fuel/app/classes/controller/install.php',
		'fuel/app/migrations/',
		'composer.json',
	),

	/**
	 * -------------------------------------------------------------------------
	 *  Backup Antes de Actualizar
	 * -------------------------------------------------------------------------
	 */
	'backup' => array(
		// Crear backup antes de actualizar
		'enabled' => true,

		// Ruta donde guardar los backups
		'path' => APPPATH . 'tmp' . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR,

		// Número máximo de backups a mantener
		'max_backups' => 5,
	),

	/**
	 * -------------------------------------------------------------------------
	 *  Post-Actualización
	 * -------------------------------------------------------------------------
	 *
	 *  Acciones a ejecutar después de una actualización exitosa.
	 *
	 */
	'post_update' => array(
		// Ejecutar composer update
		'composer_update' => true,

		// Ejecutar migraciones pendientes
		'run_migrations' => true,

		// Limpiar cache
		'clear_cache' => true,
	),

	/**
	 * -------------------------------------------------------------------------
	 *  Versión Actual del Sistema
	 * -------------------------------------------------------------------------
	 */
	'version' => array(
		'current' => '1.0.0',
		'min_php' => '7.2.0',
	),
);
