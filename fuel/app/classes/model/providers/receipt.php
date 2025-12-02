<?php

class Model_Providers_Receipt extends \Orm\Model
{
	protected static $_properties = array(
    'id',
        'provider_id',
		'receipt_number',
        'total',
        'status',
        'notes',
		'receipt_date',
        'payment_date',
		'payment_date_actual',
		'deleted',
        'created_at',
        'updated_at'
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

	protected static $_table_name = 'providers_receipts';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
		 'details' => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Providers_Receipts_Details',
            'key_to'         => 'receipt_id',
            'cascade_save'   => true, // O false, según tu lógica de negocio
            'cascade_delete' => false, // O true, según tu lógica de negocio
        ),
	);

	protected static $_many_many = array(
		
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'provider' => array(
            'key_from'       => 'provider_id',
            'model_to'       => 'Model_Provider',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
	);

}
