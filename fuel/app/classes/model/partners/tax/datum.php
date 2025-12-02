<?php

class Model_Partners_Tax_Datum extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"partner_id" => array(
			"label" => "Partner id",
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
		"email" => array(
			"label" => "Email",
			"data_type" => "varchar",
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
		"municipality" => array(
			"label" => "Municipality",
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
		"default" => array(
			"label" => "Default",
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
    public static function get_one($request)
    {
        $response = Model_Partners_Tax_Datum::query();

		if(Arr::get($request, 'id'))
        {
            $response = $response->where('id', $request['id']);
        }

        if(Arr::get($request, 'id_partner'))
        {
            $response = $response->where('partner_id', $request['id_partner']);
        }

        if(Arr::get($request, 'default'))
        {
            $response = $response->where('default', $request['default']);
        }

        $response = $response->get_one();

        return $response;
    }

    public static function set_new_record($request)
    {
        $response = new Model_Partner_Tax_Datum($request);

        return ($response->save()) ? $response : false;
    }

    public static function do_update($request, $id)
    {
        $response = Model_Partner_Tax_Datum::find($id);
        $response->set($request);

        return ($response->save()) ? $response : false;
    }

    public static function do_delete($id)
    {
        $response = Model_Partner_Tax_Datum::find($id);

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

	protected static $_table_name = 'partners_tax_data';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'partner' => array(
			'key_from'       => 'partner_id',
			'model_to'       => 'Model_Partner',
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
		),
		'cfdi' => array(
			'key_from' 		 => 'cfdi_id',
			'model_to' 		 => 'Model_Cfdi',
			'key_to' 		 => 'id',
			'cascade_save' 	 => false,
			'cascade_delete' => false,
		),
		'payment_method' => array(
			'key_from' 		 => 'payment_method_id',
			'model_to' 		 => 'Model_Payments_Method',
			'key_to' 		 => 'id',
			'cascade_save' 	 => false,
			'cascade_delete' => false,
		),
		'sat_tax_regime' => array(
			'key_from' 		 => 'sat_tax_regime_id',
			'model_to' 		 => 'Model_Sat_Tax_Regime',
			'key_to' 		 => 'id',
			'cascade_save' 	 => false,
			'cascade_delete' => false,
		)
	);

}
