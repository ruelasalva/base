<?php
class Model_Partners_Delivery extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'partner_id',
		'iddelivery',
		'street',
		'number',
		'internal_number',
		'colony',
		'zipcode',
		'city',
		'state_id',
		'municipality',
		'reception_hours',
		'delivery_notes',
		'default',
		'deleted',
		'created_at',
		'updated_at'
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events'          => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events'          => array('before_save'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'partners_delivery';

	protected static $_primary_key = array('id');

	protected static $_belongs_to = array(
		'partner' => array(
			'key_from' => 'partner_id',
			'model_to' => 'Model_Partner',
			'key_to' => 'id',
		),
		'state' => array(
			'key_from' => 'state_id',
			'model_to' => 'Model_State',
			'key_to' => 'id',
		),
	);

	protected static $_has_one = array(
		'contact' => array(
			'key_from' => 'id',
			'model_to' => 'Model_Partners_Contact',
			'key_to' => 'partner_delivery_id',
		)
	);

}
