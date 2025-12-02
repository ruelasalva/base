<?php
/**
 * MODEL PROVIDERS CREDIT NOTE
 *
 * MANEJA LAS NOTAS DE CRÉDITO SUBIDAS POR LOS PROVEEDORES
 */
class Model_Providers_Creditnote extends \Orm\Model
{
    protected static $_table_name = 'providers_credit_notes';
    protected static $_properties = array(
        'id',
        'provider_id',
        'purchase_order_id',
        'invoice_id',
        'uuid',
        'serie',
        'folio',
        'xml_file',
        'pdf_file',
        'total',
        'status',
        'requires_rep',
        'observations',
        'created_at',
        'updated_at',
        'deleted',
    );

    # RELACION CON PROVEEDOR
    protected static $_belongs_to = array(
        'provider' => array(
            'model_to' => 'Model_Provider',
            'key_from' => 'provider_id',
            'key_to'   => 'id',
        ),
    );

    # HOOKS DE TIMESTAMP
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
