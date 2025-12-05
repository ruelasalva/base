<?php

/**
 * Controller_Admin_Base
 * 
 * Clase base para controladores del área de administración
 * Proporciona funcionalidad común y validación de permisos
 */
class Controller_Admin_Base extends Controller_Admin
{
	/**
	 * Template usado para las vistas
	 * @var string
	 */
	public $template = 'admin/template_coreui';

	/**
	 * Usuario actual
	 * @var array
	 */
	protected $user = null;

	/**
	 * Tenant ID actual
	 * @var int
	 */
	protected $tenant_id = null;

	/**
	 * Datos para la vista
	 * @var array
	 */
	protected $data = array();

	/**
	 * Before - Ejecutado antes de cada acción
	 */
	public function before()
	{
		parent::before();

		// Obtener información del usuario actual
		if (Auth::check()) {
			// Obtener user_id del usuario autenticado
			list($driver, $user_id) = Auth::get_user_id();
			$this->user = Model_User::find($user_id);
			$this->tenant_id = Session::get('tenant_id', 1);
			
			// Pasar datos comunes a todas las vistas
			$this->data['user'] = $this->user;
			$this->data['tenant_id'] = $this->tenant_id;
		}
	}

	/**
	 * After - Ejecutado después de cada acción
	 */
	public function after($response)
	{
		// Si hay template, asignar datos
		if ($this->template) {
			foreach ($this->data as $key => $value) {
				$this->template->set($key, $value);
			}
		}

		return parent::after($response);
	}

	/**
	 * Verificar permiso específico
	 * 
	 * @param string $module Módulo a verificar
	 * @param string $action Acción a verificar (view, create, edit, delete)
	 * @return bool
	 */
	protected function check_permission($module, $action = 'view')
	{
		if (!Helper_Permission::can($module, $action)) {
			Session::set_flash('error', 'No tienes permisos para realizar esta acción.');
			Response::redirect('admin');
		}
		return true;
	}

	/**
	 * Set page title
	 * 
	 * @param string $title
	 */
	protected function set_title($title)
	{
		$this->data['title'] = $title;
	}

	/**
	 * Agregar breadcrumb
	 * 
	 * @param array $breadcrumb
	 */
	protected function set_breadcrumb($breadcrumb)
	{
		$this->data['breadcrumb'] = $breadcrumb;
	}

	/**
	 * Respuesta JSON
	 * 
	 * @param array $data
	 * @param int $status
	 * @return Response
	 */
	protected function json_response($data, $status = 200)
	{
		return Response::forge(json_encode($data), $status, array(
			'Content-Type' => 'application/json; charset=utf-8'
		));
	}

	/**
	 * Respuesta de éxito
	 * 
	 * @param string $message
	 * @param mixed $data
	 * @return Response
	 */
	protected function success_response($message = 'Operación exitosa', $data = null)
	{
		return $this->json_response(array(
			'success' => true,
			'message' => $message,
			'data' => $data
		));
	}

	/**
	 * Respuesta de error
	 * 
	 * @param string $message
	 * @param mixed $errors
	 * @param int $status
	 * @return Response
	 */
	protected function error_response($message = 'Error en la operación', $errors = null, $status = 400)
	{
		return $this->json_response(array(
			'success' => false,
			'message' => $message,
			'errors' => $errors
		), $status);
	}

	/**
	 * Log de actividad
	 * 
	 * @param string $action
	 * @param string $module
	 * @param mixed $data
	 */
	protected function log_activity($action, $module, $data = null)
	{
		try {
			// Obtener user_id del usuario autenticado
			list($driver, $user_id) = Auth::get_user_id();
			
			$log = Model_Activity_Log::forge(array(
				'user_id' => $user_id,
				'tenant_id' => $this->tenant_id,
				'module' => $module,
				'action' => $action,
				'data' => is_array($data) ? json_encode($data) : $data,
				'ip_address' => Input::real_ip(),
				'user_agent' => Input::user_agent(),
				'created_at' => time()
			));
			$log->save();
		} catch (Exception $e) {
			// Si falla el log, no interrumpir la operación
			\Log::error('Error logging activity: ' . $e->getMessage());
		}
	}
}
