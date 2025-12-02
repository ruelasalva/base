<?php

/**
 * MODELO DE LOG DE NEGOCIO ML
 * Registra operaciones de sincronización a nivel funcional.
 */
class Model_Plataforma_Ml_Log extends \Orm\Model
{
    protected static $_table_name = 'plataforma_ml_logs';

    protected static $_properties = array(
        'id',
        'configuration_id',
        'resource',
        'resource_id',
        'operation',
        'status',
        'message',
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

