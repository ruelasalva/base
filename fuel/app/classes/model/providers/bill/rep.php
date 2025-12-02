<?php
/**
 * MODEL: PROVIDERS_BILL_REP
 * STRUCTURE FOR ELECTRONIC PAYMENT RECEIPTS (REP) ASSOCIATED WITH PROVIDER BILLS
 * FUELPHP ORM 1.8.2
 */

use Orm\Model;

class Model_Providers_Bill_Rep extends Model
{
    // =============================
    // DATABASE TABLE
    // =============================
    protected static $_table_name = 'providers_bills_rep';

    // =============================
    // ALLOWED PROPERTIES FOR INSERT/UPDATE
    // =============================
    protected static $_properties = array(
        'id',
        'provider_bill_id',
        'uuid',
        'payment_date',
        'amount_paid',
        'xml_file',
        'pdf_file',
        'status',
        'uploaded_by',
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

   
    // =============================
    // RELATIONSHIPS
    // =============================
    protected static $_belongs_to = array(
        'provider_bill' => array(
            'model_to' => 'Model_Providers_Bill',
            'key_from' => 'provider_bill_id',
            'key_to'   => 'id',
            'cascade_save' => false,
            'cascade_delete' => false
        ),
        'user' => array(
            'model_to' => 'Model_User',
            'key_from' => 'uploaded_by',
            'key_to'   => 'id',
            'cascade_save' => false,
            'cascade_delete' => false
        ),
    );

    
}
