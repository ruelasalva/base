<?php

class Model_Plataforma_Ml_Description_Template extends Orm\Model
{
    protected static $_table_name = 'plataforma_ml_description_templates';

    protected static $_properties = [
        'id',
        'configuration_id',
        'name',
        'description_html',
        'variables_json',
        'is_active',
        'deleted',
        'created_at',
        'updated_at',
    ];

    protected static $_observers = [
        'Orm\\Observer_CreatedAt' => [
            'events' => ['before_insert'],
            'mysql_timestamp' => false,
        ],
        'Orm\\Observer_UpdatedAt' => [
            'events' => ['before_update'],
            'mysql_timestamp' => false,
        ],
    ];

    protected static $_belongs_to = [
        'config' => [
            'key_from' => 'configuration_id',
            'model_to' => 'Model_Plataforma_Ml_Configuration',
            'key_to'   => 'id',
        ],
    ];
}
