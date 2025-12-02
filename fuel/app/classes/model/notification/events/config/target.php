<?php

class Model_Notification_Events_Config_Target extends \Orm\Model
{
    // =============================
    // CAMPOS DE LA TABLA notification_events_config_targets
    // =============================
    protected static $_properties = array(
        'id',
        'config_id',
        'user_id',
        'group_id',
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
    // RELACIÓN: TARGET PERTENECE A UNA CONFIGURACIÓN
    // =============================
    protected static $_belongs_to = array(
        'config' => array(
            'key_from' => 'config_id',
            'model_to' => 'Model_Notification_Events_Config',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
        'user' => array(
            'key_from' => 'user_id',
            'model_to' => 'Model_User',
            'key_to' => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
        // Puedes agregar aquí relación a grupos si tienes Model_Group
    );

    protected static $_table_name = 'notification_events_config_targets';
}
