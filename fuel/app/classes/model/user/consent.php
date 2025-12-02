<?php
/**
 * MODEL USER CONSENT
 *
 * REGISTRA EL HISTÓRICO DE CONSENTIMIENTOS DE USUARIOS
 *
 * CONVENCIÓN DE FLAGS:
 * - 0 = ACEPTADO / VERDADERO
 * - 1 = RECHAZADO / FALSO
 */
class Model_User_Consent extends \Orm\Model
{
    /**
     * TABLA RELACIONADA
     */
    protected static $_table_name = 'user_consents';

    /**
     * CLAVE PRIMARIA
     */
    protected static $_primary_key = array('id');

    /**
     * CAMPOS DEFINIDOS
     */
    protected static $_properties = array(
        'id',
        'user_id',       // ID DEL USUARIO
        'document_id',   // ID DEL DOCUMENTO LEGAL
        'version',   // VERSION DEL DOCUMENTO
        'accepted',      // 0 = ACEPTADO / 1 = RECHAZADO
        'accepted_at',   // TIMESTAMP UNIX DE ACEPTACIÓN
        'ip_address',    // IP DESDE DONDE ACEPTÓ
        'user_agent',    // NAVEGADOR / DISPOSITIVO
        'channel',       // CANAL (web, app, físico, otro)
        'extra',         // JSON: newsletter=1, firma, etc.
    );

    /**
     * RELACIONES
     * UN CONSENTIMIENTO PERTENECE A UN USUARIO Y A UN DOCUMENTO
     */
    protected static $_belongs_to = array(
        'user' => array(
            'key_from'       => 'user_id',
            'model_to'       => 'Model_User',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'document' => array(
            'key_from'       => 'document_id',
            'model_to'       => 'Model_Legal_Document',
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
    );
}
