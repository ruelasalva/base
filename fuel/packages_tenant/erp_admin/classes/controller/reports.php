<?php
/**
 * ERP Admin Module - Reports Controller
 *
 * @package    ERP_Admin
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Admin;

/**
 * Reports Controller for the Admin Module
 *
 * Provides reporting and analytics functionality.
 */
class Controller_Reports extends \Controller
{
	/**
	 * Index action - displays reports dashboard
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Reportes y Estadísticas',
			'breadcrumb' => array(
				'Dashboard' => 'admin',
				'Reportes' => 'admin/reports',
			),
			'report_types' => array(
				'sales' => 'Reporte de Ventas',
				'users' => 'Reporte de Usuarios',
				'products' => 'Reporte de Productos',
				'inventory' => 'Reporte de Inventario',
			),
		);

		return \Response::forge(\View::forge('erp_admin/reports/index', $data, false));
	}
}
