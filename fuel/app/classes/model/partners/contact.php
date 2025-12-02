<?php
class Model_Partners_Contact extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'idcontact',
		'partner_id',
		'partner_delivery_id',
		'name',
		'last_name',
		'phone',
		'cel',
		'email',
		'departments',
		'default',
		'deleted',
		'created_at',
		'updated_at'
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'partners_contacts';
	protected static $_primary_key = array('id');

	protected static $_belongs_to = array(
		'partner' => array(
			'key_from' => 'partner_id',
			'model_to' => 'Model_Partner',
			'key_to' => 'id',
		),
		'delivery' => array(
			'key_from' => 'partner_delivery_id',
			'model_to' => 'Model_Partners_Delivery',
			'key_to' => 'id',
		),
	);

}
