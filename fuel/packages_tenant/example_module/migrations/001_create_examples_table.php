<?php
/**
 * Example Module - Migration: Create Examples Table
 *
 * Esta migración crea la tabla principal del módulo de ejemplo.
 * Se ejecuta SOLO en la BD del tenant (conexión 'default').
 *
 * @package    Example_Module
 * @version    1.0.0
 */

/**
 * Migration_001_Create_Examples_Table
 *
 * Migración para crear la tabla de ejemplos.
 */
class Migration_001_Create_Examples_Table
{
	/**
	 * Ejecutar migración (crear tablas)
	 *
	 * @return void
	 */
	public function up()
	{
		// IMPORTANTE: Esta migración se ejecuta en la BD del tenant
		// La conexión 'default' ya está configurada para el tenant actual

		\DBUtil::create_table('examples', array(
			'id' => array(
				'type'           => 'int',
				'constraint'     => 11,
				'auto_increment' => true,
				'unsigned'       => true,
			),
			'name' => array(
				'type'       => 'varchar',
				'constraint' => 255,
			),
			'description' => array(
				'type' => 'text',
				'null' => true,
			),
			'status' => array(
				'type'       => 'varchar',
				'constraint' => 50,
				'default'    => 'active',
			),
			'is_active' => array(
				'type'       => 'tinyint',
				'constraint' => 1,
				'default'    => 1,
			),
			'created_at' => array(
				'type' => 'datetime',
			),
			'updated_at' => array(
				'type' => 'datetime',
			),
		), array('id'), true, 'InnoDB', 'utf8mb4_unicode_ci');

		// Crear índices para optimizar consultas
		\DBUtil::create_index('examples', 'is_active', 'idx_examples_active');
		\DBUtil::create_index('examples', 'status', 'idx_examples_status');

		\Log::info('Migration 001: Created examples table');
	}

	/**
	 * Revertir migración (eliminar tablas)
	 *
	 * @return void
	 */
	public function down()
	{
		// Eliminar tabla
		\DBUtil::drop_table('examples');

		\Log::info('Migration 001: Dropped examples table');
	}
}
