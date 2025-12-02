<?php

class Model_Customers_Fe_Control extends \Orm\Model
{
	 protected static $_properties = array(
        'id',
        'customer_id',
        'fe_control_id',
        'created_at',
        'updated_at',
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

   

    // Define relationships
    protected static $_belongs_to = array(
        'customer' => array(
            'key_from' => 'customer_id',
            'model_to' => 'Model_Customer',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
        'controlcfdi' => array(
            'key_from' => 'fe_control_id',
            'model_to' => 'Model_Controlcfdi',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );

	protected static $_primary_key = array('id');
	 
	protected static $_table_name = 'customers_fe_control';

}
