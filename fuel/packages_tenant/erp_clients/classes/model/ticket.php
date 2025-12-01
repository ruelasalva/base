<?php
/**
 * ERP Clients Module - Ticket Model
 *
 * Support ticket model.
 *
 * @package    ERP_Clients
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Clients;

/**
 * Ticket Model
 *
 * Represents a support ticket.
 */
class Model_Ticket extends \Orm\Model
{
	/**
	 * @var string Table name
	 */
	protected static $_table_name = 'support_tickets';

	/**
	 * @var string Primary key
	 */
	protected static $_primary_key = array('id');

	/**
	 * @var array Table properties
	 */
	protected static $_properties = array(
		'id',
		'ticket_number',
		'user_id',
		'subject',
		'message',
		'category', // 'general', 'order', 'payment', 'shipping', 'other'
		'priority', // 'low', 'medium', 'high', 'urgent'
		'status', // 'open', 'in_progress', 'waiting', 'resolved', 'closed'
		'assigned_to',
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
			'open' => 'Abierto',
			'in_progress' => 'En Progreso',
			'waiting' => 'En Espera',
			'resolved' => 'Resuelto',
			'closed' => 'Cerrado',
		);

		return isset($labels[$this->status]) ? $labels[$this->status] : $this->status;
	}

	/**
	 * Get priority label
	 *
	 * @return string
	 */
	public function get_priority_label()
	{
		$labels = array(
			'low' => 'Baja',
			'medium' => 'Media',
			'high' => 'Alta',
			'urgent' => 'Urgente',
		);

		return isset($labels[$this->priority]) ? $labels[$this->priority] : $this->priority;
	}

	/**
	 * Generate ticket number
	 *
	 * @return string
	 */
	public static function generate_ticket_number()
	{
		return 'TKT-' . strtoupper(uniqid());
	}
}
