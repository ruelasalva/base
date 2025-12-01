<?php
/**
 * ERP Store Module - Search Controller
 *
 * @package    ERP_Store
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Store;

/**
 * Search Controller for the Store Module
 *
 * Provides product search functionality.
 */
class Controller_Search extends \Controller
{
	/**
	 * Index action - displays search results
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$query = \Input::get('q', '');

		$data = array(
			'page_title' => 'Búsqueda: ' . htmlspecialchars($query, ENT_QUOTES, 'UTF-8'),
			'query' => $query,
			'results' => array(),
			'total_results' => 0,
		);

		return \Response::forge(\View::forge('erp_store/search/index', $data, false));
	}
}
