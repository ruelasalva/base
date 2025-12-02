<?php
/**
 * MODEL_Account
 * TABLA: accounts
 * CUENTAS OPERATIVAS DEL ERP
 */
class Model_Account extends \Orm\Model
{
    protected static $_table_name = 'accounts';
    protected static $_primary_key = array('id');

    protected static $_properties = array(
        'id' => array(
            'label' => 'ID',
            'data_type' => 'int',
        ),
        'account_chart_id' => array(
            'label' => 'Cuenta Contable (FK)',
            'data_type' => 'int',
        ),
        'partner_id' => array(
            'label' => 'Socio de Negocio',
            'data_type' => 'int',
            'null' => true,
        ),
        'code' => array(
            'label' => 'Código',
            'data_type' => 'varchar',
        ),
        'name' => array(
            'label' => 'Nombre',
            'data_type' => 'varchar',
        ),
        'currency_id' => array(
            'label' => 'Moneda',
            'data_type' => 'int',
            'null' => true,
        ),
        'balance' => array(
            'label' => 'Saldo',
            'data_type' => 'decimal',
            'default' => 0.00,
        ),
        'limit_amount' => array(
            'label' => 'Límite',
            'data_type' => 'decimal',
            'null' => true,
        ),
        'is_active' => array(
            'label' => 'Activa',
            'data_type' => 'int',
            'default' => 1,
        ),
        'is_cash_account' => array(
            'label' => '¿Cuenta de Efectivo?',
            'data_type' => 'int',
            'default' => 0,
        ),
        'deleted' => array(
            'label' => 'Borrado lógico',
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
        'chart' => array(
            'model_to' => 'Model_Accounts_Chart',
            'key_from' => 'account_chart_id',
            'key_to'   => 'id',
        ),
        'currency' => array(
            'model_to' => 'Model_Currency',
            'key_from' => 'currency_id',
            'key_to'   => 'id',
        ),
        'partner' => array(
            'model_to' => 'Model_Partner',
            'key_from' => 'partner_id',
            'key_to'   => 'id',
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
     * Valida los campos básicos del modelo
     */
    public static function validator($data = array())
    {
        $v = \Validation::forge();
        $v->add('code', 'Código')->add_rule('required')->add_rule('max_length', 50);
        $v->add('name', 'Nombre')->add_rule('required')->add_rule('max_length', 150);
        $v->add('account_chart_id', 'Cuenta Contable')->add_rule('required')->add_rule('valid_string', array('numeric'));
        $v->add('currency_id', 'Moneda')->add_rule('valid_string', array('numeric'));
        $v->add('partner_id', 'Socio')->add_rule('valid_string', array('numeric'));
        return $v;
    }

    /**
     * Calcula el saldo disponible
     */
    public function get_available_balance()
    {
        if ($this->limit_amount === null) {
            return $this->balance;
        }
        return $this->limit_amount - $this->balance;
    }
}
