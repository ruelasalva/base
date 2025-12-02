<?php

/**
 * MODELO DE ERRORES CRÍTICOS ML
 * Almacena respuestas de error de la API para análisis posterior.
 */
class Model_Plataforma_Ml_Error extends \Orm\Model
{
    protected static $_table_name = 'plataforma_ml_errors';

    protected static $_properties = array(
        'id',
        'configuration_id',
        'product_id',
        'ml_item_id',
        'error_code',
        'error_message',
        'origin',
        'created_at',
    );

    protected static $_observers = array(
        'Orm\\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
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
            'model_to' => 'Model_Product',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );
}
