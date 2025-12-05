<?php

/**
 * Modelo: Provider_Inventory_Receipt
 * Gestiona recepciones de mercancía de proveedores
 */
class Model_Provider_Inventory_Receipt extends \Orm\Model
{
    protected static $_table_name = 'provider_inventory_receipts';
    protected static $_primary_key = array('id');

    protected static $_properties = array(
        'id',
        'tenant_id',
        'provider_id',
        'purchase_order_id',
        'receipt_number',
        'receipt_date',
        'warehouse_id',
        'received_by',
        'invoice_number',
        'invoice_date',
        'status',
        'notes',
        'total_amount',
        'verified_by',
        'verified_at',
        'posted_by',
        'posted_at',
        'created_at',
        'updated_at',
        'deleted_at'
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

    protected static $_soft_delete = array(
        'enabled' => true,
        'mysql_timestamp' => false,
        'deleted_field' => 'deleted_at',
    );

    protected static $_has_many = array(
        'details' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Provider_Inventory_Receipt_Detail',
            'key_to' => 'receipt_id',
            'cascade_save' => true,
            'cascade_delete' => true,
        ),
    );

    protected static $_belongs_to = array(
        'provider' => array(
            'key_from' => 'provider_id',
            'model_to' => 'Model_Provider',
            'key_to' => 'id',
        ),
        'purchase_order' => array(
            'key_from' => 'purchase_order_id',
            'model_to' => 'Model_Provider_Order',
            'key_to' => 'id',
        ),
    );

    /**
     * Generar número de recepción
     */
    public static function generate_number()
    {
        $tenant_id = Helper_Tenant::get_tenant_id();
        
        $last = DB::select(DB::expr('MAX(CAST(SUBSTRING(receipt_number, 5) AS UNSIGNED)) as last_num'))
            ->from('provider_inventory_receipts')
            ->where('tenant_id', $tenant_id)
            ->where('receipt_number', 'LIKE', 'REC-%')
            ->execute()
            ->get('last_num');
        
        $next_num = $last ? $last + 1 : 1;
        
        return 'REC-' . str_pad($next_num, 6, '0', STR_PAD_LEFT);
    }
}

/**
 * Modelo: Provider_Inventory_Receipt_Detail
 */
class Model_Provider_Inventory_Receipt_Detail extends \Orm\Model
{
    protected static $_table_name = 'provider_inventory_receipt_details';
    protected static $_primary_key = array('id');

    protected static $_properties = array(
        'id',
        'receipt_id',
        'product_id',
        'quantity_ordered',
        'quantity_received',
        'unit_cost',
        'subtotal',
        'tax_amount',
        'total',
        'lot_number',
        'expiration_date',
        'notes',
        'created_at',
        'updated_at'
    );

    protected static $_belongs_to = array(
        'receipt' => array(
            'key_from' => 'receipt_id',
            'model_to' => 'Model_Provider_Inventory_Receipt',
            'key_to' => 'id',
        ),
        'product' => array(
            'key_from' => 'product_id',
            'model_to' => 'Model_Product',
            'key_to' => 'id',
        ),
    );
}

/**
 * Modelo: Provider_Payment
 */
class Model_Provider_Payment extends \Orm\Model
{
    protected static $_table_name = 'provider_payments';
    protected static $_primary_key = array('id');

    protected static $_properties = array(
        'id',
        'tenant_id',
        'provider_id',
        'payment_number',
        'payment_date',
        'payment_method',
        'reference_number',
        'amount',
        'currency',
        'exchange_rate',
        'bank_account_id',
        'notes',
        'status',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at'
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

    protected static $_soft_delete = array(
        'enabled' => true,
        'mysql_timestamp' => false,
        'deleted_field' => 'deleted_at',
    );

    protected static $_has_many = array(
        'allocations' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Provider_Payment_Allocation',
            'key_to' => 'payment_id',
            'cascade_save' => true,
            'cascade_delete' => true,
        ),
    );

    protected static $_belongs_to = array(
        'provider' => array(
            'key_from' => 'provider_id',
            'model_to' => 'Model_Provider',
            'key_to' => 'id',
        ),
    );

    /**
     * Generar número de pago
     */
    public static function generate_number()
    {
        $tenant_id = Helper_Tenant::get_tenant_id();
        
        $last = DB::select(DB::expr('MAX(CAST(SUBSTRING(payment_number, 5) AS UNSIGNED)) as last_num'))
            ->from('provider_payments')
            ->where('tenant_id', $tenant_id)
            ->where('payment_number', 'LIKE', 'PAG-%')
            ->execute()
            ->get('last_num');
        
        $next_num = $last ? $last + 1 : 1;
        
        return 'PAG-' . str_pad($next_num, 6, '0', STR_PAD_LEFT);
    }
}

/**
 * Modelo: Provider_Payment_Allocation
 */
class Model_Provider_Payment_Allocation extends \Orm\Model
{
    protected static $_table_name = 'provider_payment_allocations';
    protected static $_primary_key = array('id');

    protected static $_properties = array(
        'id',
        'payment_id',
        'invoice_id',
        'order_id',
        'amount_allocated',
        'created_at'
    );

    protected static $_belongs_to = array(
        'payment' => array(
            'key_from' => 'payment_id',
            'model_to' => 'Model_Provider_Payment',
            'key_to' => 'id',
        ),
    );
}

/**
 * Modelo: Provider_Log
 */
class Model_Provider_Log extends \Orm\Model
{
    protected static $_table_name = 'provider_logs';
    protected static $_primary_key = array('id');

    protected static $_properties = array(
        'id',
        'tenant_id',
        'provider_id',
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at'
    );

    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
    );

    /**
     * Registrar una acción en el log
     */
    public static function log_action($provider_id, $action, $entity_type = null, $entity_id = null, $description = null, $old_values = null, $new_values = null)
    {
        $log = self::forge(array(
            'tenant_id' => Helper_Tenant::get_tenant_id(),
            'provider_id' => $provider_id,
            'user_id' => Auth::check() ? Auth::get('id') : null,
            'action' => $action,
            'entity_type' => $entity_type,
            'entity_id' => $entity_id,
            'description' => $description,
            'old_values' => $old_values ? json_encode($old_values) : null,
            'new_values' => $new_values ? json_encode($new_values) : null,
            'ip_address' => Input::ip(),
            'user_agent' => Input::user_agent(),
        ));
        
        $log->save();
        
        return $log;
    }
}

/**
 * Modelo: Provider_Bank_Account
 */
class Model_Provider_Bank_Account extends \Orm\Model
{
    protected static $_table_name = 'provider_bank_accounts';
    protected static $_primary_key = array('id');

    protected static $_properties = array(
        'id',
        'provider_id',
        'bank_name',
        'account_number',
        'clabe',
        'swift_code',
        'currency',
        'is_default',
        'created_at',
        'updated_at'
    );

    protected static $_belongs_to = array(
        'provider' => array(
            'key_from' => 'provider_id',
            'model_to' => 'Model_Provider',
            'key_to' => 'id',
        ),
    );
}

/**
 * Modelo: Provider_Category
 */
class Model_Provider_Category extends \Orm\Model
{
    protected static $_table_name = 'provider_categories';
    protected static $_primary_key = array('id');

    protected static $_properties = array(
        'id',
        'tenant_id',
        'name',
        'description',
        'is_active',
        'created_at',
        'updated_at'
    );

    protected static $_has_many = array(
        'providers' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Provider',
            'key_to' => 'category_id',
        ),
    );
}
