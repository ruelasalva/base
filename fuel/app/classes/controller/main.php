<?php
/**
 * CONTROLADOR MAIN
 *
 * Controlador principal de la aplicación.
 * Ejemplo de cómo estructurar un controlador usando Controller_Base.
 *
 * @package    app
 * @extends    Controller_Base
 */
class Controller_Main extends Controller_Base
{
	/**
	 * BEFORE
	 *
	 * @return  void
	 */
	public function before()
	{
		# REQUERIDA PARA EL TEMPLATING
		parent::before();

		# AQUI SE PUEDEN VERIFICAR PERMISOS
		# Ejemplo:
		# if(!Auth::member(100) && !Auth::member(50))
		# {
		#     Session::set_flash('error', 'No tienes los permisos para acceder a esta sección.');
		#     Response::redirect('/');
		# }
	}

	/**
	 * INDEX
	 *
	 * MUESTRA LA PAGINA DE INICIO
	 *
	 * @param   string  $search  Término de búsqueda (opcional)
	 * @access  public
	 * @return  void
	 */
	public function action_index($search = '')
	{
		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE ESTABLECE EL TITULO Y CONTENIDO
		$this->template->title = 'Inicio - Aplicación Base';
		$this->template->content = View::forge('main/index', $data);
	}

	/**
	 * AGREGAR
	 *
	 * MUESTRA FORMULARIO PARA AGREGAR NUEVO REGISTRO
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_agregar()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SI ES UNA PETICION POST
		if (Input::method() == 'POST')
		{
			# AQUI SE PROCESA EL FORMULARIO
			# Ejemplo:
			# $model = Model_Ejemplo::forge();
			# $model->nombre = Input::post('nombre');
			# $model->save();

			# SE ESTABLECE MENSAJE DE EXITO
			Session::set_flash('success', 'Registro agregado correctamente.');

			# SE REDIRECCIONA
			Response::redirect($this->get_module_url());
		}

		# SE ESTABLECE EL TITULO Y CONTENIDO
		$this->template->title = 'Agregar - Aplicación Base';
		$this->template->content = View::forge('main/agregar', $data);
	}

	/**
	 * INFO
	 *
	 * MUESTRA INFORMACION DETALLADA DE UN REGISTRO
	 *
	 * @param   int  $id  ID del registro
	 * @access  public
	 * @return  void
	 */
	public function action_info($id = null)
	{
		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SI NO SE PROPORCIONA ID
		if ($id === null)
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			Session::set_flash('error', 'No se proporcionó un ID válido.');

			# SE REDIRECCIONA
			Response::redirect($this->get_module_url());
		}

		# AQUI SE BUSCA EL REGISTRO
		# Ejemplo:
		# $data['registro'] = Model_Ejemplo::find($id);

		# SE ESTABLECE EL TITULO Y CONTENIDO
		$this->template->title = 'Información - Aplicación Base';
		$this->template->content = View::forge('main/info', $data);
	}

	/**
	 * EDITAR
	 *
	 * MUESTRA FORMULARIO PARA EDITAR UN REGISTRO
	 *
	 * @param   int  $id  ID del registro
	 * @access  public
	 * @return  void
	 */
	public function action_editar($id = null)
	{
		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SI NO SE PROPORCIONA ID
		if ($id === null)
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			Session::set_flash('error', 'No se proporcionó un ID válido.');

			# SE REDIRECCIONA
			Response::redirect($this->get_module_url());
		}

		# AQUI SE BUSCA EL REGISTRO
		# Ejemplo:
		# $data['registro'] = Model_Ejemplo::find($id);

		# SI ES UNA PETICION POST
		if (Input::method() == 'POST')
		{
			# AQUI SE PROCESA EL FORMULARIO
			# Ejemplo:
			# $model = Model_Ejemplo::find($id);
			# $model->nombre = Input::post('nombre');
			# $model->save();

			# SE ESTABLECE MENSAJE DE EXITO
			Session::set_flash('success', 'Registro actualizado correctamente.');

			# SE REDIRECCIONA
			Response::redirect($this->get_module_url());
		}

		# SE ESTABLECE EL TITULO Y CONTENIDO
		$this->template->title = 'Editar - Aplicación Base';
		$this->template->content = View::forge('main/editar', $data);
	}

	/**
	 * ELIMINAR
	 *
	 * REALIZA ELIMINACION LOGICA DE UN REGISTRO (NO FISICA)
	 *
	 * @param   int  $id  ID del registro
	 * @access  public
	 * @return  void
	 */
	public function action_eliminar($id = null)
	{
		# SI NO SE PROPORCIONA ID
		if ($id === null)
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			Session::set_flash('error', 'No se proporcionó un ID válido.');

			# SE REDIRECCIONA
			Response::redirect($this->get_module_url());
		}

		# AQUI SE REALIZA LA ELIMINACION LOGICA
		# Ejemplo:
		# $model = Model_Ejemplo::find($id);
		# $model->deleted = 1;
		# $model->save();

		# SE ESTABLECE EL MENSAJE DE EXITO
		Session::set_flash('success', 'Registro eliminado correctamente.');

		# SE REDIRECCIONA
		Response::redirect($this->get_module_url());
	}
}
