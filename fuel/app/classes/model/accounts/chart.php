<?php
/**
 * MODEL_Accounts_Chart
 * TABLA: accounts_chart
 * CATÁLOGO MAESTRO DEL PLAN DE CUENTAS
 */
class Model_Accounts_Chart extends \Orm\Model
{
    protected static $_table_name = 'accounts_chart';
    protected static $_primary_key = array('id');

    protected static $_properties = array(
        'id' => array(
            'label' => 'ID',
            'data_type' => 'int',
        ),
        'code' => array(
            'label' => 'Código',
            'data_type' => 'varchar',
        ),
        'name' => array(
            'label' => 'Nombre',
            'data_type' => 'varchar',
        ),
        'type' => array(
            'label' => 'Tipo',
            'data_type' => 'varchar',
        ),
        'parent_id' => array(
            'label' => 'Cuenta Padre',
            'data_type' => 'int',
            'null' => true,
        ),
        'level' => array(
            'label' => 'Nivel Jerárquico',
            'data_type' => 'int',
            'default' => 1,
        ),
        'currency_id' => array(
            'label' => 'Moneda',
            'data_type' => 'int',
            'null' => true,
        ),
        'is_confidential' => array(
            'label' => '¿Confidencial?',
            'data_type' => 'int',
            'default' => 0,
        ),
        'is_cash_account' => array(
            'label' => '¿Cuenta de Efectivo?',
            'data_type' => 'int',
            'default' => 0,
        ),
        'is_active' => array(
            'label' => '¿Activa?',
            'data_type' => 'int',
            'default' => 1,
        ),
        'annex24_code' => array(
            'label' => 'Código Anexo 24',
            'data_type' => 'varchar',
            'null' => true,
        ),
        'account_class' => array(
            'label' => 'Clase de Cuenta',
            'data_type' => 'varchar',
            'null' => true,
        ),
        'deleted' => array(
            'label' => 'Borrado Lógico',
            'data_type' => 'int',
            'default' => 0,
        ),
        'created_at' => array(
            'label' => 'Creado en',
            'data_type' => 'int',
        ),
        'updated_at' => array(
            'label' => 'Actualizado en',
            'data_type' => 'int',
        ),
    );

    protected static $_belongs_to = array(
        'parent' => array(
            'model_to' => 'Model_Accounts_Chart',
            'key_from' => 'parent_id',
            'key_to'   => 'id',
        ),
        'currency' => array(
            'model_to' => 'Model_Currency',
            'key_from' => 'currency_id',
            'key_to'   => 'id',
        ),
    );

    protected static $_has_many = array(
        'children' => array(
            'model_to' => 'Model_Accounts_Chart',
            'key_from' => 'id',
            'key_to'   => 'parent_id',
        ),
        'accounts' => array( // Relación con cuentas operativas
            'model_to' => 'Model_Account',
            'key_from' => 'id',
            'key_to'   => 'account_chart_id',
        ),
    );

    protected static $_observers = array(
        'Orm\\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'property' => 'created_at',
            'mysql_timestamp' => false,
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'events' => array('before_update'),
            'property' => 'updated_at',
            'mysql_timestamp' => false,
        ),
    );

    // ===============================
    // FUNCIONES DE NEGOCIO
    // ===============================

    /**
     * Devuelve solo cuentas activas y no borradas
     */
    public static function q_active()
    {
        return static::query()->where('deleted', 0)->where('is_active', 1);
    }

    /**
     * Calcula el nivel jerárquico con base en la cuenta padre
     */
    public function set_level_from_parent()
    {
        $this->level = 1;
        if (!empty($this->parent_id)) {
            $parent = self::find($this->parent_id);
            if ($parent) {
                $this->level = $parent->level + 1;
            }
        }
    }

    /**
     * Valida la estructura básica de una cuenta
     */
    public static function validator($data = array())
    {
        $v = \Validation::forge();
        $v->add('code', 'Código')->add_rule('required')->add_rule('max_length', 50);
        $v->add('name', 'Nombre')->add_rule('required')->add_rule('max_length', 150);
        $v->add('type', 'Tipo')->add_rule('required')->add_rule('max_length', 50);
        $v->add('currency_id', 'Moneda')->add_rule('valid_string', array('numeric'));
        $v->add('parent_id', 'Cuenta Padre')->add_rule('valid_string', array('numeric'));
        return $v;
    }
}
