<?php
/**
 * Controlador Placeholder de Módulo
 *
 * Este controlador muestra una página de placeholder para los módulos
 * que aún no han sido implementados. Permite a los usuarios ver que
 * el módulo existe pero está pendiente de desarrollo.
 *
 * @package    App
 * @subpackage Controller
 * @version    1.0.0
 */
class Controller_Module_Placeholder extends Controller_Base
{
	/**
	 * @var array Información de cada módulo
	 */
	protected $modules_info = array(
		'admin' => array(
			'name' => 'Panel de Administración',
			'icon' => 'cog',
			'color' => '#3498db',
			'description' => 'Panel de administración del sistema. Gestione usuarios, configuraciones y reportes.',
			'features' => array(
				'Gestión de usuarios y roles',
				'Configuración del sistema',
				'Reportes y estadísticas',
				'Auditoría y logs',
			),
		),
		'providers' => array(
			'name' => 'Portal de Proveedores',
			'icon' => 'briefcase',
			'color' => '#27ae60',
			'description' => 'Portal para proveedores. Gestione productos, inventario y órdenes de compra.',
			'features' => array(
				'Gestión de productos',
				'Control de inventario',
				'Órdenes de compra',
				'Facturación',
			),
		),
		'partners' => array(
			'name' => 'Portal de Socios',
			'icon' => 'link',
			'color' => '#9b59b6',
			'description' => 'Portal para socios comerciales. Administre alianzas, contratos y comisiones.',
			'features' => array(
				'Gestión de alianzas',
				'Contratos',
				'Comisiones',
				'Reportes de ventas',
			),
		),
		'sellers' => array(
			'name' => 'Panel de Vendedores',
			'icon' => 'usd',
			'color' => '#3498db',
			'description' => 'Panel para vendedores. Gestione ventas, clientes y comisiones.',
			'features' => array(
				'Gestión de ventas',
				'Cartera de clientes',
				'Cotizaciones',
				'Comisiones',
			),
		),
		'clients' => array(
			'name' => 'Portal de Clientes',
			'icon' => 'user',
			'color' => '#f39c12',
			'description' => 'Portal para clientes. Vea sus pedidos, perfil y acceda a soporte.',
			'features' => array(
				'Historial de pedidos',
				'Perfil de cliente',
				'Soporte técnico',
				'Facturación',
			),
		),
		'tienda' => array(
			'name' => 'Tienda Online',
			'icon' => 'shopping-cart',
			'color' => '#e74c3c',
			'description' => 'Tienda online completa con catálogo de productos, carrito de compras y proceso de checkout.',
			'features' => array(
				'Catálogo de productos',
				'Carrito de compras',
				'Proceso de checkout',
				'Búsqueda de productos',
			),
		),
		'landing' => array(
			'name' => 'Landing Page',
			'icon' => 'home',
			'color' => '#2c3e50',
			'description' => 'Página de aterrizaje con información institucional, contacto y páginas de contenido.',
			'features' => array(
				'Página principal',
				'Acerca de nosotros',
				'Formulario de contacto',
				'Páginas dinámicas',
			),
		),
		'contacto' => array(
			'name' => 'Formulario de Contacto',
			'icon' => 'envelope',
			'color' => '#16a085',
			'description' => 'Formulario de contacto para recibir consultas de clientes y visitantes.',
			'features' => array(
				'Formulario de contacto',
				'Notificaciones por email',
				'Historial de mensajes',
			),
		),
	);

	/**
	 * Acción placeholder genérica para cualquier módulo
	 *
	 * @param string $module Nombre del módulo
	 * @return void
	 */
	public function action_placeholder($module = 'default')
	{
		$module = strtolower($module);

		// Obtener información del módulo
		$info = isset($this->modules_info[$module]) 
			? $this->modules_info[$module] 
			: array(
				'name' => ucfirst($module),
				'icon' => 'folder-open',
				'color' => '#667eea',
				'description' => 'Este módulo está en desarrollo.',
				'features' => array('Próximamente disponible'),
			);

		$data = array(
			'module_key' => $module,
			'module_info' => $info,
		);

		$this->template->title = $info['name'] . ' - En Desarrollo';
		$this->template->content = View::forge('module/placeholder', $data);
	}

	/**
	 * Router personalizado para capturar cualquier sub-ruta
	 *
	 * @param string $method Método/subruta
	 * @param array $params Parámetros adicionales
	 * @return Response
	 */
	public function router($method, $params)
	{
		// Si el método tiene slashes, el primer segmento es el módulo
		$parts = explode('/', $method);
		$module = $parts[0];

		// Llamar al placeholder con el nombre del módulo
		return $this->action_placeholder($module);
	}
}
