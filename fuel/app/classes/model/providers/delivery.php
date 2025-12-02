<?php
class Model_Providers_Delivery extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'provider_id',
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

	protected static $_table_name = 'providers_delivery';

	protected static $_primary_key = array('id');

	protected static $_belongs_to = array(
		'provider' => array(
			'key_from' => 'provider_id',
			'model_to' => 'Model_Provider',
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
			'model_to' => 'Model_Providers_Contact',
			'key_to' => 'provider_delivery_id',
		)
	);

}
