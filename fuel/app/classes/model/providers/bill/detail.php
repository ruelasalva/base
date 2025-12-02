<?php

class Model_Providers_Bill_Detail extends \Orm\Model
{
    
    protected static $_properties = array(
        'id',              
        'bill_id',          
        'order_id',        
        'order_detail_id', 
        'product_id',      
        'code_product',    
        'description',      
        'quantity',         
        'unit_price',      
        'subtotal',         
        'retencion',         
        'iva',              
        'total',           
        'created_at',       
        'updated_at'        
    );

  protected static $_table_name = 'providers_bills_details';

    protected static $_belongs_to = array(
        'bill' => array(
            'key_from' => 'bill_id',
            'model_to' => 'Model_Providers_Bill',
            'key_to'   => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
        'order' => array(
            'key_from' => 'order_id',
            'model_to' => 'Model_Providers_Order',
            'key_to'   => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
        'order_detail' => array(
            'key_from' => 'order_detail_id',
            'model_to' => 'Model_Providers_Order_Detail',
            'key_to'   => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );
}
