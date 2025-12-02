<?php

class Model_Plataforma_Ml_Product_Attribute extends \Orm\Model
{
    protected static $_table_name = 'plataforma_ml_products_attributes';

    protected static $_properties = array(
        'id',
        'ml_product_id',
        'category_attribute_id',
        'ml_value_id',
        'value_name',
        'source',
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
        'ml_product' => array(
            'key_from'       => 'ml_product_id',
            'model_to'       => 'Model_Plataforma_Ml_Product',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'category_attribute' => array(
            'key_from'       => 'category_attribute_id',
            'model_to'       => 'Model_Plataforma_Ml_Category_Attribute',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
    );
}
