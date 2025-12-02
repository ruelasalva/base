<?php

use Orm\Model;

class Model_Appearance_Footer extends Model
{
    protected static $_table_name = 'appearance_footer';
    protected static $_primary_key = ['id'];

    protected static $_properties = [
        'id',
        'logo_main',
        'logo_secondary',
        'customer_service',
        'address',
        'phone',
        'email',
        'office_hours_week',
        'office_hours_weekend',
        'facebook',
        'instagram',
        'linkedin',
        'youtube',
        'twitter',
        'tiktok',
        'whatsapp',
        'telegram',
        'pinterest',
        'snapchat',
        'status',
        'created_at',
        'updated_at',
    ];

    // OPCIONAL: validaciones base
    protected static $_observers = [
        'Orm\Observer_CreatedAt' => [
            'events' => ['before_insert'],
            'mysql_timestamp' => false,
        ],
        'Orm\Observer_UpdatedAt' => [
            'events' => ['before_update'],
            'mysql_timestamp' => false,
        ],
    ];
}
