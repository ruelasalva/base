<?php
/**
 * English Language - Clients Module
 */

return array(
	'title' => 'Clients Management',
	
	'menu' => array(
		'dashboard' => 'Dashboard',
		'clients' => 'Clients',
		'add' => 'Add Client',
		'list' => 'List',
		'orders' => 'Orders',
		'invoices' => 'Invoices',
		'payments' => 'Payments',
		'history' => 'History',
	),
	
	'fields' => array(
		'client_code' => 'Client Code',
		'client_name' => 'Client Name',
		'contact_name' => 'Contact Name',
		'rfc' => 'Tax ID',
		'credit_limit' => 'Credit Limit',
		'payment_terms' => 'Payment Terms',
		'client_type' => 'Client Type',
		'assigned_seller' => 'Assigned Seller',
	),
	
	'messages' => array(
		'client_added' => 'Client added successfully',
		'client_updated' => 'Client updated successfully',
		'client_deleted' => 'Client deleted successfully',
	),
	
	'stats' => array(
		'total_clients' => 'Total Clients',
		'active_clients' => 'Active Clients',
		'new_this_month' => 'New This Month',
		'total_revenue' => 'Total Revenue',
	),
);
