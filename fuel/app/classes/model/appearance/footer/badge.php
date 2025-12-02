<?php

class Model_Appearance_Footer_Badge extends Orm\Model
{
    protected static $_table_name = 'appearance_footer_badges';
    
    protected static $_properties = [
        'id',
        'footer_id',
        'title',
        'image',
        'sort_order',
        'status',
        'created_at',
        'updated_at'
    ];

    protected static $_belongs_to = [
        'footer' => [
            'model_to' => 'Model_Appearance_Footer',
            'key_from' => 'footer_id',
            'key_to'   => 'id'
        ]
    ];
}
