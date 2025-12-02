<?php
// app/classes/model/discount.php

class Model_Discount extends Orm\Model
{
	protected static $_table_name = 'discounts';

	// ============================
	// CAMPOS DE LA TABLA DISCOUNTS
	// ============================
	protected static $_properties = array(
		'id',
		'name',
		'structure',
		'type',
		'final_effective',
		'active',
		'deleted',
		'created_at',
		'updated_at',
	);

	// ============================
	// CAMPOS AUTOMÁTICOS DE FECHA
	// ============================
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

	// ============================
	// FILTROS PREDEFINIDOS
	// ============================
	protected static $_conditions = array(
		'where' => array(
			array('deleted', '=', 0),
		)
	);
}
