<?php
/**
 * Modulo Ejemplo - Example Controller
 *
 * @package    Modulo_Ejemplo
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace Modulo_Ejemplo;

/**
 * Example Controller for the Modulo Ejemplo package
 *
 * This controller demonstrates a basic multi-tenant module structure.
 */
class Controller_Ejemplo extends \Controller
{
	/**
	 * Index action - displays module status
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Modulo Ejemplo',
			'status' => 'active',
			'message' => 'This module is active for the current tenant.',
		);

		return \Response::forge(\View::forge('modulo_ejemplo/index', $data, false));
	}

	/**
	 * Info action - displays module information
	 *
	 * @return \Response
	 */
	public function action_info()
	{
		$tenant_modules = array();

		if (defined('TENANT_ACTIVE_MODULES'))
		{
			$serialized = TENANT_ACTIVE_MODULES;

			if ( ! empty($serialized) && is_string($serialized))
			{
				$unserialized = unserialize($serialized, array('allowed_classes' => false));

				if ($unserialized !== false && is_array($unserialized))
				{
					$tenant_modules = $unserialized;
				}
			}
		}

		$data = array(
			'module_key' => MODULO_EJEMPLO_KEY,
			'active_modules' => $tenant_modules,
		);

		return \Response::forge(json_encode($data), 200, array(
			'Content-Type' => 'application/json',
		));
	}
}
