<?php

/**
 * Model_Payroll_Concept
 * Sistema de Nómina - Conceptos de Nómina
 * 
 * @package    App
 * @subpackage Model
 * @category   Payroll
 */
class Model_Payroll_Concept extends \Orm\Model
{
	protected static $_properties = [
		'id',
		'tenant_id' => ['data_type' => 'int', 'default' => 1],
		'code' => ['data_type' => 'varchar', 'label' => 'Código', 'validation' => ['required', 'max_length' => [50]]],
		'name' => ['data_type' => 'varchar', 'label' => 'Nombre', 'validation' => ['required', 'max_length' => [100]]],
		'description' => ['data_type' => 'text', 'label' => 'Descripción'],
		'type' => ['data_type' => 'enum', 'label' => 'Tipo', 'validation' => ['required']],
		'calculation_type' => ['data_type' => 'enum', 'default' => 'fixed'],
		'calculation_base' => ['data_type' => 'enum'],
		'percentage' => ['data_type' => 'decimal'],
		'fixed_amount' => ['data_type' => 'decimal'],
		'formula' => ['data_type' => 'text'],
		'is_taxable' => ['data_type' => 'int', 'default' => 1],
		'is_social_security' => ['data_type' => 'int', 'default' => 0],
		'affects_net' => ['data_type' => 'int', 'default' => 1],
		'sat_code' => ['data_type' => 'varchar'],
		'display_order' => ['data_type' => 'int', 'default' => 0],
		'is_mandatory' => ['data_type' => 'int', 'default' => 0],
		'is_active' => ['data_type' => 'int', 'default' => 1],
		'created_at',
		'updated_at',
		'deleted_at',
	];

	protected static $_observers = [
		'Orm\Observer_CreatedAt' => ['events' => ['before_insert'], 'mysql_timestamp' => true],
		'Orm\Observer_UpdatedAt' => ['events' => ['before_update'], 'mysql_timestamp' => true],
	];

	protected static $_table_name = 'payroll_concepts';
	
	protected static $_soft_delete = ['deleted_field' => 'deleted_at', 'mysql_timestamp' => true];

	public function get_type_label()
	{
		return $this->type === 'perception' ? 'Percepción' : 'Deducción';
	}

	public function get_type_badge()
	{
		return $this->type === 'perception' 
			? '<span class="badge bg-success">Percepción</span>'
			: '<span class="badge bg-danger">Deducción</span>';
	}

	public function get_calculation_type_label()
	{
		$types = ['fixed' => 'Fijo', 'percentage' => 'Porcentaje', 'formula' => 'Fórmula'];
		return isset($types[$this->calculation_type]) ? $types[$this->calculation_type] : $this->calculation_type;
	}
}
