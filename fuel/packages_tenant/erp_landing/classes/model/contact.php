<?php
/**
 * ERP Landing Module - Contact Model
 *
 * Contact form submission model.
 *
 * @package    ERP_Landing
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Landing;

/**
 * Contact Model
 *
 * Represents a contact form submission.
 */
class Model_Contact extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'contact_submissions';

	/**
	 * @var string Primary key
	 */
	protected static $_primary_key = array('id');

	/**
	 * @var array Table properties
	 */
	protected static $_properties = array(
		'id',
		'name',
		'email',
		'phone',
		'subject',
		'message',
		'status', // 'new', 'read', 'responded', 'archived'
		'ip_address',
		'created_at',
		'updated_at',
	);

	/**
	 * @var array Observer configuration
	 */
	protected static $_observers = array(
		'Orm\\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'property' => 'created_at',
			'mysql_timestamp' => true,
		),
		'Orm\\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'property' => 'updated_at',
			'mysql_timestamp' => true,
		),
	);

	/**
	 * Get status label
	 *
	 * @return string
	 */
	public function get_status_label()
	{
		$labels = array(
			'new' => 'Nuevo',
			'read' => 'Leído',
			'responded' => 'Respondido',
			'archived' => 'Archivado',
		);

		return isset($labels[$this->status]) ? $labels[$this->status] : $this->status;
	}
}
