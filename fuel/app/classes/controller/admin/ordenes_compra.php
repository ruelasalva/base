<?php

/**
 * Controlador de compatibilidad - Redirige a ordenescompra
 */
class Controller_Admin_Ordenes_Compra extends Controller_Admin
{
	public function action_index()
	{
		Response::redirect('admin/ordenescompra');
	}
	
	public function action_create()
	{
		Response::redirect('admin/ordenescompra/create');
	}
	
	public function action_edit($id = null)
	{
		Response::redirect('admin/ordenescompra/edit/' . $id);
	}
	
	public function action_view($id = null)
	{
		Response::redirect('admin/ordenescompra/view/' . $id);
	}
	
	public function action_delete($id = null)
	{
		Response::redirect('admin/ordenescompra/delete/' . $id);
	}
	
	public function action_approve($id = null)
	{
		Response::redirect('admin/ordenescompra/approve/' . $id);
	}
	
	public function action_reject($id = null)
	{
		Response::redirect('admin/ordenescompra/reject/' . $id);
	}
	
	public function action_receive($id = null)
	{
		Response::redirect('admin/ordenescompra/receive/' . $id);
	}
}
