<?php

class Model_Providers_Order_Status_Logs extends \Orm\Model
{

    protected static $_properties = array(
        'id',               
        'order_id',         
        'user_id',             
        'status_old',         
        'status_new',          
        'comment',        
        'created_at',        
        'updated_at'         
    );

protected static $_table_name = 'providers_orders_status_logs';

    protected static $_belongs_to = array(
        'order' => array(
            'key_from' => 'order_id',
            'model_to' => 'Model_Providers_Order',
            'key_to'   => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
        'product' => array(
            'key_from' => 'product_id',
            'model_to' => 'Model_Product',
            'key_to'   => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );
}
