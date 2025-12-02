<?php

class Model_Partners_Tickets_Message extends \Orm\Model
{
	protected static $_properties = array(
        'id',
        'ticket_id',
        'sender_id',  
        'message',
        'created_at'
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
	
	protected static $_table_name = 'partners_tickets_messages';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
	);

}
