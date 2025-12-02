<?php

/**
* CONTROLADOR ADMIN_LOGS
*
* @package  app
* @extends  Controller_Admin
*/
class Controller_Admin_Logs extends Controller_Admin
{
	/**
	* BEFORE
	*
	* @return Void
	*/
	public function before()
	{
		# REQUERIDA PARA EL TEMPLATING
		parent::before();

		# SI EL USUARIO NO TIENE PERMISOS
		if(!Auth::member(100) && !Auth::member(50))
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			Session::set_flash('error', 'No tienes los permisos para acceder a esta sección.');

			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin');
		}
	}


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
		$data      = array();
		$logs_info = array();
		$per_page  = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$logs = Model_Providers_Log::query();

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $logs->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
			'show_first'     => true,
    		'show_last'      => true,
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('logs', $config);

		# SE EJECUTA EL QUERY
		$logs = $logs->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($logs))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($logs as $log)
			{
				# SE ALMACENA LA INFORMACION
				$logs_info[] = array(
					'id'          => $log->id,
					'provider'    => $log->provider,
					'type'        => $log->type,
					'transaction' => $log->transaction,
					'date'        => date('d/m/Y - H:i', $log->created_at)
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['logs']       = $logs_info;
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Logs';
		$this->template->content = View::forge('admin/logs/index', $data, false);
	}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($log_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($log_id == 0 || !is_numeric($log_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/logs');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$log = Model_Providers_Log::query()
		->where('id', $log_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($log))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['provider']    = $log->provider;
			$data['type']        = $log->type;
			$data['transaction'] = $log->transaction;
			$data['date']        = date('d/m/Y - H:i', $log->created_at);
			$data['response']    = unserialize($log->response);
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/logs');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $log_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del log';
		$this->template->content = View::forge('admin/logs/info', $data);
	}
}
