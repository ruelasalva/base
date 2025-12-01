<?php
/**
 * Landing Module - Contact Controller
 *
 * @package    Landing
 * @version    1.0.0
 */

namespace Landing;

class Controller_Contact extends \Controller
{
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

		return \Response::forge(\View::forge('landing/contact', $data, false));
	}

	public function action_enviar()
	{
		if (\Input::method() == 'POST')
		{
			\Session::set_flash('success', 'Mensaje enviado correctamente.');
			\Response::redirect('contacto');
		}
		\Response::redirect('contacto');
	}
}
