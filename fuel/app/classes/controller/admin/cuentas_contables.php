<?php

class Controller_Admin_Cuentas_Contables extends Controller_Admin
{
	public function action_index()
	{
		if (!Helper_Permission::can('cuentas_contables', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver cuentas contables');
			Response::redirect('admin');
		}

		$data = [
			'title' => 'Cuentas Contables',
			'username' => Auth::get('username'),
			'email' => Auth::get('email')
		];

		$data['content'] = '<div class="container-fluid p-4"><h1>Módulo Cuentas Contables</h1><p>En desarrollo...</p></div>';
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}
}
