<?php
/**
 * Controlador Base
 *
 * Controlador base del que heredan todos los controladores de la aplicación.
 * Proporciona sistema de templating y acciones estándar CRUD.
 *
 * Acciones estándar:
 * - action_index()    : Listado de registros (página inicio del módulo)
 * - action_agregar()  : Formulario para insertar nuevo registro
 * - action_info()     : Mostrar información detallada de un registro
 * - action_editar()   : Formulario para modificar un registro
 * - action_eliminar() : Eliminación lógica de un registro (no física)
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
	 * @var  int  Registros por página para paginación
	 */
	protected $per_page = 100;

	/**
	 * BEFORE
	 *
	 * Método que se ejecuta antes de cada acción.
	 * Inicializa el template y puede usarse para verificar permisos.
	 *
	 * @return  void
	 */
	public function before()
	{
		# REQUERIDA PARA EL TEMPLATING
		parent::before();

		if ($this->auto_render === true)
		{
			$this->template = View::forge($this->template_name);

			# VALORES POR DEFECTO PARA EL TEMPLATE
			$this->template->title = 'Aplicación Base';
			$this->template->content = '';
		}
	}

	/**
	 * AFTER
	 *
	 * Método que se ejecuta después de cada acción.
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

	/**
	 * INDEX
	 *
	 * Muestra un listado de registros.
	 * Sobrescribir en controladores hijos.
	 *
	 * @param   string  $search  Término de búsqueda
	 * @access  public
	 * @return  void
	 */
	public function action_index($search = '')
	{
		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE ESTABLECE EL TITULO
		$this->template->title = 'Inicio';
		$this->template->content = '';
	}

	/**
	 * AGREGAR
	 *
	 * Muestra formulario para insertar nuevo registro.
	 * Sobrescribir en controladores hijos.
	 *
	 * @access  public
	 * @return  void
	 */
	public function action_agregar()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE ESTABLECE EL TITULO
		$this->template->title = 'Agregar';
		$this->template->content = '';
	}

	/**
	 * INFO
	 *
	 * Muestra información detallada de un registro.
	 * Sobrescribir en controladores hijos.
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

		# SE ESTABLECE EL TITULO
		$this->template->title = 'Información';
		$this->template->content = '';
	}

	/**
	 * EDITAR
	 *
	 * Muestra formulario para modificar un registro.
	 * Sobrescribir en controladores hijos.
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

		# SE ESTABLECE EL TITULO
		$this->template->title = 'Editar';
		$this->template->content = '';
	}

	/**
	 * ELIMINAR
	 *
	 * Realiza eliminación lógica de un registro (no física).
	 * Sobrescribir en controladores hijos.
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

		# SE ESTABLECE EL MENSAJE DE EXITO
		Session::set_flash('success', 'Registro eliminado correctamente.');

		# SE REDIRECCIONA
		Response::redirect($this->get_module_url());
	}

	/**
	 * GET MODULE URL
	 *
	 * Obtiene la URL base del módulo actual.
	 * Útil para redirecciones.
	 *
	 * @return  string
	 */
	protected function get_module_url()
	{
		# SE OBTIENE EL NOMBRE DEL CONTROLADOR
		$controller = strtolower(str_replace('Controller_', '', get_class($this)));

		# SE REEMPLAZA UNDERSCORES POR SLASHES
		$controller = str_replace('_', '/', $controller);

		return $controller;
	}

	/**
	 * LIMPIAR BUSQUEDA
	 *
	 * Limpia y prepara una cadena de búsqueda para consultas.
	 *
	 * @param   string  $search  Cadena de búsqueda
	 * @return  string
	 */
	protected function limpiar_busqueda($search)
	{
		# SE LIMPIA LA CADENA DE BUSQUEDA
		$search = str_replace('+', ' ', rawurldecode($search));

		# SE REEMPLAZA LOS ESPACIOS POR PORCENTAJES
		$search = str_replace(' ', '%', $search);

		return $search;
	}
}
