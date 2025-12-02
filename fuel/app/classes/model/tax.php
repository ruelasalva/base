<?php

class Model_Tax extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'code',
        'name',
        'type_factor',
        'rate',
        'clave_sat',
        'tipo_sat',
        'created_at',
        'updated_at'
    );

	protected static $_table_name = 'taxes';

    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
            'property' => 'created_at',
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_update'),
            'mysql_timestamp' => false,
            'property' => 'updated_at',
        ),
    );

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
