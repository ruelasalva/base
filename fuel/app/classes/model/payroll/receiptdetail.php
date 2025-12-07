<?php

/**
 * Model_Payroll_Receipt_Detail
 * Sistema de Nómina - Detalle de Recibos
 */
class Model_Payroll_Receipt_Detail extends \Orm\Model
{
	protected static $_properties = [
		'id',
		'tenant_id' => ['data_type' => 'int', 'default' => 1],
		'receipt_id' => ['data_type' => 'int', 'validation' => ['required']],
		'concept_id' => ['data_type' => 'int', 'validation' => ['required']],
		'concept_code',
		'concept_name',
		'concept_type' => ['data_type' => 'enum'],
		'calculation_type' => ['data_type' => 'enum', 'default' => 'fixed'],
		'base_amount' => ['data_type' => 'decimal', 'default' => 0.00],
		'percentage' => ['data_type' => 'decimal'],
		'quantity' => ['data_type' => 'decimal', 'default' => 1.00],
		'amount' => ['data_type' => 'decimal', 'default' => 0.00],
		'is_taxable' => ['data_type' => 'int', 'default' => 1],
		'display_order' => ['data_type' => 'int', 'default' => 0],
		'notes',
		'created_at',
		'updated_at',
	];

	protected static $_observers = [
		'Orm\Observer_CreatedAt' => ['events' => ['before_insert'], 'mysql_timestamp' => true],
		'Orm\Observer_UpdatedAt' => ['events' => ['before_update'], 'mysql_timestamp' => true],
	];

	protected static $_table_name = 'payroll_receipt_details';
	
	protected static $_belongs_to = [
		'receipt' => ['key_from' => 'receipt_id', 'model_to' => 'Model_Payroll_Receipt', 'key_to' => 'id'],
		'concept' => ['key_from' => 'concept_id', 'model_to' => 'Model_Payroll_Concept', 'key_to' => 'id'],
	];
}
