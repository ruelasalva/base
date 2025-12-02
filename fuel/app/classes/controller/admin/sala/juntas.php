<?php

/**
 * CONTROLADOR ADMIN_SALA_JUNTAS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Sala_Juntas extends Controller_Admin
{
	/**
	 * INDEX
	 *
	 * MUESTRA UNA LISTADO DE REGISTROS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_index()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE CARGA LA VISTA
		$this->template->title   = 'Sala de juntas';
		$this->template->content = View::forge('admin/sala_juntas/index', $data, false);
	}
}
