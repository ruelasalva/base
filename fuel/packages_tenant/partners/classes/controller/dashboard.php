<?php
/**
 * Partners Module - Dashboard Controller
 *
 * @package    Partners
 * @version    1.0.0
 */

namespace Partners;

class Controller_Dashboard extends \Controller
{
	public function action_index()
	{
		$data = array(
			'module_name' => 'Panel de Partner',
			'stats' => array(
				'alliances' => array('title' => 'Alianzas Activas', 'count' => 0, 'icon' => 'handshake-o', 'link' => 'partners/alliances'),
				'contracts' => array('title' => 'Contratos', 'count' => 0, 'icon' => 'file-text', 'link' => 'partners/contracts'),
				'commission' => array('title' => 'Comisión del Mes', 'count' => '$0.00', 'icon' => 'usd', 'link' => 'partners/commissions'),
			),
			'quick_links' => array(
				array('title' => 'Mis Alianzas', 'url' => 'partners/alliances', 'icon' => 'handshake-o'),
				array('title' => 'Contratos', 'url' => 'partners/contracts', 'icon' => 'file-text'),
				array('title' => 'Comisiones', 'url' => 'partners/commissions', 'icon' => 'money'),
			),
		);

		return \Response::forge(\View::forge('partners/dashboard', $data, false));
	}
}
