<?php

class Model_Providers_Receipts_Details extends \Orm\Model
{
	protected static $_properties = array(
		'id',
        'receipt_id',
        'bill_id',
		'order_id',
		'created_at',
		'updated_at'
);

	protected static $_observers = array(
		'Orm\\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'property' => 'created_at',
			'mysql_timestamp' => false,
		),
		'Orm\\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'property' => 'updated_at',
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'providers_receipts_details';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'receipt' => array(
            'key_from' => 'receipt_id',
            'model_to' => 'Model_Providers_Receipt',
            'key_to'   => 'id',
            'cascade_save' => false, 
            'cascade_delete' => false, 
        ),
        'bill' => array(
            'key_from' => 'bill_id',
            'model_to' => 'Model_Providers_Bill',
            'key_to'   => 'id',
            'cascade_save' => false, 
            'cascade_delete' => false, 
        ),
		'order' => array( 
            'key_from' => 'order_id',
            'model_to' => 'Model_Providers_Order',
            'key_to'   => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
	);

}
