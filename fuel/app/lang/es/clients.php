<?php
/**
 * Idioma Español - Módulo Clients (Clientes)
 */

return array(
	'title' => 'Gestión de Clientes',
	
	'menu' => array(
		'dashboard' => 'Panel',
		'clients' => 'Clientes',
		'add' => 'Agregar Cliente',
		'list' => 'Listado',
		'orders' => 'Pedidos',
		'invoices' => 'Facturas',
		'payments' => 'Pagos',
		'history' => 'Historial',
	),
	
	'fields' => array(
		'client_code' => 'Código de Cliente',
		'client_name' => 'Nombre del Cliente',
		'contact_name' => 'Nombre de Contacto',
		'rfc' => 'RFC',
		'credit_limit' => 'Límite de Crédito',
		'payment_terms' => 'Términos de Pago',
		'client_type' => 'Tipo de Cliente',
		'assigned_seller' => 'Vendedor Asignado',
	),
	
	'messages' => array(
		'client_added' => 'Cliente agregado exitosamente',
		'client_updated' => 'Cliente actualizado exitosamente',
		'client_deleted' => 'Cliente eliminado exitosamente',
	),
	
	'stats' => array(
		'total_clients' => 'Clientes Totales',
		'active_clients' => 'Clientes Activos',
		'new_this_month' => 'Nuevos Este Mes',
		'total_revenue' => 'Ingresos Totales',
	),
);
