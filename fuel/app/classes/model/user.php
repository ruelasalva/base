<?php

class Model_User extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'username',
		'password',
		'group_id',
		'email',
		'first_name',
		'last_name',
		'phone',
		'avatar',
		'last_login',
		'previous_login',
		'login_hash',
		'is_active',
		'is_verified',
		'verification_token',
		'created_at',
		'updated_at',
		'deleted_at'
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

	protected static $_has_many = array(
    'notification_recipients' => array(
        'key_from' => 'id',
        'model_to' => 'Model_Notification_Recipient',
        'key_to'   => 'user_id',
        'cascade_save' => false,
        'cascade_delete' => false,
    ),
);

	protected static $_has_one = array(
		'customer' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Customer',
			'key_to'         => 'user_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'provider' => array(
            'model_to' => 'Model_Provider',
            'key_from' => 'id',          
            'key_to'   => 'user_id',     
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
		'partner' => array(
            'model_to' => 'Model_Partner',
            'key_from' => 'id',          
            'key_to'   => 'user_id',     
            'cascade_save' => true,
            'cascade_delete' => false,
        )
	);
}
