<?php

class Model_Notification extends \Orm\Model
{
    // =============================
    // CAMPOS DE LA TABLA notifications
    // =============================
    protected static $_properties = array(
        'id',
        'type',
        'title',
        'message',
        'url',
        'icon',
        'priority',
        'params',
        'active',
        'created_by',
        'created_at',
        'updated_at',
        'expires_at',
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
    // RELACIÓN: UNA NOTIFICACIÓN TIENE MUCHOS DESTINATARIOS
    // =============================
    protected static $_has_many = array(
        'recipients' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Notification_Recipient',
            'key_to' => 'notification_id',
            'cascade_save' => true,
            'cascade_delete' => true,
        ),
    );

    protected static $_table_name = 'notifications';
}
