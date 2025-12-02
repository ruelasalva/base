<?php

class Model_Providers_Department extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'provider_id',
        'employees_department_id',
        'main',
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


    protected static $_table_name = 'providers_departments';

    protected static $_belongs_to = array(
        'provider' => array(
            'key_from' => 'provider_id',
            'model_to' => 'Model_Provider',
            'key_to' => 'id',
        ),
        'department' => array(
            'key_from' => 'employees_department_id',
            'model_to' => 'Model_Employees_Department',
            'key_to' => 'id',
        ),
    );
}
