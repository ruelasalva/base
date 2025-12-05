<?php

namespace Fuel\Migrations;

class Create_provider_departments
{
	public function up()
	{
		\DBUtil::create_table('provider_departments', array(
			'id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
			'provider_id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
			'department_id' => array('type' => 'int', 'constraint' => 11),
			'is_primary' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 0),
			'notes' => array('type' => 'text', 'null' => true),
			'deleted' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 0),
			'created_at' => array('type' => 'datetime', 'null' => false),
			'updated_at' => array('type' => 'datetime', 'null' => true),
		), array('id'), true, 'InnoDB', 'utf8mb4_unicode_ci');

		// Índices
		\DBUtil::create_index('provider_departments', 'provider_id', 'idx_provider_id');
		\DBUtil::create_index('provider_departments', 'department_id', 'idx_department_id');

		// Foreign keys
		\DB::query("ALTER TABLE `provider_departments` 
			ADD CONSTRAINT `fk_provider_departments_provider` 
			FOREIGN KEY (`provider_id`) REFERENCES `providers`(`id`) 
			ON DELETE CASCADE ON UPDATE CASCADE"
		)->execute();

		\DB::query("ALTER TABLE `provider_departments` 
			ADD CONSTRAINT `fk_provider_departments_department` 
			FOREIGN KEY (`department_id`) REFERENCES `employees_departments`(`id`) 
			ON DELETE CASCADE ON UPDATE CASCADE"
		)->execute();
	}

	public function down()
	{
		\DB::query("ALTER TABLE `provider_departments` DROP FOREIGN KEY `fk_provider_departments_provider`")->execute();
		\DB::query("ALTER TABLE `provider_departments` DROP FOREIGN KEY `fk_provider_departments_department`")->execute();
		\DBUtil::drop_table('provider_departments');
	}
}
