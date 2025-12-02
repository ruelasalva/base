<?php

namespace sectorweb;

class Controller_User extends \Controller
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
	* DESLOGUEA A UN USUARIO
	*
	*
	* @access  public
	* @return  array
	*/
	public function action_login($username, $password)
	{
		# SI LA PETICION ES LLAMADA DESDE UN CONTROLADOR
		if(\Request::is_hmvc())
		{
			# SE INICIALIZAN LAS VARIABLES
			$response = 'error';

			# SE VERIFICA EL USUARIO Y LA CONTRASEÑA COTEJANDOLOS ANTE LA BD
			if($this->auth->login(trim($username), trim($password)))
			{
				# SE ACTUALIZA AL ESTADO CONECTADO///
				$this->auth->update_user(array('connected' => true), $username);

				# SI EL USUARIO ES VALIDO
				if(\Request::forge('sectorweb/user/is_valid', false)->execute()->response->body)
				{
					# SE OBTIENE EL CUSTOMER
					$customer = \Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

					# SE OBTIENE EL ULTIMO PEDIDO NO ENVIADO
					$sale = \Model_Sale::get_last_order_not_sent($customer->id);

					# SI SE OBTUVO EL ULTIMO PEDIDO NO ENVIADO
					if(!empty($sale))
					{
						# SI EXISTE UNA SESION DEL CARRITO
						if(\Session::get('cart'))
						{
							# SE ELIMINA LA SESION DEL CARRITO ACTUAL
							\Session::delete('cart');
						}

						# SI LA ORDEN TIENE PRODUCTOS
						if(!empty($sale->products))
						{
							# SE RECORREN LOS PRODUCTOS POR PRODUCTO
							foreach($sale->products as $sale_product)
							{
								# SE CREA UN REGISTRO DEL PRODUCTO Y SU CANTIDAD
								$cart[$sale_product->product->id]['quantity'] = (int)$sale_product->quantity;
							}

							# SE ACTUALIZA EL CARRITO
							\Session::set('cart', $cart);
						}
					}
					# SI NO
					else
					{
						# SE CREA UNA ORDEN NO ENVIADA RELACIONADA AL CUSTOMER
						$sale = \Model_Sale::set_new_order_not_sent($customer->id);
					}

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
				# SI EL USUARIO LOGEADO ES UN USUARIO NORMAL
				if($this->auth->member(1))
				{
					# SE OBTIENE EL CAMPO PROFILE FIELDS
					$profile_fields = $this->auth->get_profile_fields();

					# SE OBTIENE EL CAMPO BANNED DE PROFILED FIELDS
					$banned = $profile_fields['banned'];

					# SI EL USUARIO NO ESTA BANEADO
					if(!$banned)
					{
						# SE BUSCA EL CUSTOMER
						$customer = \Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

						# SI NO SE OBTUVO LA INFORMACION
						if(!empty($customer))
						{
							# SE CAMBIA EL VALOR DE LA VARIABLE
							$response = true;
						}
						else
						{
							# SE EJECUTA EL MODULO DE DESLOGUEO
							\Request::forge('sectorweb/user/logout', false)->execute();
						}
					}
					else
					{
						# SE EJECUTA EL MODULO DE DESLOGUEO
						\Request::forge('sectorweb/user/logout', false)->execute();
					}
				}
				else
				{
					# SE EJECUTA EL MODULO DE DESLOGUEO
					\Request::forge('sectorweb/user/logout', false)->execute();
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
	* GET TYPE CUSTOMER
	*
	* OBTIENE EL TIPO DE CLIENTE
	*
	*
	* @access  public
	* @return  array
	*/
	public function action_get_type_customer()
	{
		# SI LA PETICION ES LLAMADA DESDE UN CONTROLADOR
		if(\Request::is_hmvc())
		{
			# SE BUSCA EL CUSTOMER
			$customer = \Model_Customer::get_one(array('id_user' => $this->auth->get('id')));

			# SI SE OBTIENE INFORMACION
			if(!empty($customer))
			{
				# SE ESTABLECE EL TIPO DE USUARIO
				$response = $customer->type_id;
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
