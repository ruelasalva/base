<?php

class Model_Payments_Term extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'code',
        'name',
        'base_date_type',
        'start_offset_days',
        'days_tolerance',
        'installment_count',
        'open_on_receive',
        'total_discount',
        'credit_interest',
        'price_list_id',
        'credit_limit',
        'committed_limit',
        'created_at',
        'updated_at'
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

	protected static $_table_name = 'payments_terms';

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
