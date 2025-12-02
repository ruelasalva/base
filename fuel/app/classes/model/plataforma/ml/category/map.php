<?php

/**
 * MODELO DE MAPEO DE CATEGORÍAS ML
 * Relaciona categorías internas con categorías oficiales de Mercado Libre.
 */
class Model_Plataforma_Ml_Category_Map extends \Orm\Model
{
    protected static $_table_name = 'plataforma_ml_categories_map';

    protected static $_properties = array(
        'id',
        'internal_category_id',
        'ml_category_id',
        'created_at',
        'updated_at',
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
