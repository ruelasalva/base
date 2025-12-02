<?php
/**
 * Archivo de idioma Inglés - Común
 * 
 * General system translations
 */

return array(
	// General actions
	'actions' => array(
		'add' => 'Add',
		'edit' => 'Edit',
		'delete' => 'Delete',
		'save' => 'Save',
		'cancel' => 'Cancel',
		'back' => 'Back',
		'search' => 'Search',
		'filter' => 'Filter',
		'export' => 'Export',
		'import' => 'Import',
		'view' => 'View',
		'print' => 'Print',
		'download' => 'Download',
		'upload' => 'Upload',
		'submit' => 'Submit',
		'close' => 'Close',
		'confirm' => 'Confirm',
		'refresh' => 'Refresh',
		'reset' => 'Reset',
	),
	
	// Success messages
	'success' => array(
		'saved' => 'Record saved successfully',
		'updated' => 'Record updated successfully',
		'deleted' => 'Record deleted successfully',
		'created' => 'Record created successfully',
		'uploaded' => 'File uploaded successfully',
		'sent' => 'Sent successfully',
	),
	
	// Error messages
	'error' => array(
		'general' => 'An error has occurred. Please try again.',
		'required' => 'This field is required',
		'invalid' => 'The entered value is not valid',
		'not_found' => 'Record not found',
		'duplicate' => 'A record with this data already exists',
		'permission' => 'You do not have permission to perform this action',
		'database' => 'Database error',
		'file_upload' => 'Error uploading file',
		'file_size' => 'File is too large',
		'file_type' => 'File type not allowed',
	),
	
	// Warning messages
	'warning' => array(
		'unsaved' => 'There are unsaved changes',
		'delete_confirm' => 'Are you sure you want to delete this record?',
		'irreversible' => 'This action cannot be undone',
	),
	
	// Informative messages
	'info' => array(
		'no_records' => 'No records to display',
		'loading' => 'Loading...',
		'processing' => 'Processing...',
		'select_option' => 'Select an option',
	),
	
	// Common fields
	'fields' => array(
		'id' => 'ID',
		'name' => 'Name',
		'email' => 'Email',
		'phone' => 'Phone',
		'address' => 'Address',
		'city' => 'City',
		'state' => 'State',
		'country' => 'Country',
		'zipcode' => 'Zip Code',
		'description' => 'Description',
		'notes' => 'Notes',
		'status' => 'Status',
		'active' => 'Active',
		'inactive' => 'Inactive',
		'date' => 'Date',
		'created_at' => 'Created At',
		'updated_at' => 'Updated At',
		'created_by' => 'Created By',
		'updated_by' => 'Updated By',
		'password' => 'Password',
		'password_confirm' => 'Confirm Password',
		'username' => 'Username',
		'role' => 'Role',
		'permissions' => 'Permissions',
	),
	
	// Navigation
	'nav' => array(
		'home' => 'Home',
		'dashboard' => 'Dashboard',
		'admin' => 'Administration',
		'settings' => 'Settings',
		'profile' => 'Profile',
		'logout' => 'Logout',
		'login' => 'Login',
	),
	
	// Pagination
	'pagination' => array(
		'previous' => 'Previous',
		'next' => 'Next',
		'first' => 'First',
		'last' => 'Last',
		'showing' => 'Showing',
		'of' => 'of',
		'results' => 'results',
		'page' => 'Page',
	),
	
	// Validation
	'validation' => array(
		'required' => 'The :field field is required',
		'min_length' => 'The :field field must be at least :param characters',
		'max_length' => 'The :field field cannot be more than :param characters',
		'exact_length' => 'The :field field must be exactly :param characters',
		'match_field' => 'The :field field must match :param',
		'valid_email' => 'The :field field must be a valid email',
		'valid_emails' => 'The :field field must contain valid emails',
		'valid_url' => 'The :field field must be a valid URL',
		'valid_ip' => 'The :field field must be a valid IP',
		'numeric' => 'The :field field must be numeric',
		'numeric_min' => 'The :field field must be greater than or equal to :param',
		'numeric_max' => 'The :field field must be less than or equal to :param',
		'valid_string' => 'The :field field can only contain valid characters',
	),
	
	// Modules
	'modules' => array(
		'admin' => 'Administration',
		'clients' => 'Clients',
		'partners' => 'Partners',
		'providers' => 'Providers',
		'sellers' => 'Sellers',
		'store' => 'Store',
		'landing' => 'Landing Page',
	),
	
	// Days of the week
	'days' => array(
		'monday' => 'Monday',
		'tuesday' => 'Tuesday',
		'wednesday' => 'Wednesday',
		'thursday' => 'Thursday',
		'friday' => 'Friday',
		'saturday' => 'Saturday',
		'sunday' => 'Sunday',
	),
	
	// Months
	'months' => array(
		'january' => 'January',
		'february' => 'February',
		'march' => 'March',
		'april' => 'April',
		'may' => 'May',
		'june' => 'June',
		'july' => 'July',
		'august' => 'August',
		'september' => 'September',
		'october' => 'October',
		'november' => 'November',
		'december' => 'December',
	),
	
	// Date formats
	'date_format' => array(
		'short' => 'm/d/Y',
		'long' => 'F d, Y',
		'time' => 'h:i:s A',
		'datetime' => 'm/d/Y h:i:s A',
	),
);
