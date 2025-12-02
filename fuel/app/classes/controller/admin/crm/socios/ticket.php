<?php

class Controller_Admin_Crm_Socios_Ticket extends Controller_Admin
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
		if(!Auth::member(100) && !Auth::member(50) && !Auth::member(25) && !Auth::member(20))
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
	public function action_index($search = '')
	{
		# INICIALIZAR VARIABLES
		$data     = array();
		$tickets  = array();
		$per_page = 50;

		# OBTENER ID DEL SOCIO AUTENTICADO
		$user_id = Auth::get('id');
		$partner = Model_Partner::query()->where('user_id', $user_id)->get_one();

		# CONSULTA BASE
		$query = Model_Partners_Ticket::query()
			->related('partner')
			->related('asiguser') // por si después ocupas el nombre del usuario asignado
			->order_by('created_at', 'desc')
			->where('deleted', 0);

		# FILTRAR POR BÚSQUEDA
		if ($search != '')
		{
			$original_search = $search;
			$search = str_replace('+', ' ', rawurldecode($search));
			$search = str_replace(' ', '%', $search);

			$query->where_open()
				->where('subject', 'like', "%{$search}%")
				->or_where('message', 'like', "%{$search}%")
				->where_close();
		}

		# CONFIGURACIÓN DE PAGINACIÓN
		$config = array(
			'name'           => 'tickets',
			'pagination_url' => Uri::current(),
			'total_items'    => $query->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
			'show_first'     => true,
			'show_last'      => true,
		);

		$pagination = Pagination::forge('tickets', $config);

		# OBTENER TICKETS CON LÍMITE
		$results = $query
			->rows_limit($pagination->per_page)
			->rows_offset($pagination->offset)
			->get();

		# PROCESAR RESULTADOS
		if (!empty($results))
		{
			foreach ($results as $ticket)
			{

				# OBTENER PARTNER A PARTIR DEL USER_ID GUARDADO EN partner_id
				$partner_model = Model_Partner::query()
					->where('user_id', $ticket->partner_id)
					->get_one();

				# NOMBRE DEL SOCIO O 'Desconocido'
				$partner_name = $partner_model ? $partner_model->name : 'Desconocido';

				$tickets[] = array(
					'id'          => $ticket->id,
					'partner_id'  => $partner_name,
					'subject'     => Str::truncate($ticket->subject, 60),
					'message'     => Str::truncate($ticket->message, 60),
					'status'      => $ticket->status ?? 'N/A',
					'asig_user_id'=> $ticket->asiguser->name ?? 'Sin asignar',
					'created_at'  => !empty($ticket->created_at) ? date('d/m/Y H:i', $ticket->created_at) : '',
				);
			}
		}

		# ENVIAR A LA VISTA
		$data['tickets']    = $tickets;
		$data['pagination'] = $pagination->render();
		$data['search']     = str_replace('%', ' ', $search);

		# CARGAR LA VISTA
		$this->template->title   = 'Tickets Socios de Negocios';
		$this->template->content = View::forge('admin/crm/ticket/socios/index', $data, false);
	}

	/**
	* BUSCAR
	*
	* REDIRECCIONA A LA URL DE BUSCAR REGISTROS
	*
	* @access  public
	* @return  Void
	*/
	public function action_buscar()
	{
		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE OBTIENEN LOS VALORES
			$data = array(
				'search' => ($_POST['search'] != '') ? $_POST['search'] : '',
			);

			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('search');
			$val->add_callable('Rules');
			$val->add_field('search', 'search', 'max_length[100]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run($data))
			{
				# SE REMPLAZAN ALGUNOS CARACTERES
				$search = str_replace(' ', '+', $val->validated('search'));
				$search = str_replace('*', '', $search);

				# SE ALMACENA LA CADENA DE BUSQUEDA
				$search = ($val->validated('search') != '') ? $search : '';

				# SE REDIRECCIONA A BUSCAR
				Response::redirect('admin/crm/ticket/socios/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/crm/ticket/socios');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/crm/ticket/socios');
		}
	}


	/**
	 * INFO
	 *
	 * INFO DEL TICKET Y CAMBIO DEL MISMO, NO SE AGREGA EDITAR POR QUE ESTE MODULO NO APLICARA MAS INFORMACION
	 *
	 * @param int $ticket_id
	 * @return void
	 */
	public function action_info($ticket_id = 0)
	{
		# VALIDACIÓN DEL ID
		if ($ticket_id == 0 || !is_numeric($ticket_id)) {
			Session::set_flash('error', 'ID inválido.');
			Response::redirect('admin/crm/socios/ticket');
		}

		# OBTENER EL TICKET CON RELACIONES
		$ticket = Model_Partners_Ticket::query()
			->related('partner')
			->where('id', $ticket_id)
			->where('deleted', 0)
			->get_one();

		
		if (!$ticket) {
			Session::set_flash('error', 'No se encontró el ticket solicitado.');
			Response::redirect('admin/crm/socios/ticket');
		}

		# PROCESAR FORMULARIO UNIFICADO (cambiar estatus y/o responder)
		if (Input::method() == 'POST' && Input::post('enviar_respuesta_estatus')) {
			$message_text = trim(Input::post('message'));
			$new_status   = Input::post('estatus');

			$has_message       = $message_text !== '';
			$has_status_change = in_array($new_status, [0, 1, 2, 3]) && $ticket->status != $new_status;

			# No hay cambios
			if (!$has_message && !$has_status_change) {
				Session::set_flash('info', 'No se detectaron cambios para guardar.');
				Response::redirect('admin/crm/socios/ticket/info/' . $ticket->id);
			}

			# Guardar mensaje
			if ($has_message) {
				Model_Partners_Tickets_Message::forge([
					'ticket_id'  => $ticket->id,
					'message'    => $message_text,
					'sender_id'  => Auth::get('id'),
					'created_at' => time(),
				])->save();
			}

			$ticket->updated_at = time();
			$ticket->asig_user_id = Auth::get('id');
			$ticket->save();

			# Cambiar estatus
			if ($has_status_change) {
				$ticket->status = $new_status;
				$ticket->save();
			}

			# Mensajes de éxito según acciones
			if ($has_message && $has_status_change) {
				Session::set_flash('success', 'Mensaje y estatus actualizados correctamente.');
			} elseif ($has_message) {
				Session::set_flash('success', 'Tu respuesta ha sido registrada.');
			} elseif ($has_status_change) {
				Session::set_flash('success', 'El estatus del ticket se actualizó correctamente.');
			}

			Response::redirect('admin/crm/socios/ticket/info/' . $ticket->id);
		}

		# OBTENER MENSAJES
		$messages = Model_Partners_Tickets_Message::query()
			->where('ticket_id', $ticket->id)
			->order_by('created_at', 'asc')
			->get();

		# PROCESAR MENSAJES PARA LA VISTA
		$conversation = [];
		foreach ($messages as $msg) {
			$user  = Model_User::find($msg->sender_id);
			$group = $user->group ?? 0;

			$is_admin = in_array($group, [20, 25, 50, 100]);
			$conversation[] = [
				'message'      => $msg->message,
				'created_at'   => $msg->created_at,
				'sender_label' => $is_admin ? 'Administrador' : 'Socio',
				'align_class'  => $is_admin ? 'justify-content-end' : 'justify-content-start',
				'bg_class'     => $is_admin ? 'bg-primary text-white' : 'bg-light border',
			];
		}

		# ENVIAR DATOS A LA VISTA
		$data = [
			'ticket'         => $ticket,
			'messages'       => $conversation,
			'current_status' => $ticket->status,
		];

		# CARGAR LA VISTA
		$this->template->title   = 'Detalle del Ticket';
		$this->template->content = View::forge('admin/crm/ticket/socios/info', $data, false);
	}







}