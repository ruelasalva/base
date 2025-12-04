<?php

class Controller_Admin_Ordenes_Compra extends Controller_Admin
{
	public function action_index()
	{
		if (!Helper_Permission::can('ordenes_compra', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver órdenes de compra');
			Response::redirect('admin');
		}

		$data = [
			'title' => 'Órdenes de Compra',
			'username' => Auth::get('username'),
			'email' => Auth::get('email')
		];

		$data['content'] = '<div class="container-fluid p-4"><h1>Módulo Órdenes de Compra</h1><p>En desarrollo...</p></div>';
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}
}
