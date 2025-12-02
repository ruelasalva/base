<?php
/**
 * MODELO PARA MANEJAR LOS TEMPLATES VISUALES DEL TEMA (EDITADOS CON GRAPESJS)
 *
 * @author    TuNombre
 * @date      2025-06-26
 */

class Model_Theme_Layout extends Orm\Model
{
    
    protected static $_properties = array(
        'id',
        'name',
        'html',
        'css',
        'components',
        'styles',
        'preview',
        'created_at',
        'updated_at'
    );

    // PROPIEDADES DEL MODELO
    protected static $_table_name = 'theme_layouts';
    protected static $_primary_key = array('id');
    
    // OBSERVER PARA FECHA AUTOMÁTICA
    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
            'property' => 'created_at',
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_update'),
            'mysql_timestamp' => false,
            'property' => 'updated_at',
        ),
    );
}
