<?php
/**
 * Configuración para entorno de STAGING/PRUEBAS
 * Logs de debug para detectar problemas antes de producción
 */

return array(
	/**
	 * Activar profiling en staging
	 */
	'profiling' => true,

	/**
	 * Nivel de logs: DEBUG para capturar info importante
	 */
	'log_threshold' => Fuel::L_DEBUG,

	/**
	 * Configuración de errores para staging
	 */
	'errors' => array(
		'continue_on' => array(),
		'throttle' => 10,
		'notices' => true,
		'render_prior' => false,
	),

	/**
	 * Caching activado en staging para simular producción
	 */
	'caching' => true,
	'cache_lifetime' => 1800,
);
