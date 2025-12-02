<?php

class Model_Partners_Ticket extends \Orm\Model
{
	protected static $_properties = array(
    'id',
    'partner_id',
    'subject',
    'message',
    'asig_user_id',
    'status',
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

	protected static $_table_name = 'partners_tickets';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'asiguser' => array(
            'key_from'       => 'asig_user_id',
            'model_to'       => 'Model_Employee',
            'key_to'         => 'user_id',
            'cascade_save'   => true,
            'cascade_delete' => false,
        ),
		'partner' => array(
			'key_from' 		 => 'partner_id',
			'model_to' 		 => 'Model_Partner',
			'key_to' 		 => 'id',
			'cascade_save'   => true,
            'cascade_delete' => false,
		)
	);

}
