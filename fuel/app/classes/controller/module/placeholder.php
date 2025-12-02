<?php
/**
 * Placeholder Controller
 *
 * Controller for displaying placeholder pages for modules that are still in development.
 * This provides a user-friendly message instead of 404 errors.
 *
 * @package    App
 * @subpackage Controller
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */
class Controller_Module_Placeholder extends Controller_Base
{
	/**
	 * @var array Mapping of route names to module display names
	 */
	protected $module_names = array(
		'admin'     => 'Panel de Administración',
		'providers' => 'Portal de Proveedores',
		'partners'  => 'Portal de Socios Comerciales',
		'sellers'   => 'Portal de Vendedores',
		'clients'   => 'Portal de Clientes',
		'tienda'    => 'Tienda Online',
		'landing'   => 'Landing Page',
		'contacto'  => 'Contacto',
	);

	/**
	 * @var array Module descriptions
	 */
	protected $module_descriptions = array(
		'admin'     => 'Gestión de usuarios, roles, configuración del sistema y reportes.',
		'providers' => 'Gestión de productos, control de inventario y órdenes de compra.',
		'partners'  => 'Gestión de alianzas, contratos y comisiones de partner.',
		'sellers'   => 'Gestión de ventas, CRM de clientes y cotizaciones.',
		'clients'   => 'Historial de pedidos, perfil del cliente y tickets de soporte.',
		'tienda'    => 'Catálogo de productos, carrito de compras y proceso de checkout.',
		'landing'   => 'Página principal, información de la empresa y contenido.',
		'contacto'  => 'Formulario de contacto y atención al cliente.',
	);

	/**
	 * Handle Admin module placeholder
	 *
	 * @return void
	 */
	public function action_admin()
	{
		$this->render_placeholder('admin');
	}

	/**
	 * Handle Providers module placeholder
	 *
	 * @return void
	 */
	public function action_providers()
	{
		$this->render_placeholder('providers');
	}

	/**
	 * Handle Partners module placeholder
	 *
	 * @return void
	 */
	public function action_partners()
	{
		$this->render_placeholder('partners');
	}

	/**
	 * Handle Sellers module placeholder
	 *
	 * @return void
	 */
	public function action_sellers()
	{
		$this->render_placeholder('sellers');
	}

	/**
	 * Handle Clients module placeholder
	 *
	 * @return void
	 */
	public function action_clients()
	{
		$this->render_placeholder('clients');
	}

	/**
	 * Handle Tienda (Store) module placeholder
	 *
	 * @return void
	 */
	public function action_tienda()
	{
		$this->render_placeholder('tienda');
	}

	/**
	 * Handle Landing module placeholder
	 *
	 * @return void
	 */
	public function action_landing()
	{
		$this->render_placeholder('landing');
	}

	/**
	 * Handle Contacto module placeholder
	 *
	 * @return void
	 */
	public function action_contacto()
	{
		$this->render_placeholder('contacto');
	}

	/**
	 * Generic index action that catches any module
	 *
	 * @param string $module Module name from URL
	 * @return void
	 */
	public function action_index($module = null)
	{
		if ($module && array_key_exists($module, $this->module_names))
		{
			$this->render_placeholder($module);
		}
		else
		{
			$this->render_placeholder('unknown');
		}
	}

	/**
	 * Render the placeholder view
	 *
	 * @param string $module_key Module key for display
	 * @return void
	 */
	protected function render_placeholder($module_key)
	{
		$module_name = isset($this->module_names[$module_key])
			? $this->module_names[$module_key]
			: ucfirst($module_key);

		$module_description = isset($this->module_descriptions[$module_key])
			? $this->module_descriptions[$module_key]
			: 'Este módulo se encuentra en desarrollo.';

		$this->template->title = $module_name . ' - En Desarrollo';

		$this->template->content = View::forge('module/placeholder', array(
			'module_key'         => $module_key,
			'module_name'        => $module_name,
			'module_description' => $module_description,
		));
	}
}
