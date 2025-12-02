<?php

class Model_Providers_Order_Detail extends \Orm\Model
{

    protected static $_properties = array(
        'id',               
        'order_id',         
        'product_id',        
        'code_product',      
        'description',       
        'quantity',         
        'unit_price',        
        'subtotal',         
        'iva',               
        'retencion',               
        'total',             
        'delivered',         
        'invoiced',          
        'received_at',
        'tax_id',
        'retention_id',
        'currency_id',
        'accounts_chart_id',
        'cost_center_id',
        'deleted',       
        'created_at',        
        'updated_at'         
    );

protected static $_table_name = 'providers_orders_details';

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
        'tax' => array(
			'key_from' 			=> 'tax_id',
			'model_to' 			=> 'Model_Tax',
			'key_to' 			=> 'id',
			'cascade_save' 		=> false,
			'cascade_delete' 	=> false,
		),
        'retention' => array(
            'key_from' => 'retention_id',
            'model_to' => 'Model_Retention',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
        'account' => array(
            'key_from' => 'accounts_chart_id',
            'model_to' => 'Model_Accounts_Chart',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );

    // ============================================================
    // VALIDACIÓN DE CUENTA CONTABLE
    // ============================================================
    public static function validate_accounts($order_id)
    {
        $details = static::query()
            ->where('order_id', $order_id)
            ->where('deleted', 0)
            ->get();

        foreach ($details as $d) {
            if (empty($d->accounts_chart_id)) {
                return false;
            }
        }
        return true;
    }
}
