
<?php
class Model_Ticket extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'type_id',
        'incident_id',
        'description',
        'status_id' => array(
            'default' =>'1'
        ),
        'priority_id',
        'employee_id',
        'user_id',
        'asig_user_id',
        'solution',
        'rating',
        'start_date',
        'finish_date',
        'created_at',
        'updated_at',
    );

    protected static $_table_name = 'tickets';

    // Define las relaciones con otros modelos si es necesario

    protected static $_belongs_to = array(
         'employee' => array(
            'key_from'       => 'employee_id',
            'model_to'       => 'Model_Employee',
            'key_to'         => 'id',
            'cascade_save'   => true,
            'cascade_delete' => false,
        ),
        'user' => array(
            'key_from'       => 'user_id',
            'model_to'       => 'Model_User',
            'key_to'         => 'id',
            'cascade_save'   => true,
            'cascade_delete' => false,
        ),
        'asiguser' => array(
            'key_from'       => 'asig_user_id',
            'model_to'       => 'Model_Employee',
            'key_to'         => 'id',
            'cascade_save'   => true,
            'cascade_delete' => false,
        ),
         'incidentticket' => array(
            'key_from'       => 'incident_id',
            'model_to'       => 'Model_Tickets_Incident',
            'key_to'         => 'id',
            'cascade_save'   => true,
            'cascade_delete' => false,
        ),
        'statusticket' => array(
            'key_from'       => 'status_id',
            'model_to'       => 'Model_Tickets_Status',
            'key_to'         => 'id',
            'cascade_save'   => true,
            'cascade_delete' => false,
        ),
        'priorityticket' => array(
            'key_from'       => 'priority_id',
            'model_to'       => 'Model_Tickets_Priority',
            'key_to'         => 'id',
            'cascade_save'   => true,
            'cascade_delete' => false,
        ),
        'typeticket' => array(
            'key_from'       => 'type_id',
            'model_to'       => 'Model_Tickets_Type',
            'key_to'         => 'id',
            'cascade_save'   => true,
            'cascade_delete' => false,
        )
    );
}
