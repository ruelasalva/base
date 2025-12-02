<?php

/**
 * CONTROLADOR ADMIN_PERFIL
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Perfil extends Controller_Admin
{
	/**
	 * INDEX
	 *
	 * PERMITE EDITAR EL PERFIL DEL PROPIO USUARIO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_index()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('email', 'password', 'full_name');
		$user_id = 0;

		# SE RECORRE CAMPO POR CAMPO
		foreach($fields as $field)
		{
			# SE CREAN LAS CLASES DEL CAMPO
			$classes[$field] = array (
				'form-group'   => null,
				'form-control' => null,
			);
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$user = Model_User::query()
		->where('id', Auth::get('id'))
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($user))
		{
			# SE ALMACENA EL ID DEL USUARIO
			$user_id = $user->id;

			# SE DESERIALIZAN LOS CAMPOS EXTRAS
			$status = unserialize($user->profile_fields);

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['username']  = $user->username;
			$data['email']     = $user->email;
			$data['full_name'] = $status['full_name'];
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('user');
			$val->add_callable('Rules');
			$val->add_field('username', 'usuario', 'required|valid_string[alpha,numeric]|min_length[3]|max_length[50]');
			$val->add_field('email', 'email', 'required|min_length[7]|max_length[255]|valid_email');
			$val->add_field('password', 'contraseña', 'min_length[6]|max_length[20]');
			$val->add_field('full_name', 'nombre completo', 'required|alphabetic_spaces|min_length[3]|max_length[255]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				try
				{
					# SE ESTEBLECE LA NUEVA INFORMACION
					$data_to_update = array(
						'email'     => $val->validated('email'),
						'full_name' => $val->validated('full_name')
					);

					# SE ACTUALIZA LA INFORMACION DEL USUARIO EN LA BASE DE DATOS
					$user_auth = Auth::instance()->update_user($data_to_update, $user->username);

					# PROCESAR CONSENTIMIENTOS (si vienen en POST)
					if (Input::post('consent')) {
						foreach (Input::post('consent') as $doc_id => $value) {
							Helper_Legal::register_consent($user_id, $doc_id, true);
						}
					}

					# PROCESAR COOKIES (si el formulario incluye switches)
					if (Input::post('cookies')) {
						Helper_Legal::update_cookies_preferences(Input::post('cookies'), $user_id);
					}

					# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
					if($user_auth)
					{
						# SE ESTABLECE EL MENSAJE DE EXITO
						Session::set_flash('success', 'Se actualizó la información de <b>'.$user->username.'</b> correctamente.');

						# SI HAY UN PASSWORD
						if($val->validated('password') != '')
						{
							# SE RESETEA LA CONTRASEÑA DEL USUARIO Y SE ALMACENA
							$new_password = Auth::reset_password($user->username);

							# SE CAMBIA LA CONTRASEÑA
							Auth::change_password($new_password, $val->validated('password'), $user->username);
						}

						# SE REDIRECCIONA AL USUARIO
						Response::redirect('admin/perfil');
					}
				}
				catch(\SimpleUserUpdateException $e)
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					Session::set_flash('error', 'El correo electrónico <b>'.$val->validated('email').'</b> ya está asociado a otra cuenta, por favor escribe una dirección de correo electrónico diferente.');

					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes['email']['form-group']   = 'has-danger';
					$classes['email']['form-control'] = 'is-invalid';

					# SI LA CONTRASEÑA ES VALIDA
					if($classes['password']['form-group'] == 'has-success')
					{
						# SE ESTABLECE EL VALOR DE LAS CLASES
						$classes['password']['form-group']   = null;
						$classes['password']['form-control'] = null;
					}

					# SE ALMACENA LA INFORMACION PARA LA VISTA
					$data['email']     = $val->validated('email');
					$data['full_name'] = $val->validated('full_name');
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifícalo.');

				# SE ALMACENA LOS ERRORES DETECTADOS
				$data['errors'] = $val->error();

				# SE RECORRE CLASE POR CLASE
				foreach($classes as $name => $class)
				{
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
					$classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
				}

				# SI LA CONTRASEÑA ES VALIDA
				if($classes['password']['form-group'] == 'has-success')
				{
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes['password']['form-group']   = null;
					$classes['password']['form-control'] = null;
				}

				# SE ALMACENA LA INFORMACION PARA LA VISTA
				$data['email']     = Input::post('email');
				$data['full_name'] = Input::post('full_name');
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']      = $user_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Mi perfil';
		$this->template->content = View::forge('admin/perfil/index', $data);
	}
}
