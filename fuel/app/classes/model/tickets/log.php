<?php

class Model_Tickets_Log extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"ticket_id" => array(
			"label" => "Ticket id",
			"data_type" => "int",
		),
		"message" => array(
			"label" => "Message",
			"data_type" => "varchar",
		),
		"color" => array(
			"label" => "Color",
			"data_type" => "varchar",
		),
		"date" => array(
			"label" => "Date",
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

	protected static $_table_name = 'tickets_logs';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'ticket' => array(
            'key_from'       => 'type_id',
            'model_to'       => 'Model_Ticket',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        )
	);
}
