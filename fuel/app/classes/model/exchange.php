<?php

class Model_Exchange extends \Orm\Model
{
    

    protected static $_properties = array(
        'id',
        'currency_id',
        'rate',
        'date',
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

	protected static $_table_name = 'exchange_rates';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'currency' => array(
            'key_from' => 'currency_id',
            'model_to' => 'Model_Currency',
            'key_to'   => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
	);

}
