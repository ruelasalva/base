<?php

class Controller_Admin_Recepciones extends Controller_Admin
{
	public function action_index()
	{
		if (!Helper_Permission::can('recepciones', 'view'))
		{
			Session::set_flash('error', 'No tienes permisos para ver recepciones');
			Response::redirect('admin');
		}

		$data = [
			'title' => 'Recepciones de Mercancía',
			'username' => Auth::get('username'),
			'email' => Auth::get('email')
		];

		$data['content'] = '<div class="container-fluid p-4"><h1>Módulo Recepciones</h1><p>En desarrollo...</p></div>';
		$template_file = Helper_Template::get_template_file();
		return View::forge($template_file, $data);
	}
}
