<?php

namespace Fuel\Migrations;

class Create_user_identities
{
	public function up()
	{
		\DBUtil::create_table('user_identities', array(
			'id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
			'user_id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
			'identity_type' => array('type' => 'enum', 'constraint' => array('employee', 'provider', 'customer', 'partner'), 'default' => 'employee'),
			'identity_id' => array('type' => 'int', 'constraint' => 11, 'unsigned' => true),
			'is_primary' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 0),
			'can_login' => array('type' => 'tinyint', 'constraint' => 1, 'default' => 1),
			'access_level' => array('type' => 'enum', 'constraint' => array('full', 'readonly', 'limited'), 'default' => 'full'),
			'created_at' => array('type' => 'datetime', 'null' => false),
			'updated_at' => array('type' => 'datetime', 'null' => true),
		), array('id'), true, 'InnoDB', 'utf8mb4_unicode_ci');

		// Índices
		\DBUtil::create_index('user_identities', 'user_id', 'idx_user_id');
		\DBUtil::create_index('user_identities', array('identity_type', 'identity_id'), 'unique_identity', 'UNIQUE');

		// Foreign key
		\DB::query("ALTER TABLE `user_identities` 
			ADD CONSTRAINT `fk_user_identities_user` 
			FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) 
			ON DELETE CASCADE ON UPDATE CASCADE"
		)->execute();
	}

	public function down()
	{
		\DB::query("ALTER TABLE `user_identities` DROP FOREIGN KEY `fk_user_identities_user`")->execute();
		\DBUtil::drop_table('user_identities');
	}
}
