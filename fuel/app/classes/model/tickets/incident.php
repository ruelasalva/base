<?php
class Model_Tickets_Incident extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'type_id',
        'name',
        'created_at',
        'updated_at',
    );

    protected static $_table_name = 'tickets_incidents';

     protected static $_belongs_to = array(
        'typeticket' => array(
            'key_from'       => 'type_id',
            'model_to'       => 'Model_Tickets_Type',
            'key_to'         => 'id',
            'cascade_save'   => true,
            'cascade_delete' => false,
        ),
    );

}
