<?php
/**
 * Controlador Principal
 *
 * Controlador principal de la aplicación que extiende del Controller_Base.
 *
 * @package    app
 * @extends    Controller_Base
 */
class Controller_Main extends Controller_Base
{
	/**
	 * Página de inicio
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_index()
	{
		$this->template->title = 'Inicio - Aplicación Base';
		$this->template->content = View::forge('main/index');
	}
}
