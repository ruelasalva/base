<?php

/**
 * MODELO DE TIENDA ML
 * Define parámetros operativos de la tienda en Mercado Libre.
 */
class Model_Plataforma_Ml_Store extends \Orm\Model
{
    protected static $_table_name = 'plataforma_ml_stores';

    protected static $_properties = array(
        'id',
        'configuration_id',
        'store_name',
        'store_logo_url',
        'default_site_id',
        'default_currency',
        'default_listing_type',
        'shipping_mode',
        'default_warranty',
        'return_policy',
        'notifications_url',
        'auto_sync_prices',
        'auto_sync_stock',
        'auto_sync_orders',
        'auto_invoice_on_paid',
        'auto_publish_new_products',
        'created_at',
        'updated_at',
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

    protected static $_belongs_to = array(
        'configuration' => array(
            'key_from' => 'configuration_id',
            'model_to' => 'Model_Plataforma_Ml_Configuration',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );
}
