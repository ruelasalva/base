<?php

class Model_Payments_Method extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"name" => array(
			"label" => "Name",
			"data_type" => "varchar",
		),
		"code" => array(
			"label" => "Code",
			"data_type" => "varchar",
		),
		"deleted" => array(
			"label" => "Deleted",
			"data_type" => "int",
		),
		"created_at" => array(
			"label" => "Created at",
			"data_type" => "int",
		),
		"updated_at" => array(
			"label" => "Updated at",
			"data_type" => "int",
		),
	);

	/* Functions */
    public static function get_for_input()
    {
        $payment_method_opts = array(0 => 'Selecciona una opción');

        $methods = Model_Payments_Method::query()
		->where('deleted', 0)
        ->order_by('code', 'asc')
        ->get();

        if(!empty($methods))
        {
            foreach($methods as $method)
            {
                $payment_method_opts += array($method->id => $method->code.' '.$method->name);
            }
        }

        return $payment_method_opts;
    }

	public static function get_code_by_id($id = 0)
    {
        $method = Model_Payments_Method::query()
        ->where('id', $id)
        ->get_one();

        if(!empty($method))
        {
			$payment_method_code = $method->code;
        }
		else
		{
			$payment_method_code = '99';
		}

        return $payment_method_code;
    }

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'property' => 'created_at',
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'property' => 'updated_at',
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'payments_methods';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
	);

}
