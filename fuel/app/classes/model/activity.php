
<?php
class Model_Activity extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'act_num',
        'customer',
        'company',
        'user_id',
        'employee_id',
        'hour',
        'invoice',
        'foreing',
        'time_id',
        'contact_id',
        'category_id',
        'status_id',
        'type_id',
        'total',
        'comments',
        'global_date',
        'created_at',
        'updated_at',
        'deleted',
    );

    protected static $_table_name = 'activitys';


    protected static $_belongs_to = array(
        'employee' => array(
            'key_from'       => 'employee_id',
            'model_to'       => 'Model_Employee',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'user' => array(
            'key_from'       => 'user_id',
            'model_to'       => 'Model_User',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'status' => array(
            'key_from'       => 'status_id',
            'model_to'       => 'Model_Activitys_Status',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'time' => array(
            'key_from'       => 'time_id',
            'model_to'       => 'Model_Activitys_Time',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'type' => array(
            'key_from'       => 'type_id',
            'model_to'       => 'Model_Activitys_Type',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'category' => array(
            'key_from'       => 'category_id',
            'model_to'       => 'Model_Category',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'contact' => array(
            'key_from'       => 'contact_id',
            'model_to'       => 'Model_Activitys_Methods_Contact',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        )
    );
}
