<?php
/**
 * MODELO: PARÁMETROS DE REPORTES
 * Tabla: reports_parameters
 */

class Model_Reports_Parameter extends Orm\Model
{
    protected static $_table_name = 'reports_parameters';

    protected static $_properties = array(
        'id',
        'query_id',
        'param_name',
        'param_label',
        'param_type',
    );

    protected static $_belongs_to = array(
        'query' => array(
            'model_to' => 'Model_Reports_Query',
            'key_from' => 'query_id',
            'key_to'   => 'id',
        ),
    );
}
