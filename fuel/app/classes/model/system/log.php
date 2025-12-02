<?php

/**
 * MODEL SYSTEM_LOG
 *
 * REPRESENTA LOS REGISTROS DE LOG GENERALES DEL SISTEMA
 */
class Model_System_Log extends \Orm\Model
{
    // === TABLA ASOCIADA ===
    protected static $_table_name = 'system_logs';

    // === PRIMARY KEY ===
    protected static $_primary_key = ['id'];

    // === CAMPOS PERMITIDOS ===
    protected static $_properties = [
        'id',
        'level',
        'module',
        'action',
        'entity',
        'entity_id',
        'message',
        'context',
        'user_id',
        'group_id',
        'ip',
        'url',
        'created_at',
    ];

    // === OBSERVERS PARA TIMESTAMPS ===
    protected static $_observers = [
        'Orm\Observer_CreatedAt' => [
            'events' => ['before_insert'],
            'mysql_timestamp' => true,
        ],
    ];

    /**
     * RELACIÓN CON USUARIOS
     * (SI QUIERES CONSULTAR QUIÉN HIZO LA ACCIÓN)
     */
    protected static $_belongs_to = [
        'user' => [
            'key_from' => 'user_id',
            'model_to' => 'Model_User',
            'key_to'   => 'id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ],
    ];

    /**
     * DECODIFICAR CONTEXTO JSON A ARRAY
     */
    public function get_context_array()
    {
        return !empty($this->context) ? json_decode($this->context, true) : [];
    }
}
