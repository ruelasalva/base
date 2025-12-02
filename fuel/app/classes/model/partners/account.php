<?php
class Model_Partners_Account extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'partner_id',
		'bank',
		'account_number',
		'clabe',
		'currency',
        'pay_days',
		'name',
		'email',
		'phone',
		'default',
		'created_at',
		'updated_at'
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'partners_accounts';
    protected static $_primary_key = array('id');

	protected static $_belongs_to = array(
	'partner' => array(
		'key_from' => 'partner_id',
		'model_to' => 'Model_Partner',
		'key_to' => 'id',
	),
);

}
