<?php
class Model_Sat_Unit extends \Orm\Model
{
    protected static $_table_name = 'sat_units';

    protected static $_properties = [
        'id',
        'code',
        'name',
        'abbreviation',
        'description',
        'is_internal',
        'conversion_factor',
        'active',
        'deleted',
        'created_at',
        'updated_at',
    ];

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

    /**
     * Retorna unidades activas (SAT o internas)
     * @param bool|null $internal true=solo internas, false=solo SAT, null=todas
     */
    public static function get_active($internal = null)
    {
        $query = self::query()
            ->where('active', 1)
            ->where('deleted', 0);

        if ($internal !== null) {
            $query->where('is_internal', (int)$internal);
        }

        return $query->order_by('name', 'asc')->get();
    }

    /**
     * Busca por clave o nombre
     */
    public static function search($term)
    {
        return self::query()
            ->where_open()
                ->where('code', 'like', "%{$term}%")
                ->or_where('name', 'like', "%{$term}%")
            ->where_close()
            ->where('deleted', 0)
            ->limit(30)
            ->get();
    }
}
