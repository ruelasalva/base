<?php

class Model_Plataforma_Ml_Category_Attribute extends \Orm\Model
{
    protected static $_table_name = 'plataforma_ml_categories_attributes';

    protected static $_properties = array(
        'id',
        'category_id',
        'ml_attribute_id',
        'name',
        'value_type',
        'is_required',
        'is_catalog_required',
        'is_variation',
        'raw_tags',
        'raw_json',
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

    protected static $_has_many = array(
        'values' => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Plataforma_Ml_Attribute_Value',
            'key_to'         => 'category_attribute_id',
            'cascade_save'   => false,
            'cascade_delete' => true,
        ),
        'products_attributes' => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Plataforma_Ml_Product_Attribute',
            'key_to'         => 'category_attribute_id',
            'cascade_save'   => false,
            'cascade_delete' => true,
        ),
    );
}
