<?php

class Model_Cfdi extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"code" => array(
			"label" => "Code",
			"data_type" => "varchar",
		),
		"name" => array(
			"label" => "Name",
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
        # SE INICIALIZA EL ARREGLO CFDIS_OPTS
        $cfdis_opts = array('none' => 'Selecciona una opción');

        $cfdis = Model_Cfdi::query()
        ->order_by('code', 'asc')
        ->get();

        # SI SE OBTUVO LA INFORMACION
        if(!empty($cfdis))
        {
            # SE RECORRE ELEMENTO POR ELEMENTO
            foreach($cfdis as $cfdi)
            {
                # SE ALMACENA LA INFORMACION EN EL ARREGLO
                $cfdis_opts += array($cfdi->id => $cfdi->code.' '.$cfdi->name);
            }
        }

        return $cfdis_opts;
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

	protected static $_table_name = 'cfdis';

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
