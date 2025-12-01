<?php
/**
 * ERP Landing Module - Contact Controller
 *
 * @package    ERP_Landing
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Landing;

/**
 * Contact Controller for the Landing Module
 *
 * Provides contact form functionality.
 */
class Controller_Contact extends \Controller
{
	/**
	 * Index action - displays contact page
	 *
	 * @return \Response
	 */
	public function action_index()
	{
		$data = array(
			'page_title' => 'Contacto',
			'contact_info' => array(
				'email' => 'info@example.com',
				'phone' => '+1 234 567 890',
				'address' => 'Dirección de la empresa',
			),
		);

		return \Response::forge(\View::forge('erp_landing/contact', $data, false));
	}

	/**
	 * Submit action - process contact form
	 *
	 * @return void
	 */
	public function action_enviar()
	{
		if (\Input::method() == 'POST')
		{
			// Process contact form
			// Validate and send email

			\Session::set_flash('success', 'Mensaje enviado correctamente.');
			\Response::redirect('contacto');
		}

		\Response::redirect('contacto');
	}
}
