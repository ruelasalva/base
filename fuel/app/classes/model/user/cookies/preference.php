<?php
/**
 * MODEL USER COOKIES PREFERENCE
 *
 * ADMINISTRA LAS PREFERENCIAS DE COOKIES DE CADA USUARIO
 *
 * CONVENCIÓN DE FLAGS:
 * - 0 = ACEPTADO / ACTIVO
 * - 1 = RECHAZADO / INACTIVO
 *
 * NOTA: EL CAMPO "necessary" SIEMPRE ES 0 (NO PUEDE DESACTIVARSE).
 */
class Model_User_Cookies_Preference extends \Orm\Model
{
    /**
     * TABLA RELACIONADA
     */
    protected static $_table_name = 'user_cookies_preferences';

    /**
     * CLAVE PRIMARIA
     */
    protected static $_primary_key = array('id');

    /**
     * CAMPOS DEFINIDOS
     */
    protected static $_properties = array(
        'id',
        'user_id',          // ID DEL USUARIO RELACIONADO
        'necessary',        // 0 = ACTIVAS / 1 = DESACTIVADAS (SIEMPRE 0)
        'analytics',        // 0 = ACEPTA / 1 = RECHAZA COOKIES ANALÍTICAS
        'marketing',        // 0 = ACEPTA / 1 = RECHAZA COOKIES DE MARKETING
        'personalization',  // 0 = ACEPTA / 1 = RECHAZA COOKIES DE PERSONALIZACIÓN
        'accepted_at',      // TIMESTAMP UNIX DE ACEPTACIÓN INICIAL
        'updated_at',       // TIMESTAMP UNIX DE ÚLTIMA MODIFICACIÓN
        'ip_address',       // IP DESDE DONDE SE GUARDÓ PREFERENCIA
        'user_agent',       // NAVEGADOR / DISPOSITIVO
    );

    /**
     * RELACIONES
     * UNA PREFERENCIA DE COOKIES PERTENECE A UN USUARIO
     */
    protected static $_belongs_to = array(
        'user' => array(
            'key_from'       => 'user_id',
            'model_to'       => 'Model_User',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
    );

    /**
     * OBSERVERS
     * AUTO-REGISTRO DE FECHAS
     */
    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events'          => array('before_insert'),
            'mysql_timestamp' => false,
            'property'        => 'accepted_at',
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events'          => array('before_update'),
            'mysql_timestamp' => false,
            'property'        => 'updated_at',
        ),
    );
}
