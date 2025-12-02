<?php

class Model_Employee extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"codigo" => array(
			"label" => "codigo",
			"data_type" => "varchar",
		),
		"user_id" => array(
			"label" => "User id",
			"data_type" => "int",
		),
		"customer_id" => array(
			"label" => "Customer id",
			"data_type" => "int",
		),
		"name" => array(
			"label" => "Name",
			"data_type" => "varchar",
		),
		"code_seller" => array(
			"label" => "Code seller",
			"data_type" => "int",
		),
		"last_name" => array(
			"label" => "Last name",
			"data_type" => "varchar",
		),
		"department_id" => array(
			"label" => "Departament id",
			"data_type" => "int",
		),
		"phone" => array(
			"label" => "Phone",
			"data_type" => "varchar",
		),
		"email" => array(
			"label" => "Email",
			"data_type" => "varchar",
		),
		"deleted" => array(
			"label" => "deleted",
			"data_type" => "int",
		),
		"created_at" => array(
			"label" => "Created at",
			"data_type" => "int",
		),
		"updated_at" => array(
			"label" => "Updated at",
			"data_type" => "int",
		),
    );

   

	protected static $_table_name = 'employees';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(

	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	
	);

	protected static $_belongs_to = array(
		'user' => array(
			'key_from'       => 'user_id',
			'model_to'       => 'Model_User',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'department' => array(
			'key_from'       => 'department_id',
			'model_to'       => 'Model_Employees_Department',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'customer' => array(
			'key_from'       => 'customer_id',
			'model_to'       => 'Model_Customer',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

}
