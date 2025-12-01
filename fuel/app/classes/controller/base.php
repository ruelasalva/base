<?php
/**
 * Controlador Base
 *
 * @package    app
 * @extends    Controller
 */
class Controller_Base extends Controller
{
	/**
	 * @var  View  Template de página
	 */
	public $template;

	/**
	 * @var  string  Nombre del template
	 */
	public $template_name = 'template';

	/**
	 * @var  bool  Si se debe autorender el template
	 */
	public $auto_render = true;

	/**
	 * Método before - Se ejecuta antes de cada acción
	 *
	 * @return  void
	 */
	public function before()
	{
		parent::before();

		if ($this->auto_render === true)
		{
			$this->template = View::forge($this->template_name);

			// Valores por defecto para el template
			$this->template->title = 'Aplicación Base';
			$this->template->content = '';
		}
	}

	/**
	 * Método after - Se ejecuta después de cada acción
	 *
	 * @param   Response  $response  Response object
	 * @return  Response
	 */
	public function after($response)
	{
		if ($this->auto_render === true)
		{
			$response = Response::forge($this->template);
		}

		return parent::after($response);
	}
}
