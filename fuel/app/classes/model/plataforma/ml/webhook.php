<?php

/**
 * MODELO DE WEBHOOKS ML
 * Eventos recibidos desde Mercado Libre para procesar en segundo plano.
 */
class Model_Plataforma_Ml_Webhook extends \Orm\Model
{
    protected static $_table_name = 'plataforma_ml_webhooks';

    protected static $_properties = array(
        'id',
        'topic',
        'resource',
        'user_id_ml',
        'configuration_id',
        'payload',
        'processed',
        'processed_at',
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
    );
}
