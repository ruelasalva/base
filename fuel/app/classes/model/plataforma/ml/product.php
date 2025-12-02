<?php

/**
 * MODELO DE PRODUCTO VINCULADO A ML
 * Define cómo se publica un producto del ERP en Mercado Libre.
 */
class Model_Plataforma_Ml_Product extends \Orm\Model
{
    protected static $_table_name = 'plataforma_ml_products';

    protected static $_properties = array(
        'id',
        'product_id',
        'configuration_id',
        'ml_item_id',
        'ml_category_id',
        'ml_enabled',
        'ml_title_override',
        'ml_description_template_id',
        'ml_price_override',
        'ml_stock_override',
        'ml_listing_type_override',
        'ml_status_override',
        'last_sync_at',
        'last_error_at',
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
        'product' => array(
            'key_from' => 'product_id',
            'model_to' => 'Model_Product', // tu modelo actual de productos
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );
}
