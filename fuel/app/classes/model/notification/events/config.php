<?php

class Model_Notification_Events_Config extends \Orm\Model
{
    // =============================
    // CAMPOS DE LA TABLA notification_events_config
    // =============================
    protected static $_properties = array(
        'id',
        'event_key',
        'title',
        'message',
        'url_pattern',
        'icon',
        'priority',
        'active',
        'created_at',
        'updated_at',
    );

    // =============================
    // OBSERVERS DE FECHA DE CREACIÓN Y ACTUALIZACIÓN
    // =============================
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

    // =============================
    // RELACIÓN: UNA CONFIGURACIÓN TIENE MUCHOS DESTINATARIOS (TARGETS)
    // =============================
    protected static $_has_many = array(
        'targets' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Notification_Events_Config_Target',
            'key_to' => 'config_id',
            'cascade_save' => true,
            'cascade_delete' => true,
        ),
    );

    protected static $_table_name = 'notification_events_config';
}
