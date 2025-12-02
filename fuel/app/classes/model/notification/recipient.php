<?php

class Model_Notification_Recipient extends \Orm\Model
{
    // =============================
    // CAMPOS DE LA TABLA notification_recipients
    // =============================
    protected static $_properties = array(
        'id',
        'notification_id',
        'user_id',
        'user_group_id',
        'status',      // 0 = NO LEÍDA, 1 = LEÍDA, ETC.
        'read_at',
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
    // RELACIONES: BELONGS_TO
    // =============================
    protected static $_belongs_to = array(
        'notification' => array(
        'key_from' => 'notification_id',
        'model_to' => 'Model_Notification',
        'key_to'   => 'id',
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
    );

    protected static $_table_name = 'notification_recipients';
}
