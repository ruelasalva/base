<?php
class Model_Providers_Purchase extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'provider_id',
        'name',
        'email',
        'phone',
        'days_to_receive_invoice',
        'purchase_conditions',
        'created_at',
        'updated_at'
    );

    protected static $_observers = array(
        'Orm\\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'events' => array('before_update'),
            'mysql_timestamp' => false,
        ),
    );

    protected static $_table_name = 'providers_purchases';
    
    protected static $_primary_key = array('id');

    protected static $_belongs_to = array(
        'provider' => array(
            'key_from' => 'provider_id',
            'model_to' => 'Model_Provider',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
    );
}
