<?php
/**
 * Configuración de Base de Datos del Tenant
 *
 * Generado automáticamente por el instalador
 * Fecha: 2025-12-02 07:56:51
 *
 * NOTA: Este archivo sobrescribe la configuración por defecto.
 */

return array(
	'default' => array(
		'type'        => 'pdo',
		'connection'  => array(
			'dsn'      => 'mysql:host=localhost;dbname=base',
			'username' => 'root',
			'password' => '',
		),
		'identifier'  => '`',
		'table_prefix' => '',
		'charset'     => 'utf8mb4',
		'collation'   => false,
		'enable_cache' => true,
		'profiling'   => false,
		'readonly'    => false,
	),
);
