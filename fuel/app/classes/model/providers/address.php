<?php

class Model_Providers_Address extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"provider_id" => array(
			"label" => "Provider id",
			"data_type" => "int",
		),
		"state_id" => array(
			"label" => "State id",
			"data_type" => "int",
		),
		"phone" => array(
			"label" => "Phone",
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
		"municipality" => array(
			"label" => "Municipality",
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
		"details" => array(
			"label" => "Details",
			"data_type" => "mediumtext",
		),
		"default" => array(
			"label" => "Default",
			"data_type" => "int",
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


    /* Functions */
    public static function get_one($request)
    {
        $response = Model_Providers_Address::query();

        if(Arr::get($request, 'id'))
        {
            $response = $response->where('id', $request['id']);
        }

        if(Arr::get($request, 'id_provider'))
        {
            $response = $response->where('provider_id', $request['id_provider']);
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
        $response = new Model_Providers_Address($request);

        return ($response->save()) ? $response : false;
    }

    public static function do_update($request, $id)
    {
        $response = Model_Providers_Address::find($id);
        $response->set($request);

        return ($response->save()) ? $response : false;
    }

    public static function do_delete($id)
    {
        $response = Model_Providers_Address::find($id);

        return $response->delete();
    }


	protected static $_table_name = 'providers_addresses';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'provider' => array(
			'key_from'       => 'provider_id',
			'model_to'       => 'Model_Provider',
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
