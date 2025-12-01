<?php
/**
 * ERP Landing Module - Content Service
 *
 * Content management service.
 *
 * @package    ERP_Landing
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Landing;

/**
 * Content Service
 *
 * Handles content operations for landing pages.
 */
class Service_Content
{
	/**
	 * Get page by slug
	 *
	 * @param string $slug Page slug
	 * @return Model_Page|null
	 */
	public static function get_page($slug)
	{
		return Model_Page::get_by_slug($slug);
	}

	/**
	 * Get all active pages
	 *
	 * @return array
	 */
	public static function get_all_pages()
	{
		return Model_Page::query()
			->where('is_active', 1)
			->order_by('sort_order', 'asc')
			->get();
	}

	/**
	 * Submit contact form
	 *
	 * @param array $data Form data
	 * @return Model_Contact|false
	 */
	public static function submit_contact($data)
	{
		// Validate required fields
		$required = array('name', 'email', 'subject', 'message');

		foreach ($required as $field)
		{
			if (empty($data[$field]))
			{
				return false;
			}
		}

		// Create contact submission
		$contact = Model_Contact::forge(array(
			'name' => $data['name'],
			'email' => $data['email'],
			'phone' => isset($data['phone']) ? $data['phone'] : '',
			'subject' => $data['subject'],
			'message' => $data['message'],
			'status' => 'new',
			'ip_address' => \Input::real_ip(),
		));

		if ($contact->save())
		{
			return $contact;
		}

		return false;
	}
}
