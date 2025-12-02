<?php

class Model_Permission extends Orm\Model
{
    

    protected static $_properties = array(
        'id',
        'user_id',
        'resource',
        'can_view',
        'can_edit',
        'can_delete',
        'can_create'
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

	protected static $_table_name = 'permissions';

	
}
