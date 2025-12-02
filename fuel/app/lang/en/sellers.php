<?php
/**
 * English Language - Sellers Module
 */

return array(
	'title' => 'Sellers Management',
	
	'menu' => array(
		'dashboard' => 'Dashboard',
		'sellers' => 'Sellers',
		'add' => 'Add Seller',
		'list' => 'List',
		'sales' => 'Sales',
		'commissions' => 'Commissions',
		'goals' => 'Goals',
		'reports' => 'Reports',
	),
	
	'fields' => array(
		'seller_code' => 'Seller Code',
		'full_name' => 'Full Name',
		'commission_percentage' => 'Commission Percentage',
		'sales_zone' => 'Sales Zone',
		'hire_date' => 'Hire Date',
		'monthly_goal' => 'Monthly Goal',
		'status' => 'Status',
	),
	
	'messages' => array(
		'seller_added' => 'Seller added successfully',
		'seller_updated' => 'Seller updated successfully',
		'seller_deleted' => 'Seller deleted successfully',
		'goal_achieved' => 'Goal achieved',
	),
	
	'stats' => array(
		'total_sales' => 'Total Sales',
		'commission_earned' => 'Commission Earned',
		'goal_progress' => 'Goal Progress',
		'active_sellers' => 'Active Sellers',
	),
);
