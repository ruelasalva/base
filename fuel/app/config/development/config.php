<?php
/**
 * Configuración para entorno de DESARROLLO
 * Los logs completos y debug están activados
 */

return array(
	/**
	 * Activar profiling para desarrollo
	 */
	'profiling' => true,

	/**
	 * Nivel de logs: TODOS (L_ALL) para capturar todo en desarrollo
	 */
	'log_threshold' => Fuel::L_ALL,

	/**
	 * Configuración de errores para desarrollo
	 */
	'errors' => array(
		// Mostrar todos los errores en desarrollo
		'continue_on' => array(),
		'throttle' => 10,
		'notices' => true,
		'render_prior' => false,
	),

	/**
	 * Caching desactivado en desarrollo
	 */
	'caching' => false,
);
