<?php
class Model_Providers_Contact extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'idcontact',
		'provider_id',
		'provider_delivery_id',
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

	protected static $_table_name = 'providers_contacts';
	protected static $_primary_key = array('id');

	protected static $_belongs_to = array(
		'provider' => array(
			'key_from' => 'provider_id',
			'model_to' => 'Model_Provider',
			'key_to' => 'id',
		),
		'delivery' => array(
			'key_from' => 'provider_delivery_id',
			'model_to' => 'Model_Providers_Delivery',
			'key_to' => 'id',
		),
	);

}
