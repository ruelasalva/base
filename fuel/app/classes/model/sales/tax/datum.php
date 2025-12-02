<?php

class Model_Sales_Tax_Datum extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"sale_id" => array(
			"label" => "Sale id",
			"data_type" => "int",
		),
		"payment_method_id" => array(
			"label" => "Payment method id",
			"data_type" => "int",
		),
		"cfdi_id" => array(
			"label" => "Cfdi id",
			"data_type" => "int",
		),
		"sat_tax_regime_id" => array(
			"label" => "Sat tax regime id",
			"data_type" => "int",
		),
		"state_id" => array(
			"label" => "State id",
			"data_type" => "int",
		),
		"business_name" => array(
			"label" => "Business name",
			"data_type" => "varchar",
		),
		"rfc" => array(
			"label" => "Rfc",
			"data_type" => "varchar",
		),
		"street" => array(
			"label" => "Street",
			"data_type" => "varchar",
		),
		"number" => array(
			"label" => "Number",
			"data_type" => "varchar",
		),
		"internal_number" => array(
			"label" => "Internal number",
			"data_type" => "varchar",
		),
		"colony" => array(
			"label" => "Colony",
			"data_type" => "varchar",
		),
		"zipcode" => array(
			"label" => "Zipcode",
			"data_type" => "varchar",
		),
		"city" => array(
			"label" => "City",
			"data_type" => "varchar",
		),
		"csf" => array(
			"label" => "Csf",
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
	public static function set_new_record($request)
    {
        $response = new Model_Sales_Tax_Datum($request);

        return ($response->save()) ? $response : false;
    }

	public static function do_update($request, $sale_id)
	{
		$response = Model_Sales_Tax_Datum::query()->where('sale_id', $sale_id)->get_one();
        $response->set($request);

		return ($response->save()) ? $response : false;
	}

	public static function do_delete($sale_id)
    {
        $response = Model_Sales_Tax_Datum::query()->where('sale_id', $sale_id)->get_one();

        return $response->delete();
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

	protected static $_table_name = 'sales_tax_data';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'sale' => array(
            'key_from'       => 'sale_id',
            'model_to'       => 'Model_Sale',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
		'payment_method' => array(
            'key_from'       => 'payment_method_id',
            'model_to'       => 'Model_Payments_Method',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
		'cfdi' => array(
            'key_from'       => 'cfdi_id',
            'model_to'       => 'Model_Cfdi',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
		'sat_tax_regime' => array(
            'key_from'       => 'sat_tax_regime_id',
            'model_to'       => 'Model_Sat_Tax_Regime',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
		'state' => array(
            'key_from'       => 'state_id',
            'model_to'       => 'Model_State',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        )
	);

}
