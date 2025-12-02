<?php

namespace sectorweb;

class Controller_Quote_Partners extends \Controller
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
	 * DEBUG
	 *
	 * DEPURA EL CARRITO DE COTIZACION
	 *
	 * RECIBE EL CARRITO Y UN ID DE PRODUCTO EN CASO DE QUE TENGA QUE SER ELIMINADO
	 * RETORNA UN ARREGLO INDICANDO QUE PRODUCTOS ESTAN DISPONIBLES Y CUALES NO
	 * REGRESA EL TOTAL EN PRECIO Y CANTIDAD DE LOS PRODUCTOS DISPONIBLES
	 *
	 *
	 * @access  public
	 * @return  array
	 */
	public function action_debug($ajax = false, $delete_product_id = null)
	{
		# SI LA PETICION ES LLAMADA DESDE UN CONTROLADOR
		if(\Request::is_hmvc())
		{
            # SE INICIALIZAN LOS ARREGLOS
            $quote_unavailable = array();
            $quote_data        = array();
            $quote             = array();

            # SE INICIALIZAN LAS VARIABLES
            $total_products_quantity  = 0;
            $total_products_price     = 0;
			$total_shipping           = 0;
			$total_discount           = 0;

            # SI EXISTE SESION DE CARRITO O HAY UN PRODUCTO A ELIMINAR
            if(\Session::get('quote') or $delete_product_id != null)
            {
                # SE OBTIENE LA SESION DEL CARRITO
                $quote = \Session::get('quote');

                # SI HAY PRODUCTOS EN EL CARRITO O HAY UN PRODUCTO A ELIMINAR
                if(!empty($quote) or $delete_product_id != null)
                {
					# SE RECORRE PRODUCTO POR PRODUCTO
                    foreach($quote as $id => $array)
                    {
                        # SE BUSCA EL PRODUCTO
                        $product_quote = \Model_Product::find($id);

                        # SI SE OBTUVO EL RESULTADO
                        if(!empty($product_quote))
                        {
                            # SI EL PRODUCTO ESTA DISPONIBLE
                            if(
                                $product_quote->status == 1 and
                                $product_quote->available > 0 and
                                $product_quote->deleted == 0
                            )
                            {
                                # SE OBTIENE LA CANTIDAD SOLICITADA
                                $product_quantity = $array['quantity'];

                                # SI LA CANTIDAD DE LA PROPIEDAD ES MENOR A LA SOLICITADA
                                if($product_quote->available < $product_quantity)
                                {
                                    # SE ESTABLECE LA CANTIDAD MAXIMA DISPONIBLE DE LA PROPIEDAD EN CART Y SE SOBREESCRIBE LA VAR PRODUCT_QUANTITY
                                    $quote[$product_quote->id]['quantity'] = $product_quote->available;
                                    $product_quantity                    = $product_quote->available;
                                }

                                # SE OBTIENE EL PRECIO DEL PRODUCTO
                                $price_quote = number_format(\Model_Products_Price::get_price($product_quote->id, \Request::forge('sectorweb/user/get_type_customer', false)->execute()->response->body), 2, '.', '');

								# DEPENDIENDO EL CASO
								switch($product_quote->price_per)
								{
									# UNIDAD
									case 'u':
										# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
										$price_wholesale = \Model_Products_Prices_Wholesale::query()
										->where('product_id', $product_quote->id)
										->and_where_open()
										->where('min_quantity', '<=', $product_quantity)
										->where('max_quantity', '>=', $product_quantity)
										->and_where_close()
										->order_by('id', 'asc')
										->get_one();

										# SI SE OBTUVO INFORMACION
										if(!empty($price_wholesale))
										{
											# SE ALMACENA EL PRECIO
											$price_quote = number_format($price_wholesale->price, 2, '.', '');
										}
									break;

									# MONTO
									case 'm':
										# SI UN MONTO RELACIONADO
										if(!empty($product_quote->products_price_amount))
										{
											# SI EXISTE UN MONTO RELACIONADO
											if($product_quote->products_price_amount->amount_id > 0)
											{
												# SE OBTIENE EL TOTAL DEL PRODUCTO
				                                $total_product_price_tmp = number_format($price_quote * $product_quantity, 2, '.', '');

												# SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
												$price_amount = \Model_Prices_Amount::query()
												->where('amount_id', $product_quote->products_price_amount->amount_id)
												->and_where_open()
												->where('min_amount', '<=', $total_product_price_tmp)
												->where('max_amount', '>=', $total_product_price_tmp)
												->and_where_close()
												->order_by('id', 'asc')
												->get_one();

												# SI SE OBTUVO INFORMACION
												if(!empty($price_amount))
												{
													# SE ALMACENA EL PRECIO
													$price_quote = number_format($price_quote - (($price_amount->percentage / 100) * $price_quote), 2, '.', '');
												}
											}
										}
									break;
								}

                                # SE OBTIENE EL TOTAL DEL PRODUCTO
                                $total_product_price = number_format($price_quote * $product_quantity, 2, '.', '');

                                # SE CALCULA EL TOTAL EN CANTIDAD DE PRODUCTOS
                                $total_products_quantity += $product_quantity;

                                # SE CALCULA EL TOTAL DEL PRECIO DE LOS PRODUCTOS
                                $total_products_price += number_format($total_product_price, 2, '.', '');

                                # SE LLENA EL ARREGLO CART_DATA CON LA INFORMACION DEL PRODUCTO
                                $quote_data[] = array(
                                    'id'                => $product_quote->id,
                                    'slug'              => $product_quote->slug,
                                    'name'              => $product_quote->name,
                                    'code'              => $product_quote->code,
                                    'image'             => $product_quote->image,
                                    'description'       => $product_quote->description,
                                    'price'             => array(
                                        'original'      => array(
                                            'regular'   => number_format($product_quote->original_price, 2, '.', ''),
                                            'formatted' => number_format($product_quote->original_price, 2, '.', ',')
                                        ),
                                        'current'       => array(
                                            'regular'   => number_format($price_quote, 2, '.', ''),
                                            'formatted' => number_format($price_quote, 2, '.', ',')
                                        ),
                                        'total'         => array(
                                            'regular'   => number_format($total_product_price, 2, '.', ''),
                                            'formatted' => number_format($total_product_price, 2, '.', ',')
                                        ),
                                    ),
                                    'available'         => (int)$product_quote->available,
                                    'quantity'          => array(
                                        'valid'         => (int)$product_quantity,
                                        'current'       => (int)$array['quantity'],
                                    ),
                                );
                            }
                            # SI NO
                            else
                            {
                                # SE ELIMINA DEL CARRITO
                                unset($quote[$product_quote->id]);

                                # SE LLENA EL ARREGLO CART_UNAVAILABLE CON LA INFORMACION DEL PRODUCTO
                                $quote_unavailable[] = array(
                                    'id'   => $product_quote->id,
                                    'name' => $product_quote->name
                                );
                            }
                        }
                        # SI NO SE ENCONTRO EL PRODUCTO EN LA BD
                        else
                        {
                            # SE ELIMINA EL PRODUCTO DEL CARRITO
                            unset($quote[$id]);
                        }
                    }

                    # SE ACTUALIZA O ELIMINA LA SESION DEL CARRITO
                    (!empty($quote)) ? \Session::set('quote', $quote) : \Session::delete('quote');
                }
                else
                {
                    # SE ELIMINA LA SESION DEL CARRITO
                    \Session::delete('quote');
                }
            }

            return array(
                'quote_data'               => $quote_data,
                'quote_session'            => $quote,
                'quote_unavailable'        => $quote_unavailable,
                'total_products_quantity' => $total_products_quantity,
                'total_products_price'    => array(
                    'regular'   => number_format($total_products_price, 2, '.', ''),
                    'formatted' => number_format($total_products_price, 2, '.', ',')
                ),
				'shipping'                => array(
					'regular'   => number_format($total_shipping, 2, '.', ''),
                    'formatted' => number_format($total_shipping, 2, '.', ',')
				),
				'discount'                => array(
					'regular'   => number_format($total_discount, 2, '.', ''),
                    'formatted' => number_format($total_discount, 2, '.', ',')
				),
				'total'                   => array(
					'regular'   => number_format($total_products_price + $total_shipping - $total_discount, 2, '.', ''),
                    'formatted' => number_format($total_products_price + $total_shipping - $total_discount, 2, '.', ',')
				),
				'total_without_discount'  => array(
					'regular'   => number_format($total_products_price + $total_shipping, 2, '.', ''),
                    'formatted' => number_format($total_products_price + $total_shipping, 2, '.', ',')
				)
            );
		}
		else
		{
			# SE MUESTRA 404
			\Response::redirect('404');
		}
	}
}
