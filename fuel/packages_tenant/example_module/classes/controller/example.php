<?php
/**
 * Example Module - Example Controller
 *
 * @package    Example_Module
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace Example_Module;

/**
 * Example Controller for the Example Module package
 *
 * This controller demonstrates a basic multi-tenant module structure.
 */
class Controller_Example extends \Controller
{
	/**
	 * Index action - displays module status
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'module_name' => 'Example Module',
			'status' => 'active',
			'message' => 'This module is active for the current tenant.',
		);

		return \Response::forge(\View::forge('example_module/index', $data, false));
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
			'module_key' => EXAMPLE_MODULE_KEY,
			'active_modules' => $tenant_modules,
		);

		return \Response::forge(json_encode($data), 200, array(
			'Content-Type' => 'application/json',
		));
	}
}
