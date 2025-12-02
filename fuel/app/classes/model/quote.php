<?php

class Model_Quote extends \Orm\Model
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
		"employee_id" => array(
			"label" => "Employee id",
			"data_type" => "int",
		),
        "seller_asig_id" => array(
			"label" => "Seller id",
			"data_type" => "int",
		),
        "partner_contact_id" => array(
			"label" => "Partner id",
			"data_type" => "int",
		),
		"admin_updated" => array(
			"label" => "Admin Updated",
			"data_type" => "int",
		),
		"payment_id" => array(
			"label" => "Payment id",
			"data_type" => "int",
		),
		"address_id" => array(
			"label" => "Address id",
			"data_type" => "int",
		),
		"total" => array(
			"label" => "Total",
			"data_type" => "float",
		),
		"discount" => array(
			"label" => "Discount",
			"data_type" => "float",
		),
		"valid_date" => array(
			"label" => "Valid date",
			"data_type" => "int",
		),
        "reference" => array(
			"label" => "Reference",
			"data_type" => "mediumtext",
		),
        "comments" => array(
			"label" => "Comments",
			"data_type" => "mediumtext",
		),
        "status" => array(
			"label" => "Status",
			"data_type" => "int",
		),
		"docnum" => array(
			"label" => "Docnum",
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
    public static function get_sold($request = null)
    {
        $response = Model_Quote::query();

        if(isset($request))
        {
            if(Arr::get($request, 'id_partner'))
            {
                $response = $response->where('partner_id', $request['id_partner']);
            }

            if(Arr::get($request, 'limit'))
            {
                $response = $response->limit($request['limit']);
            }
        }

        $response = $response->where('status', '>', 0)
        ->order_by('id', 'desc')
        ->get();

        return $response;
    }

    public static function get_one_sold($request)
    {
        $response = Model_Quote::query();

        if(Arr::get($request, 'id'))
        {
            $response = $response->where('id', $request['id']);
        }

        if(Arr::get($request, 'id_partner'))
        {
            $response = $response->where('partner_id', $request['id_partner']);
        }

        if(Arr::get($request, 'limit'))
        {
            $response = $response->limit($request['limit']);
        }

        $response = $response->where('status', '>', 0)
        ->get_one();

        return $response;
    }

    public static function do_update($request, $id_quote)
    {
        $response = Model_Quote::find($id_quote);
        $response->set($request);

        return ($response->save()) ? $response : false;
    }

    public static function get_last_order_not_sent($id_partner)
    {
        $response = Model_Quote::query()
        ->where('partner_id', $id_partner)
        ->where('status', 0)
        ->order_by('id', 'desc')
        ->get_one();

        return $response;
    }

    public static function set_new_order_not_sent($id_partner)
    {
        $response = new Model_Quote(array(
            'partner_id'            => $id_partner,
            'payment_id'            => 0,
            'address_id'            => 0,
            'employee_id'           => '',
            'partner_contact_id'    => '',
            'valid_date'            => '',
            'reference'             => '',
            'comments'              => '',
            'total'                 => 0,
            'discount'              => 0,
            'transaction'           => '',
            'status'                => 0,
            'ordersap'              => 0,
            'package_id'            => 0,
            'guide'                 => 0,
            'voucher'               => '',
            'factsap'               => 0,
            'order_id'              => 0,
            'package_id'            => 0,
			'sale_date'             => 0,
			'admin_updated'         => 0
        ));

        return ($response->save()) ? $response : false;
    }

    public static function get_last_order_purchased($id_partner)
    {
        $response = Model_Quote::query()
        ->where('partner_id', $id_partner)
        ->where('status', 1)
        ->order_by('id', 'desc')
        ->get_one();

        return $response;
    }

	public static function get_last_order_transfer($id_partner)
    {
        $response = Model_Quote::query()
        ->where('partner_id', $id_partner)
        ->where('status', 2)
        ->order_by('id', 'desc')
        ->get_one();

        return $response;
    }

	public static function check_transaction($quote_id, $transaction)
    {
        $response = Model_Quote::query()
		->where('id', $quote_id)
		->where('transaction', $transaction)
		->where('status', 0)
		->get_one();

        return $response;
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

	protected static $_table_name = 'quotes';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
        'products' => array(
            'key_from'       => 'id',
            'model_to'       => 'Model_Quotes_Product',
            'key_to'         => 'quote_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        )
	);

	protected static $_many_many = array(
	);

	protected static $_has_one = array(
        'quotes_address' => array(
            'key_from' => 'address_id',
            'model_to' => 'Model_Quotes_Address',
            'key_to' => 'id',
            'cascade_delete' => false,
        )
    );

	protected static $_belongs_to = array(
        'partner' => array(
            'key_from'       => 'partner_id',
            'model_to'       => 'Model_Partner',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'employee' => array(
            'key_from'       => 'employee_id',
            'model_to'       => 'Model_Employee',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'seller_asig' => array( 
            'key_from'       => 'seller_asig_id',
            'model_to'       => 'Model_Employee',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'contact' => array(
            'key_from'       => 'partner_contact_id',
            'model_to'       => 'Model_Partners_Contact',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'address' => array(
            'key_from'       => 'address_id',
            'model_to'       => 'Model_Quotes_Address',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
		),
		'payment' => array(
            'key_from'       => 'payment_id',
            'model_to'       => 'Model_Quotes_Payment',
            'key_to'         => 'id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        )
	);

}
