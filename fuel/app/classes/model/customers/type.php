<?php

class Model_Customers_Type extends \Orm\Model
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
        $type_opts = array('none' => 'Selecciona una opción');

		# SE OBTIENE LA INFORMACION DEL MODELO
        $types = Model_Customers_Type::query()
        ->order_by('id', 'asc')
        ->get();

        # SI SE OBTUVO LA INFORMACION
        if(!empty($types))
        {
            # SE RECORRE ELEMENTO POR ELEMENTO
            foreach($types as $type)
            {
                # SE ALMACENA LA INFORMACION
                $type_opts += array($type->id => $type->name);
            }
        }

		# SE DEVUELVE EL ARREGLO
        return $type_opts;
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

	protected static $_table_name = 'customers_types';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
		'customers' => array(
			'key_from'       => 'id',
			'model_to'       => 'Model_Customer',
			'key_to'         => 'type_id',
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
