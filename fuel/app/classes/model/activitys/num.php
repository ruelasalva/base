<?php

class Model_Activitys_Num extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"act_num" => array(
			"label" => "Act num",
			"data_type" => "varchar",
		),
		"date" => array(
			"label" => "Date",
			"data_type" => "int",
		),
		"completed" => array(
			"label" => "Completed",
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

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'property' => 'created_at',
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'property' => 'updated_at',
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'activitys_nums';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	'activities' => array(
            'key_from'       => 'act_num',
            'model_to'       => 'Model_Activity',
            'key_to'         => 'act_num',
            'cascade_save'   => false,
            'cascade_delete' => false,
        )
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
	);

}
