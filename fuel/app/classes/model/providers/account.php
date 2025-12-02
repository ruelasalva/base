<?php
class Model_Providers_Account extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'provider_id',
		'bank_id',
		'account_number',
		'clabe',
		'currency_id',
        'pay_days',
		'name',
		'email',
		'phone',
		'bank_cover',
		'default',
		'deleted',
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

	protected static $_table_name = 'providers_accounts';
    protected static $_primary_key = array('id');

	protected static $_belongs_to = array(
	'provider' => array(
			'key_from'       => 'provider_id',
			'model_to'       => 'Model_Provider',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		),
	'bank' => array(
        'key_from'       => 'bank_id',
        'model_to'       => 'Model_Bank',
        'key_to'         => 'id',
        'cascade_save'   => false,
        'cascade_delete' => false,
    ),
    'currency' => array(
        'key_from'       => 'currency_id',
        'model_to'       => 'Model_Currency',
        'key_to'         => 'id',
        'cascade_save'   => false,
        'cascade_delete' => false,
    ),
);

}
