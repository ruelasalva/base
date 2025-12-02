<?php
/**
 * Configuración para entorno de PRODUCCIÓN
 * Solo logs de errores y advertencias
 */

return array(
	/**
	 * Desactivar profiling en producción
	 */
	'profiling' => false,

	/**
	 * Nivel de logs: Solo WARNING y ERROR en producción
	 */
	'log_threshold' => Fuel::L_WARNING,

	/**
	 * Configuración de errores para producción
	 */
	'errors' => array(
		// No mostrar notices en producción
		'continue_on' => array(),
		'throttle' => 10,
		'notices' => false,
		'render_prior' => false,
	),

	/**
	 * Caching activado en producción
	 */
	'caching' => true,
	'cache_lifetime' => 3600,
);
