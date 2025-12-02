<?php

class Model_Sat_Tax_Regime extends \Orm\Model
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
        $sat_tax_regimes_opts = array('none' => 'Selecciona una opción');

        $sat_tax_regimes = Model_Sat_Tax_Regime::query()
        ->order_by('code', 'asc')
        ->get();

        # SI SE OBTUVO LA INFORMACION
        if(!empty($sat_tax_regimes))
        {
            # SE RECORRE ELEMENTO POR ELEMENTO
            foreach($sat_tax_regimes as $sat_tax_regime)
            {
                # SE ALMACENA LA INFORMACION EN EL ARREGLO
                $sat_tax_regimes_opts += array($sat_tax_regime->id => $sat_tax_regime->code.' '.$sat_tax_regime->name);
            }
        }

        return $sat_tax_regimes_opts;
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

	protected static $_table_name = 'sat_tax_regimes';

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
