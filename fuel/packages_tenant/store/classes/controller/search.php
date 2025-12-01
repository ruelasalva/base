<?php
/**
 * Store Module - Search Controller
 *
 * @package    Store
 * @version    1.0.0
 */

namespace Store;

class Controller_Search extends \Controller
{
	public function action_index()
	{
		$query = \Input::get('q', '');
		$data = array(
			'page_title' => 'Búsqueda: ' . htmlspecialchars($query, ENT_QUOTES, 'UTF-8'),
			'query' => $query,
			'results' => array(),
			'total_results' => 0,
		);
		return \Response::forge(\View::forge('store/search/index', $data, false));
	}
}
