<?php
/**
 * Configuración del Instalador
 *
 * Este archivo contiene la configuración del instalador de base de datos.
 * Permite personalizar el comportamiento del instalador.
 *
 * @package    app
 * @author     ERP Development Team
 */

return array(
	/**
	 * -------------------------------------------------------------------------
	 *  Habilitar/Deshabilitar el instalador
	 * -------------------------------------------------------------------------
	 *
	 *  Establecer en false para deshabilitar el acceso al instalador.
	 *  Recomendado en producción después de la instalación inicial.
	 *
	 */
	'enabled' => true,

	/**
	 * -------------------------------------------------------------------------
	 *  Requerir autenticación para el instalador
	 * -------------------------------------------------------------------------
	 *
	 *  Si está habilitado, solo usuarios autenticados con permisos de admin
	 *  pueden acceder al instalador (útil para actualizaciones).
	 *
	 */
	'require_auth' => false,

	/**
	 * -------------------------------------------------------------------------
	 *  IP permitidas para acceder al instalador
	 * -------------------------------------------------------------------------
	 *
	 *  Lista de IPs que pueden acceder al instalador.
	 *  Dejar vacío para permitir cualquier IP.
	 *
	 */
	'allowed_ips' => array(
		// '127.0.0.1',
		// '::1',
	),

	/**
	 * -------------------------------------------------------------------------
	 *  Ruta de migraciones
	 * -------------------------------------------------------------------------
	 *
	 *  Directorio donde se encuentran los archivos SQL de migración.
	 *
	 */
	'migrations_path' => APPPATH . 'migrations' . DIRECTORY_SEPARATOR,

	/**
	 * -------------------------------------------------------------------------
	 *  Archivo de bloqueo
	 * -------------------------------------------------------------------------
	 *
	 *  Archivo que indica que la instalación está completa.
	 *
	 */
	'lock_file' => APPPATH . 'config' . DIRECTORY_SEPARATOR . '.installed',

	/**
	 * -------------------------------------------------------------------------
	 *  Configuración de contraseñas
	 * -------------------------------------------------------------------------
	 */
	'password' => array(
		'min_length' => 8,
		'require_uppercase' => false,
		'require_lowercase' => false,
		'require_number' => false,
		'require_special' => false,
	),

	/**
	 * -------------------------------------------------------------------------
	 *  Información del sistema
	 * -------------------------------------------------------------------------
	 */
	'system' => array(
		'name' => 'ERP Multi-tenant',
		'version' => '1.0.0',
	),
);
