<?php
class Model_Emails_Template extends \Orm\Model
{
    protected static $_table_name = 'email_templates';

    protected static $_properties = array(
        'id',
        'code',        // identificador único: venta_confirmacion, contacto_nuevo, ticket_nuevo
        'role',        // rol asociado (ventas, contacto, etc.)
        'subject',
        'view',        // ruta a la vista FuelPHP
        'content',
        'deleted',
        'created_at',
        'updated_at',
    );

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
}
