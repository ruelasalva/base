<?php
/**
 * CONTROLADOR DE PRODUCTOS PARA MERCADO LIBRE
 * ---------------------------------------------------------
 * - Selección manual de productos del ERP
 * - Gestión de overrides (título, precio, stock, categoría)
 * - Acciones: vincular / publicar / actualizar / pausar
 * - Sincronización vía Service_Ml_Client
 * ---------------------------------------------------------
 * Compatible con los modelos:
 * - Model_Plataforma_Ml_Product
 * - Model_Product
 * - Model_Plataforma_Ml_Configuration
 */

class Controller_Admin_Plataforma_Ml_Productos extends Controller_Admin
{
       /**
     * LISTADO DE PRODUCTOS ERP + ESTADO ML
     */
    public function action_index()
    {
        // ==========================
        // PARAMETROS GET
        // ==========================
        $config_id = (int) Input::get('config_id', 0);
        $search    = trim(Input::get('search', ''));
        $sort      = Input::get('sort', 'name');   // campo default
        $dir       = strtolower(Input::get('dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        // ==========================
        // CONFIGURACIONES ML
        // ==========================
        $configs = Model_Plataforma_Ml_Configuration::query()
            ->order_by('name', 'asc')
            ->get();

        if (!$configs) {
            Session::set_flash('error', 'No hay cuentas de Mercado Libre configuradas.');
            return Response::redirect('admin/plataforma/ml');
        }

        // Si no viene config_id, tomar la primera
        if ($config_id <= 0) {
            $config = reset($configs);
        } else {
            $config = Model_Plataforma_Ml_Configuration::find($config_id);
            if (!$config) {
                $config = reset($configs);
                $config_id = $config->id;
            }
        }

        if (!$config_id) {
            $config_id = $config->id;
        }

        // ==========================
        // QUERY BASE DE PRODUCTOS
        // ==========================
        $products_query = Model_Product::query()
            ->related('brand')
            ->related('category')
            ->where('deleted', 0)
            ->where('status', 1);

        if ($search !== '') {
            $products_query
                ->and_where_open()
                    ->where('name', 'like', '%'.$search.'%')
                    ->or_where('code', 'like', '%'.$search.'%')
                    ->or_where('sku', 'like', '%'.$search.'%')
                    //->or_where_related('brand', 'name', 'like', '%'.$search.'%')
                    //->or_where_related('category', 'name', 'like', '%'.$search.'%')
                ->and_where_close();
        }

        // ==========================
        // ORDENAMIENTO
        // ==========================
        switch ($sort) {
            case 'code':
                $products_query->order_by('code', $dir);
                break;
            case 'brand':
                $products_query->order_by('brand.name', $dir);
                break;
            case 'category':
                $products_query->order_by('category.name', $dir);
                break;
            case 'available':
                $products_query->order_by('available', $dir);
                break;
            case 'name':
            default:
                $sort = 'name';
                $products_query->order_by('name', $dir);
                break;
        }

        // ==========================
        // PAGINACIÓN
        // ==========================
        $total_items = $products_query->count();

        $pagination = \Pagination::forge('ml_products', array(
            'pagination_url' => Uri::create('admin/plataforma/ml/productos', array(), array(
                'config_id' => $config_id,
                'search'    => $search,
                'sort'      => $sort,
                'dir'       => $dir,
            )),
            'total_items'    => $total_items,
            'per_page'       => 25,
            'uri_segment'    => 'pagina',
        ));

        $products = $products_query
            ->rows_offset($pagination->offset)
            ->rows_limit($pagination->per_page)
            ->get();

        // ==========================
        // MAPEO: PRODUCTO -> VÍNCULO ML
        // ==========================
        $links_by_product = array();

        if ($products) {
            $ids = array();
            foreach ($products as $p) {
                $ids[] = $p->id;
            }

            if ($ids) {
                $links = Model_Plataforma_Ml_Product::query()
                    ->where('configuration_id', $config_id)
                    ->where('product_id', 'in', $ids)
                    ->get();

                foreach ($links as $l) {
                    $links_by_product[$l->product_id] = $l;
                }
            }
        }

        // ==========================
        // RENDER
        // ==========================
        $this->template->title = 'Productos Mercado Libre';

        $this->template->content = View::forge('admin/plataformas/ml/productos/index', array(
            'configs'          => $configs,
            'config'           => $config,
            'products'         => $products,
            'links_by_product' => $links_by_product,
            'search'           => $search,
            'sort'             => $sort,
            'dir'              => $dir,
            'pagination'       => $pagination,
        ), false);
    }

    public function action_sin_publicar()
{
    $config_id   = (int) Input::param('config_id', 0);
    $search      = trim(Input::param('search', ''));
    $category_id = (int) Input::param('category_id', 0);
    $brand_id    = (int) Input::param('brand_id', 0);
    $stock_only  = (int) Input::param('stock_only', 0);
    $sort        = Input::param('sort', 'name');
    $dir         = strtolower(Input::param('dir', 'asc'));
    $per_page    = (int) Input::param('per_page', 50);

    if (!in_array($dir, ['asc', 'desc'])) {
        $dir = 'asc';
    }

    $allowed_per_page = [25, 50, 100, 200];
    if (!in_array($per_page, $allowed_per_page)) {
        $per_page = 50;
    }

    // ================================
    // Cargar configuración ML
    // ================================
    $config = Model_Plataforma_Ml_Configuration::find($config_id);

    if (!$config) {
        Session::set_flash('error', 'Configuración de Mercado Libre no encontrada.');
        return Response::redirect('admin/plataforma/ml');
    }

    // ================================
    // Catálogos ERP
    // ================================
    $categories = Model_Category::query()
        ->where('deleted', 0)
        ->order_by('name', 'asc')
        ->get();

    $brands = Model_Brand::query()
        ->where('deleted', 0)
        ->order_by('name', 'asc')
        ->get();

    // =====================================================
    // ACCIONES MASIVAS (POST)
    // =====================================================
    if (Input::method() === 'POST') {

        $bulk_action = Input::post('bulk_action', '');
        $selected    = Input::post('selected', []);

        if (!is_array($selected)) {
            $selected = [];
        }

        if (empty($selected)) {
            Session::set_flash('warning', 'Selecciona al menos un producto para aplicar la acción.');
            return Response::redirect(
                'admin/plataforma/ml/productos/sin_publicar?'.http_build_query(array(
                    'config_id'   => $config_id,
                    'search'      => $search,
                    'category_id' => $category_id,
                    'brand_id'    => $brand_id,
                    'stock_only'  => $stock_only,
                    'sort'        => $sort,
                    'dir'         => $dir,
                    'per_page'    => $per_page,
                ))
            );
        }

        switch ($bulk_action) {
            case 'desvincular':
                $count = 0;
                foreach ($selected as $link_id) {
                    $link = Model_Plataforma_Ml_Product::find((int)$link_id);
                    if ($link && (int)$link->configuration_id === (int)$config_id) {
                        $link->delete();
                        $count++;
                    }
                }
                Session::set_flash('success', "Se desvincularon {$count} productos de Mercado Libre.");
                return Response::redirect('admin/plataforma/ml/productos/sin_publicar?'.http_build_query(array(
                    'config_id'   => $config_id,
                    'search'      => $search,
                    'category_id' => $category_id,
                    'brand_id'    => $brand_id,
                    'stock_only'  => $stock_only,
                    'sort'        => $sort,
                    'dir'         => $dir,
                    'per_page'    => $per_page,
                )));

            case 'publicar':
                $count_ok  = 0;
                $count_err = 0;

                foreach ($selected as $link_id) {
                    $link = Model_Plataforma_Ml_Product::query()
                        ->related('product')
                        ->where('id', (int)$link_id)
                        ->where('configuration_id', $config_id)
                        ->get_one();

                    if (!$link || !$link->product) {
                        $count_err++;
                        continue;
                    }

                    try {
                        // Aquí podrías reutilizar la lógica de action_publicar()
                        $client  = new Service_Ml_Client($config);
                        $payload = Service_Ml_Helper::build_item_payload($link->product, $link);
                        $resp    = $client->post('/items', $payload, 'product', $link->product_id);

                        if (!empty($resp['id'])) {
                            $link->ml_item_id  = $resp['id'];
                            $link->last_error_at = null;
                            $link->save();
                            $count_ok++;
                        } else {
                            $count_err++;
                        }
                    } catch (\Exception $e) {
                        \Log::error('[ML][BULK_PUBLICAR] Error: '.$e->getMessage());
                        $link->last_error_at = time();
                        $link->save();
                        $count_err++;
                    }
                }

                Session::set_flash(
                    'info',
                    "Publicación masiva ejecutada. Exitosos: {$count_ok}, con error: {$count_err}."
                );

                return Response::redirect('admin/plataforma/ml/productos/sin_publicar?'.http_build_query(array(
                    'config_id'   => $config_id,
                    'search'      => $search,
                    'category_id' => $category_id,
                    'brand_id'    => $brand_id,
                    'stock_only'  => $stock_only,
                    'sort'        => $sort,
                    'dir'         => $dir,
                    'per_page'    => $per_page,
                )));

            case 'export':
                // Exportar CSV de seleccionados
                $links_export = Model_Plataforma_Ml_Product::query()
                    ->related('product')
                    ->related('product.category')
                    ->related('product.brand')
                    ->where('configuration_id', $config_id)
                    ->where('ml_item_id', null)
                    ->where('id', 'in', $selected)
                    ->get();

                $filename = 'ml_sin_publicar_'.date('Ymd_His').'.csv';

                $response = Response::forge();
                $response->set_header('Content-Type', 'text/csv; charset=UTF-8');
                $response->set_header(
                    'Content-Disposition',
                    'attachment; filename="'.$filename.'"'
                );

                $fh = fopen('php://output', 'w');
                // BOM UTF-8
                fwrite($fh, "\xEF\xBB\xBF");

                fputcsv($fh, array(
                    'ID Link',
                    'Código',
                    'Nombre',
                    'SKU',
                    'Categoría',
                    'Marca',
                    'Existencia',
                    'ML Enabled',
                    'Último error',
                ));

                foreach ($links_export as $l) {
                    $p = $l->product;

                    fputcsv($fh, array(
                        $l->id,
                        $p ? $p->code : '',
                        $p ? $p->name : '',
                        $p ? $p->sku : '',
                        $p && $p->category ? $p->category->name : '',
                        $p && $p->brand ? $p->brand->name : '',
                        $p ? $p->available : 0,
                        $l->ml_enabled ? '1' : '0',
                        $l->last_error_at ? date('Y-m-d H:i', $l->last_error_at) : '',
                    ));
                }

                fclose($fh);
                return $response;

            default:
                Session::set_flash('warning', 'Acción masiva no válida.');
                return Response::redirect('admin/plataforma/ml/productos/sin_publicar?'.http_build_query(array(
                    'config_id'   => $config_id,
                    'search'      => $search,
                    'category_id' => $category_id,
                    'brand_id'    => $brand_id,
                    'stock_only'  => $stock_only,
                    'sort'        => $sort,
                    'dir'         => $dir,
                    'per_page'    => $per_page,
                )));
        }
    }

    // ================================
    // QUERY BASE PARA LISTADO (GET)
    // ================================
    $query = Model_Plataforma_Ml_Product::query()
        ->where('configuration_id', $config_id)
        ->where('ml_item_id', null)
        ->related('product')
        ->related('product.category')
        ->related('product.brand')
        ->where('product.deleted', 0);

    if ($search) {
        $query->where_open()
            ->where('product.name', 'like', "%{$search}%")
            ->or_where('product.code', 'like', "%{$search}%")
            ->or_where('product.sku', 'like', "%{$search}%")
        ->where_close();
    }

    if ($category_id > 0) {
        $query->where('product.category_id', $category_id);
    }

    if ($brand_id > 0) {
        $query->where('product.brand_id', $brand_id);
    }

    if ($stock_only) {
        $query->where('product.available', '>', 0);
    }

    // ================================
    // ORDENAMIENTO
    // ================================
    switch ($sort) {
        case 'code':
            $query->order_by('product.code', $dir);
            break;
        case 'available':
            $query->order_by('product.available', $dir);
            break;
        case 'brand':
            $query->order_by('product.brand.name', $dir);
            break;
        case 'category':
            $query->order_by('product.category.name', $dir);
            break;
        default:
            $query->order_by('product.name', $dir);
            $sort = 'name';
            break;
    }

    // ================================
    // PAGINACIÓN
    // ================================
    $pagination_url = Uri::create('admin/plataforma/ml/productos/sin_publicar', [], [
        'config_id'   => $config_id,
        'search'      => $search,
        'category_id' => $category_id,
        'brand_id'    => $brand_id,
        'stock_only'  => $stock_only,
        'sort'        => $sort,
        'dir'         => $dir,
        'per_page'    => $per_page,
    ]);

    $pagination = Pagination::forge('ml_sin_publicar', [
        'pagination_url' => $pagination_url,
        'total_items'    => $query->count(),
        'per_page'       => $per_page,
        'uri_segment'    => 'page',
        'num_links'      => 4,
    ]);

    $links = $query
        ->rows_offset($pagination->offset)
        ->rows_limit($pagination->per_page)
        ->get();

    $this->template->title = 'Productos vinculados sin publicar';

    $this->template->content = View::forge(
        'admin/plataformas/ml/productos/sin_publicar',
        [
            'config'      => $config,
            'links'       => $links,
            'search'      => $search,
            'category_id' => $category_id,
            'brand_id'    => $brand_id,
            'stock_only'  => $stock_only,
            'per_page'    => $per_page,
            'categories'  => $categories,
            'brands'      => $brands,
            'sort'        => $sort,
            'dir'         => $dir,
            'pagination'  => $pagination,
        ],
        false
    );
}



    /**
     * VINCULAR UN PRODUCTO A MERCADO LIBRE
     * ---------------------------------------------------------
     */
    /**
 * VINCULAR PRODUCTO AL CATÁLOGO DE MERCADO LIBRE
 *
 * @param int $config_id   ID de plataforma_ml_configurations
 * @param int $product_id  ID del producto del ERP
 */
public function action_vincular($config_id = null, $product_id = null)
{
    // ==========================
    // VALIDACIONES BASE
    // ==========================
    if (!$config_id || !$product_id) {
        Session::set_flash('error', 'Solicitud inválida.');
        return Response::redirect('admin/plataforma/ml/productos');
    }

    $config = Model_Plataforma_Ml_Configuration::find($config_id);

    if (!$config) {
        Session::set_flash('error', 'Configuración de ML no encontrada.');
        return Response::redirect('admin/plataforma/ml/productos');
    }

    $product = Model_Product::find($product_id);

    if (!$product) {
        Session::set_flash('error', 'Producto no encontrado.');
        return Response::redirect('admin/plataforma/ml/productos?config_id='.$config_id);
    }

    // ==========================
    // VERIFICAR SI YA ESTÁ VINCULADO
    // ==========================
    $existe = Model_Plataforma_Ml_Product::query()
        ->where('product_id', $product_id)
        ->where('configuration_id', $config_id)
        ->get_one();

    if ($existe) {
        Session::set_flash('warning', 'Este producto ya está vinculado a esta cuenta ML.');
        return Response::redirect('admin/plataforma/ml/productos?config_id='.$config_id);
    }

    // ==========================
    // CREAR REGISTRO BASE ML
    // ==========================
    try {

        $link = Model_Plataforma_Ml_Product::forge([
            'product_id'                => $product_id,
            'configuration_id'          => $config_id,
            'ml_item_id'                => null,      // aún no se publica
            'ml_category_id'            => null,      // se asignará después
            'ml_enabled'                => 1,         // por default marcado como habilitado
            'ml_title_override'         => null,
            'ml_description_template_id'=> null,
            'ml_price_override'         => null,
            'ml_stock_override'         => null,
            'ml_listing_type_override'  => null,
            'ml_status_override'        => null,
            'last_sync_at'              => null,
            'last_error_at'             => null,
        ]);

        $link->save();

        // ==========================
        // REGISTRAR LOG DE NEGOCIO
        // ==========================
        Model_Plataforma_Ml_Log::forge([
            'configuration_id' => $config_id,
            'resource'         => 'product',
            'resource_id'      => $product_id,
            'operation'        => 'link',
            'status'           => 200,
            'message'          => 'Producto vinculado a ML correctamente'
        ])->save();

        Session::set_flash('success', 'Producto vinculado correctamente. Ahora puedes editar su configuración ML.');

    } catch (Exception $e) {

        // ==========================
        // REGISTRAR ERROR
        // ==========================
        Model_Plataforma_Ml_Error::forge([
            'configuration_id' => $config_id,
            'product_id'       => $product_id,
            'ml_item_id'       => null,
            'error_code'       => 500,
            'error_message'    => $e->getMessage(),
            'origin'           => 'action_vincular'
        ])->save();

        Session::set_flash('error', 'Error al vincular el producto: '.$e->getMessage());
    }

    return Response::redirect('admin/plataforma/ml/productos?config_id='.$config_id);
}


    /**
     * DESVINCULAR PRODUCTO
     */
    /**
 * DESVINCULAR PRODUCTO DE MERCADO LIBRE
 *
 * @param int|null $id  ID del registro plataforma_ml_products
 */
public function action_desvincular($id = null)
{
    // ==========================
    // VALIDAR EXISTENCIA
    // ==========================
    $link = Model_Plataforma_Ml_Product::find($id);

    if (!$link) {
        Session::set_flash('error', 'No se encontró el vínculo ML.');
        return Response::redirect('admin/plataforma/ml/productos');
    }

    // Obtenemos config para regresar correctamente al listado
    $config_id = $link->configuration_id;

    // Guardamos datos para el log antes de borrar
    $product_id = $link->product_id;
    $ml_item_id = $link->ml_item_id;

    try {

        // ============================================
        // ELIMINACIÓN FINAL DEL VÍNCULO
        // ============================================
        $link->delete();

        // ============================================
        // REGISTRAR LOG DE NEGOCIO
        // ============================================
        Model_Plataforma_Ml_Log::forge([
            'configuration_id' => $config_id,
            'resource'         => 'product',
            'resource_id'      => $product_id,
            'operation'        => 'unlink',
            'status'           => 200,
            'message'          => 'Producto desvinculado de ML (item_id='.$ml_item_id.')'
        ])->save();

        Session::set_flash('success', 'Producto desvinculado correctamente de Mercado Libre.');

    } catch (Exception $e) {

        // ============================================
        // SI ALGO FALLA, REGISTRAMOS ERROR
        // ============================================
        Model_Plataforma_Ml_Error::forge([
            'configuration_id' => $config_id,
            'product_id'       => $product_id,
            'ml_item_id'       => $ml_item_id,
            'error_code'       => 500,
            'error_message'    => $e->getMessage(),
            'origin'           => 'action_desvincular'
        ])->save();

        Session::set_flash('error', 'Error al desvincular: '.$e->getMessage());
    }

    return Response::redirect('admin/plataforma/ml/productos?config_id='.$config_id);
}


    /**
     * EDITAR OVERRIDES DE PUBLICACIÓN
     * ---------------------------------------------------------
     * - Título alterno
     * - Categoría ML
     * - Precio / Stock override
     * - Listing type
     */
    /**
 * EDITAR CONFIGURACIÓN ML DEL PRODUCTO
 *
 * @param int|null $id  ID del registro en plataforma_ml_products
 */
public function action_editar($id = null)
{
    // ==========================
    // VALIDAR EXISTENCIA
    // ==========================
    $link = Model_Plataforma_Ml_Product::find($id);

    if (!$link) {
        Session::set_flash('error', 'Configuración ML no encontrada.');
        return Response::redirect('admin/plataforma/ml/productos');
    }

    // Cargar producto real del ERP
    $product = Model_Product::find($link->product_id);

    if (!$product) {
        Session::set_flash('error', 'Producto no encontrado en ERP.');
        return Response::redirect('admin/plataforma/ml/productos');
    }

    // Configuración ML correspondiente
    $config = Model_Plataforma_Ml_Configuration::find($link->configuration_id);


    // =====================================================
    // PROCESO POST: GUARDAR CAMBIOS
    // =====================================================
    if (Input::method() == 'POST') {

        // --------------------------
        // VALIDACIÓN
        // --------------------------
        $val = Validation::forge();

        $val->add_field('ml_category_id', 'Categoría ML', 'max_length[50]');
        $val->add_field('ml_title_override', 'Título ML', 'max_length[60]');
        $val->add_field('ml_description_template_id', 'Plantilla', 'max_length[50]');
        $val->add_field('ml_price_override', 'Precio Override', 'numeric_between[0,999999]');
        $val->add_field('ml_stock_override', 'Stock Override', 'valid_string[numeric]');
        $val->add_field('ml_listing_type_override', 'Tipo', 'max_length[50]');
        $val->add_field('ml_status_override', 'Estado', 'max_length[20]');
        $val->add_field('ml_enabled', 'Habilitado', 'required');

        if ($val->run()) {

            // --------------------------
            // ASIGNAR CAMPOS
            // --------------------------
            $link->ml_category_id            = Input::post('ml_category_id');
            $link->ml_title_override         = Input::post('ml_title_override');
            $link->ml_description_template_id= Input::post('ml_description_template_id');
            $link->ml_price_override         = Input::post('ml_price_override');
            $link->ml_stock_override         = Input::post('ml_stock_override');
            $link->ml_listing_type_override  = Input::post('ml_listing_type_override');
            $link->ml_status_override        = Input::post('ml_status_override');
            $link->ml_enabled                = Input::post('ml_enabled');

            // Fecha de última edición ML
            $link->last_sync_at = time();

            if ($link->save()) {
                Session::set_flash('success', 'Configuración ML actualizada correctamente.');
                return Response::redirect('admin/plataforma/ml/productos?config_id='.$config->id);
            }

            Session::set_flash('error', 'Error al guardar los cambios.');
        }
        else {
            Session::set_flash('error', 'Corrige los errores del formulario.');
        }
    }


    // =====================================================
    // CARGAR VISTA
    // =====================================================
    $this->template->title = 'Editar Configuración ML – '.$product->name;

    $this->template->content = View::forge(
        'admin/plataformas/ml/productos/editar',
        [
            'link'     => $link,
            'product'  => $product,
            'config'   => $config,
        ],
        false
    );
}


    /**
     * (PENDIENTE PARA EL SIGUIENTE PASO)
     * PUBLICAR PRODUCTO EN ML
     */
    /**
 * PUBLICAR PRODUCTO EN MERCADO LIBRE (POST /items)
 *
 * @param int|null $id  ID de plataforma_ml_products
 */
public function action_publicar($id = null)
{
    $link = Model_Plataforma_Ml_Product::find($id);

    if (!$link) {
        Session::set_flash('error', 'Vínculo ML no encontrado.');
        return Response::redirect('admin/plataforma/ml');
    }

    $product = Model_Product::find($link->product_id);
    $config  = Model_Plataforma_Ml_Configuration::find($link->configuration_id);

    if (!$product || !$config) {
        Session::set_flash('error', 'Error cargando producto y/o configuración.');
        return Response::redirect('admin/plataforma/ml');
    }

    try {

        $client = new Service_Ml_Client($config);
        $payload = Service_Ml_Helper::build_item_payload($product, $link, $config);

        $result = $client->post('/items', $payload);

        if (!isset($result['id'])) {
            throw new Exception("Respuesta sin ID");
        }

        // Guardar Item
        $link->ml_item_id   = $result['id'];
        $link->last_sync_at = time();
        $link->save();

        // LOG CORRECTO
        Service_Ml_Helper::log(
            $config,
            "product",
            $product->id,
            "publish",
            200,
            "Producto publicado correctamente (Item {$result['id']})."
        );

        Session::set_flash('success', "Producto publicado correctamente.");
    }
    catch (Exception $e) {

        // REGISTRO DE ERROR
        Service_Ml_Helper::error(
            $config,
            $product->id,
            null,
            500,
            $e->getMessage(),
            "action_publicar"
        );

        Session::set_flash('error', 'Error publicando: '.$e->getMessage());
    }

    return Response::redirect('admin/plataforma/ml/productos?config_id='.$config->id);
}


    /**
     * (PENDIENTE)
     * ACTUALIZAR STOCK / PRECIO EN ML
     */
    /**
 * ACTUALIZAR PUBLICACIÓN EN MERCADO LIBRE (PUT /items/{id})
 *
 * @param int|null $id  ID de plataforma_ml_products
 */
public function action_actualizar($id = null)
{
    $link = Model_Plataforma_Ml_Product::find($id);

    if (!$link || empty($link->ml_item_id)) {
        Session::set_flash('error', 'Este producto no está publicado en ML.');
        return Response::redirect('admin/plataforma/ml');
    }

    $product = Model_Product::find($link->product_id);
    $config  = Model_Plataforma_Ml_Configuration::find($link->configuration_id);

    try {
        $client = new Service_Ml_Client($config);

        // Construir payload actualizado
        $payload = Service_Ml_Helper::build_item_payload($product, $link, $config);

        // Enviar actualización PATCH
        $result = $client->put('/items/'.$link->ml_item_id, $payload);

        $link->last_sync_at = time();
        $link->save();

        Model_Plataforma_Ml_Log::registrar_log(
            $config->id,
            "ACTUALIZADO",
            "Item ".$link->ml_item_id." actualizado correctamente."
        );

        Session::set_flash('success', 'Producto actualizado correctamente.');
    }
    catch (Exception $e) {

        Model_Plataforma_Ml_Log::legistrar_log(
            $config->id,
            "ERROR_ACTUALIZAR",
            $e->getMessage(),
            $product->id
        );

        Session::set_flash('error', 'Error actualizando: '.$e->getMessage());
    }

    return Response::redirect('admin/plataforma/ml/productos?config_id='.$config->id);
}


/**
 * CAMBIAR ESTADO DE PUBLICACIÓN EN MERCADO LIBRE (PUT /items/{id})
 *
 * @param int $id     ID de plataforma_ml_products
 * @param string $status  Estado solicitado: active | paused | closed
 */
public function action_status($id = null, $status = null)
{
    // ============================
    // VALIDAR REGISTRO ML
    // ============================
    $link = Model_Plataforma_Ml_Product::find($id);

    if (!$link) {
        Session::set_flash('error', 'Vínculo ML no encontrado.');
        return Response::redirect('admin/plataforma/ml/productos');
    }

    $product = Model_Product::find($link->product_id);

    if (!$product) {
        Session::set_flash('error', 'Producto del ERP no encontrado.');
        return Response::redirect('admin/plataforma/ml/productos');
    }

    $config = Model_Plataforma_Ml_Configuration::find($link->configuration_id);


    // ============================
    // VALIDAR ITEM PUBLICADO
    // ============================
    if (empty($link->ml_item_id)) {
        Session::set_flash('error', 'Este producto aún no está publicado en Mercado Libre.');
        return Response::redirect('admin/plataforma/ml/productos?config_id='.$config->id);
    }

    // ============================
    // VALIDAR ESTADO SOLICITADO
    // ============================
    $validos = ['active', 'paused', 'closed'];

    if (!in_array($status, $validos)) {
        Session::set_flash('error', 'Estado inválido.');
        return Response::redirect('admin/plataforma/ml/productos?config_id='.$config->id);
    }

    // ============================
    // PREPARAR PAYLOAD ML
    // ============================
    try {

        $client = new Service_Ml_Client($config);

        $payload = [
            "status" => $status
        ];

        $response = $client->put(
            "/items/".$link->ml_item_id,
            $payload,
            "product",
            $link->product_id
        );

        if (!isset($response['id'])) {
            throw new Exception("Respuesta inválida de ML: ".json_encode($response));
        }


        // Guardar estado override local
        $link->ml_status_override = $status;
        $link->last_sync_at = time();
        $link->save();


        // LOG de negocio
        Model_Plataforma_Ml_Log::forge([
            'configuration_id' => $config->id,
            'resource'         => 'product',
            'resource_id'      => $link->product_id,
            'operation'        => 'status_change',
            'status'           => 200,
            'message'          => "Estado cambiado a '{$status}' para ML item ".$link->ml_item_id
        ])->save();

        Session::set_flash('success', 'Estado actualizado a: '.$status);

    } catch (Exception $e) {

        // LOG de error
        Model_Plataforma_Ml_Error::forge([
            'configuration_id' => $config->id,
            'product_id'       => $link->product_id,
            'ml_item_id'       => $link->ml_item_id,
            'error_code'       => 500,
            'error_message'    => $e->getMessage(),
            'origin'           => 'action_status'
        ])->save();

        Session::set_flash('error', 'Error al cambiar estado: '.$e->getMessage());
    }

    return Response::redirect('admin/plataforma/ml/productos?config_id='.$config->id);
}

/**
 * PREVIEW DEL PAYLOAD ML
 * Permite ver el JSON final antes de publicar
 */
public function action_preview($id = null)
{
    // ============================
    // VALIDACIONES
    // ============================
    $link = Model_Plataforma_Ml_Product::find($id);

    if (!$link) {
        return Response::forge(json_encode([
            'error' => 'Producto ML no encontrado.'
        ]), 404)->set_header('Content-Type', 'application/json');
    }

    $product = Model_Product::find($link->product_id);
    $config  = Model_Plataforma_Ml_Configuration::find($link->configuration_id);

    if (!$product || !$config) {
        return Response::forge(json_encode([
            'error' => 'Error cargando producto o configuración.'
        ]), 404)->set_header('Content-Type', 'application/json');
    }

    // ============================
    // CONSTRUIR PAYLOAD
    // ============================
    $payload = Service_Ml_Helper::build_item_payload($product, $link, $config);

    // ============================
    // DEVOLVER JSON PURO (sin vista)
    // ============================
    return Response::forge(
        json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    )->set_header('Content-Type', 'application/json');
}

/**
 * ATRIBUTOS ML DEL PRODUCTO
 * Pantalla dedicada
 */
public function action_atributos_ml($id = null)
{
    $link = Model_Plataforma_Ml_Product::find($id);

    if (!$link) {
        Session::set_flash('error', 'Vínculo ML no encontrado.');
        return Response::redirect('admin/plataforma/ml');
    }

    $product = Model_Product::find($link->product_id);
    $config  = Model_Plataforma_Ml_Configuration::find($link->configuration_id);

    if (!$product || !$config) {
        Session::set_flash('error', 'Error cargando producto o configuración.');
        return Response::redirect('admin/plataforma/ml');
    }

    $this->template->title = "Atributos ML – ".$product->name;

    $this->template->content = View::forge(
        'admin/plataformas/ml/productos/atributos',
        [
            'product' => $product,
            'link'    => $link,
            'config'  => $config,
        ],
        false
    );
}



}
