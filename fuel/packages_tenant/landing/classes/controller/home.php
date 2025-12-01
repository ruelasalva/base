<?php
/**
 * Landing Module - Home Controller
 *
 * @package    Landing
 * @version    1.0.0
 */

namespace Landing;

class Controller_Home extends \Controller
{
	public function action_index()
	{
		$data = array(
			'page_title' => 'Bienvenido',
			'hero_title' => 'Tu Solución ERP Completa',
			'hero_subtitle' => 'Gestiona tu negocio de manera eficiente',
			'features' => array(
				array('title' => 'Gestión de Inventario', 'description' => 'Control total de tu inventario en tiempo real.', 'icon' => 'archive'),
				array('title' => 'Ventas y CRM', 'description' => 'Gestiona tus clientes y ventas de manera efectiva.', 'icon' => 'users'),
				array('title' => 'Reportes', 'description' => 'Análisis detallados para tomar mejores decisiones.', 'icon' => 'bar-chart'),
			),
		);

		return \Response::forge(\View::forge('landing/home', $data, false));
	}
}
