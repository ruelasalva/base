<?php

class Model_State extends \Orm\Model
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
        # SE INICIALIZA EL ARREGLO STATES_OPTS
        $states_opts = array('none' => 'Selecciona una opción');

		# SE OBTIENE LA INFORMACION DEL MODELO
        $states = Model_State::query()
        ->order_by('name', 'asc')
        ->get();

        # SI SE OBTUVO LA INFORMACION
        if(!empty($states))
        {
            # SE RECORRE CIUDAD POR CIUDAD
            foreach($states as $state)
            {
                # SE ALMACENA LA INFORMACION EN EL ARREGLO CITIES_OPTS
                $states_opts += array($state->id => $state->name);
            }
        }

		# SE DEVUELVE EL ARREGLO
        return $states_opts;
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

	protected static $_table_name = 'states';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
		'customer_addresses' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Customers_Address',
			'key_to'         => 'state_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
		'sales_addresses' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Sales_Address',
			'key_to'         => 'state_id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
	);

}
