<?php
/**
 * MODEL LEGAL DOCUMENT VERSION
 *
 * Guarda las versiones previas de documentos legales.
 */
class Model_Legal_Document_Version extends \Orm\Model
{
    protected static $_table_name = 'legal_documents_versions';
    protected static $_primary_key = ['id'];

    protected static $_properties = [
        'id',
        'document_id',
        'change_type',
        'version',
        'title',
        'category',
        'type',
        'content',
        'shortcode',
        'upload_path',
        'created_at',
        'updated_at',
    ];

    // RELACIÓN CON EL DOCUMENTO PRINCIPAL
    protected static $_belongs_to = [
        'document' => [
            'key_from' => 'document_id',
            'model_to' => 'Model_Legal_Document',
            'key_to'   => 'id',
        ],
    ];
}
