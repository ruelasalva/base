<?php
/**
 * MODELO: REPORTES
 * Tabla: reports_queries
 * Administra las consultas SQL personalizadas agrupadas por departamento.
 */

class Model_Reports_Query extends Orm\Model
{
    protected static $_table_name = 'reports_queries';

    protected static $_properties = array(
        'id',
        'query_name',
        'query_sql',
        'description',
        'department_id',
        'user_id',
        'is_active',
        'version',
        'deleted',
        'created_at',
        'updated_at',
    );

    // RELACIONES
    protected static $_belongs_to = array(
        'department' => array(
            'model_to' => 'Model_Employees_Department',
            'key_from' => 'department_id',
            'key_to'   => 'id',
        ),
        'user' => array(
            'model_to' => 'Model_User',
            'key_from' => 'user_id',
            'key_to'   => 'id',
        ),
    );

    protected static $_has_many = array(
        'parameters' => array(
            'model_to' => 'Model_Reports_Parameter',
            'key_from' => 'id',
            'key_to'   => 'query_id',
        ),
    );

    // TIMESTAMPS
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
