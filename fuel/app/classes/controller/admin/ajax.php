<?php

/**
* The Ajax Controller.
*
* A basic controller example.  Has examples of how to set the
* response body and status.
*
* @package  app
* @extends  Controller
*/
class Controller_Admin_Ajax extends Controller_Rest
{

	/**
	* SYNC_COTIZACIONES
	*
	* @access  public
	* @return  Object
	*/
	public function action_sync_cotizaciones()
	{
		// SOLO PERMITE AJAX
		if (!Input::is_ajax()) {
			return $this->response(['success' => false, 'message' => 'Acceso no permitido.'], 403);
		}

		$cotizaciones = Input::post('cotizaciones');
		if (empty($cotizaciones) || !is_array($cotizaciones)) {
			return $this->response(['success' => false, 'message' => 'Sin datos para sincronizar.'], 400);
		}

		$guardadas = 0;
		$errores = [];
		foreach ($cotizaciones as $coti) {
			try {
				// ADAPTA ESTO A TU PROCESO NORMAL DE GUARDADO DE COTIZACION:
				// (Ejemplo: Model_Quote::guardarOffline($coti); )
				// Puedes usar el mismo proceso que tu action_finalizar_cotizacion

				$model = new Model_Quote();
				$model->partner_id        = $coti['partner_id'] ?? null;
				$model->products          = $coti['products'] ?? null;
				$model->comments          = $coti['comments'] ?? '';
				$model->payment_id        = $coti['payment_id'] ?? null;
				$model->address_id        = $coti['address_id'] ?? null;
				$model->seller_asig_id    = $coti['seller_asig_id'] ?? null;
				$model->partner_contact_id= $coti['partner_contact_id'] ?? null;
				$model->reference         = $coti['reference'] ?? null;
				$model->valid_date        = $coti['valid_date'] ?? null;
				$model->created_at        = date('Y-m-d H:i:s');
				$model->updated_at        = date('Y-m-d H:i:s');
				// ... cualquier campo más ...
				if ($model->save()) {
					$guardadas++;
				} else {
					$errores[] = $coti;
				}
			} catch (Exception $e) {
				$errores[] = $coti;
			}
		}

		if (count($errores) === 0) {
			return $this->response(['success' => true, 'guardadas' => $guardadas]);
		} else {
			return $this->response([
				'success'  => false,
				'guardadas'=> $guardadas,
				'fallidas' => count($errores),
				'message'  => 'Algunas cotizaciones no se guardaron',
			]);
		}
	}

	/**
 * OBTIENE LAS NOTIFICACIONES NO LEÍDAS DEL USUARIO AUTENTICADO.
 * BUSCA EN LA TABLA notification_recipients TODAS LAS NOTIFICACIONES
 * DONDE status = 0 Y user_id = USUARIO LOGUEADO.
 * SI LA RELACIÓN ORM FUELPHP FALLA, RECUPERA LA NOTIFICACIÓN DIRECTAMENTE.
 * DEVUELVE UN ARRAY JSON DE NOTIFICACIONES LISTAS PARA MOSTRAR EN VUE.
 *
 * @return Response
 */
public function action_get_notifications()
{
    // ===========================
    // INICIALIZA RESPUESTA
    // ===========================
    $response = ['success' => false, 'notifications' => []];

    // ===========================
    // OBTIENE USUARIO AUTENTICADO
    // ===========================
    $current_user_id = Auth::get('id');

    if (!$current_user_id) {
        $response['message'] = 'Usuario no autenticado.';
        return $this->response($response);
    }

    try {
        // ===========================
        // CONSULTA DE RECIPIENTS NO LEÍDOS
        // ===========================
        $notification_recipients = Model_Notification_Recipient::query()
            ->where('user_id', $current_user_id)
            ->where('status', 0)
            ->order_by('created_at', 'desc')
            ->get();

        $formatted_notifications = [];

        foreach ($notification_recipients as $recipient) {
            // INTENTA CARGAR LA NOTIFICACIÓN VÍA RELACIÓN ORM
            $notification = $recipient->notification;

            // SI FALLA, CÁRGALA MANUALMENTE POR SU ID
            if (!$notification || !$notification->active) {
                $notification = Model_Notification::find($recipient->notification_id);
            }

            // AGREGA NOTIFICACIÓN SOLO SI ESTÁ ACTIVA
            if ($notification && $notification->active) {
                $formatted_notifications[] = [
                    'id'         => $recipient->id,
                    'title'      => $notification->title,
                    'message'    => $notification->message,
                    'url'        => $notification->url,
                    'icon'       => $notification->icon,
                    'status'     => (string)$recipient->status, // SIEMPRE STRING
                    'created_at' => date('Y-m-d H:i:s', $recipient->created_at),
                ];
            }
        }

        $response['success'] = true;
        $response['notifications'] = $formatted_notifications;

    } catch (\Exception $e) {
        $response['message'] = 'Error al obtener notificaciones.';
    }

    return $this->response($response);
}




    /**
 * MARCA UNA NOTIFICACIÓN COMO LEÍDA POR EL USUARIO AUTENTICADO.
 * SOLO MODIFICA EL REGISTRO EN notification_recipients.
 * DEVUELVE ÉXITO O MENSAJE DE ERROR EN JSON.
 *
 * @return Response
 */
public function action_mark_notification_read()
{
    // ===========================
    // INICIALIZA RESPUESTA
    // ===========================
    $response = ['success' => false, 'message' => ''];

    // ===========================
    // OBTIENE DATOS DEL POST Y USUARIO
    // ===========================
    $recipient_id    = Input::post('id');
    $current_user_id = Auth::get('id');

    if (!$recipient_id) {
        $response['message'] = 'ID DE NOTIFICACIÓN DE DESTINATARIO NO PROPORCIONADO.';
        return $this->response($response);
    }
    if (!$current_user_id) {
        $response['message'] = 'USUARIO NO AUTENTICADO.';
        return $this->response($response);
    }

    try {
        // ===========================
        // BUSCA EL REGISTRO DEL DESTINATARIO (RECIPIENT)
        // ===========================
        $recipient = Model_Notification_Recipient::query()
            ->where('id', $recipient_id)
            ->where('user_id', $current_user_id)
            ->get_one();

        if ($recipient) {
            // ===========================
            // ACTUALIZA ESTATUS Y FECHAS
            // ===========================
            $recipient->status     = 1;              // MARCAR COMO LEÍDA
            $recipient->read_at    = time();         // UNIX TIMESTAMP
            $recipient->updated_at = time();

            if ($recipient->save()) {
                $response['success'] = true;
                $response['message'] = 'NOTIFICACIÓN MARCADA COMO LEÍDA.';
            } else {
                $response['message'] = 'ERROR AL GUARDAR EL ESTADO DE LA NOTIFICACIÓN.';
            }
        } else {
            $response['message'] = 'NOTIFICACIÓN DE DESTINATARIO NO ENCONTRADA O NO PERTENECE AL USUARIO ACTUAL.';
        }
    } catch (\Exception $e) {
        $response['message'] = 'ERROR INTERNO AL MARCAR NOTIFICACIÓN.';
    }

    return $this->response($response);
}





	/**
	* CKEDITOR_IMAGE
	*
	* @access  public
	* @return  Object
	*/
	public function post_ckeditor_image()
	{
		# SE INICIALIZAN LAS VARIABLES
		$response = array();

		# SE CREA LA VALIDACION DE LOS CAMPOS
		$val = Validation::forge('image');
		$val->add_callable('Rules');
		$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
		$val->add_field('access_token', 'access_token', 'min_length[1]');
		$val->add_field('file', 'file', 'min_length[1]');

		# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
		if($val->run())
		{
			# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
			$check_access = Model_User::query()
			->where('id', $val->validated('access_id'))
			->get_one();

			# SI SE OBTIENE INFORMACION
			if(!empty($check_access))
			{
				# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
				if(md5($check_access->login_hash) == $val->validated('access_token'))
				{
					# SE OBTIENE LA REFERENCIA DE LA IMAGEN
					$image = $_FILES['file']['name'];

					# SI EL USUARIO SUBE LA IMAGEN
					if(!empty($image))
					{
						# SE ESTABLECE LA CONFIGURACION
						$config = array(
							'auto_process'        => false,
							'path'                => DOCROOT.DS.'assets/uploads',
							'randomize'           => false,
							'auto_rename'         => true,
							'normalize'           => true,
							'normalize_separator' => '-',
							'ext_whitelist'       => array('jpg', 'jpeg', 'png', 'gif'),
							'max_size'            => 20971520,
						);

						# SE INICIALIZA EL PROCESO UPLOAD CON LA CONFIGURACION ESTABLECIDA
						Upload::process($config);

						# SI EL ARCHIVO ES VALIDO
						if(Upload::is_valid())
						{
							# SE SUBE EL ARCHIVO
							Upload::save();

							# SE OBTIENE LA INFORMACION DEL ARCHIVO
							$value = Upload::get_files();

							# SE ALMACENA EL NOMBRE DEL ARCHIVO
							$file = $value[0]['saved_as'];

							# SE AGREGA EL PATH UPLOADS
							Asset::add_path('assets/uploads/', 'uploads');

							# SE BUSCA LAS FOTOS EN EL SERVIDOR
							$file_uploaded = DOCROOT.Asset::find_file($file, 'uploads');

							# SE OBTIENE LA INFORMACION DE LAS MEDIDAS
							$size = Image::sizes($file_uploaded);

							# SE ESTABLECE EL MENSAJE DE EXITO
							$response = array(
								'url' => Uri::base(false).'assets/uploads/'.$file
							);

							# SI HAY UN ARCHIVO ANTERIOR
							if($val->validated('last_file') != '')
							{
								# SI EL ARCHIVO EXISTE
								if(file_exists(DOCROOT.'assets/uploads/'.$val->validated('last_file')))
								{
									# SE ELIMINAN EL ARCHIVO
									File::delete(DOCROOT.'assets/uploads/'.$val->validated('last_file'));
								}
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$response = array(
								'error' => array(
									'message' => 'Solo están permitidos las imágenes con extensión .jpg, .jpeg, .png y .gif.'
								)
							);
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$response = array(
							'error' => array(
								'message' => 'No se envió ninguna imagen.'
							)
						);
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$response = array(
						'error' => array(
							'message' => 'Las credenciales no permiten el acceso al servidor.'
						)
					);
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$response = array(
					'error' => array(
						'message' => 'Las credenciales no permiten el acceso al servidor.'
					)
				);
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$response = array(
				'error' => array(
					'message' => 'No es posible subir la imagen con la información enviada.'
				)
			);
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response($response);
	}


	/**
	* IMAGE
	*
	* @access  public
	* @return  Object
	*/
	public function post_image()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg  = '';
		$file = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('image');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('width', 'width', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('height', 'height', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('last_file', 'last_file', 'min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE OBTIENE LA REFERENCIA DE LA IMAGEN
						$image = $_FILES['file']['name'];

						# SI EL USUARIO SUBE LA IMAGEN
						if(!empty($image))
						{
							# SE ESTABLECE LA CONFIGURACION
							$config = array(
								'auto_process'  => false,
								'path'          => DOCROOT.DS.'assets/uploads',
								'randomize'     => false,
								'auto_rename'   => true,
								'normalize'     => true,
								'ext_whitelist' => array('jpg', 'jpeg', 'png', 'gif'),
								'max_size'      => 20971520,
								'prefix'        => 'sw_',
							);

							# SE INICIALIZA EL PROCESO UPLOAD CON LA CONFIGURACION ESTABLECIDA
							Upload::process($config);

							# SI EL ARCHIVO ES VALIDO
							if(Upload::is_valid())
							{
								# SE SUBE EL ARCHIVO
								Upload::save();

								# SE OBTIENE LA INFORMACION DEL ARCHIVO
								$value = Upload::get_files();

								# SE ALMACENA EL NOMBRE DEL ARCHIVO
								$file = $value[0]['saved_as'];

								# SE AGREGA EL PATH UPLOADS
								Asset::add_path('assets/uploads/', 'uploads');

								# SE BUSCA LAS FOTOS EN EL SERVIDOR
								$file_uploaded = DOCROOT.Asset::find_file($file, 'uploads');

								# SE OBTIENE LA INFORMACION DE LAS MEDIDAS
								$size = Image::sizes($file_uploaded);

								# SI LA IMAGEN CUMPLE CON LAS MEDIDAS
								if($size->width == $val->validated('width') && $size->height == $val->validated('height'))
								{
									# SE ESTABLECE EL MENSAJE DE EXITO
									$msg = 'ok';

									# SI HAY UN ARCHIVO ANTERIOR
									if($val->validated('last_file') != '')
									{
										# SI EL ARCHIVO EXISTE
										if(file_exists(DOCROOT.'assets/uploads/'.$val->validated('last_file')))
										{
											# SE ELIMINAN EL ARCHIVO
											File::delete(DOCROOT.'assets/uploads/'.$val->validated('last_file'));
										}
									}
								}
								else
								{
									# SI EL ARCHIVO RECIEN SUBIDO EXISTE
									if(file_exists(DOCROOT.'assets/uploads/'.$file))
									{
										# SE ELIMINAN EL ARCHIVO
										File::delete(DOCROOT.'assets/uploads/'.$file);
									}

									# SE LIMPIA LA VARIABLE
									$file = '';

									# SE ESTABLECE EL MENSAJE DE ERROR
									$msg = 'La imagen no tiene las medidas exactas ('.$val->validated('width').' X '.$val->validated('height').' px), por favor vuelve a subirla.';
								}
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'Solo están permitidos las imágenes con extensión .jpg, .jpeg, .png y .gif.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'No se envió ninguna imagen.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible subir la imagen con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg'  => $msg,
			'file' => $file
		));
	}


	/**
	* PRODUCT_IMAGE
	*
	* @access  public
	* @return  Object
	*/
	public function post_product_image()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg  = '';
		$file = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('image');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('width', 'width', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('height', 'height', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('last_file', 'last_file', 'min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE OBTIENE LA REFERENCIA DE LA IMAGEN
						$image = $_FILES['file']['name'];

						# SI EL USUARIO SUBE LA IMAGEN
						if(!empty($image))
						{
							# SE ESTABLECE LA CONFIGURACION
							$config = array(
								'auto_process'  => false,
								'path'          => DOCROOT.DS.'assets/uploads',
								'randomize'     => false,
								'auto_rename'   => true,
								'normalize'     => true,
								'ext_whitelist' => array('jpg', 'jpeg', 'png', 'gif'),
								'max_size'      => 20971520,
								'prefix'        => 'sw_',
							);

							# SE INICIALIZA EL PROCESO UPLOAD CON LA CONFIGURACION ESTABLECIDA
							Upload::process($config);

							# SI EL ARCHIVO ES VALIDO
							if(Upload::is_valid())
							{
								# SE SUBE EL ARCHIVO
								Upload::save();

								# SE OBTIENE LA INFORMACION DEL ARCHIVO
								$value = Upload::get_files();

								# SE ALMACENA EL NOMBRE DEL ARCHIVO
								$file = $value[0]['saved_as'];

								# SE AGREGA EL PATH UPLOADS
								Asset::add_path('assets/uploads/', 'uploads');

								# SE BUSCA LAS FOTOS EN EL SERVIDOR
								$file_uploaded = DOCROOT.Asset::find_file($file, 'uploads');

								# SE CREA CREA UNA INSTANCIA A LA CLASE IMAGE
								$thumb = Image::forge();

								# SE CARGA LA CONFIGURACION PREDETERMINADA Y SE CREA EL THUMBNAIL
								$thumb->load($file_uploaded)->preset('thumb_products')->save_pa('thumb_');

								# SE OBTIENE LA INFORMACION DE LAS MEDIDAS
								$size = Image::sizes($file_uploaded);

								# SI LA IMAGEN CUMPLE CON LAS MEDIDAS
								if($size->width == $val->validated('width') && $size->height == $val->validated('height'))
								{
									# SE ESTABLECE EL MENSAJE DE EXITO
									$msg = 'ok';

									# SI HAY UN ARCHIVO ANTERIOR
									if($val->validated('last_file') != '')
									{
										# SI EL ARCHIVO EXISTE
										if(file_exists(DOCROOT.'assets/uploads/'.$val->validated('last_file')))
										{
											# SE ELIMINAN EL ARCHIVO
											File::delete(DOCROOT.'assets/uploads/'.$val->validated('last_file'));
										}

										# SI EL ARCHIVO EXISTE
										if(file_exists(DOCROOT.'assets/uploads/thumb_'.$val->validated('last_file')))
										{
											# SE ELIMINAN EL ARCHIVO
											File::delete(DOCROOT.'assets/uploads/thumb_'.$val->validated('last_file'));
										}
									}
								}
								else
								{
									# SI EL ARCHIVO RECIEN SUBIDO EXISTE
									if(file_exists(DOCROOT.'assets/uploads/'.$file))
									{
										# SE ELIMINAN EL ARCHIVO
										File::delete(DOCROOT.'assets/uploads/'.$file);
									}

									# SI EL ARCHIVO RECIEN SUBIDO EXISTE
									if(file_exists(DOCROOT.'assets/uploads/thumb_'.$file))
									{
										# SE ELIMINAN EL ARCHIVO
										File::delete(DOCROOT.'assets/uploads/thumb_'.$file);
									}

									# SE LIMPIA LA VARIABLE
									$file = '';

									# SE ESTABLECE EL MENSAJE DE ERROR
									$msg = 'La imagen no tiene las medidas exactas ('.$val->validated('width').' X '.$val->validated('height').' px), por favor vuelve a subirla.';
								}
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'Solo están permitidos las imágenes con extensión .jpg, .jpeg, .png y .gif.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'No se envió ninguna imagen.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible subir la imagen con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg'  => $msg,
			'file' => $file
		));
	}


	/**
	* POST_IMAGE
	*
	* @access  public
	* @return  Object
	*/
	public function post_post_image()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg  = '';
		$file = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('image');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('width', 'width', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('height', 'height', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('last_file', 'last_file', 'min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE OBTIENE LA REFERENCIA DE LA IMAGEN
						$image = $_FILES['file']['name'];

						# SI EL USUARIO SUBE LA IMAGEN
						if(!empty($image))
						{
							# SE ESTABLECE LA CONFIGURACION
							$config = array(
								'auto_process'        => false,
								'path'                => DOCROOT.DS.'assets/uploads',
								'randomize'           => false,
								'auto_rename'         => true,
								'normalize'           => true,
								'normalize_separator' => '-',
								'ext_whitelist'       => array('jpg', 'jpeg', 'png', 'gif'),
								'max_size'            => 20971520,
							);

							# SE INICIALIZA EL PROCESO UPLOAD CON LA CONFIGURACION ESTABLECIDA
							Upload::process($config);

							# SI EL ARCHIVO ES VALIDO
							if(Upload::is_valid())
							{
								# SE SUBE EL ARCHIVO
								Upload::save();

								# SE OBTIENE LA INFORMACION DEL ARCHIVO
								$value = Upload::get_files();

								# SE ALMACENA EL NOMBRE DEL ARCHIVO
								$file = $value[0]['saved_as'];

								# SE AGREGA EL PATH UPLOADS
								Asset::add_path('assets/uploads/', 'uploads');

								# SE BUSCA LAS FOTOS EN EL SERVIDOR
								$file_uploaded = DOCROOT.Asset::find_file($file, 'uploads');

								# SE CREA CREA UNA INSTANCIA A LA CLASE IMAGE
								$thumb = Image::forge();

								# SE CARGA LA CONFIGURACION PREDETERMINADA Y SE CREA EL THUMBNAIL
								$thumb->load($file_uploaded)->preset('mini_posts')->save_pa('mini-');

								# SE OBTIENE LA INFORMACION DE LAS MEDIDAS
								$size = Image::sizes($file_uploaded);

								# SI LA IMAGEN CUMPLE CON LAS MEDIDAS
								if($size->width == $val->validated('width') && $size->height == $val->validated('height'))
								{
									# SE ESTABLECE EL MENSAJE DE EXITO
									$msg = 'ok';

									# SI HAY UN ARCHIVO ANTERIOR
									if($val->validated('last_file') != '')
									{
										# SI EL ARCHIVO EXISTE
										if(file_exists(DOCROOT.'assets/uploads/'.$val->validated('last_file')))
										{
											# SE ELIMINAN EL ARCHIVO
											File::delete(DOCROOT.'assets/uploads/'.$val->validated('last_file'));
										}

										# SI EL ARCHIVO EXISTE
										if(file_exists(DOCROOT.'assets/uploads/mini-'.$val->validated('last_file')))
										{
											# SE ELIMINAN EL ARCHIVO
											File::delete(DOCROOT.'assets/uploads/mini-'.$val->validated('last_file'));
										}
									}
								}
								else
								{
									# SI EL ARCHIVO RECIEN SUBIDO EXISTE
									if(file_exists(DOCROOT.'assets/uploads/'.$file))
									{
										# SE ELIMINAN EL ARCHIVO
										File::delete(DOCROOT.'assets/uploads/'.$file);
									}

									# SI EL ARCHIVO RECIEN SUBIDO EXISTE
									if(file_exists(DOCROOT.'assets/uploads/mini-'.$file))
									{
										# SE ELIMINAN EL ARCHIVO
										File::delete(DOCROOT.'assets/uploads/mini-'.$file);
									}

									# SE LIMPIA LA VARIABLE
									$file = '';

									# SE ESTABLECE EL MENSAJE DE ERROR
									$msg = 'La imagen no tiene las medidas exactas ('.$val->validated('width').' X '.$val->validated('height').' px), por favor vuelve a subirla.';
								}
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'Solo están permitidos las imágenes con extensión .jpg, .jpeg, .png y .gif.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'No se envió ninguna imagen.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible subir la imagen con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg'  => $msg,
			'file' => $file
		));
	}


	/**
	* ORDER_TABLE
	*
	* @access  public
	* @return  Object
	*/
	public function post_order_table()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('order');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('id', 'id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('order', 'order', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$slide = Model_Slide::query()
						->where('id', $val->validated('id'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($slide))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$slide->order = $val->validated('order');

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($slide->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se puede actualizar el orden, por favor intentalo más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El slide enviado no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible ordenar los slides con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////// PRODUCTOS /////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	* ORDER_TABLE_PRODUCT_IMAGES
	*
	* @access  public
	* @return  Object
	*/
	public function post_order_table_product_images()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('order');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('id', 'id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('order', 'order', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$image = Model_Products_Image::query()
						->where('id', $val->validated('id'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($image))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$image->order = $val->validated('order');

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($image->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se puede actualizar el orden, por favor intentalo más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'La imagen enviada no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible ordenar las imágenes con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}


	/**
	* PRODUCT_STATUS
	*
	* @access  public
	* @return  Object
	*/
	public function post_product_status()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('product_status');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('product', 'producto', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('value', 'valor', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$product = Model_Product::query()
						->where('id', $val->validated('product'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($product))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$product->status = ($val->validated('value') == 0) ? 0 : 1;

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($product->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se pudo cambiar el estatus del producto, por favor intenta más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El producto enviado no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible modificar el valor en base de datos con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}


	/**
	* PRODUCT_STATUS_INDEX
	*
	* @access  public
	* @return  Object
	*/
	public function post_product_status_index()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('product_status_index');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('product', 'producto', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('value', 'valor', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$product = Model_Product::query()
						->where('id', $val->validated('product'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($product))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$product->status_index = ($val->validated('value') == 0) ? 0 : 1;

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($product->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se pudo cambiar el estatus del producto, por favor intenta más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El producto enviado no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible modificar el valor en base de datos con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}

		/**
	* PRODUCTS SOON
	*
	* @access  public
	* @return  Object
	*/
	public function post_product_soon()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('product_soon');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('product', 'product', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('value', 'valor', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$product = Model_Product::query()
						->where('id', $val->validated('product'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($product))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$product->soon = ($val->validated('value') == 0) ? 0 : 1;

							if ($product->soon == 1) {
								// MUTUA EXCLUSIÓN
								$product->newproduct = 0;
								$product->temporarily_sold_out = 0;
								// LOGS
								\Log::debug('[PRODUCT BADGE] SOON=1 => NEW=0, OUT=0 PARA PRODUCT ID='.$product->id);
							}

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($product->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se pudo cambiar el estatus del producto, por favor intenta más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El producto enviado no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible modificar el valor en base de datos con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}
	
	/**
	* PRODUCTS NEW
	*
	* @access  public
	* @return  Object
	*/
	public function post_product_new()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('product_new');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('product', 'product', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('value', 'valor', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$product = Model_Product::query()
						->where('id', $val->validated('product'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($product))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$product->newproduct = ($val->validated('value') == 0) ? 0 : 1;

							if ($product->newproduct == 1) {
								$product->soon = 0;
								$product->temporarily_sold_out = 0;
								\Log::debug('[PRODUCT BADGE] NEW=1 => SOON=0, OUT=0 PARA PRODUCT ID='.$product->id);
							}


							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($product->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se pudo cambiar el estatus del producto, por favor intenta más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El producto enviado no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible modificar el valor en base de datos con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}
	
	/**
	* PRODUCTS NEW
	*
	* @access  public
	* @return  Object
	*/
	public function post_product_out()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('product_out');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('product', 'product', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('value', 'valor', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$product = Model_Product::query()
						->where('id', $val->validated('product'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($product))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$product->temporarily_sold_out = ($val->validated('value') == 0) ? 0 : 1;
							
							if ($product->temporarily_sold_out == 1) {
								$product->soon = 0;
								$product->newproduct = 0;
								\Log::debug('[PRODUCT BADGE] OUT=1 => SOON=0, NEW=0 PARA PRODUCT ID='.$product->id);
							}


							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($product->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se pudo cambiar el estatus del producto, por favor intenta más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El producto enviado no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible modificar el valor en base de datos con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////// TERMINA PRODUCTOS /////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////// TICKETS ////////////////////////////////////////////////////////////////	
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	* INCIDENTES
	*
	* @access  public
	* @return  Object
	*/
	public function post_incidentes()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg           = '';
		$incident_opts = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('incident');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('type_id', 'type_id', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE ESTABLECE LA OPCION POR DEFAULT
						$incident_opts = '<option selected="selected" value="none">Selecciona una opción</option>';

						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$incidents = Model_Tickets_Incident::query()
						->where('type_id', $val->validated('type_id'))
						->order_by('name', 'asc')
						->get();

						# SI SE OBTIENE INFORMACION
						if(!empty($incidents))
						{
							# SE RECORRE ELEMENTO POR ELEMENTO
							foreach($incidents as $incident)
							{
								# SE ALMACENA LA OPCION
								$incident_opts .= '<option value="'.$incident->id.'">'.$incident->name.'</option>';
							}

							# SE ESTABLECE EL MENSAJE DE EXITO
							$msg = 'ok';
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El tipo de ticket no tiene incidencias relacionadas.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible obtener los incidentes con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg'  => $msg,
			'data' => $incident_opts
		));

	}

	/**
	* ESTATUS
	*
	* @access  public
	* @return  Object
	*/
	public function post_status()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg           = '';
		$statusticket_opts = '';


		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('status');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('asig_user_id', 'asig_user_id', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE ESTABLECE LA OPCION POR DEFAULT
						$statusticket_opts = '<option selected="selected" value="none">Selecciona una opción</option>';

						#creo la variable
						$asig_user_id = $val->validated('asig_user_id');

						#consulto los tickets
						$tickets = Model_Ticket::query()
						->where('asig_user_id', $asig_user_id)
						->get();

						#obtengo todos los tickets
						$status_ids = [];

						#recorro para obtener los id
						foreach ($tickets as $ticket) {
							$status_ids[] = $ticket->status_id;
						}

						// Elimina duplicados, si es necesario
						$status_ids = array_unique($status_ids);

						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$status = Model_Tickets_Status::query()
						->where('id','IN', $status_ids)
						->order_by('name', 'asc')
						->get();

						# SI SE OBTIENE INFORMACION
						if(!empty($status))
						{
							# SE RECORRE ELEMENTO POR ELEMENTO
							foreach($status as $statu)
							{
								# SE ALMACENA LA OPCION
								$statusticket_opts .= '<option value="'.$statu->id.'">'.$statu->name.'</option>';
							}

							# SE ESTABLECE EL MENSAJE DE EXITO
							$msg = 'ok';
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El usuario no tiene tickets relacionados.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible obtener los tickets de este usuario o no cuentacon ellos.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg'  => $msg,
			'data' => $statusticket_opts
		));

	}

	/**
	* ESTATUS
	*
	* @access  public
	* @return  Object
	*/
	public function post_result()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg           = '';
		$tickets_info = '';
		// Registro de depuración

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('status');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('status_id', 'status_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('asig_user_id', 'asig_user_id', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{

						// Obtiene los valores de asig_user_id y status_id
						$asig_user_id = $val->validated('asig_user_id');
						$status_id = $val->validated('status_id');

						// Realiza la consulta a model_tickets utilizando asig_user_id y status_id
						$tickets = Model_Tickets::query()
						->where('asig_user_id', $asig_user_id)
						->where('status_id', $status_id)
						->get();


						# SI SE OBTIENE INFORMACION
						if(!empty($tickets))
						{
							# SE RECORRE ELEMENTO POR ELEMENTO
							foreach($tickets as $ticket)
							{
								# SE ALMACENA LA INFORMACION
								$tickets_info[] = array(
									'id'            => $ticket->id,
									'type_id'       => $ticket->typeticket->name,
									'incident_id'   => $ticket->incidentticket->name,
									'description'   => $ticket->description,
									'status_id'     => $ticket->statusticket->name,
									'priority_id'   => $ticket->priorityticket->name,
									'department_id' => $ticket->employee->department->name,
									'employee_id'   => $ticket->employee->name,
									'user_id'       => $ticket->user->username,
									'asig_user_id'  => $asiguser,
									'created_at' 	=> date('d/m/Y - H:i', $ticket->created_at),
									'updated_at' 	=>  $ticket->updated_at
								);
							}

							# SE ESTABLECE EL MENSAJE DE EXITO
							$msg = 'ok';
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El usuario no tienen tickets.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible obtener los incidentes con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}
		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg'  => $msg,
			'data' => $tickets_info
		));
	}

	/**
	* ASIGNAR_TICKET
	*
	* @access  public
	* @return  Object
	*/
	public function post_asig_ticket()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('status');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('ticket', 'ticket', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('asig_user', 'asig_user', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$ticket = Model_Ticket::query()
						->where('id', $val->validated('ticket'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($ticket))
						{
							#FECHA DE CIERRE
							$currentDate = time();

							# SE ESTEBLECE LA NUEVA INFORMACION
							$ticket->asig_user_id = $val->validated('asig_user');
							$ticket->updated_at   = $currentDate;

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($ticket->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El ticket no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible editar el ticket con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}



	/**
	* CLOSE_TICKET
	*
	* @access  public
	* @return  Object
	*/
	public function post_close_ticket()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('status');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('ticket', 'ticket', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('solution', 'Solución detallada', 'required|min_length[1]');
			$val->add_field('asig_user', 'asig_user', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$ticket = Model_Ticket::query()
						->where('id', $val->validated('ticket'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($ticket))
						{
							#FECHA DE CIERRE
							$currentDate = time();

							# SE ESTEBLECE LA NUEVA INFORMACION
							$ticket->solution  = $val->validated('solution');
							$ticket->asig_user_id = $val->validated('asig_user');
							$ticket->updated_at   = $currentDate;

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($ticket->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El ticket no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible editar el ticket con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}

	/**
	* CLOSE_TICKET_INDEX
	*
	* @access  public
	* @return  Object
	*/
	public function post_ticket_clos()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('status');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('ticket', 'ticket', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('solution', 'Solución detallada', 'required|min_length[1]');
			$val->add_field('asig_user', 'asig_user', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$ticket = Model_Ticket::query()
						->where('id', $val->validated('ticket'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($ticket))
						{
							#FECHA DE CIERRE
							$currentDate = time();

							# SE ESTEBLECE LA NUEVA INFORMACION
							$ticket->solution  = $val->validated('solution');
							$ticket->asig_user_id = $val->validated('asig_user');
							$ticket->updated_at   = $currentDate;

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($ticket->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El ticket no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible editar el ticket con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}


	/**
	* CLOSE_TASK
	*
	* @access  public
	* @return  Object
	*/
	public function post_close_task()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg 	= '';
		$date   = date('d/m/Y');


		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('task');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('task', 'task', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('comments', 'comments', 'required|min_length[1]');
			$val->add_field('employee_id', 'employee_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('finish_at', 'finish_at', 'required', 'date');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$task = Model_Task::query()
						->where('id', $val->validated('task'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($task))
						{
							#FECHA DE CIERRE
							$currentDate = time();

							# SE ESTEBLECE LA NUEVA INFORMACION
							$task->comments    = $val->validated('comments');
							$task->employee_id = $val->validated('employee_id');
							$task->updated_at  = $currentDate;
							$task->finish_at   = $this->date2unixtime($val->validated('finish_at'));

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($task->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'La tarea no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible editar la tarea con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////// TERMINA TICKETS ////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



	/**
	* ORDER_TABLE_BANNERS
	*
	* @access  public
	* @return  Object
	*/
	public function post_order_table_banners()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('order');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('id', 'id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('order', 'order', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$banner = Model_Banner::query()
						->where('id', $val->validated('id'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($banner))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$banner->order = $val->validated('order');

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($banner->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se puede actualizar el orden, por favor intentalo más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El banner enviado no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible ordenar los slides con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}


	/**
	* ORDER_TABLE_BANNERS_LATERALES
	*
	* @access  public
	* @return  Object
	*/
	public function post_order_table_banners_laterales()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('order');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('id', 'id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('order', 'order', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$banner = Model_Banners_Side::query()
						->where('id', $val->validated('id'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($banner))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$banner->order = $val->validated('order');

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($banner->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se puede actualizar el orden, por favor intentalo más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El banner enviado no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible ordenar los banners con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}


	/**
	* BANNER_SIDES_STATUS
	*
	* @access  public
	* @return  Object
	*/
	public function post_banner_status()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('banner_status');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('banner', 'banner', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('value', 'valor', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$banner = Model_Banners_Side::query()
						->where('id', $val->validated('banner'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($banner))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$banner->status = ($val->validated('value') == 0) ? 0 : 1;

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($banner->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se pudo cambiar el estatus del banner, por favor intenta más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El banner enviado no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible modificar el valor en base de datos con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}


	/**
	* BANNER_STATUS
	*
	* @access  public
	* @return  Object
	*/
	public function post_banne_status()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('banne_status');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('banne', 'banne', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('value', 'valor', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$banne = Model_Banner::query()
						->where('id', $val->validated('banne'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($banne))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$banne->status = ($val->validated('value') == 0) ? 0 : 1;

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($banne->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se pudo cambiar el estatus del banner, por favor intenta más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El banner enviado no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible modificar el valor en base de datos con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}

	/**
	* SLIDER_STATUS
	*
	* @access  public
	* @return  Object
	*/
	public function post_slide_status()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('slide_status');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('slide', 'slide', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('value', 'valor', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$slide = Model_Slide::query()
						->where('id', $val->validated('slide'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($slide))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$slide->status = ($val->validated('value') == 0) ? 0 : 1;

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($slide->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se pudo cambiar el estatus del slider, por favor intenta más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El slider enviado no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible modificar el valor en base de datos con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}

	/**
	* BRANDS_STATUS
	*
	* @access  public
	* @return  Object
	*/
	public function post_brand_status()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('brand_status');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('brand', 'brand', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('value', 'valor', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$brand = Model_Brand::query()
						->where('id', $val->validated('brand'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($brand))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$brand->status = ($val->validated('value') == 0) ? 0 : 1;

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($brand->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se pudo cambiar el estatus de la marca, por favor intenta más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'La Marca enviada no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible modificar el valor en base de datos con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}



	/**
	* 	CATEGORY_STATUS
	*
	* @access  public
	* @return  Object
	*/
	public function post_category_status()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('category_status');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('category', 'category', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('value', 'valor', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$category = Model_Category::query()
						->where('id', $val->validated('category'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($category))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$category->status = ($val->validated('value') == 0) ? 0 : 1;

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($category->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se pudo cambiar el estatus de la marca, por favor intenta más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'La Marca enviada no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible modificar el valor en base de datos con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}



	/**
	* 	SUBCATEGORY_STATUS
	*
	* @access  public
	* @return  Object
	*/
	public function post_subcategory_status()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('category_status');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('category', 'category', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('value', 'valor', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$category = Model_Subcategory::query()
						->where('id', $val->validated('category'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($category))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$category->status = ($val->validated('value') == 0) ? 0 : 1;

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($category->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se pudo cambiar el estatus de la marca, por favor intenta más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'La Marca enviada no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible modificar el valor en base de datos con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}

	/**
	* GET_TICKET_OPTS
	*
	* @access  public
	* @return  Object
	*/
	public function post_get_ticket_opts()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg  = '';
		$opts = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('get_ticket_opts');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('ticket', 'ticket', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$ticket = Model_Ticket::query()
						->where('id', $val->validated('ticket'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($ticket))
						{
							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$employees = Model_Employee::query()
							->get();

							# SI SE OBTIENE INFORMACION
							if(!empty($employees))
							{
								# SE RECORRE ELEMENTO POR ELEMENTO
								foreach($employees as $employee)
								{
									# SE ALMACENA EL SELECTED
									if($ticket->asig_user_id == $employee->id)
									{
										# SE ALMACENA LA INFORMACION
										$opts .= "<option value='$employee->id' selected>".$employee->name." ".$employee->last_name."</option>";
									}
									else
									{
										# SE ALMACENA LA INFORMACION
										$opts .= "<option value='$employee->id'>".$employee->name." ".$employee->last_name."</option>";
									}
								}
							}

							# SE ESTABLECE EL MENSAJE DE EXITO
							$msg = 'ok';
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El ticket enviado no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible obtener el select con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg'  => $msg,
			'opts' => $opts
		));
	}

	/**
	* GET_TICKET_ASIG
	*
	* @access  public
	* @return  Object
	*/
	public function post_get_ticket_asig()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg  = '';
		$opts = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('get_ticket_asig');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('ticket', 'ticket', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$ticket = Model_Ticket::query()
						->where('id', $val->validated('ticket'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($ticket))
						{
							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$employees = Model_Employee::query()
							->where('department_id','=',1)
							->get();

							# SI SE OBTIENE INFORMACION
							if(!empty($employees))
							{
								# SE RECORRE ELEMENTO POR ELEMENTO
								foreach($employees as $employee)
								{
									# SE ALMACENA EL SELECTED
									if($ticket->asig_user_id == $employee->id)
									{
										# SE ALMACENA LA INFORMACION
										$opts .= "<option value='$employee->id' selected>".$employee->name." ".$employee->last_name."</option>";
									}
									else
									{
										# SE ALMACENA LA INFORMACION
										$opts .= "<option value='$employee->id'>".$employee->name." ".$employee->last_name."</option>";
									}
								}
							}

							# SE ESTABLECE EL MENSAJE DE EXITO
							$msg = 'ok';
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El ticket enviado no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible obtener el select con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg'  => $msg,
			'opts' => $opts
		));
	}

	/**
	* GET_TICKET_CLOS
	*
	* @access  public
	* @return  Object
	*/
	public function post_get_ticket_clos()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg  = '';
		$opts = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('get_ticket_clos');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('ticket', 'ticket', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$ticket = Model_Ticket::query()
						->where('id', $val->validated('ticket'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($ticket))
						{
							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$employees = Model_Employee::query()
							->where('department_id','=',1)
							->get();

							# SI SE OBTIENE INFORMACION
							if(!empty($employees))
							{
								# SE RECORRE ELEMENTO POR ELEMENTO
								foreach($employees as $employee)
								{
									# SE ALMACENA EL SELECTED
									if($ticket->asig_user_id == $employee->id)
									{
										# SE ALMACENA LA INFORMACION
										$opts .= "<option value='$employee->id' selected>".$employee->name." ".$employee->last_name."</option>";
									}
									else
									{
										# SE ALMACENA LA INFORMACION
										$opts .= "<option value='$employee->id'>".$employee->name." ".$employee->last_name."</option>";
									}
								}
							}

							# SE ESTABLECE EL MENSAJE DE EXITO
							$msg = 'ok';
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El ticket enviado no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible obtener el select con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg'  => $msg,
			'opts' => $opts
		));
	}


	/**
	* GET_TASK_OPTS
	*
	* @access  public
	* @return  Object
	*/
	public function post_get_task_opts()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg  = '';
		$opts = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('get_task_opts');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('task', 'tarea', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$task = Model_Task::query()
						->where('id', $val->validated('task'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($task))
						{
							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$employees = Model_Employee::query()
							->get();

							# SI SE OBTIENE INFORMACION
							if(!empty($employees))
							{
								# SE RECORRE ELEMENTO POR ELEMENTO
								foreach($employees as $employee)
								{
									# SE ALMACENA EL SELECTED
									if($task->employee_id == $employee->id)
									{
										# SE ALMACENA LA INFORMACION
										$opts .= "<option value='$employee->id' selected>".$employee->name." ".$employee->last_name."</option>";
									}
									else
									{
										# SE ALMACENA LA INFORMACION
										$opts .= "<option value='$employee->id'>".$employee->name." ".$employee->last_name."</option>";
									}
								}
							}

							# SE ESTABLECE EL MENSAJE DE EXITO
							$msg = 'ok';
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'La tarea enviada no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible obtener el select con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg'  => $msg,
			'opts' => $opts
		));
	}




	/**
	 * GET_RESERVATIONS
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_get_reservations()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg         = '';
        $events_info = array();

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('reservations');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$reservations = Model_Reservation::query()
						->where('deleted', 0)
						->get();

						# SI SE OBTIENE INFORMACION
						if(!empty($reservations))
						{
							# SE RECORRE ELEMENTO POR ELEMENTO
							foreach($reservations as $reservation)
							{
								# SI EXISTE PROFILE_FIELDS
								if(isset($reservation->user->profile_fields))
								{
									# SE DESERIALIZAN LOS CAMPOS EXTRAS
									$status = unserialize($reservation->user->profile_fields);

									# SE ALMACENA EL NOMBRE DEL USUARIO
									$user_name = $status['full_name'];
								}
								else
								{
									# SE ESTABLECE EL NOMBRE DEL USUARIO
									$user_name = 'N/A';
								}

								# SE GENERAN LAS CADENAS CON LA FECHA
								$start = date('Y-m-d', $reservation->date_start).'T'.date('H:i', $reservation->date_start).':00';
								$end   = date('Y-m-d', $reservation->date_end).'T'.date('H:i', $reservation->date_end).':00';

								# SE ALMACENA LA INFORMACION
								$events_info[] = array(
									'id'          => $reservation->id,
									'title' 	  => $user_name . "\n" . Str::truncate($reservation->description, 25),
									'start'       => $start,
									'end'         => $end,
									'className'   => 'bg-blue',
									'description' => $reservation->description
								);
							}

							# SE ESTABLECE EL MENSAJE DE EXITO
							$msg = 'ok';
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE EXITO
							$msg = 'ok';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible obtener las reservaciones de la sala de juntas con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg'    => $msg,
			'events' => $events_info
		));
	}


	/**
	 * SET_RESERVATION
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_set_reservation()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg            = '';
		$title          = '';
		$reservation_id = 0;

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('reservation');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('date_start', 'hora inicio', 'required|isodate');
			$val->add_field('date_end', 'hora fin', 'required|isodate');
			$val->add_field('description', 'descripción', 'min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE OBTIENE LA FECHA DEL MOMENTO
		                $now = time();

						# SE OBTIENE EL UNIXTIME DE LAS FECHAS
						$date_start = $this->caldate2unixtime($val->validated('date_start'))+1;
						$date_end   = $this->caldate2unixtime($val->validated('date_end'));

		                # SI LA FECHA DE INICIO ES MAYOR QUE EL MOMENTO
		                if($date_start > $now)
		                {
		                    # SI LA HORA DE INICIO ES MENOR QUE LA HORA FINAL
		    				if($date_start < $date_end)
		    				{
		    					# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		    					$check_reservation = Model_Reservation::query()
		    					->and_where_open()
		    						->and_where_open()
		    							->where('date_start', '<=', $date_start)
		    							->where('date_end', '>=', $date_start)
		    						->and_where_close()
		    						->or_where_open()
		    							->where('date_start', '<=', $date_end)
		    							->where('date_end', '>=', $date_end)
		    						->or_where_close()
		    					->and_where_close()
		    					->where('deleted', 0)
		    					->get();

		    					# SI NO SE OBTIENE INFORMACION
		    					if(empty($check_reservation))
		    					{
		    						# SE CREA EL MODELO CON LA INFORMACION
		    						$reservation = new Model_Reservation(array(
		    							'user_id'      => Auth::get('id'),
		    							'date_start'   => $date_start-1,
		    							'date_end'     => $date_end,
		    							'description'  => $val->validated('description'),
		    							'deleted'      => 0
		    						));

		    						# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
		    						if($reservation->save())
		    						{
		    							# SE ESTABLECE EL ID DE LA RESERVACION
		    							$reservation_id = $reservation->id;

		    							# SE DESERIALIZAN LOS CAMPOS EXTRAS
		    							$status = unserialize($reservation->user->profile_fields);

		    							# SE ALMACENA EL NOMBRE DEL USUARIO
		    							$title = $status['full_name'];

		    							# SE ESTABLECE EL MENSAJE DE EXITO
		    							$msg = 'ok';
		    						}
		    						else
		    						{
		    							# SE ESTABLECE EL MENSAJE DE ERROR
		    							$msg = 'No se pudo guardar la reservación en la base de datos, por favor intenta más tarde.';
		    						}
		    					}
		    					else
		    					{
		    						# SE ESTABLECE EL MENSAJE DE ERROR
		    						$msg = 'No se puede reservar la sala de juntas porque ya existe un evento registrado a esa hora, por favor selecciona otra hora.';
		    					}
		    				}
		    				else
		    				{
		    					# SE ESTABLECE EL MENSAJE DE ERROR
		    					$msg = 'La hora de inicio no puede se mayor a la hora final de la reservación, por favor verifica la hora.';
		    				}
		                }
						else
		                {
		                    # SE ESTABLECE EL MENSAJE DE ERROR
		                    $msg = 'No se puede reservar la sala de juntas en una fecha menor a la fecha actual.';
		                }
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible reservar la sala de juntas con la información enviada. Por favor revisa el formulario.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg'   => $msg,
			'title' => $title,
			'id'    => $reservation_id
		));
	}


	/**
	 * UPDATE_RESERVATION
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_update_reservation()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('reservation');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('id', 'id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('description', 'descripción', 'min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$reservation = Model_Reservation::query()
						->where('id', $val->validated('id'))
						->where('user_id', Auth::get('id'))
						->where('deleted', 0)
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($reservation))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$reservation->description = $val->validated('description');

							# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
							if($reservation->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se pudo editar la reservación en la base de datos, por favor intenta más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'Solo el usuario que reservó la sala de juntas puede actualizar la reservación.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible actualizar la reservación de la sala de juntas con la información enviada. Por favor revisa el formulario.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}


	/**
	 * DELETE_RESERVATION
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_delete_reservation()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('reservation');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('id', 'id', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$reservation = Model_Reservation::query()
						->where('id', $val->validated('id'))
						->where('user_id', Auth::get('id'))
						->where('deleted', 0)
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($reservation))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$reservation->deleted = 1;

							# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
							if($reservation->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se pudo eliminar la reservación en la base de datos, por favor intenta más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'Solo el usuario que reservó la sala de juntas puede eliminar la reservación.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible eliminar el registro de la sala de juntas con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}


	/**
	 * AGREGA ACTIVIDADES
	 *
	 * @access  private
	 * @return  Int
	 */
    public function post_add_activity()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg  = '';
		$data = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('activity');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('act_num', 'act_num', 'required|min_length[1]|max_length[255]');
			$val->add_field('global_date', 'global_date', 'required|date');
			$val->add_field('customer', 'customer', 'required|min_length[1]|max_length[255]');
			$val->add_field('company', 'company', 'required|min_length[1]|max_length[255]');
			$val->add_field('total', 'total', 'required|float');
			$val->add_field('contact_id', 'contact_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('hour', 'hour', 'required|min_length[1]|max_length[255]');
			$val->add_field('invoice', 'invoice', 'required|valid_string[numeric]|numeric_min[0]');
			$val->add_field('foreing', 'foreing', 'required|valid_string[numeric]|numeric_min[0]');
			$val->add_field('time_id', 'time_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('type_id', 'type_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('status_id', 'status_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('category_id', 'category_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('comments', 'comments', 'min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE OBTIENE EL ID DEL USUARIO
						$user_id = Auth::get('id');

						# SE BUSCA LA INFORMACION DEL EMPLEADO
						$employee = Model_Employee::query()->where('user_id', $user_id)->get_one();

						# SI EXISTE INFORMACION
						if($employee)
						{
							# SE BUSCA LA INFORMACION
							$type     = Model_Activitys_Type::find($val->validated('type_id'));
							$contact  = Model_Activitys_Methods_Contact::find($val->validated('contact_id'));
							$time     = Model_Activitys_Time::find($val->validated('time_id'));
							$category = Model_Category::find($val->validated('category_id'));
							$status   = Model_Activitys_Status::find($val->validated('status_id'));

							# SI NO EXISTE UN PROBLEMA CON LAS OPCIONES SELECCIONADAS
							if(!empty($type) && !empty($contact) && !empty($time) && !empty($category) && !empty($status))
							{
								# SE BUSCA INFORMACION A TRAVES DEL MODELO
								$activity_num_check = Model_Activitys_Num::query()
								->where('act_num', $val->validated('act_num'))
								->get_one();

								# SI NO SE OBTIENE INFORMACION
								if(empty($activity_num_check))
								{
									# SE CREA EL MODELO CON LA INFORMACION
									$activity_num = new Model_Activitys_Num(array(
										'act_num'   => $val->validated('act_num'),
										'date'      => $this->date2unixtime2($val->validated('global_date')),
										'completed' => 0
									));

									# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
									$activity_num->save();
								}

								# SE ESTABLECE LA INFORMACION
								$activity = new Model_Activity(array(
									'act_num'     => $val->validated('act_num'),
									'type_id'     => $val->validated('type_id'),
									'customer'    => $val->validated('customer'),
									'company'     => $val->validated('company'),
									'contact_id'  => $val->validated('contact_id'),
									'hour'        => $val->validated('hour'),
									'category_id' => $val->validated('category_id'),
									'status_id'   => $val->validated('status_id'),
									'comments'    => $val->validated('comments'),
									'global_date' => $this->date2unixtime2($val->validated('global_date')),
									'total'       => $val->validated('total'),
									'user_id'     => $user_id,
									'employee_id' => $employee->id,
									'created_at'  => time(),
									'time_id'     => $val->validated('time_id'),
									'foreing'     => $val->validated('foreing'),
									'invoice'     => $val->validated('invoice')
								));

								# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
								if($activity->save())
								{
									# SE ALMACENA LA INFORMACION
									$data = array(
										'id'       => $activity->id,
										'customer' => $activity->customer,
										'invoice'  => ($activity->invoice == 0) ? 'No': 'Sí',
										'company'  => $activity->company,
										'foreing'  => ($activity->foreing == 0) ? 'No': 'Sí',
										'contact'  => $activity->contact->name,
										'hour'     => $activity->hour,
										'time'     => $activity->time->name,
										'type'     => $activity->type->name,
										'status'   => $activity->status->name,
										'category' => $activity->category->name,
										'comments' => $activity->comments,
										'total'    => $activity->total
									);

									# SE ESTABLECE EL MENSAJE DE EXITO
									$msg = 'ok';
								}
							}
							else
							{
								# SI ENTRANTE/SALIENTE ESTA VACIO
								if(empty($type))
								{
									# SE ESTABLECE EL MENSAJE DE ERROR
									$msg = 'El campo Entrante/Saliente es icorrecto.';
								}

								# SI MEDIO ESTA VACIO
								if(empty($contact))
								{
									# SE ESTABLECE EL MENSAJE DE ERROR
									$msg = 'El campo Medio es icorrecto.';
								}

								# SI DURACION DE LA LLAMADA ESTA VACIO
								if(empty($time))
								{
									# SE ESTABLECE EL MENSAJE DE ERROR
									$msg = 'El campo Duración de Llamada es icorrecto.';
								}

								# SI PRODUCTO ESTA VACIO
								if(empty($category))
								{
									# SE ESTABLECE EL MENSAJE DE ERROR
									$msg = 'El campo Producto de Interés es icorrecto.';
								}

								# SI SEGUIMIENTO ESTA VACIO
								if(empty($status))
								{
									# SE ESTABLECE EL MENSAJE DE ERROR
									$msg = 'El campo Iniciación, Seguimiento o Venta es icorrecto.';
								}
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El usuario no cuenta con información de empleado.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible agregar el registro de la actividad con la información enviada. Por favor revisa el formulario.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg'  => $msg,
			'data' => $data
		));
	}


	/**
	 * CARGAR INFORMACION DE ACTIVIDAD
	 *
	 * @access  private
	 * @return  Int
	 */
    public function post_load_activity()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg  = '';
		$data = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('activity');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('activity_id', 'activity_id', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE OBTIENE EL ID DEL USUARIO
						$user_id = Auth::get('id');

						# SE BUSCA INFORMACION A TRAVES DEL MODELO
						$activity= Model_Activity::query()
						->where('id', $val->validated('activity_id'))
						->where('user_id', $user_id)
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($activity))
						{
							# SE ALMACENA LA INFORMACION
							$data = array(
								'id'          => $activity->id,
								'customer'    => $activity->customer,
								'invoice'     => $activity->invoice,
								'company'     => $activity->company,
								'foreing'     => $activity->foreing,
								'contact_id'  => $activity->contact_id,
								'hour'        => $activity->hour,
								'time_id'     => $activity->time_id,
								'type_id'     => $activity->type_id,
								'status_id'   => $activity->status_id,
								'category_id' => $activity->category_id,
								'comments'    => $activity->comments,
								'total'       => $activity->total
							);

							# SE ESTABLECE EL MENSAJE DE EXITO
							$msg = 'ok';
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'La actividad solicitada no pertenece a tu usuario.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible cargar el registro de la actividad con la información enviada. Por favor revisa el formulario.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg'  => $msg,
			'data' => $data
		));
	}


	/**
	 * EDITAR ACTIVIDADES
	 *
	 * @access  private
	 * @return  Int
	 */
    public function post_edit_activity()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';
		$url = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('activity');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('activity_id', 'activity_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('customer', 'customer', 'required|min_length[1]|max_length[255]');
			$val->add_field('company', 'company', 'required|min_length[1]|max_length[255]');
			$val->add_field('total', 'total', 'required|float');
			$val->add_field('contact_id', 'contact_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('hour', 'hour', 'required|min_length[1]|max_length[255]');
			$val->add_field('invoice', 'invoice', 'required|valid_string[numeric]|numeric_min[0]');
			$val->add_field('foreing', 'foreing', 'required|valid_string[numeric]|numeric_min[0]');
			$val->add_field('time_id', 'time_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('type_id', 'type_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('status_id', 'status_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('category_id', 'category_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('comments', 'comments', 'min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE OBTIENE EL ID DEL USUARIO
						$user_id = Auth::get('id');

						# SE BUSCA INFORMACION A TRAVES DEL MODELO
						$activity= Model_Activity::query()
						->where('id', $val->validated('activity_id'))
						->where('user_id', $user_id)
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($activity))
						{
							# SE BUSCA LA INFORMACION
							$type     = Model_Activitys_Type::find($val->validated('type_id'));
							$contact  = Model_Activitys_Methods_Contact::find($val->validated('contact_id'));
							$time     = Model_Activitys_Time::find($val->validated('time_id'));
							$category = Model_Category::find($val->validated('category_id'));
							$status   = Model_Activitys_Status::find($val->validated('status_id'));

							# SI NO EXISTE UN PROBLEMA CON LAS OPCIONES SELECCIONADAS
							if(!empty($type) && !empty($contact) && !empty($time) && !empty($category) && !empty($status))
							{
								# SE ESTABLECE LA NUEVA INFORMACION
								$activity->type_id     = $val->validated('type_id');
								$activity->customer    = $val->validated('customer');
								$activity->company     = $val->validated('company');
								$activity->contact_id  = $val->validated('contact_id');
								$activity->hour        = $val->validated('hour');
								$activity->status_id   = $val->validated('status_id');
								$activity->comments    = $val->validated('comments');
								$activity->global_date = $this->date2unixtime2($val->validated('global_date'));
								$activity->total       = $val->validated('total');
								$activity->time_id     = $val->validated('time_id');
								$activity->foreing     = $val->validated('foreing');
								$activity->invoice     = $val->validated('invoice');

								# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
								if($activity->save())
								{
									# SE ESTABLECE EL MENSAJE DE EXITO
									$msg = 'ok';

									# SE ESTABLECE EL MENSAJE DE EXITO
									Session::set_flash('success', 'El registro de activiades se ha actualizado correctamente.');

									# SE CREA A LA URL
									$url = Uri::base().'admin/crm/activity/editar/'.$activity->act_num;
								}
							}
							else
							{
								# SI ENTRANTE/SALIENTE ESTA VACIO
								if(empty($type))
								{
									# SE ESTABLECE EL MENSAJE DE ERROR
									$msg = 'El campo Entrante/Saliente es icorrecto.';
								}

								# SI MEDIO ESTA VACIO
								if(empty($contact))
								{
									# SE ESTABLECE EL MENSAJE DE ERROR
									$msg = 'El campo Medio es icorrecto.';
								}

								# SI DURACION DE LA LLAMADA ESTA VACIO
								if(empty($time))
								{
									# SE ESTABLECE EL MENSAJE DE ERROR
									$msg = 'El campo Duración de Llamada es icorrecto.';
								}

								# SI PRODUCTO ESTA VACIO
								if(empty($category))
								{
									# SE ESTABLECE EL MENSAJE DE ERROR
									$msg = 'El campo Producto de Interés es icorrecto.';
								}

								# SI SEGUIMIENTO ESTA VACIO
								if(empty($status))
								{
									# SE ESTABLECE EL MENSAJE DE ERROR
									$msg = 'El campo Iniciación, Seguimiento o Venta es icorrecto.';
								}
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El usuario no cuenta con información de empleado.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible agregar el registro de la actividad con la información enviada. Por favor revisa el formulario.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg,
			'url' => $url
		));
	}


	/**
	 * FINALIZA
	 *
	 * FINALIZA LAS ACTIVIDADES
	 *
	 * @access  private
	 * @return  Int
	 */
	public function post_finalize_activities()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';
		$url = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('activity');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('act_num', 'act_num', 'required|min_length[1]|max_length[255]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE OBTIENE EL ID DEL USUARIO
						$user_id = Auth::get('id');

						# SE BUSCA LA INFORMACION DEL EMPLEADO
						$employee = Model_Employee::query()->where('user_id', $user_id)->get_one();

						# SI EXISTE INFORMACION
						if($employee)
						{
							# SE BUSCA INFORMACION A TRAVES DEL MODELO
							$activity_num = Model_Activitys_Num::query()
							->where('act_num', $val->validated('act_num'))
							->get_one();

							# SI SE OBTIENE INFORMACION
							if(!empty($activity_num))
							{
								# SE ESTBLECE LA NUEVA INFORMACION
								$activity_num->completed = 1;

								# SE ACTUALIZA LA INFORMACION EN LA BASE DE DATOS
								$activity_num->save();

								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';

								# SE ESTABLECE EL MENSAJE DE EXITO
								Session::set_flash('success', 'El registro de activiades se ha finalizado correctamente.');

								# SE CREA A LA URL
								$url = Uri::base().'admin/crm/activity/index';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se puede finalizar las actividades porque no existen registros.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'El usuario no cuenta con información de empleado.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible finalizar el registro de la actividad con la información enviada.';


			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg,
			'url' => $url
		));
	}


	
	/**
	* ACTIVITY_COMPLETED
	*
	* @access  public
	* @return  Object
	*/
	public function post_activity_completed()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = '';

		# SI ES UNA LLAMADA AJAX
		if(Input::is_ajax())
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('activity_completed');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'min_length[1]');
			$val->add_field('activity', 'actividad', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('value', 'valor', 'required|valid_string[numeric]|numeric_min[0]|numeric_max[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
				$check_access = Model_User::query()
				->where('id', $val->validated('access_id'))
				->get_one();

				# SI SE OBTIENE INFORMACION
				if(!empty($check_access))
				{
					# SI EL TOKEN ESTA REGISTRADO EN LA BASE DE DATOS
					if(md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
						$activity = Model_Activitys_Num::query()
						->where('id', $val->validated('activity'))
						->get_one();

						# SI SE OBTIENE INFORMACION
						if(!empty($activity))
						{
							# SE ESTEBLECE LA NUEVA INFORMACION
							$activity->completed = ($val->validated('value') == 0) ? 0 : 1;

							# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
							if($activity->save())
							{
								# SE ESTABLECE EL MENSAJE DE EXITO
								$msg = 'ok';
							}
							else
							{
								# SE ESTABLECE EL MENSAJE DE ERROR
								$msg = 'No se pudo cambiar el estatus de la actividad, por favor intenta más tarde.';
							}
						}
						else
						{
							# SE ESTABLECE EL MENSAJE DE ERROR
							$msg = 'La actividad enviada no existe.';
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'No es posible modificar el valor en base de datos con la información enviada.';
			}
		}
		else
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVIA EL ARREGLO CON LA RESPUESTA
		$this->response(array(
			'msg' => $msg
		));
	}

	/**
	 * BUSCAR CLIENTE
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_search_customers()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = 'error';
		$data = [];

		# SI ES UNA LLAMADA AJAX
		if (Input::is_ajax())
		{
			# SE CREA LA VALIDACIÓN DE LOS CAMPOS
			$val = Validation::forge('customer_search');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'required|min_length[1]');
			$val->add_field('term', 'Término de búsqueda', 'required|min_length[1]|max_length[255]');

			# SI NO HAY PROBLEMAS CON LA VALIDACIÓN
			if ($val->run())
			{
				# VALIDAR USUARIO Y CREDENCIALES
				$check_access = Model_User::query()
					->where('id', $val->validated('access_id'))
					->get_one();

				# SI SE ENCUENTRA EL USUARIO
				if (!empty($check_access))
				{
					# VALIDAR EL TOKEN
					if (md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# BUSCAR CLIENTES EN LA BASE DE DATOS
						$term = $val->validated('term');

						$customers = Model_Customer::query()
							->related('user') // Asegurar que trae los datos del usuario
							->where_open()
								->where('name', 'like', "%{$term}%")
								->or_where('last_name', 'like', "%{$term}%")
								->or_where('sap_code', 'like', "%{$term}%")
								->or_where('user.email', 'like', "%{$term}%")  // Filtrar por correo
								->or_where('user.username', 'like', "%{$term}%")  // Filtrar por usuario
							->where_close()
							->get();

						# SI SE ENCUENTRAN CLIENTES
						if (!empty($customers))
						{
							foreach ($customers as $customer)
							{
								$data[] = [
									'id'        => $customer->id,
									'name'      => $customer->name,
									'last_name' => $customer->last_name,
									'sap_code'  => $customer->sap_code ?? 'Sin código SAP',
									'email'     => $customer->user->email ?? 'Sin correo',
									'username'  => $customer->user->username ?? 'Sin usuario'
								];
							}

							$msg = 'ok';
						}
						else
						{
							$msg = 'No se encontraron clientes con ese término de búsqueda.';
						}
					}
					else
					{
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				$msg = 'Error en los datos enviados. Verifica el formulario.';
			}
		}
		else
		{
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVÍA LA RESPUESTA
		$this->response(['msg' => $msg, 'data' => $data]);
	}

	/**
	 * BUSCAR DIRECCIÓN DEL CLIENTE
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_get_customer_addresses()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = 'error';
		$addresses = [];

		# SI ES UNA LLAMADA AJAX
		if (Input::is_ajax())
		{
			# SE CREA LA VALIDACIÓN DE LOS CAMPOS
			$val = Validation::forge('customer_address');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'required|min_length[1]');
			$val->add_field('customer_id', 'customer_id', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY PROBLEMAS CON LA VALIDACIÓN
			if ($val->run())
			{
				# VALIDAR USUARIO Y CREDENCIALES
				$check_access = Model_User::query()
					->where('id', $val->validated('access_id'))
					->get_one();

				# SI SE ENCUENTRA EL USUARIO
				if (!empty($check_access))
				{
					# VALIDAR EL TOKEN
					if (md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# BUSCAR CLIENTE Y SUS DIRECCIONES
						$customer_id = $val->validated('customer_id');
						$customer = Model_Customer::query()
							->related('addresses')
							->where('id', $customer_id)
							->get_one();

						# SI SE ENCONTRÓ EL CLIENTE Y TIENE DIRECCIONES
						if (!empty($customer) && !empty($customer->addresses))
						{
							foreach ($customer->addresses as $address)
							{
								$addresses[] = [
									'id' => $address->id,
									'full_address' => $address->street . ' #' . $address->number .
										(empty($address->internal_number) ? '' : ', Int. ' . $address->internal_number) .
										', ' . $address->colony . ', CP: ' . $address->zipcode .
										', ' . $address->city . ', ' . $address->state->name,
									'default' => $address->default
								];
							}

							$msg = 'ok';
						}
						else
						{
							$msg = 'El cliente no tiene domicilios registrados.';
						}
					}
					else
					{
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				$msg = 'Error en los datos enviados. Verifica el formulario.';
			}
		}
		else
		{
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVÍA LA RESPUESTA
		$this->response([
			'msg' => $msg,
			'addresses' => $addresses
		]);
	}

	/**
	 * DEVOLVER PRECIO POR CLIENTE
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_get_customer_prices()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = 'error';
		$products_with_prices = [];

		# SI ES UNA LLAMADA AJAX
		if (Input::is_ajax())
		{
			# SE CREA LA VALIDACIÓN DE LOS CAMPOS
			$val = Validation::forge('customer_prices');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'required|min_length[1]');
			$val->add_field('customer_id', 'customer_id', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY PROBLEMAS CON LA VALIDACIÓN
			if ($val->run())
			{
				# VALIDAR USUARIO Y CREDENCIALES
				$check_access = Model_User::query()
					->where('id', $val->validated('access_id'))
					->get_one();

				# SI SE ENCUENTRA EL USUARIO
				if (!empty($check_access))
				{
					# VALIDAR EL TOKEN
					if (md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# BUSCAR CLIENTE Y SU TIPO DE PRECIO
						$customer_id = $val->validated('customer_id');
						$customer = Model_Customer::find($customer_id);

						if (!empty($customer))
						{
							$type_id = $customer->type_id; // Obtener el type_id del cliente

							# OBTENER PRODUCTOS DISPONIBLES CON PRECIOS SEGÚN EL TIPO DE CLIENTE
							$products = Model_Product::query()
								->related('price')
								->where('status', 1)
								->where('available', '>', 0)
								->get();

							foreach ($products as $product)
							{
								$price = Model_Products_Price::get_price($product->id, $type_id);
								$products_with_prices[] = [
									'id' => $product->id,
									'name' => $product->name,
									'available' => $product->available,
									'price' => $price,
								];
							}

							$msg = 'ok';
						}
						else
						{
							$msg = 'Cliente no encontrado.';
						}
					}
					else
					{
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				$msg = 'Error en los datos enviados. Verifica el formulario.';
			}
		}
		else
		{
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVÍA LA RESPUESTA
		$this->response([
			'msg' => $msg,
			'products' => $products_with_prices
		]);
	}

	/**
	 * BUSCAR DATOS DE FACTURACIÓN DEL CLIENTE
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_get_customer_invoice_data()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = 'error';
		$formatted_tax_data = [];

		# SI ES UNA LLAMADA AJAX
		if (Input::is_ajax())
		{
			# SE CREA LA VALIDACIÓN DE LOS CAMPOS
			$val = Validation::forge('customer_invoice_data');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'required|min_length[1]');
			$val->add_field('customer_id', 'customer_id', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY PROBLEMAS CON LA VALIDACIÓN
			if ($val->run())
			{
				# VALIDAR USUARIO Y CREDENCIALES
				$check_access = Model_User::query()
					->where('id', $val->validated('access_id'))
					->get_one();

				# SI SE ENCUENTRA EL USUARIO
				if (!empty($check_access))
				{
					# VALIDAR EL TOKEN
					if (md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# BUSCAR CLIENTE Y SUS DATOS DE FACTURACIÓN
						$customer_id = $val->validated('customer_id');
						$customer = Model_Customer::query()
							->related('tax_data') // Relación con datos de facturación
							->where('id', $customer_id)
							->get_one();

						if (!empty($customer) && !empty($customer->tax_data))
						{
							# Convertir `tax_data` a un array si no lo es
							$tax_data = is_array($customer->tax_data) ? $customer->tax_data : [$customer->tax_data];

							# Construir la lista de datos formateados
							foreach ($tax_data as $data)
							{
								$formatted_tax_data[] = [
									'id' => $data->id,
									'formatted' => sprintf(
										"Razón Social: %s | RFC: %s | Dirección: %s, #%s%s, %s, CP: %s, %s, %s",
										$data->business_name,
										$data->rfc,
										$data->street,
										$data->number,
										($data->internal_number ? ', Int. ' . $data->internal_number : ''),
										$data->colony,
										$data->zipcode,
										$data->city,
										$data->state->name
									),
									'default' => $data->default
								];
							}

							$msg = 'ok';
						}
						else
						{
							$msg = 'El cliente no tiene datos de facturación registrados.';
						}
					}
					else
					{
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				$msg = 'Error en los datos enviados. Verifica el formulario.';
			}
		}
		else
		{
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVÍA LA RESPUESTA
		$this->response([
			'msg' => $msg,
			'invoice_data' => $formatted_tax_data
		]);
	}

	/**
	 * AGREGAR PRODUCTO
	 *
	 * @access  public
	 * @return  Object
	 */
	public function post_add_product()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = 'error';
		$product_data = [];
		$price = 0;

		# SI ES UNA LLAMADA AJAX
		if (Input::is_ajax())
		{
			# SE CREA LA VALIDACIÓN DE LOS CAMPOS
			$val = Validation::forge('add_product');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'required|min_length[1]');
			$val->add_field('product_id', 'Id del producto', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('quantity', 'Cantidad', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('customer_id', 'Id del cliente', 'required|valid_string[numeric]|numeric_min[1]');

			# SI NO HAY PROBLEMAS CON LA VALIDACIÓN
			if ($val->run())
			{
				# VALIDAR USUARIO Y CREDENCIALES
				$check_access = Model_User::query()
					->where('id', $val->validated('access_id'))
					->get_one();

				# SI SE ENCUENTRA EL USUARIO
				if (!empty($check_access))
				{
					# VALIDAR EL TOKEN
					if (md5($check_access->login_hash) == $val->validated('access_token'))
					{
						# OBTENER EL CLIENTE PARA CONSEGUIR EL type_id
						$customer_id = $val->validated('customer_id');
						$customer = Model_Customer::find($customer_id);

						if (!empty($customer))
						{
							# OBTENER type_id DEL CLIENTE
							$type_id = $customer->type_id;

							# OBTENER DATOS DEL PRODUCTO
							$product_id = $val->validated('product_id');
							$quantity = $val->validated('quantity');

							# BUSCAR PRODUCTO EN LA BASE DE DATOS
							$product = Model_Product::find($product_id);

							if (!empty($product))
							{
								# OBTENER EL PRECIO DEL PRODUCTO SEGÚN EL CLIENTE
								$price = Model_Products_Price::get_price($product_id, $type_id);

								# Validar que el precio sea un número válido antes de continuar
								if (!is_numeric($price) || $price <= 0) {
									$msg = 'No se pudo obtener el precio del producto.';
								} else {
									$total = number_format($quantity * $price, 2, '.', '');

									# FORMATEAR DATOS PARA RESPUESTA
									$product_data = [
										'product_id' => $product_id,
										'name' => $product->name,
										'quantity' => $quantity,
										'price' => number_format($price, 2, '.', ''),
										'total' => $total
									];

									$msg = 'ok';
								}
							}
							else
							{
								$msg = 'El producto no existe.';
							}
						}
						else
						{
							$msg = 'El cliente no existe.';
						}
					}
					else
					{
						$msg = 'Las credenciales no permiten el acceso al servidor.';
					}
				}
				else
				{
					$msg = 'Las credenciales no permiten el acceso al servidor.';
				}
			}
			else
			{
				$msg = 'Error en los datos enviados. Verifica el formulario.';
			}
		}
		else
		{
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVÍA LA RESPUESTA
		$this->response([
			'msg' => $msg,
			'price' => $price, // Asegurar que el precio esté en la raíz de la respuesta JSON
			'product' => $product_data
		]);
	}



	/**
	 * FINALIZAR CON TRANSFERENCIA
	 *
	 * @access  public
	 * @return  Object
	 */
		public function post_finalizar_transferencia()
		{
			# SE INICIALIZAN LAS VARIABLES
			$msg = 'error';
			$response_data = [];

			# SI ES UNA LLAMADA AJAX
			if (Input::is_ajax())
			{
				# SE CREA LA VALIDACIÓN DE LOS CAMPOS
				$val = Validation::forge('finalizar_transferencia');
				$val->add_callable('Rules');
				$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
				$val->add_field('access_token', 'access_token', 'required|min_length[1]');
				$val->add_field('customer_id', 'customer_id', 'required|valid_string[numeric]|numeric_min[1]');
				$val->add_field('payment_id', 'payment_id', 'required|valid_string[numeric]|numeric_min[1]');
				$val->add_field('address_id', 'address_id', 'required|valid_string[numeric]|numeric_min[1]');
				$val->add_field('tax_datum', 'tax_datum', 'valid_string[numeric]');
				$val->add_field('products', 'products', 'required');

				# SI NO HAY PROBLEMAS CON LA VALIDACIÓN
				if ($val->run())
				{
					# VALIDAR USUARIO Y CREDENCIALES
					$check_access = Model_User::query()
						->where('id', $val->validated('access_id'))
						->get_one();

					# SI SE ENCUENTRA EL USUARIO
					if (!empty($check_access))
					{
						# VALIDAR EL TOKEN
						if (md5($check_access->login_hash) == $val->validated('access_token'))
						{
							# OBTENER LOS DATOS DE LA SOLICITUD
							$customer_id  = $val->validated('customer_id');
							$payment_id   = $val->validated('payment_id');
							$address_id   = $val->validated('address_id');
							$tax_datum_id = $val->validated('tax_datum');
							$products     = Input::post('products', []);

							# VALIDACIONES INICIALES
							if ($payment_id != 2) {
								$msg = 'El método de pago debe ser transferencia.';
							}
							else
							{
								# BUSCAR CLIENTE
								$customer = Model_Customer::find($customer_id);

								if (!$customer) {
									$msg = 'Cliente no encontrado.';
								}
								else
								{
									# VALIDAR DIRECCIÓN
									$address = Model_Customers_Address::query()
										->where('id', $address_id)
										->where('customer_id', $customer_id)
										->get_one();

									if (!$address) {
										$msg = 'La dirección seleccionada no pertenece al cliente.';
									}
									else
									{
										# DUPLICAR DIRECCIÓN EN LA TABLA DE VENTAS
										$sales_address = Model_Sales_Address::forge([
											'state_id'        => $address->state_id,
											'name'            => $address->name,
											'last_name'       => $address->last_name,
											'phone'           => $address->phone,
											'street'          => $address->street,
											'number'          => $address->number,
											'internal_number' => $address->internal_number,
											'colony'          => $address->colony,
											'zipcode'         => $address->zipcode,
											'city'            => $address->city,
											'details'         => $address->details,
										]);

										if ($sales_address->save())
										{
											# CREAR NUEVA VENTA
											$new_sale = Model_Sale::forge([
												'customer_id'   => $customer_id,
												'address_id'    => $sales_address->id,
												'status'        => 2,
												'total'         => 0,
												'discount'      => Input::post('discount', 0),
												'sale_date'     => time(),
												'payment_id'    => 2,
												'transaction'   => '',
												'order_id'      => 0,
												'ordersap'      => 0,
												'factsap'       => 0,
												'package_id'    => 0,
												'guide'         => '',
												'voucher'       => '',
												'admin_updated' => 0,
												'updated_at' => null,
											]);

											if ($new_sale->save())
											{
												# REGISTRAR PAGO
												$payment_data = [
													'type_id' => 2, // Transferencia
													'token'   => 'Transferencia',
													'total'   => 0, // Se actualizará después
												];

												$new_payment = Model_Payment::set_new_record($payment_data);
												if (!$new_payment) {
													$msg = 'Error al registrar el pago.';
												}
												else
												{
													# ASOCIAR EL PAGO A LA VENTA
													$new_sale->payment_id = $new_payment->id;

													if ($new_sale->save())
													{
														# PROCESAR DATOS FISCALES
														if (!empty($tax_datum_id))
														{
															$tax_datum = Model_Customers_Tax_Datum::query()
																->where('id', $tax_datum_id)
																->where('customer_id', $customer_id)
																->get_one();

															if ($tax_datum)
															{
																$new_tax_datum = Model_Sales_Tax_Datum::forge([
																	'sale_id'           => $new_sale->id,
																	'payment_method_id' => $tax_datum->payment_method_id,
																	'cfdi_id'           => $tax_datum->cfdi_id,
																	'sat_tax_regime_id' => $tax_datum->sat_tax_regime_id,
																	'state_id'          => $tax_datum->state_id,
																	'business_name'     => $tax_datum->business_name,
																	'rfc'               => $tax_datum->rfc,
																	'street'            => $tax_datum->street,
																	'number'            => $tax_datum->number,
																	'internal_number'   => $tax_datum->internal_number,
																	'colony'            => $tax_datum->colony,
																	'zipcode'           => $tax_datum->zipcode,
																	'city'              => $tax_datum->city,
																	'csf'               => $tax_datum->csf,
																]);

																if (!$new_tax_datum->save()) {
																	$msg = 'Error al guardar los datos fiscales.';
																}
															}
															else
															{
																$msg = 'Datos fiscales no válidos o no pertenecen al cliente.';
															}
														}

														# VALIDAR Y GUARDAR PRODUCTOS
														$total_sale = 0;

														foreach ($products as $product)
														{
															$product_id = $product['id'];
															$quantity   = $product['quantity'];
															$price      = $product['price'];

															if (!is_numeric($price) || $price <= 0) {
																$msg = "Precio inválido para el producto ID: {$product_id}";
															}
															else
															{
																$total = number_format($quantity * $price, 2, '.', '');
																$total_sale += $total;

																$new_product = Model_Sales_Product::forge([
																	'sale_id'    => $new_sale->id,
																	'product_id' => $product_id,
																	'quantity'   => $quantity,
																	'price'      => number_format($price, 2, '.', ''),
																	'total'      => $total,
																]);

																$new_product->save();
															}
														}

														# ACTUALIZAR TOTAL EN VENTA Y PAGO
														$new_sale->total = $total_sale;
														$new_payment->total = $total_sale;

														if ($new_sale->save() && $new_payment->save())
														{
															# Enviar correo al cliente con los datos de la compra
															$this->send_user_mail($new_sale->id);
															# Responder con éxito
															$msg = 'ok';
															$response_data = ['redirect' => 'reload'];
														}
														else
														{
															$msg = 'Error al actualizar el total.';
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}

			# SE ENVÍA LA RESPUESTA
			$this->response([
				'msg' => $msg,
				'data' => $response_data
			]);
		}


	/**
     *
     *
     * BUSCAR PRODUCTOS
     *
     * @access  private
     * @return  Boolean
     */
	public function post_search_products()
	{
		# SE INICIALIZAN LAS VARIABLES
		$msg = 'error';
		$products = [];

		# SI ES UNA LLAMADA AJAX
		if (Input::is_ajax())
		{
			# SE CREA LA VALIDACIÓN DE LOS CAMPOS
			$val = Validation::forge('product_search');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'required|min_length[1]');
			$val->add_field('customer_id', 'customer_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('term', 'Término de búsqueda', 'required|min_length[2]|max_length[255]');

			# SI NO HAY PROBLEMAS CON LA VALIDACIÓN
			if ($val->run())
			{
				# VALIDAR USUARIO Y CREDENCIALES
				$check_access = Model_User::query()
					->where('id', $val->validated('access_id'))
					->get_one();

				if (!empty($check_access) && md5($check_access->login_hash) == $val->validated('access_token'))
				{
					$term = $val->validated('term');
					$customer_id = $val->validated('customer_id');

					# OBTENER EL TYPE_ID DEL CLIENTE PARA CONSEGUIR SU PRECIO CORRECTO
					$customer = Model_Customer::find($customer_id);
					$type_id = $customer ? $customer->type_id : null;

					# BUSCAR PRODUCTOS QUE COINCIDAN CON EL TÉRMINO DE BÚSQUEDA
					$product_results = Model_Product::query()
						->where('name', 'like', "%{$term}%")
						->or_where('code', 'like', "%{$term}%")
						->where('available', '>', 0)
						->limit(200)
						->get();

					# FORMATEAR RESPUESTA
					foreach ($product_results as $product)
					{
						$price = Model_Products_Price::get_price($product->id, $type_id);
						$products[] = [
							'id' => $product->id,
							'name' => $product->name,
							'available' => $product->available,
							'price' => number_format($price, 2, '.', '')
						];
					}

					$msg = 'ok';
				}
				else
				{
					$msg = 'Las credenciales no permiten el acceso.';
				}
			}
			else
			{
				$msg = 'Error en los datos enviados.';
			}
		}
		else
		{
			$msg = 'La petición no es del tipo AJAX.';
		}

		# SE ENVÍA LA RESPUESTA
		$this->response(['msg' => $msg, 'products' => $products]);
	}

	/**
     * SEND ADMIN MAIL
     *
     * ENVIA POR EMAIL UN MENSAJE DEL PEDIDO AL USUARIO
     *
     * @access  private
     * @return  Boolean
     */
	private function send_user_mail($sale_id = 0)
	{
		# SE INICIALIZAN LAS VARIABLES
		$data               = array();
		$address_html       = '';
		$products_html      = '';
		$transfer_data_html = '';

		# SE BUSCA LA INFORMACIÓN DE LA VENTA
		$sale = Model_Sale::query()
			->related('products')
			->related('customer')
			->where('id', $sale_id)
			->where('status', 2)
			->get_one();

		# SI NO SE ENCUENTRA LA VENTA, SALIR
		if (empty($sale)) {
			Log::error("No se encontró la venta ID {$sale_id}, no se enviará correo.");
			return false;
		}

		# VALIDAR QUE EL CLIENTE TIENE UN EMAIL REGISTRADO
		if (empty($sale->customer->user->email)) {
			Log::error("Cliente ID {$sale->customer->id} no tiene email registrado. No se enviará correo.");
			return false;
		}

		# VALIDAR QUE HAY PRODUCTOS EN LA VENTA
		if (empty($sale->products)) {
			Log::error("Venta ID {$sale->id} no tiene productos registrados. No se enviará correo.");
			return false;
		}

		# OBTENER INFORMACIÓN DE TRANSFERENCIA
		$transfer_data = Model_Transfer_Datum::query()
			->where('id', 1)
			->get_one();

		$transfer_data_html = !empty($transfer_data) ? $transfer_data->info : 'Datos bancarios no disponibles. Contacta a soporte.';

		# GENERAR EL HTML DE LOS PRODUCTOS
		foreach ($sale->products as $product) {
			$imagePath = 'thumb_' . $product->product->image;
			$imageSrc = file_exists(DOCROOT . $imagePath) ? Asset::img($imagePath, array('alt' => $product->product->name)) : Asset::img('thumb_no_image.png', array('alt' => 'No Imagen'));

			$products_html .= $imageSrc . '
				<strong style="display: block; margin-bottom: 15px">' . $product->product->name . '</strong>
				<strong style="display: block;">Precio unitario:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">$' . number_format($product->price, 2, '.', ',') . '</span>
				<strong style="display: block;">Cantidad:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">' . $product->quantity . '</span>
				<strong style="display: block;">Total:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">$' . number_format($product->total, 2, '.', ',') . '</span>
			';
		}

		# GENERAR HTML PARA LA DIRECCIÓN SI EXISTE
		if ($sale->address_id != 0) {
			$address_html .= '<h1>Datos de envío</h1>
				<p>
					<span><strong>Nombre: ' . $sale->address->name . ' ' . $sale->address->last_name . '<br>
					Calle: ' . $sale->address->street . ' ' . $sale->address->number . ' ' . $sale->address->internal_number . '<br>
					Colonia: ' . $sale->address->colony . ', Código Postal: ' . $sale->address->zipcode . '<br>
					' . $sale->address->city . ', ' . $sale->address->state->name . ', México<br>
					Teléfono: ' . $sale->address->phone . '</strong></span>
				</p>';
		}

		# CREAR EL CUERPO DEL CORREO
		$data['body'] = '
			<tr>
				<td>
					<h1>¡Gracias por comprar en Distribuidora Sajor!</h1>
					<p><strong>ID de pedido:</strong> <span>' . $sale->id . '</span></p>
					<p><strong>Fecha:</strong> <span>' . date('d/m/Y', $sale->sale_date) . '</span></p>
					<p><strong>Total:</strong> <span>$' . number_format($sale->total - $sale->discount, 2, '.', ',') . '</span></p>
					<p>Tu orden tiene una vigencia de 48 hrs para enviar el comprobante de pago.</p>
					<h1>Formas de pago</h1>
					<p>' . $transfer_data_html . '</p>
					' . $address_html . '
					<h1>Productos</h1>
					<p>' . $products_html . '</p>
				</td>
			</tr>';

		# ENVIAR EL CORREO
		$email = Email::forge();
		$email->from('ventasenlinea@sajor.com.mx', 'Distribuidora Sajor');
		$email->reply_to('ventasenlinea@sajor.com.mx', 'Distribuidora Sajor');
		$email->to([$sale->customer->user->email => $sale->customer->name . ' ' . $sale->customer->last_name]);
		$email->subject('Sajor - Pedido realizado');
		$email->html_body(View::forge('email_templates/default', $data, false), false);

		try {
			if ($email->send()) {
				Log::info("Correo enviado con éxito a {$sale->customer->user->email} para la venta ID: {$sale->id}");
				return true;
			}
		} catch (\EmailSendingFailedException $e) {
			Log::error("Error al enviar correo: " . $e->getMessage());
		} catch (\EmailValidationFailedException $e) {
			Log::error("Error de validación de correo: " . $e->getMessage());
		}

		return false;
	}

	/**
     * FILTRO INDEX EN LOS SOCIOS
     *
     * PARA EL APRTADO DE SOCIOS ES SOLO EL FILTRO DEL INDEX
     *
     * @access  private
     * @return  Boolean
     */
	public function post_filtro_socios()
	{
		# INICIALIZAR VARIABLES
		$msg     = '';
		$content = '';

		# SI ES UNA LLAMADA AJAX
		if (Input::is_ajax())
		{
			# VALIDACIÓN BÁSICA
			$filter = Input::post('filter');

			# SE CREA LA CONSULTA BASE
			$partners = Model_User::query()
				->related('partner')
				->where('group', 15);

			# APLICAR FILTROS SEGÚN OPCIÓN SELECCIONADA
			switch ($filter)
			{
				case 'updated':
					# SOCIOS ACTUALIZADOS EN LOS ÚLTIMOS 7 DÍAS
					$seven_days_ago = strtotime('-7 days');

					$partners->where('t1.updated_at', '>=', $seven_days_ago)
							->where(DB::expr('t1.updated_at'), '!=', DB::expr('t1.created_at'));
					break;

				case 'csf':
					# SOCIOS CON CONSTANCIA FISCAL
					$csf_partners = array();
					$tax_data = Model_Partners_Tax_Datum::query()->where('csf', '!=', '')->get();
					foreach ($tax_data as $item) {
						$csf_partners[] = $item->partner_id;
					}

					if (!empty($csf_partners)) {
						$partners->where('t1.id', 'IN', $csf_partners);
					} else {
						$partners->where('t1.id', '=', 0);
					}
					break;

				case 'deliveries':
					# SOCIOS CON DOMICILIOS DE ENTREGA
					$delivery_partners = array();
					$deliveries = Model_Partners_Delivery::query()->where('deleted', 0)->get();
					foreach ($deliveries as $item) {
						$delivery_partners[] = $item->partner_id;
					}

					if (!empty($delivery_partners)) {
						$partners->where('t1.id', 'IN', $delivery_partners);
					} else {
						$partners->where('t1.id', '=', 0);
					}
					break;

				case 'contacts':
					# SOCIOS CON CONTACTOS
					$contact_partners = array();
					$contacts = Model_Partners_Contact::query()->where('deleted', 0)->get();
					foreach ($contacts as $item) {
						$contact_partners[] = $item->partner_id;
					}

					if (!empty($contact_partners)) {
						$partners->where('t1.id', 'IN', $contact_partners);
					} else {
						$partners->where('t1.id', '=', 0);
					}
					break;

				default:

					break;
			}

			# ORDEN GLOBAL POR FECHA DE ACTUALIZACIÓN
			$partners->order_by('updated_at', 'desc');


			# OBTENER LOS DATOS
			$result = $partners->get();

			# SI HAY RESULTADOS
			if (!empty($result))
			{


				foreach ($result as $partner)
					{

						$status = unserialize($partner->profile_fields);

						$content .= '<tr>';
						$content .= '<th class="id">' . Html::anchor('admin/socios/info/' . $partner->partner->id, $partner->partner->id) . '</th>';
						$content .= '<th class="code_sap">' . Html::anchor('admin/socios/info/' . $partner->partner->id, $partner->partner->code_sap) . '</th>';
						$content .= '<td class="name">' . $partner->partner->name . '</td>';
						$content .= '<td class="rfc">' . $partner->partner->rfc . '</td>';
						$content .= '<td class="email">' . $partner->email . '</td>';
						$content .= '<td class="csf">' . ((isset($partner->partner->tax_datum) && $partner->partner->tax_datum->csf != '') ? 'Sí' : 'No') . '</td>';
						$content .= '<td class="deliveries">' . Model_Partners_Delivery::query()->where('partner_id', $partner->partner->id)->where('deleted', 0)->count() . '</td>';
						$content .= '<td class="contacts">' . Model_Partners_Contact::query()->where('partner_id', $partner->partner->id)->where('deleted', 0)->count() . '</td>';
						$content .= '<td class="type_id">' . ($partner->partner->type_id->name ?? 'Sin asignar') . '</td>';
						$content .= '<td class="employee_id">' . ($partner->partner->employee->name ?? '') . '</td>';
						$content .= '<td class="banned">' . (($status['banned']) ? 'Sí' : 'No') . '</td>';
						$content .= '<td class="updated_at">' . date('d/m/Y - H:i', $partner->updated_at) . '</td>';
						$content .= '<td class="text-right">
										<div class="dropdown">
											<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="fas fa-ellipsis-v"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
												' . Html::anchor('admin/socios/info/' . $partner->partner->id, 'Ver', array('class' => 'dropdown-item')) . '
												' . Html::anchor('admin/socios/editar/' . $partner->partner->id, 'Editar', array('class' => 'dropdown-item')) . '
												' . Html::anchor('admin/socios/recuperar_contrasena_socios/' . $partner->id, 'Recuperar Contraseña', array('class' => 'dropdown-item')) . '
											</div>
										</div>
									</td>';
						$content .= '</tr>';
					}
				$msg = 'ok';
			}
			else
			{
				$content = '<tr><td colspan="13">No se encontraron registros con el filtro aplicado.</td></tr>';
				$msg     = 'vacio';
			}
		}
		else
		{
			$msg = 'La petición no es del tipo AJAX.';
		}

		# RESPUESTA
		$this->response(array(
			'msg'  => $msg,
			'html' => $content
		));
	}


	/////EMPIZO PARA COTIZACIONES DE SOCIOS DE NEGOCIOS SOLO PARA SOCIOS DADOS DE ALTA

// =========================
// AJAX PARA MÓDULO DE COTIZACIONES
// =========================


/**cotizaciones pendientes */
public function post_get_pending_quote()
{
    // ============================================================
    // [COTIZACIONES][AJAX] OBTIENE COTIZACIÓN PENDIENTE POR ID
    // ============================================================

    // === OBTENER EL ID DE LA COTIZACIÓN PENDIENTE DESDE POST ===
   // LEER JSON MANUALMENTE DEL CUERPO DEL REQUEST
$body = file_get_contents('php://input');
$data = json_decode($body, true);

$pending_id = $data['pending_id'] ?? null;

\Log::debug('[Cotizaciones][AJAX] ID pendiente recibido (por json decode): ' . $pending_id);

    \Log::debug('[Cotizaciones][AJAX] ID pendiente recibido: ' . $pending_id);

    if (!$pending_id || !is_numeric($pending_id)) {
        \Log::debug('[Cotizaciones][AJAX] ID inválido. Valor recibido: ' . $pending_id . ' Tipo: ' . gettype($pending_id));
        return $this->response(['msg' => 'ID no válido']);
    }

    // === BUSCAR LA COTIZACIÓN PENDIENTE ===
    $pq = Model_Quotes_Partner::query()
        ->where('id', $pending_id)
        ->get_one();

    if (!$pq) {
        \Log::debug("[Cotizaciones][AJAX] No se encontró la cotización con ID $pending_id");
        return $this->response(['msg' => 'Cotización no encontrada']);
    }

    // === OBTENER EL SOCIO RELACIONADO POR USER_ID ===
    $user_id = $pq->partner_id;
    $partner = Model_Partner::query()
        ->where('user_id', $user_id)
        ->get_one();

    if (!$partner) {
        \Log::debug("[Cotizaciones][AJAX] No se encontró socio con user_id $user_id");
        return $this->response(['msg' => 'Socio no encontrado']);
    }

    // === DESERIALIZAR LOS PRODUCTOS GUARDADOS ===
    $productos = [];
    $products_serialized = @unserialize($pq->quote);

    if ($products_serialized && is_array($products_serialized)) {
        foreach ($products_serialized as $product_id => $item) {
            $prod = Model_Product::find($product_id);
            if (!$prod) continue;

            $productos[] = [
                'id'        => $prod->id,
                'code'      => $prod->code,
                'name'      => $prod->name,
                'quantity'  => $item['quantity'],
                'image'     => $prod->image,
                // El precio se asignará más adelante en el flujo normal
            ];
        }
    }

    // === RESPUESTA FINAL ===
    return $this->response([
        'msg'      => 'ok',
        'partner'  => [
            'id'           => $partner->id,
            'name'         => $partner->name,
            'code_sap'     => $partner->code_sap,
        ],
        'products' => $productos
    ]);
}


	//CATALOGOS COMPLETOS PARA COTIZACIONES
	// CARGAR CATÁLOGOS COMPLETOS
	public function post_catalogos_cotizaciones_completo()
	{
		\Log::debug('[Catálogos][AJAX] INICIO post_catalogos_cotizaciones_completo');
		$headers = getallheaders();
		$is_ajax = \Input::is_ajax() || (isset($headers['X-Requested-With']) && strtolower($headers['X-Requested-With']) == 'xmlhttprequest');

		if (!$is_ajax) {
			\Log::debug('[Catálogos][AJAX] No es AJAX');
			return $this->response(['msg' => 'No es una petición AJAX']);
		}

		$json = file_get_contents('php://input');
		$post = json_decode($json, true);

		$access_id    = $post['access_id']    ?? null;
		$access_token = $post['access_token'] ?? null;

		// === VALIDACIÓN DE CREDENCIALES ===
		if (empty($access_id) || empty($access_token)) {
			\Log::debug('[Catálogos][AJAX] Faltan credenciales');
			return $this->response(['msg' => 'Faltan credenciales']);
		}
		$user = \Model_User::find($access_id);
		if (!$user || md5($user->login_hash) !== $access_token) {
			\Log::debug('[Catálogos][AJAX] Token inválido');
			return $this->response(['msg' => 'Token inválido']);
		}

		// ==============================
		// SOCIOS (PARTNERS) ACTIVOS
		// ==============================
		$partners = \Model_Partner::query()
			->select(['id', 'name', 'code_sap', 'email', 'type_id', 'user_id'])
			->where('deleted', 0)
			->get();
		$partners_data = [];
		foreach ($partners as $p) {
			$partners_data[] = [
				'id'       => $p->id,
				'name'     => $p->name,
				'code_sap' => $p->code_sap,
				'email'    => $p->email,
				'type_id'  => $p->type_id,
				'user_id'  => $p->user_id
			];
		}
		\Log::debug('[Catálogos][AJAX] Partners consultados: ' . count($partners_data));

		// ==============================
		// PRODUCTOS ACTIVOS
		// ==============================
		$products = \Model_Product::query()
			->select(['id', 'name', 'code', 'original_price', 'image', 'minimum_sale', 'brand_id', 'status', 'available'])
			->where('deleted', 0)
			->where('status', 1)
			->get();
		$products_data = [];
		foreach ($products as $pr) {
			$products_data[] = [
				'id'           			=> $pr->id,
				'name'         			=> $pr->name,
				'code'         			=> $pr->code,
				'original_price'        => (float)$pr->original_price,

				'image'    				=> $pr->image ?: '/assets/uploads/thumb_no_image.png',
				'minimum_sale' 			=> (int)($pr->minimum_sale ?? 0),
				'brand_id'     			=> $pr->brand_id,
				'available'    			=> $pr->available
			];
		}
		\Log::debug('[Catálogos][AJAX] Productos consultados: ' . count($products_data));

		// ==============================
		// PRECIOS DE PRODUCTOS POR TIPO (products_prices)
		// ==============================
		$products_prices = \Model_Products_Price::query()
			->select(['id', 'product_id', 'type_id', 'price'])
			->get();
		$products_prices_data = [];
		foreach ($products_prices as $pp) {
			$products_prices_data[] = [
				'id'         => $pp->id,
				'product_id' => $pp->product_id,
				'type_id'    => $pp->type_id,
				'price'      => (float)$pp->price,
			];
		}

		\Log::debug('[Catálogos][AJAX] Precios de productos consultados: ' . count($products_prices_data));

		// ==============================
		// EMPLEADOS/VENDEDORES
		// ==============================
		$employees = \Model_Employee::query()
			->select(['id', 'name', 'user_id'])
			->where('deleted', 0)
			->get();
		$employees_data = [];
		foreach ($employees as $e) {
			$employees_data[] = [
				'id'      => $e->id,
				'name'    => $e->name,
				'user_id' => $e->user_id,
			];
		}
		\log::debug('[Catálogos][AJAX] Empleados consultados: ' . count($employees_data));

		// ==============================
		// MÉTODOS DE PAGO
		// ==============================
		$payments = \Model_Payments_Method::query()
			->select(['id', 'name'])
			->where('deleted', 0)
			->get();
		$payments_data = [];
		foreach ($payments as $pm) {
			$payments_data[] = [
				'id'   => $pm->id,
				'name' => $pm->name,
			];
		}

		\Log::debug('[Catálogos][AJAX] Métodos de pago consultados: ' . count($payments_data));

		// ==============================
		// IMPUESTOS
		// ==============================
		$taxes = \Model_Tax::query()
			->select(['id', 'name',  'code', 'rate'])
			//->where('deleted', 0)
			->order_by('name', 'asc')
			->get();
		$taxes_data = [];
		foreach ($taxes as $t) {
			$taxes_data[] = [
				'id'    => $t->id,
				'name'  => $t->code.' ('.$t->rate*100 . '%)',
				'rate'  => $t->rate*100
			];
		}
		\Log::debug('[Catálogos][AJAX] Impuestos consultados: ' . count($taxes_data));

		// ==============================
		// RETENCIONES
		// ==============================
		$retentions = \Model_Retention::query()
			->select(['id', 'description', 'code', 'rate'])
			//->where('deleted', 0)
			->order_by('code', 'asc')
			->get();
		$retentions_data = [];
		foreach ($retentions as $r) {
			$retentions_data[] = [
				'id'    => $r->id,
				'name'  => $r->code.' ('.$r->rate*100 . '%)',
				'rate'  => $r->rate*100
			];
		}
		\Log::debug('[Catálogos][AJAX] Retenciones consultadas: ' . count($retentions_data));

		// ==============================
		// MONEDAS
		// ==============================
		$currencies = \Model_Currency::query()
			->select(['id', 'name', 'symbol', 'code'])
			->where('deleted', 0)
			->order_by('name', 'asc')
			->get();
		$currencies_data = [];
		foreach ($currencies as $c) {
			$currencies_data[] = [
				'id'     => $c->id,
				'name'   => $c->code.' - '.$c->name.($c->symbol ? " ($c->symbol)" : ''),
				'symbol' => $c->symbol,
				'code'   => $c->code
			];
		}

		\Log::debug('[Catálogos][AJAX] Monedas consultadas: ' . count($currencies_data));

		// ==============================
		// ESTADOS
		// ==============================
		$states = \Model_State::query()
			->order_by('name', 'asc')
			->get();
		$states_data = [];
		foreach ($states as $s) {
			$states_data[] = [
				'id'   => $s->id,
				'name' => $s->name,
			];
		}

		\Log::debug('[Catálogos][AJAX] Estados consultados: ' . count($states_data));
		// ==============================
		// MARCAS
		// ==============================
		$brands = \Model_Brand::query()
			->select(['id', 'name'])
			->where('deleted', 0)
			->where('status', 1)
			->order_by('name', 'asc')
			->get();
		$brands_data = [];
		foreach ($brands as $brand) {
			$brands_data[] = [
				'id'   => $brand->id,
				'name' => $brand->name,
			];
		}
		\Log::debug('[Catálogos][AJAX] Marcas consultadas: ' . count($brands_data));

		// ==============================
		// DESCUENTOS AUTORIZADOS
		// ==============================
		$discounts = \Model_Discount::query()
			->select(['id', 'name', 'structure', 'type', 'final_effective'])
			->where('deleted', 0)
			->where('active', 1)
			->order_by('name', 'asc')
			->get();

		$discounts_data = [];
		foreach ($discounts as $d) {
			$discounts_data[] = [
				'id'              => $d->id,
				'name'            => $d->name,
				'structure'       => $d->structure,
				'type'            => $d->type,
				'final_effective' => $d->final_effective
			];
		}
		\Log::debug('[Catálogos][AJAX] Descuentos consultados: ' . count($discounts_data));


		\Log::debug('[Catálogos][AJAX] ÉXITO catálogos enviados.');

		return $this->response([
			'msg'        		=> 'ok',
			'partners'   		=> $partners_data,
			'products'   		=> $products_data,
			'employees'  		=> $employees_data,
			'payments'   		=> $payments_data,
			'taxes'      		=> $taxes_data,
			'retentions' 		=> $retentions_data,
			'currencies' 		=> $currencies_data,
			'states'    		=> $states_data,
			'brands'     		=> $brands_data,
			'products_prices'	=> $products_prices_data,
			'discounts' 		=> $discounts_data,
		]);
	}



	/**
	 * CARGAR CATÁLOGOS GENERALES PARA COTIZACIONES
	 * Esta función se encarga de cargar los catálogos necesarios para el módulo de cotizaciones.
	 * Incluye impuestos, retenciones, monedas, métodos de pago, marcas, empleados y descuentos.
	 */
	public function post_catalogos_cotizaciones()
	{
		\Log::debug('[Cotizaciones][AJAX] INICIO post_catalogos_cotizaciones');
		if (\Input::is_ajax()) {
			$json = file_get_contents('php://input');
			$post = json_decode($json, true);

			$access_id    = $post['access_id']    ?? null;
			$access_token = $post['access_token'] ?? null;

			// VALIDACIONES DE TOKEN
			if (empty($access_id) || empty($access_token)) {
				return $this->response(['msg' => 'Faltan credenciales', 'data' => []]);
			}
			$user = \Model_User::find($access_id);
			if (!$user || md5($user->login_hash) !== $access_token) {
				return $this->response(['msg' => 'Token inválido', 'data' => []]);
			}

			// Impuestos
			$taxes = \Model_Tax::query()->order_by('name', 'asc')->get();
			$taxes_data = [];
			foreach ($taxes as $tax) {
				$taxes_data[] = ['id' => $tax->id, 'name' => $tax->code.' ('.$tax->rate*100 . '%)', 'rate' => $tax->rate*100];
			}

			// Retenciones
			$retentions = \Model_Retention::query()->order_by('code', 'asc')->get();
			$ret_data = [];
			foreach ($retentions as $ret) {
				$ret_data[] = ['id' => $ret->id, 'name' => $ret->code.' ('.$ret->rate*100 . '%)', 'rate' => $ret->rate*100];
			}

			// Monedas
			$currencies = \Model_Currency::query()->where('deleted', 0)->order_by('name', 'asc')->get();
			$curr_data = [];
			foreach ($currencies as $currency) {
				$curr_data[] = ['id' => $currency->id, 'name' => $currency->code.' - '.$currency->name.($currency->symbol ? " ($currency->symbol)" : '')];
			}

			// Métodos de pago
			$payments = \Model_Payments_Method::query()->order_by('name', 'asc')->get();
			$pay_data = [];
			foreach ($payments as $p) {
				$pay_data[] = ['id' => $p->id, 'name' => $p->name];
			}

			// Controlador AJAX (catálogos)
			$brands = Model_Brand::query()
				->where('deleted', 0)
				->where('status', 1)
				->order_by('name', 'asc')
				->get();
			$data['marcas'] = [];
			foreach ($brands as $brand) {
				$data['marcas'][] = [
					'id' 	=> $brand->id,
					'name' 	=> $brand->name
				];
			}


			// Empleados (vendedores)
			$user_id = $access_id;
			$employees = \Model_Employee::query()
				->where('user_id', $user_id)
				->where('deleted', 0)
				->order_by('name', 'asc')
				->get();
				\Log::debug('User ID para empleados: '.$user_id);
				\Log::debug('Empleados encontrados: '.count($employees));
				//$employees = \Model_Employee::query()->order_by('name', 'asc')->get();
			$emp_data = [];
			foreach ($employees as $e) {
				$emp_data[] = ['id' => $e->id, 'name' => $e->name];
			}

			// ===============================
			// OBTIENE CATÁLOGO DE DESCUENTOS
			// ===============================
			$discounts = \Model_Discount::query()
				->where('deleted', 0)
				->where('active', 1)
				->order_by('name', 'asc')
				->get();

			$discounts_data = [];
			foreach ($discounts as $d) {
				$discounts_data[] = [
					'id'             => $d->id,
					'name'           => $d->name,
					'final_effective'=> $d->final_effective ?? 0,
				];
			}
			\Log::debug('[Cotizaciones][AJAX] Descuentos consultados: '.count($discounts_data));



			return $this->response([
				'msg' 			=> 'ok',
				'taxes' 		=> $taxes_data,
				'retentions' 	=> $ret_data,
				'currencies' 	=> $curr_data,
				'payments' 		=> $pay_data,
				'employees' 	=> $emp_data,
				'marcas'    	=> $data['marcas'],
				'discounts'  	=> $discounts_data,
			]);
		}
		return $this->response(['msg' => 'No es una petición AJAX']);
	}

	/**
	 * OBTENER CATÁLOGO DE ESTADOS PARA SELECTS
	 * PETICIÓN AJAX POST: Debe incluir access_id, access_token
	 */
	public function post_catalogo_estados()
	{
		\Log::debug('[Catálogo][AJAX] INICIO post_catalogo_estados');

		if (\Input::is_ajax()) {
			$json = file_get_contents('php://input');
			$post = json_decode($json, true);

			$access_id    = $post['access_id']    ?? null;
			$access_token = $post['access_token'] ?? null;

			// === VALIDACIÓN DE CREDENCIALES ===
			if (empty($access_id) || empty($access_token)) {
				return $this->response(['msg' => 'Faltan credenciales', 'estados' => []]);
			}
			$user = \Model_User::find($access_id);
			if (!$user || md5($user->login_hash) !== $access_token) {
				return $this->response(['msg' => 'Token inválido', 'estados' => []]);
			}

			// === CONSULTA DE ESTADOS ===
			$estados = \Model_State::query()->order_by('name', 'asc')->get();
			$data = [];
			foreach ($estados as $estado) {
				$data[] = [
					'id'   => $estado->id,
					'name' => $estado->name
				];
			}

			return $this->response(['msg' => 'ok', 'estados' => $data]);
		}
		return $this->response(['msg' => 'No es una petición AJAX', 'estados' => []]);
	}


	/**
	 * BUSCAR SOCIOS DE NEGOCIOS
	 * PETICIÓN AJAX POST: Debe incluir access_id, access_token, term
	 * Esta función busca socios de negocios por nombre o código SAP.
	 * Retorna un máximo de 20 resultados.
	 * @param  string $term Término de búsqueda
	 * @return array        Lista de socios encontrados
	 * @throws \Exception Si faltan datos obligatorios o el token es inválido
	 * BUSCAR SOCIOS (VUE-SELECT)
	 */
	public function post_search_partners()
	{
		\Log::debug('[Cotizaciones][AJAX] INICIO post_search_partners');
		if (\Input::is_ajax()) {
			$json = file_get_contents('php://input');
			$post = json_decode($json, true);

			$access_id    = $post['access_id']    ?? null;
			$access_token = $post['access_token'] ?? null;
			$term         = $post['term']         ?? null;

			if (empty($access_id) || empty($access_token) || empty($term)) {
				return $this->response(['msg' => 'Faltan datos obligatorios en la petición.', 'data' => []]);
			}
			$user = \Model_User::find($access_id);
			if (!$user || md5($user->login_hash) !== $access_token) {
				return $this->response(['msg' => 'Token inválido', 'data' => []]);
			}

			$partners = \Model_Partner::query()
				->where('name', 'like', "%$term%")
				->or_where('code_sap', 'like', "%$term%")
				->limit(20)->get();

			$data = [];
			foreach ($partners as $p) {
				$data[] = [
					'id' => $p->id,
					'name' => $p->name,
					'code_sap' => $p->code_sap,
				];
			}
			return $this->response(['msg' => 'ok', 'data' => $data]);
		}
		return $this->response(['msg' => 'No es una petición AJAX']);
	}

	// BUSCAR CONTACTOS DEL SOCIO
	/**
	 * OBTIENE LOS CONTACTOS DE UN SOCIO DE NEGOCIOS
	 * PETICIÓN AJAX POST: Debe incluir access_id, access_token, partner_id
	 */
	public function post_search_partners_contacts()
	{
		if (\Input::is_ajax()) {
			$json = file_get_contents('php://input');
			$post = json_decode($json, true);

			$access_id    = $post['access_id']    ?? null;
			$access_token = $post['access_token'] ?? null;
			$partner_id   = $post['partner_id']   ?? null;

			if (empty($access_id) || empty($access_token) || empty($partner_id)) {
				return $this->response(['msg' => 'Faltan datos obligatorios', 'data' => []]);
			}
			$user = \Model_User::find($access_id);
			if (!$user || md5($user->login_hash) !== $access_token) {
				return $this->response(['msg' => 'Token inválido', 'data' => []]);
			}

			$contacts = \Model_Partners_Contact::query()
				->where('partner_id', $partner_id)
				->where('deleted', 0)
				->get();

			$data = [];
			foreach ($contacts as $c) {
				$data[] = [
					'id' => $c->id,
					'name' => $c->name,
					'email' => $c->email,
				];
			}
			return $this->response(['msg' => 'ok', 'data' => $data]);
		}
		return $this->response(['msg' => 'No es una petición AJAX']);
	}

	/**
	 * AÑADE UN NUEVO CONTACTO PARA UN SOCIO DE NEGOCIOS
	 * PETICIÓN AJAX POST: Debe incluir access_id, access_token, partner_id, name, last_name, phone, email
	 */
	public function post_add_partner_contact()
	{
		Log::debug('[Contacto] Iniciando post_add_partner_contact()');
		$msg = 'error';
		$data = [];

		// === LEE EL JSON COMO LOS OTROS MÉTODOS ===
		$json = file_get_contents('php://input');
		$post = json_decode($json, true);
		// === LOG DE LOS DATOS RECIBIDOS ===
		Log::debug('[Contacto] POST recibido: ' . json_encode(Input::post()));

		if (Input::is_ajax())
		{
			$val = Validation::forge('add_partner_contact');
			$val->add_callable('Rules');
			$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('access_token', 'access_token', 'required|min_length[1]');
			$val->add_field('partner_id', 'partner_id', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('name', 'Nombre', 'required|min_length[1]|max_length[255]');
			$val->add_field('last_name', 'Apellido', 'max_length[255]');
			$val->add_field('phone', 'Teléfono', 'max_length[255]');
			$val->add_field('email', 'Email', 'valid_email|required');
			// Más campos si requieres

			// === LOG DE ERRORES DE VALIDACIÓN ===
			if ($val->run($post))
			{
				$user = Model_User::find($val->validated('access_id'));
				if ($user && md5($user->login_hash) === $val->validated('access_token'))
				{
					$partner = Model_Partner::find($val->validated('partner_id'));
					if ($partner)
					{
						$ultimo = \Model_Partners_Contact::query()
							->where('partner_id', $partner->id)
							->order_by('id', 'desc')
							->get_one();

						$correlativo = $ultimo ? ((int) substr($ultimo->idcontact, 2)) + 1 : 1;
						$idcontact = 'CT' . str_pad($correlativo, 5, '0', STR_PAD_LEFT);

						$contact = Model_Partners_Contact::forge([
							'idcontact'    => $idcontact,
							'partner_id'   => $partner->id,
							'name'         => $val->validated('name'),
							'last_name'    => $val->validated('last_name') ?? '',
							'phone'        => $val->validated('phone') ?? '',
							'email'        => $val->validated('email'),
							'default'      => 0,
							'partner_delivery_id'      => 0,
							'cel'      => '',
							'departments'      => '',
							'deleted'      => 0,
							'created_at'      => time()
						]);
						if ($contact->save())
						{
							$partner->updated_at = time();
							$partner->save();
							$msg = 'ok';
							$data = [
								'contact' => [
									'id'        => $contact->id,
									'name'      => $contact->name,
									'last_name' => $contact->last_name,
								]
							];
						}
						else
						{
							$msg = 'Error al guardar el contacto.';
						}
					}
					else
					{
						$msg = 'Socio no encontrado.';
					}
				}
				else
				{
					$msg = 'Credenciales inválidas.';
				}
			}
			else
			{
				// === LOG DE ERRORES DE VALIDACIÓN ===
				Log::error('[Contacto] Fallo validación. Errores: ' . json_encode($val->error()));
				$msg = 'Campos inválidos o incompletos.';
			}
		}
		else
		{
			$msg = 'La petición no es AJAX.';
		}

		$this->response([
			'msg'  => $msg,
			'data' => $data
		]);
}



	/**
	 * OBTIENE TODOS LOS PRODUCTOS CON PRECIOS PARA EL SOCIO DE NEGOCIOS (SEGÚN SU type_id)
	 * PETICIÓN AJAX POST: Debe incluir access_id, access_token y partner_id
	 */
	public function post_get_partner_prices()
	{
		\Log::debug('[AJAX] Entrando a get_partner_prices()');

		if (\Input::is_ajax()) {
			// RECIBE EL JSON DEL POST
			$json = file_get_contents('php://input');
			$post = json_decode($json, true);

			$access_id    = $post['access_id']    ?? null;
			$access_token = $post['access_token'] ?? null;
			$partner_id   = $post['partner_id']   ?? null;

			\Log::info("[get_partner_prices] access_id: " . var_export($access_id, true));
			\Log::info("[get_partner_prices] access_token: " . var_export($access_token, true));
			\Log::info("[get_partner_prices] partner_id: " . var_export($partner_id, true));

			// VALIDACIÓN DE CAMPOS
			if (empty($access_id) || empty($access_token) || empty($partner_id)) {
				\Log::warning('[get_partner_prices] Faltan datos obligatorios');
				return $this->response(['msg' => 'Faltan datos obligatorios', 'products' => []]);
			}

			// VALIDAR USUARIO Y TOKEN
			$user = \Model_User::find($access_id);
			if (!$user || md5($user->login_hash) !== $access_token) {
				\Log::warning('[get_partner_prices] Token inválido o usuario no existe');
				return $this->response(['msg' => 'Token inválido', 'products' => []]);
			}

			// OBTENER EL PARTNER Y SU TYPE_ID
			$partner = \Model_Partner::find($partner_id);
			if (!$partner) {
				\Log::warning('[get_partner_prices] Socio no encontrado');
				return $this->response(['msg' => 'Socio no encontrado', 'products' => []]);
			}
			$type_id = $partner->type_id;

			// OBTENER PRODUCTOS Y PRECIOS PARA type_id
			$products = \Model_Product::query()
				->where('status', 1)
				->where('available', '>', 0)
				->get();

			$products_with_prices = [];
			foreach ($products as $product) {
				$price = Model_Products_Price::get_price($product->id, $type_id); // Usa tu método estático
				$products_with_prices[] = [
					'id'           => $product->id,
					'code'         => $product->code,
					'name'         => $product->name,
					'available'    => $product->available,
					'price'        => $price,
					'minimum_sale' => $product->minimum_sale ?? 0,

					'image'    	   => $product->image ?? '',
				];
			}

			\Log::info('[get_partner_prices] Productos enviados: ' . count($products_with_prices));
			return $this->response([
				'msg'     => 'ok',
				'products'=> $products_with_prices
			]);
		}

		\Log::warning('[get_partner_prices] No es una petición AJAX');
		return $this->response(['msg' => 'No es una petición AJAX', 'products' => []]);
	}


	/**
	 * BUSCAR DIRECCIONES DEL SOCIO DE NEGOCIOS
	 * PETICIÓN AJAX POST: Debe incluir access_id, access_token, partner_id
	 * Esta función busca las direcciones de entrega asociadas a un socio de negocios.
	 * Retorna un array con las direcciones encontradas.
	 */
	public function post_get_partner_addresses()
	{
		\Log::debug('[AJAX] Entrando a post_get_partner_addresses()');

		if (\Input::is_ajax()) {
			// Lee el JSON crudo
			$json = file_get_contents('php://input');
			$post = json_decode($json, true);

			$access_id    = $post['access_id']    ?? null;
			$access_token = $post['access_token'] ?? null;
			$partner_id   = $post['partner_id']   ?? null;

			\Log::info("[get_partner_addresses] access_id: $access_id, access_token: $access_token, partner_id: $partner_id");

			if (empty($access_id) || empty($access_token) || empty($partner_id)) {
				\Log::warning("[get_partner_addresses] Faltan datos obligatorios.");
				return $this->response(['msg' => 'Faltan datos obligatorios', 'addresses' => []]);
			}

			$user = \Model_User::find($access_id);
			if (!$user || md5($user->login_hash) !== $access_token) {
				\Log::warning("[get_partner_addresses] Token inválido o usuario no encontrado.");
				return $this->response(['msg' => 'Token inválido', 'addresses' => []]);
			}

			try {
				$addresses = \Model_Partners_Delivery::query()
					->where('partner_id', $partner_id)
					->where('deleted', 0)
					->get();

				$data = [];
				foreach ($addresses as $a) {
					$data[] = [
						'id'   => $a->id,
						'text' => trim($a->street . ' #' . $a->number
							. ($a->internal_number ? ' Int. ' . $a->internal_number : '') . ', ' . $a->colony
							. ', CP:' . $a->zipcode . ', ' . $a->city . ($a->state ? ', ' . $a->state->name : '')
						),
						'default' => $a->default
					];
				}

				\Log::info('[get_partner_addresses] Direcciones encontradas: ' . count($data));
				return $this->response(['msg' => 'ok', 'addresses' => $data]);
			} catch (\Exception $e) {
				\Log::error('[get_partner_addresses] ERROR: ' . $e->getMessage());
				return $this->response(['msg' => 'Error interno al buscar direcciones', 'addresses' => []]);
			}
		}

		\Log::warning('[get_partner_addresses] No es una petición AJAX.');
		return $this->response(['msg' => 'No es una petición AJAX', 'addresses' => []]);
	}


	/**
	 * 	BUSCAR PRODUCTOS/PRECIOS DEL SOCIO DE NEGOCIOS
	 *  PETICIÓN AJAX POST: Debe incluir access_id, access_token, partner_id, term
	 *  Esta función busca productos por nombre, código o SKU y retorna sus precios según el tipo de socio.
	 *  Retorna un máximo de 20 productos.
	 * * @param  string $term Término de búsqueda
	 * @return array        Lista de productos encontrados con precios
	 * @throws \Exception Si faltan datos obligatorios o el token es inválido
	 * BUSCAR PRODUCTOS/PRECIOS DEL SOCIO (vue-select)
	 */
	public function post_search_products_socios()
	{
		if (\Input::is_ajax()) {
			$json = file_get_contents('php://input');
			$post = json_decode($json, true);

			$access_id    = $post['access_id']    ?? null;
			$access_token = $post['access_token'] ?? null;
			$partner_id   = $post['partner_id']   ?? null;
			$term         = $post['term']         ?? null;

			if (empty($access_id) || empty($access_token) || empty($partner_id) || $term === null) {
				return $this->response(['msg' => 'Faltan datos obligatorios', 'products' => []]);
			}
			$user = \Model_User::find($access_id);
			if (!$user || md5($user->login_hash) !== $access_token) {
				return $this->response(['msg' => 'Token inválido', 'products' => []]);
			}

			// 1. Obtener el type_id del socio
			$partner = \Model_Partner::find($partner_id);
			if (!$partner) {
				return $this->response(['msg' => 'Socio no encontrado', 'products' => []]);
			}
			$type_id = $partner->type_id;

			\Log::debug("[search_products_socios] type_id del socio: $type_id");
			\Log::debug("[search_products_socios] term recibido: ".json_encode($term));

			// 2. Buscar productos por nombre/código, etc
			$term = trim($term);

	\Log::debug("[search_products_socios] Termino de búsqueda (raw): $term");

	$products = \Model_Product::query()
	//->where('deleted', 0)
		->and_where_open()
			->where('name', 'like', "%$term%")
			->or_where('code', 'like', "%$term%")
			->or_where('sku', 'like', "%$term%")
			->or_where(DB::expr('UPPER(code)'), 'like', '%' . strtoupper($term) . '%')
			->or_where(DB::expr('UPPER(sku)'), 'like', '%' . strtoupper($term) . '%')
			// Opcional: busca en codebar también
			->or_where('codebar', 'like', "%$term%")
		->and_where_close()
		->limit(20)
		->get();
		\Log::debug("[search_products_socios] SQL: " . \DB::last_query());


	if (empty($products)) {
		\Log::debug("[search_products_socios] No se encontraron productos para el término: $term");
		return $this->response(['msg' => 'No se encontraron productos', 'products' => []]);
	}

	\Log::debug("[search_products_socios] Productos encontrados: " . count($products));
	\Log::debug("[search_products_socios] Termino de búsqueda: $term");

	// ARMADO DE RESPUESTA...
	$data = [];
	foreach ($products as $p) {
		$price = \Model_Products_Price::get_price($p->id, $type_id);
		$data[] = [
			'id'           => $p->id,
			'code'         => $p->code,
			'name'         => $p->name,
			'price'        => $price,
			'available'    => $p->available,
			'minimum_sale' => $p->minimum_sale,
			//'image_url'    => $p->image_url ?? '/assets/uploads/thumb_no_image.png',
			'image'        => $p->image ?? '/assets/uploads/thumb_no_image.png'
		];
	}
	return $this->response(['msg' => 'ok', 'products' => $data]);

		}
		return $this->response(['msg' => 'No es una petición AJAX']);
	}


	/**
	 * OBTENER PRODUCTOS POR MARCA
	 * PETICIÓN AJAX POST: Debe incluir access_id, access_token, partner_id,
	 * filtro (ID de la marca)
	 * Esta función busca productos por marca y retorna sus precios según el tipo de socio.
	 * Retorna un máximo de 50 productos.
	 * @return array Lista de productos encontrados con precios
	 * @throws \Exception Si faltan datos obligatorios o el token es inválido
	 * BUSCAR PRODUCTOS POR MARCA (vue-select)
	 */
	public function post_get_partner_products_by_brand()
	{
		\Log::debug('[AJAX] Entrando a get_partner_products_by_brand()');
		if (\Input::is_ajax()) {
			$json = file_get_contents('php://input');
			$post = json_decode($json, true);

			$access_id    = $post['access_id']    ?? null;
			$access_token = $post['access_token'] ?? null;
			$partner_id   = $post['partner_id']   ?? null;
			$filtro       = $post['filtro']       ?? null;

			if (empty($access_id) || empty($access_token) || empty($partner_id) || empty($filtro)) {
				return $this->response(['msg' => 'Faltan datos obligatorios.', 'products' => []]);
			}
			$access_user = \Model_User::find($access_id);
			if ($access_user && md5($access_user->login_hash) === $access_token) {
				// Obtener type_id del socio
				$partner = \Model_Partner::find($partner_id);
				if (!$partner) {
					return $this->response(['msg' => 'Socio no encontrado.', 'products' => []]);
				}
				$type_id = $partner->type_id;

				// CONSULTA CORREGIDA: brand_id, status, deleted
				$products = \Model_Product::query()
					->where('brand_id', $filtro)
					->where('status', 1)
					->where('deleted', 0)
					->limit(50)
					->get();

				$result = [];
				foreach ($products as $prod) {
					// Traer precio por type_id
					$price = \Model_Products_Price::get_price($prod->id, $type_id);
					$result[] = [
						'id'           => $prod->id,
						'code'         => $prod->code,
						'name'         => $prod->name,
						'price'        => $price,
						//'image_url'    => $prod->image_url ?? '/assets/uploads/thumb_no_image.png',
						'image'    	   => $prod->image ?? '/assets/uploads/thumb_no_image.png',
						'minimum_sale' => $prod->minimum_sale,
						'available'    => $prod->available ?? '',
					];
				}
				return $this->response(['msg' => 'ok', 'products' => $result]);
			}
			return $this->response(['msg' => 'Credenciales inválidas.', 'products' => []]);
		}
		return $this->response(['msg' => 'No es una petición AJAX', 'products' => []]);
	}



    // =========================
    // PRODUCTOS POR RANGO DE CÓDIGOS
    // =========================
    public function post_get_partner_products_by_code_range()
	{
		if (\Input::is_ajax()) {
			$json = file_get_contents('php://input');
			$post = json_decode($json, true);

			$access_id    = $post['access_id']    ?? null;
			$access_token = $post['access_token'] ?? null;
			$partner_id   = $post['partner_id']   ?? null;
			$codigo_inicio = $post['codigo_inicio'] ?? null;
			$codigo_fin    = $post['codigo_fin'] ?? null;

			if (empty($access_id) || empty($access_token) || empty($partner_id) || empty($codigo_inicio) || empty($codigo_fin)) {
				return $this->response(['msg' => 'Faltan datos obligatorios', 'products' => []]);
			}
			$user = \Model_User::find($access_id);
			if (!$user || md5($user->login_hash) !== $access_token) {
				return $this->response(['msg' => 'Token inválido', 'products' => []]);
			}

			// 1. Obtener el type_id del socio
			$partner = \Model_Partner::find($partner_id);
			if (!$partner) {
				return $this->response(['msg' => 'Socio no encontrado', 'products' => []]);
			}
			$type_id = $partner->type_id;

			// 2. Buscar productos por rango de códigos
			$products = \Model_Product::query()
				->where('code', '>=', $codigo_inicio)
				->where('code', '<=', $codigo_fin)
				->order_by('code', 'asc')
				->limit(100) // puedes ajustar el límite
				->get();

			// 3. Armar la respuesta usando el type_id
			$data = [];
			foreach ($products as $p) {
				$price = \Model_Products_Price::get_price($p->id, $type_id);
				$data[] = [
					'id'           => $p->id,
					'code'         => $p->code,
					'name'         => $p->name,
					'price'        => $price, // SIEMPRE 0 si no hay, jamás null
					'available'    => $p->available,
					'minimum_sale' => $p->minimum_sale,
					//'image_url'    => $p->image_url ?? '/assets/uploads/thumb_no_image.png',
					'image'    => $p->image ?? '/assets/uploads/thumb_no_image.png'
				];
			}
			return $this->response(['msg' => 'ok', 'products' => $data]);
		}
		return $this->response(['msg' => 'No es una petición AJAX']);
	}

	// GUARDAR ENTREGA DESDE COTIZACIÓN
	/**
	 * Guarda una nueva entrega desde la cotización.
	 * - Valida campos básicos (menos estrictas que en pedido).
	 * - Crea el domicilio y, si se envían datos de contacto, también crea el contacto.
	 * - Devuelve el ID del domicilio creado para usar en el frontend.
	 */
	public function post_save_entrega_cotizacion()
	{
		\Log::info('[SAVE_ENTREGA_COTIZACION] INICIANDO GUARDADO DE ENTREGA DESDE COTIZACIÓN');
		$msg = '';
		$errors = [];

		$json = file_get_contents('php://input');
		$post = json_decode($json, true);

		$access_id    = $post['access_id'] ?? null;
		$access_token = $post['access_token'] ?? null;
		$partner_id   = $post['partner_id'] ?? null;
		// No hay edición en cotización, solo agregar

		$fields_entrega = ['iddelivery', 'street', 'number', 'internal_number', 'colony', 'city', 'municipality', 'state', 'zipcode', 'reception_hours', 'delivery_notes'];
		$fields_contact = ['name', 'last_name', 'phone'];
		$data = [];
		foreach (array_merge($fields_entrega, $fields_contact) as $field) {
			$data[$field] = $post[$field] ?? '';
		}

		// Validaciones mínimas para cotización (menos estrictas)
		if (empty($data['iddelivery']))   $errors[] = 'El campo Identificador es obligatorio.';
		if (empty($data['street']))       $errors[] = 'El campo Calle es obligatorio.';
		if (empty($data['number']))       $errors[] = 'El campo Número es obligatorio.';
		if (empty($data['colony']))       $errors[] = 'El campo Colonia es obligatorio.';
		if (empty($data['city']))         $errors[] = 'El campo Ciudad es obligatorio.';
		if (empty($data['zipcode']))      $errors[] = 'El campo Código Postal es obligatorio.';
		if (empty($data['reception_hours'])) $errors[] = 'El campo Horario de recepción es obligatorio.';
		// Contacto es OPCIONAL en cotización

		if ($errors) {
			\Log::error('[SAVE_ENTREGA_COTIZACION] Errores de validación: ' . implode(' | ', $errors));
			return $this->response(['msg' => 'Error en validación.', 'errors' => $errors]);
		}

		// NUEVA entrega
		$delivery = Model_Partners_Delivery::forge([
			'partner_id'      => $partner_id,
			'iddelivery'      => $data['iddelivery'],
			'street'          => $data['street'],
			'number'          => $data['number'],
			'internal_number' => $data['internal_number'],
			'colony'          => $data['colony'],
			'city'            => $data['city'],
			'municipality'    => $data['municipality'],
			'state_id'        => $data['state'],
			'zipcode'         => $data['zipcode'],
			'reception_hours' => $data['reception_hours'],
			'delivery_notes'  => $data['delivery_notes'],
			'default'         => 0,
			'deleted'         => 0
		]);
		$delivery->save();

		// Solo crea contacto si envían datos (cotización)
		if (!empty($data['name']) || !empty($data['last_name']) || !empty($data['phone'])) {
			$partner_contact = Model_Partners_Contact::forge([
				'idcontact'           => 'ENT-' . strtoupper($data['iddelivery']),
				'partner_id'          => $partner_id,
				'partner_delivery_id' => $delivery->id,
				'name'                => $data['name'],
				'last_name'           => $data['last_name'],
				'phone'               => $data['phone'],
				'cel'                 => '',
				'email'               => '',
				'departments'         => '',
				'default'             => 1,
				'deleted'             => 0
			]);
			$partner_contact->save();
		}

		$msg = 'ok';
		\Log::info('[SAVE_ENTREGA_COTIZACION] Entrega y (si aplica) contacto creados correctamente.');

		// Devuelve el domicilio para seleccionar en el frontend
		return $this->response([
			'msg' => $msg,
			'id' => $delivery->id,
			'text' => $delivery->street . ' #' . $delivery->number . ', ' . $delivery->colony . ', ' . $delivery->city,
			'errors' => $errors
		]);
	}



	/**
	 * FINALIZAR COTIZACION
	 *
	 * - Valida productos, precios, cantidades mínimas, descuentos y retenciones.
	 * - Permite guardar aunque no haya domicilio, contacto, comentarios u observaciones.
	 * - Al finalizar, genera la cotización, crea los registros asociados y devuelve el ID para imprimir/enviar.
	 * - Deja advertencias si faltan datos opcionales.
	 * - TODO: Generar correo y botón de envío después de guardar.
	 */
	public function post_finalizar_cotizacion()
	{
		$msg = 'error';
		$response_data = [];
		$warnings = [];

		// =====================
		// LEE JSON DEL REQUEST
		// =====================
		$json = file_get_contents('php://input');
		$post = json_decode($json, true);
		\Log::debug('[Cotización] POST recibido: ' . json_encode($post));

		\Log::debug('[Cotización] Iniciando método post_finalizar_cotizacion');

		if (!Input::is_ajax()) {
			$this->response(['msg' => 'Petición no válida', 'data' => []]);
			return;
		}

		// VALIDAR CAMPOS BÁSICOS
		$val = Validation::forge('finalizar_cotizacion');
		$val->add_callable('Rules');
		$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
		$val->add_field('access_token', 'access_token', 'required|min_length[1]');
		$val->add_field('partner_id', 'partner_id', 'required|valid_string[numeric]|numeric_min[1]');
		$val->add_field('payment_id', 'payment_id', 'valid_string[numeric]');
		$val->add_field('address_id', 'address_id', 'valid_string[numeric]');
		$val->add_field('seller_asig_id', 'seller_asig_id', 'valid_string[numeric]');
		$val->add_field('tax_datum', 'tax_datum', 'valid_string[numeric]');
		$val->add_field('products', 'products', 'required');
		$val->add_field('partner_contact_id', 'partner_contact_id', 'valid_string[numeric]');
		$val->add_field('reference', 'reference', 'max_length[100]');
		$val->add_field('valid_date', 'valid_date', 'max_length[100]');
		$val->add_field('comments', 'comments', 'max_length[255]');

		if (!$val->run($post)) {
			\Log::debug('[Cotización] Error de validación.', $val->error());
			$this->response(['msg' => 'Parámetros inválidos: '.json_encode($val->error()), 'data' => []]);
			return;
		}



		// ====================
		// VALIDACIÓN DE USUARIO
		// ====================
		$access_id = $val->validated('access_id');
		$access_token = $val->validated('access_token');
		$check_access = Model_User::query()->where('id', $access_id)->get_one();

		if (!$check_access || md5($check_access->login_hash) != $access_token) {
			\Log::debug('[Cotización] Usuario inválido.');
			$this->response(['msg' => 'Acceso inválido.', 'data' => []]);
			return;
		}

		// ====================
		// OBTENER DATOS DEL FORMULARIO
		// ====================
		$partner_id 		= $val->validated('partner_id');
		$payment_id 		= $val->validated('payment_id') ?: null;
		$seller_asig_id 	= $val->validated('seller_asig_id') ?: null;
		$address_id 		= $val->validated('address_id') ?: null;
		$tax_datum_id 		= $val->validated('tax_datum') ?: null;
		$partner_contact_id = $val->validated('partner_contact_id') ?: null;
		$reference 			= $val->validated('reference');
		$valid_date 		= $val->validated('valid_date');
		$comments 			= $val->validated('comments');
		$products_json 		= Input::post('products');
		$products 			= json_decode($products_json, true);

		// ===========================================
		// DECODIFICAR PRODUCTS: SIEMPRE VIENE COMO STRING JSON
		// ===========================================
		$products_json = $post['products']; // OJO: NO uses Input::post aquí
		$products = json_decode($products_json, true);
		if (!is_array($products) || empty($products)) {
			\Log::debug('[Cotización] No se recibieron productos válidos.');
			$this->response(['msg' => 'No se recibieron productos válidos.', 'data' => []]);
			return;
		}

		// VALIDACIÓN DE PARTIDAS
		if (!is_array($products) || empty($products)) {
			\Log::debug('[Cotización] No se recibieron productos válidos.');
			$this->response(['msg' => 'No se recibieron productos válidos.', 'data' => []]);
			return;
		}

		// ====================
		// VALIDAR PARTIDAS: PRECIOS, DESCUENTOS, REPETIDOS, MINIMOS
		// ====================
		$ids_seen = [];
		foreach ($products as &$product) {
			if (empty($product['id'])) continue;

			// Validar precio
			if (!isset($product['unit_price']) && isset($product['price'])) {
				$product['unit_price'] = $product['price'];
			}
			$precio = isset($product['unit_price']) ? floatval($product['unit_price']) : (isset($product['price']) ? floatval($product['price']) : 0);
			$cantidad = isset($product['quantity']) ? intval($product['quantity']) : 0;

			if ($precio <= 0) {
				$this->response([
					'msg' => 'error',
					'data' => [],
					'message' => 'No puedes cotizar productos con precio 0: ' . ($product['name'] ?? '')
				]);
				return;
			}

			// Validar cantidad mínima (si aplica)
			$min_sale = 0;
			$product_db = Model_Product::find($product['id']);
			if ($product_db && $product_db->minimum_sale > 0) {
				$min_sale = intval($product_db->minimum_sale);
				if ($cantidad < $min_sale) {
					$this->response([
						'msg' => 'error',
						'data' => [],
						'message' => 'La cantidad mínima para "' . $product_db->name . '" es: ' . $min_sale
					]);
					return;
				}
			}

			// Sumar cantidades si hay productos repetidos
			if (in_array($product['id'], $ids_seen)) {
				foreach ($products as &$p) {
					if ($p['id'] == $product['id'] && $p !== $product) {
						$p['quantity'] += $cantidad;
						unset($products[$k]);
					}
				}
			}
			$ids_seen[] = $product['id'];
		}
		unset($product);

		// ====================
		// VALIDACIONES FLEXIBLES: CAMPOS OPCIONALES
		// ====================
		if (!$partner_contact_id) $warnings[] = 'No se capturó contacto.';
		if (!$address_id) $warnings[] = 'No se capturó domicilio.';
		if (!$comments) $warnings[] = 'No se registraron observaciones.';
		if (!$reference) $warnings[] = 'No se capturó referencia.';

		// ====================
		// VALIDAR/MANEJAR CLIENTE Y MÉTODO DE PAGO
		// ====================
		$partner = Model_Partner::find($partner_id);
		if (!$partner) {
			$this->response(['msg' => 'Socio no encontrado.', 'data' => []]);
			return;
		}

		$payment_method = null;
		if ($payment_id) $payment_method = Model_Payments_Method::find($payment_id);

		// ====================
		// DOMICILIO DE ENTREGA (CREA VACIO SI NO HAY)
		// ====================
		$quotes_address = null;
		if ($address_id) {
			$address = Model_Partners_Delivery::query()
				->where('id', $address_id)
				->where('partner_id', $partner_id)
				->get_one();
			if ($address) {
				$quotes_address = Model_Quotes_Address::forge([
					'state_id'        => $address->state_id,
					'name'            => $address->partner->name,
					'last_name'       => '',
					'phone'           => '',
					'street'          => $address->street,
					'number'          => $address->number,
					'internal_number' => $address->internal_number,
					'colony'          => $address->colony,
					'zipcode'         => $address->zipcode,
					'city'            => $address->city,
					'details'         => $address->delivery_notes,
					'created_at'      => time(),
				]);
			}
		}
		if (!$quotes_address) {
			$quotes_address = Model_Quotes_Address::forge([
				'state_id'        => 0,
				'name'            => 'Cotización sin domicilio',
				'last_name'       => '',
				'phone'           => '',
				'street'          => '',
				'number'          => '',
				'internal_number' => '',
				'colony'          => '',
				'zipcode'         => '',
				'city'            => '',
				'details'         => '',
				'created_at'      => time(),
			]);
		}
		$quotes_address->save();

		// ====================
		// GENERAR LA COTIZACION
		// ====================
		// 1. Intentar usar Auth
		$user_id = Auth::get('id');
		$employee = $user_id ? Model_Employee::query()->where('user_id', $user_id)->get_one() : null;

		// 2. Fallback: si viene desde Vue, úsalo
		$employee_id = $employee ? $employee->id : ($post['employee_id'] ?? 0);

		if (!$employee_id) {
			\Log::warning('[Cotización] employee_id no encontrado, usando 0 por fallback');
		}
		$valid_date_ts = $valid_date ? strtotime(str_replace('/', '-', $valid_date)) : null;

		$new_quote = Model_Quote::forge([
			'partner_id'         => $partner_id,
			'employee_id'        => $employee_id,
			'seller_asig_id'     => $seller_asig_id,
			'partner_contact_id' => $partner_contact_id,
			'payment_id'         => '', // se asocia después
			'address_id'         => $quotes_address->id,
			'status'             => 0,
			'total'              => 0,
			'discount'           => 0,
			'valid_date'         => $valid_date_ts,
			'reference'          => $reference,
			'comments'           => $comments,
			'admin_updated'      => 0,
			'docnum'             => null,
			'created_at'         => time(),
		]);
		$new_quote->save();

		// ====================
		// ASOCIAR MÉTODO DE PAGO
		// ====================
		$new_payment = null;
		if ($payment_method) {
			$new_payment = Model_Quotes_Payment::set_new_record([
				'type_id' => $payment_method->id,
				'token'   => $payment_method->name,
				'total'   => 0
			]);
			if ($new_payment) {
				$new_quote->payment_id = $new_payment->id;
				$new_quote->save();
			}
		}

		// ====================
		// DATOS FISCALES (OPCIONAL)
		// ====================
		if (empty($tax_datum_id)) {
			$tax_datum = Model_Partners_Tax_Datum::query()
				->where('partner_id', $partner_id)
				->order_by('default', 'desc')
				->get_one();
			$tax_datum_id = $tax_datum ? $tax_datum->id : null;
			if ($tax_datum) {
				$new_tax_datum = Model_Quotes_Tax_Datum::forge([
					'quote_id'           => $new_quote->id,
					'payment_method_id'  => $tax_datum->payment_method_id,
					'cfdi_id'            => $tax_datum->cfdi_id,
					'sat_tax_regime_id'  => $tax_datum->sat_tax_regime_id,
					'state_id'           => $tax_datum->state_id,
					'business_name'      => $tax_datum->business_name,
					'rfc'                => $tax_datum->rfc,
					'street'             => $tax_datum->street,
					'number'             => $tax_datum->number,
					'internal_number'    => $tax_datum->internal_number,
					'colony'             => $tax_datum->colony,
					'zipcode'            => $tax_datum->zipcode,
					'city'               => $tax_datum->city,
					'csf'                => $tax_datum->csf,
				]);
				$new_tax_datum->save();
			}
		}

		// ====================
		// REGISTRAR PARTIDAS (CON DESCUENTO Y RETENCIÓN SI APLICA)
		// ====================
		$total_quote = 0;
		// ✅ Nueva variable para acumular el descuento total en moneda
		$total_descuento = 0;

		foreach ($products as $product) {
			    // 🔹 Normalizar product_id (acepta tanto "product_id" como "id")
				$pid = $product['product_id'] ?? $product['id'] ?? null;

				// 🔹 Si aún no hay pid, buscar por code
				if (!$pid && !empty($product['code'])) {
					$p = Model_Product::query()->where('code', $product['code'])->get_one();
					$pid = $p ? $p->id : null;
				}

				if (!$pid) {
					\Log::error('[Cotización] Producto inválido, no tiene id ni product_id. Datos: ' . json_encode($product));
					continue; // ⚠️ No rompe, solo salta el producto problemático
				}
			// Extraer campos
			$qty 		= isset($product['quantity']) ? intval($product['quantity']) : 0;
			$price 		= isset($product['unit_price']) ? floatval($product['unit_price']) : (isset($product['price']) ? floatval($product['price']) : 0);
			$discount 	= isset($product['discount']) ? floatval($product['discount']) : 0; // %
			$retention 	= isset($product['retention']) ? floatval($product['retention']) : 0; // %

			// Calcular importe
			$subtotal = $qty * $price;
			$desc = $discount > 0 ? ($subtotal * ($discount / 100)) : 0;
			$ret = $retention > 0 ? ($subtotal * ($retention / 100)) : 0;
			$total = $subtotal - $desc - $ret;


			// ✅ Acumular el descuento monetario de esta partida
			$total_descuento += $desc;

			$total_quote += $total;

			Model_Quotes_Product::forge([
				'quote_id'    => $new_quote->id,
				'product_id'  => $pid,
				'quantity'    => $qty,
				'price'       => number_format($price, 2, '.', ''),
				'deleted'     => 0,
				'discount'    => $discount,
				'retention'   => $retention,
				'total'       => number_format($total, 2, '.', ''),
			])->save();

			 \Log::debug('[Cotización] Producto agregado a la cotización. product_id=' . $pid . ' qty=' . $qty);
		}


		// ====================
		// ACTUALIZAR TOTALES
		// ====================
		$new_quote->total = $total_quote;
		$new_quote->discount = $total_descuento; // ✅ Asignación del descuento total en moneda
		if ($new_payment) $new_payment->total = $total_quote;
		$new_quote->save();
		if ($new_payment) $new_payment->save();

		// ====================
		// RESPUESTA FINAL
		// ====================
		$msg = 'ok';
		$response_data = [
			'redirect' => 'reload',
			'quote_id' => $new_quote->id,
			'warnings' => $warnings,
		];

		\Log::debug('[Cotización] Cotización finalizada correctamente. ID: ' . $new_quote->id);

		// ========================================
		// SI FUE UNA COTIZACIÓN PENDIENTE, MARCARLA COMO PROCESADA
		// ========================================
		\Log::debug('[Cotización] Pending ID recibido: ' . ($post['pending_id'] ?? 'null'));
		if (!empty($post['pending_id']) && is_numeric($post['pending_id'])) {
		$cotPend = Model_Quotes_Partner::find($post['pending_id']);
		if ($cotPend && $cotPend->status == 0) {
			$cotPend->status = 1;
			$cotPend->save();
			\Log::info('Cotización pendiente actualizada correctamente: ID ' . $cotPend->id);
		}
	}

		$this->response([
			'msg'  => $msg,
			'data' => $response_data,
		]);
	}


	/**
	 * FINALIZAR EDICIÓN DE COTIZACIÓN
	 * - Permite editar una cotización existente.
	 * - Valida campos requeridos y opcionales.
	 */
	public function post_finalizar_edicion()
	{
		$msg = 'error';
		$response_data = [];
		$warnings = [];

		// =====================
		// 1. LEE JSON DEL REQUEST Y VALIDA PETICIÓN
		// =====================
		if (!Input::is_ajax()) {
			$this->response(['msg' => 'Petición no válida', 'data' => []]);
			return;
		}

		$json = file_get_contents('php://input');
		$post = json_decode($json, true);
		\Log::debug('[Cotización] POST de edición recibido: ' . json_encode($post));
		\Log::debug('[Cotización] Iniciando método post_finalizar_edicion');

		// =====================
		// 2. VALIDACIÓN DE CAMPOS REQUERIDOS
		// =====================
		$val = Validation::forge('finalizar_edicion');
		$val->add_callable('Rules');
		$val->add_field('access_id', 'access_id', 'required|valid_string[numeric]|numeric_min[1]');
		$val->add_field('access_token', 'access_token', 'required|min_length[1]');
		$val->add_field('quote_id', 'quote_id', 'required|valid_string[numeric]|numeric_min[1]');
		$val->add_field('partner_id', 'partner_id', 'required|valid_string[numeric]|numeric_min[1]');
		$val->add_field('products', 'products', 'required');
		$val->add_field('partner_contact_id', 'partner_contact_id', 'valid_string[numeric]');
		$val->add_field('reference', 'reference', 'max_length[100]');
		$val->add_field('valid_date', 'valid_date', 'max_length[100]');
		$val->add_field('comments', 'comments', 'max_length[255]');
		$val->add_field('payment_id', 'payment_id', 'valid_string[numeric]');
		$val->add_field('address_id', 'address_id', 'valid_string[numeric]');
		$val->add_field('seller_asig_id', 'seller_asig_id', 'valid_string[numeric]');

		if (!$val->run($post)) {
			\Log::debug('[Cotización] Error de validación en edición.', $val->error());
			$this->response(['msg' => 'Parámetros inválidos: '.json_encode($val->error()), 'data' => []]);
			return;
		}

		// =====================
		// 3. OBTENER DATOS VALIDADOS Y BUSCAR LA COTIZACIÓN
		// =====================
		$access_id = $val->validated('access_id');
		$access_token = $val->validated('access_token');
		$quote_id = $val->validated('quote_id');
		$partner_id = $val->validated('partner_id');
		$valid_date = $val->validated('valid_date');
		$comments = $val->validated('comments');
		$address_id = $val->validated('address_id');
		$payment_id = $val->validated('payment_id');
		$seller_asig_id = $val->validated('seller_asig_id');
		$partner_contact_id = $val->validated('partner_contact_id');
		$reference = $val->validated('reference');
		$products_json = $post['products'];
		$products = json_decode($products_json, true);

		// =====================
		// 4. VALIDAR ACCESO Y CARGAR LA COTIZACIÓN
		// =====================
		$check_access = Model_User::query()->where('id', $access_id)->get_one();
		if (!$check_access || md5($check_access->login_hash) != $access_token) {
			$this->response(['msg' => 'Acceso inválido.', 'data' => []]);
			return;
		}

		$quote = Model_Quote::find($quote_id);
		if (!$quote) {
			$this->response(['msg' => 'Cotización no encontrada para edición.', 'data' => []]);
			return;
		}

// =====================
// 5. VALIDACIÓN DE PRODUCTOS
// =====================
if (!is_array($products) || empty($products)) {
    \Log::debug('[Cotización] No se recibieron productos válidos.');
    $this->response(['msg' => 'No se recibieron productos válidos.', 'data' => []]);
    return;
}

$ids_seen = [];
foreach ($products as &$product) {

    // Usamos product_id real (del catálogo) si existe
    $pid = $product['product_id'] ?? null;

    // Fallback: si no viene product_id, intenta buscarlo por code
    if (!$pid && !empty($product['code'])) {
        $p = Model_Product::query()->where('code', $product['code'])->get_one();
        $pid = $p ? $p->id : null;
        $product['product_id'] = $pid; // lo guardamos para que esté normalizado
    }

    if (!$pid) {
        \Log::error('[Cotización][Validación] Producto sin product_id válido. Datos: ' . json_encode($product));
        continue; // lo saltamos
    }

    // Normalizamos el precio
    if (!isset($product['unit_price']) && isset($product['price'])) {
        $product['unit_price'] = $product['price'];
    }

    $precio   = isset($product['unit_price']) ? floatval($product['unit_price']) : 0;
    $cantidad = isset($product['quantity']) ? intval($product['quantity']) : 0;

    if ($precio <= 0) {
        $this->response([
            'msg' => 'error',
            'data' => [],
            'message' => 'No puedes cotizar productos con precio 0: ' . ($product['name'] ?? '')
        ]);
        return;
    }

    // Validamos cantidad mínima desde catálogo
    $product_db = Model_Product::find($pid);
    if ($product_db && $product_db->minimum_sale > 0) {
        $min_sale = intval($product_db->minimum_sale);
        if ($cantidad < $min_sale) {
            $this->response([
                'msg' => 'error',
                'data' => [],
                'message' => 'La cantidad mínima para "' . $product_db->name . '" es: ' . $min_sale
            ]);
            return;
        }
    }

    // Evitar duplicados (ahora usando product_id real)
    if (in_array($pid, $ids_seen)) {
        foreach ($products as &$p) {
            if (($p['product_id'] ?? null) == $pid && $p !== $product) {
                $p['quantity'] += $cantidad;
                $product['quantity'] = 0;
            }
        }
    }

    $ids_seen[] = $pid;
}
unset($product);


		// ===========================================
		// 6. OBTENER ID DEL EMPLEADO ASOCIADO AL USUARIO LOGUEADO
		// ===========================================
		$user_id = Auth::get('id');
		$employee = Model_Employee::query()->where('user_id', $user_id)->get_one();

		if ($employee && isset($employee->id)) {
			$employee_id = $employee->id;
			\Log::debug('[Cotización][Edición] Empleado encontrado: ' . $employee_id);
		} else {
			$employee_id = null;
			\Log::debug('[Cotización][Edición] No se encontró empleado para user_id: ' . $user_id);
		}

		// ====================
		// 7. ACTUALIZAR COTIZACIÓN
		// ====================
		$valid_date_ts = $valid_date ? strtotime(str_replace('/', '-', $valid_date)) : null;

		$quote->partner_id = $partner_id;
		$quote->employee_id = $employee_id;
		$quote->seller_asig_id = $seller_asig_id;
		$quote->partner_contact_id = $partner_contact_id;
		$quote->address_id = $address_id;
		$quote->valid_date = $valid_date_ts;
		$quote->reference = $reference;
		$quote->comments = $comments;
		$quote->admin_updated = 1;
		$quote->updated_at = time();

		// Compatibilidad: si viene como employee_id desde Vue, úsalo como seller_asig_id
if (empty($seller_asig_id) && Input::post('employee_id')) {
    $seller_asig_id = Input::post('employee_id');
    \Log::debug('[Cotización][Edición] Mapeando employee_id → seller_asig_id: ' . $seller_asig_id);
}

		// ====================
		// 8. ACTUALIZAR O CREAR DOMICILIO DE ENTREGA
		// ====================
		$quotes_address = null;
		if ($address_id) {
			$address = Model_Partners_Delivery::query()->where('id', $address_id)->where('partner_id', $partner_id)->get_one();
			if ($address) {
				$quotes_address = Model_Quotes_Address::find($quote->address_id);
				if ($quotes_address) {
					$quotes_address->set([
						'state_id' => $address->state_id,
						'name' => $address->partner->name,
						'street' => $address->street,
						'number' => $address->number,
						'internal_number' => $address->internal_number,
						'colony' => $address->colony,
						'zipcode' => $address->zipcode,
						'city' => $address->city,
						'details' => $address->delivery_notes,
						'updated_at' => time(),
					])->save();
				} else {
					$quotes_address = Model_Quotes_Address::forge([
						'state_id' => $address->state_id,
						'name' => $address->partner->name,
						'last_name' => '',
						'phone' => '',
						'street' => $address->street,
						'number' => $address->number,
						'internal_number' => $address->internal_number,
						'colony' => $address->colony,
						'zipcode' => $address->zipcode,
						'city' => $address->city,
						'details' => $address->delivery_notes,
						'created_at' => time(),
					])->save();
					$quote->address_id = $quotes_address->id;
				}
			}
		} else {
			$quote->address_id = null;
		}
		$quote->save();

		// ====================
		// 9. ACTUALIZAR O CREAR MÉTODO DE PAGO
		// ====================
		if ($payment_id) {
			$payment_method = Model_Payments_Method::find($payment_id);
			if ($payment_method) {
				$quotes_payment = Model_Quotes_Payment::find($quote->payment_id);
				if ($quotes_payment) {
					$quotes_payment->set([
						'type_id' => $payment_method->id,
						'token' => $payment_method->name,
					])->save();
				} else {
					$new_payment = Model_Quotes_Payment::set_new_record([
						'type_id' => $payment_method->id,
						'token' => $payment_method->name,
						'total' => 0
					]);
					if ($new_payment) {
						$quote->payment_id = $new_payment->id;
						$quote->save();
					}
				}
			}
		} else {
			$quote->payment_id = null;
		}

		// ====================
// 10. ACTUALIZAR PARTIDAS: UPDATE / INSERT / DELETE CONTROLADO
// ====================
$productos_actuales = Model_Quotes_Product::query()
    ->where('quote_id', $quote->id)
    ->where('deleted', 0)
    ->get();

$ids_actuales  = array_map(fn($p) => $p->id, $productos_actuales); // IDs en quotes_products
$ids_recibidos = [];

$total_quote     = 0;
$total_descuento = 0;

foreach ($products as $product) {
    $qty       = intval($product['quantity'] ?? 0);
    $price     = floatval($product['unit_price'] ?? $product['price'] ?? 0);
    $discount  = floatval($product['discount'] ?? 0);
    $retention = floatval($product['retention'] ?? 0);

    // Calcular totales
    $subtotal = $qty * $price;
    $desc     = $discount > 0 ? ($subtotal * ($discount / 100)) : 0;
    $ret      = $retention > 0 ? ($subtotal * ($retention / 100)) : 0;
    $total    = $subtotal - $desc - $ret;

    $total_descuento += $desc;
    $total_quote     += $total;

    // Caso 1: actualizar producto existente (según quote_product_id)
    if (!empty($product['quote_product_id'])) {
        $existing_product = Model_Quotes_Product::find($product['quote_product_id']);
        if ($existing_product) {
            $existing_product->quantity   = $qty;
            $existing_product->price      = number_format($price, 2, '.', '');
            $existing_product->discount   = $discount;
            $existing_product->retention  = $retention;
            $existing_product->total      = number_format($total, 2, '.', '');
            $existing_product->updated_at = time();
            $existing_product->save();
            $ids_recibidos[] = $existing_product->id;
            \Log::debug('[Cotización][Edición] Producto actualizado quote_product_id=' . $existing_product->id);
        }
    }
    // Caso 2: insertar producto nuevo
    else {
        $pid = $product['product_id'] ?? null;

        // fallback: si no viene product_id, buscar por code
        if (!$pid && !empty($product['code'])) {
            $p = Model_Product::query()->where('code', $product['code'])->get_one();
            $pid = $p ? $p->id : null;
        }

        if ($pid) {
            $new_product = Model_Quotes_Product::forge([
                'quote_id'     => $quote->id,
                'product_id'   => $pid,
                'quantity'     => $qty,
                'price'        => number_format($price, 2, '.', ''),
                'discount'     => $discount,
                'retention'    => $retention,
                'total'        => number_format($total, 2, '.', ''),
                'deleted'      => 0,
                'created_at'   => time(),
                'updated_at'   => time(),
            ]);
            $new_product->save();
            $ids_recibidos[] = $new_product->id;
            \Log::debug('[Cotización][Edición] Producto insertado con product_id=' . $pid);
        } else {
            \Log::error('[Cotización][Edición] No se pudo insertar producto: faltó product_id y no se encontró por code');
        }
    }
}

// ==========================================
// ELIMINAR PRODUCTOS QUE YA NO VIENEN
// ==========================================
$ids_eliminados = array_diff($ids_actuales, $ids_recibidos);
foreach ($ids_eliminados as $id) {
    $qp = Model_Quotes_Product::find($id);
    if ($qp) {
        $qp->deleted    = 1;
        $qp->updated_at = time();
        $qp->save();
        \Log::debug('[Cotización][Edición] Producto marcado como eliminado ID ' . $id);
    }
}




		// ====================
		// 11. ACTUALIZAR TOTALES Y GUARDAR
		// ====================
		$quote->total = $total_quote;
		$quote->discount = $total_descuento;
		$quote->save();

		// ====================
		// 12. RESPUESTA FINAL
		// ====================
		$msg = 'ok';
		$response_data = [
			'redirect' => 'reload',
			'quote_id' => $quote->id,
			'warnings' => $warnings,
		];

		$this->response([
			'msg' => $msg,
			'data' => $response_data,
		]);
	}

	/**
	 * IMPRIMIR COTIZACIÓN
	 *
	 * MUESTRA LA COTIZACIÓN EN FORMATO IMPRIMIBLE Y ABRE UNA VISTA LIMPIA PARA PDF/IMPRESIÓN
	 * @param int $id
	 * @return Response
	 */
	public function action_imprimir($id = null)
	{
		// VALIDAR ID
		is_null($id) and \Response::redirect('admin/cotizaciones');

		// LOG INICIO
		\Log::info("[action_imprimir] ID de cotización recibido: $id");

		// BUSCAR LA COTIZACIÓN CON TODAS SUS RELACIONES
		$quote = \Model_Quote::find($id, [
   		 'related' => ['partner', 'products', 'products.product', 'employee', 'contact', 'address', 'payment']
		]);

		// Filtrar los productos eliminados manualmente
		if ($quote && !empty($quote->products)) {
			$quote->products = array_filter($quote->products, function($p) {
				return empty($p->deleted) || $p->deleted == 0;
			});
		}


		if (!$quote) {
			\Log::error("[action_imprimir] Cotización no encontrada para ID: $id");
			\Session::set_flash('error', 'Cotización no encontrada.');
			return \Response::redirect('admin/cotizaciones');
		}

		$data = [];
		$data['quote'] = $quote;

		// OPCIONAL: PREPARA COMENTARIOS, CAMPOS EXTRA, ETC.
		$data['comments'] = $quote->comments ?? '';
		$data['vendedor'] = $quote->employee ? $quote->employee->name : '—';

		\Log::info("[action_imprimir] Cotización encontrada y enviada a la vista: $id");

		return \Response::forge(\View::forge('admin/cotizaciones/imprimir', $data));
	}



	/**
	 * ENVIAR COTIZACIÓN POR CORREO
	 * @access public
	 * @return Response (JSON)
	 */
	public function post_enviar_correo_cotizacion()
	{
		$msg = 'error';
		$response = [];

		if (Input::is_ajax()) {
			$val = Validation::forge('enviar_correo');
			$val->add_field('access_id', 'access_id', 'required');
			$val->add_field('access_token', 'access_token', 'required');
			$val->add_field('quote_id', 'quote_id', 'required|valid_string[numeric]|numeric_min[1]');
			if ($val->run()) {
				$access_id = $val->validated('access_id');
				$access_token = $val->validated('access_token');
				$quote_id = $val->validated('quote_id');

				$user = Model_User::find($access_id);
				if ($user && md5($user->login_hash) == $access_token) {
					// LLAMA TU FUNCIÓN PRIVADA DE ENVÍO
					$resultado = $this->send_partner_mail($quote_id);
					if ($resultado) {
						$msg = 'ok';
					} else {
						$msg = 'No se pudo enviar el correo. Verifica que el socio tenga correo asignado y productos en la cotización.';
					}
				} else {
					$msg = 'Credenciales inválidas.';
				}
			} else {
				$msg = 'Datos inválidos.';
			}
		}
		$this->response(['msg' => $msg, 'data' => $response]);
	}


	/** * CARGAR DATOS PARA EDITAR COTIZACIÓN
	 * POST: get_cotizacion_editar
	 * Carga los datos necesarios para editar una cotización existente.
	 * - Valida el acceso del usuario.
	 * - Carga la cotización y sus productos.
	 * - Devuelve los datos estructurados para el frontend.
	 * @param int $quote_id ID de la cotización a editar.
	 * @param int $access_id ID del usuario que solicita la edición.
	 * @param string $access_token Token de acceso del usuario.
	 * @return Response JSON con los datos de la cotización y productos.
	 *
	 * @access public
	 * @return Response (JSON)
	 * */
	public function post_get_cotizacion_editar()
	{
		\Log::debug('[Editar Cotización] Iniciando carga de datos para edición...');

		// === VALIDAR PARÁMETROS DE ACCESO ===
		$quote_id     = Input::post('quote_id');
		$access_id    = Input::post('access_id');
		$access_token = Input::post('access_token');

		\Log::debug("[Editar Cotización] quote_id: $quote_id");
		\Log::debug("[Editar Cotización] access_id: $access_id");
		\Log::debug("[Editar Cotización] access_token: $access_token");

		// === VALIDAR USUARIO ===
		$usuario = Model_User::find($access_id);
		if (!$usuario || md5($usuario->login_hash) != $access_token) {
			\Log::debug("[Editar Cotización] Acceso inválido.");
			return $this->response(['msg' => 'Acceso inválido']);
		}

		\Log::debug("[Editar Cotización] Usuario autenticado: {$usuario->username}");

		// === VALIDAR COTIZACIÓN ===
		$cotizacion = Model_Quote::find($quote_id);
		if (!$cotizacion) {
			\Log::debug("[Editar Cotización] Cotización no encontrada con ID: $quote_id");
			return $this->response(['msg' => 'Cotización no encontrada']);
		}

		\Log::debug("[Editar Cotización] Cotización encontrada. ID: {$cotizacion->id}");

		// === CARGAR PRODUCTOS DE LA COTIZACIÓN ===
		$productos = [];
		foreach ($cotizacion->products as $product) {
			$p = $product->product;

			$productos[] = [
				'id'         => $p->id,
				'code'       => $p->code,
				'name'       => $p->name,
				'image'      => $p->image,
				'unit_price' => floatval($product->price),
				'discount'   => floatval($product->discount),
				'retention'  => floatval($product->retention),
				'quantity'   => intval($product->quantity),
			];
		}

		$cotizacion->productos = Model_Quotes_Product::query()
		->related('product')
		->where('quote_id', $cotizacion->id)
		->get();


		\Log::debug("[Editar Cotización] Productos cargados: " . count($productos));

		// === ESTRUCTURAR DATOS DE COTIZACIÓN ===
		$cotizacionData = [
			'id'                  => $cotizacion->id,
			'partner_id'          => $cotizacion->partner_id,
			'reference'           => $cotizacion->reference,
			'valid_date'          => date('Y-m-d', $cotizacion->valid_date),
			'comments'            => $cotizacion->comments,
			'payment_id'          => $cotizacion->payment_id,
			'address_id'          => $cotizacion->address_id,
			'partner_contact_id'  => $cotizacion->partner_contact_id,
		];

		// === CARGAR CATÁLOGOS RELACIONADOS ===
		$monedas     = Model_Currency::find('all');
		$pagos       = Model_Payments_Method::find('all');
		$estados     = Model_State::find('all');
		$retenciones = Model_Retention::find('all');
		$impuestos   = Model_Tax::find('all');

		\Log::debug("[Editar Cotización] Catálogos cargados:");
		\Log::debug(" - Monedas: " . count($monedas));
		\Log::debug(" - Pagos: " . count($pagos));
		\Log::debug(" - Estados: " . count($estados));
		\Log::debug(" - Retenciones: " . count($retenciones));
		\Log::debug(" - Impuestos: " . count($impuestos));


		// === CARGAR CONTACTO DE LA COTIZACIÓN ===
	// === CARGAR CONTACTO DE LA COTIZACIÓN SI EXISTE ===
	$contacto = null;
	if (!empty($cotizacion->partner_contact_id)) {
		$contacto_model = Model_Partners_Contact::find($cotizacion->partner_contact_id);
		if ($contacto_model) {
			$contacto = [
				'id'         => $contacto_model->id,
				'name'       => $contacto_model->name,
				'last_name'  => $contacto_model->last_name,
				'email'      => $contacto_model->email
			];
		}
	}



		// === RESPUESTA ===
		\Log::debug("[Editar Cotización] Enviando respuesta final...");

		return $this->response([
			'msg'  => 'ok',
			'data' => [
				'cotizacion'  		=> $cotizacionData,
				'productos'   		=> $productos,
				'monedas'     		=> $monedas,
				'pagos'       		=> $pagos,
				'estados'     		=> $estados,
				'retenciones' 		=> $retenciones,
				'impuestos'   		=> $impuestos,
				'contacto'       => $contacto,
			]
		]);
	}


		/**
   	 * ADD QUOTE
   	 *
   	 * AGREGA UN PRODUCTO AL CARRITO
   	 *
   	 * @access  public
   	 * @return  Object
   	 */
	public function post_add_product_quote()
		{
			# SE OBTIENE LOS DATOS ENVIADOS POR AJAX
			$product_id = Input::json('idProduct');
			$quantity   = Input::json('quantity');

			# SE INICIALIZAN LOS ARREGLOS
			$msg                     = 'error';
			$quote_data               = array();
			$quote_unavailable        = array();
			$total_products_quantity = 0;

			# SE ESTABLECEN LAS REGLAS DE VALIDACION
			$val = Validation::forge();
			$val->add_field('id_product', 'Id producto', 'required|numeric_min[1]|valid_string[numeric]');
			$val->add_field('quantity', 'Cantidad', 'required|numeric_min[1]|valid_string[numeric]');

			# SI LA VALIDACION ES CORRECTA
			if($val->run(array(
				'id_product' => $product_id,
				'quantity'   => $quantity
			))){
				# SE FORMATEAN NUMEROS A SOLO ENTEROS
				$product_id = (int)$product_id;
				$quantity   = (int)$quantity;

				# SE BUSCA EL PRODUCTO
				$product = Model_Product::get_valid(array('id_product' => $product_id));

				# SI SE OBTIENE LA INFORMACION
				if(!empty($product))
				{
					# SE OBTIENE LA SESION DEL CARRITO
					$quote = Session::get('quote');

					# SI YA EXISTE UN REGISTRO DEL PRODUCTO
					if(isset($quote[$product_id]))
					{
						# SE ESTABLECE LA CANTIDAD
						$quote[$product_id]['quantity'] = $quote[$product_id]['quantity'] + $quantity;
					}
					else
					{
						# SI NO EXISTE EL ARREGLO DEL CARRITO
						if(!$quote)
						{
							# SE CREA EL ARREGLO DEL CARRITO
							$quote = array();
						}

						# SE CREA UN REGISTRO DEL PRODUCTO Y SU CANTIDAD
						Arr::insert_assoc($quote, array($product_id => array('quantity' => $quantity)), count($quote));
					}

					# SE ACTUALIZA LA SESION QUOTE
					Session::set('quote', $quote);

					# SE EJECUTA EL MODULO QUE DEPURA EL CARRITO
					$response = Request::forge('sectorweb/quote/debug', false)->execute(true, null)->response->body;

					# SE OBTIENE LA INFORMACION DE LA RESPUESTA
					$quote_data              = $response['quote_data'];
					$quote_session           = $response['quote_session'];
					$quote_unavailable       = $response['quote_unavailable'];
					$total_products_quantity = $response['total_products_quantity'];

					# SE ESTABLECE EL MENSAJE DE EXITO
					$msg = 'ok';
				}
				else
				{
					$msg = 'product_not_found';
				}
			}
			else
			{
				$msg = 'invalid_request';
			}

			# SE ENVIA EL ARREGLO CON LAS OPCIONES
			$this->response(array(
				'msg'                     => $msg,
				'product_id'              => $product_id,
				'quantity'                => $quantity,
				'quote_data'              => $quote_data,
				'quote_unavailable'       => $quote_unavailable,
				'total_products_quantity' => $total_products_quantity
			));
		}


    /**
   	 * EDIT QUOTE
   	 *
   	 * MODIFICA LA CANTIDAD DE UN PRODUCTO EN EL CARRITO
   	 *
   	 * @access  public
   	 * @return  Object
   	 */
    public function post_edit_product_quote()
		{
			# SE INICIALIZA MSG
			$msg = 'error';

			# SI EL USUARIO ES VALIDO
			if(Request::forge('sectorweb/admin/is_valid', false)->execute()->response->body)
			{
				# SE OBTIENE LOS DATOS ENVIADOS POR AJAX
				$product_id = Input::json('idProduct');
				$quantity   = Input::json('quantity');

				# SE INICIALIZAN LOS ARREGLOS
				$quote_data              = array();
				$quote_unavailable       = array();
				$total_products_quantity = 0;
				$delete_product_id       = null;

				# SE ESTABLECEN LAS REGLAS DE VALIDACION
				$val = Validation::forge();
				$val->add_field('product_id', 'Id producto', 'required|numeric_min[0]|valid_string[numeric]');
				$val->add_field('quantity', 'Cantidad', 'required|numeric_min[0]|valid_string[numeric]');

				# SI LA VALIDACION ES CORRECTA
				if($val->run(array(
					'product_id' => $product_id,
					'quantity'   => $quantity
				)))
				{
					# SE FORMATEAN NUMEROS A SOLO ENTEROS
					$product_id = (int)$product_id;
					$quantity   = (int)$quantity;

					# SI SE OBTUVO EL CARRITO
					if(Session::get('quote'))
					{
						# SE OBTIENE LA SESION DEL CARRITO
						$quote = Session::get('quote');

						# SE BUSCA EL PRODUCTO
						$product = Model_Product::get_valid(array('product_id' => $product_id));

						# SI SE OBTUVO INFORMACION
						if(!empty($product))
						{
							# SI LA CANTIDAD DEL PRODUCTO A MODIFICAR ES 0
							if($quantity == 0)
							{
								# SE ELIMINA EL PRODUCTO DEL CARRITO
								unset($quote[$product_id]);

								# SE GUARDA EL ID DEL PRODUCTO A ELIMINAR
								$delete_product_id = $product_id;
							}
							else
							{
								# SE ESTABLECE LA CANTIDAD
								$quote[$product_id]['quantity'] = $quantity;
							}

							# SE GUARDA EL CARRITO
							Session::set('quote', $quote);
						}

						# SI HAY UN PRODUCTO A ELIMINAR O EL CARRITO NO ESTA VACIO
						if($delete_product_id != null or !empty($quote))
						{
							# SE EJECUTA EL MODULO QUE DEPURA EL CARRITO
							$response = Request::forge('sectorweb/quote/debug', false)->execute(array(true, $delete_product_id))->response->body;

							# SE OBTIENE LA INFORMACION DE LA RESPUESTA
							$quote_data              = $response['quote_data'];
							$quote_session           = $response['quote_session'];
							$quote_unavailable       = $response['quote_unavailable'];
							$total_products_quantity = $response['total_products_quantity'];
						}

						# SE CAMBIA EL VALOR DE MENSAJE A OK
						$msg = 'ok';
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'no_quote';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'invalid_request';
				}
			}
			else
			{
				$msg = 'invalid_user';
			}

			# SE ENVIA EL ARREGLO CON LAS OPCIONES
			$this->response(array(
				'msg'                     => $msg,
				'product_id'              => $product_id,
				'quantity'                => $quantity,
				'quote_data'              => $quote_data,
				'quote_unavailable'       => $quote_unavailable,
				'total_products_quantity' => $total_products_quantity
			));
		}


    /**
   	 * DELETE QUOTE
   	 *
   	 * ELIMINA UN PRODUCTO DEL CARRITO
   	 *
   	 * @access  public
   	 * @return  Object
   	 */
    public function post_delete_product_quote()
		{
			# SI EL USUARIO ES VALIDO
			if(Request::forge('sectorweb/admin/is_valid', false)->execute()->response->body)
			{
				# SE OBTIENE LOS DATOS ENVIADOS POR AJAX
				$product_id = Input::json('idProduct');

				# SE INICIALIZAN LAS VARIABLES
				$msg                     = 'error';
				$quote_data              = array();
				$total_products_quantity = 0;
				$delete_product_id       = null;

				# SE ESTABLECEN LAS REGLAS DE VALIDACION
				$val = Validation::forge();
				$val->add_field('id_product', 'Id producto', 'required|numeric_min[0]|valid_string[numeric]');

				# SI LA VALIDACION ES CORRECTA
				if($val->run(array(
					'id_product' => $product_id,
				)))
				{
					# SE FORMATEAN NUMEROS A SOLO ENTEROS
					$product_id = (int)$product_id;

					# SI SE OBTUVO EL CARRITO
					if(Session::get('quote'))
					{
						# SE OBTIENE LA SESION DEL CARRITO
						$quote = Session::get('quote');

						# SE BUSCA EL PRODUCTO
						$product = Model_Product::get_valid(array('id_product' => $product_id));

						# SI SE OBTUVO INFORMACION
						if(!empty($product))
						{
							# SE ELIMINA EL PRODUCTO DEL CARRITO
							unset($quote[$product_id]);

							# SE GUARDA EL ID EN DELETE PRODUCT ID
							$delete_product_id = $product_id;
						}
						else
						{
							# SI EL ID SE ENCUENTRA EN EL CARRITO
							if(isset($quote[$product_id]))
							{
								# SE ELIMINA DEL CARRITO
								unset($quote[$product_id]);
							}
						}

						# SE GUARDA EL CARRITO
						Session::set('quote', $quote);

						# SI HAY UN PRODUCTO A ELIMINAR O EL CARRITO NO ESTA VACIO
						if($delete_product_id != null or !empty($quote))
						{
							# SE EJECUTA EL MODULO QUE DEPURA EL CARRITO
							$response = Request::forge('sectorweb/quote/debug', false)->execute(array(true, $delete_product_id))->response->body;

							# SE OBTIENE LA INFORMACION DE LA RESPUESTA
							$quote_data              = $response['quote_data'];
							$quote_session           = $response['quote_session'];
							$quote_unavailable       = $response['quote_unavailable'];
							$total_products_quantity = $response['total_products_quantity'];
						}

						# SE CAMBIA EL VALOR DE MENSAJE A OK
						$msg = 'ok';
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						$msg = 'no_quote';
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$msg = 'invalid_request';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$msg = 'invalid_user';
			}

			# SE ENVIA EL ARREGLO CON LAS OPCIONES
			$this->response(array(
				'msg'                     => $msg,
				'product_id'              => $product_id,
				'quantity'                => 0,
				'quote_data'              => $quote_data,
				'quote_unavailable'       => $quote_unavailable,
				'total_products_quantity' => $total_products_quantity
			));
		}


    /**
   	 * PRODUCTS AVAILABLES
   	 *
   	 * CREA O ELIMINA UNA SESION
   	 *
   	 * @access  public
   	 * @return  Object
   	 */
	public function post_products_availables()
		{
			# SE OBTIENE LOS DATOS ENVIADOS POR AJAX
			$msg   = 'error';
			$value = Input::post('value');

			# SE ESTABLECEN LAS REGLAS DE VALIDACION
			$val = Validation::forge();
			$val->add_field('value', 'Valor', 'required|valid_string[numeric]|numeric_between[0,1]');

			# SI LA VALIDACION ES CORRECTA
			if($val->run(array(
				'value' => $value
			)))
			{
				# SI EL VALOR ES 1
				if($val->validated('value') == 1)
				{
					# SE CREA LA SESION PRODUCTS_AVALIABLE
					Session::set('products_available', true);
				}
				else
				{
					# SE ELIMINA LA SESION PRODUCTS_AVALIABLE
					Session::delete('products_available');
				}

				# SE ESTABLECE EL MENSAJE DE EXITO
				$msg = 'ok';
			}

			# SE ENVIA EL ARREGLO CON LAS OPCIONES
			$this->response(array(
				'msg' => $msg
			));
		}




	////////////////////////////////////////////////////
	///APARTADO PARA MODIFICAR LOS DATOS DEL SOCIO DE NEGOCIOS///////////////////////
	////////////////////////////////////////////////////
	/**
     * GENERALES
     *
     * ENVIA PARA RECIBIR LOS GENERALES
     *
     * @access  private
     * @return  Boolean
     */
	public function post_get_generales_opts()
	{

		$msg = '';
		$errors = [];

		if (Input::is_ajax()) {
			$access_id    = Input::post('access_id');
			$access_token = Input::post('access_token');
			$partner_id   = Input::post('partner_id');


			$user = Model_User::query()->where('id', $access_id)->get_one();
			if ($user && md5($user->login_hash) == $access_token) {

				$partner = Model_Partner::query()
					->related('user')
					->where('id', $partner_id)
					->get_one();

				if ($partner && !empty($partner->user)) {
					$fields = @unserialize($partner->user->profile_fields) ?: [];
					$banned = isset($fields['banned']) ? (int)$fields['banned'] : 0;



					// Opciones para Usuario Web
					$customer_opts_html = '<option value="">Seleccionar...</option>';
					$customers_log = '';
					foreach (Model_Customer::query()->order_by('name', 'asc')->get() as $customer) {
						$selected = ($partner->customer_id == $customer->id) ? 'selected' : '';
						$customer_opts_html .= "<option value='{$customer->id}' {$selected}>{$customer->name}</option>";
						$customers_log .= "[{$customer->id}]=>{$customer->name} ".($selected ? '[selected]' : '')." | ";
					}

					// Opciones para Vendedor
					$employee_opts_html = '<option value="">Seleccionar...</option>';
					$employees_log = '';
					foreach (Model_Employee::query()->order_by('name', 'asc')->get() as $employee) {
						$selected = ($partner->employee_id == $employee->id) ? 'selected' : '';
						$employee_opts_html .= "<option value='{$employee->id}' {$selected}>{$employee->name}</option>";
						$employees_log .= "[{$employee->id}]=>{$employee->name} ".($selected ? '[selected]' : '')." | ";
					}

					// Opciones para Lista de Precios
					$type_opts_html = '<option value="">Seleccionar...</option>';
					$types_log = '';
					foreach (Model_Customers_Type::query()->order_by('name', 'asc')->get() as $type) {
						$selected = ($partner->type_id == $type->id) ? 'selected' : '';
						$type_opts_html .= "<option value='{$type->id}' {$selected}>{$type->name}</option>";
						$types_log .= "[{$type->id}]=>{$type->name} ".($selected ? '[selected]' : '')." | ";
					}

					//Opciones para los dias de credito
					/*$payment_terms_opts_html = '<option value="">Seleccionar...</option>';
					$payment_log = '';
					foreach (Model_Payments_Method::query()->order_by('name', 'asc')->get() as $payment) {
						$selected = ($partner->payment_id == $payment->id) ? 'selected' : '';
						$payment_opts_html .= "<option value='{$payment->id}' {$selected}>{$payment->name}</option>";
					}
					*/
					$msg = 'ok';

					$this->response([
						'msg' => $msg,
						'code_sap' => $partner->code_sap,
						'name' => $partner->name,
						'rfc'  => $partner->rfc,
						'email' => $partner->user->email,
						'customer_id' => $partner->customer_id,
						'employee_id' => $partner->employee_id,
						'type_id' => $partner->type_id,
						'banned' => $banned,
						'customer_opts_html' => $customer_opts_html,
						'employee_opts_html' => $employee_opts_html,
						'type_opts_html' => $type_opts_html
					]);
					return;
				} else {
					$msg = 'No se encontró el socio.';

				}
			} else {
				$msg = 'Acceso no autorizado.';

			}
		} else {
			$msg = 'Petición inválida.';

		}
		$this->response(['msg' => $msg, 'errors' => $errors]);
	}





	/**
     * EDITAR GENERALES
     *
     * ENVIA PARA EDITAR LOS GENERALES
     *
     * @access  private
     * @return  Boolean
     */
	public function post_save_generales()
	{
		\Log::info('[SAVE_GENERALES] INICIANDO GUARDADO DATOS GENERALES DEL SOCIO');
		$msg = '';
		$errors = [];
		$cambios = [];

		if (Input::is_ajax()) {
			$access_id    = Input::post('access_id');
			$access_token = Input::post('access_token');
			$partner_id   = Input::post('partner_id');
			$name         = trim(Input::post('name'));
			$rfc          = trim(Input::post('rfc'));
			$email        = trim(Input::post('email'));
			$customer_id  = Input::post('customer_id');
			$employee_id  = Input::post('employee_id');
			$type_id      = Input::post('type_id');
			$banned       = Input::post('banned');

			// 1. Autenticación básica (puedes mejorarla con permisos según tus necesidades)
			$access_user = Model_User::find($access_id);
			if ($access_user && md5($access_user->login_hash) == $access_token) {

				// 2. Carga el partner y el usuario relacionado (el dueño de los datos a editar)
				$partner = Model_Partner::find($partner_id);
				if ($partner && $partner->user_id) {
					$user = Model_User::find($partner->user_id);

					// 3. Solo actualiza si realmente cambia algún campo
					if ($partner->name !== $name) {
						$partner->name = $name;
						$cambios[] = 'Razón Social';
					}
					if ($partner->rfc !== $rfc) {
						$partner->rfc = $rfc;
						$cambios[] = 'RFC';
					}
					if ($partner->customer_id != $customer_id) {
						$partner->customer_id = $customer_id;
						$cambios[] = 'Usuario Web';
					}
					if ($partner->employee_id != $employee_id) {
						$partner->employee_id = $employee_id;
						$cambios[] = 'Vendedor';
					}
					if ($partner->type_id != $type_id) {
						$partner->type_id = $type_id;
						$cambios[] = 'Lista de Precios';
					}

					// 4. Cambia banned en profile_fields solo si hay cambio
					$profile_fields = @unserialize($user->profile_fields) ?: [];
					if ((isset($profile_fields['banned']) ? $profile_fields['banned'] : 0) != $banned) {
						$profile_fields['banned'] = $banned;
						$user->profile_fields = serialize($profile_fields);
						$cambios[] = 'Bloqueado';
					}

					// 5. Cambia email solo si realmente cambió
					$email_form = strtolower(trim($email));
					$email_user = strtolower(trim($user->email));
					if ($email_form !== $email_user) {
						$email_existente = Model_User::query()
							->where('email', $email_form)
							->where('id', '!=', $user->id)
							->get_one();
						if (!empty($email_existente)) {
							$errors[] = 'El correo electrónico ingresado ya existe. Usa uno diferente.';
							\Log::error("[SAVE_GENERALES] Error: Email duplicado '{$email_form}' para el usuario ID={$user->id}");
						} else {
							$user->email = $email_form;
							$cambios[] = 'Correo electrónico';
						}
					}

					// 6. Guarda sólo si hubo cambios y no hubo errores
					if (!$errors && count($cambios) > 0) {
						$partner->save();
						$user->save();
						$msg = 'ok';
						\Log::info('[SAVE_GENERALES] Cambios realizados: ' . implode(', ', $cambios) . " para el socio ID={$partner_id}");
					} elseif (!$errors) {
						// Mejor un msg especial para JS
						$msg = 'no_changes';
					}

				} else {
					$msg = 'No se encontró el socio o el usuario asociado.';
				}
			} else {
				$msg = 'Acceso no autorizado.';
			}
		} else {
			$msg = 'Petición inválida.';
		}
		$this->response([
			'msg' => $msg,
			'errors' => $errors
		]);
	}


	/**
     * FISCALES
     *
     * ENVIA PARA RECIBIR LOS FISCALES
     *
     * @access  private
     * @return  Boolean
     */
	public function post_get_fiscal_opts()
	{
		$msg = '';
		$errors = [];

		if (Input::is_ajax()) {
			$access_id    = Input::post('access_id');
			$access_token = Input::post('access_token');
			$partner_id   = Input::post('partner_id');

			$user = Model_User::query()->where('id', $access_id)->get_one();
			if ($user && md5($user->login_hash) == $access_token) {

				// 1. Buscar datos fiscales del socio
				$tax_data = Model_Partners_Tax_Datum::query()
					->where('partner_id', $partner_id)
					->get_one();

				$editing = false;
				$csf_link = '';
				if ($tax_data) {
					$editing = true;
					if (!empty($tax_data->csf)) {
						$csf_link = Uri::base(false) . $tax_data->csf;
					}
				}

				// 2. Opciones Estado
				$state_opts_html = '<option value="">Seleccionar...</option>';
				foreach (Model_State::query()->order_by('name', 'asc')->get() as $state) {
					$selected = ($tax_data && $tax_data->state_id == $state->id) ? 'selected' : '';
					$state_opts_html .= "<option value='{$state->id}' {$selected}>{$state->name}</option>";
				}

				// 3. Opciones Régimen Fiscal SAT
				$sat_tax_regime_opts_html = '<option value="">Seleccionar...</option>';
				foreach (Model_Sat_Tax_Regime::query()->order_by('name', 'asc')->get() as $regime) {
					$selected = ($tax_data && $tax_data->sat_tax_regime_id == $regime->id) ? 'selected' : '';
					$sat_tax_regime_opts_html .= "<option value='{$regime->id}' {$selected}>{$regime->name}</option>";
				}

				// 4. Opciones Forma de Pago
				$payment_method_opts_html = '<option value="">Seleccionar...</option>';
				foreach (Model_Payments_Method::query()->order_by('name', 'asc')->get() as $payment) {
					$selected = ($tax_data && $tax_data->payment_method_id == $payment->id) ? 'selected' : '';
					$payment_method_opts_html .= "<option value='{$payment->id}' {$selected}>{$payment->name}</option>";
				}

				// 5. Opciones CFDI
				$cfdi_opts_html = '<option value="">Seleccionar...</option>';
				foreach (Model_Cfdi::query()->order_by('name', 'asc')->get() as $cfdi) {
					$selected = ($tax_data && $tax_data->cfdi_id == $cfdi->id) ? 'selected' : '';
					$cfdi_opts_html .= "<option value='{$cfdi->id}' {$selected}>{$cfdi->name}</option>";
				}

				// 6. Enviar respuesta
				$msg = 'ok';
				$this->response([
					'msg'                   => $msg,
					'editing'               => $editing,
					'business_name'         => $tax_data->business_name ?? '',
					'rfc'                   => $tax_data->rfc ?? '',
					'street'                => $tax_data->street ?? '',
					'number'                => $tax_data->number ?? '',
					'internal_number'       => $tax_data->internal_number ?? '',
					'colony'                => $tax_data->colony ?? '',
					'city'                  => $tax_data->city ?? '',
					'municipality'          => $tax_data->municipality ?? '',
					'state_id'              => $tax_data->state_id ?? '',
					'zipcode'               => $tax_data->zipcode ?? '',
					'sat_tax_regime_id'     => $tax_data->sat_tax_regime_id ?? '',
					'payment_method_id'     => $tax_data->payment_method_id ?? '',
					'cfdi_id'               => $tax_data->cfdi_id ?? '',
					'email'                 => $tax_data->email ?? '',
					'csf_link'              => $csf_link,
					'states_opts_html'          => $state_opts_html,
					'sat_tax_regime_opts_html'  => $sat_tax_regime_opts_html,
					'payment_method_opts_html'  => $payment_method_opts_html,
					'cfdi_opts_html'            => $cfdi_opts_html,
				]);
				return;
			} else {
				$msg = 'Acceso no autorizado.';
			}
		} else {
			$msg = 'Petición inválida.';
		}
		$this->response(['msg' => $msg, 'errors' => $errors]);
	}



	/**
     * FISCALES
     *
     * ENVIA PARA EDITAR LOS FISCALES
     *
     * @access  private
     * @return  Boolean
     */
	public function post_save_fiscal()
	{
		\Log::info('[SAVE_FISCAL] INICIANDO GUARDADO DE DATOS FISCALES DEL SOCIO');
		$msg = '';
		$errors = [];
		$cambios = [];

		// 1. Validar que sea AJAX
		if (!Input::is_ajax()) {
			$msg = 'Petición inválida.';
			\Log::error('[SAVE_FISCAL] No es una petición AJAX');
			return $this->response(['msg' => $msg, 'errors' => $errors]);
		}

		// 2. Validar autenticación
		$access_id    = Input::post('access_id');
		$access_token = Input::post('access_token');
		$partner_id   = Input::post('partner_id');
		\Log::info("[SAVE_FISCAL] access_id={$access_id}, partner_id={$partner_id}");

		$user = Model_User::find($access_id);
		if (!$user || md5($user->login_hash) != $access_token) {
			$msg = 'Acceso no autorizado.';
			\Log::error("[SAVE_FISCAL] Acceso no autorizado para user {$access_id}");
			return $this->response(['msg' => $msg, 'errors' => $errors]);
		}

		// 3. Definir los campos esperados
		$fields = [
			'business_name', 'rfc', 'street', 'number', 'internal_number', 'colony',
			'city', 'municipality', 'state_id', 'zipcode', 'sat_tax_regime_id',
			'payment_method_id', 'cfdi_id', 'email', 'default'
		];

		// 4. Obtener datos POST
		$data = [];
		foreach ($fields as $field) {
			$data[$field] = Input::post($field, ($field === 'default' ? 0 : ''));
		}
		// Asegurar que default sea 0/1
		$data['default'] = (int)$data['default'];
		\Log::info('[SAVE_FISCAL] Datos recibidos: ' . json_encode($data));

		// 5. Manejo de archivo PDF (CSF) - OPCIONAL
	// 5. Manejo de archivo PDF (CSF) - OPCIONAL
	$csf_file = '';
	if (isset($_FILES['csf']) && $_FILES['csf']['error'] === 0) {
		$ext = strtolower(pathinfo($_FILES['csf']['name'], PATHINFO_EXTENSION));
		if ($ext === 'pdf') {
			$ruta = 'assets/uploads/csf/';
			@mkdir(DOCROOT . $ruta, 0755, true);
			$filename = $ruta . uniqid('csf_') . '.' . $ext;
			if (move_uploaded_file($_FILES['csf']['tmp_name'], DOCROOT . $filename)) {
				$csf_file = $filename;
				\Log::info("[SAVE_FISCAL] Archivo CSF cargado en: {$csf_file}");
			} else {
				$errors[] = 'Error al subir la constancia de situación fiscal.';
				\Log::error('[SAVE_FISCAL] Error al mover el archivo CSF.');
			}
		} else {
			$errors[] = 'El archivo CSF debe ser PDF.';
			\Log::error('[SAVE_FISCAL] El archivo CSF no es PDF.');
		}
	} else {
		// Si NO se sube nada, el valor debe ser cadena vacía
		$csf_file = '';
		\Log::info('[SAVE_FISCAL] No se recibió archivo CSF. Se guarda como vacío ("").');
	}


		// 6. Buscar registro existente de datos fiscales
		$tax_data = Model_Partners_Tax_Datum::query()
			->where('partner_id', $partner_id)
			->get_one();

		if ($tax_data) {
			// --- EDICIÓN ---
			\Log::info("[SAVE_FISCAL] Editando datos fiscales ID={$tax_data->id} para partner_id={$partner_id}");
			foreach ($fields as $field) {
				// Solo guardar si hay cambios reales
				if ($tax_data->$field != $data[$field]) {
					$tax_data->$field = $data[$field];
					$cambios[] = $field;
				}
			}
			if ($csf_file) {
				$tax_data->csf = $csf_file;
				$cambios[] = 'csf';
			}
			// Siempre actualiza updated_at si hay cambios
			if (count($cambios) > 0 && !$errors) {
				$tax_data->updated_at = time();
				$tax_data->save();
				$msg = 'ok';
				\Log::info('[SAVE_FISCAL] Cambios realizados: ' . implode(', ', $cambios) . " para el socio ID={$partner_id}");
			} elseif (!$errors) {
				$msg = 'no_changes';
				\Log::info('[SAVE_FISCAL] No hubo cambios en los datos fiscales.');
			}
		} else {
			// --- AGREGAR ---
			\Log::info("[SAVE_FISCAL] Agregando nuevo registro fiscal para partner_id={$partner_id}");
			// --- AGREGAR ---
			$tax_data = Model_Partners_Tax_Datum::forge(array_merge([
				'partner_id' => $partner_id,
				'csf'        => $csf_file, // ← aquí ya nunca es null
				'created_at' => time(),
				'updated_at' => time(),
			], $data));
			$tax_data->save();
			$msg = 'ok';
			$cambios = $fields;
			if ($csf_file) $cambios[] = 'csf';
			\Log::info('[SAVE_FISCAL] Registro fiscal agregado correctamente para el socio ID=' . $partner_id);
		}

		// 7. Regresa respuesta
		return $this->response([
			'msg' => $msg,
			'errors' => $errors,
			'cambios' => $cambios
		]);
	}


	/**
     * CONTACTOS
     *
     * ENVIA PARA RECIBIR LOS CONTACTOS
     *
     * @access  private
     * @return  Boolean
     */
	public function post_get_contacto_opts()
	{
		\Log::info('[GET_CONTACTO_OPTS] INICIANDO');

		$access_id    = Input::post('access_id');
		$access_token = Input::post('access_token');
		$partner_id   = Input::post('partner_id');
		$contact_id   = Input::post('contact_id', 0);

		// Validar autenticación
		$user = Model_User::find($access_id);
		if (!$user || md5($user->login_hash) != $access_token) {
			\Log::error('[GET_CONTACTO_OPTS] Acceso no autorizado');
			return $this->response(['msg' => 'Acceso no autorizado.']);
		}

		$msg = 'ok';
		$data = [
			'idcontact'   => '',
			'name'        => '',
			'last_name'   => '',
			'phone'       => '',
			'cel'         => '',
			'email'       => '',
			'departments' => ''
		];

		if ($contact_id && $contact_id > 0) {
			// EDITAR: Traer info existente
			$contact = Model_Partners_Contact::find($contact_id);
			if (!$contact) {
				\Log::error("[GET_CONTACTO_OPTS] Contacto no encontrado (ID={$contact_id})");
				return $this->response(['msg' => 'Contacto no encontrado.']);
			}
			$data['idcontact']   = $contact->idcontact;
			$data['name']        = $contact->name;
			$data['last_name']   = $contact->last_name;
			$data['phone']       = $contact->phone;
			$data['cel']         = $contact->cel;
			$data['email']       = $contact->email;
			$data['departments'] = $contact->departments;
			\Log::info("[GET_CONTACTO_OPTS] Editando contacto ID={$contact_id}");
		} else {
			// NUEVO: Autogenerar idcontact (ejemplo: CT00001)
			$last = Model_Partners_Contact::query()
				->where('partner_id', $partner_id)
				->order_by('idcontact', 'desc')
				->get_one();
			$next = 1;
			if ($last && preg_match('/CT(\d+)/', $last->idcontact, $m)) {
				$next = intval($m[1]) + 1;
			}
			$data['idcontact'] = 'CT' . str_pad($next, 5, '0', STR_PAD_LEFT);
			\Log::info("[GET_CONTACTO_OPTS] Nuevo contacto, idcontact generado: {$data['idcontact']}");
		}

		return $this->response(array_merge(['msg' => $msg], $data));
	}


	/**
     * CONTACTOS
     *
     * ENVIA PARA EDITAR LOS CONTACTOS
     *
     * @access  private
     * @return  Boolean
     */
	public function post_save_contacto()
	{
		\Log::info('[SAVE_CONTACTO] INICIANDO');

		$access_id    = Input::post('access_id');
		$access_token = Input::post('access_token');
		$partner_id   = Input::post('partner_id');
		$contact_id   = Input::post('contact_id', 0);

		$fields = ['idcontact', 'name', 'last_name', 'phone', 'cel', 'email', 'departments'];
		$data = [];
		foreach ($fields as $field) {
			$data[$field] = trim(Input::post($field, ''));
		}
		\Log::info('[SAVE_CONTACTO] Datos recibidos: ' . json_encode($data));

		// Validar autenticación
		$user = Model_User::find($access_id);
		if (!$user || md5($user->login_hash) != $access_token) {
			\Log::error('[SAVE_CONTACTO] Acceso no autorizado');
			return $this->response(['msg' => 'Acceso no autorizado.']);
		}

		$msg = '';
		$errors = [];

		// Validación básica (puedes ajustar)
		if (empty($data['idcontact']))   $errors[] = 'El campo ID de contacto es obligatorio.';
		if (empty($data['name']))        $errors[] = 'El campo nombre es obligatorio.';
		if (empty($data['last_name']))   $errors[] = 'El campo apellido es obligatorio.';
		if (empty($data['phone']))       $errors[] = 'El campo teléfono es obligatorio.';
		if (empty($data['email']))       $errors[] = 'El campo correo es obligatorio.';
		if (empty($data['departments'])) $errors[] = 'El campo departamento es obligatorio.';

		// Validar duplicidad idcontact para nuevos
		if ($contact_id == 0) {
			$exists = Model_Partners_Contact::query()
				->where('partner_id', $partner_id)
				->where('idcontact', $data['idcontact'])
				->get_one();
			if ($exists) {
				$errors[] = 'El ID de contacto ya existe para este socio.';
			}
		}

		// Validar email (puedes extender con regex)
		if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'El correo no tiene formato válido.';
		}

		if ($errors) {
			\Log::error('[SAVE_CONTACTO] Errores de validación: ' . implode(' | ', $errors));
			return $this->response(['msg' => 'Error en validación.', 'errors' => $errors]);
		}

		if ($contact_id && $contact_id > 0) {
			// EDITAR
			$contact = Model_Partners_Contact::find($contact_id);
			if (!$contact) {
				\Log::error("[SAVE_CONTACTO] Contacto no encontrado para editar (ID={$contact_id})");
				return $this->response(['msg' => 'No se encontró el contacto a editar.']);
			}
			foreach ($fields as $field) {
				$contact->$field = $data[$field];
			}
			$contact->save();
			$msg = 'ok';
			\Log::info("[SAVE_CONTACTO] Contacto editado ID={$contact_id}");
		} else {
			// NUEVO
			$contact = Model_Partners_Contact::forge(array_merge($data, [
				'partner_id'          => $partner_id,
				'partner_delivery_id' => 0,
				'default'             => 0,
				'deleted'             => 0
			]));
			$contact->save();
			$msg = 'ok';
			\Log::info('[SAVE_CONTACTO] Contacto agregado correctamente');
		}

		return $this->response(['msg' => $msg, 'errors' => $errors]);
	}


	/**
     * ENTREGAS
     *
     * ENVIA PARA RECIBIR LAS ENTREGAS
     *
     * @access  private
     * @return  Boolean
     */
	public function post_get_entrega_opts()
	{
		\Log::info('[GET_ENTREGA_OPTS] INICIANDO');

		$access_id    = Input::post('access_id');
		$access_token = Input::post('access_token');
		$partner_id   = Input::post('partner_id');
		$entrega_id   = Input::post('entrega_id', 0);

		// Validar autenticación
		$user = Model_User::find($access_id);
		if (!$user || md5($user->login_hash) != $access_token) {
			\Log::error('[GET_ENTREGA_OPTS] Acceso no autorizado');
			return $this->response(['msg' => 'Acceso no autorizado.']);
		}

		// Opciones de estados para select
		$state_opts_html = '<option value="">Seleccionar...</option>';
		foreach (Model_State::query()->order_by('name', 'asc')->get() as $state) {
			$state_opts_html .= "<option value='{$state->id}'>{$state->name}</option>";
		}

		// Datos por default vacíos (para nuevo)
		$data = [
			'iddelivery'      => '',
			'street'          => '',
			'number'          => '',
			'internal_number' => '',
			'colony'          => '',
			'city'            => '',
			'municipality'    => '',
			'state'           => '',
			'zipcode'         => '',
			'reception_hours' => '',
			'delivery_notes'  => '',
			'name'            => '',
			'last_name'       => '',
			'phone'           => ''
		];

		if ($entrega_id && $entrega_id > 0) {
			$delivery = Model_Partners_Delivery::find($entrega_id);
			if ($delivery) {
				$data['iddelivery']      = $delivery->iddelivery;
				$data['street']          = $delivery->street;
				$data['number']          = $delivery->number;
				$data['internal_number'] = $delivery->internal_number;
				$data['colony']          = $delivery->colony;
				$data['city']            = $delivery->city;
				$data['municipality']    = $delivery->municipality;
				$data['state']           = $delivery->state_id;
				$data['zipcode']         = $delivery->zipcode;
				$data['reception_hours'] = $delivery->reception_hours;
				$data['delivery_notes']  = $delivery->delivery_notes;

				// 🔴 Aquí obtienes el contacto asociado a la entrega
				$contact = Model_Partners_Contact::query()
					->where('partner_delivery_id', $delivery->id)
					->where('deleted', 0)
					->order_by('id', 'asc')
					->get_one();
				if ($contact) {
					$data['name']      = $contact->name;
					$data['last_name'] = $contact->last_name;
					$data['phone']     = $contact->phone;
				}
			}
		}

		return $this->response([
			'msg' => 'ok',
			'state_opts_html' => $state_opts_html
		] + $data);
	}



	/**
     * ENTREGA
     *
     * ENVIA PARA EDITAR LAS ENTREGAS
     *
     * @access  private
     * @return  Boolean
     */
	public function post_save_entrega()
	{
		\Log::info('[SAVE_ENTREGA] INICIANDO GUARDADO DE ENTREGA');
		$msg = '';
		$errors = [];

		// 1. Validar autenticación
		$access_id    = Input::post('access_id');
		$access_token = Input::post('access_token');
		$partner_id   = Input::post('partner_id');
		$entrega_id   = Input::post('entrega_id', 0); // Para editar
		\Log::info("[SAVE_ENTREGA] access_id={$access_id}, partner_id={$partner_id}, entrega_id={$entrega_id}");

		$user = Model_User::find($access_id);
		if (!$user || md5($user->login_hash) != $access_token) {
			\Log::error('[SAVE_ENTREGA] Acceso no autorizado');
			return $this->response(['msg' => 'Acceso no autorizado.']);
		}

		// 2. Obtener datos (ajusta los nombres según tu frontend)
		$fields_entrega = ['iddelivery', 'street', 'number', 'internal_number', 'colony', 'city', 'municipality', 'state', 'zipcode', 'reception_hours', 'delivery_notes'];
		$fields_contact = ['name', 'last_name', 'phone'];
		$data = [];
		foreach (array_merge($fields_entrega, $fields_contact) as $field) {
			$data[$field] = Input::post($field, '');
		}

		// Validaciones aquí (puedes extenderlas)
		if (empty($data['iddelivery']))   $errors[] = 'El campo ID de dirección es obligatorio.';
		if (empty($data['street']))       $errors[] = 'El campo Calle es obligatorio.';
		if (empty($data['number']))       $errors[] = 'El campo Número es obligatorio.';
		if (empty($data['colony']))       $errors[] = 'El campo Colonia es obligatorio.';
		if (empty($data['city']))         $errors[] = 'El campo Ciudad es obligatorio.';
		if (empty($data['zipcode']))      $errors[] = 'El campo Código Postal es obligatorio.';
		if (empty($data['reception_hours'])) $errors[] = 'El campo Horario de recepción es obligatorio.';
		if (empty($data['name']))         $errors[] = 'El nombre del contacto es obligatorio.';
		if (empty($data['last_name']))    $errors[] = 'El apellido del contacto es obligatorio.';
		if (empty($data['phone']))        $errors[] = 'El teléfono del contacto es obligatorio.';

		if ($errors) {
			\Log::error('[SAVE_ENTREGA] Errores de validación: ' . implode(' | ', $errors));
			return $this->response(['msg' => 'Error en validación.', 'errors' => $errors]);
		}

		// 3. Guardar entrega (nuevo o editar)
		if ($entrega_id && $entrega_id > 0) {
			// EDITAR entrega
			$delivery = Model_Partners_Delivery::find($entrega_id);
			if (!$delivery) {
				\Log::error("[SAVE_ENTREGA] No se encontró la entrega ID={$entrega_id}");
				return $this->response(['msg' => 'No se encontró el domicilio de entrega.']);
			}
			foreach ($fields_entrega as $field) {
				if ($field == 'state') {
					$delivery->state_id = $data['state'];
				} else {
					$delivery->$field = $data[$field];
				}
			}
			$delivery->save();

			// Editar contacto principal
			$contact = Model_Partners_Contact::query()
				->where('partner_delivery_id', $delivery->id)
				->where('deleted', 0)
				->get_one();
			if ($contact) {
				$contact->name = $data['name'];
				$contact->last_name = $data['last_name'];
				$contact->phone = $data['phone'];
				$contact->save();
			}
			$msg = 'ok';
			\Log::info("[SAVE_ENTREGA] Entrega y contacto editados correctamente.");
		} else {
			// NUEVA entrega
			$delivery = Model_Partners_Delivery::forge([
				'partner_id'      => $partner_id,
				'iddelivery'      => $data['iddelivery'],
				'street'          => $data['street'],
				'number'          => $data['number'],
				'internal_number' => $data['internal_number'],
				'colony'          => $data['colony'],
				'city'            => $data['city'],
				'municipality'    => $data['municipality'],
				'state_id'        => $data['state'],
				'zipcode'         => $data['zipcode'],
				'reception_hours' => $data['reception_hours'],
				'delivery_notes'  => $data['delivery_notes'],
				'default'         => 0,
				'deleted'         => 0
			]);
			$delivery->save();

			// Contacto principal de entrega
			$partner_contact = Model_Partners_Contact::forge([
				'idcontact'           => 'ENT-' . strtoupper($data['iddelivery']),
				'partner_id'          => $partner_id,
				'partner_delivery_id' => $delivery->id,
				'name'                => $data['name'],
				'last_name'           => $data['last_name'],
				'phone'               => $data['phone'],
				'cel'                 => '',
				'email'               => '',
				'departments'         => '',
				'default'             => 1,
				'deleted'             => 0
			]);
			$partner_contact->save();

			$msg = 'ok';
			\Log::info('[SAVE_ENTREGA] Entrega y contacto creados correctamente.');
		}

		return $this->response(['msg' => $msg, 'errors' => $errors]);
	}


	///////////////////////////////////////////////////////////////////
	////////AQUI TERMINAN LOS SOCIOS////////////////////////////////////
	///////LO QUE SEA DE DATOS GENERALES DE SOCIOS DEBE IR ARRIBA///////
	///////////////////////////////////////////////////////////////////	



	///////////////////////////////////////////////////
	//** EDICION DE DATOS GENERALES PARA PROVEDORES *//
	///////////////////////////////////////////////////

/**
 * OBTIENE LOS DATOS GENERALES DEL PROVEEDOR
 *
 * @access  private
 * @return  JSON
 */
public function post_get_generales_opts_provider()
{
	$msg = '';
	$errors = [];

	if (Input::is_ajax()) {
		$access_id    = Input::post('access_id');
		$access_token = Input::post('access_token');
		$provider_id  = Input::post('provider_id');

		$user = Model_User::query()->where('id', $access_id)->get_one();
		if ($user && md5($user->login_hash) == $access_token) {

			$provider = Model_Provider::query()
				->related('user')
				->where('id', $provider_id)
				->get_one();

			if ($provider && !empty($provider->user)) {
				$fields = @unserialize($provider->user->profile_fields) ?: [];
				$banned = isset($fields['banned']) ? (int)$fields['banned'] : 0;

				// Armamos el select de términos de pago
				$terms_opts_html = '<option value="">Seleccionar...</option>';
				foreach (Model_Payments_Term::query()->order_by('name','asc')->get() as $term) {
					$selected = ($provider->payment_terms_id == $term->id) ? 'selected' : '';
					$terms_opts_html .= "<option value='{$term->id}' {$selected}>{$term->name}</option>";
				}

				// Obtener catálogo de departamentos
				$departments = Model_Employees_Department::query()
					->order_by('name', 'asc')
					->get();

				// Asegurar que sea iterable
				if (!is_array($departments) && !is_object($departments)) {
					$departments = [];
				}

				// Generar opciones del select
				$departments_opts_html = '';
				foreach ($departments as $d) {
					$selected = (!empty($provider->main_department_id) && $provider->main_department_id == $d->id)
						? 'selected'
						: '';
					$departments_opts_html .= "<option value='{$d->id}' {$selected}>{$d->name}</option>";
				}

				$msg = 'ok';
				$this->response([
					'msg'              => $msg,
					'code_sap'         => $provider->code_sap,
					'name'             => $provider->name,
					'rfc'              => $provider->rfc,
					'email'            => $provider->user->email,
					'provider_type' => $provider->provider_type,
    				'origin' => $provider->origin,
					'banned'           => $banned,
					'departments_opts_html' => $departments_opts_html,
					'payment_terms_id' => $provider->payment_terms_id,
					'terms_opts_html'  => $terms_opts_html
				]);
				return;
			} else {
				$msg = 'No se encontró el proveedor.';
			}
		} else {
			$msg = 'Acceso no autorizado.';
		}
	} else {
		$msg = 'Petición inválida.';
	}

	$this->response(['msg' => $msg, 'errors' => $errors]);
}



/**
 * GUARDA LOS DATOS GENERALES DEL PROVEEDOR
 *
 * @access  private
 * @return  JSON
 */
public function post_save_generales_provider()
{
    \Log::info('[SAVE_GENERALES_PROVIDER] INICIANDO GUARDADO DE DATOS GENERALES DEL PROVEEDOR');
    $msg = '';
    $errors = [];
    $cambios = [];

    if (Input::is_ajax()) {
        $access_id        = Input::post('access_id');
        $access_token     = Input::post('access_token');
        $provider_id      = Input::post('provider_id');
        $name             = trim(Input::post('name'));
        $rfc              = trim(Input::post('rfc'));
        $email            = trim(Input::post('email'));
        $banned           = Input::post('banned');
        $payment_terms_id = Input::post('payment_terms_id');
        $provider_type    = Input::post('provider_type', 0);
        $origin           = Input::post('origin', 0);
        $department_id    = Input::post('employees_department_id', 0);

        $access_user = Model_User::find($access_id);
        if ($access_user && md5($access_user->login_hash) == $access_token) {

            $provider = Model_Provider::find($provider_id);
            // Usar activated_by en lugar de user_id (campo actualizado)
            if ($provider && $provider->activated_by) {
                $user = Model_User::find($provider->activated_by);

                // --- CAMPOS GENERALES ---
                if ($provider->name !== $name) {
                    $provider->name = $name;
                    $cambios[] = 'Razón Social';
                }
                if ($provider->rfc !== $rfc) {
                    $provider->rfc = $rfc;
                    $cambios[] = 'RFC';
                }
                if ($provider->payment_terms_id != $payment_terms_id) {
                    $provider->payment_terms_id = $payment_terms_id;
                    $cambios[] = 'Términos de Pago';
                }

                // --- NUEVOS CAMPOS ---
                if ($provider->provider_type != $provider_type) {
                    $provider->provider_type = $provider_type;
                    $cambios[] = 'Tipo de proveedor';
                }
                if ($provider->origin != $origin) {
                    $provider->origin = $origin;
                    $cambios[] = 'Procedencia';
                }

                // --- BLOQUEADO ---
                $profile_fields = @unserialize($user->profile_fields) ?: [];
                if ((isset($profile_fields['banned']) ? $profile_fields['banned'] : 0) != $banned) {
                    $profile_fields['banned'] = $banned;
                    $user->profile_fields = serialize($profile_fields);
                    $cambios[] = 'Bloqueado';
                }

                // --- EMAIL ---
                $email_form = strtolower(trim($email));
                $email_user = strtolower(trim($user->email));
                if ($email_form !== $email_user) {
                    $email_existente = Model_User::query()
                        ->where('email', $email_form)
                        ->where('id', '!=', $user->id)
                        ->get_one();
                    if (!empty($email_existente)) {
                        $errors[] = 'El correo electrónico ingresado ya existe.';
                        \Log::error("[SAVE_GENERALES_PROVIDER] Email duplicado: {$email_form}");
                    } else {
                        $user->email = $email_form;
                        $cambios[] = 'Correo electrónico';
                    }
                }

                // --- GUARDAR CAMBIOS ---
                if (!$errors && count($cambios) > 0) {
                    $provider->save();
                    $user->save();

                    // --- RELACIÓN CON DEPARTAMENTO ---
                    if ($department_id > 0) {
                        try {
                            // Limpiar anteriores
                            \DB::update('providers_departments')
                                ->set(['main' => 0])
                                ->where('provider_id', '=', $provider_id)
                                ->execute();

                            // Buscar o crear relación
                            $relation = Model_Providers_Department::query()
                                ->where('provider_id', $provider_id)
                                ->where('employees_department_id', $department_id)
                                ->get_one();

                            if (!$relation) {
                                $relation = Model_Providers_Department::forge([
                                    'provider_id' => $provider_id,
                                    'employees_department_id' => $department_id,
                                    'main' => 1,
                                    'created_at' => time(),
                                ]);
                            } else {
                                $relation->main = 1;
                                $relation->updated_at = time();
                            }
                            $relation->save();

                            \Log::info("[SAVE_GENERALES_PROVIDER] Relación con departamento principal actualizada: {$department_id}");
                        } catch (Exception $e) {
                            \Log::error('[SAVE_GENERALES_PROVIDER] Error al guardar departamento: ' . $e->getMessage());
                        }
                    }

                    $msg = 'ok';
                    \Log::info('[SAVE_GENERALES_PROVIDER] Cambios realizados: ' . implode(', ', $cambios));
                } elseif (!$errors) {
                    $msg = 'no_changes';
                }
            } else {
                $msg = 'No se encontró el proveedor.';
            }
        } else {
            $msg = 'Acceso no autorizado.';
        }
    } else {
        $msg = 'Petición inválida.';
    }

    $this->response([
        'msg' => $msg,
        'errors' => $errors
    ]);
}



/**
 * OBTIENE LOS DATOS FISCALES DE UN PROVEEDOR
 * SI NO EXISTEN, SE TOMAN DEL REGISTRO DEL PROVEEDOR Y DE SU DIRECCIÓN PRINCIPAL
 *
 * @return JSON CON LOS DATOS FISCALES Y OPCIONES PARA LOS SELECTS
 */
public function post_get_fiscal_opts_provider()
{
	$msg    = '';
	$errors = [];

	# VALIDAR SI LA PETICIÓN ES AJAX
	if (!Input::is_ajax()) {
		return $this->response(['msg' => 'PETICIÓN INVÁLIDA.', 'errors' => $errors]);
	}

	# OBTENER DATOS DE AUTENTICACIÓN
	$access_id    = Input::post('access_id');
	$access_token = Input::post('access_token');
	$provider_id  = Input::post('provider_id');

	# VALIDAR USUARIO
	$user = Model_User::query()->where('id', $access_id)->get_one();
	if (!$user || md5($user->login_hash) != $access_token) {
		return $this->response(['msg' => 'ACCESO NO AUTORIZADO.', 'errors' => $errors]);
	}

	# SE INICIALIZA VARIABLES
	$editing  = false;
	$csf_link = '';

	# BUSCAR SI YA EXISTEN DATOS FISCALES
	$data = Model_Providers_Tax_Datum::query()
		->where('provider_id', $provider_id)
		->get_one();

	# SI EXISTEN, SE USAN PARA EDICIÓN
	if ($data) {
		$editing = true;
		if (!empty($data->csf)) {
			$csf_link = Uri::base(false) . $data->csf;
		}
	}
	# SI NO EXISTEN, SE TOMAN DATOS BASE
	else {
		$data = new stdClass();
		$data->business_name     = '';
		$data->rfc               = '';
		$data->street            = '';
		$data->number            = '';
		$data->internal_number   = '';
		$data->colony            = '';
		$data->zipcode           = '';
		$data->city              = '';
		$data->municipality      = '';
		$data->state_id          = '';
		$data->sat_tax_regime_id = '';
		$data->payment_method_id = '';
		$data->cfdi_id           = '';
		$data->email             = '';

		# SE TOMA RFC Y RAZÓN SOCIAL DEL PROVEEDOR
		$provider = Model_Provider::find($provider_id);
		if ($provider) {
			$data->business_name = $provider->name;
			$data->rfc           = $provider->rfc;
		}

		# SE BUSCA LA PRIMERA DIRECCIÓN DEL PROVEEDOR
		$address = Model_Providers_Address::query()
			->where('provider_id', $provider_id)
			->where('default', 1) // Dirección por defecto
			->order_by('id', 'asc')
			->get_one();

		if ($address) {
			$data->street          = $address->street;
			$data->number          = $address->number;
			$data->internal_number = $address->internal_number;
			$data->colony          = $address->colony;
			$data->zipcode         = $address->zipcode;
			$data->city            = $address->city;
			$data->municipality    = $address->municipality;
			$data->state_id        = $address->state_id;
		}
	}

	# SE GENERAN OPCIONES PARA LOS SELECTS
	$states_opts_html = '<option value="">Seleccionar...</option>';
	foreach (Model_State::query()->order_by('name', 'asc')->get() as $state) {
		$selected = ($data && $data->state_id == $state->id) ? 'selected' : '';
		$states_opts_html .= "<option value='{$state->id}' {$selected}>{$state->name}</option>";
	}

	$sat_tax_regime_opts_html = '<option value="">Seleccionar...</option>';
	foreach (Model_Sat_Tax_Regime::query()->order_by('name', 'asc')->get() as $regime) {
		$selected = ($data && $data->sat_tax_regime_id == $regime->id) ? 'selected' : '';
		$sat_tax_regime_opts_html .= "<option value='{$regime->id}' {$selected}>{$regime->name}</option>";
	}

	$payment_method_opts_html = '<option value="">Seleccionar...</option>';
	foreach (Model_Payments_Method::query()->order_by('name', 'asc')->get() as $payment) {
		$selected = ($data && $data->payment_method_id == $payment->id) ? 'selected' : '';
		$payment_method_opts_html .= "<option value='{$payment->id}' {$selected}>{$payment->name}</option>";
	}

	$cfdi_opts_html = '<option value="">Seleccionar...</option>';
	foreach (Model_Cfdi::query()->order_by('name', 'asc')->get() as $cfdi) {
		$selected = ($data && $data->cfdi_id == $cfdi->id) ? 'selected' : '';
		$cfdi_opts_html .= "<option value='{$cfdi->id}' {$selected}>{$cfdi->name}</option>";
	}

	# SE RETORNAN LOS DATOS
	return $this->response([
		'msg'                       => 'ok',
		'editing'                   => $editing,
		'business_name'             => $data->business_name ?? '',
		'rfc'                       => $data->rfc ?? '',
		'street'                    => $data->street ?? '',
		'number'                    => $data->number ?? '',
		'internal_number'           => $data->internal_number ?? '',
		'colony'                    => $data->colony ?? '',
		'city'                      => $data->city ?? '',
		'municipality'              => $data->municipality ?? '',
		'state_id'                  => $data->state_id ?? '',
		'zipcode'                   => $data->zipcode ?? '',
		'sat_tax_regime_id'         => $data->sat_tax_regime_id ?? '',
		'payment_method_id'         => $data->payment_method_id ?? '',
		'cfdi_id'                   => $data->cfdi_id ?? '',
		'email'                     => $data->email ?? '',
		'csf_link'                  => $csf_link,
		'states_opts_html'          => $states_opts_html,
		'sat_tax_regime_opts_html'  => $sat_tax_regime_opts_html,
		'payment_method_opts_html'  => $payment_method_opts_html,
		'cfdi_opts_html'            => $cfdi_opts_html,
	]);
}





/**
 * GUARDA LOS DATOS FISCALES DEL PROVEEDOR
 *
 * @access  private
 * @return  JSON
 */
public function post_save_fiscal_provider()
{
	\Log::info('[SAVE_FISCAL_PROVIDER] INICIANDO GUARDADO DE DATOS FISCALES DE PROVEEDOR');
	$msg = '';
	$errors = [];

	if (Input::is_ajax()) {
		$access_id    = Input::post('access_id');
		$access_token = Input::post('access_token');
		$provider_id  = Input::post('provider_id');

		$access_user = Model_User::find($access_id);
		if ($access_user && md5($access_user->login_hash) == $access_token) {
			$data = Model_Providers_Tax_Datum::query()
				->where('provider_id', $provider_id)
				->where('default', 1)
				->get_one();

			if (!$data) {
				$data = Model_Providers_Tax_Datum::forge();
				$data->provider_id = $provider_id;
				$data->default = 1;
			}

			$data->business_name     = Input::post('business_name');
			$data->rfc               = Input::post('rfc');
			$data->street            = Input::post('street');
			$data->number            = Input::post('number');
			$data->internal_number   = Input::post('internal_number');
			$data->colony            = Input::post('colony');
			$data->zipcode           = Input::post('zipcode');
			$data->city              = Input::post('city');
			$data->municipality      = Input::post('municipality');
			$data->state_id          = Input::post('state_id');
			$data->sat_tax_regime_id = Input::post('sat_tax_regime_id');
			$data->payment_method_id = Input::post('payment_method_id');
			$data->cfdi_id           = Input::post('cfdi_id');
			$data->email             = Input::post('email');
			$data->updated_at        = time();

			// GUARDAR ARCHIVO CSF SI EXISTE, SI NO ASIGNAR VACÍO
			if (!empty($_FILES['csf']) && $_FILES['csf']['error'] == 0) {
				$filename = 'csf_' . $provider_id . '_' . time() . '.pdf';
				$upload_path = DOCROOT . 'assets/uploads/csf/';
				if (!file_exists($upload_path)) {
					mkdir($upload_path, 0777, true);
				}

				if (move_uploaded_file($_FILES['csf']['tmp_name'], $upload_path . $filename)) {
					$data->csf = 'assets/uploads/csf/' . $filename; // Asegúrate de guardar la ruta relativa
				} else {
					\Log::error('[SAVE_FISCAL_PROVIDER] ERROR al mover el archivo CSF.');
					$data->csf = ''; // Fallback vacío
				}
			} else {
				// SI NO SE SUBIÓ ARCHIVO Y ES NUEVO, ASIGNAR VACÍO
				if (!$data->csf) {
					$data->csf = '';
				}
			}
			
			// GUARDAR ARCHIVO OPC SI EXISTE, SI NO ASIGNAR VACÍO
			if (!empty($_FILES['opc']) && $_FILES['opc']['error'] == 0) {
				$filename = 'opc_' . $provider_id . '_' . time() . '.pdf';
				$upload_path = DOCROOT . 'uploads/opc/';
				if (!file_exists($upload_path)) {
					mkdir($upload_path, 0777, true);
				}

				if (move_uploaded_file($_FILES['opc']['tmp_name'], $upload_path . $filename)) {
					$data->opc = 'uploads/opc/' . $filename; // Asegúrate de guardar la ruta relativa
				} else {
					\Log::error('[SAVE_FISCAL_PROVIDER] ERROR al mover el archivo OPC.');
					$data->opc = ''; // Fallback vacío
				}
			} else {
				// SI NO SE SUBIÓ ARCHIVO Y ES NUEVO, ASIGNAR VACÍO
				if (!$data->opc) {
					$data->opc = '';
				}
			}


			$data->save();

			$msg = 'ok';
			\Log::info('[SAVE_FISCAL_PROVIDER] Datos fiscales actualizados correctamente.');
		} else {
			$msg = 'Acceso no autorizado.';
		}
	} else {
		$msg = 'Petición inválida.';
	}

	$this->response([
		'msg' => $msg,
		'errors' => $errors
	]);
}

/**
 * OBTIENE LOS DATOS DE UN CONTACTO DEL PROVEEDOR
 *
 * @access  private
 * @return  JSON
 */
public function post_get_contacto_opts_provider()
{
	$msg = '';
	$errors = [];

	if (Input::is_ajax()) {
		$access_id    = Input::post('access_id');
		$access_token = Input::post('access_token');
		$provider_id  = Input::post('provider_id');
		$contact_id   = Input::post('contact_id');

		$user = Model_User::find($access_id);
		if ($user && md5($user->login_hash) == $access_token) {
			if ($contact_id > 0) {
				$data = Model_Providers_Contact::find($contact_id);
			} else {
				$data = null;
			}

			$msg = 'ok';
			$this->response([
				'msg'        => $msg,
				'idcontact'  => $data ? $data->idcontact : '',
				'name'       => $data ? $data->name : '',
				'last_name'  => $data ? $data->last_name : '',
				'phone'      => $data ? $data->phone : '',
				'cel'        => $data ? $data->cel : '',
				'email'      => $data ? $data->email : '',
				'departments'=> $data ? $data->departments : '',
			]);
			return;
		} else {
			$msg = 'Acceso no autorizado.';
		}
	} else {
		$msg = 'Petición inválida.';
	}

	$this->response(['msg' => $msg, 'errors' => $errors]);
}

/*Guarda un contacto de proveedor
 *
 * @access  private
 * @return  JSON
 */
public function post_save_contacto_provider()
{
	\Log::info('[SAVE_CONTACTO_PROVIDER] INICIANDO GUARDADO DE CONTACTO DE PROVEEDOR');
	$msg = '';
	$errors = [];

	if (Input::is_ajax()) {
		$access_id    = Input::post('access_id');
		$access_token = Input::post('access_token');
		$provider_id  = Input::post('provider_id');
		$contact_id   = Input::post('contact_id');

		$access_user = Model_User::find($access_id);
		if ($access_user && md5($access_user->login_hash) == $access_token) {

			if ($contact_id > 0) {
				$data = Model_Providers_Contact::find($contact_id);
			} else {
				$data = Model_Providers_Contact::forge();
				$data->provider_id = $provider_id;
			}

			$data->idcontact   			= Input::post('idcontact');
			$data->name        			= Input::post('name');
			$data->last_name   			= Input::post('last_name');
			$data->phone       			= Input::post('phone');
			$data->cel         			= Input::post('cel');
			$data->email       			= Input::post('email');
			$data->departments 			= Input::post('departments');
			$data->provider_delivery_id = Input::post('partner_delivery_id', '');
			$data->default 				= Input::post('default', '0');
			$data->deleted 				= Input::post('deleted', '0');
			$data->updated_at  			= time();

			$data->save();

			$msg = 'ok';
			\Log::info('[SAVE_CONTACTO_PROVIDER] Contacto guardado correctamente.');
		} else {
			$msg = 'Acceso no autorizado.';
		}
	} else {
		$msg = 'Petición inválida.';
	}

	$this->response([
		'msg' => $msg,
		'errors' => $errors
	]);
}


/**
 * OBTIENE LOS DATOS DE UN DOMICILIO DE ENTREGA DEL PROVEEDOR
 *
 * @access  private
 * @return  JSON
 */
public function post_get_entrega_opts_provider()
{
	\Log::info('[GET_ENTREGA_OPTS_PROVIDER] INICIANDO');

	$msg = '';
	$errors = [];

	if (Input::is_ajax()) {
		$access_id    = Input::post('access_id');
		$access_token = Input::post('access_token');
		$entrega_id   = (int) Input::post('entrega_id');
		$provider_id  = (int) Input::post('provider_id');

		$user = Model_User::find($access_id);
		if (!$user || md5($user->login_hash) != $access_token) {
			\Log::error('[GET_ENTREGA_OPTS_PROVIDER] Acceso no autorizado');
			return $this->response(['msg' => 'Acceso no autorizado.']);
		}

		$data = [
			'iddelivery'      => '',
			'street'          => '',
			'number'          => '',
			'internal_number' => '',
			'colony'          => '',
			'city'            => '',
			'municipality'    => '',
			'state_id'        => '',
			'zipcode'         => '',
			'reception_hours' => '',
			'delivery_notes'  => '',
			'default'         => '',
		];

		if ($entrega_id > 0) {
			$entrega = Model_Providers_Delivery::find($entrega_id);
			if ($entrega) {
				$data['iddelivery']      = $entrega->iddelivery;
				$data['street']          = $entrega->street;
				$data['number']          = $entrega->number;
				$data['internal_number'] = $entrega->internal_number;
				$data['colony']          = $entrega->colony;
				$data['city']            = $entrega->city;
				$data['municipality']    = $entrega->municipality;
				$data['state_id']        = $entrega->state_id;
				$data['zipcode']         = $entrega->zipcode;
				$data['reception_hours'] = $entrega->reception_hours;
				$data['delivery_notes']  = $entrega->delivery_notes;
				$data['default']         = (int) $entrega->default;
			}
		}

		// Estados
		$state_opts_html = '<option value="">Seleccionar...</option>';
		foreach (Model_State::query()->order_by('name', 'asc')->get() as $state) {
			$selected = ($data['state_id'] == $state->id) ? 'selected' : '';
			$state_opts_html .= "<option value='{$state->id}' {$selected}>{$state->name}</option>";
		}

		\Log::info('[GET_ENTREGA_OPTS_PROVIDER] Datos cargados para entrega_id=' . $entrega_id);
		return $this->response(array_merge(['msg' => 'ok', 'state_opts_html' => $state_opts_html], $data));
	}

	$this->response(['msg' => 'Petición inválida.']);
}


/**
 * GUARDA O EDITA UN DOMICILIO DE ENTREGA DE PROVEEDOR
 *
 * @access  private
 * @return  JSON
 */
public function post_save_entrega_provider()
{
	\Log::info('[SAVE_ENTREGA_PROVIDER] INICIANDO GUARDADO DE ENTREGA');
	$msg = '';
	$errors = [];

	if (Input::is_ajax()) {
		$access_id     = Input::post('access_id');
		$access_token  = Input::post('access_token');
		$entrega_id    = (int) Input::post('entrega_id');
		$provider_id   = (int) Input::post('provider_id');

		$fields = [
			'iddelivery', 'street', 'number', 'internal_number', 'colony', 'city',
			'municipality', 'state_id', 'zipcode', 'reception_hours', 'delivery_notes',
			'name', 'last_name', 'phone', 'default'
		];

		$data = [];
		foreach ($fields as $field) {
			$data[$field] = trim(Input::post($field));
		}
		$data['default'] = (int) $data['default'];

		$user = Model_User::find($access_id);
		if ($user && md5($user->login_hash) == $access_token) {
			if ($entrega_id > 0) {
				$entrega = Model_Providers_Delivery::find($entrega_id);
				if ($entrega) {
					foreach ($data as $key => $val) {
						if ($key === 'state_id') {
							if ($entrega->state_id != $val) {
								$entrega->state_id = $val;
							}
						} elseif (property_exists($entrega, $key) && $entrega->$key != $val) {
							$entrega->$key = $val;
						}
					}
					$entrega->updated_at = time();
					$entrega->save();
					$msg = 'ok';
					\Log::info('[SAVE_ENTREGA_PROVIDER] ENTREGA ACTUALIZADA');
				} else {
					$msg = 'No se encontró el domicilio.';
				}
			} else {
				// Verifica duplicados
				$existe = Model_Providers_Delivery::query()
					->where('provider_id', $provider_id)
					->where('iddelivery', $data['iddelivery'])
					->get_one();

				if ($existe) {
					$errors[] = 'Ya existe un domicilio con ese identificador.';
				} else {
					$entrega = Model_Providers_Delivery::forge([
						'provider_id'      => $provider_id,
						'iddelivery'       => $data['iddelivery'],
						'street'           => $data['street'],
						'number'           => $data['number'],
						'internal_number'  => $data['internal_number'],
						'colony'           => $data['colony'],
						'city'             => $data['city'],
						'municipality'     => $data['municipality'],
						'state_id'         => $data['state_id'],
						'zipcode'          => $data['zipcode'],
						'reception_hours'  => $data['reception_hours'],
						'delivery_notes'   => $data['delivery_notes'],
						'default'          => $data['default'],
						'deleted'          => 0,
						'created_at'       => time(),
						'updated_at'       => time(),
					]);

					$entrega->save();

					// Genera contacto asociado
					$contact = Model_Providers_Contact::forge([
						'idcontact'           => 'ENT-' . strtoupper($data['iddelivery']),
						'provider_id'         => $provider_id,
						'provider_delivery_id'=> $entrega->id,
						'name'                => $data['name'],
						'last_name'           => $data['last_name'],
						'phone'               => $data['phone'],
						'cel'                 => '',
						'email'               => '',
						'departments'         => '',
						'default'             => 1,
						'deleted'             => 0,
						'created_at'          => time(),
						'updated_at'          => time(),
					]);
					$contact->save();

					$msg = 'ok';
					\Log::info('[SAVE_ENTREGA_PROVIDER] NUEVO DOMICILIO Y CONTACTO GUARDADOS');
				}
			}
		} else {
			$msg = 'Acceso no autorizado.';
			\Log::error('[SAVE_ENTREGA_PROVIDER] Token inválido');
		}
	} else {
		$msg = 'Petición inválida.';
		\Log::error('[SAVE_ENTREGA_PROVIDER] Método no AJAX');
	}

	$this->response(['msg' => $msg, 'errors' => $errors]);
}

/**Subir constancia */
public function post_parse_csf_provider()
{
    \Log::info('[PARSE_CSF_PROVIDER] INICIANDO PROCESO');

    $access_id    = Input::post('access_id');
    $access_token = Input::post('access_token');
    $provider_id  = Input::post('provider_id');

    // VALIDACIÓN DE USUARIO
    $user = Model_User::find($access_id);
    if (!$user || md5($user->login_hash) !== $access_token) {
        return $this->response(['msg' => 'Acceso no autorizado.']);
    }

    // VALIDACIÓN DE ARCHIVO PDF
    if (!isset($_FILES['csf']) || $_FILES['csf']['error'] !== 0) {
        return $this->response(['msg' => 'Archivo PDF no válido.']);
    }

    $ext = strtolower(pathinfo($_FILES['csf']['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        return $this->response(['msg' => 'El archivo debe ser un PDF.']);
    }

    // GUARDAR TEMPORALMENTE
    $tmp_dir = DOCROOT . 'uploads/tmp/';
    if (!is_dir($tmp_dir)) {
        mkdir($tmp_dir, 0777, true);
    }

    $filename = 'csf_' . uniqid() . '.pdf';
    $full_path = $tmp_dir . $filename;
    move_uploaded_file($_FILES['csf']['tmp_name'], $full_path);

    \Log::info("[PARSE_CSF_PROVIDER] Archivo guardado: {$full_path}");

    try {
        // PARSEAR PDF
        $datos = Helper_CsfParser::parse($full_path);
        \Log::info('[PARSE_CSF_PROVIDER] DATOS EXTRAÍDOS DEL CSF: ' . json_encode($datos));

        if (empty($datos['rfc'])) {
            return $this->response(['msg' => 'No se pudo extraer el RFC del PDF.']);
        }

        // OBTENER DATOS DEL PROVEEDOR ACTUAL
        $provider = Model_Provider::find($provider_id);
        if (!$provider) {
            return $this->response(['msg' => 'Proveedor no encontrado.']);
        }

        \Log::info('[PARSE_CSF_PROVIDER] RFC DEL PROVEEDOR: ' . $provider->rfc);
        \Log::info('[PARSE_CSF_PROVIDER] NOMBRE DEL PROVEEDOR: ' . $provider->name);

        // VALIDAR RFC
        if (strtoupper(trim($datos['rfc'])) !== strtoupper(trim($provider->rfc))) {
            return $this->response([
                'msg' => "El RFC del archivo no coincide con el del proveedor actual.\n\n"
                       . "RFC en PDF: {$datos['rfc']} ({$datos['business_name']})\n"
                       . "RFC del proveedor: {$provider->rfc} ({$provider->name})\n\n"
                       . "Verifica que el proveedor sea el correcto o da de alta uno nuevo.",
            ]);
        }

        // TODO CORRECTO
        return $this->response([
            'msg'         => 'ok',
            'provider_id' => $provider_id,
            'data'        => $datos,
        ]);
    } catch (Exception $e) {
        \Log::error('[PARSE_CSF_PROVIDER] ERROR: ' . $e->getMessage());
        return $this->response(['msg' => 'Error al procesar el archivo.']);
    }
}


/**
 * OBTIENE LOS DATOS DE UNA CUENTA BANCARIA DEL PROVEEDOR
 *
 * @access  private
 * @return  JSON
 */
public function post_get_banco_opts_provider()
{
    $msg = '';
    $errors = [];

    if (Input::is_ajax()) {
        $access_id    = Input::post('access_id');
        $access_token = Input::post('access_token');
        $provider_id  = Input::post('provider_id');
        $bank_id      = Input::post('bank_id');

        $user = Model_User::find($access_id);
        if ($user && md5($user->login_hash) == $access_token) {
            if ($bank_id > 0) {
                $data = Model_Providers_Account::find($bank_id);
            } else {
                $data = null;
            }

             // -- LLENADO DE OPCIONES DE BANCO --
            $bank_opts_html = '<option value="">Seleccionar banco...</option>';
			foreach (Model_Bank::query()->order_by('name', 'asc')->get() as $bank) {
				$selected = $data && $data->bank_id == $bank->id ? 'selected' : '';
				$bank_opts_html .= "<option value='{$bank->id}' {$selected}>{$bank->name}</option>";
			}

			 // -- LLENADO DE OPCIONES DE MONEDA --
            $currency_opts_html =  '<option value="">Seleccionar moneda...</option>';
			foreach (Model_Currency::query()->order_by('name', 'asc')->get() as $currency) {
				$selected = $data && $data->currency_id == $currency->id ? 'selected' : '';
				$currency_opts_html .= "<option value='{$currency->id}' {$selected}>{$currency->name}</option>";
			}

            $msg = 'ok';
            $this->response([
                'msg'              => $msg,
                'bank_opts_html'   => $bank_opts_html,
                'currency_opts_html' => $currency_opts_html,
                'bank_id'          => $data ? $data->bank_id : '',
                'currency_id'      => $data ? $data->currency_id : '',
                'account_number'   => $data ? $data->account_number : '',
                'clabe'            => $data ? $data->clabe : '',
                'name'             => $data ? $data->name : '',
                'email'            => $data ? $data->email : '',
                'phone'            => $data ? $data->phone : '',
                'pay_days'         => $data ? $data->pay_days : '',
                'default'          => $data ? $data->default : 0,
            ]);
            return;
        } else {
            $msg = 'Acceso no autorizado.';
        }
    } else {
        $msg = 'Petición inválida.';
    }

    $this->response(['msg' => $msg, 'errors' => $errors]);
}


/**
 * GUARDA UNA CUENTA BANCARIA DEL PROVEEDOR
 *
 * @access  private
 * @return  JSON
 */
public function post_save_banco_provider()
{
    \Log::info('[SAVE_BANCO_PROVIDER] INICIANDO GUARDADO DE CUENTA BANCARIA');
    $msg = '';
    $errors = [];

    if (Input::is_ajax()) {
        $access_id    = Input::post('access_id');
        $access_token = Input::post('access_token');
        $provider_id  = Input::post('provider_id');
        $bank_id      = Input::post('bank_id');

        $access_user = Model_User::find($access_id);
        if ($access_user && md5($access_user->login_hash) == $access_token) {

            if ($bank_id > 0) {
                $data = Model_Providers_Account::find($bank_id);
                $data->updated_at = time();
            } else {
                $data = Model_Providers_Account::forge();
                $data->provider_id = $provider_id;
                $data->created_at  = time();
                $data->updated_at  = time();
            }

            // Asignar campos
            $data->bank_id        = Input::post('bank_id_val');
            $data->currency_id    = Input::post('currency_id');
            $data->account_number = Input::post('account_number');
            $data->clabe          = Input::post('clabe');
            $data->name           = Input::post('name');
            $data->email          = Input::post('email');
            $data->phone          = Input::post('phone');
            $data->pay_days       = Input::post('pay_days');
            $data->default        = Input::post('default', '0');

            // Si es nueva y la pones default, actualiza las otras a 0
            if (Input::post('default') == 1 && $provider_id) {
                // ¡IMPORTANTE!: Usa el nombre correcto de tu tabla en la siguiente línea
                \DB::update('providers_accounts') // <-- CORRIGE AQUÍ EL NOMBRE DE LA TABLA
                    ->set(['default' => 0])
                    ->where('provider_id', '=', $provider_id)
                    ->execute();
                $data->default = 1;
            }

			// === SUBIDA DE CARÁTULA BANCARIA SEGURA ===
			if (isset($_FILES['bank_cover']) && !empty($_FILES['bank_cover']['name'])) {
				$file = $_FILES['bank_cover'];

				// Validar tamaño y extensión
				$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
				$allowed = ['pdf','jpg','jpeg','png','bmp'];
				if (!in_array($ext, $allowed)) {
					$errors[] = 'Formato de archivo no permitido.';
				} elseif ($file['size'] > 5 * 1024 * 1024) { // 5 MB
					$errors[] = 'El archivo excede el tamaño máximo permitido (5 MB).';
				} else {
					$new_name = time().'_'.preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file['name']);
					$destino = DOCROOT.'uploads/providers/banks/'.$new_name;

					if (move_uploaded_file($file['tmp_name'], $destino)) {
						$data->bank_cover = $new_name;
						\Log::info('[SAVE_BANCO_PROVIDER] Carátula bancaria guardada: '.$new_name);
					} else {
						$errors[] = 'Error al guardar el archivo.';
					}
				}
			}


            $data->save();

            $msg = 'ok';
            \Log::info('[SAVE_BANCO_PROVIDER] Cuenta bancaria guardada correctamente.');
        } else {
            $msg = 'Acceso no autorizado.';
        }
    } else {
        $msg = 'Petición inválida.';
    }

    $this->response([
        'msg' => $msg,
        'errors' => $errors
    ]);
}

/**
 * VISUALIZA LOS DEPARTAMENTOS DADOS DE ALTA
 */
public function post_get_departments_opts()
{
    $departments = Model_Employees_Department::query()
        ->order_by('name', 'asc')
        ->get();

    $opts_html = '';
    foreach ($departments as $d) {
        $opts_html .= "<option value='{$d->id}'>{$d->name}</option>";
    }

    $this->response(['msg' => 'ok', 'departments_opts_html' => $opts_html]);
}

/**
 * GUARDA LOS DEPARTAMENTOS QUE ATIENDE EL PROVEEDOR
 */
public function post_save_department_provider()
{
    $provider_id   = (int) Input::post('provider_id');
    $department_id = (int) Input::post('employees_department_id');
    $main          = (int) Input::post('main', 0);

    \Log::info("[DEPARTAMENTO][SAVE] Iniciando guardado: provider_id={$provider_id}, dept_id={$department_id}, main={$main}");

    try {
        // Validar proveedor
        $provider = Model_Provider::find($provider_id);
        if (!$provider) {
            \Log::error("[DEPARTAMENTO][SAVE] Error: provider_id {$provider_id} no existe");
            return \Response::forge(json_encode(['msg' => 'Proveedor no encontrado']))
                ->set_header('Content-Type', 'application/json')
                ->set_status(400);
        }

        // Validar departamento
        $dept = Model_Employees_Department::find($department_id);
        if (!$dept) {
            \Log::error("[DEPARTAMENTO][SAVE] Error: department_id {$department_id} no existe");
            return \Response::forge(json_encode(['msg' => 'Departamento no válido']))
                ->set_header('Content-Type', 'application/json')
                ->set_status(400);
        }

        // Si se marca como principal, quitar bandera main de otros
        if ($main == 1) {
            \DB::update('providers_departments')
                ->set(['main' => 0])
                ->where('provider_id', '=', $provider_id)
                ->execute();
        }

        // Buscar relación existente (sin importar si está eliminada)
        $exists = Model_Providers_Department::query()
            ->where('provider_id', $provider_id)
            ->where('employees_department_id', $department_id)
            ->get_one();

        if (!$exists) {
            // No existe, crear nueva
            \Log::info("[DEPARTAMENTO][SAVE] Creando nueva relación provider={$provider_id}, dept={$department_id}");
            $exists = Model_Providers_Department::forge([
                'provider_id'             => $provider_id,
                'employees_department_id' => $department_id,
                'main'                    => $main,
                'deleted'                 => 0,
                'created_at'              => time(),
                'updated_at'              => time(),
            ]);
        } else {
            if ($exists->deleted == 1) {
                // Ya existía pero fue eliminado -> crear NUEVO registro para historial
                \Log::info("[DEPARTAMENTO][SAVE] Existía eliminado (id={$exists->id}), creando nuevo registro para mantener historial");
                $exists = Model_Providers_Department::forge([
                    'provider_id'             => $provider_id,
                    'employees_department_id' => $department_id,
                    'main'                    => $main,
                    'deleted'                 => 0,
                    'created_at'              => time(),
                    'updated_at'              => time(),
                ]);
            } else {
                // Actualizar registro existente activo
                \Log::info("[DEPARTAMENTO][SAVE] Actualizando relación existente id={$exists->id}");
                $exists->main       = $main;
                $exists->updated_at = time();
            }
        }

        $exists->save();
        \Log::info("[DEPARTAMENTO][SAVE] Relación guardada correctamente.");

        return \Response::forge(json_encode(['msg' => 'ok']))
            ->set_header('Content-Type', 'application/json')
            ->set_status(200);

    } catch (\Database_Exception $e) {
        \Log::error('[DEPARTAMENTO][SAVE] Database error: ' . $e->getMessage());
        return \Response::forge(json_encode(['msg' => 'error', 'errors' => [$e->getMessage()]]))
            ->set_header('Content-Type', 'application/json')
            ->set_status(500);
    } catch (\Exception $e) {
        \Log::error('[DEPARTAMENTO][SAVE] Error general: ' . $e->getMessage());
        return \Response::forge(json_encode(['msg' => 'error', 'errors' => [$e->getMessage()]]))
            ->set_header('Content-Type', 'application/json')
            ->set_status(500);
    }
}



/**
 * PONE COMO DEFAULT O DEPARTAMENTO PRINCIPAL DE SURTIDO 
 * O EN EL QUE EL PROVEEDOR SURTE MAS
 */
public function post_set_main_department_provider()
{
    $id = Input::post('id');
    $dept = Model_Providers_Department::find($id);
    if ($dept) {
        \DB::update('providers_departments')
            ->set(['main' => 0])
            ->where('provider_id', '=', $dept->provider_id)
            ->execute();

        $dept->main = 1;
        $dept->save();
        return $this->response(['msg' => 'ok']);
    }
    return $this->response(['msg' => 'No encontrado']);
}

/**
 * ESTE ES PARA ELIMINAR EL DEPARTAMENTO RELACONADO AL PROVEEDOR
 * 
 */
public function post_delete_department_provider()
{
    $id = Input::post('id');
    $dept = Model_Providers_Department::find($id);

    if ($dept) {
        $dept->deleted = 1;
        $dept->updated_at = time();
        $dept->save();

        return \Response::forge(json_encode(['msg' => 'ok']))
            ->set_header('Content-Type', 'application/json')
            ->set_status(200);
    }

    return \Response::forge(json_encode(['msg' => 'No encontrado']))
        ->set_header('Content-Type', 'application/json')
        ->set_status(404);
}


// ===============================
// CONTRATOS PROVEEDOR – GET OPTS
// ===============================
public function post_get_contrato_opts_provider()
{
    if (!Input::is_ajax()) return $this->response(['msg'=>'invalid']);
    $provider_id = (int) Input::post('provider_id', 0);
    $contract_id = (int) Input::post('contract_id', 0);

    // Seguridad básica
    if (!Helper_Permission::can('config_proveedores', 'edit')) {
        return $this->response(['msg' => 'No autorizado']);
    }

    // provider -> user_id
    $prov = Model_Provider::find($provider_id);
    if (!$prov) return $this->response(['msg'=>'Proveedor no encontrado']);
    $user_id = (int) $prov->user_id;

    // Defaults
    $resp = [
        'msg'        => 'ok',
        'user_id'    => $user_id,
        'title'      => '',
        'code'       => '',
        'category'   => '',
        'status'     => 0,
        'start_date' => '',
        'end_date'   => '',
        'description'=> '',
        // Opciones de categoría y status para armar selects
        'category_opts_html' =>
            '<option value="">— Selecciona —</option>'.
            '<option value="NDA">NDA</option>'.
            '<option value="Comercial">Comercial</option>'.
            '<option value="Servicios">Servicios</option>'.
            '<option value="Proveedor">Proveedor</option>'.
            '<option value="Otro">Otro</option>',
        'status_opts_html' =>
            '<option value="0">Borrador</option>'.
            '<option value="1">Vigente</option>'.
            '<option value="2">Vencido</option>'.
            '<option value="3">Cancelado</option>',
    ];

    if ($contract_id > 0) {
        $c = Model_Legal_Contract::find($contract_id);
        if (!$c || $c->deleted == 1) return $this->response(['msg'=>'Contrato no encontrado']);
        // Garantizar pertenencia por user_id
        if ((int)$c->user_id !== $user_id) return $this->response(['msg'=>'No autorizado']);

        $resp['title']       = $c->title;
        $resp['code']        = $c->code;
        $resp['category']    = $c->category;
        $resp['status']      = (int)$c->status;
        $resp['start_date']  = $c->start_date ?: '';
        $resp['end_date']    = $c->end_date   ?: '';
        $resp['description'] = $c->description ?: '';
        $resp['pdf_link']    = !empty($c->file_path) ? Uri::base(false).$c->file_path : '';
    }

    return $this->response($resp);
}

// ===============================
// CONTRATOS PROVEEDOR – SAVE
// alta/edición + PDF opcional
// ===============================
public function post_save_contrato_provider()
{
    if (!Input::is_ajax()) return $this->response(['msg'=>'invalid']);
    if (!Helper_Permission::can('config_proveedores', 'edit')) return $this->response(['msg'=>'No autorizado']);

    $provider_id = (int) Input::post('provider_id', 0);
    $contract_id = (int) Input::post('contract_id', 0);

    $prov = Model_Provider::find($provider_id);
    if (!$prov) return $this->response(['msg'=>'Proveedor no encontrado']);
    $user_id = (int) $prov->user_id;

    // Datos
    $title       = trim(Input::post('title', ''));
    $code        = trim(Input::post('code', ''));
    $category    = trim(Input::post('category', ''));
    $status      = (int) Input::post('status', 0);
    $start_date  = trim(Input::post('start_date', ''));
    $end_date    = trim(Input::post('end_date', ''));
    $description = trim(Input::post('description', ''));

    // Validación
    $errors = [];
    if ($title === '') $errors[] = 'El título es obligatorio.';
    if ($start_date && $end_date && strtotime($end_date) < strtotime($start_date)) {
        $errors[] = 'La fecha final no puede ser anterior a la inicial.';
    }
    if (!empty($errors)) return $this->response(['msg'=>'error','errors'=>$errors]);

    // Crear o editar
    if ($contract_id > 0) {
        $c = Model_Legal_Contract::find($contract_id);
        if (!$c || $c->deleted == 1) return $this->response(['msg'=>'Contrato no encontrado']);
        if ((int)$c->user_id !== $user_id) return $this->response(['msg'=>'No autorizado']);

        $c->title       = $title;
        $c->code        = $code;
        $c->category    = $category;
        $c->status      = $status;
        $c->start_date  = $start_date ?: null;
        $c->end_date    = $end_date   ?: null;
        $c->description = $description;
        $c->authorized_by = Auth::get('id');
        $c->updated_at  = time();
    } else {
        $c = Model_Legal_Contract::forge([
            'title'       => $title,
            'code'        => $code,
            'category'    => $category,
            'user_id'     => $user_id,
            'status'      => $status,
            'start_date'  => $start_date ?: null,
            'end_date'    => $end_date   ?: null,
            'description' => $description,
            'authorized_by'=> Auth::get('id'),
            'deleted'     => 0,
            'created_at'  => time(),
            'updated_at'  => time(),
        ]);
    }

    // Manejo de PDF
    if (!empty($_FILES['contract_file']['name'])) {
        $dir = DOCROOT.'assets/uploads/legal/contracts/';
        if (!is_dir($dir)) @mkdir($dir, 0777, true);

        $cfg = [
            'path'          => $dir,
            'ext_whitelist' => ['pdf'],
            'randomize'     => true,
            'auto_rename'   => true,
        ];
        \Upload::process($cfg);
        if (\Upload::is_valid()) {
            \Upload::save();
            $f = \Upload::get_files(0);
            $saved = $f['saved_as'];
            // Renombrado amigable
            $slug = preg_replace('/[^a-z0-9]+/i', '_', strtolower($title ?: 'contrato'));
            $new  = 'contrato_'.$slug.'_'.date('Ymd_His').'.pdf';
            @rename($dir.$saved, $dir.$new);
            $c->file_path = 'assets/uploads/legal/contracts/'.$new;
        } else {
            $errs = \Upload::get_errors(0);
            $msgs = [];
            if (!empty($errs['errors'])) {
                foreach ($errs['errors'] as $er) $msgs[] = $er['message'];
            }
            return $this->response(['msg'=>'error','errors'=>($msgs ?: ['Archivo inválido'])]);
        }
    }

    $c->save();
    \Log::info('[PROVEEDOR][CONTRATO][SAVE] provider_id='.$provider_id.' user_id='.$user_id.' contract_id='.($c->id));

    return $this->response(['msg'=>'ok','id'=>$c->id]);
}

// ===============================
// CONTRATOS PROVEEDOR – DELETE
// ===============================
public function post_delete_contrato_provider()
{
    if (!Input::is_ajax()) return $this->response(['msg'=>'invalid']);
    if (!Helper_Permission::can('config_proveedores', 'delete')) return $this->response(['msg'=>'No autorizado']);

    $contract_id = (int) Input::post('contract_id', 0);
    $provider_id = (int) Input::post('provider_id', 0);

    $prov = Model_Provider::find($provider_id);
    if (!$prov) return $this->response(['msg'=>'Proveedor no encontrado']);
    $user_id = (int) $prov->user_id;

    $c = Model_Legal_Contract::find($contract_id);
    if (!$c || $c->deleted == 1) return $this->response(['msg'=>'Contrato no encontrado']);
    if ((int)$c->user_id !== $user_id) return $this->response(['msg'=>'No autorizado']);

    $c->deleted = 1;
    $c->save();
    \Log::info('[PROVEEDOR][CONTRATO][DELETE] contract_id='.$contract_id.' provider_id='.$provider_id);

    return $this->response(['msg'=>'ok']);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////AQUI TERMINA LOS GENERALES DEL PROVEEDOR///////////////////////////////////////////
//////////////////////////////////TODO LO QUE TENGA QUE VER CON DATOS DEL PROVEEDOR DEBE IR AQUI///////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////




///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////AQUI EMPIEZAN LOS ENDPOINTS PARA EL EDITOR DE PLANTILLAS///////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * ENDPOINT AJAX PARA CARGAR EL TEMPLATE ACTUAL
     * @access public
     * @return JSON
     */
   public function action_load($id = null)
{
    if (empty($id) || !is_numeric($id)) {
        return $this->response(['error'=>'ID inválido'], 400);
    }
    $plantilla = Model_Theme_Layout::find($id);
    if (!$plantilla) {
        return $this->response(['error'=>'No encontrada'], 404);
    }
    return $this->response([
        'html' => $plantilla->html ?? '',
        'css' => $plantilla->css ?? '',
        'components' => $plantilla->components ?? '',
        'styles' => $plantilla->styles ?? '',
    ]);
}


    /**
     * ENDPOINT AJAX PARA GUARDAR EL TEMPLATE EDITADO
     * @access public
     * @return JSON
     */
    public function action_save($id = null)
{
    // AGREGAR LOG PARA DEPURAR
    \Log::info('INICIO GUARDADO PLANTILLA: id=' . $id);

    if (empty($id) || !is_numeric($id)) {
        \Log::error('NO SE RECIBIÓ ID VÁLIDO PARA GUARDAR');
        return $this->response(['success' => false, 'msg' => 'ID inválido'], 400);
    }

    // OBTIENE EL RAW BODY DE LA PETICIÓN
    $data = json_decode(file_get_contents('php://input'), true);

    \Log::info('DATOS RECIBIDOS PARA GUARDAR PLANTILLA: ' . print_r($data, true));

    // VALIDACIÓN BÁSICA
    if (!$data) {
        \Log::error('NO SE RECIBIÓ DATA JSON VÁLIDA EN EL BODY');
        return $this->response(['success' => false, 'msg' => 'Datos vacíos'], 400);
    }

    // BUSCAR PLANTILLA EXISTENTE
    $plantilla = Model_Theme_Layout::find($id);

    if (!$plantilla) {
        \Log::error('NO SE ENCONTRÓ PLANTILLA id=' . $id);
        return $this->response(['success' => false, 'msg' => 'Plantilla no encontrada'], 404);
    }

    // ASIGNAR DATOS Y GUARDAR
    $plantilla->html = $data['html'] ?? '';
    $plantilla->css = $data['css'] ?? '';
    $plantilla->components = $data['components'] ?? '';
    $plantilla->styles = $data['styles'] ?? '';
    $plantilla->updated_at = date('Y-m-d H:i:s');
	$plantilla->preview = $data['preview'] ?? $plantilla->preview;

    if ($plantilla->save()) {
        \Log::info('PLANTILLA GUARDADA CORRECTAMENTE id=' . $id);
        return $this->response(['success' => true]);
    } else {
        \Log::error('ERROR AL GUARDAR PLANTILLA id=' . $id);
        return $this->response(['success' => false, 'msg' => 'No se pudo guardar'], 500);
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////AQUI TERMINAN LOS ENDPOINTS PARA EL EDITOR DE PLANTILLAS///////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




	/**************** */
	/**
	 * BUSCAR USUARIOS PARA CONSENTIMIENTOS
	 */
	public function post_catalogos_consentimientos()
    {
        if ( ! Input::is_ajax()) {
            return $this->response(['msg' => 'Petición inválida'], 400);
        }

        try {

            # Usuarios activos
			$users = Model_User::query()
				//->where('deleted', 0)
				->limit(50)
				->get();

			$users_arr = [];
			foreach ($users as $u) {
				$status = unserialize($u->profile_fields);

				$users_arr[] = [
					'id'        => $u->id,
					'username'  => $u->username,
					'email'     => $u->email,
					'full_name' => $status['full_name'] ?? '',
					'banned'    => $status['banned'] ?? false, // <- aquí sí lo mandas en el JSON
				];
			}

            # Documentos legales activos
            $docs = Model_Legal_Document::query()
                //->where('deleted', 0)
                ->where('active', 0) // activos
                ->order_by('title', 'asc')
                ->get();

            $docs_arr = [];
            foreach ($docs as $d) {
                $docs_arr[] = [
                    'id'        => $d->id,
                    'title'     => $d->title,
                    'shortcode' => $d->shortcode,
                    'version'   => $d->version,
                ];
            }

            # Canales por default
            $channels = [
                ['id' => 'web',    'name' => 'Web'],
                ['id' => 'app',    'name' => 'App'],
                ['id' => 'fisico', 'name' => 'Físico'],
                ['id' => 'otro',   'name' => 'Otro'],
            ];

            return $this->response([
                'msg'       => 'ok',
                'users'     => $users_arr,
                'documents' => $docs_arr,
                'channels'  => $channels,
            ]);
        } catch (\Exception $e) {
            \Log::error("[CONSENTIMIENTOS][CATÁLOGOS] Error: ".$e->getMessage());
            return $this->response(['msg' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * GUARDAR CONSENTIMIENTO
     */
    public function post_save()
    {
        if ( ! Input::is_ajax()) {
            return $this->response(['status'=>'error','msg'=>'Petición inválida'], 400);
        }

        $user_id     = Input::post('user_id');
        $document_id = Input::post('document_id');
        $accepted    = Input::post('accepted', 0);
        $channel     = Input::post('channel', 'web');
        $extra       = Input::post('extra', null);

        if (!$user_id || !$document_id) {
            return $this->response(['status'=>'error','msg'=>'Faltan datos obligatorios']);
        }

        try {
            $doc = Model_Legal_Document::find($document_id);

            $consent = Model_User_Consent::forge();
            $consent->user_id     = $user_id;
            $consent->document_id = $document_id;
            $consent->version     = $doc ? $doc->version : '1.0';
            $consent->accepted    = $accepted == 0 ? 0 : 1;
            $consent->channel     = $channel;
            $consent->extra       = $extra;
            $consent->ip_address  = Input::real_ip();
            $consent->user_agent  = Input::user_agent();
            $consent->accepted_at = time();

            $consent->save();

            return $this->response(['status'=>'ok']);
        } catch (\Exception $e) {
            \Log::error("[CONSENTIMIENTOS][SAVE] Error: ".$e->getMessage());
            return $this->response(['status'=>'error','msg'=>'No se pudo guardar el consentimiento']);
        }
    }

		//******
	// cookies
	//
	//  */

	/**
	 * Obtener preferencias actuales de cookies
	 */
	public function post_get_cookies_prefs()
	{
		try {
			$input        = json_decode(file_get_contents('php://input'), true);
			$access_id    = $input['access_id']    ?? null;
			$access_token = $input['access_token'] ?? null;
			$user_id      = null;

			// Validación dual: usuario autenticado o anónimo
			if ($access_id && $access_token) {
				$user = Model_User::find($access_id);
				if ($user && md5($user->login_hash) == $access_token) {
					$user_id = $user->id;
				} else {
					return $this->response([
						'success' => false,
						'message' => 'Acceso no autorizado.'
					], 403);
				}
			}

			$prefs = Helper_Legal::get_cookies_preferences($user_id);

			if ($prefs) {
				return $this->response([
					'success' => true,
					'prefs'   => [
						'necessary'       => (int)$prefs->necessary,
						'analytics'       => (int)$prefs->analytics,
						'marketing'       => (int)$prefs->marketing,
						'personalization' => (int)$prefs->personalization,
					]
				]);
			} else {
				return $this->response([
					'success' => false,
					'prefs'   => null,
				]);
			}
		} catch (\Exception $e) {
			\Log::error("[LEGAL][COOKIES] Error en post_get_cookies_prefs: " . $e->getMessage());
			return $this->response([
				'success' => false,
				'message' => 'Error al obtener preferencias de cookies'
			], 500);
		}
	}


	/**
	 * Guardar/actualizar preferencias de cookies
	 */
	public function post_update_cookies_prefs()
	{
		try {
			$input        = json_decode(file_get_contents('php://input'), true);
			$access_id    = $input['access_id']    ?? null;
			$access_token = $input['access_token'] ?? null;
			$user_id      = null;

			if (!$input) {
				return $this->response([
					'success' => false,
					'message' => 'No se recibieron preferencias de cookies.'
				], 400);
			}

			// Validación dual
			if ($access_id && $access_token) {
				$user = Model_User::find($access_id);
				if ($user && md5($user->login_hash) == $access_token) {
					$user_id = $user->id;
				} else {
					return $this->response([
						'success' => false,
						'message' => 'Acceso no autorizado.'
					], 403);
				}
			}

			$prefs = [
				'analytics'       => isset($input['analytics']) ? (int)$input['analytics'] : 1,
				'marketing'       => isset($input['marketing']) ? (int)$input['marketing'] : 1,
				'personalization' => isset($input['personalization']) ? (int)$input['personalization'] : 1,
				'necessary'       => isset($input['necessary']) ? (int)$input['necessary'] : 1,
			];

			$model = Helper_Legal::update_cookies_preferences($prefs, $user_id);

			// Si rechazó las necesarias → forzar logout
			if ($prefs['necessary'] != 0) {
				return $this->response([
					'success'      => true,
					'force_logout' => true,
					'message'      => 'No aceptaste cookies necesarias, se cerrará la sesión.'
				]);
			}

			return $this->response([
				'success' => true,
				'message' => 'Preferencias de cookies actualizadas.',
				'prefs'   => [
					'necessary'       => (int)$model->necessary,
					'analytics'       => (int)$model->analytics,
					'marketing'       => (int)$model->marketing,
					'personalization' => (int)$model->personalization,
				]
			]);
		} catch (\Exception $e) {
			\Log::error("[LEGAL][COOKIES] Error en post_update_cookies_prefs: " . $e->getMessage());
			return $this->response([
				'success' => false,
				'message' => 'Error al actualizar preferencias de cookies'
			], 500);
		}
	}




//////////////////////////////////////////////////////////////////////////////
////PARA REPORTES GENERALES
//////////////////////////////////////////////////////////////////////////////
/* ============================================================
 * MÓDULO REPORTES - ENDPOINTS AJAX
 * ============================================================ */

/**
 * Retorna todas las tablas disponibles en la base de datos.
 * Salida: [{"TABLE_NAME": "partners"}, {"TABLE_NAME": "employees"}, ...]
 */
public function post_get_tables()
{
    \Log::info('[REPORTES][AJAX] Listando tablas disponibles (SHOW TABLES)...');

    try {
        $tables = \DB::query("SHOW TABLES")->execute()->as_array();
        $out = [];

        foreach ($tables as $t) {
            $out[] = ['TABLE_NAME' => reset($t)];
        }

        return \Response::forge(json_encode($out), 200, ['Content-Type' => 'application/json']);
    } catch (\Database_Exception $e) {
        \Log::error('[REPORTES][AJAX][ERROR] ' . $e->getMessage());
        return \Response::forge(json_encode(['error' => $e->getMessage()]), 200, ['Content-Type' => 'application/json']);
    }
}

/**
 * Retorna los campos de una tabla específica.
 * Entrada: {table: "partners"}
 * Salida: [{"COLUMN_NAME": "id", "DATA_TYPE": "int(11)"}, ...]
 */
public function post_get_fields()
{
    // Leer cuerpo JSON manualmente
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    $table = isset($data['table']) ? trim($data['table']) : null;

    \Log::info('[REPORTES][AJAX] Cargando campos de la tabla: ' . $table);

    if (!$table) {
        return \Response::forge(json_encode(['error' => 'No se recibió el nombre de la tabla.']),
            400, ['Content-Type' => 'application/json']);
    }

    try {
        $fields = \DB::query("DESCRIBE `{$table}`")->execute()->as_array();
        $out = array_map(function($f) {
            return [
                'COLUMN_NAME' => $f['Field'],
                'DATA_TYPE'   => $f['Type'],
            ];
        }, $fields);

        return \Response::forge(json_encode($out), 200, ['Content-Type' => 'application/json']);
    } catch (\Database_Exception $e) {
        \Log::error('[REPORTES][AJAX][ERROR] ' . $e->getMessage());
        return \Response::forge(json_encode(['error' => $e->getMessage()]), 200,
            ['Content-Type' => 'application/json']);
    }
}


/**
 * Retorna todas las tablas junto con sus campos.
 * Solo para pruebas o vistas completas.
 */
public function post_get_tables_with_fields()
{
    \Log::info('[REPORTES][AJAX] Cargando tablas con sus campos (SHOW TABLES + DESCRIBE)...');

    try {
        $tables = \DB::query("SHOW TABLES")->execute()->as_array();
        $out = [];

        foreach ($tables as $t) {
            $table_name = reset($t);

            try {
                $fields = \DB::query("DESCRIBE `{$table_name}`")->execute()->as_array();

                $out[$table_name] = array_map(function($f) {
                    return [
                        'COLUMN_NAME' => $f['Field'],
                        'DATA_TYPE'   => $f['Type'],
                    ];
                }, $fields);
            } catch (\Database_Exception $e2) {
                \Log::error("[REPORTES][AJAX][ERROR] No se pudieron leer los campos de {$table_name}: " . $e2->getMessage());
            }
        }

        return \Response::forge(json_encode($out), 200, ['Content-Type' => 'application/json']);

    } catch (\Database_Exception $e) {
        \Log::error('[REPORTES][AJAX][ERROR] ' . $e->getMessage());
        return \Response::forge(json_encode(['error' => $e->getMessage()]), 200, ['Content-Type' => 'application/json']);
    }
}

//////////////////////////////////////////////////////////////////////////////
//// PARA REPORTES GENERALES - COMPLETOS
//////////////////////////////////////////////////////////////////////////////

/**
 * Retorna todos los departamentos disponibles.
 * Fuente: tabla employees_departments
 * Salida: [{"id":1,"name":"Sistemas"}, {"id":2,"name":"Contabilidad"}]
 */
public function post_get_departments()
{
    \Log::info('[REPORTES][AJAX] Cargando departamentos...');

    try {
        $rows = \DB::select('id', 'name')
            ->from('employees_departments')
            ->where('deleted', '=', 0)
            ->order_by('name', 'asc')
            ->execute()
            ->as_array();

        return \Response::forge(json_encode($rows), 200, ['Content-Type' => 'application/json']);
    } catch (\Database_Exception $e) {
        \Log::error('[REPORTES][AJAX][ERROR] get_departments: ' . $e->getMessage());
        return \Response::forge(json_encode(['error' => $e->getMessage()]), 200, ['Content-Type' => 'application/json']);
    }
}

/**
 * Ejecuta una consulta SELECT para probarla antes de guardar.
 * Entrada: {sql: "SELECT ..."}
 * Salida: {"rows":[{"campo":"valor",...}]}
 */
public function post_test_query()
{
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    $sql  = isset($data['sql']) ? trim($data['sql']) : '';

    \Log::info('[REPORTES][AJAX] Probar consulta: ' . $sql);

    if (!$sql) {
        return \Response::forge(json_encode(['error' => 'Consulta vacía.']), 400, ['Content-Type' => 'application/json']);
    }

    if (stripos($sql, 'SELECT') !== 0) {
        return \Response::forge(json_encode(['error' => 'Solo se permiten consultas SELECT.']), 400, ['Content-Type' => 'application/json']);
    }

    try {
        $rows = \DB::query($sql)->execute()->as_array();
        return \Response::forge(json_encode(['rows' => $rows]), 200, ['Content-Type' => 'application/json']);
    } catch (\Database_Exception $e) {
        \Log::error('[REPORTES][AJAX][ERROR] test_query: ' . $e->getMessage());
        return \Response::forge(json_encode(['error' => $e->getMessage()]), 200, ['Content-Type' => 'application/json']);
    }
}

/**
 * Guarda un reporte en la tabla reports_queries.
 * Entrada:
 * {
 *   query_name: "Ventas por mes",
 *   description: "Reporte de ventas por mes",
 *   department_id: 1,
 *   query_sql: "SELECT ..."
 * }
 */
public function post_save_query()
{
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);

    $name        = trim($data['query_name'] ?? '');
    $description = trim($data['description'] ?? '');
    $dept_id     = (int)($data['department_id'] ?? 0);
    $query_sql   = trim($data['query_sql'] ?? '');

    \Log::info("[REPORTES][AJAX] Guardando reporte: {$name}");

    if (!$name || !$query_sql) {
        return \Response::forge(json_encode(['msg' => 'Nombre y consulta son obligatorios.']), 200, ['Content-Type' => 'application/json']);
    }

    try {
        $insert = \DB::insert('reports_queries')
            ->set([
                'query_name'        => $name,
                'description' => $description,
                'department_id' => $dept_id,
                'query_sql'   => $query_sql,
                'created_at'  => time(),
                'updated_at'  => time(),
            ])->execute();

        return \Response::forge(json_encode(['msg' => 'ok', 'id' => $insert[0]]), 200, ['Content-Type' => 'application/json']);
    } catch (\Database_Exception $e) {
        \Log::error('[REPORTES][AJAX][ERROR] save_query: ' . $e->getMessage());
        return \Response::forge(json_encode(['msg' => $e->getMessage()]), 200, ['Content-Type' => 'application/json']);
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////AQUI EMPIEZAN LOS ENDPOINTS PARA EL PLAN DE CUENTAS///////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 /**
     * POST GET TREE
     * Devuelve el árbol jerárquico de cuentas contables
     */
    public function post_get_tree()
{
    \Log::info('[PLAN_CUENTAS][TREE] Iniciando generación del árbol...');

    try {
        $accounts = Model_Accounts_Chart::query()
            ->where('deleted', 0)
            ->order_by('code', 'asc')
            ->get();

        \Log::info('[PLAN_CUENTAS][TREE] Registros obtenidos: ' . count($accounts));

        $arr = [];
        foreach ($accounts as $a) {
            $pid = $a->parent_id !== null && $a->parent_id !== '' ? (int)$a->parent_id : null;
            $arr[] = [
                'id'              => (int)$a->id,
                'code'            => $a->code,
                'name'            => $a->name,
                'type'            => $a->type,
                'parent_id'       => $pid,
                'level'           => (int)$a->level,
                'currency_id'     => $a->currency_id ? (int)$a->currency_id : null,
                'is_confidential' => (int)$a->is_confidential,
                'is_cash_account' => (int)$a->is_cash_account,
                'is_active'       => (int)$a->is_active,
                'annex24_code'    => $a->annex24_code,
                'account_class'   => $a->account_class,
                'children'        => [],
            ];
        }

        $refs = [];
        foreach ($arr as &$node) {
            $refs[$node['id']] = &$node;
        }

        $tree = [];
        foreach ($arr as &$node) {
            $pid = $node['parent_id'];
            if ($pid !== null && isset($refs[$pid])) {
                $refs[$pid]['children'][] = &$node;
                \Log::debug("[PLAN_CUENTAS][TREE] Asignando hijo {$node['id']} → padre {$pid}");
            } else {
                $tree[] = &$node;
                \Log::debug("[PLAN_CUENTAS][TREE] Nodo raíz {$node['id']}");
            }
        }

        // 🔧 Clonar para eliminar referencias antes de enviar
        $tree = json_decode(json_encode($tree), true);

        \Log::info('[PLAN_CUENTAS][TREE] Árbol generado con ' . count($tree) . ' nodos raíz.');

        return \Response::forge(json_encode(['rows' => $tree]))
            ->set_header('Content-Type', 'application/json');

    } catch (\Exception $e) {
        \Log::error('[PLAN_CUENTAS][TREE][ERROR] ' . $e->getMessage());
        return \Response::forge(json_encode(['error' => 'No fue posible obtener el árbol de cuentas']))
            ->set_header('Content-Type', 'application/json');
    }
}




    public function post_get_account()
{
	\Log::debug('[PLAN_CUENTAS][GET_ACCOUNT][POST_RAW] ' . json_encode($_POST));
    \Log::info('[PLAN_CUENTAS][GET_ACCOUNT] Iniciando petición...');
    $id = (int) Input::post('id');

    \Log::info('[PLAN_CUENTAS][GET_ACCOUNT] ID recibido: ' . $id);

    $data = [];

    try {
        $account = Model_Accounts_Chart::find($id);

        if (!$account) {
            \Log::warning("[PLAN_CUENTAS][GET_ACCOUNT] No se encontró cuenta con id={$id}");
            return \Response::forge(json_encode([
                'msg' => 'no_found',
                'row' => null
            ]))->set_header('Content-Type', 'application/json');
        }

        // Log general de cuenta encontrada
        \Log::info('[PLAN_CUENTAS][GET_ACCOUNT] Cuenta encontrada: ' . $account->code . ' - ' . $account->name);

        $data = [
            'row' => [
                'id'              => (int) $account->id,
                'code'            => $account->code,
                'name'            => $account->name,
                'type'            => $account->type,
                'parent_id'       => $account->parent_id ? (int) $account->parent_id : null,
                'level'           => (int) $account->level,
                'currency_id'     => $account->currency_id ? (int) $account->currency_id : null,
                'is_confidential' => (int) $account->is_confidential,
                'is_cash_account' => (int) $account->is_cash_account,
                'is_active'       => (int) $account->is_active,
                'annex24_code'    => $account->annex24_code,
                'account_class'   => $account->account_class,
                'created_at'      => $account->created_at,
                'updated_at'      => $account->updated_at
            ],
            'success' => true
        ];

        \Log::debug('[PLAN_CUENTAS][GET_ACCOUNT] Datos enviados: ' . json_encode($data['row']));

    } catch (\Exception $e) {
        \Log::error('[PLAN_CUENTAS][GET_ACCOUNT][ERROR] ' . $e->getMessage());
        $data = ['error' => 'Error interno al obtener cuenta'];
    }

    return \Response::forge(json_encode($data))
        ->set_header('Content-Type', 'application/json');
}

    /**
 * POST SAVE ACCOUNT
 * Guarda o actualiza una cuenta contable.
 * - Si existe una cuenta eliminada (deleted = 1) con el mismo código → la reactiva.
 * - Si existe una cuenta activa con el mismo código → impide duplicado.
 * - Si es nueva → crea.
 * - Si es edición → actualiza.
 */
public function post_save_account()
{
    $data = [];

    try {
        \Log::info('[PLAN_CUENTAS][SAVE] Iniciando guardado de cuenta...');

        // ==========================
        // Normalizar ID y datos base
        // ==========================
        $id = Input::post('id');
        if ($id === 'null' || $id === '' || $id === 0) {
            $id = null;
        }

        $is_new = empty($id);
        $code   = trim(Input::post('code'));
        $name   = trim(Input::post('name'));
        $type   = trim(Input::post('type'));

        \Log::info('[PLAN_CUENTAS][SAVE] ID normalizado: ' . ($id ?: 'NUEVO'));
        \Log::info('[PLAN_CUENTAS][SAVE] Datos recibidos: ' . json_encode(Input::post()));

        // ==========================
        // VALIDAR DUPLICADOS POR CÓDIGO
        // ==========================
        $existing = Model_Accounts_Chart::query()
            ->where('code', $code)
            ->get_one();

        if ($existing) {
            if ($existing->deleted == 0 && ($is_new || $existing->id != $id)) {
                // Ya existe una cuenta activa con el mismo código
                \Log::warning('[PLAN_CUENTAS][SAVE] Código duplicado activo: ' . $code);
                return Response::forge(json_encode([
                    'success' => false,
                    'msg'     => 'El código "' . $code . '" ya está en uso por otra cuenta activa.'
                ]))->set_header('Content-Type', 'application/json');
            }

            if ($existing->deleted == 1 && $is_new) {
                // Reactivar cuenta eliminada con mismo código
                $account = $existing;
                $is_new  = false;
                $id      = $existing->id;
                \Log::info('[PLAN_CUENTAS][SAVE] Reactivando cuenta eliminada ID: ' . $id);
            }
        }

        // ==========================
        // CREAR O CARGAR REGISTRO
        // ==========================
        if ($is_new) {
            $account = Model_Accounts_Chart::forge();
            $account->created_at = time();
            \Log::info('[PLAN_CUENTAS][SAVE] Creando nueva cuenta...');
        } else {
            if (!isset($account)) {
                $account = Model_Accounts_Chart::find($id);
            }
            if (!$account) {
                \Log::error('[PLAN_CUENTAS][SAVE] Cuenta no encontrada con ID: ' . $id);
                return Response::forge(json_encode([
                    'success' => false,
                    'msg' => 'Cuenta no encontrada'
                ]))->set_header('Content-Type', 'application/json');
            }
            \Log::info('[PLAN_CUENTAS][SAVE] Editando cuenta existente ID: ' . $account->id);
        }

        // ==========================
        // ASIGNAR CAMPOS
        // ==========================
        $account->code            = $code;
        $account->name            = $name;
        $account->type            = $type;
        $account->parent_id       = Input::post('parent_id') && Input::post('parent_id') !== 'null'
                                    ? (int) Input::post('parent_id') : null;
        $account->level           = (int) Input::post('level', 1);
        $account->currency_id     = Input::post('currency_id') ?: null;
        $account->is_confidential = (int) Input::post('is_confidential', 0);
        $account->is_cash_account = (int) Input::post('is_cash_account', 0);
        $account->is_active       = (int) Input::post('is_active', 1);
        $account->annex24_code    = trim(Input::post('annex24_code'));
        $account->account_class   = trim(Input::post('account_class'));
        $account->deleted         = 0;
        $account->updated_at      = time();

        // ==========================
        // GUARDAR
        // ==========================
        if ($account->save()) {
            $msg = $existing && $existing->deleted == 1
                ? 'Cuenta reactivada correctamente'
                : ($is_new ? 'Cuenta creada correctamente' : 'Cuenta actualizada correctamente');

            \Log::info('[PLAN_CUENTAS][SAVE] ' . $msg . ' (' . $account->code . ')');

            $data = [
                'success' => true,
                'id'      => $account->id,
                'msg'     => $msg
            ];
        } else {
            \Log::error('[PLAN_CUENTAS][SAVE] Falló save() sin excepción.');
            $data = ['success' => false, 'msg' => 'No se pudo guardar la cuenta.'];
        }

    } catch (Exception $e) {
        \Log::error('[PLAN_CUENTAS][SAVE][EXCEPTION] ' . $e->getMessage() . ' (línea ' . $e->getLine() . ')');
        $data = ['success' => false, 'msg' => 'Error al guardar la cuenta: ' . $e->getMessage()];
    }

    \Log::info('[PLAN_CUENTAS][SAVE] Respuesta final: ' . json_encode($data));
    return Response::forge(json_encode($data))->set_header('Content-Type', 'application/json');
}


public function post_get_all_accounts()
{
    try {
        $rows = Model_Accounts_Chart::query()
            ->where('deleted', 0)
            ->order_by('code', 'asc')
            ->get();

        $result = [];
        foreach ($rows as $r) {
            $result[] = [
                'id' => $r->id,
                'code' => $r->code,
                'name' => $r->name
            ];
        }

        return Response::forge(json_encode(['rows' => $result]))
            ->set_header('Content-Type', 'application/json');
    } catch (Exception $e) {
        \Log::error('[PLAN_CUENTAS][PADRES][ERROR] ' . $e->getMessage());
        return Response::forge(json_encode(['rows' => []]))
            ->set_header('Content-Type', 'application/json');
    }
}

/**
 * Obtener clases únicas de cuenta desde la tabla accounts_chart
 */
public function post_get_account_classes()
{
    try {
        $rows = DB::select('account_class')
            ->from('accounts_chart')
            ->where('deleted', 0)
            ->execute()
            ->as_array();

        $classes = [];
        $hasUnclassified = false;

        foreach ($rows as $r) {
            $class = trim($r['account_class']);
            if ($class === '' || $class === null) {
                $hasUnclassified = true;
            } else {
                $classes[] = $class;
            }
        }

        $classes = array_values(array_unique($classes));
        sort($classes);

        return Response::forge(json_encode([
            'msg' => 'ok',
            'classes' => $classes,
            'has_unclassified' => $hasUnclassified
        ]))->set_header('Content-Type', 'application/json');

    } catch (Exception $e) {
        \Log::error('[PLAN_CUENTAS][CLASES][ERROR] ' . $e->getMessage());
        return Response::forge(json_encode([
            'msg' => 'error',
            'error' => $e->getMessage()
        ]))->set_header('Content-Type', 'application/json');
    }
}

public function post_get_account_types()
{
    try {
        $rows = DB::select('type')
            ->from('accounts_chart')
            ->where('deleted', 0)
            ->execute()
            ->as_array();

        $types = [];
        foreach ($rows as $r) {
            $type = trim($r['type']);
            if ($type !== '' && $type !== null) {
                $types[] = ucfirst(strtolower($type));
            }
        }

        $types = array_values(array_unique($types));
        sort($types);

        return Response::forge(json_encode([
            'msg' => 'ok',
            'types' => $types
        ]))->set_header('Content-Type', 'application/json');

    } catch (Exception $e) {
        \Log::error('[PLAN_CUENTAS][TIPOS][ERROR] ' . $e->getMessage());
        return Response::forge(json_encode([
            'msg' => 'error',
            'error' => $e->getMessage()
        ]))->set_header('Content-Type', 'application/json');
    }
}




    /**
     * POST DELETE ACCOUNT
     * Elimina (borrado lógico) una cuenta
     */
    public function post_delete_account()
    {
        $id = (int) Input::post('id');
        $data = [];

        try {
            $account = Model_Accounts_Chart::find($id);
            if (!$account) {
                return Response::forge(json_encode(['success' => false, 'msg' => 'Cuenta no encontrada']));
            }

            $account->deleted = 1;
            $account->updated_at = time();

            if ($account->save()) {
                \Log::info('[PLAN_CUENTAS][DELETE] Cuenta marcada como eliminada: ' . $id);
                $data = ['success' => true];
            } else {
                $data = ['success' => false, 'msg' => 'No fue posible eliminar la cuenta'];
            }
        } catch (Exception $e) {
            \Log::error('[PLAN_CUENTAS][DELETE] ' . $e->getMessage());
            $data = ['success' => false, 'msg' => 'Error al eliminar la cuenta'];
        }

        return Response::forge(json_encode($data))->set_header('Content-Type', 'application/json');
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////AQUI TERMINAN LOS ENDPOINTS PARA EL PLAN DE CUENTAS////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * POST GET CURRENCIES
     * Devuelve el catálogo de monedas activas
     */
    public function post_get_currencies()
    {
        $data = [];
        try {
            $currencies = Model_Currency::query()
                ->where('deleted', 0)
                ->order_by('name', 'asc')
                ->get();

            $rows = [];
            foreach ($currencies as $c) {
                $rows[] = [
                    'id'   => (int) $c->id,
                    'code' => $c->code,
                    'name' => $c->name,
                ];
            }
            $data = ['rows' => $rows];
        } catch (Exception $e) {
            \Log::error('[PLAN_CUENTAS][CURRENCIES] ' . $e->getMessage());
            $data = ['rows' => []];
        }

        return Response::forge(json_encode($data))->set_header('Content-Type', 'application/json');
    }



	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// PROCESO COMPRAS ORDENES DE COMPRA, FACTURAS, REP, NOTAS DE CREDITO, PAGOS.
	////////////////////////////////////////////////////////
	// ================================================
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// CATALOGO COMPRAS (productos, impuestos, monedas, tipos)
// Incluye soporte para tipo de documento (document_types)
// ================================================
public function action_catalogos_compras()
{
    \Log::debug('[AJAX] Entrando a catalogos_compras');

    if (!\Input::is_ajax()) {
        \Log::error('[AJAX] Llamada no AJAX');
        return $this->response(['error' => 'Método no permitido'], 405);
    }

    try {

		// =========================================
		// PROVEEDORES
		// =========================================
		$providers_opts = [];
		$providers = Model_Provider::query()
			->order_by('name', 'asc')
			->get();

		foreach ($providers as $prov) {
			$providers_opts[] = [
				'id'   => $prov->id,
				'code' => $prov->code_sap,
				'name' => $prov->name,
			];
		}

		// ===========================
		// PRODUCTOS INTERNOS
		// ===========================
		$products_internal = [];
		$productos = Model_Product::query()->order_by('name', 'asc')->get();

		foreach ($productos as $p) {
			$products_internal[] = [
				'id'   => $p->id,
				'code' => $p->code,
				'name' => $p->name,
				'type' => 'interno'
			];
		}

		// ===========================
		// PRODUCTOS PARA ORDEN / PROVEEDOR
		// ===========================
		$products_order = [];
		foreach ($productos as $p) {
			if (!empty($p->code_order) || !empty($p->name_order)) {
				$products_order[] = [
					'id'   => $p->id,
					'code' => $p->code_order ?: $p->code,
					'name' => $p->name_order ?: $p->name,
					'type' => 'proveedor'
				];
			}
		}


        // ===========================
        // PRODUCTOS
        // ===========================
        $productos = Model_Product::query()->get();
        $all_products = [];
        foreach ($productos as $p) {
            $all_products[] = [
                'id'   => $p->id,
                'code' => $p->code,
                'name' => $p->name
            ];
        }

        // ===========================
        // MONEDAS
        // ===========================
        $currency_opts = [];
        $currencies = Model_Currency::query()
            ->where('deleted', 0)
            ->order_by('name', 'asc')
            ->get();

        $default_currency_id = '';
        foreach ($currencies as $currency) {
            if ($currency->id == 1) $default_currency_id = $currency->id; // ID 1 como default
            $currency_opts[] = [
                'id'    => $currency->id,
                'label' => $currency->code . ' - ' . $currency->name . (!empty($currency->symbol) ? " ({$currency->symbol})" : '')
            ];
        }

        // ===========================
        // IMPUESTOS
        // ===========================
        $tax_opts = [];
        $taxes = Model_Tax::query()
            ->order_by('name', 'asc')
            ->get();

        $default_tax_id = '';
        foreach ($taxes as $tax) {
            if ($tax->id == 1) $default_tax_id = $tax->id; // ID 1 como default
            $tax_opts[] = [
                'id'    => $tax->id,
                'rate'  => floatval($tax->rate),
                'label' => $tax->code . ' (' . ($tax->rate * 100) . '%)'
            ];
        }

        // ===========================
        // RETENCIONES
        // ===========================
        $retention_opts = [];
        $retentions = Model_Retention::query()
            ->order_by('code', 'asc')
            ->get();

        foreach ($retentions as $ret) {
            $retention_opts[] = [
                'id'    => $ret->id,
                'rate'  => floatval($ret->rate),
                'label' => $ret->code . ' (' . ($ret->rate * 100) . '%)'
            ];
        }

        // ===========================
        // TIPOS GENERALES (artículo / servicio)
        // ===========================
        $type_opts = [
            ['id' => 'articulo', 'label' => 'Artículo'],
            ['id' => 'servicio', 'label' => 'Servicio']
        ];
        $default_type_id = 'articulo';

        // ===========================
        // TIPOS DE DOCUMENTO (nuevo)
        // ===========================
        $document_type_opts = [];
        $document_types = Model_Document_Type::query()
            ->where('deleted', 0)
            ->where('active', 1)
            ->order_by('name', 'asc')
            ->get();

        foreach ($document_types as $doc) {
            $document_type_opts[] = [
                'id'   => $doc->id,
                'name' => $doc->name,
                'scope'=> $doc->scope
            ];
        }

        // ===========================
        // RESPUESTA FINAL
        // ===========================
        return $this->response([
            'all_products'          => $all_products,
            'currency_opts'         => $currency_opts,
            'default_currency_id'   => $default_currency_id,
            'tax_opts'              => $tax_opts,
            'default_tax_id'        => $default_tax_id,
            'type_opts'             => $type_opts,
            'default_type_id'       => $default_type_id,
            'retention_opts'        => $retention_opts,
            'document_type_opts'    => $document_type_opts,
			'providers_opts' 		=> $providers_opts,
			'products_internal' 	=> $products_internal,
			'products_order'    	=> $products_order,
        ]);

    } catch (\Exception $e) {
        \Log::error('[AJAX][catalogos_compras][ERROR] ' . $e->getMessage());
        return $this->response(['error' => 'Error al obtener catálogos'], 500);
    }
}


/**
 * GUARDAR ORDEN DE COMPRA DESDE AJAX (VUE)
 * Recibe JSON, valida, calcula y guarda TODO en el backend.
 * RESPONDE SIEMPRE EN JSON.
 */
public function action_guardar_ajax()
{
    \Log::debug('[AJAX][ORDEN] Entrando a action_guardar_ajax - Método: ' . \Input::method());

    // Solo aceptar AJAX y POST
    if (!\Input::is_ajax() || \Input::method() !== 'POST') {
        return $this->response(['error' => 'Método no permitido'], 405);
    }

    // Verificar que el usuario esté autenticado
    if (!\Auth::check()) {
        return $this->response(['error' => 'No autenticado'], 403);
    }

    try {
        // 1. RECIBIR Y DECODIFICAR DATOS JSON
        $input = json_decode(file_get_contents('php://input'), true);
        \Log::debug('[AJAX][ORDEN] Payload recibido: ' . json_encode($input));

        // 2. VALIDACIÓN FuelPHP
        $val = \Validation::forge('orden_ajax');
        $val->add_field('proveedor_id', 'Proveedor', 'required|valid_string[numeric]');
        $val->add_field('codigo_oc', 'Código OC', 'required|min_length[1]|max_length[100]');
        $val->add_field('fecha', 'Fecha', 'required');
        $val->add_field('moneda', 'Moneda', 'required');
        $val->add_field('notas', 'Notas', 'max_length[255]');

        if (!$val->run($input)) {
            $errores = [];
            foreach ($val->error() as $field => $e) {
                $errores[$field] = $e->get_message();
            }
            return $this->response(['error' => 'Validación fallida', 'detalles' => $errores], 400);
        }

        // 3. VALIDAR QUE EL PROVEEDOR EXISTA
        if (!Model_Provider::find($input['proveedor_id'])) {
            return $this->response(['error' => 'Proveedor inválido'], 400);
        }

        // 4. CREAR ORDEN DE COMPRA (cabecera sin total aún)
        $order = Model_Providers_Order::forge([
            'provider_id' => $input['proveedor_id'],
            'code_order'  => $input['codigo_oc'],
            'date_order'  => strtotime($input['fecha']),
            'currency_id' => $input['moneda'],
            'tax_id'      => $input['tax_id'] ?? null,
            'retention_id'=> $input['retention_id'] ?? null,
            'status'      => 0,  // Abierta
            'has_invoice' => 0,  // aún no tiene factura
            'subtotal'    => 0,  // se calcula al final
            'iva'         => 0,  // se calcula al final
            'retencion'   => 0,  // se calcula al final
            'total'       => 0,  // se calcula al final
            'notes'       => $input['notas'] ?? '',
            'deleted'     => 0,
            'origin'      => 0,  // Web
            'created_at'  => time(),
        ]);
        $order->save();
        \Log::debug('[AJAX][ORDEN] Orden creada ID: ' . $order->id);

        // 5. PROCESAR Y GUARDAR PARTIDAS
		$subtotal_general   = 0;
		$iva_general        = 0;
		$retencion_general  = 0;
        $total_orden = 0;
        foreach ($input['partidas'] as $prod) {
            if (empty($prod['description']) || empty($prod['quantity']) || empty($prod['unit_price'])) {
                \Log::warning('[AJAX][ORDEN] Partida inválida omitida: ' . json_encode($prod));
                continue;
            }

            // Tasas
            $tax_rate = $this->_get_rate($prod['tax_id'] ?? '', 'tax');
            $ret_rate = $this->_get_rate($prod['retention_id'] ?? '', 'retencion');

            // Cálculos
            $cantidad = floatval($prod['quantity']);
            $precio   = floatval($prod['unit_price']);
            $subtotal = $cantidad * $precio;
            $ivaMonto = $subtotal * $tax_rate;
            $retMonto = $subtotal * $ret_rate;
            $total    = $subtotal + $ivaMonto - $retMonto;

            $subtotal_general  += $subtotal;
			$iva_general       += $ivaMonto;
			$retencion_general += $retMonto;
			$total_orden += $total;

            Model_Providers_Order_Detail::forge([
                'order_id'     => $order->id,
                'product_id'   => $prod['product_id'] ?? null,
                'code_product' => $prod['code_product'] ?? '',
                'description'  => $prod['description'],
                'quantity'     => $cantidad,
                'unit_price'   => $precio,
                'subtotal'     => $subtotal,
                'iva'          => $ivaMonto,
                'retencion'    => $retMonto,
                'total'        => $total,
                'tax_id'       => $prod['tax_id'] ?? $input['tax_id'] ?? null,
                'retention_id' => $prod['retention_id'] ?? $input['retention_id'] ?? null,
                'currency_id'  => $input['moneda'],
                'delivered'    => 0,
                'invoiced'     => 0,
                'deleted'     => 0,
                'received_at'  => time(),
                'created_at'   => time(),
            ])->save();
        }

        // 6. ACTUALIZAR TOTAL
		$order->subtotal  = $subtotal_general;
		$order->iva       = $iva_general;
		$order->retencion = $retencion_general;
		$order->total = $total_orden;
		$order->save();
		\Log::debug('[AJAX][ORDEN] Totales actualizados para orden ID ' . $order->id);

// 7. Preparar respuesta
return $this->response([
    'success' => true,
    'id'      => $order->id,
    'status'  => [
        'code'  => $order->status,
        'label' => Helper_Purchases::label('order', $order->status),
        'badge' => Helper_Purchases::badge_class('order', $order->status),
    ]
], 200);



    } catch (\Exception $e) {
        \Log::error('[AJAX][ORDEN] ERROR: ' . $e->getMessage());
        return $this->response(['error' => 'Error al guardar: ' . $e->getMessage()], 500);
    }
}



/**
 * OBTENER LOS DATOS DE UNA ORDEN DE COMPRA PARA EDITAR (AJAX)
 */
public function action_obtener_oc()
{
    // SOLO AJAX Y GET
    if (!\Input::is_ajax()) {
        return $this->response(['error' => 'Método no permitido'], 405);
    }

    $id = Input::get('id');
    if (!$id || !is_numeric($id)) {
        return $this->response(['error' => 'ID inválido'], 400);
    }

    $oc = Model_Providers_Order::find($id);
    if (!$oc) {
        return $this->response(['error' => 'Orden no encontrada'], 404);
    }

    // Trae partidas
    $partidas = [];
    foreach ($oc->details as $detalle) {
        $partidas[] = [
            'tipo'         => $detalle->product_id ? 'articulo' : 'servicio',
            'product_id'   => $detalle->product_id ?? '',
            'code_product' => $detalle->code_product ?? '',
            'description'  => $detalle->description,
            'quantity'     => $detalle->quantity,
            'unit_price'   => $detalle->unit_price,
            'tax_id'       => $detalle->tax_id ?? '',
            'retention_id' => $detalle->retention_id ?? '',
        ];
    }

    // Datos generales
    $data = [
        'id'               => $oc->id,
        'proveedor_id'     => $oc->provider_id,
        'codigo_oc'        => $oc->code_order,
        'fecha'            => date('Y-m-d', $oc->date_order),
        'moneda'           => $oc->currency_id,
        'notas'            => $oc->notes,
        'tax_general'      => $oc->tax_id ?? '',
        'retention_general'=> $oc->retention_id ?? '',
        'tipo_general'     => count($partidas) ? $partidas[0]['tipo'] : 'articulo',
        'partidas'         => $partidas,
    ];

    return $this->response($data, 200);
}


/**
 * ACTUALIZAR ORDEN DE COMPRA (AJAX)
 * Requiere: JSON con { id, proveedor_id, codigo_oc, fecha(YYYY-MM-DD), moneda, notas?, tax_id?, retention_id?, partidas[] }
 * Seguridad: access_id, access_token (opcional si no usas tokens en backend)
 */
/**
 * ACTUALIZAR ORDEN DE COMPRA (AJAX)
 */
public function action_editar_ajax()
{
    if ( ! \Input::is_ajax() || \Input::method() !== 'POST') {
        return $this->response(['error' => 'Método no permitido'], 405);
    }

    try {
        // Payload (Axios / fetch)
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = \Input::post();
        }

        if (empty($input['id']) || !is_numeric($input['id'])) {
            return $this->response(['error' => 'ID inválido'], 400);
        }

        /** @var Model_Providers_Order $oc */
        $oc = Model_Providers_Order::find($input['id']);
        if (!$oc) {
            return $this->response(['error' => 'Orden no encontrada'], 404);
        }

        // No permitir editar cerrada/cancelada
        if ((int)$oc->status === 2 || (int)$oc->status === 3) {
            return $this->response(['error' => 'La orden no se puede editar (cerrada/cancelada).'], 409);
        }

        // Validaciones básicas
        $requeridos = ['proveedor_id', 'codigo_oc', 'fecha', 'moneda', 'partidas'];
        foreach ($requeridos as $campo) {
            if (!isset($input[$campo]) || $input[$campo] === '' || (is_array($input[$campo]) && !count($input[$campo]))) {
                return $this->response(['error' => "Falta campo obligatorio: {$campo}"], 400);
            }
        }
        if (!is_array($input['partidas']) || count($input['partidas']) === 0) {
            return $this->response(['error' => 'Debes capturar al menos una partida.'], 400);
        }

        if (!Model_Provider::find($input['proveedor_id'])) {
            return $this->response(['error' => 'Proveedor inválido'], 400);
        }
        if (!Model_Currency::find($input['moneda'])) {
            return $this->response(['error' => 'Moneda inválida'], 400);
        }

        $timestamp = strtotime($input['fecha']);
        if ($timestamp === false) {
            return $this->response(['error' => 'Fecha inválida'], 400);
        }

        \DB::start_transaction();

        // ==========================
        // CABECERA
        // ==========================
        $oc->provider_id  = (int)$input['proveedor_id'];
        $oc->code_order   = trim($input['codigo_oc']);
        $oc->date_order   = $timestamp;
        $oc->currency_id  = (int)$input['moneda'];
        $oc->notes        = isset($input['notas']) ? trim($input['notas']) : '';
        $oc->tax_id       = isset($input['tax_id']) ? (int)$input['tax_id'] : null;
        $oc->retention_id = isset($input['retention_id']) ? (int)$input['retention_id'] : null;
        $oc->updated_at   = time();

        // tipo_general
        $tipo_general = $input['tipo_general']
            ?? ($input['partidas'][0]['tipo'] ?? $oc->type_general ?? 'articulo');
        $oc->type_general = $tipo_general;

        $oc->save();

        // ==========================
        // DETALLES EXISTENTES
        // ==========================
        $detalles_previos = Model_Providers_Order_Detail::query()
            ->where('order_id', $oc->id)
            ->where('deleted', 0)
            ->order_by('id', 'asc')
            ->get();

        // Normalizamos a array indexado (por posición)
        $detalles_previos_arr = array_values($detalles_previos);

        $subtotal_general = 0.0;
        $iva_general      = 0.0;
        $ret_general      = 0.0;
        $total_general    = 0.0;

        $moneda_id = (int)$input['moneda'];

        // ==========================
        // RECORRER PARTIDAS NUEVAS
        // ==========================
        $index = 0;
        foreach ($input['partidas'] as $p) {

            if (empty($p['description']) || empty($p['quantity']) || empty($p['unit_price'])) {
                continue;
            }

            $qty   = (float)$p['quantity'];
            $price = (float)$p['unit_price'];

            $tax_id       = $p['tax_id']       ?? $oc->tax_id;
            $retention_id = $p['retention_id'] ?? $oc->retention_id;

            $tax_rate = $this->_get_rate($tax_id, 'tax');          // 0..1
            $ret_rate = $this->_get_rate($retention_id, 'retencion');

            $sub      = round($qty * $price, 2);
            $iva      = round($sub * $tax_rate, 2);
            $ret      = round($sub * $ret_rate, 2);
            $line_tot = round($sub + $iva - $ret, 2);

            $subtotal_general += $sub;
            $iva_general      += $iva;
            $ret_general      += $ret;
            $total_general    += $line_tot;

            // Si existe un detalle previo en esta posición → actualizar
            if (isset($detalles_previos_arr[$index])) {
                /** @var Model_Providers_Order_Detail $det */
                $det = $detalles_previos_arr[$index];

                $det->type              = $p['tipo'] ?? $tipo_general;
                $det->product_id        = !empty($p['product_id']) ? (int)$p['product_id'] : null;
                $det->code_product      = $p['code_product'] ?? '';
                $det->description       = $p['description'] ?? '';
                $det->quantity          = $qty;
                $det->unit_price        = $price;
                $det->subtotal          = $sub;
                $det->iva               = $iva;
                $det->retencion         = $ret;
                $det->total             = $line_tot;
                $det->currency_id       = $moneda_id;
                $det->tax_id            = $tax_id ?: null;
                $det->retention_id      = $retention_id ?: null;
                $det->accounts_chart_id = !empty($p['accounts_chart_id']) ? (int)$p['accounts_chart_id'] : null;
                $det->updated_at        = time();
                $det->save();
            } else {
                // No hay detalle en esta posición → crear nuevo
                Model_Providers_Order_Detail::forge([
                    'order_id'          => $oc->id,
                    'type'              => $p['tipo'] ?? $tipo_general,
                    'product_id'        => !empty($p['product_id']) ? (int)$p['product_id'] : null,
                    'code_product'      => $p['code_product'] ?? '',
                    'description'       => $p['description'] ?? '',
                    'quantity'          => $qty,
                    'unit_price'        => $price,
                    'subtotal'          => $sub,
                    'iva'               => $iva,
                    'retencion'         => $ret,
                    'total'             => $line_tot,
                    'currency_id'       => $moneda_id,
                    'tax_id'            => $tax_id ?: null,
                    'retention_id'      => $retention_id ?: null,
                    'delivered'         => 0,
                    'invoiced'          => 0,
                    'deleted'           => 0,
                    'accounts_chart_id' => !empty($p['accounts_chart_id']) ? (int)$p['accounts_chart_id'] : null,
                    'created_at'        => time(),
                    'updated_at'        => time(),
                ])->save();
            }

            $index++;
        }

        // ==========================
        // MARCAR DETALLES SOBRANTES COMO ELIMINADOS
        // ==========================
        $total_previos = count($detalles_previos_arr);
        if ($index < $total_previos) {
            for ($i = $index; $i < $total_previos; $i++) {
                $det = $detalles_previos_arr[$i];
                $det->deleted    = 1;
                $det->updated_at = time();
                $det->save();
            }
        }

        // ==========================
        // ACTUALIZA CABECERA (totales)
        // ==========================
        $oc->subtotal  = $subtotal_general;
        $oc->iva       = $iva_general;
        $oc->retencion = $ret_general;
        $oc->total     = $total_general;
        $oc->updated_at = time();
        $oc->save();

        \DB::commit_transaction();

        return $this->response(['success' => true, 'id' => $oc->id], 200);

    } catch (\Throwable $e) {
        \DB::rollback_transaction();
        \Log::error('[AJAX][EDITAR OC] '.$e->getMessage());
        return $this->response(['error' => 'Error al guardar: '.$e->getMessage()], 500);
    }
}


/**
 * OBTENER ÓRDENES POR PROVEEDOR (AJAX)
 *
 * Retorna las órdenes activas asociadas a un proveedor.
 * Incluye id, código, total y fecha.
 */
public function action_get_orders_by_provider()
{
    // 1. Solo AJAX y POST
    if (!\Input::is_ajax() || \Input::method() !== 'POST') {
        return $this->response(['success' => false, 'error' => 'Método no permitido'], 405);
    }

    try {
        // 2. Tokens de seguridad
        $input = json_decode(file_get_contents('php://input'), true);

        $access_id    = $input['access_id'] ?? null;
        $access_token = $input['access_token'] ?? null;
        $provider_id  = $input['provider_id'] ?? null;

        if (!Helper_Access::validate($access_id, $access_token)) {
            return $this->response(['success' => false, 'error' => 'Acceso no autorizado'], 403);
        }

        // 3. Validación proveedor
        if (empty($provider_id) || !is_numeric($provider_id)) {
            return $this->response(['success' => false, 'error' => 'Proveedor inválido'], 400);
        }

        // 4. Traemos órdenes activas del proveedor
        $orders = \Model_Providers_Order::query()
            ->where('provider_id', $provider_id)
            ->where('deleted', 0)
            ->order_by('id', 'desc')
            ->get();

        $result = [];
        foreach ($orders as $oc) {
            $result[] = [
                'id'         => $oc->id,
                'code_order' => $oc->code_order,
                'total'      => number_format($oc->total, 2, '.', ''),
                'date'       => date('d/m/Y', $oc->date_order), // 👈 más legible para el select
            ];
        }

        // 5. Respuesta
        return $this->response([
            'success' => true,
            'orders'  => $result,
        ], 200);

    } catch (\Exception $e) {
        \Log::error('[AJAX][OC POR PROVEEDOR] ' . $e->getMessage());
        return $this->response(['success' => false, 'error' => 'Error interno'], 500);
    }
}



    /**
 * BUSCAR CUENTAS CONTABLES (AJAX)
 * Devuelve lista reducida para el datalist dinámico.
 */
public function post_search_accounts()
{
    if (!\Input::is_ajax()) {
        return \Response::forge(json_encode(['error' => 'Petición no válida.']))
            ->set_header('Content-Type', 'application/json');
    }

    // === CORRECCIÓN CLAVE: leer JSON si Input::post() está vacío ===
    $q = trim(\Input::post('q', ''));
    if ($q === '' && $raw = file_get_contents('php://input')) {
        $decoded = json_decode($raw, true);
        if (isset($decoded['q'])) {
            $q = trim($decoded['q']);
        }
    }

    \Log::debug('[SEARCH ACCOUNTS] Valor recibido (final): "' . $q . '"');

    if ($q === '') {
        \Log::debug('[SEARCH ACCOUNTS] Sin término de búsqueda');
        return \Response::forge(json_encode([]))
            ->set_header('Content-Type', 'application/json');
    }

    try {
        $results = \Model_Accounts_Chart::query()
            ->where_open()
                ->where(DB::expr('LOWER(code)'), 'like', '%' . strtolower($q) . '%')
                ->or_where(DB::expr('LOWER(name)'), 'like', '%' . strtolower($q) . '%')
            ->where_close()
            ->where('deleted', 0)
            ->order_by('code', 'asc')
            ->limit(20)
            ->get();

        \Log::debug('[SEARCH ACCOUNTS] Resultados encontrados: ' . count($results));

        $arr = [];
        foreach ($results as $r) {
            $arr[] = [
                'id'   => $r->id,
                'code' => $r->code,
                'name' => $r->name
            ];
        }

        return \Response::forge(json_encode($arr))
            ->set_header('Content-Type', 'application/json');

    } catch (\Throwable $e) {
        \Log::error('[SEARCH ACCOUNTS][ERROR] ' . $e->getMessage());
        return \Response::forge(json_encode([
            'error' => 'Error al buscar cuentas: ' . $e->getMessage()
        ]))->set_header('Content-Type', 'application/json');
    }
}


// ACTUALIZAR Y AUTORIZAR ORDEN DE COMPRA (AJAX)
// =========================================================
// GUARDAR CAMBIOS DE OC + (OPCIONAL) AUTORIZAR
// =========================================================
public function action_autorizar_ajax()
{
    if ( ! \Input::is_ajax()) {
        return \Response::forge(json_encode([
            'success' => false,
            'error'   => 'Petición inválida'
        ]), 400)->set_header('Content-Type', 'application/json');
    }

    // FuelPHP + Axios
    $data = \Input::post();
    if (empty($data)) {
        $json = json_decode(file_get_contents('php://input'), true);
        if (is_array($json)) {
            $data = $json;
        }
    }

    \Log::debug('[OC_AUTORIZAR][REQUEST] ' . json_encode($data));

    $order_id = (int) ($data['id'] ?? 0);
    if ($order_id <= 0) {
        return \Response::forge(json_encode([
            'success' => false,
            'error'   => 'ID de orden inválido'
        ]))->set_header('Content-Type', 'application/json');
    }

    /** @var Model_Providers_Order $order */
    $order = Model_Providers_Order::find($order_id);
    if ( ! $order) {
        return \Response::forge(json_encode([
            'success' => false,
            'error'   => 'Orden no encontrada'
        ]))->set_header('Content-Type', 'application/json');
    }

    \DB::start_transaction();

    // =========================================================
    // ACTUALIZAR ENCABEZADO (sin tocar status aún)
    // =========================================================
    $order->provider_id      = (int) ($data['proveedor_id'] ?? $order->provider_id);
    $order->code_order       = $data['codigo_oc']           ?? $order->code_order;
    $order->date_order       = !empty($data['fecha'])      ? strtotime($data['fecha'])      : $order->date_order;
    $order->currency_id      = $data['moneda']             ?? $order->currency_id;
    $order->notes            = $data['notas']              ?? $order->notes;
    $order->document_type_id = $data['document_type_id']   ?? $order->document_type_id;
    $order->code_type        = $data['codigo_producto_tipo'] ?? $order->code_type;
    $order->tax_id           = $data['tax_id']             ?? $order->tax_id;
    $order->retention_id     = $data['retention_id']       ?? $order->retention_id;

    $tipo_general = $data['tipo_general']
        ?? ($data['partidas'][0]['tipo'] ?? $order->type_general ?? 'servicio');
    $order->type_general = $tipo_general;

    $order->updated_at = time();
    $order->save();

    // =========================================================
    // DETALLES EXISTENTES
    // =========================================================
    $detalles_previos = Model_Providers_Order_Detail::query()
        ->where('order_id', $order_id)
        ->where('deleted', 0)
        ->order_by('id', 'asc')
        ->get();

    $detalles_previos_arr = array_values($detalles_previos);

    $moneda_id = (int) ($data['moneda'] ?? $order->currency_id);

    $subtotal_general = 0.0;
    $iva_general      = 0.0;
    $ret_general      = 0.0;
    $total_general    = 0.0;

    // =========================================================
    // INSERTAR / ACTUALIZAR PARTIDAS
    // =========================================================
    $index = 0;

    if (!empty($data['partidas']) && is_array($data['partidas'])) {

        foreach ($data['partidas'] as $p) {

            if (empty($p['description']) || empty($p['quantity']) || empty($p['unit_price'])) {
                continue;
            }

            $qty   = isset($p['quantity'])   ? (float) $p['quantity']   : 0;
            $price = isset($p['unit_price']) ? (float) $p['unit_price'] : 0;
            $tax_id       = $p['tax_id']       ?? $order->tax_id;
            $retention_id = $p['retention_id'] ?? $order->retention_id;

            $tax_rate = $this->_get_rate($tax_id, 'tax');            // 0..1
            $ret_rate = $this->_get_rate($retention_id, 'retencion');

            $subtotal = round($qty * $price, 2);
            $iva      = round($subtotal * $tax_rate, 2);
            $ret      = round($subtotal * $ret_rate, 2);
            $total    = round($subtotal + $iva - $ret, 2);

            $subtotal_general += $subtotal;
            $iva_general      += $iva;
            $ret_general      += $ret;
            $total_general    += $total;

            // Si hay detalle previo en esta posición -> actualizar
            if (isset($detalles_previos_arr[$index])) {
                /** @var Model_Providers_Order_Detail $detail */
                $detail = $detalles_previos_arr[$index];

                $detail->type              = $p['tipo']         ?? $tipo_general;
                $detail->product_id        = !empty($p['product_id'])   ? (int) $p['product_id']   : null;
                $detail->code_product      = $p['code_product'] ?? '';
                $detail->description       = $p['description']  ?? '';
                $detail->quantity          = $qty;
                $detail->unit_price        = $price;
                $detail->tax_id            = $tax_id;
                $detail->retention_id      = $retention_id;
                $detail->subtotal          = $subtotal;
                $detail->iva               = $iva;
                $detail->retencion         = $ret;
                $detail->total             = $total;
                $detail->currency_id       = $moneda_id;
                $detail->accounts_chart_id = !empty($p['accounts_chart_id']) ? (int) $p['accounts_chart_id'] : null;
                $detail->updated_at        = time();
                $detail->save();
            } else {
                // Crear nueva partida
                $detail = Model_Providers_Order_Detail::forge([
                    'order_id'          => $order_id,
                    'type'              => $p['tipo']         ?? $tipo_general,
                    'product_id'        => !empty($p['product_id'])   ? (int) $p['product_id']   : null,
                    'code_product'      => $p['code_product'] ?? '',
                    'description'       => $p['description']  ?? '',
                    'quantity'          => $qty,
                    'unit_price'        => $price,
                    'tax_id'            => $tax_id,
                    'retention_id'      => $retention_id,
                    'subtotal'          => $subtotal,
                    'iva'               => $iva,
                    'retencion'         => $ret,
                    'total'             => $total,
                    'currency_id'       => $moneda_id,
                    'delivered'         => 0,
                    'invoiced'          => 0,
                    'deleted'           => 0,
                    'accounts_chart_id' => !empty($p['accounts_chart_id']) ? (int) $p['accounts_chart_id'] : null,
                    'created_at'        => time(),
                    'updated_at'        => time(),
                ]);
                $detail->save();
            }

            $index++;
        }
    }

    // =========================================================
    // MARCAR COMO ELIMINADAS (LÓGICO) LAS PARTIDAS SOBRANTES
    // =========================================================
    $total_previos = count($detalles_previos_arr);
    if ($index < $total_previos) {
        for ($i = $index; $i < $total_previos; $i++) {
            $detail = $detalles_previos_arr[$i];
            $detail->deleted    = 1;
            $detail->updated_at = time();
            $detail->save();
        }
    }

    // =========================================================
    // ACTUALIZAR CABECERA CON TOTALES
    // =========================================================
    $order->subtotal  = $subtotal_general;
    $order->iva       = $iva_general;
    $order->retencion = $ret_general;
    $order->total     = $total_general;

    // =========================================================
    // AUTORIZACIÓN
    // =========================================================
    $modo = $data['modo'] ?? 'solo_guardar';

    if ($modo === 'autorizar') {

        // Validar cuentas contables antes de autorizar
        if ( ! Model_Providers_Order_Detail::validate_accounts($order_id)) {
            \DB::rollback_transaction();
            return \Response::forge(json_encode([
                'success' => false,
                'error'   => 'No se puede autorizar: faltan cuentas contables en las partidas.'
            ]))->set_header('Content-Type', 'application/json');
        }

        $order->status        = 1;               // autorizado
        $order->authorized_by = \Auth::get('id');
        $order->authorized_at = time();          // <-- timestamp UNIX
    }

    $order->updated_at = time();
    $order->save();

    \DB::commit_transaction();

    return \Response::forge(json_encode([
        'success' => true
    ]))->set_header('Content-Type', 'application/json');
}






    ////////////////////////////////////////////////////////
    // NOTAS DE CRÉDITO A PROVEEDORES
    ////////////////////////////////////////////////////////

        /**
     * BUSCAR PROVEEDORES
     *
     * Endpoint AJAX para buscar proveedores por nombre o RFC.
     */
    public function action_search_providers()
    {
        if (!\Input::is_ajax()) {
            \Log::error("[NOTASDECREDITO][SEARCH_PROVIDERS] Llamada no AJAX");
            return \Response::forge(json_encode(['msg' => 'invalid_request']));
        }

        $access_id    = \Input::post('access_id');
        $access_token = \Input::post('access_token');
        $term         = trim(\Input::post('term'));

        \Log::debug("[NOTASDECREDITO][SEARCH_PROVIDERS] INICIO - term={$term}, access_id={$access_id}");

        // TODO: validar access_id y access_token según tu helper de auth

        $query = \Model_Provider::query(); //->where('deleted', 0);

        if ($term) {
            $query->and_where_open()
                ->where('name', 'like', "%{$term}%")
                ->or_where('rfc', 'like', "%{$term}%")
                ->and_where_close();
        }

        $providers = $query->order_by('name', 'asc')
                        ->limit(20)
                        ->get();

        \Log::debug("[NOTASDECREDITO][SEARCH_PROVIDERS] Proveedores encontrados=" . count($providers));

        $data = [];
        foreach ($providers as $p) {
            $data[] = [
                'id'   => $p->id,
                'name' => $p->name,
                'rfc'  => $p->rfc,
            ];
            \Log::debug("[NOTASDECREDITO][SEARCH_PROVIDERS] Proveedor: id={$p->id}, name={$p->name}, rfc={$p->rfc}");
        }

        return \Response::forge(json_encode([
            'msg'  => 'ok',
            'data' => $data
        ]));
    }

    /**
     * OBTENER FACTURAS DE UN PROVEEDOR PARA NOTAS DE CRÉDITO
     */
    public function action_get_compras_facturas($provider_id = null)
    {
        if (!\Input::is_ajax()) {
            \Log::error("[NOTASDECREDITO][GET_FACTURAS] Llamada no AJAX");
            return;
        }

        \Log::debug("[NOTASDECREDITO][GET_FACTURAS] INICIO - provider_id={$provider_id}");

        $facturas = Model_Providers_Bill::query()
            ->where('provider_id', $provider_id)
            ->where('deleted', 0)
            ->where('status', 1) # abiertas
            ->get();

        \Log::debug("[NOTASDECREDITO][GET_FACTURAS] Facturas encontradas=" . count($facturas));

        $data = [];
        foreach ($facturas as $f) {
            $data[] = [
                'id'    => $f->id,
                'uuid'  => $f->uuid,
                'total' => $f->total,
            ];
            \Log::debug("[NOTASDECREDITO][GET_FACTURAS] Factura: id={$f->id}, uuid={$f->uuid}, total={$f->total}");
        }

        return \Response::forge(json_encode(['msg' => 'ok', 'facturas' => $data]));
    }

    /**
     * OBTENER ORDENES DE COMPRA DE UN PROVEEDOR PARA NOTAS DE CRÉDITO
     */
    public function action_get_compras_ocs($provider_id = null)
    {
        if (!\Input::is_ajax()) {
            \Log::error("[NOTASDECREDITO][GET_OCS] Llamada no AJAX");
            return;
        }

        \Log::debug("[NOTASDECREDITO][GET_OCS] INICIO - provider_id={$provider_id}");

        $ocs = Model_Providers_Order::query()
            ->where('provider_id', $provider_id)
            ->where('deleted', 0)
            ->where('status', 1) # abiertas
            ->get();

        \Log::debug("[NOTASDECREDITO][GET_OCS] OCs encontradas=" . count($ocs));

        $data = [];
        foreach ($ocs as $oc) {
            $folio = $oc->folio ?? "OC {$oc->id}";
            $data[] = [
                'id'    => $oc->id,
                'folio' => $folio,
                'total' => $oc->total,
            ];
            \Log::debug("[NOTASDECREDITO][GET_OCS] OC: id={$oc->id}, folio={$folio}, total={$oc->total}");
        }

        return \Response::forge(json_encode(['msg' => 'ok', 'ocs' => $data]));
    }


	// POST: admin/compras/contrarecibos/get_proveedores
// CONTROLADOR: Controller_Admin_Compras_Contrarecibos.php

/**
     * Endpoint para obtener la lista de proveedores con sus días de crédito.
     * Utilizado en la vista de agregar contrarecibos para el selector de proveedor.
     * URL de ejemplo: /admin/compras/contrarecibos/get_proveedores
     *
     * @return Response
     */
    public function post_get_proveedores()
    {
        try {
            // Cargar proveedores y su relación con el término de pago
            $proveedores = Model_Provider::query()
                ->related('payment_term') // Carga la relación con Model_Payments_Term
                ->order_by('name', 'asc')
                ->get();

            $proveedores_data = [];

            // CORRECCIÓN: Obtener los días de crédito por defecto de tu Model_Config actual
            $config_global = Model_Config::query()->get_one(); // Asumo que solo hay una fila de configuración
            $default_credit_days = $config_global && !is_null($config_global->payment_terms_days) ? (int)$config_global->payment_terms_days : 30;

            foreach ($proveedores as $prov) {
                $dias_credito = $default_credit_days; // Por defecto, los días de la configuración global
                $origen_credito = 'config';

                // Si el proveedor tiene un término de pago asociado y días de offset definidos
                if ($prov->payment_term && !is_null($prov->payment_term->start_offset_days)) {
                    $dias_credito = (int)$prov->payment_term->start_offset_days;
                    $origen_credito = 'proveedor';
                } elseif ($prov->payment_terms_id) {
                    // Si payment_terms_id está seteado pero payment_term es null, significa que el término no se encontró
                    \Log::warning("Proveedor {$prov->id} tiene payment_terms_id ('{$prov->payment_terms_id}') pero no se encontró el Model_Payments_Term asociado.");
                }

                $proveedores_data[] = [
                    'id' => $prov->id,
                    'name' => $prov->name,
                    'rfc' => $prov->rfc,
                    'dias_credito' => $dias_credito,
                    'origen_credito' => $origen_credito,
                ];
            }

            return $this->response([
                'success' => true,
                'proveedores' => $proveedores_data,
            ]);

        } catch (Exception $e) {
            \Log::error("Error al cargar proveedores para contrarecibos: " . $e->getMessage());
            return $this->response([
                'success' => false,
                'message' => 'Error al cargar proveedores: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Endpoint para obtener las facturas pendientes de pago para un proveedor específico.
     * Calcula la fecha de pago estimada y si la factura "puede_pagar".
     * URL de ejemplo: /admin/compras/contrarecibos/get_facturas_pendientes
     *
     * @return Response
     */
    public function post_get_facturas_pendientes()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $proveedor_id = $input['proveedor_id'] ?? null;

            if (empty($proveedor_id)) {
                return $this->response([
                    'success' => false,
                    'message' => 'ID de proveedor no proporcionado.',
                ], 400);
            }

            // Obtener el proveedor para determinar sus días de crédito
            $proveedor = Model_Provider::query()
                ->where('id', $proveedor_id)
                ->related('payment_term')
                ->get_one();

            if (!$proveedor) {
                return $this->response([
                    'success' => false,
                    'message' => 'Proveedor no encontrado.',
                ], 404);
            }

            // CORRECCIÓN: Obtener los días de crédito de tu Model_Config actual
            $config_global = Model_Config::query()->get_one(); // Asumo que solo hay una fila de configuración
            $default_credit_days = $config_global && !is_null($config_global->payment_terms_days) ? (int)$config_global->payment_terms_days : 30;

            $dias_credito_proveedor = $default_credit_days;
            $origen_credito = 'config';
            if ($proveedor->payment_term && !is_null($proveedor->payment_term->start_offset_days)) {
			$dias_credito_proveedor = (int)$proveedor->payment_term->start_offset_days;
                $origen_credito = 'proveedor';
            }

            // Obtener las facturas pendientes de pago para el proveedor
            // 'pendiente_pago' es el estado que asignamos al importar facturas que aún no están en un contrarecibo.
            $facturas = Model_Providers_Bill::query()
                ->where('provider_id', $proveedor_id)
                ->where('status', '1')
				->or_where('status', '2') // Filtra por el estado de factura pendiente
                ->where('deleted', 0) // Asumo que las facturas eliminadas no deben aparecer
                ->related('order') // Carga la relación con Model_Providers_Order para obtener datos de la OC
                ->order_by('created_at', 'desc')
                ->get();

            $facturas_data = [];
            foreach ($facturas as $fac) {
                $fecha_factura_timestamp = null;
                $invoice_data_arr = [];

                // Deserializar invoice_data para obtener la fecha de la factura
                if (!empty($fac->invoice_data)) {
                    // Intenta deserializar. Si falla, unserialize devuelve false.
                    $deserialized_data = @unserialize($fac->invoice_data); // Usamos @ para suprimir warnings si no es serializable
                    if ($deserialized_data !== false) {
                        $invoice_data_arr = $deserialized_data;
                    } else {
                        // Si no es serializable, intenta como JSON (si es una posibilidad en tu sistema)
                        $json_data = json_decode($fac->invoice_data, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $invoice_data_arr = $json_data;
                        } else {
                            \Log::warning("Factura UUID {$fac->uuid}: invoice_data no es serializable ni JSON válido.");
                        }
                    }
                }

                if (isset($invoice_data_arr['fecha'])) {
                    $fecha_factura_timestamp = strtotime($invoice_data_arr['fecha']);
                }

                $fecha_pago_estimada = null;
                $puede_pagar = false;

                // Calcular fecha de pago estimada solo si hay una fecha de factura válida
                if ($fecha_factura_timestamp) {
                    // Por simplicidad, la fecha base para el cálculo de crédito es la fecha de la factura.
                    // Si necesitas lógica para 'base_date_type' (ej. fecha de recepción), la añadirías aquí.
                    $fecha_base_timestamp = $fecha_factura_timestamp;

					$fecha_pago_estimada = strtotime("+$dias_credito_proveedor days", $fecha_base_timestamp);
                    $puede_pagar = true; // La factura es "completa" si se pudo calcular la fecha de pago
                } else {
                    \Log::warning("Factura UUID {$fac->uuid} no tiene una fecha de factura válida o invoice_data está corrupto.");
                }

                $facturas_data[] = [
                    'id' => $fac->id,
                    'uuid' => $fac->uuid,
                    'total' => (float)$fac->total,
                    'fecha_oc' => $fac->order ? date('d/m/Y', $fac->order->date_order) : 'N/A',
                    'fecha_factura' => $fecha_factura_timestamp ? date('d/m/Y', $fecha_factura_timestamp) : 'N/A',
                    'code_order' => $fac->order ? $fac->order->code_order : 'Sin OC',
                    'dias_credito' => $dias_credito_proveedor,
                    'fecha_pago' => $fecha_pago_estimada ? date('d/m/Y', $fecha_pago_estimada) : 'N/A',
                    'puede_pagar' => $puede_pagar,
                    'invoice_data_completa' => $invoice_data_arr, // Pasar los datos completos para el modal de detalle
                ];
            }

            return $this->response([
                'success' => true,
                'facturas' => $facturas_data,
                'dias_credito' => $dias_credito_proveedor,
                'origen_credito' => $origen_credito,
                'faltantes_config' => false, // Se puede setear a true si falta alguna configuración crítica
            ]);

        } catch (Exception $e) {
            \Log::error("Error al cargar facturas pendientes: " . $e->getMessage());
            return $this->response([
                'success' => false,
                'message' => 'Error al cargar facturas: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Endpoint para crear un nuevo contrarecibo.
     * Recibe el ID del proveedor y un array de IDs de facturas seleccionadas.
     * URL de ejemplo: /admin/compras/contrarecibos/crear_contrarecibo
     *
     * @return Response
     */
    public function post_crear_contrarecibo()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $proveedor_id = $input['proveedor_id'] ?? null;
            $factura_ids = $input['facturas'] ?? []; // Este es un array de IDs de factura

            if (empty($proveedor_id) || empty($factura_ids)) {
                return $this->response([
                    'success' => false,
                    'message' => 'Datos incompletos para crear el contrarecibo.',
                ], 400);
            }

            // 1. Validar que las facturas existan, pertenezcan al proveedor y estén pendientes
            $facturas_seleccionadas = Model_Providers_Bill::query()
                ->where('provider_id', $proveedor_id)
                ->where('id', 'IN', $factura_ids)
                ->where('status', 1)
				->or_where('status', 2) // Asegurarse que estén pendientes de pago (status 0)
                ->get();

            if (count($facturas_seleccionadas) !== count($factura_ids)) {
                return $this->response([
                    'success' => false,
                    'message' => 'Algunas facturas no son válidas, no pertenecen al proveedor o ya han sido procesadas.',
                ], 400);
            }

            $total_contrarecibo = 0;
            $orden_ids_para_detalle = []; // Para Model_Providers_Receipts_Details

            // Recalcular días de crédito del proveedor para este contexto
            $proveedor = Model_Provider::query()
                ->where('id', $proveedor_id)
                ->related('payment_term')
                ->get_one();

            // Obtener los días de crédito de tu Model_Config actual
            $config_global = Model_Config::query()->get_one(); // Asumo que solo hay una fila de configuración
            $default_credit_days = $config_global && !is_null($config_global->payment_terms_days) ? (int)$config_global->payment_terms_days : 30;

            $dias_credito_proveedor = $default_credit_days;
            if ($proveedor->payment_term && !is_null($proveedor->payment_term->start_offset_days)) {
                $dias_credito_proveedor = (int)$proveedor->payment_term->start_offset_days;
            }

            // Fecha de creación del contrarecibo (timestamp actual)
            $receipt_creation_date = time();
            // Calcular la fecha de pago estimada para el contrarecibo basada en su fecha de creación
            $fecha_pago_contrarecibo = strtotime("+$dias_credito_proveedor days", $receipt_creation_date);


            foreach ($facturas_seleccionadas as $fac) {
                $total_contrarecibo += (float)$fac->total;

                // Recopilar order_id para la tabla de detalles del contrarecibo
                // Si la factura no tiene OC, se guarda como null o 0 según tu esquema de DB
                $orden_ids_para_detalle[$fac->id] = $fac->order_id ?? null;
            }

            // 2. Crear el nuevo Contrarecibo (Model_Providers_Receipt)
            $new_receipt = Model_Providers_Receipt::forge([
                'provider_id' => $proveedor_id,
                'total' => $total_contrarecibo,
                'status' => 3, // CAMBIO: Estado inicial del contrarecibo: 1 (En revisión)
                'receipt_date' => $receipt_creation_date, // Fecha de creación del contrarecibo (timestamp actual)
                'payment_date' => $fecha_pago_contrarecibo, // Fecha de pago estimada del contrarecibo
                'receipt_number' => null, // Se asignará después de obtener el ID
            ]);
            $new_receipt->save(); // Guarda para obtener el ID

            // Generar un número de contrarecibo único (ej. REC-YYYYMMDD-ID)
            $new_receipt->receipt_number = 'REC-' . date('Ymd', $new_receipt->receipt_date) . '-' . $new_receipt->id;
            $new_receipt->save(); // Guarda de nuevo para actualizar el número

            // 3. Asociar las facturas al contrarecibo y actualizar su estado
            foreach ($facturas_seleccionadas as $fac) {
                Model_Providers_Receipts_Details::forge([
                    'receipt_id' => $new_receipt->id,
                    'bill_id' => $fac->id,
                    'order_id' => $orden_ids_para_detalle[$fac->id], // Puede ser null si la factura no tenía OC
                ])->save();

                // Actualizar el estado de la factura a 'En revisión' (status 1)
                $fac->status = 1; // CAMBIO: Nuevo estado para facturas ya en un contrarecibo (En revisión)
                $fac->save();
            }

            return $this->response([
                'success' => true,
                'message' => 'Contrarecibo generado exitosamente.',
                'receipt_id' => $new_receipt->id,
                'receipt_number' => $new_receipt->receipt_number,
            ]);

        } catch (Exception $e) {
            \Log::error("Error al crear contrarecibo: " . $e->getMessage());
            return $this->response([
                'success' => false,
                'message' => 'Error al crear contrarecibo: ' . $e->getMessage(),
            ], 500);
        }
    }

/**auxiliares */
private function getTaxRate($tax_id) {
    if (!$tax_id) return 0;
    $tax = Model_Tax::find($tax_id);
    return $tax ? floatval($tax->rate) : 0;
}

private function getRetentionRate($ret_id) {
    if (!$ret_id) return 0;
    $ret = Model_Retention::find($ret_id);
    return $ret ? floatval($ret->rate) : 0;
}

/**
 * AYUDA PARA OBTENER EL RATE DE IMPUESTO O RETENCIÓN
 * @param string $id
 * @param string $tipo 'tax' o 'retencion'
 * @return float
 */
private function _get_rate($id, $tipo = 'tax')
{
    if (empty($id)) return 0.0;
    if ($tipo === 'tax') {
        $t = Model_Tax::find($id);
        return $t ? (float)$t->rate : 0.0;
    } else {
        $r = Model_Retention::find($id);
        return $r ? (float)$r->rate : 0.0;
    }
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////AQUI EMPIEZAN LOS ENDPOINTS PARA EL PARSEO DE FACTURAS XML O CARGA O VALIDACION XML///////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * AJAX: PRE-PROCESA ARCHIVOS XML (NO CREA NADA, SOLO LEE Y VALIDA)
 * Retorna un JSON para Vue con:
 * - Facturas válidas (info básica extraída)
 * - Proveedores nuevos a crear (y campos faltantes)
 * - Errores de validación
 */
/**
 * AJAX: PARSEA MÚLTIPLES XMLs DE FACTURA, SÓLO LEE Y VALIDA, NO CREA NADA
 * USO: Para carga previa de archivos en el módulo de importación masiva.
 * RESPONDE EN JSON, listo para Vue.js (facturas-proveedores-vue.js)
 */
 public function action_ajax_parse_multiple()
    {
        // ==========================================
        // 1. VALIDAR PERMISOS DE USUARIO
        // ==========================================
        if (!Auth::member(100) && !Auth::member(50)) {
            return $this->response(
                ['error' => 'No autorizado.'],
                403,
                ['Content-Type' => 'application/json']
            );
        }

        // ==========================================
        // 2. SOLO SE PERMITE POR AJAX
        // ==========================================
        if (!Input::is_ajax()) {
            return $this->response(
                ['error' => 'Solo solicitudes AJAX.'],
                400,
                ['Content-Type' => 'application/json']
            );
        }

        // ==========================================
        // 3. OBTENER CONFIGURACIÓN DE LA EMPRESA
        // ==========================================
        $config = Model_Config::query()->get_one();
        if (!$config) {
            \Log::error('NO SE ENCONTRÓ LA CONFIGURACIÓN DE LA EMPRESA.');
            return $this->response(
                ['error' => 'NO SE ENCONTRÓ LA CONFIGURACIÓN DE LA EMPRESA.'],
                500,
                ['Content-Type' => 'application/json']
            );
        }

        $rfc_empresa  = trim($config->rfc);
        $name_empresa = trim($config->name);

        $resultados = [];
        $proveedores_nuevos = [];
        $directorio_temporal = DOCROOT . 'assets/tmp_facturas/';

        // Asegurarse de que el directorio temporal exista
        if (!is_dir($directorio_temporal)) {
            mkdir($directorio_temporal, 0755, true);
        }

        // ==========================================
        // 4. VALIDAR Y MOVER ARCHIVOS SUBIDOS
        // ==========================================
        $files = $_FILES['facturas'] ?? null;
        if (!$files || empty($files['name'][0])) {
            \Log::error('No se han subido archivos.');
            return $this->response(
                ['error' => 'No se han subido archivos.'],
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $facturas_para_procesar = [];
        foreach ($files['name'] as $index => $file_name) {
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $tmp_name  = $files['tmp_name'][$index];

            if ($extension === 'xml' && is_uploaded_file($tmp_name)) {
                // Genera un nombre de archivo único para evitar colisiones
                $nombre_guardado = uniqid('xml_') . '.xml';
                $ruta_guardada = $directorio_temporal . $nombre_guardado;

                // Mueve el archivo temporal de PHP a nuestra carpeta temporal persistente
                if (move_uploaded_file($tmp_name, $ruta_guardada)) {
                    $facturas_para_procesar[] = [
                        'original_name' => $file_name,
                        'path'          => $ruta_guardada, // ¡Esta es la ruta que pasaremos a Vue!
                    ];
                } else {
                    \Log::error('No se pudo mover el archivo: ' . $file_name);
                }
            }
        }

        if (empty($facturas_para_procesar)) {
            \Log::error('No se encontraron archivos XML válidos para procesar.');
            return $this->response(
                ['error' => 'No se encontraron archivos XML válidos para procesar.'],
                400,
                ['Content-Type' => 'application/json']
            );
        }

        // ==========================================
        // 5. PROCESAR ARCHIVO POR ARCHIVO (ahora desde la ruta guardada)
        // ==========================================
        foreach ($facturas_para_procesar as $factura_temp_info) {
            // Inicializa la fila de resultados con los datos básicos
            $row = [
                'archivo'             => $factura_temp_info['original_name'],
                'ruta_archivo_temp'   => $factura_temp_info['path'], // ¡Aquí se guarda la ruta temporal!
                'ok'                  => false,
                'errores'             => [],
                'uuid'                => '',
                'emisor_rfc'          => '',
                'emisor_nombre'       => '',
                'proveedor_existe'    => false,
                'proveedor_id'        => null,
                'proveedor_faltantes' => [],
                'factura_duplicada'   => false,
                'valida_sat'          => null,
                'mensaje_sat'         => '',
                'invoice_data_completa' => [], // Para almacenar todos los datos del XML
            ];

            // ====== LEE Y PARSEA EL XML ======
            try {
                $xml_content = file_get_contents($factura_temp_info['path']);
                $invoice_data = Helper_Invoicexml::extract_invoice_data_from_xml($xml_content);
            } catch (Exception $e) {
                $row['errores'][] = 'Error leyendo o parseando el XML: ' . $e->getMessage();
                // Elimina el archivo temporal si hubo un error al procesarlo
                if (file_exists($factura_temp_info['path'])) {
                    unlink($factura_temp_info['path']);
                }
                $resultados[] = $row;
                continue;
            }

            if (!$invoice_data || empty($invoice_data['uuid'])) {
                $row['errores'][] = 'El XML no tiene un UUID válido o no se pudo extraer la información.';
                // Elimina el archivo temporal si no se pudo extraer el UUID
                if (file_exists($factura_temp_info['path'])) {
                    unlink($factura_temp_info['path']);
                }
                $resultados[] = $row;
                continue;
            }

            // ====== EXTRAE INFORMACIÓN Y LA COPIA A $ROW ======
            // Copia todos los datos extraídos del XML a la fila de resultados
            $row = array_merge($row, $invoice_data);
            $row['invoice_data_completa'] = $invoice_data; // Guarda el array completo para el guardado final

            // ====== ¿EXISTE PROVEEDOR? ======
            $provider = Model_Provider::query()
                ->where('rfc', $invoice_data['emisor_rfc'])
                ->get_one();

            if ($provider) {
                $row['proveedor_existe'] = true;
                $row['proveedor_id']     = $provider->id;
            } else {
                $faltantes = [
                    'email'           => '',
                    'telefono'        => '',
                    'nombre_contacto' => ''
                ];
                $row['proveedor_faltantes'] = $faltantes;
                $proveedores_nuevos[$invoice_data['emisor_rfc']] = [
                    'rfc'       => $invoice_data['emisor_rfc'],
                    'nombre'    => $invoice_data['emisor_nombre'],
                    'faltantes' => $faltantes,
                ];
            }

            // ====== ¿FACTURA DUPLICADA? ======
            $factura_existe_db = false;
            if ($provider) {
                $existe_bill = Model_Providers_Bill::query()
                    ->where('uuid', $invoice_data['uuid'])
                    ->where('provider_id', $provider->id)
                    ->get_one();
                if ($existe_bill) {
                    $factura_existe_db = true;
                    $row['factura_duplicada'] = true;
                    $row['errores'][] = "Ya existe una factura con este UUID ('{$invoice_data['uuid']}') para el proveedor.";
                }
            }

            // ====== VALIDACIÓN CONTRA CONFIGURACIÓN DE EMPRESA ======
            $valida = Helper_Invoicexml::validate_against_config($invoice_data, $config);
            if (!$valida['success']) {
                $row['errores'][] = $valida['mensaje'];
            }

            // ====== (OPCIONAL) VALIDACIÓN SAT ======
            // Asegúrate de que Helper_Invoicexml::validate_cfdi_sat exista y funcione
            if (class_exists('Helper_Invoicexml') && method_exists('Helper_Invoicexml', 'validate_cfdi_sat')) {
                $validacion_sat = Helper_Invoicexml::validate_cfdi_sat(
                    $invoice_data['uuid'],
                    $invoice_data['emisor_rfc'],
                    $invoice_data['receptor_rfc'],
                    $invoice_data['total']
                );
                $row['valida_sat'] = $validacion_sat['estatus_sat'];
                $row['mensaje_sat'] = $validacion_sat['mensaje'];
            } else {
                $row['valida_sat'] = 'NO_DISPONIBLE';
                $row['mensaje_sat'] = 'Validación SAT no disponible.';
            }

            // ====== OK SOLO SI NO HUBO ERRORES Y NO DUPLICADA ======
            $row['ok'] = (empty($row['errores']) && !$factura_existe_db);

            $resultados[] = $row;
        }

        // ==========================================
        // 6. DEVUELVE LA RESPUESTA JSON PARA VUE
        // ==========================================
        \Log::info('Respuesta enviada a Vue (Import XML): ' . json_encode([
            'facturas' => $resultados,
            'proveedores_nuevos' => array_values($proveedores_nuevos)
        ]));

        return $this->response([
            'facturas' => $resultados,
            'proveedores_nuevos' => array_values($proveedores_nuevos),
        ], 200, ['Content-Type' => 'application/json']);
    }




 /**
     * GUARDA FACTURAS DE PROVEEDOR MASIVO (solo XML, NO PDF)
     * Este endpoint recibe un array de facturas y un array de proveedores nuevos (si los hay).
     * Por cada factura:
     * - Verifica que exista el proveedor.
     * - Busca OC manual o crea OC automática (con uuid).
     * - Guarda los detalles de la OC (partidas/productos).
     * - Si ya existe la OC para ese UUID, NO la vuelve a crear.
     * - Guarda la factura si no existe previamente.
     * - Garantiza que no haya duplicidad de OC ni de factura (uuid único por proveedor).
     * - Mueve el archivo XML de la ubicación temporal a la final.
     *
     * DEVUELVE: { success: bool, mensaje: string, errores: array }
     */
    public function post_guardar_facturas_masivo()
    {
        \Log::info('==== [INICIO] Guardado masivo de facturas (AJAX) ====');

        $input = json_decode(file_get_contents('php://input'), true);
        $facturas = isset($input['facturas']) ? $input['facturas'] : [];

        $guardadas = 0;
        $resultados_procesamiento = []; // Nuevo array para guardar los resultados detallados

        foreach ((array)$facturas as $idx => $factura) {
            \Log::info("----- [FACTURA #$idx] Procesando UUID: {$factura['uuid']} -----");

            try {
                // 1. VALIDACIÓN MÍNIMA
                if (empty($factura['uuid']) || empty($factura['emisor_rfc']) || empty($factura['ruta_archivo_temp'])) {
                    throw new Exception("Faltan datos críticos en la factura (UUID, RFC emisor o ruta del archivo XML temporal).");
                }

                // 2. OBTENER EL PROVEEDOR YA CREADO (DEBE EXISTIR)
                $rfc = strtoupper(trim($factura['emisor_rfc']));
                $provider = Model_Provider::query()->where('rfc', $rfc)->get_one();

                if (!$provider) {
                    $resultados_procesamiento[] = [
                        'uuid' => $factura['uuid'],
                        'tipo_error' => 'sin_proveedor',
                        'mensaje' => "Proveedor con RFC {$rfc} no encontrado. Debe ser creado antes."
                    ];
                    \Log::warning("[FACTURA #$idx] ERROR: Proveedor no encontrado para RFC {$rfc}.");
                    continue; // Pasa a la siguiente factura
                }

                // 3. BUSCAR O CREAR OC (ORDEN DE COMPRA) SOLO SI NO EXISTE CON ESTE UUID
                $order_id = null;
                if (!empty($factura['order_id'])) {
                    $order_id = $factura['order_id'];
                    \Log::info("[FACTURA #$idx] Usando OC manual, ID: $order_id");
                } else {
                    $existe_oc = Model_Providers_Order::query()
                        ->where('provider_id', $provider->id)
                        ->where('uuid', $factura['uuid'])
                        ->get_one();

                    if ($existe_oc) {
                        $order_id = $existe_oc->id;
                        \Log::info("[FACTURA #$idx] OC automática YA EXISTE: $order_id");
                    } else {
                        // Código para crear nueva OC
                        $code_oc = Helper_OC::next_code();
                        $date_order = !empty($factura['fecha']) ? strtotime($factura['fecha']) : time();
                        $currency_id = 1;

                        $tax_id_global = 0;
                        if (!empty($factura['traslados_globales']) && isset($factura['traslados_globales'][0])) {
                            $traslado_global = $factura['traslados_globales'][0];
                            $tax = Model_Tax::query()
                                ->where('clave_sat', $traslado_global['impuesto'])
                                ->where('rate', $traslado_global['tasaocuota'])
                                ->get_one();
                            if ($tax) {
                                $tax_id_global = $tax->id;
                            }
                        }

                        $retention_id_global = 0;
                        if (!empty($factura['retenciones_globales']) && isset($factura['retenciones_globales'][0])) {
                            $retencion_global = $factura['retenciones_globales'][0];
                            $ret = Model_Retention::query()
                                ->where('code', $retencion_global['impuesto'])
                                ->where('rate', $retencion_global['tasaocuota'])
                                ->get_one();
                            if ($ret) {
                                $retention_id_global = $ret->id;
                            }
                        }

                        $nueva_oc = new Model_Providers_Order([
                            'provider_id' => $provider->id,
                            'code_order' => $code_oc,
                            'date_order' => $date_order,
                            'currency_id' => $currency_id,
                            'status' => 2,
                            'total' => $factura['total'],
                            'notes' => 'Orden generada automáticamente por XML',
                            'deleted' => 0,
                            'origin' => 0,
                            'has_invoice' => 0,
                            'tax_id' => $tax_id_global,
                            'retention_id' => $retention_id_global,
                            'uuid' => $factura['uuid'],
                            'created_at' => time(),
                        ]);
                        $nueva_oc->save();
                        $order_id = $nueva_oc->id;
                        \Log::info("[FACTURA #$idx] OC automática creada: $order_id");

                        foreach ((array)$factura['productos'] as $prod) {
                            $iva_total = 0;
                            $retencion_total = 0;
                            $tax_id = 0;
                            $retention_id = 0;

                            if (!empty($prod['traslados'])) {
                                foreach ($prod['traslados'] as $traslado) {
                                    $iva_total += $traslado['importe'];
                                    $tax = Model_Tax::query()
                                        ->where('clave_sat', $traslado['impuesto'])
                                        ->where('type_factor', $traslado['tipo_factor'])
                                        ->where('rate', $traslado['tasaocuota'])
                                        ->get_one();
                                    if ($tax) {
                                        $tax_id = $tax->id;
                                    }
                                }
                            }

                            if (!empty($prod['retenciones'])) {
                                foreach ($prod['retenciones'] as $retencion) {
                                    $retencion_total += $retencion['importe'];
                                    $ret = Model_Retention::query()
                                        ->where('code', $retencion['impuesto'])
                                        ->where('factor_type', $retencion['tipo_factor'])
                                        ->where('rate', $retencion['tasaocuota'])
                                        ->get_one();
                                    if ($ret) {
                                        $retention_id = $ret->id;
                                    }
                                }
                            }

                            Model_Providers_Order_Detail::forge([
                                'order_id' => $order_id,
                                'product_id' => $prod['product_id'] ?? null,
                                'code_product' => $prod['noidentificacion'] ?? '',
                                'description' => $prod['descripcion'] ?? '',
                                'quantity' => $prod['cantidad'] ?? 1,
                                'unit_price' => $prod['valor_unitario'] ?? 0,
                                'subtotal' => $prod['importe'] ?? 0,
                                'iva' => $iva_total,
                                'retencion' => $retencion_total,
                                'total' => ($prod['importe'] + $iva_total) - $retencion_total,
                                'delivered' => $prod['delivered'] ?? 0,
                                'invoiced' => $prod['invoiced'] ?? 0,
                                'tax_id' => $tax_id,
                                'retention_id' => $retention_id,
                                'currency_id' => $prod['currency_id'] ?? 0,
                                'created_at' => time(),
                            ])->save();
                            \Log::info("[FACTURA #$idx] Partida agregada. IVA: {$iva_total}, Retención: {$retencion_total}");
                        }
                    }
                }

                // 4. EVITA DUPLICIDAD DE FACTURA
                $existe_bill_db = Model_Providers_Bill::query()
                    ->where('uuid', $factura['uuid'])
                    ->where('provider_id', $provider->id)
                    ->get_one();

                if ($existe_bill_db) {
                    $resultados_procesamiento[] = [
                        'uuid' => $factura['uuid'],
                        'tipo_error' => 'duplicada',
                        'mensaje' => "La factura UUID {$factura['uuid']} ya existe para este proveedor."
                    ];
                    \Log::warning("[FACTURA #$idx] FACTURA DUPLICADA, UUID: {$factura['uuid']}. Omitiendo guardado.");
                    if (file_exists($factura['ruta_archivo_temp'])) {
                        unlink($factura['ruta_archivo_temp']);
                        \Log::info("[FACTURA #$idx] Archivo temporal eliminado por duplicidad: {$factura['ruta_archivo_temp']}");
                    }
                    continue; // Pasa a la siguiente factura
                }

                // 5. GUARDA LA FACTURA
                $ruta_temp = $factura['ruta_archivo_temp'];
                if (!file_exists($ruta_temp)) {
                    throw new Exception("El archivo XML temporal para el UUID {$factura['uuid']} no existe en la ruta: {$ruta_temp}.");
                }
                $xml_content = file_get_contents($ruta_temp);

				// DETERMINA SI REQUIERE REP (PAGO 99)
				$require_rep = 0;
				if (
					//(isset($factura['metodo_pago']) && $factura['metodo_pago'] == 'PPD') ||
					(isset($factura['forma_pago']) && $factura['forma_pago'] == '99')
				) {
					$require_rep = 1;
				}
				\Log::info("[FACTURA #$idx] Requiere REP: " . ($require_rep ? 'Sí' : 'No'));

                $invoice = new Model_Providers_Bill([
                    'provider_id' => $provider->id,
                    'xml' => $xml_content,
                    'uuid' => $factura['uuid'],
                    'total' => number_format((float)$factura['total'], 2, '.', ''),
                    'order_id' => $order_id,
                    'deleted' => 0,
                    'invoice_data' => serialize($factura['invoice_data_completa']),
                    'status' => 1,
					'require_rep'   => $require_rep,
                    'pdf' => 0,
                    'created_at' => time(),
                ]);
                $invoice->save();
                $guardadas++;
                \Log::info("[FACTURA #$idx] Factura guardada. ID: {$invoice->id}, UUID: {$factura['uuid']}");

                $resultados_procesamiento[] = [
                    'uuid' => $factura['uuid'],
                    'success' => true,
                    'mensaje' => 'Factura guardada correctamente.'
                ];

                // 6. MUEVE EL ARCHIVO XML
                $upload_path = DOCROOT . 'assets/facturas/proveedores/' . $provider->id;
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0755, true);
                    \Log::info("[FACTURA #$idx] Carpeta creada: $upload_path");
                }
                $nombre_archivo_final = $factura['uuid'] . '.xml';
                if (!rename($ruta_temp, $upload_path . '/' . $nombre_archivo_final)) {
                    \Log::error("[FACTURA #$idx] ERROR: No se pudo mover el archivo de {$ruta_temp} a {$upload_path}/{$nombre_archivo_final}");
                } else {
                    \Log::info("[FACTURA #$idx] Archivo XML movido a: {$upload_path}/{$nombre_archivo_final}");
                }

            } catch (Exception $e) {
                \Log::error("ERROR AL PROCESAR FACTURA {$factura['uuid']}: " . $e->getMessage());

                $resultados_procesamiento[] = [
                    'uuid' => $factura['uuid'],
                    'tipo_error' => 'general',
                    'mensaje' => $e->getMessage()
                ];
            }
        } // Fin foreach

        \Log::info('==== [FIN] Guardado masivo de facturas ====');

        $message = "Se procesaron " . count($facturas) . " facturas. " . ($guardadas > 0 ? "Se guardaron $guardadas con éxito." : "No se guardó ninguna factura.");

        // <--- CAMBIO CLAVE: Usa $this->response() y confía en la negociación de contenido de FuelPHP --->
        // Ahora que el frontend envía 'Accept: application/json', esto debería funcionar.
        return $this->response([
            'success' => $guardadas > 0,
            'mensaje' => $message,
            'detalles' => $resultados_procesamiento
        ]);
    }






public function action_ajax_crear_proveedor()
{
    \Log::info('[AJAX] Entrando a alta rápida de proveedor vía XML');

    try {


        // --- CAPTURA LOS DATOS RECIBIDOS ---
        $rfc    = strtoupper(trim(Input::post('rfc')));
        $nombre = trim(Input::post('nombre'));
        $email  = trim(Input::post('email'));

        \Log::info("[AJAX] Recibido RFC: $rfc, Nombre: $nombre, Email: $email");

        // --- VALIDA DATOS OBLIGATORIOS ---
        if (empty($rfc) || empty($nombre) || empty($email)) {
            \Log::error("[AJAX] Faltan campos obligatorios al crear proveedor: RFC=$rfc, Nombre=$nombre, Email=$email");
            throw new Exception('Todos los campos son obligatorios.');
        }

        // --- ¿YA EXISTE EL PROVEEDOR? ---
        $provider = Model_Provider::query()->where('rfc', $rfc)->get_one();
        if ($provider) {
            \Log::info("[AJAX] Proveedor ya existe. ID: {$provider->id}");
            return $this->response(['success' => true, 'mensaje' => 'Ya existe el proveedor', 'provider_id' => $provider->id]);
        }

        // --- ¿YA EXISTE EL USUARIO? ---
        $user = Model_User::query()->where('username', $rfc)->get_one();
        if (!$user) {
            \Log::info("[AJAX] Usuario NO existe. Procediendo a crear...");
            $user_id = Auth::instance()->create_user(
                $rfc,           // usuario (username)
                $rfc,           // password (RFC como contraseña)
                $email,         // email capturado en el modal
                10,             // grupo proveedor
                [
                    'connected' => false,
                    'banned'    => true, // NUEVO PROVEEDOR QUEDA BANEADO hasta validación
                ]
            );
            if (!$user_id) {
                \Log::error("[AJAX] No se pudo crear el usuario.");
                throw new Exception('No se pudo crear el usuario.');
            }
            $user = Model_User::find($user_id);
            \Log::info("[AJAX] Usuario creado OK, ID: $user_id");
        } else {
            \Log::info("[AJAX] Usuario YA existe. ID: {$user->id}");
        }

        // --- CREAR PROVEEDOR ASOCIADO ---
        $provider = new Model_Provider([
            'user_id'          => $user->id,
            'name'             => strtoupper($nombre),
            'rfc'              => $rfc,
            'employee_id'      => 0,
            'payment_terms_id' => 0,
            'code_sap'         => '',           // VACÍO, solo si tienes ese dato en XML
            'require_purchase' => 0,
            'created_at'       => time(),
        ]);
        if (!$provider->save()) {
            \Log::error("[AJAX] Error al guardar el proveedor en la BD.");
            throw new Exception('No se pudo guardar el proveedor en la base de datos.');
        }
        \Log::info("[AJAX] Proveedor guardado OK, ID: {$provider->id}");

        // --- CREAR CARPETA DE ARCHIVOS ---
        $upload_path = DOCROOT . 'assets/facturas/proveedores/' . $provider->id;
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
            \Log::info("[AJAX] Carpeta creada: $upload_path");
        }

        // --- RESPUESTA EXITOSA ---
        return $this->response([
            'success'     => true,
            'mensaje'     => 'Proveedor creado correctamente.',
            'provider_id' => $provider->id
        ]);
    } catch (Exception $e) {
        \Log::error("[AJAX] ERROR: " . $e->getMessage());
        return $this->response([
            'success' => false,
            'mensaje' => $e->getMessage()
        ]);
    }
}




////////////////////////////////////////////////////////
// TERMINA ORDENES DE COMPRA
// TODO LO QUE SEA DE ORDENES DE COMPRAS, FACTURAS, NOTAS DE CREDITO, REP QUE SEA PROCESSO PARA COMPRAS DEBE IR DENTRO DE ESTOS MARCADORES
////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////FUNCIONES PARA PLATAFORMAS DE VENTA EJEMPLO ML AMAZON ETC///////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * AJAX – OBTENER ATRIBUTOS OFICIALES DE ML PARA UNA CATEGORÍA
 */
/**
 * AJAX – OBTENER ATRIBUTOS OFICIALES DE ML PARA UNA CATEGORÍA (DESDE BD LOCAL)
 *
 * Usado por: assets/js/admin/plataformas/ml/ml-attributes.js
 */
public function action_get_category_attributes_ml()
{
    if (!Input::is_ajax()) {
        return Response::forge(json_encode([
            'success' => false,
            'msg'     => 'Acceso no permitido'
        ]), 400)->set_header('Content-Type', 'application/json');
    }

    // Soporte Axios
    $post = Input::post();
    if (empty($post)) {
        $post = json_decode(file_get_contents('php://input'), true) ?: [];
    }

    $config_id   = (int) Arr::get($post, 'config_id');
    $product_id  = (int) Arr::get($post, 'product_id');
    $category_id = Arr::get($post, 'category_id');

    if (!$config_id || !$product_id || !$category_id) {
        return Response::forge(json_encode([
            'success' => false,
            'msg'     => 'Parámetros incompletos (config_id, product_id, category_id)'
        ]), 400)->set_header('Content-Type', 'application/json');
    }

    // Config
    $config = Model_Plataforma_Ml_Configuration::find($config_id);
    if (!$config) {
        return Response::forge(json_encode([
            'success' => false,
            'msg'     => 'Configuración ML no encontrada'
        ]), 404)->set_header('Content-Type', 'application/json');
    }

    // ---------------------------------------------------------
    // 1. Atributos por categoría
    // ---------------------------------------------------------
    $raw_attrs = Model_Plataforma_Ml_Category_Attribute::query()
        ->where('category_id', $category_id)
        ->order_by('is_required', 'desc')   // primero requeridos
        ->order_by('is_catalog_required', 'desc')
        ->order_by('name', 'asc')
        ->get();

    if (!$raw_attrs) {
        return Response::forge(json_encode([
            'success' => true,
            'attributes' => [],
            'values'     => [],
            'msg'        => 'No hay atributos. Ejecuta sincronización.'
        ]), 200)->set_header('Content-Type', 'application/json');
    }

    // ---------------------------------------------------------
    // 2. Valores del producto
    // ---------------------------------------------------------
    $raw_values = Model_Plataforma_Ml_Product_Attribute::query()
        ->where('ml_product_id', $product_id)
        ->get();

    $values = [];
    foreach ($raw_values as $v) {
    $values[$v->category_attribute_id] = $v->value_name;
}


    // ---------------------------------------------------------
    // 3. Construcción final para Vue
    // ---------------------------------------------------------
    $attributes = [];

    foreach ($raw_attrs as $attr) {

        // valores del catálogo
        $catalog = Model_Plataforma_Ml_Attribute_Value::query()
            ->where('category_attribute_id', $attr->id)
            ->order_by('name', 'asc')
            ->get();

        $val_list = array_map(function ($v) {
            return [
                'ml_value_id' => $v->ml_value_id,
                'name'        => $v->name
            ];
        }, $catalog);

        $attributes[] = [
            'category_attribute_id' => $attr->id,
            'ml_attribute_id'       => $attr->ml_attribute_id,
            'name'                  => $attr->name,
            'value_type'            => $attr->value_type,
            'is_required'           => (int) $attr->is_required,
            'is_catalog_required'   => (int) $attr->is_catalog_required,
            'is_variation'          => (int) $attr->is_variation,
            'values'                => $val_list
        ];
    }

    return Response::forge(json_encode([
        'success'    => true,
        'attributes' => $attributes,
        'values'     => $values
    ]), 200)->set_header('Content-Type', 'application/json');
}


/**
 * ===============================================================
 * SINCRONIZACIÓN DE ATRIBUTOS Y VALORES DE CATEGORÍA ML
 * ERP SAJOR – IMPLEMENTACIÓN ENTERPRISE
 * ===============================================================
 *
 * @param Model_Plataforma_Ml_Configuration $config
 * @param string $category_id
 * @return array
 * @throws \Exception
 */
protected function sync_ml_category_attributes($config, $category_id)
{
    \Log::info("[ML][SYNC] Iniciando sync de atributos para category_id={$category_id}");

    // =====================================================
    // 1. Obtener ACCESS TOKEN actual
    // =====================================================
    $token = $config->access_token;
    if (!$token) {
        throw new \Exception("No hay access_token en la configuración ML.");
    }

    // =====================================================
    // 2. Consumir API oficial ML
    // =====================================================
    $url = "https://api.mercadolibre.com/categories/{$category_id}/attributes";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Bearer {$token}",
            "Content-Type: application/json"
        ],
        CURLOPT_TIMEOUT        => 30
    ]);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        throw new \Exception("Error CURL: " . curl_error($ch));
    }

    curl_close($ch);

    if ($httpcode != 200) {
        throw new \Exception("ML devolvió HTTP {$httpcode}: {$response}");
    }

    $attrs = json_decode($response, true);

    if (!is_array($attrs)) {
        throw new \Exception("Respuesta ML inválida para attributes.");
    }

    \Log::info("[ML][SYNC] Total atributos recibidos: " . count($attrs));

    // =====================================================
    // 3. Preparar contadores
    // =====================================================
    $attrs_synced  = 0;
    $values_synced = 0;

    // =====================================================
    // 4. Procesar cada atributo recibido
    // =====================================================
    foreach ($attrs as $a)
    {
        $ml_attribute_id = $a['id'];

        // Buscar si ya existe
        $cat_attr = \Model_Plataforma_Ml_Category_Attribute::query()
            ->where('category_id', $category_id)
            ->where('ml_attribute_id', $ml_attribute_id)
            ->get_one();

        if (!$cat_attr) {
            $cat_attr = new \Model_Plataforma_Ml_Category_Attribute();
            $cat_attr->category_id = $category_id;
            $cat_attr->ml_attribute_id = $ml_attribute_id;
        }

        // Guardar información principal
        $cat_attr->name               = $a['name'] ?? '';
        $cat_attr->value_type         = $a['value_type'] ?? 'string';
        $cat_attr->is_required        = !empty($a['tags']['required']);
        $cat_attr->is_catalog_required= !empty($a['tags']['catalog_required']);
        $cat_attr->is_variation       = !empty($a['tags']['variation_attribute']);
        $cat_attr->raw_tags           = json_encode($a['tags'] ?? []);
        $cat_attr->raw_json           = json_encode($a);

        $cat_attr->updated_at = time();

        if (!$cat_attr->id) {
            $cat_attr->created_at = time();
        }

        $cat_attr->save();
        $attrs_synced++;

        // =====================================================
        // 5. Procesar valores (si existen)
        // =====================================================
        if (!empty($a['values']) && is_array($a['values'])) {

            foreach ($a['values'] as $v)
            {
                $ml_value_id = $v['id'] ?? null;
                if (!$ml_value_id) continue;

                $cat_value = \Model_Plataforma_Ml_Attribute_Value::query()
                    ->where('category_attribute_id', $cat_attr->id)
                    ->where('ml_value_id', $ml_value_id)
                    ->get_one();

                if (!$cat_value) {
                    $cat_value = new \Model_Plataforma_Ml_Attribute_Value();
                    $cat_value->category_attribute_id = $cat_attr->id;
                    $cat_value->ml_value_id = $ml_value_id;
                    $cat_value->created_at = time();
                }

                $cat_value->name     = $v['name'] ?? '';
                $cat_value->raw_json = json_encode($v);
                $cat_value->updated_at = time();

                $cat_value->save();
                $values_synced++;
            }
        }
    }

    \Log::info("[ML][SYNC] OK -> {$attrs_synced} atributos, {$values_synced} valores.");

    return [
        'attrs_synced'  => $attrs_synced,
        'values_synced' => $values_synced
    ];
}


/**
 * AJAX – SINCRONIZAR ATRIBUTOS OFICIALES DE ML PARA UNA CATEGORÍA
 *
 * Espera:
 *  - config_id   (int)
 *  - category_id (string)  Ej. 'MLM416632'
 */
public function action_sync_category_attributes_ml()
{
    if ( ! \Input::is_ajax())
    {
        return \Response::forge(json_encode(array(
            'success' => false,
            'msg'     => 'Acceso no permitido',
        )), 400)->set_header('Content-Type', 'application/json');
    }

    // Soporte Axios: Input::post() + php://input
    $post = \Input::post();
    if (empty($post))
    {
        $post = json_decode(file_get_contents('php://input'), true) ?: array();
    }

    $config_id   = (int) \Arr::get($post, 'config_id');
    $category_id = (string) \Arr::get($post, 'category_id');

    if ( ! $config_id || ! $category_id)
    {
        return \Response::forge(json_encode(array(
            'success' => false,
            'msg'     => 'Parámetros incompletos (config_id, category_id)',
        )), 400)->set_header('Content-Type', 'application/json');
    }

    $config = Model_Plataforma_Ml_Configuration::find($config_id);
    if ( ! $config)
    {
        return \Response::forge(json_encode(array(
            'success' => false,
            'msg'     => 'Configuración ML no encontrada',
        )), 404)->set_header('Content-Type', 'application/json');
    }

    try
    {
        $result = $this->sync_ml_category_attributes($config, $category_id);

        return \Response::forge(json_encode(array(
            'success'       => true,
            'msg'           => 'Sincronización completada correctamente',
            'attrs_synced'  => (int) $result['attrs_synced'],
            'values_synced' => (int) $result['values_synced'],
        )), 200)->set_header('Content-Type', 'application/json');
    }
    catch (\Exception $e)
    {
        \Log::error('[ML][SYNC] Error: '.$e->getMessage());

        return \Response::forge(json_encode(array(
            'success' => false,
            'msg'     => 'Error al sincronizar atributos: '.$e->getMessage(),
        )), 500)->set_header('Content-Type', 'application/json');
    }
}







/**
 * AJAX – GUARDAR ATRIBUTOS ML DEL PRODUCTO
 */
/**
 * AJAX – GUARDAR UN ATRIBUTO ML DEL PRODUCTO
 *
 * Usado por: assets/js/admin/plataformas/ml/ml-attributes.js
 */
public function action_save_product_attributes_ml()
{
    \Log::info("[ML][SAVE] === INICIO ===");

    if ( ! Input::is_ajax()) {
        \Log::error("[ML][SAVE] Acceso no permitido (no AJAX)");
        return Response::forge(json_encode([
            'success' => false,
            'msg' => 'Acceso no permitido'
        ]), 400)->set_header('Content-Type', 'application/json');
    }

    // =====================================================================
    // LECTURA POST
    // =====================================================================
    $post = Input::post();
    if (empty($post)) {
        $post = json_decode(file_get_contents('php://input'), true) ?: [];
    }

    \Log::info("[ML][SAVE] POST RECIBIDO: " . json_encode($post));

    $config_id   = (int) Arr::get($post, 'config_id');
    $product_id  = (int) Arr::get($post, 'product_id');
    $category_id = Arr::get($post, 'category_id');
    $attr_ml_id  = Arr::get($post, 'attribute_id');
    $value       = Arr::get($post, 'value');

    \Log::info("[ML][SAVE] PARAMS => config_id={$config_id}, product_id={$product_id}, category_id={$category_id}, attr_ml_id={$attr_ml_id}, value=" . json_encode($value));

    // Validación detallada de qué falta
if (!$config_id)  \Log::error("[ML][SAVE] FALTA -> config_id");
if (!$product_id) \Log::error("[ML][SAVE] FALTA -> product_id");
if (!$category_id)\Log::error("[ML][SAVE] FALTA -> category_id");
if (!$attr_ml_id) \Log::error("[ML][SAVE] FALTA -> attribute_id");

if (!$config_id || !$product_id || !$category_id || !$attr_ml_id) {
    \Log::error("[ML][SAVE] ERROR → parámetros incompletos (ver líneas anteriores)");
    return Response::forge(json_encode([
        'success' => false,
        'msg' => 'Parámetros incompletos'
    ]), 400)->set_header('Content-Type', 'application/json');
}


    $value_name = trim((string) $value);

    // =====================================================================
    // 1. BUSCAR ATRIBUTO LOCAL
    // =====================================================================
    \Log::info("[ML][SAVE] Buscando atributo category_id={$category_id}, ml_attribute_id={$attr_ml_id}");

    $catAttr = Model_Plataforma_Ml_Category_Attribute::query()
        ->where('category_id', $category_id)
        ->where('ml_attribute_id', $attr_ml_id)
        ->get_one();

    if (!$catAttr) {
        \Log::error("[ML][SAVE] ERROR → No existe el atributo localmente.");
        return Response::forge(json_encode([
            'success' => false,
            'msg' => "Atributo ML '{$attr_ml_id}' no encontrado en categoría {$category_id}"
        ]), 404)->set_header('Content-Type', 'application/json');
    }

    \Log::info("[ML][SAVE] category_attribute encontrado: ID={$catAttr->id}, value_type={$catAttr->value_type}");

    // =====================================================================
    // 2. MANEJO DE LISTA DE CATÁLOGO
    // =====================================================================
    $value_id = null;

    if ($catAttr->value_type === 'list') {

        \Log::info("[ML][SAVE] Atributo es LIST, buscando valor ml_value_id={$value}");

        $catValue = Model_Plataforma_Ml_Attribute_Value::query()
            ->where('category_attribute_id', $catAttr->id)
            ->where('ml_value_id', $value)
            ->get_one();

        if ($catValue) {
            \Log::info("[ML][SAVE] Valor de catálogo encontrado: {$catValue->name} ({$catValue->ml_value_id})");
            $value_id   = $catValue->ml_value_id;
            $value_name = $catValue->name;
        } else {
            \Log::warning("[ML][SAVE] No se encontró valor de catálogo ML para ml_value_id={$value}");
        }
    }

    // =====================================================================
    // 3. GUARDAR product_attribute
    // =====================================================================
    \Log::info("[ML][SAVE] Buscando registro producto_atributo: ml_product_id={$product_id}, category_attribute_id={$catAttr->id}");

    $productAttr = Model_Plataforma_Ml_Product_Attribute::query()
        ->where('ml_product_id', $product_id)
        ->where('category_attribute_id', $catAttr->id)
        ->get_one();

    if (!$productAttr) {
        \Log::info("[ML][SAVE] No existía, se creará nuevo atributo.");

        $productAttr = Model_Plataforma_Ml_Product_Attribute::forge([
            'ml_product_id'         => $product_id,
            'category_attribute_id' => $catAttr->id,
            'ml_value_id'           => $value_id,
            'value_name'            => $value_name,
			'source'                => $value_id ? 'catalog' : 'manual', // ✔ default seguro
            'created_at'            => time(),
            'updated_at'            => time(),
        ]);
    } else {
        \Log::info("[ML][SAVE] Actualizando registro existente ID={$productAttr->id}");

        $productAttr->ml_value_id = $value_id;
        $productAttr->value_name  = $value_name;
		$productAttr->source      = $value_id ? 'catalog' : 'manual'; // ✔ actualización segura
        $productAttr->updated_at  = time();
    }

    try {
        $productAttr->save();
        \Log::info("[ML][SAVE] Guardado correctamente.");
    } catch (Exception $e) {
        \Log::error("[ML][SAVE] ERROR AL GUARDAR: " . $e->getMessage());
        return Response::forge(json_encode([
            'success' => false,
            'msg' => 'Error al guardar: ' . $e->getMessage()
        ]), 500)->set_header('Content-Type', 'application/json');
    }

    return Response::forge(json_encode([
        'success' => true,
        'msg'     => 'Atributo guardado correctamente',
        'data'    => [
            'attribute_id' => $attr_ml_id,
            'value_name'   => $value_name,
            'value_id'     => $value_id
        ]
    ]), 200)->set_header('Content-Type', 'application/json');
}


/**
 * ===========================================================
 *  AJAX → Obtener HTML de Plantilla ML
 *  Logs extendidos corporativos para diagnóstico
 * ===========================================================
 */
public function action_get_ml_template_html()
{
    \Log::info("[ML][TPL][REQ] === INICIO ===");

    // ---------------------------------------------------------
    // Validar AJAX
    // ---------------------------------------------------------
    if (!Input::is_ajax()) {
        \Log::error("[ML][TPL][REQ] No es AJAX → método bloqueado.");
        return $this->response([
            'success' => false,
            'msg' => 'Acceso no permitido'
        ], 400);
    }

    // ---------------------------------------------------------
    // Leer POST
    // ---------------------------------------------------------
    $post = Input::post();

    if (empty($post)) {
        \Log::warning("[ML][TPL][REQ] POST vacío → intentando php://input");
        $post = json_decode(file_get_contents('php://input'), true) ?: [];
    }

    \Log::info("[ML][TPL][REQ] POST RECIBIDO: " . json_encode($post));

    $template_id = (int) ($post['template_id'] ?? 0);
    $access_id   = $post['access_id'] ?? null;
    $access_token = $post['access_token'] ?? null;

    \Log::info("[ML][TPL][REQ] PARAMS => template_id={$template_id}, access_id={$access_id}, token=" . substr((string)$access_token,0,8) . "...");

    // ---------------------------------------------------------
    // Validaciones
    // ---------------------------------------------------------
    if (!$template_id) {
        \Log::error("[ML][TPL][REQ] ERROR → template_id vacío o inválido");
        return $this->response([
            'success' => false,
            'msg' => 'ID de plantilla inválido'
        ], 400);
    }

    // ---------------------------------------------------------
    // Buscar plantilla
    // ---------------------------------------------------------
    \Log::info("[ML][TPL][REQ] Buscando plantilla ID={$template_id}");

    $tpl = Model_Plataforma_Ml_Description_Template::find($template_id);

    if (!$tpl) {
        \Log::error("[ML][TPL][REQ] ERROR → Plantilla no encontrada en DB");
        return $this->response([
            'success' => false,
            'msg' => 'Plantilla no encontrada'
        ], 404);
    }

    if ($tpl->deleted == 1) {
        \Log::error("[ML][TPL][REQ] ERROR → Plantilla eliminada (deleted=1)");
        return $this->response([
            'success' => false,
            'msg' => 'Plantilla eliminada'
        ], 404);
    }

    \Log::info("[ML][TPL][REQ] OK → Plantilla encontrada: '{$tpl->name}' (len=" . strlen($tpl->description_html) . ")");

    // ---------------------------------------------------------
    // Enviar respuesta
    // ---------------------------------------------------------
    \Log::info("[ML][TPL][REQ] === FIN OK ===");

    return $this->response([
        'success' => true,
        'html' => $tpl->description_html,
        'name' => $tpl->name
    ], 200);
}


 /**
 * ===========================================================
 *  MERCADO LIBRE – IMÁGENES DE PRODUCTO
 *  Controller: admin/ajax
 *  Endpoints oficiales (Axios + FuelPHP):
 *      admin/ajax/get_images_ml
 *      admin/ajax/add_image_ml
 *      admin/ajax/update_image_ml
 * ===========================================================
 */


/**
 * ===========================================================
 * GET IMAGES – Lista de imágenes ML del producto
 * ===========================================================
 */
/**
 * =======================================================
 * GET IMAGES (POST) – lista de imágenes ML del producto
 * =======================================================
 */
public function post_get_images_ml()
{
    \Log::info("[ML][IMG][get_images_ml] === INICIO ===");

    if (!$this->validate_ajax()) {
        return $this->response(['success' => false, 'msg' => 'Acceso no permitido'], 400);
    }

    // Leer POST (Axios JSON o form-data)
    $post = \Input::post();
    if (empty($post)) {
        $post = json_decode(file_get_contents("php://input"), true) ?: [];
    }

    \Log::info("[ML][IMG][get_images_ml] POST RECIBIDO: " . json_encode($post));

    $ml_product_id = (int) \Arr::get($post, 'ml_product_id');

    if (!$ml_product_id) {
        \Log::error("[ML][IMG][get_images_ml] ERROR: falta ml_product_id");
        return $this->response(['success' => false, 'msg' => 'Falta ml_product_id'], 400);
    }

    // Obtener imágenes
    $rows = \Model_Plataforma_Ml_Product_Image::query()
        ->where('ml_product_id', $ml_product_id)
        ->order_by('is_primary', 'desc')
        ->order_by('sort_order', 'asc')
        ->get();

    $images     = [];
    $primary_id = null;

    foreach ($rows as $r) {
        $images[] = [
            'id'         => $r->id,
            'url'        => $r->url,
            'is_primary' => (int) $r->is_primary,
            'sort_order' => (int) $r->sort_order,
        ];

        if ($r->is_primary == 1 && $primary_id === null) {
            $primary_id = $r->id;
        }
    }

    \Log::info("[ML][IMG][get_images_ml] Imágenes encontradas: " . count($images));

    return $this->response([
        'success'    => true,
        'images'     => $images,
        'primary_id' => $primary_id,
    ]);
}


/**
 * ===========================================================
 * ADD IMAGE – Agrega imagen ML por URL
 * ===========================================================
 */
public function post_add_image_ml()
{
    \Log::info("[ML][IMG][add_image_ml] === INICIO ===");

    if (!$this->validate_ajax()) {
        \Log::error("[ML][IMG][add_image_ml] Acceso no permitido");
        return $this->response(['success' => false, 'msg' => 'Acceso no permitido'], 400);
    }

    // Fuel + Axios fix
    $post = Input::post();
    if (empty($post)) {
        $post = json_decode(file_get_contents("php://input"), true) ?: [];
    }

    \Log::info("[ML][IMG][add_image_ml] POST RECIBIDO: " . json_encode($post));

    $ml_product_id = (int) Arr::get($post, 'ml_product_id');
    $url           = trim(Arr::get($post, 'url'));

    if (!$ml_product_id || !$url) {
        \Log::error("[ML][IMG][add_image_ml] ERROR: Parámetros incompletos");
        return $this->response(['success' => false, 'msg' => 'Parámetros incompletos'], 400);
    }

    // Guardar
    try {
        $img = Model_Plataforma_Ml_Product_Image::forge([
            'ml_product_id' => $ml_product_id,
            'url'           => $url,
            'is_primary'    => 0,
            'sort_order'    => 999,
            'created_at'    => time(),
            'updated_at'    => time()
        ]);

        $img->save();

        \Log::info("[ML][IMG][add_image_ml] Imagen agregada ID={$img->id}");

    } catch (Exception $e) {
        \Log::error("[ML][IMG][add_image_ml] ERROR AL GUARDAR: " . $e->getMessage());
        return $this->response(['success' => false, 'msg' => 'Error al guardar imagen'], 500);
    }

    return $this->response(['success' => true]);
}



/**
 * ===========================================================
 * UPDATE IMAGE – cambiar principal y/o orden
 * ===========================================================
 */
public function post_update_image_ml()
{
    \Log::info("[ML][IMG][update_image_ml] === INICIO ===");

    if (!$this->validate_ajax()) {
        \Log::error("[ML][IMG][update_image_ml] Acceso no permitido");
        return $this->response(['success'=>false,'msg'=>'Acceso no permitido'], 400);
    }

    // Fuel + Axios fix
    $post = Input::post();
    if (empty($post)) {
        $post = json_decode(file_get_contents("php://input"), true) ?: [];
    }

    \Log::info("[ML][IMG][update_image_ml] POST RECIBIDO: " . json_encode($post));

    $id         = (int) Arr::get($post, 'id');
    $is_primary = (int) Arr::get($post, 'is_primary');
    $sort_order = (int) Arr::get($post, 'sort_order');

    if (!$id) {
        \Log::error("[ML][IMG][update_image_ml] ERROR: Falta id");
        return $this->response(['success'=>false,'msg'=>'Falta id'], 400);
    }

    $img = Model_Plataforma_Ml_Product_Image::find($id);

    if (!$img) {
        \Log::error("[ML][IMG][update_image_ml] ERROR: Imagen no encontrada ID=$id");
        return $this->response(['success'=>false,'msg'=>'Imagen no encontrada'], 404);
    }

    // Si se marca como principal, desmarcar todas las demás
    if ($is_primary == 1) {
        \DB::update('plataforma_ml_products_images')
            ->set(['is_primary' => 0])
            ->where('ml_product_id', $img->ml_product_id)
            ->execute();
    }

    $img->is_primary = ($is_primary == 1 ? 1 : 0);
    $img->sort_order = $sort_order;
    $img->updated_at = time();

    try {
        $img->save();
        \Log::info("[ML][IMG][update_image_ml] Imagen actualizada correctamente ID=$id");
    } catch (Exception $e) {
        \Log::error("[ML][IMG][update_image_ml] ERROR AL GUARDAR: " . $e->getMessage());
        return $this->response(['success'=>false,'msg'=>'Error al guardar imagen'], 500);
    }

    return $this->response(['success'=>true]);
}

/**
 * =======================================================
 * UPLOAD IMAGE ML (POST)
 * =======================================================
 */
/**
 * =======================================================
 * UPLOAD IMAGE (POST) – subir archivo local para ML
 * =======================================================
 */
public function post_upload_image_ml()
{
    \Log::info("[ML][IMG][upload_image_ml] === INICIO ===");

    if (!$this->validate_ajax()) {
        return $this->response(['success' => false, 'msg' => 'Acceso no permitido'], 400);
    }

    // POST normal (ml_product_id, access_id, access_token ya validados por validate_ajax)
    $ml_product_id = (int) \Input::post('ml_product_id', 0);

    if (!$ml_product_id) {
        \Log::error("[ML][IMG][upload_image_ml] ERROR: falta ml_product_id");
        return $this->response(['success' => false, 'msg' => 'Falta ml_product_id'], 400);
    }

    if (empty($_FILES['file']['name'])) {
        \Log::error("[ML][IMG][upload_image_ml] ERROR: no se envió archivo");
        return $this->response(['success' => false, 'msg' => 'No se envió ninguna imagen'], 400);
    }

    // Config upload (similar a CKEditor)
    $config = [
        'auto_process'        => false,
        'path'                => DOCROOT . 'assets/uploads/plataformas/uploads_ml',
        'randomize'           => false,
        'auto_rename'         => true,
        'normalize'           => true,
        'normalize_separator' => '-',
        'ext_whitelist'       => ['jpg', 'jpeg', 'png', 'gif'],
        'max_size'            => 20971520, // 20MB
    ];

    \Upload::process($config);

    if (!\Upload::is_valid()) {
        \Log::error("[ML][IMG][upload_image_ml] ERROR: archivo no válido");
        return $this->response([
            'success' => false,
            'msg'     => 'Solo están permitidas imágenes .jpg, .jpeg, .png y .gif.'
        ], 400);
    }

    \Upload::save();
    $files = \Upload::get_files();

    if (empty($files)) {
        \Log::error("[ML][IMG][upload_image_ml] ERROR: no se pudo guardar archivo");
        return $this->response(['success' => false, 'msg' => 'No se pudo guardar la imagen'], 500);
    }

    $saved = $files[0];
    $file  = $saved['saved_as']; // nombre final
    $path  = DOCROOT . 'assets/uploads/plataformas/uploads_ml' . DIRECTORY_SEPARATOR . $file;

    \Log::info("[ML][IMG][upload_image_ml] Archivo subido: " . $file);

    // ===================================================
    // OPCIONAL: aplicar watermark ML (logo SAJOR/SAIRA)
    // ===================================================
    try {
        $watermark_file = DOCROOT . 'assets/img/watermarks/ml-watermark.png';
        if (file_exists($watermark_file)) {
            \Image::load($path)
                ->watermark($watermark_file, 'center', 5) // ajusta posición/padding según tu config
                ->save($path);
            \Log::info("[ML][IMG][upload_image_ml] Watermark aplicado");
        } else {
            \Log::info("[ML][IMG][upload_image_ml] Watermark no aplicado: archivo no existe");
        }
    } catch (\Exception $e) {
        \Log::error("[ML][IMG][upload_image_ml] ERROR watermark: " . $e->getMessage());
        // No detenemos el flujo por watermark
    }

    // URL pública
    $url = \Uri::base(false) . 'assets/uploads/plataformas/uploads_ml' . $file;

    // Crear registro ML
    $img = \Model_Plataforma_Ml_Product_Image::forge([
        'ml_product_id' => $ml_product_id,
        'url'           => $url,
        'is_primary'    => 0,
        'sort_order'    => 999,
        'created_at'    => time(),
        'updated_at'    => time(),
    ]);

    try {
        $img->save();
        \Log::info("[ML][IMG][upload_image_ml] Imagen ML creada ID={$img->id}");
    } catch (\Exception $e) {
        \Log::error("[ML][IMG][upload_image_ml] ERROR al guardar registro ML: " . $e->getMessage());
        return $this->response(['success' => false, 'msg' => 'Error al registrar la imagen ML'], 500);
    }

    return $this->response(['success' => true]);
}


/**
 * =======================================================
 * DELETE IMAGE (POST) – eliminar imagen ML
 * =======================================================
 */
public function post_delete_image_ml()
{
    \Log::info("[ML][IMG][delete_image_ml] === INICIO ===");

    if (!$this->validate_ajax()) {
        return $this->response(['success' => false, 'msg' => 'Acceso no permitido'], 400);
    }

    $post = \Input::post();
    if (empty($post)) {
        $post = json_decode(file_get_contents("php://input"), true) ?: [];
    }

    \Log::info("[ML][IMG][delete_image_ml] POST: " . json_encode($post));

    $id = (int) \Arr::get($post, 'id', 0);

    if (!$id) {
        \Log::error("[ML][IMG][delete_image_ml] ERROR: falta id");
        return $this->response(['success' => false, 'msg' => 'Falta id'], 400);
    }

    $img = \Model_Plataforma_Ml_Product_Image::find($id);

    if (!$img) {
        \Log::error("[ML][IMG][delete_image_ml] ERROR: imagen no encontrada ID=$id");
        return $this->response(['success' => false, 'msg' => 'Imagen no encontrada'], 404);
    }

    // Intentar eliminar archivo solo si es URL local del sistema
    try {
        $base = \Uri::base(false) . 'assets/uploads/plataformas/uploads_ml/';
        if (strpos($img->url, $base) === 0) {
            $filename = substr($img->url, strlen($base));
            $filepath = DOCROOT . 'assets/uploads/plataformas/uploads_ml' . DIRECTORY_SEPARATOR . $filename;

            if (file_exists($filepath)) {
                \File::delete($filepath);
                \Log::info("[ML][IMG][delete_image_ml] Archivo físico eliminado: " . $filepath);
            }
        }
    } catch (\Exception $e) {
        \Log::error("[ML][IMG][delete_image_ml] ERROR al eliminar archivo físico: " . $e->getMessage());
    }

    try {
        $img->delete();
        \Log::info("[ML][IMG][delete_image_ml] Registro eliminado ID=$id");
    } catch (\Exception $e) {
        \Log::error("[ML][IMG][delete_image_ml] ERROR delete(): " . $e->getMessage());
        return $this->response(['success' => false, 'msg' => 'Error al eliminar imagen'], 500);
    }

    return $this->response(['success' => true]);
}


/**
 * =======================================================
 * REORDER IMAGES (POST) – drag & drop
 * =======================================================
 */
public function post_reorder_images_ml()
{
    \Log::info("[ML][IMG][reorder_images_ml] === INICIO ===");

    if (!$this->validate_ajax()) {
        return $this->response(['success' => false, 'msg' => 'Acceso no permitido'], 400);
    }

    $post = \Input::post();
    if (empty($post)) {
        $post = json_decode(file_get_contents("php://input"), true) ?: [];
    }

    \Log::info("[ML][IMG][reorder_images_ml] POST: " . json_encode($post));

    $images = \Arr::get($post, 'images', []);

    if (empty($images) || !is_array($images)) {
        \Log::error("[ML][IMG][reorder_images_ml] ERROR: arreglo images vacío");
        return $this->response(['success' => false, 'msg' => 'No hay datos de orden'], 400);
    }

    foreach ($images as $row) {
        $id         = (int) \Arr::get($row, 'id', 0);
        $sort_order = (int) \Arr::get($row, 'sort_order', 0);

        if (!$id) {
            continue;
        }

        $img = \Model_Plataforma_Ml_Product_Image::find($id);
        if (!$img) {
            continue;
        }

        $img->sort_order = $sort_order;
        $img->updated_at = time();
        try {
            $img->save();
        } catch (\Exception $e) {
            \Log::error("[ML][IMG][reorder_images_ml] ERROR ID=$id: " . $e->getMessage());
        }
    }

    return $this->response(['success' => true]);
}





////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////AQUI TERMINA TODO LO DE PLATAFORMAS DE VENTA////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////////////////////////
////////////////////////// FUNCIONES PRIVADAS PARA FECHAS Y TIEMPOS ETC/////////////////////
////////////////////////////////////////////////////////////////////////////////


	/**
	* DATE2UNIXTIME
	*
	* CONVIERTE UNA FECHA EN UNIXTIME
	*
	* @access  private
	* @return  Int
	*/
	private function date2unixtime($date = 0, $time = 0)
	{
		# Establece valores predeterminados si no se proporcionan
		$date = ($date != 0) ? $date : date('Y-m-d');
		$time = ($time != 0) ? $time : date('H:i');

		# Ajusta para manejar el formato ISO de la fecha
		$dateParts = explode('-', $date); // Año, Mes, Día
		$timeParts = explode(':', $time); // Hora, Minuto

		# Devuelve el UNIXTIME
		return mktime($timeParts[0], $timeParts[1], 0, $dateParts[1], $dateParts[2], $dateParts[0]);
	}


	/**
	* DATE2UNIXTIME2
	*
	* CONVIERTE UNA FECHA EN UNIXTIME
	*
	* @access  private
	* @return  Int
	*/
	private function date2unixtime2($date = 0)
	{
		# Establece valores predeterminados si no se proporcionan
		$date = ($date != 0) ? $date : date('d/m/Y');

		# Ajusta para manejar el formato ISO de la fecha
		$dateParts = explode('/', $date);

		# Devuelve el UNIXTIME
		return mktime(0, 0, 0, $dateParts[1], $dateParts[0], $dateParts[2]);
	}


	/**
	 * DATE2UNIXTIME
	 *
	 * CONVIERTE UNA FECHA EN UNIXTIME
	 *
	 * @access  private
	 * @return  Int
	 */
	private function caldate2unixtime($string = 0)
	{
		# SE CORTA LA CADENA POR LA LETRA T
		$string = explode('T', $string);

		# SE CORTA LA CADENA POR LOS GUIONES
		$date = explode('-', $string[0]);

		# SE CORTA LA CADENA POR LOS DOS PUNTOS
		$time = explode(':', $string[1]);

		# SE DEVUELVE EL UNIXTIME
		return mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
	}


	/**
 * =======================================================
 * VALIDAR AJAX ESTÁNDAR
 * =======================================================
 */
protected function validate_ajax()
{
    // Debe ser AJAX
    if (!\Input::is_ajax()) {
        \Log::error("[AJAX][VALIDATE] No es AJAX");
        return false;
    }

    // Leer POST (FuelPHP + Axios)
    $post = \Input::post();
    if (empty($post)) {
        $post = json_decode(file_get_contents("php://input"), true) ?: [];
    }

    // access_id
    $access_id = (int) \Arr::get($post, 'access_id');
    $access_token = trim((string) \Arr::get($post, 'access_token'));

    if (!$access_id || !$access_token) {
        \Log::error("[AJAX][VALIDATE] Faltan credenciales");
        return false;
    }

    // Buscar usuario
    $user = \Model_User::find($access_id);

    if (!$user) {
        \Log::error("[AJAX][VALIDATE] Usuario no encontrado ID={$access_id}");
        return false;
    }

    // Validar token generado
    $expected = md5($user->login_hash);

    if ($expected !== $access_token) {
        \Log::error("[AJAX][VALIDATE] Token inválido. Esperado={$expected} Enviado={$access_token}");
        return false;
    }

    return true;
}

}
