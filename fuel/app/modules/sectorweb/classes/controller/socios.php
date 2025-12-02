<?php

namespace sectorweb;

class Controller_Socios extends \Controller
{
	/**
	* BEFORE
	*
	*
	* @return Void
	*/
	public function before()
	{
		# SE HACE UNA INSTANCIA DE AUTH
		$this->auth = \Auth::instance();
	}


	/**
	* LOGIN
	*
	* LOGUEA A UN USUARIO
	*
	*
	* @access  public
	* @return  array
	*/
	public function action_login($username, $password, $rememberme)
	{
		# SI LA PETICION ES LLAMADA DESDE UN CONTROLADOR
		if(\Request::is_hmvc())
		{
			# SE INICIALIZAN LAS VARIABLES
			$response = 'error';

			# SE VERIFICA EL USUARIO Y LA CONTRASEÑA COTEJANDOLOS ANTE LA BD
			if($this->auth->login($username, $password))
			{
				# SI EL USUARIO ES VALIDO
				if(\Request::forge('sectorweb/socios/is_valid', false)->execute()->response->body)
				{
					# SE ACTUALIZA AL ESTADO CONECTADO
					$this->auth->update_user(array('connected' => true), $username);

					# SI SE QUIERE RECORDAR SESION
					if($rememberme == true)
					{
						# SE ESPECIFICA A AUTH QUE RECUERDE LA SESION
						$this->auth->remember_me();
					}
					else
					{
						# SE ESPECIFICA A AUTH QUE NO SE RECUERDE LA SESION
						$this->auth->dont_remember_me();
					}

					# SE INICIALIZA EL ARREGLO DE COTIZACION
					$quote = array();

					# SE INICIALIZA EL CARRITO DE COTIZACION
					\Session::set('quote', $quote);

					# SE ESTABLECE EL MENSAJE DE EXITO
					$response = 'ok';
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					$response = 'ivalid_user';
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				$response = 'invalid_credentials';
			}

			return $response;
		}
		else
		{
			# SE EJECUTA UN 404
			throw new HttpNotFoundException;
		}
	}


	/**
	* LOGOUT
	*
	* DESLOGUEA A UN USUARIO
	*
	*
	* @access  public
	* @return  array
	*/
	public function action_logout()
	{
		# SI LA PETICION ES LLAMADA DESDE UN CONTROLADOR
		if(\Request::is_hmvc())
		{
			# SE ACTUALIZA AL ESTADO DESCONECTADO
			$this->auth->update_user(array('connected' => false), $this->auth->get('username'));

			# SE DESLOGUEA AL USUARIO
			$this->auth->logout();

			# SE DESTRUYEN LAS SESIONES
			\Session::destroy();
		}
		else
		{
			# SE EJECUTA UN 404
			throw new HttpNotFoundException;
		}
	}


	/**
	* IS VALID
	*
	* DESLOGUEA A UN USUARIO
	*
	*
	* @access  public
	* @return  array
	*/
	public function action_is_valid()
	{
		# SI LA PETICION ES LLAMADA DESDE UN CONTROLADOR
		if(\Request::is_hmvc())
		{
			# SE INICIALIZAN LAS VARIABLES
			$response = false;

			# SI EXISTE UN USUARIO LOGEADO
			if($this->auth->check())
			{
				# SI EL USUARIO LOGEADO ES UN ADMINISTRADOR
				if($this->auth->member(15))
				{
					# SE OBTIENE EL CAMPO PROFILE FIELDS
					$profile_fields = $this->auth->get_profile_fields();

					# SE OBTIENE EL CAMPO BANNED DE PROFILED FIELDS
					$banned = $profile_fields['banned'];

					# SI EL USUARIO NO ESTA BANEADO
					if(!$banned)
					{
						# SE CAMBIA EL VALOR DE LA VARIABLE
						$response = true;
					}
					else
					{
						# SE EJECUTA EL MODULO DE DESLOGUEO
						\Request::forge('sectorweb/socios/logout', false)->execute();
					}
				}
				else
				{
					# SE EJECUTA EL MODULO DE DESLOGUEO
					\Request::forge('sectorweb/socios/logout', false)->execute();
				}
			}

			return $response;
		}
		else
		{
			# SE EJECUTA UN 404
			throw new HttpNotFoundException;
		}
	}

	/**
	* GET TYPE PARTNER
	*
	* OBTIENE EL TIPO DE CLIENTE
	*
	*
	* @access  public
	* @return  array
	*/
	public function action_get_type_partner()
	{
		# SI LA PETICION ES LLAMADA DESDE UN CONTROLADOR
		if(\Request::is_hmvc())
		{
			# SE BUSCA EL PARTNER
			$partner = \Model_Partner::get_one(array('id_user' => $this->auth->get('id')));

			# SI SE OBTIENE INFORMACION
			if(!empty($partner))
			{
				# SE ESTABLECE EL TIPO DE USUARIO
				$response = $partner->type_id;
			}
			else
			{
				# SE ESTABLECE EL TIPO DE USUARIO POR DEFAULT
				$response = 1;
			}

			# SE DEVUELVE LA RESPUESTA
			return $response;
		}
		else
		{
			# SE EJECUTA UN 404
			throw new HttpNotFoundException;
		}
	}
}
