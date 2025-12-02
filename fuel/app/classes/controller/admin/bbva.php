<?php

/**
* CONTROLADOR ADMIN_BBVA
*
* @package  app
* @extends  Controller_Admin
*/
class Controller_Admin_Bbva extends Controller_Admin
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
    * PERMITE HACER UNA CONSULTA A BBVA
    *
    * @access  public
    * @return  Void
    */
    public function action_index()
    {
        # SE INICIALIZAN LAS VARIABLES
        $data    = array();
        $classes = array();
        $fields  = array('transaction');

        # SE RECORRE CAMPO POR CAMPO
        foreach($fields as $field)
        {
            # SE CREAN LAS CLASES DEL CAMPO
            $classes[$field] = array (
                'form-group'   => null,
                'form-control' => null,
            );
        }

        # SI SE UTILIZA EL METODO POST
        if(Input::method() == 'POST')
        {
            # SE CREA LA VALIDACION DE LOS CAMPOS
            $val = Validation::forge('bbva');
            $val->add_callable('Rules');
            $val->add_field('transaction', 'trasacción', 'required|min_length[1]|max_length[255]');

            # SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
            if($val->run())
            {
                # SE ESTABLECEN PRODUCTIVO O SANDBOX
                $bbva = Bbva::setProductionMode(true);

                # SE ESTABLECEN LAS LLAVES DE BBVA
                $bbva = Bbva::getInstance('mt2rah8yd5pizoza4rv5', 'sk_2b17754df5a24ec6b44ef47bace07732');

                try
                {
                    # SE OBTIENE LA INFORMACION DEL CARGO
                    $charge = $bbva->charges->get(trim($val->validated('transaction')));

                    # SI EXISTE LA PROPIEDAD SERIALIZABLEDATA
                    if(property_exists($charge, 'serializableData'))
                    {
                        # SE SERIALIZA LA RESPUESTA
                        $response = serialize($charge->serializableData);
                    }
                    else
                    {
                        # SE INICIALIZA EL ARREGLO
                        $status = array();

                        # SE ALMACENA EL ESTATUS
                        $status['status'] = $charge->status;

                        # SE SERIALIZA EL ESTATUS
                        $response = serialize($status);
                    }

                    # SE CREA EL MODELO CON LA INFORMACION
                    $log = new Model_Providers_Log(array(
                        'provider'    => 'BBVA',
                        'type'        => 'Consulta',
                        'transaction' => trim($val->validated('transaction')),
                        'response'    => $response
                    ));

                    # SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
                    $log->save();

                    # SI EL PAGO ES EXITOSO
                    if($charge->status == 'completed')
                    {
                        # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
                        $sale = Model_Sale::query()
                        ->where('transaction', trim($val->validated('transaction')))
                        ->get_one();

                        # SI SE OBTIENE INFORMACION
                        if(!empty($sale))
                        {
                            # SI LA VENTA NO SE HA REGISTRADO
                            if($sale->status == 0)
                            {
                                # SE ALMACENA LA INFORMACION
                                $request = array(
                					'type_id' => 1, // BBVA
                                    'token'   => $charge->authorization,
                                    'total'   => $charge->amount
                                );

                				# SE CREA UN REGISTRO DEL PAGO
                                $new_payment = Model_Payment::set_new_record($request);

                				# SE ALMACENA LA INFORMACION
                                $request = array(
                                    'payment_id' => $new_payment->id,
                                    'status'     => 1,
                                    'sale_date'  => time()
                                );

                				# SE ACTUALIZA EL REGISTRO DEL PAGO
                                Model_Sale::do_update($request, $sale->id);

                				# SE RECORRE ELEMENTO POR ELEMENTO
                				foreach($sale->products as $sale_product)
                				{
                					# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
                					$product = Model_Product::query()
                					->where('id', $sale_product->product_id)
                					->get_one();

                					# SE RESTA LA CANTIDAD COMPRADA
                					$product->available -= $sale_product->quantity;

                					# SE GUARDA EL REGISTRO EN LA BASE DE DATOS
                					$product->save();
                				}

                				# SE ENVIA EL CORREO AL USUARIO
                				$this->send_user_mail($sale->id);

                				# SE ENVIA EL CORREO AL ADMINISTRADOR
                				$this->send_admin_mail($sale->id);

                				# SE ENVIA EL CORREO AL DESARROLLADOR
                				$this->send_dev_mail($sale->id, 'BBVA', 'Pagado');

                                # SE ESTABLECE EL MENSAJE DE EXITO
                                Session::set_flash('success', 'Se registró la respuesta en Logs, y se confimó la venta <b>'.$sale->id.'</b>.');
                            }
                            else
                            {
                                # SE ESTABLECE EL MENSAJE DE EXITO
                                Session::set_flash('success', 'Se registró la respuesta en Logs, pero anteriormente se había confirmado la venta <b>'.$sale->id.'</b>.');
                            }
                        }
                        else
                        {
                            # SE ESTABLECE EL MENSAJE DE ERROR
                            Session::set_flash('error', 'Se registró la respuesta en Logs, pero no existe una venta en la base de datos con la transación <b>'.trim($val->validated('transaction')).'</b>.');
                        }
                    }
                    else
                    {
                        # SE ESTABLECE EL MENSAJE DE EXITO
                        Session::set_flash('success', 'Se registró la respuesta en Logs, pero no se autorizó ninguna venta.');
                    }
                }
                catch(Exception $e)
                {
                    # SE ESTABLECE EL MENSAJE DE ERROR
                    Session::set_flash('error', 'No se pudo ejecutar la consulta en BBVA.');
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

                    # SE ALMACENA LA INFORMACION PARA LA VISTA
                    $data[$name] = Input::post($name);
                }
            }
        }

        # SE ALMACENA LA INFORMACION PARA LA VISTA
        $data['classes'] = $classes;

        # SE CARGA LA VISTA
        $this->template->title   = 'BBVA';
        $this->template->content = View::forge('admin/bbva/index', $data);
    }


    /**
     * SEND USER MAIL
     *
     * ENVIA POR EMAIL UN MENSAJE DEL PEDIDO AL USUARIO
     *
     * @access  private
     * @return  Boolean
     */
    private function send_user_mail($sale_id = 0)
    {
		# SE INICIALIZAN LAS VARIABLES
		$data          = array();
		$address_html  = '';
		$products_html = '';
        $totals_html   = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$sale = Model_Sale::query()
		->related('products')
		->related('customer')
		->where('id', $sale_id)
		->where('status', 1)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($sale))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($sale->products as $product)
			{
				# SE CREA EL HTML DE LOS PRODUCTOS
				$products_html .= Asset::img('thumb_'.$product->product->image, array('alt' => $product->product->name)).'
				<strong style="display: block; margin-bottom: 15px">'.$product->product->name.'</strong>
				<strong style="display: block;">Precio unitario:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">$'.number_format($product->price, 2, '.', ',').'</span>
				<strong style="display: block;">Cantidad:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">'.$product->quantity.'</span>
				<strong style="display: block;">Total:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">$'.number_format($product->total, 2, '.', ',').'</span>
				';
			}

            # SI EXISTE DIRECCION
            if($sale->address_id != 0)
            {
                # SE CREA EL HTML DE LA DIRECCION DE ENVIO
                $address_html .= '<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Dirección de envío</h1>
                <p>
                    <strong style="display: block;">Calle:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->street.'</span>
                    <strong style="display: block;">Número:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->number.'</span>
                    <strong style="display: block;">Número interior:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->internal_number.'</span>
                    <strong style="display: block;">Colonia:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->colony.'</span>
                    <strong style="display: block;">Código postal:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->zipcode.'</span>
                    <strong style="display: block;">Ciudad:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->city.'</span>
                    <strong style="display: block;">Estado:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->state->name.'</span>
                    <strong style="display: block;">Detalles:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->details.'</span>
                    <strong style="display: block;">Recibe:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->name.' '.$sale->address->last_name.'</span>
                    <strong style="display: block;">Teléfono:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->phone.'</span>
                    <hr>
                </p>';
            }

            # SE CREA EL HTML DEL TOTAL DE PRODUCTOS
            $totals_html .= '<strong style="display: block;">Total:</strong>
            <span style="display: block; margin-bottom: 15px; color: #ee3530">$'.number_format($sale->total, 2, '.', ',').'</span>';

			# SE CREA EL CUERPO DEL CORREO
            $data['body'] =
            '<tr>
                <td style="background-color: #ffffff;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                                <h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">¡Gracias por comprar en Distribuidora Sajor!</h1>
                                <p style="margin: 0;">Este mesaje ha sido enviado automáticamente desde la página web de <strong>Distribuidora Sajor</strong>, para confirmar tu pedido, a continuación te dejamos la información registrada:</p>
                                <p>
                                    <strong style="display: block;">Nombre:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->customer->name.' '.$sale->customer->last_name.'</span>
                                    <strong style="display: block;">Correo electrónico:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->customer->user->email.'</span>
									<strong style="display: block;">Pedido:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">#'.$sale->id.'</span>
									'.$totals_html.'
                                    <hr>
                                </p>
								'.$address_html.'
								<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Productos</h1>
                                <p>
                                    '.$products_html.'
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>';

            # SE ENVIA UN EMAIL CON EL MENSAJE
            $email = Email::forge();
            $email->from('sistemas@sajor.mx', 'Distribuidora Sajor');
            $email->reply_to('sistemas@sajor.mx', 'Distribuidora Sajor');
            $email->to(array(
                $sale->customer->user->email => $sale->customer->name.' '.$sale->customer->last_name,
                //'omar@sectorweb.mx' => 'Sector Web',
                //'sistemas@sajor.mx' => 'Distribuidora Sajor',
            ));
            $email->subject('Distribuidora Sajor - Pedido #'.$sale->id);
            $email->html_body(View::forge('email_templates/default', $data, false), false);

            # SE ENVIA EL EMAIL
            try
			{
                # SE ENVIA EL EMAIL
                if($email->send())
                {
                    # SE DEVUELVE TRUE
                    return true;
                }
            }
            catch(\EmailSendingFailedException $e)
            {
				# SE DEVUELVE FALSE
                return false;
            }
            catch(\EmailValidationFailedException $e)
            {
				# SE DEVUELVE FALSE
                return false;
            }
		}
		else
		{
			# SE DEVUELVE FALSE
			return false;
		}
    }


	/**
     * SEND ADMIN MAIL
     *
     * ENVIA POR EMAIL UN MENSAJE DEL PEDIDO AL ADMINISTRADOR
     *
     * @access  private
     * @return  Boolean
     */
    private function send_admin_mail($sale_id = 0)
    {
		# SE INICIALIZAN LAS VARIABLES
		$data          = array();
		$address_html  = '';
		$products_html = '';
        $totals_html   = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$sale = Model_Sale::query()
		->related('products')
		->related('customer')
		->where('id', $sale_id)
		->where('status', 1)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($sale))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($sale->products as $product)
			{
				# SE CREA EL HTML DE LOS PRODUCTOS
				$products_html .= Asset::img('thumb_'.$product->product->image, array('alt' => $product->product->name)).'
				<strong style="display: block; margin-bottom: 15px">'.$product->product->name.'</strong>
				<strong style="display: block;">Precio unitario:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">$'.number_format($product->price, 2, '.', ',').'</span>
				<strong style="display: block;">Cantidad:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">'.$product->quantity.'</span>
				<strong style="display: block;">Total:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">$'.number_format($product->total, 2, '.', ',').'</span>
				';
			}

            # SI EXISTE DIRECCION
            if($sale->address_id != 0)
            {
                # SE CREA EL HTML DE LA DIRECCION DE ENVIO
                $address_html .= '<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Dirección de envío</h1>
                <p>
                    <strong style="display: block;">Calle:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->street.'</span>
                    <strong style="display: block;">Número:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->number.'</span>
                    <strong style="display: block;">Número interior:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->internal_number.'</span>
                    <strong style="display: block;">Colonia:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->colony.'</span>
                    <strong style="display: block;">Código postal:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->zipcode.'</span>
                    <strong style="display: block;">Ciudad:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->city.'</span>
                    <strong style="display: block;">Estado:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->state->name.'</span>
                    <strong style="display: block;">Detalles:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->details.'</span>
                    <strong style="display: block;">Recibe:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->name.' '.$sale->address->last_name.'</span>
                    <strong style="display: block;">Teléfono:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->phone.'</span>
                    <hr>
                </p>';
            }

            # SE CREA EL HTML DEL TOTAL DE PRODUCTOS
            $totals_html .= '<strong style="display: block;">Total:</strong>
            <span style="display: block; margin-bottom: 15px; color: #ee3530">$'.number_format($sale->total, 2, '.', ',').'</span>';

			# SE CREA EL CUERPO DEL CORREO
            $data['body'] =
            '<tr>
                <td style="background-color: #ffffff;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                                <h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Nueva venta en línea.</h1>
                                <p style="margin: 0;">Este mesaje ha sido enviado desde la página web de <strong>Distribuidora Sajor</strong>, con la siguiente información:</p>
                                <p>
                                    <strong style="display: block;">Nombre:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->customer->name.' '.$sale->customer->last_name.'</span>
                                    <strong style="display: block;">Correo electrónico:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->customer->user->email.'</span>
									<strong style="display: block;">Pedido:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">#'.$sale->id.'</span>
									'.$totals_html.'
                                    <hr>
                                </p>
                                '.$address_html.'
								<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Productos</h1>
                                <p>
                                    '.$products_html.'
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>';

            # SE ENVIA UN EMAIL CON EL MENSAJE
            $email = Email::forge();
            $email->from('sistemas@sajor.mx', 'Distribuidora Sajor');
            $email->reply_to($sale->customer->user->email, $sale->customer->name.' '.$sale->customer->last_name);
            $email->to(array(
                'sistemas@sajor.mx' => 'Distribuidora Sajor',
                //'omar@sectorweb.mx' => 'Sector Web',
            ));
            $email->subject('Distribuidora Sajor - Venta en línea #'.$sale->id);
            $email->html_body(View::forge('email_templates/default', $data, false), false);

            # SE ENVIA EL EMAIL
            try
			{
                # SE ENVIA EL EMAIL
                if($email->send())
                {
                    # SE DEVUELVE TRUE
                    return true;
                }
            }
            catch(\EmailSendingFailedException $e)
            {
				# SE DEVUELVE FALSE
                return false;
            }
            catch(\EmailValidationFailedException $e)
            {
				# SE DEVUELVE FALSE
                return false;
            }
		}
		else
		{
			# SE DEVUELVE FALSE
			return false;
		}
    }


	/**
     * SEND DEV MAIL
     *
     * ENVIA POR EMAIL UN MENSAJE DEL PEDIDO AL DESARROLLADOR
     *
     * @access  private
     * @return  Boolean
     */
    private function send_dev_mail($sale_id = 0, $payment_type = '', $payment_status = '')
    {
		# SE INICIALIZAN LAS VARIABLES
		$data          = array();
        $address_html  = '';
		$products_html = '';
        $totals_html   = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$sale = Model_Sale::query()
		->related('products')
		->related('customer')
		->where('id', $sale_id)
		->where('status', 1)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($sale))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($sale->products as $product)
			{
				# SE CREA EL HTML DE LOS PRODUCTOS
				$products_html .= Asset::img('thumb_'.$product->product->image, array('alt' => $product->product->name)).'
				<strong style="display: block; margin-bottom: 15px">'.$product->product->name.'</strong>
				<strong style="display: block;">Precio unitario:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">$'.number_format($product->price, 2, '.', ',').'</span>
				<strong style="display: block;">Cantidad:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">'.$product->quantity.'</span>
				<strong style="display: block;">Total:</strong>
				<span style="display: block; margin-bottom: 15px; color: #ee3530">$'.number_format($product->total, 2, '.', ',').'</span>
				';
			}

            # SI EXISTE DIRECCION
            if($sale->address_id != 0)
            {
                # SE CREA EL HTML DE LA DIRECCION DE ENVIO
                $address_html .= '<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Dirección de envío</h1>
                <p>
                    <strong style="display: block;">Calle:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->street.'</span>
                    <strong style="display: block;">Número:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->number.'</span>
                    <strong style="display: block;">Número interior:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->internal_number.'</span>
                    <strong style="display: block;">Colonia:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->colony.'</span>
                    <strong style="display: block;">Código postal:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->zipcode.'</span>
                    <strong style="display: block;">Ciudad:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->city.'</span>
                    <strong style="display: block;">Estado:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->state->name.'</span>
                    <strong style="display: block;">Detalles:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->details.'</span>
                    <strong style="display: block;">Recibe:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->name.' '.$sale->address->last_name.'</span>
                    <strong style="display: block;">Teléfono:</strong>
                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->address->phone.'</span>
                    <hr>
                </p>';
            }

            # SE CREA EL HTML DEL TOTAL DE PRODUCTOS
            $totals_html .= '<strong style="display: block;">Total:</strong>
            <span style="display: block; margin-bottom: 15px; color: #ee3530">$'.number_format($sale->total, 2, '.', ',').'</span>';

			# SE CREA EL CUERPO DEL CORREO
            $data['body'] =
            '<tr>
                <td style="background-color: #ffffff;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tr>
                            <td style="padding: 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #555555;">
                                <h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Nueva venta en línea.</h1>
                                <p style="margin: 0;">Este mesaje ha sido enviado desde la página web de <strong>Distribuidora Sajor</strong>, con la siguiente información:</p>
                                <p>
                                    <strong style="display: block;">Nombre:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->customer->name.' '.$sale->customer->last_name.'</span>
                                    <strong style="display: block;">Correo electrónico:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$sale->customer->user->email.'</span>
									<strong style="display: block;">Pedido:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">#'.$sale->id.'</span>
									'.$totals_html.'
									<strong style="display: block;">Método de pago:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$payment_type.'</span>
									<strong style="display: block;">Status:</strong>
                                    <span style="display: block; margin-bottom: 15px; color: #ee3530">'.$payment_status.'</span>
                                    <hr>
                                </p>
								'.$address_html.'
								<h1 style="margin: 0 0 10px 0; font-family: sans-serif; font-size: 25px; line-height: 30px; color: #333333; font-weight: normal;">Productos</h1>
                                <p>
                                    '.$products_html.'
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>';

            # SE ENVIA UN EMAIL CON EL MENSAJE
            $email = Email::forge();
            $email->from('sistemas@sajor.mx', 'Distribuidora Sajor');
            $email->reply_to($sale->customer->user->email, $sale->customer->name.' '.$sale->customer->last_name);
            $email->to(array(
                'omar@sectorweb.mx' => 'Sector Web',
				'ysolis@sajor.mx' => 'Yaneth'
            ));
            $email->subject('Distribuidora Sajor - Dev #'.$sale->id);
            $email->html_body(View::forge('email_templates/default', $data, false), false);

            # SE ENVIA EL EMAIL
            try
			{
                # SE ENVIA EL EMAIL
                if($email->send())
                {
                    # SE DEVUELVE TRUE
                    return true;
                }
            }
            catch(\EmailSendingFailedException $e)
            {
				# SE DEVUELVE FALSE
                return false;
            }
            catch(\EmailValidationFailedException $e)
            {
				# SE DEVUELVE FALSE
                return false;
            }
		}
		else
		{
			# SE DEVUELVE FALSE
			return false;
		}
    }
}
