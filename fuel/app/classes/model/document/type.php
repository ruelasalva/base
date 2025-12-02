<?php
/**
 * MODELO: Document Types
 * Catálogo genérico para tipos de documentos (Remisión, Cotización, Requisición, etc.)
 * Reutilizable en áreas: Proveedores, Clientes o Interno.
 */
class Model_Document_Type extends \Orm\Model
{
    protected static $_table_name = 'document_types';
    protected static $_properties = array(
        'id',
        'name',
        'scope',
        'active',
        'deleted',
        'created_at',
        'updated_at'
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
     * Obtiene tipos activos filtrados por área.
     * @param string|null $scope 'provider', 'customer', 'internal', 'general'
     */
    public static function get_active($scope = null)
    {
        $query = self::query()->where('active', 1)->where('deleted', 0);
        if ($scope) {
            $query->where('scope', $scope);
        }
        return $query->order_by('name', 'asc')->get();
    }
}
