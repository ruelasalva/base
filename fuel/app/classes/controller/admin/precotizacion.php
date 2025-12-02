<?php

/**
* CONTROLADOR PRECOTIZACION
*
* @package  app
* @extends  Controller_Admin
*/
class Controller_Admin_Precotizacion extends Controller_Admin
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
    *
    * MUESTRA LOS PRODUCTOS DEL CATALOGO
    *
    * @access  public
    * @return  Void
    */
    public function action_index()
    {
        # INICIALIZAR VARIABLES
        $data                = array();
        $categories_info     = array();
        $subcategories_info  = array();
        $brands_info         = array();
        $products_info       = array();
        $per_page            = 32;
        $order_by            = Input::get('orden', 'nuevos');

        # FILTROS OPCIONALES
        $brand_slug = Input::get('marca');
        $category_slug = Input::get('categoria');
        $subcategory_slug = Input::get('grupo');

        # SE OBTIENE EL ID DEL USUARIO
        $user_id = Auth::get('id');

        # CATEGORÍAS
        foreach(Model_Category::query()
        ->where('deleted', 0)
        ->where('status', 1)
        ->order_by('name', 'asc')
        ->get() as $category)
        {
            $categories_info[] = array('slug' => $category->slug, 'name' => Str::truncate($category->name, 30));
        }

        # SUBCATEGORÍAS
        foreach(Model_Subcategory::query()
        ->where('deleted', 0)
        ->where('status', 1)
        ->order_by('name', 'asc')
        ->get() as $subcategory)
        {
            $subcategories_info[] = array('slug' => $subcategory->slug, 'name' => Str::truncate($subcategory->name, 30));
        }

        # MARCAS
        foreach(Model_Brand::query()
        ->where('deleted', 0)
        ->where('status', 1)
        ->order_by('name', 'asc')
        ->get() as $brand)
        {
            $brands_info[] = array('slug' => $brand->slug, 'name' => Str::truncate($brand->name, 30));
        }

        # QUERY DE PRODUCTOS
        $products_query = Model_Product::query()
        ->where('status', 1)
        ->where('deleted', 0)
        ->where('available', '>=', 0);

        # FILTROS
        if ($brand_slug)
        {
            $brand = Model_Brand::query()->where('slug', $brand_slug)->get_one();
            if ($brand) $products_query->where('brand_id', $brand->id);
        }
        if ($category_slug)
        {
            $category = Model_Category::query()->where('slug', $category_slug)->get_one();
            if ($category) $products_query->where('category_id', $category->id);
        }
        if ($subcategory_slug)
        {
            $subcategory = Model_Subcategory::query()->where('slug', $subcategory_slug)->get_one();
            if ($subcategory) $products_query->where('subcategory_id', $subcategory->id);
        }

        # ORDENAMIENTO
        switch($order_by)
        {
            case 'a-z':
            $products_query->order_by('name', 'asc');
            break;
            case 'z-a':
            $products_query->order_by('name', 'desc');
            break;
            case 'nuevos':
            default:
            $products_query->order_by(DB::expr('(newproduct + 1) * id'), 'desc');
            break;
        }

        # PAGINACIÓN
        $total_items = $products_query->count();
        $base_url = Uri::current().'?';
        $params = array();
        if ($brand_slug) $params[] = 'marca=' . urlencode($brand_slug);
        if ($category_slug) $params[] = 'categoria=' . urlencode($category_slug);
        if ($subcategory_slug) $params[] = 'grupo=' . urlencode($subcategory_slug);
        if ($order_by) $params[] = 'orden=' . urlencode($order_by);
        $pagination_url = $base_url . implode('&', $params);

        $pagination = Pagination::forge('products', array(
            'name'           => 'admin',
            'pagination_url' => $pagination_url,
            'total_items'    => $total_items,
            'per_page'       => $per_page,
            'uri_segment'    => 'pagina',
            'show_first'     => true,
            'show_last'      => true,
        ));

        # OBTENER PRODUCTOS
        $products = $products_query
        ->rows_limit($pagination->per_page)
        ->rows_offset($pagination->offset)
        ->get();

        foreach($products as $product)
        {
            # HELPER PARA RENDERIZAR EL BADGE DEL PRODUCTO
			$badge = Helper_Purchases::render_product_badge($product, 'admin');

            $products_info[] = array(
                'id'         => $product->id,
                'product_id' => $product->id,
                'slug'       => $product->slug,
                'name'       => $product->name,
                'code'       => $product->code,
                'image'      => $product->image,
                'newproduct' => $product->newproduct,
                'soon'                  => $product->soon,
                'temporarily_sold_out'  => $product->temporarily_sold_out,
                'badge'                 => $badge,
                'available'             => $product->available
            );
        }

        # DATOS A LA VISTA
        $data['order_by']      = $order_by;
        $data['categories']    = $categories_info;
        $data['subcategories'] = $subcategories_info;
        $data['brands']        = $brands_info;
        $data['products']      = $products_info;
        $data['pagination']    = $pagination->render();

        # RENDER
        $this->template->title = 'Pecotización';
        $this->template->content = View::forge('admin/precotizacion/index', $data, false);
    }




    /**
    * BUSQUEDA
    *
    * REDIRECCIONA A LA URL DE BUSCAR CON LOS PARAMETROS
    *
    * @access  public
    * @return  Void
    */
    public function action_busqueda()
    {
        # SI SE UTILIZO EL METODO POST
        if(Input::method() == 'POST')
        {
            # SE CREA LA VALIDACION DE LOS CAMPOS
            $val = Validation::forge('search');
            $val->add_callable('Rules');
            $val->add_field('search', 'search', 'required|max_length[100]');

            # SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
            if($val->run())
            {
                # SE REMPLAZAN ALGUNOS CARACTERES
                $search = str_replace(' ', '+', $val->validated('search'));
                $search = str_replace('*', '', $search);

                # SE REDIRECCIONA A BUSCAR ARTICULOS
                Response::redirect('admin/precotizacion/buscar/'.$search);
            }
            else
            {
                # SE REDIRECCIONA AL USUARIO
                Response::redirect('admin');
            }
        }
        else
        {
            # SE REDIRECCIONA AL USUARIO
            Response::redirect('/');
        }
    }


    /**
    * BUSCAR
    *
    * MUESTRA LOS RESULTADOS DE LA BUSQUEDA
    *
    * @access  public
    * @return  Void
    */
    public function action_buscar($search = '')
    {
        # INICIALIZAR VARIABLES
        $data               = array();
        $banners_info       = array();
        $categories_info    = array();
        $subcategories_info = array();
        $brands_info        = array();
        $products_info      = array();
        $order_by           = 'nuevos';
        $per_page           = 30;

        # SE OBTIENE EL ID DEL USUARIO
        $user_id = Auth::get('id');

        # SE RECORRE ELEMENTO POR ELEMENTO
        foreach($_GET as $key => $get)
        {
            # SI EXISTE UN ORDEN
            if($key == 'orden')
            {
                # SE ALMACENA EL ORDEN
                $order_by = $get;
            }
        }

        # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
        $categories = Model_Category::query()
        ->where('deleted', 0)
        ->order_by('name', 'asc')
        ->get();

        # SI SE OBTIENE INFORMACION
        if(!empty($categories))
        {
            # SE RECORRE ELEMENTO POR ELEMENTO
            foreach($categories as $category)
            {
                # SE ALMACENA LA INFORMACION
                $categories_info[] = array(
                    'slug' => $category->slug,
                    'name' => Str::truncate($category->name, 30),
                );
            }
        }

        # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
        $subcategories = Model_Subcategory::query()
        ->where('deleted', 0)
        ->order_by('name', 'asc')
        ->get();

        # SI SE OBTIENE INFORMACION
        if(!empty($subcategories))
        {
            # SE RECORRE ELEMENTO POR ELEMENTO
            foreach($subcategories as $subcategory)
            {
                # SE ALMACENA LA INFORMACION
                $subcategories_info[] = array(
                    'slug' => $subcategory->slug,
                    'name' => Str::truncate($subcategory->name, 30)
                );
            }
        }

        # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
        $brands = Model_Brand::query()
        ->where('deleted', 0)
        ->order_by('name', 'asc')
        ->get();

        # SI SE OBTIENE INFORMACION
        if(!empty($brands))
        {
            # SE RECORRE ELEMENTO POR ELEMENTO
            foreach($brands as $brand)
            {
                # SE ALMACENA LA INFORMACION
                $brands_info[] = array(
                    'slug' => $brand->slug,
                    'name' => Str::truncate($brand->name, 30)
                );
            }
        }

        # SE BUSCA LA INFORMACION A TRAVES DEL MODELO
        $products = Model_Product::query()
        ->related('price')
        ->where('status', 1)
        ->where('deleted', 0)
        ->where('available', '>=', 0)
        ->where_open()
        ->where('name', 'like', '%'.$search.'%')
        ->or_where('code', 'like', '%'.$search.'%')
        ->where_close();

        # SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
        $config = array(
            'name'           => 'admin',
            'pagination_url' => Uri::base(false).substr($_SERVER['REQUEST_URI'], 1),
            'total_items'    => $products->count(),
            'per_page'       => $per_page,
            'num_links'		 => 5,
            'link_offset'	 => 0.50,
            'uri_segment'    => 'pagina',
            'show_first'     => true,
            'show_last'      => true,
        );

        # SE CREA LA INSTANCIA DE LA PAGINACION
        $pagination = Pagination::forge('products', $config);

        # DEPENDIENDO EL ORDEN SELECCIONADO
        switch($order_by)
        {
            case 'nuevos':
            # SE AGREGA LA CLAUSULA DEL ORDEN
            $products = $products->order_by('newproduct', 'desc')->order_by('id', 'desc');
            break;
            case 'a-z':
            # SE AGREGA LA CLAUSULA DEL ORDEN
            $products = $products->order_by('name', 'asc');
            break;
            case 'z-a':
            # SE AGREGA LA CLAUSULA DEL ORDEN
            $products = $products->order_by('name', 'desc');
            break;
        }

        # SE EJECUTA EL QUERY
        $products = $products->rows_limit($pagination->per_page)
        ->rows_offset($pagination->offset)
        ->get();

        # SI SE OBTIENE INFORMACION
        if(!empty($products))
        {
            # SE RECORRE ELEMENTO POR ELEMENTO
            foreach($products as $product)
            {

            # HELPER PARA RENDERIZAR EL BADGE DEL PRODUCTO
			$badge = Helper_Purchases::render_product_badge($product, 'admin');

                # SE ALMACENA LA INFORMACION
                $products_info[] = array(
                    'id'         => $product->id,
                    'product_id' => $product->id,
                    'slug'       => $product->slug,
                    'name'       => $product->name,
                    'code'       => $product->code,
                    'image'      => $product->image,
                    'newproduct' => $product->newproduct,
                    'soon'                  => $product->soon,
                    'temporarily_sold_out'  => $product->temporarily_sold_out,
                    'badge'                 => $badge,
                    'available'  => $product->available
                );
            }
        }

        # PASAR INFORMACION A LA VISTA
        $data['order_by']      = $order_by;
        $data['categories']    = $categories_info;
        $data['subcategories'] = $subcategories_info;
        $data['brands']        = $brands_info;
        $data['products']      = $products_info;
        $data['pagination']    = $pagination->render();

        # SE CARGA LA VISTA
        $this->template->title = 'Resultados de Búsqueda';
        $this->template->content = View::forge('admin/precotizacion/buscar', $data, false);
    }


    /**
    * Redirecciona al catálogo con filtro por categoría
    *
    * @param string|null $slug Slug de la categoría
    */
    public function action_categoria($slug = null)
    {
        // Validar que se proporcione el slug
        if (empty($slug)) {
            Session::set_flash('error', 'No se encontró la categoría.');
            Response::redirect('admin/precotizacion');
        }

        // Buscar la categoría por slug
        $category = Model_Category::query()
        ->where('slug', $slug)
        ->where('deleted', 0)
        ->get_one();

        // Validar existencia de la categoría
        if (empty($category)) {
            Session::set_flash('error', 'La categoría especificada no existe.');
            Response::redirect('admin/precotizacion');
        }

        // Redirigir al catálogo con el filtro aplicado
        Response::redirect('admin/precotizacion?categoria=' . urlencode($slug));
    }

    /**
    * Redirecciona al catálogo con filtro por subcategoría (grupo)
    *
    * @param string|null $slug Slug de la subcategoría
    */
    public function action_subcategoria($slug = null)
    {
        if (empty($slug)) {
            Session::set_flash('error', 'No se encontró el grupo.');
            Response::redirect('admin/precotizacion');
        }

        $subcategory = Model_Subcategory::query()
        ->where('slug', $slug)
        ->where('deleted', 0)
        ->get_one();

        if (empty($subcategory)) {
            Session::set_flash('error', 'El grupo especificado no existe.');
            Response::redirect('admin/precotizacion');
        }

        Response::redirect('admin/precotizacion?grupo=' . urlencode($slug));
    }

    /**
    * Redirecciona al catálogo con filtro por marca
    *
    * @param string|null $slug Slug de la marca
    */
    public function action_marca($slug = null)
    {
        if (empty($slug)) {
            Session::set_flash('error', 'No se encontró la marca.');
            Response::redirect('admin/precotizacion');
        }

        $brand = Model_Brand::query()
        ->where('slug', $slug)
        ->where('deleted', 0)
        ->get_one();

        if (empty($brand)) {
            Session::set_flash('error', 'La marca especificada no existe.');
            Response::redirect('admin/precotizacion');
        }

        Response::redirect('admin/precotizacion?marca=' . urlencode($slug));
    }

    /**
    * Redirecciona al catálogo con filtro por promociones
    * Requiere un socio autenticado
    */
    public function action_promociones()
    {
        $user_id = Auth::get('id');

        // Validar socio autenticado
        $partner = Model_Partner::query()
        ->where('user_id', $user_id)
        ->get_one();

        if (empty($partner)) {
            Session::set_flash('error', 'No se encontró el socio asignado al usuario.');
            Response::redirect('admin');
        }

        Response::redirect('admin/precotizacion?promocion=1');
    }

    /**
    * PRODUTO
    *
    * CARGA LA VISTA DE PRODUCTO
    *
    * @access  public
    * @return  void
    */
    public function action_producto($slug = '')
    {
        # SE INICIALIZAN LAS VARIABLES
        $data = array();

        # SE INICIALIZAN LOS ARREGLOS
        $prices_wholesales_info = array();
        $prices_amount_info     = array();
        $related_products_info  = array();
        $components_repeated    = array();
        $components_info        = array();
        $files_info             = array();
        $description            = '';
        $available              = 0;

        # SE BUSCA EL PRODUCTO
        $product = Model_Product::get_product(array('slug' => $slug));

        # SI SE ENCONTRO EL PRODUCTO
        if(empty($product))
        {
            # SE REDIRECCIONA A INICIO
            Response::redirect_back('/', 'refresh');
        }

        # SE INICIALIZAN LOS ARREGLOS
        $pictures_info = array();

        # SI SE ENCUENTRAN LAS IMAGENES
        if(!empty($product->galleries))
        {
            # SE BUSCAN LAS IMAGENES DEL PRODUCTO
            $galleries = Model_Products_Image::get_product_gallery($product->id);

            # SI SE OBTUVIERO LA GALERIA
            if(!empty($galleries))
            {
                # SE RECORRE FOTO POR FOTO
                foreach($galleries as $picture)
                {
                    # SE ALMACENA EN EL ARREGLO PICTURES_INFO
                    $pictures_info[] = array(
                        'image' => $picture->image,
                    );
                }
            }
        }

        # DEPENDIENDO EL CASO
        switch($product->price_per)
        {
            # UNIDAD
            case 'u':
            # SI EXISTEN RANGOS
            if(!empty($product->products_prices_wholesales))
            {
                # SE RECORRE ELEMENTO POR ELEMENTO
                foreach($product->products_prices_wholesales as $price_wholesale)
                {
                    # SE ALMACENA LA INFORMACION
                    $prices_wholesales_info[] = array(
                        'id'           => $price_wholesale->id,
                        'min_quantity' => $price_wholesale->min_quantity,
                        'max_quantity' => $price_wholesale->max_quantity,
                        'price'        => '$'.number_format($price_wholesale->price, '2', '.', ',')
                    );
                }
            }
            break;

            # MONTOS
            case 'm':
            # SE ALMACENA EL PRECIO DEL PRODUCTO
            $product_price = number_format(Model_Products_Price::get_price($product->id, Request::forge('sectorweb/user/get_type_customer', false)->execute()->response->body), 2, '.', ',');

            # SI UN MONTO RELACIONADO
            if(!empty($product->products_price_amount))
            {
                # SI EXISTE UN MONTO RELACIONADO
                if($product->products_price_amount->amount_id > 0)
                {
                    # SE OBTIENE LA INFORMACION A TRAVES DEL MODELO
                    $prices_amount = Model_Prices_Amount::query()
                    ->where('amount_id', $product->products_price_amount->amount_id)
                    ->get();

                    # SI UN MONTO RELACIONADO
                    if(!empty($prices_amount))
                    {
                        # SE RECORRE ELEMENTO POR ELEMENTO
                        foreach($prices_amount as $price_amount)
                        {
                            # SE ALMACENA LA INFORMACION
                            $prices_amount_info[] = array(
                                'id'         => $price_amount->id,
                                'min_amount' => '$'.number_format($price_amount->min_amount, '2', '.', ','),
                                'max_amount' => '$'.number_format($price_amount->max_amount, '2', '.', ','),
                                'price'      => '$'.number_format($product_price - (($price_amount->percentage / 100) * $product_price), '2', '.', '')
                            );
                        }
                    }
                }
            }
            break;
        }

        # HELPER PARA RENDERIZAR EL BADGE DEL PRODUCTO
		$badge = Helper_Purchases::render_product_badge($product, 'admin');

        # SE ALMACENAN LOS DATOS PARA LA VISTA
        $data['product_id']        = $product->id;
        $data['image']             = $product->image;
        $data['newproduct']        = $product->newproduct;
        $data['soon']        = $product->soon;
        $data['temporarily_sold_out']        = $product->temporarily_sold_out;
        $data['badge']        = $badge;
        $data['category']          = array(
            'name' => $product->category->name,
            'slug' => $product->category->slug
        );
        $data['subcategory']       = array(
            'name' => $product->subcategory->name,
            'slug' => $product->subcategory->slug
        );
        $data['brand']             = array(
            'name' => $product->brand->name,
            'slug' => $product->brand->slug
        );
        $data['name']              = $product->name;
        $data['code']              = $product->code;
        $data['sku']               = $product->sku;
        $data['price_per']         = $product->price_per;
        $data['prices_wholesales'] = $prices_wholesales_info;
        $data['prices_amount']     = $prices_amount_info;
        $data['price_facebook']    = number_format(Model_Products_Price::get_price($product->id, 1), 2, '.', ',');
        $data['available']         = ($product->available == 0) ? true : false;
        $data['description']       = nl2br($product->description);
        $data['galleries']         = $pictures_info;
        $data['files']             = $files_info;
        $data['related_products']  = $related_products_info;

        # SE LIMPIA LA DESCRIPCION
        $description = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $product->description);
        $description = preg_replace("[\n|\r|\n\r]", ' ', $description);
        $description = str_replace(PHP_EOL, '', $description);
        $description = str_replace('', '"', $description);

        # SE CARGA LA VISTA
        $this->template->title       = $product->name.' | Distribuidora Sajor';
        $this->template->description = $description;
        $this->template->content     = View::forge('admin/precotizacion/producto', $data, false);
    }
}
