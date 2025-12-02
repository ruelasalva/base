
<?php
class Model_Task extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'description',
        'status_id' => array(
            'default' =>'3'
        ),
        'employee_id',
        'user_id',
        'comments',
        'created_at',
        'updated_at',
        'commitment_at',
        'finish_at',
        'deleted',
    );

    protected static $_table_name = 'tasks';

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
         'statusticket' => array(
            'key_from'       => 'status_id',
            'model_to'       => 'Model_Tickets_Status',
            'key_to'         => 'id',
            'cascade_save'   => true,
            'cascade_delete' => false,
        )
    );
}
