<?php

class Model_Providers_Bill extends \Orm\Model
{
	protected static $_properties = array(
    'id',
    'provider_id',
	'order_id',
    'pdf',
    'xml',
    'uuid',
    'require_rep',  
    'status',
    'message',
    'total',
    'purchase',
    'payment_date',
    'invoice_data',
    'deleted',
    'created_at',
    'updated_at'
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

	protected static $_table_name = 'providers_bills';

	public static function get_for_provider($provider_id)
{
    $arr = [];
    $facturas = static::query()
        ->where('provider_id', $provider_id)
        ->where('deleted', 0)
        ->order_by('created_at', 'desc')
        ->get();

    foreach ($facturas as $f) {
        $arr[$f->id] = $f->uuid . ' ($' . number_format($f->total, 2) . ')';
    }
    return $arr;
}


	protected static $_primary_key = array('id');

	protected static $_has_many = array(
        'receipt_details' => array( 
            'key_from' => 'id',
            'model_to' => 'Model_Providers_Receipts_Details', 
            'key_to'   => 'bill_id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
        'reps' => array(
        'key_from' => 'id',                  
        'model_to' => 'Model_Providers_Bill_Rep',
        'key_to'   => 'provider_bill_id',   
        'cascade_save' => false,
        'cascade_delete' => false,
        ),
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'provider' => array(
			'key_from' => 'provider_id',
            'model_to' => 'Model_Provider',
            'key_to'   => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
		'order' => array(
            'key_from' => 'order_id',
            'model_to' => 'Model_Providers_Order',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),

	);

}
