<?php
/**
 * Admin Module - Reports Controller
 *
 * @package    Admin
 * @version    1.0.0
 */

namespace Admin;

class Controller_Reports extends \Controller
{
	public function action_index()
	{
		$data = array(
			'module_name' => 'Reportes y Estadísticas',
			'report_types' => array(
				'sales' => 'Reporte de Ventas',
				'users' => 'Reporte de Usuarios',
				'products' => 'Reporte de Productos',
				'inventory' => 'Reporte de Inventario',
			),
		);

		return \Response::forge(\View::forge('admin/reports/index', $data, false));
	}
}
