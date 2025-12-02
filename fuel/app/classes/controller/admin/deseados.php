<?php

/**
* CONTROLADOR ADMIN_ABANDONADOS
*
* @package  app
* @extends  Controller_Admin
*/
class Controller_Admin_Deseados extends Controller_Admin
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
		if(!Auth::member(100) && !Auth::member(50) && !Auth::member(25))
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
	* CARGA LA VISTA DE PRODUCTOS DESEADOS
	*
	* @access  public
	* @return  void
	*/
	public function action_index($search = '')
	{

		# SE INICIALIZAN LAS VARIABLES
		$data       	= array();
		$wishlists_info = array();
		$per_page   	= 100;


		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
        $wishlists = Model_Wishlist::query()
            ->related('customer')
            ->related('products');

		# SI HAY UNA BUSQUEDA
		if($search != '')
		{
			# SE ALMACENA LA BUSQUEDA ORIGINAL
			$original_search = $search;

			# SE LIMPIA LA CADENA DE BUSQUEDA
			$search = str_replace('+', ' ', rawurldecode($search));

			# SE REEMPLAZA LOS ESPACIOS POR PORCENTAJES
			$search = str_replace(' ', '%', $search);

			# SE AGREGA LA CLAUSULA
			$wishlists = $wishlists->where(DB::expr("CONCAT(`t0`.`id`, ' ', `t1`.`name`, ' ', `t1`.`last_name`)"), 'like', '%'.$search.'%');
		}


		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $wishlists->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
			'show_first'     => true,
    		'show_last'      => true,
		);

		#SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('wishlists', $config);

		# SE EJECUTA EL QUERY
		$wishlists = $wishlists->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();
		
		# SI SE OBTIENE INFORMACION
		if(!empty($wishlists))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($wishlists as $wishlist)
			{

			# Inicializar el total de precio para cada lista de deseos
            $total_price = 0;

            # Calcular el total de precio sumando el precio de cada producto en la lista
            foreach ($wishlist->products as $wishlist_product) {
                if (isset($wishlist_product->product->price->price)) {
                    $total_price += $wishlist_product->product->price->price;
                }
            	}

				# SE ALMACENA LA INFORMACION
				$wishlists_info[] = array(
					'id'     		=> $wishlist->id,
					'customer_id'  	=> $wishlist->customer->name. ' ' .$wishlist->customer->last_name,
					'created_at'  	=> date('d/M/Y', $wishlist->created_at),
					'updated_at'  	=> ($wishlist->updated_at != 0) ? date('d/M/Y', $wishlist->updated_at) : '',
					'total'         => $total_price,
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
        $data['wishlists'] = $wishlists_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

        # SE CARGA LA VISTA
        $this->template->title = "Listas de Deseados";
        $this->template->content = View::forge('admin/deseados/index', $data, false);
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
				Response::redirect('admin/deseados/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/deseados');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/deseados');
		}
	}


	/**
	* INFO
	*
	* MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	*
	* @access  public
	* @return  Void
	*/
	public function action_info($id = null)
	{
	# SE INICIALIZAN LAS VARIABLES
	 $products = array();

    // Validar la existencia del id
    is_null($id) and Response::redirect('admin/deseados');

    // Obtener el item de la lista de deseados
    $wishlist_item = Model_Wishlist::query()
        ->related('customer')
        ->related('products')
        ->where('id', $id)
        ->get_one();

    // Validar la existencia del item
    if (!$wishlist_item) {
        Session::set_flash('error', 'No se encontró la lista de deseados #'.$id);
        Response::redirect('admin/deseados');
    }

    // Inicializar variables
    $total_price = 0;
   

    // Calcular el total y preparar la lista de productos
    foreach ($wishlist_item->products as $wishlist_product) {
        if (isset($wishlist_product->product->price->price)) {
            $total_price += $wishlist_product->product->price->price;
        }
        $products[] = $wishlist_product->product;
    }

    // Preparar los datos para la vista
    $data['wishlist_item'] = array(
        'id'           => $wishlist_item->id,
        'customer_id'  => $wishlist_item->customer->name. ' ' .$wishlist_item->customer->last_name,
        'created_at'   => date('d/M/Y', $wishlist_item->created_at),
        'updated_at'   => ($wishlist_item->updated_at != 0) ? date('d/M/Y', $wishlist_item->updated_at) : '',
        'total_price'  => $total_price,
    );
    $data['products'] = $products;

    // Cargar la vista
    $this->template->title = "Detalles de la Lista de Deseados";
    $this->template->content = View::forge('admin/deseados/info', $data);
	}

	


}
