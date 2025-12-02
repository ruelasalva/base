<?php
/**
 * MODEL LEGAL DOCUMENT
 *
 * ADMINISTRA LOS DOCUMENTOS LEGALES DEL SISTEMA
 *
 * CONVENCIÓN DE FLAGS:
 * - 0 = ACTIVO / VERDADERO / PERMITIDO
 * - 1 = INACTIVO / FALSO / NO PERMITIDO
 */
class Model_Legal_Document extends \Orm\Model
{
    /**
     * TABLA RELACIONADA
     */
    protected static $_table_name = 'legal_documents';

    /**
     * CLAVE PRIMARIA
     */
    protected static $_primary_key = array('id');

    /**
     * CAMPOS DEFINIDOS
     */
    protected static $_properties = array(
        'id',
        'category',       // ENUM: cliente, proveedor, socio, empleado, visitante, general
        'type',           // ENUM: aviso_privacidad, terminos, politicas, cookies, newsletter, medidas, codigo, otros
        'title',          // TITULO DEL DOCUMENTO
        'content',        // CONTENIDO EDITABLE (HTML)
        'upload_path',    // ARCHIVO SUBIDO (DOC/PDF BASE)
        'shortcode',      // NOMBRE CORTO PARA USAR EN FORMULARIOS
        'version',        // NUMERO DE VERSION
        'allow_edit',     // 0 = EDITABLE / 1 = NO EDITABLE
        'allow_download', // 0 = DESCARGABLE / 1 = NO DESCARGABLE
        'active',         // 0 = ACTIVO / 1 = INACTIVO
        'required',       // 0 = ACTIVO / 1 = INACTIVO
        'valid_from',     // FECHA DE INICIO DE VIGENCIA
        'valid_until',    // FECHA DE FIN DE VIGENCIA (OPCIONAL)
        'deleted',        // ELIMINADO LOGICO
        'created_at',     // TIMESTAMP UNIX DE CREACIÓN
        'updated_at',     // TIMESTAMP UNIX DE ACTUALIZACIÓN
    );

    /**
     * RELACIONES
     * UN DOCUMENTO TIENE MUCHOS CONSENTIMIENTOS
     */
    protected static $_has_many = array(
        'consents' => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_User_Consent',
            'key_to'         => 'document_id',
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
            'property'        => 'created_at',
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events'          => array('before_update'),
            'mysql_timestamp' => false,
            'property'        => 'updated_at',
        ),
    );
}
