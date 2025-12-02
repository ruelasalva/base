<?php

namespace sectorweb;

class Controller_Quote extends \Controller
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
	 * DEPURA LA COTIZACION
	 *
	 * RECIBE LA COTIZACION Y UN ID DE PRODUCTO EN CASO DE QUE TENGA QUE SER ELIMINADO
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
            $total_products_quantity = 0;

            # SI EXISTE SESION DE COTIZACION O HAY UN PRODUCTO A ELIMINAR
            if(\Session::get('quote') or $delete_product_id != null)
            {
                # SE OBTIENE LA SESION DE LA COTIZACION
                $quote = \Session::get('quote');

                # SI HAY PRODUCTOS EN LA COTIZACION O HAY UN PRODUCTO A ELIMINAR
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
                                $product_quote->deleted == 0
                            )
                            {
                                # SE OBTIENE LA CANTIDAD SOLICITADA
                                $product_quantity = $array['quantity'];

                                # SE CALCULA EL TOTAL EN CANTIDAD DE PRODUCTOS
                                $total_products_quantity += $product_quantity;

                                # SE LLENA EL ARREGLO QUOTE_DATA CON LA INFORMACION DEL PRODUCTO
                                $quote_data[] = array(
                                    'id'          => $product_quote->id,
                                    'slug'        => $product_quote->slug,
                                    'name'        => $product_quote->name,
                                    'code'        => $product_quote->code,
                                    'image'       => $product_quote->image,
                                    'description' => $product_quote->description,
                                    'available'   => (int)$product_quote->available,
                                    'quantity'    => (int)$array['quantity']
                                );
                            }
                            # SINO
                            else
                            {
                                # SE ELIMINA DE LA COTIZACION
                                unset($quote[$product_quote->id]);

                                # SE LLENA EL ARREGLO QUOTE_UNAVAILABLE CON LA INFORMACION DEL PRODUCTO
                                $quote_unavailable[] = array(
                                    'id'   => $product_quote->id,
                                    'name' => $product_quote->name
                                );
                            }
                        }
                        # SI NO SE ENCONTRO EL PRODUCTO EN LA BD
                        else
                        {
                            # SE ELIMINA EL PRODUCTO DE LA COTIZACION
                            unset($quote[$id]);
                        }
                    }

                    # SE ACTUALIZA O ELIMINA LA SESION DE LA COTIZACION
                    (!empty($quote)) ? \Session::set('quote', $quote) : \Session::delete('quote');
                }
                else
                {
                    # SE ELIMINA LA SESION DE COTIZACION
                    \Session::delete('quote');
                }
            }

            return array(
                'quote_data'              => $quote_data,
                'quote_session'           => $quote,
                'quote_unavailable'       => $quote_unavailable,
                'total_products_quantity' => $total_products_quantity
            );
		}
		else
		{
			# SE MUESTRA 404
			\Response::redirect('404');
		}
	}
}
