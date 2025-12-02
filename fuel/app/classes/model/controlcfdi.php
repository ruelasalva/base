<?php

class Model_Controlcfdi extends \Orm\Model
{
	 protected static $_properties = array(
        'id',
        'docentry',
        'objtype',
        'fechahorasat',
        'serie',
        'folio',
        'uuid',
        'series',
        'total',
        'certificado',
        'respuesta',
        'docnum',
        'status',
        'ligaxmlct',
        'ligapdfct',
        'version',
        'created_at',
        'updated_at'
    );

    protected static $_has_many = array(
        'customers_fe_control' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Customers_Fe_Control',
            'key_to' => 'fe_control_id',
            'cascade_save' => false,
            'cascade_delete' => false,
        )
    );

    protected static $_observers = array(
        'Orm\\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'events' => array('before_save'),
            'mysql_timestamp' => false,
        ),
    );
	
	protected static $_primary_key = array('id');

    protected static $_table_name = 'fe_control';

}
