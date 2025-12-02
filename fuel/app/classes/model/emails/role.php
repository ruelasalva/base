<?php
class Model_Emails_Role extends \Orm\Model
{
    protected static $_table_name = 'email_roles';

    protected static $_properties = array(
        'id',
        'role',              // ventas, contacto, soporte, tickets
        'from_email',
        'from_name',
        'reply_to_email',
        'reply_to_name',
        'to_emails',         // lista separada por coma
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
