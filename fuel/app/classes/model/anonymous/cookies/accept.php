<?php
/**
 * MODEL ANONYMOUS COOKIES ACCEPT
 *
 * Guarda las preferencias de cookies de visitantes sin login.
 */
class Model_Anonymous_Cookies_Accept extends \Orm\Model
{
    protected static $_table_name = 'anonymous_cookies_accepts';
    protected static $_primary_key = ['id'];

    protected static $_properties = [
        'id',
        'token',
        'necessary',
        'analytics',
        'marketing',
        'personalization',
        'accepted_at',
        'updated_at',
        'ip_address',
        'user_agent',
    ];

    protected static $_observers = [
        'Orm\Observer_CreatedAt' => [
            'events' => ['before_insert'],
            'mysql_timestamp' => false,
            'property' => 'accepted_at',
        ],
        'Orm\Observer_UpdatedAt' => [
            'events' => ['before_update'],
            'mysql_timestamp' => false,
            'property' => 'updated_at',
        ],
    ];
}
