<?php
/**
 * MODEL RELACION NOTAS DE CRÉDITO ↔ FACTURAS
 */
class Model_Providers_Creditnote_Bill extends \Orm\Model
{
    protected static $_table_name = 'providers_creditnote_bills';
    protected static $_properties = array(
        'id',
        'creditnote_id',
        'bill_id',
        'amount',
        'created_at',
        'updated_at',
    );

    protected static $_belongs_to = array(
        'creditnote' => array(
            'model_to' => 'Model_Providers_Creditnote',
            'key_from' => 'creditnote_id',
            'key_to'   => 'id',
        ),
        'bill' => array(
            'model_to' => 'Model_Providers_Bill',
            'key_from' => 'bill_id',
            'key_to'   => 'id',
        ),
    );

    protected static $_observers = array(
        'Orm\\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'events' => array('before_update'),
            'mysql_timestamp' => false,
        ),
    );
}
