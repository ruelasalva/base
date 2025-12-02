<?php

class Model_Plataforma_Ml_Product_Image extends Orm\Model
{
    protected static $_table_name = 'plataforma_ml_product_images';

    protected static $_properties = array(
        'id',
        'ml_product_id',
        'product_image_id',
        'url',
        'is_primary',
        'sort_order',
        'deleted',
        'source',
        'created_at',
        'updated_at'
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
        )
    );
}
