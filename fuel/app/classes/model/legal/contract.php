<?php
class Model_Legal_Contract extends \Orm\Model
{
    protected static $_table_name = 'legal_contracts';

    protected static $_properties = array(
        'id',
        'user_id',
        'legal_document_id',
        'document_type_id',
        'category',
        'title',
        'code',
        'start_date',
        'end_date',
        'status',
        'file_path',
        'description',
        'authorized_by',
        'is_global',
        'deleted',
        'created_at',
        'updated_at',
    );

    // Relaciones
    protected static $_belongs_to = array(
        'user' => array(
            'key_from' => 'user_id',
            'model_to' => 'Model_User',
            'key_to' => 'id',
        ),
        'document' => array(
            'key_from' => 'legal_document_id',
            'model_to' => 'Model_Legal_Document',
            'key_to' => 'id',
        ),
        'type' => array(
            'key_from' => 'document_type_id',
            'model_to' => 'Model_Document_Type',
            'key_to' => 'id',
        ),
        'authorizer' => array(
            'key_from' => 'authorized_by',
            'model_to' => 'Model_User',
            'key_to' => 'id',
        ),
    );

    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_update'),
            'mysql_timestamp' => false,
        ),
    );

    /**
     * Retorna si el contrato está activo según fechas y estatus.
     */
    public function is_active()
    {
        if ($this->deleted || $this->status != 1) return false;
        $today = time();
        $start = $this->start_date ? strtotime($this->start_date) : 0;
        $end = $this->end_date ? strtotime($this->end_date) : PHP_INT_MAX;
        return ($today >= $start && $today <= $end);
    }

    /**
     * Devuelve una etiqueta visual del estatus.
     */
    public static function status_label($status)
    {
        $map = array(
            0 => ['label' => 'Borrador', 'class' => 'secondary'],
            1 => ['label' => 'Activo', 'class' => 'success'],
            2 => ['label' => 'Vencido', 'class' => 'warning'],
            3 => ['label' => 'Cancelado', 'class' => 'danger'],
        );
        $s = $map[$status] ?? ['label' => 'Desconocido', 'class' => 'light'];
        return "<span class=\"badge badge-{$s['class']}\">{$s['label']}</span>";
    }
}
