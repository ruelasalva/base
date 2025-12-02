<?php

/**
 * CONTROLADOR SOLICITUD PRECOTIZACION
 */
class Controller_Admin_Solicitud_Precotizacion extends Controller_Admin
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
        if (!Auth::check()) {
            Session::set_flash('error', 'Debes iniciar sesión.');
            Response::redirect('admin/login');
        }
    }


	/**
	 * INDEX
	 * MUESTRA LOS PRODUCTOS Y PROCESA POST DEL FORM (SI LA VISTA AÚN APUNTA AQUÍ)
	 */
	public function action_index()
	{
		# INICIALIZAR VARIABLES
		$data          = array();
		$quote         = array();
		$products_info = array();
		$partner_opts  = array();
		$classes       = array(); 


		# CARGAR SESION DE COTIZACION
		$quote = \Session::get('quote');

		# SI NO HAY PRODUCTOS, REDIRECT
		if (empty($quote))
		{
			\Session::set_flash('error', 'No existen productos en la precotización. Por favor agrega por lo menos uno.');
			\Response::redirect('admin/precotizacion');
			return;
		}

		# ARMAR LISTA DE PRODUCTOS PARA LA VISTA
		foreach ($quote as $id => $array)
		{
			$product_quote = \Model_Product::find($id);
			if ($product_quote)
			{
				$products_info[] = array(
					'id'          => $product_quote->id,
					'slug'        => $product_quote->slug,
					'name'        => $product_quote->name,
					'description' => $product_quote->description,
					'image'       => $product_quote->image,
					'quantity'    => $array['quantity'] ?? 0,
				);
			}
			else
			{
				\Log::warning('[PRECOTIZACION][INDEX] PRODUCTO NO ENCONTRADO id='.$id);
			}
		}

		# SI LLEGA POST, PROCESAR GUARDADO (TRADUCIENDO partners.id -> partners.user_id)
		if (\Input::method() === 'POST')
		{
			\Log::debug('[PRECOTIZACION][INDEX] POST='.json_encode(\Input::post(), JSON_UNESCAPED_UNICODE));

			$val = \Validation::forge('quote');
			$val->add_field('partner', 'socio', 'required|valid_string[numeric]|numeric_min[1]');

			if ($val->run())
			{
				$partner_id_post = (int) $val->validated('partner'); # LLEGA partners.id
				\Log::debug('[PRECOTIZACION][INDEX] PARTNER POST partners.id='.$partner_id_post);

				$partner = \Model_Partner::find($partner_id_post);
				if (!$partner || !$partner->user_id)
				{
					\Log::warning('[PRECOTIZACION][INDEX] PARTNER NO ENCONTRADO O SIN user_id. partners.id='.$partner_id_post);
					\Session::set_flash('error', 'Socio inválido.');
					\Response::redirect('admin/precotizacion');
					return;
				}

				$user_id_to_save = (int) $partner->user_id;

				$payload = array(
					'partner_id' => $user_id_to_save,     # GUARDAR EL USER_ID
					'status'     => 0,
					'quote'      => serialize($quote),
				);

				try {
					$quote_partner = new \Model_Quotes_Partner($payload);
					if ($quote_partner->save())
					{
						\Session::delete('quote');

						\Session::set_flash(
							'success',
							'Precotización enviada. Continúa en "Precotización" para seguir agregando y recuerda finalizar en el módulo de Cotizaciones.'
						);

						\Response::redirect('admin/precotizacion');
						return;
					}
					else
					{
						\Session::set_flash('error', 'No se pudo enviar la precotización, por favor intenta de nuevo.');
					}
				} catch (\Exception $e) {
					\Session::set_flash('error', 'Error al guardar la precotización.');
				}
			}
			else
			{
				\Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifícalo.');
				$data['errors'] = $val->error();

				foreach ($classes as $name => $class)
				{
					$classes[$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
					$classes[$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
					$data[$name] = \Input::post($name);
				}
			}
		}

		# CARGAR CATALOGO DE PARTNERS (VALUE = partners.id)
		$partner_opts = array('0' => 'Selecciona una opción');
		$partners = \Model_Partner::query()->order_by('name', 'asc')->get();

		if (!empty($partners))
		{
			foreach ($partners as $partner)
			{
				$partner_opts[(string)$partner->id] = $partner->name.' ('.$partner->email.')';
			}
		}

		# PASAR DATOS A VISTA
		$data['products']     = $products_info;
		$data['partner_opts'] = $partner_opts;
		$data['classes']      = $classes;

		$this->template->title   = 'Solicitud de Precotización';
		$this->template->content = \View::forge('admin/solicitud_precotizacion/index', $data, false);
	}

	/**
	 * ENVIAR
	 * RECIBE partner_id = partners.id DESDE EL POST, TRADUCE A partners.user_id Y GUARDA user_id
	 */
	public function action_enviar()
	{

		# OBTENER POST COMPLETO PARA TRAZA
		$post = \Input::post();

		# LEER partner_id (NOMBRE NUEVO) O partner (NOMBRE VIEJO) DESDE LA VISTA
		$partner_id_post = \Input::post('partner_id', null);  # ESPERADO
		$partner_alt     = \Input::post('partner', null);     # COMPATIBILIDAD

		if (!$partner_id_post && $partner_alt) {
			$partner_id_post = $partner_alt;
		}

		# RESOLVER partners.user_id
		$partner = $partner_id_post ? \Model_Partner::find($partner_id_post) : null;
		if ($partner) {
		} else {
			\Log::warning('[PRECOTIZACION][ENVIAR] PARTNER NO ENCONTRADO PARA partners.id='.var_export($partner_id_post, true));
		}
		$user_id_to_save = $partner ? (int)$partner->user_id : null;

		# SESION quote
		$quote = \Session::get('quote');

		# VALIDAR Y GUARDAR
		if (!empty($quote) && $user_id_to_save)
		{
			$payload = array(
				'partner_id' => $user_id_to_save,  # GUARDAR EL USER_ID
				'status'     => 0,
				'quote'      => serialize($quote),
			);

			try {
				$quote_partner = new \Model_Quotes_Partner($payload);
				if ($quote_partner->save())
				{
						\Session::delete('quote');

						\Session::set_flash(
							'success',
							'Precotización enviada. Continúa en "Precotización" para seguir agregando y recuerda finalizar en el módulo de Cotizaciones.'
						);

						\Response::redirect('admin/precotizacion');
						return;
				}
				else
				{
					\Session::set_flash('error', 'No se pudo enviar la cotización, por favor intenta de nuevo.');
				}
			} catch (\Exception $e) {
				\Session::set_flash('error', 'Error al guardar la cotización.');
			}
		}
		else
		{
			\Session::set_flash('error', 'No existen productos en la cotización o el socio no es válido.');
		}

		\Response::redirect('socios/catalogo');
	}

}
