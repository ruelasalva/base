<?php

class Model_Appearance_Footer_Link extends Orm\Model
{
    protected static $_table_name = 'appearance_footer_links';
    
    protected static $_properties = [
        'id',
        'footer_id',
        'legal_id',
        'type',
        'title',
        'url',
        'slug',
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
        ],
        'legal' => [   
            'model_to' => 'Model_Legal_Document',
            'key_from' => 'legal_id',
            'key_to'   => 'id'
        ]
    ];
}
