<?php
/**
 * Partners Module - Commissions Controller
 *
 * @package    Partners
 * @version    1.0.0
 */

namespace Partners;

class Controller_Commissions extends \Controller
{
	public function action_index()
	{
		$data = array(
			'module_name' => 'Mis Comisiones',
			'commissions' => array(),
			'summary' => array('month' => 0, 'year' => 0, 'pending' => 0),
		);
		return \Response::forge(\View::forge('partners/commissions/index', $data, false));
	}
}
