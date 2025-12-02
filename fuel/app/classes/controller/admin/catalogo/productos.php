<?php

/**
 * CONTROLADOR ADMIN_CATALOGO_PRODUCTOS
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Catalogo_Productos extends Controller_Admin
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
	 * MUESTRA UNA LISTADO DE REGISTROS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_index($search = '')
	{
		# SE INICIALIZAN LAS VARIABLES
		$data          = array();
		$products_info = array();
		$per_page      = 100;

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$products = Model_Product::query()
		->where('deleted', 0);

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
			$products = $products->where(DB::expr("CONCAT(`t0`.`name`,`code`,`sku`)"), 'like', '%'.$search.'%');
		}

		# SE ESTABLECE LA CONFIGURACION DE LA PAGINACION
		$config = array(
			'name'           => 'admin',
			'pagination_url' => Uri::current(),
			'total_items'    => $products->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
			'show_first'     => true,
    		'show_last'      => true,
		);

		# SE CREA LA INSTANCIA DE LA PAGINACION
		$pagination = Pagination::forge('products', $config);

		# SE EJECUTA EL QUERY
		$products = $products->order_by('id', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($products))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($products as $product)
			{
				# SE ALMACENA LA INFORMACION
				$products_info[] = array(
					'id'            => $product->id,
					'code'          => $product->code,
					'codebar'       => $product->codebar,
					'image'         => $product->image,
					'slug'         	=> $product->slug,
					'name'          => Str::truncate($product->name, '60', '...'),
					'name_complete' => $product->name,
					'brand'    	    => Str::truncate($product->brand->name, '15','...'),
					'category'      => $product->category->name,
					'available'     => $product->available,
					'weight'        => $product->weight,
					'price_1'       => '$'.number_format(Model_Products_Price::get_price($product->id, 1), '2', '.', ','),
					'price_2'       => '$'.number_format(Model_Products_Price::get_price($product->id, 2), '2', '.', ','),
					'price_3'       => '$'.number_format(Model_Products_Price::get_price($product->id, 3), '2', '.', ','),
					'status'        => $product->status,
					'status_index'  => $product->status_index,
					'soon'  		=> $product->soon,
					'newproduct'  	=> $product->newproduct,
					'temporarily_sold_out'  => $product->temporarily_sold_out
				);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['products']   = $products_info;
		$data['search']     = str_replace('%', ' ', $search);
		$data['pagination'] = $pagination->render();

		# SE CARGA LA VISTA
		$this->template->title   = 'Productos';
		$this->template->content = View::forge('admin/catalogo/productos/index', $data, false);
	}


	/**
	 * CSV
	 *
	 * PERMITE DAR DE ALTA CLIENTES A TRAVES DE UN CSV
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_csv()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data            = array();
		$fields          = array('file');
		$products_errors = array();
		$news_count      = 0;
		$edits_count     = 0;
		$errors_count    = 0;
		$errors_products = '';
		$msg             = '';

		# SE RECORRE CAMPO POR CAMPO
		foreach($fields as $field)
		{
			# SE CREAN LAS CLASES DEL CAMPO
			$classes[$field] = array (
				'form-group'   => null,
				'form-control' => null,
			);
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE OBTIENE LA REFERENCIA DEL ARCHIVO
			$file = $_FILES['file']['name'];

			# SI EL USUARIO SUBE EL ARCHIVO
			if(!empty($file))
			{
				# SE ESTABLECE LA CONFIGURACION
				$config = array(
					'path'          => DOCROOT.DS.'assets/csv',
					'randomize'     => true,
					'ext_whitelist' => array('csv'),
					'max_size'      => 20971520,
				);

				# SE INICIALIZA EL PROCESO UPLOAD CON LA CONFIGURACION ESTABLECIDA
				Upload::process($config);

				# SI EL ARCHIVO ES VALIDO
				if(Upload::is_valid())
				{
					# SE SUBE EL ARCHIVO
					Upload::save();

					# SE OBTIENE LA INFORMACION DEL ARCHIVO
					$value = Upload::get_files();

					# SE ALMACENA EL NOMBRE DE LOS ARCHIVOS
					$file = $value[0]['saved_as'];

					# OBTENEMOS EL ARCHIVO
					$file_open = File::get(DOCROOT.DS.'assets/csv/'.$file);

					# OBTENEMOS EL CONTENIDO
					$content = $file_open->read(true);

					# SI HAY CONTENIDO
					if($content != '')
					{
						# SE CAMBIA EL FINAL DE LINEA DEL ARCHIVO POR UN CARACTER
						$content = str_replace("\n", '#', $content);

						# SE CREA UN ARREGLO CON DATA POR CADA RENGLON DEL ARCHIVO
						$content = explode('#', $content);

						# POR CADA RENGLON DEL ARCHIVO
						foreach($content as $key => $row)
						{
							# SI NO ES LA PRIMERA FILA
							if($key > 0)
							{
								# SE CORTA CAMPO POR CAMPO A PARTIR DE LA COMA
								$exp = explode(',', $row);

								# SI EXISTEN TODOS LOS CAMPOS
								if(count($exp) == 17)
								{
									# SE ALMACENA LA INFORMACION DE LA FILA
									$product_info = array(
										'code'           => $exp[0],
										'category_id'    => (int)$exp[1],
										'subcategory_id' => (int)$exp[2],
										'brand_id'       => (int)$exp[3],
										'name'           => $exp[4],
										'codebar'        => (int)$exp[5],
										'image'          => $exp[6],
										'description'    => $exp[7],
										'original_price' => (float)$exp[8],
										'available'      => (int)$exp[9],
										'weight'         => (float)$exp[10],
										'status'         => (int)$exp[11],
										'status_index'   => (int)$exp[12],
										'deleted'        => (int)$exp[13],
										'price_1'        => (float)$exp[14],
										'price_2'        => (float)$exp[15],
										'price_3'        => (float)$exp[16],
									);

									# SE CREA LA VALIDACION DE LOS CAMPOS
									$val = Validation::forge('product');
									$val->add_callable('Rules');
									$val->add_field('code', 'code', 'required|min_length[1]|max_length[255]');
									$val->add_field('category_id', 'category_id', 'required|valid_string[numeric]|numeric_min[1]');
									$val->add_field('subcategory_id', 'subcategory_id', 'required|valid_string[numeric]|numeric_min[1]');
									$val->add_field('brand_id', 'brand_id', 'required|valid_string[numeric]|numeric_min[1]');
									$val->add_field('name', 'name', 'required|min_length[1]|max_length[255]');
									$val->add_field('codebar', 'codebar', 'required|valid_string[numeric]|numeric_min[0]');
									$val->add_field('image', 'image', 'required|min_length[1]|max_length[255]');
									$val->add_field('description', 'description', 'required|min_length[1]');
									$val->add_field('original_price', 'original_price', 'required|float');
									$val->add_field('available', 'available', 'required|valid_string[numeric]|numeric_min[0]');
									$val->add_field('weight', 'weight', 'required|float');
									$val->add_field('status', 'status', 'required|valid_string[numeric]|numeric_min[0]');
									$val->add_field('status_index', 'status_index', 'required|valid_string[numeric]|numeric_min[0]');
									$val->add_field('deleted', 'deleted', 'required|valid_string[numeric]|numeric_min[0]');
									$val->add_field('price_1', 'price_1', 'required|float');
									$val->add_field('price_2', 'price_2', 'required|float');
									$val->add_field('price_3', 'price_3', 'required|float');

									# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
									if($val->run($product_info))
									{
										# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
										$check_product = Model_Product::query()
										->where('code', $val->validated('code'))
										->where('deleted', 0)
										->get_one();

										# SI NO SE OBTIENE INFORMACION
										if(empty($check_product))
										{
											# SE INICIALIZA EL CONTADOR
											$count = 1;

											# SE GENERA EL SLUG A PARTIR DEL NOMBRE
											$original_slug = Inflector::friendly_title($val->validated('name'), '-', true);
											$slug_temp     = Inflector::friendly_title($val->validated('name'), '-', true);

											# HACER HASTA NO ENCONTRAR EL SLUG REPETIDO
											do{
												# SE VERIFICA SI EXISTE EL SLUG EN LA BASE DE DATOS
												$exist = Model_Product::query()
												->where('slug', $slug_temp)
												->get();

												# SI EL SLUG EXISTE
												if(!empty($exist))
												{
													# SE LE AGREGA EL VALOR DEL CONTADOR AL FINAL DE SLUG
													$slug_temp = $original_slug.'-'.$count;

													# SE INCREMENTA EL CONTADOR
													$count++;
												}
											}while(!empty($exist));
											# FIN DE LA VERIFICACION DEL SLUG

											# SE CREA EL MODELO CON LA INFORMACION
											$product = new Model_Product(array(
												'category_id'    => $val->validated('category_id'),
												'subcategory_id' => $val->validated('subcategory_id'),
												'brand_id'       => $val->validated('brand_id'),
												'slug'           => $slug_temp,
												'name'           => $val->validated('name'),
												'code'           => $val->validated('code'),
												'codebar'        => $val->validated('codebar'),
												'image'          => $val->validated('image'),
												'description'    => $val->validated('description'),
												'original_price' => $val->validated('original_price'),
												'available'      => $val->validated('available'),
												'weight'         => $val->validated('weight'),
												'price_per'      => '',
												'status'         => $val->validated('status'),
												'status_index'   => $val->validated('status_index'),
												'deleted'        => $val->validated('deleted')
											));

											# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
											if($product->save())
											{
												# SE CREA EL MODELO CON LA INFORMACION
												$product_price_1 = new Model_Products_Price(array(
													'type_id'    => 1,
													'product_id' => $product->id,
													'price'      => $val->validated('price_1')
												));

												# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
												$product_price_1->save();

												# SE CREA EL MODELO CON LA INFORMACION
												$product_price_2 = new Model_Products_Price(array(
													'type_id'    => 2,
													'product_id' => $product->id,
													'price'      => $val->validated('price_2')
												));

												# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
												$product_price_2->save();

												# SE CREA EL MODELO CON LA INFORMACION
												$product_price_3 = new Model_Products_Price(array(
													'type_id'    => 3,
													'product_id' => $product->id,
													'price'      => $val->validated('price_3')
												));

												# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
												$product_price_3->save();

												# SE INCREMENTA EL CONTADOR
												$news_count++;
											}
										}
										else
										{
											# SE INICIALIZA EL CONTADOR
											$count = 1;

											# SE GENERA EL SLUG A PARTIR DEL NOMBRE
											$original_slug = Inflector::friendly_title($val->validated('name'), '-', true);
											$slug_temp     = Inflector::friendly_title($val->validated('name'), '-', true);

											# HACER HASTA NO ENCONTRAR EL SLUG REPETIDO
											do{
												# SE VERIFICA SI EXISTE EL SLUG EN LA BASE DE DATOS
												$exist = Model_Product::query()
												->where('slug', $slug_temp)
												->get_one();

												# SI EL SLUG EXISTE
												if(!empty($exist))
												{
													# SI EL ID DEL SLUG ES DIFERENTE AL ID ORIGINAL
													if($exist->id != $check_product->id)
													{
														# SE LE AGREGA EL VALOR DEL CONTADOR AL FINAL DE SLUG
														$slug_temp = $original_slug.'-'.$count;

														# SE INCREMENTA EL CONTADOR
														$count++;
													}
													else
													{
														# SE ROMPE EL CICLO DEL DO-WHILE
														break;
													}
												}
											}while(!empty($exist));
											# FIN DE LA VERIFICACION DEL SLUG

											# SE ESTEBLECE LA NUEVA INFORMACION
											$check_product->category_id    = $val->validated('category_id');
											$check_product->subcategory_id = $val->validated('subcategory_id');
											$check_product->brand_id       = $val->validated('brand_id');
											$check_product->slug           = $slug_temp;
											$check_product->name           = $val->validated('name');
											$check_product->code           = $val->validated('code');
											$check_product->codebar        = $val->validated('codebar');
											$check_product->image          = $val->validated('image');
											$check_product->description    = $val->validated('description');
											$check_product->original_price = $val->validated('original_price');
											$check_product->available      = $val->validated('available');
											$check_product->weight         = $val->validated('weight');
											$check_product->price_per      = '';
											$check_product->status         = $val->validated('status');
											$check_product->status_index   = $val->validated('status_index');
											$check_product->deleted        = $val->validated('deleted');

											# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
											if($check_product->save())
											{
												# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
												$product_price_1 = Model_Products_Price::query()
												->where('product_id', $check_product->id)
												->where('type_id', 1)
												->get_one();

												# SE ESTEBLECE LA NUEVA INFORMACION
												$product_price_1->price = $val->validated('price_1');

												# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
												$product_price_1->save();

												# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
												$product_price_2 = Model_Products_Price::query()
												->where('product_id', $check_product->id)
												->where('type_id', 2)
												->get_one();

												# SE ESTEBLECE LA NUEVA INFORMACION
												$product_price_2->price = $val->validated('price_2');

												# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
												$product_price_2->save();

												# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
												$product_price_3 = Model_Products_Price::query()
												->where('product_id', $check_product->id)
												->where('type_id', 3)
												->get_one();

												# SE ESTEBLECE LA NUEVA INFORMACION
												$product_price_3->price = $val->validated('price_3');

												# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
												$product_price_3->save();

												# SE INCREMENTA EL CONTADOR
												$edits_count++;
											}
										}
									}
									else
									{
										# SE ALMACENA LA INFORMACION
										$temp_key = $key + 1;

										# SE ALMACENA LA INFORMACION DEL ERROR
										$errors_products .= 'Existe un error en la fila <strong>'.$temp_key.'</strong> y no se registró su información.<br>';

										# SE INCREMENTA EL CONTADOR
										$errors_count++;
									}
								}
								else
								{
									# SI EXISTE POR LO MENOS UN CAMPO
									if(count($exp) > 1)
									{
										# SE ALMACENA LA INFORMACION
										$temp_key = $key + 1;

										# SE ALMACENA LA INFORMACION DEL ERROR
										$errors_products .= 'La fila <strong>'.$temp_key.'</strong> no cumple con los campor requeridos.<br>';

										# SE INCREMENTA EL CONTADOR
										$errors_count++;
									}

								}
							}
						}

						# SI SE ELIMINA EL ARCHIVO
						if($file_open->delete())
						{
							# SI SE AGREGO O ACTUALIZO UN REGISTRO
							if($news_count > 0 || $edits_count > 0)
							{
								# SE CONCATENA LA INFORMACION DEL MENSAJE
								$msg .= '¡Se cargó la información con éxito!<br><br>';

								# SI SE AGREGO UN REGISTRO
								if($news_count > 0)
								{
									# SE CONCATENA LA INFORMACION DEL MENSAJE
									$msg .= 'Se agregaron <strong>'.$news_count.'</strong> productos<br>';
								}

								# SI SE ACTUALIZO UN REGISTRO
								if($edits_count > 0)
								{
									# SE CONCATENA LA INFORMACION DEL MENSAJE
									$msg .= 'Se actualizaron <strong>'.$edits_count.'</strong> productos<br>';
								}

								# SI HUBO ALGUN ERROR
								if($errors_count > 0)
								{
									# SE CONCATENA LA INFORMACION DEL MENSAJE
									$msg .= '<br>Hubo <strong>'.$errors_count.'</strong> problemas:<br>';

									# SE CONCATENA LA INFORMACION DEL MENSAJE
									$msg .= $errors_products;
								}

								# SE ESTABLECE EL MENSAJE DE ERROR
								Session::set_flash('success', $msg);
							}
							else
							{
								# SE CONCATENA LA INFORMACION DEL MENSAJE
								$msg .= 'No se pudo cargar la información del CSV.<br><br>';

								# SI HUBO ALGUN ERROR
								if($errors_count > 0)
								{
									# SE CONCATENA LA INFORMACION DEL MENSAJE
									$msg .= '<br>Hubo <strong>'.$errors_count.'</strong> problemas:<br>';

									# SE CONCATENA LA INFORMACION DEL MENSAJE
									$msg .= $errors_products;
								}

								# SE ESTABLECE EL MENSAJE DE ERROR
								Session::set_flash('error', $msg);
							}
						}
					}
					else
					{
						# SE ESTABLECE EL MENSAJE DE ERROR
						Session::set_flash('error', 'El archivo CSV está vacío.');
					}
				}
				else
				{
					# SE ESTABLECE EL MENSAJE DE ERROR
					Session::set_flash('error', 'Solo están permitidos archivos con extensión <b>.csv</b>.');
				}
			}
			else
			{
				# SE ESTABLECE EL MENSAJE DE ERROR
				Session::set_flash('error', 'Asegúrate de subir el archivo CSV.');
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Importar CSV';
		$this->template->content = View::forge('admin/catalogo/productos/csv', $data);
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
				Response::redirect('admin/catalogo/productos/index/'.$search);
			}
			else
			{
				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/catalogo/productos');
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}
	}


	/**
	 * AGREGAR
	 *
	 * PERMITE AGREGAR UN REGISTRO A LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_agregar()
	{
		# SE INICIALIZAN LAS VARIABLES
		$data             = array();
		$classes          = array();
		$fields           = array('name', 'category', 'subcategory','brand', 'image', 'description', 'code', 'sku', 'claveprodserv', 'claveunidad', 'codebar', 'original_price', 'price_1', 'price_2', 'price_3', 'available', 'weight', 'price_per', 'amount');
		$category_opts    = array();
		$subcategory_opts = array();
		$brand_opts       = array();
		$amount_opts      = array();
		$price_per_opts   = array(
			''  => 'Lista de precios',
			'u' => 'Unidades',
			'm' => 'Montos',
		);

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
			$val = Validation::forge('product');
			$val->add_callable('Rules');
			$val->add_field('category', 'categoría', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('subcategory', 'grupo', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('brand', 'marca', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');
			$val->add_field('code', 'código', 'required|min_length[1]|max_length[255]');
			$val->add_field('sku', 'sku', 'min_length[1]|max_length[255]');
			$val->add_field('claveprodserv', 'codigo sat', 'min_length[1]|max_length[255]');
			$val->add_field('claveunidad', 'unidad sat', 'min_length[1]|max_length[255]');
			$val->add_field('codebar', 'código de barras', 'min_length[1]|max_length[255]');
			$val->add_field('image', 'imagen', 'required|min_length[1]');
			$val->add_field('description', 'descripción', 'required|min_length[1]');
			$val->add_field('available', 'cantidad disponible', 'valid_string[numeric]|numeric_min[0]');
			$val->add_field('weight', 'peso producto', 'required|float');
			$val->add_field('price_per', 'precio por', 'min_length[0]');
			$val->add_field('original_price', 'precio original', 'required|float');
			$val->add_field('price_1', 'precio (normal)', 'required|float');
			$val->add_field('price_2', 'precio (mayorista #1)', 'required|float');
			$val->add_field('price_3', 'precio (mayorista #3)', 'required|float');

			# DEPENDIENDO DEL CAMPO PRECIO POR
			switch(Input::post('price_per'))
			{
				# MONTO
				case 'm':
					# SE AGREGA LA VALIDACION DEL CAMPO
					$val->add_field('amount', 'monto', 'required|valid_string[numeric]|numeric_min[1]');
				break;
			}

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE INICIALIZA EL CONTADOR
				$count = 1;

				# SE GENERA EL SLUG A PARTIR DEL NOMBRE
				$original_slug = Inflector::friendly_title($val->validated('name'), '-', true);
				$slug_temp     = Inflector::friendly_title($val->validated('name'), '-', true);

				# HACER HASTA NO ENCONTRAR EL SLUG REPETIDO
				do{
					# SE VERIFICA SI EXISTE EL SLUG EN LA BASE DE DATOS
					$exist = Model_Product::query()
					->where('slug', $slug_temp)
					->get();

					# SI EL SLUG EXISTE
					if(!empty($exist))
					{
						# SE LE AGREGA EL VALOR DEL CONTADOR AL FINAL DE SLUG
						$slug_temp = $original_slug.'-'.$count;

						# SE INCREMENTA EL CONTADOR
						$count++;
					}
				}while(!empty($exist));
				# FIN DE LA VERIFICACION DEL SLUG

				# SE CREA EL MODELO CON LA INFORMACION
				$product = new Model_Product(array(
					'category_id'    => $val->validated('category'),
					'subcategory_id' => $val->validated('subcategory'),
					'brand_id'       => $val->validated('brand'),
					'slug'           => $slug_temp,
					'name'           => $val->validated('name'),
					'code'           => $val->validated('code'),
					'sku'            => $val->validated('sku'),
					'claveprodserv'  => $val->validated('clave´rpdserv'),
					'claveunidad'    => $val->validated('claveunidad'),
					'codebar'        => $val->validated('codebar'),
					'image'          => $val->validated('image'),
					'description'    => $val->validated('description'),
					'original_price' => $val->validated('original_price'),
					'available'      => $val->validated('available'),
					'weight'         => $val->validated('weight'),
					'price_per'      => $val->validated('price_per'),
					'status'         => 1,
					'status_index'   => 0,
					'deleted'        => 0
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($product->save())
				{
					# SE CREA EL MODELO CON LA INFORMACION
					$product_price_1 = new Model_Products_Price(array(
						'type_id'    => 1,
						'product_id' => $product->id,
						'price'      => $val->validated('price_1')
					));

					# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
					$product_price_1->save();

					# SE CREA EL MODELO CON LA INFORMACION
					$product_price_2 = new Model_Products_Price(array(
						'type_id'    => 2,
						'product_id' => $product->id,
						'price'      => $val->validated('price_2')
					));

					# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
					$product_price_2->save();

					# SE CREA EL MODELO CON LA INFORMACION
					$product_price_3 = new Model_Products_Price(array(
						'type_id'    => 3,
						'product_id' => $product->id,
						'price'      => $val->validated('price_3')
					));

					# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
					$product_price_3->save();

					# DEPENDIENDO DEL CAMPO PRECIO POR
					switch(Input::post('price_per'))
					{
						# MONTO
						case 'm':
							# SE CREA EL MODELO CON LA INFORMACION
							$price_amount = new Model_Products_Prices_Amount(array(
								'product_id'   => $product->id,
								'amount_id'    => $val->validated('amount'),
							));

							# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
							$price_amount->save();
						break;
					}

					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó el producto <b>'.$val->validated('name').'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/productos');
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

				# SI LA DIVISA ES PESOS
				if(Input::post('badge') == 0)
				{
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes['exchange_rate']['form-group']   = '';
					$classes['exchange_rate']['form-control'] = '';
				}
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$category_opts += array('0' => 'Selecciona una opción');

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
				# SE ALMACENA LA OPCION
				$category_opts += array($category->id => $category->name);
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$subcategory_opts += array('0' => 'Selecciona una opción');

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
				# SE ALMACENA LA OPCION
				$subcategory_opts += array($subcategory->id => $subcategory->name);
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$brand_opts += array('0' => 'Selecciona una opción');

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
				# SE ALMACENA LA OPCION
				$brand_opts += array($brand->id => $brand->name);
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$amount_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$amounts = Model_Amount::query()
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($amounts))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($amounts as $amount)
			{
				# SE ALMACENA LA OPCION
				$amount_opts += array($amount->id => $amount->name);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['classes']          = $classes;
		$data['category_opts']    = $category_opts;
		$data['subcategory_opts'] = $subcategory_opts;
		$data['brand_opts']       = $brand_opts;
		$data['amount_opts']      = $amount_opts;
		$data['price_per_opts']   = $price_per_opts;

		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar producto';
		$this->template->content = View::forge('admin/catalogo/productos/agregar', $data);
	}


	/**
	 * INFO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info($product_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($product_id == 0 || !is_numeric($product_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data                   = array();
		$filter                 = '';
		$price_per_text         = '';
		$prices_wholesales_info = array();
		$galleries_info         = array();
		$files_info = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$product = Model_Product::query()
		->related('products_prices_wholesales')
		->related('galleries')
		->related('sale_unit')
		->related('purchase_unit')
		->related('claveunidad_sat')
		->related('products_files')
		->related('products_files.file_type')
		->where('id', $product_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($product))
		{
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

			# SI EXISTE LA RELACION
			if(!empty($product->galleries))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($product->galleries as $image)
				{
					# SE ALMACENA LA INFORMACION
					$galleries_info[] = array(
						'id'    => $image->id,
						'image' => $image->image,
						'order' => $image->order
					);
				}
			}

			// OBTENER ARCHIVOS PDF/FILES DEL PRODUCTO
			if (!empty($product->products_files)) {
				foreach ($product->products_files as $file) {
					$files_info[] = array(
						'id'        => $file->id,
						'file_name' => $file->file_name,
						'file_path' => $file->file_path,
						'file_type' => $file->file_type ? $file->file_type->name : '',
						'downloads' => $file->downloads,
						'created_at' => $file->created_at ?? '',
						'updated_at' => $file->updated_at ?? ''
					);
				}
			}


			# DEPENDIENDO EL CASO
			switch($product->price_per)
			{
				# VACIO
				case '':
					# SE ETSABLECE EL NOMBRE DEL TIPO DE PRECIO
					$price_per_text = 'Lista de precios';
				break;

				# UNIDADES
				case 'u':
					# SE ETSABLECE EL NOMBRE DEL TIPO DE PRECIO
					$price_per_text = 'Unidades';
				break;

				# MONTOS
				case 'm':
					# SE ETSABLECE EL NOMBRE DEL TIPO DE PRECIO
					$price_per_text = 'Montos';
				break;

				# DEFAULT
				default:
					# SE ETSABLECE EL NOMBRE DEL TIPO DE PRECIO
					$price_per_text = 'Sin definir';
				break;
			}

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name']              = $product->name;
			$data['name_order']        = $product->name_order;
			$data['category']          = $product->category->name;
			$data['subcategory']       = $product->subcategory->name;
			$data['brand']             = $product->brand->name;
			$data['code']              = $product->code;
			$data['code_order']        = $product->code_order;
			$data['sku']               = $product->sku;
			$data['factor']            = $product->factor;
			$data['purchase_unit_id']  = $product->purchase_unit ? $product->purchase_unit->name : '';
			$data['sale_unit_id']  	   = $product->sale_unit ? $product->sale_unit->name : '';
			$data['claveprodserv']     = $product->claveprodserv;
			$data['claveunidad']       = $product->claveunidad_sat ? $product->claveunidad_sat->code : '';
			$data['codebar']           = $product->codebar;
			$data['image']             = $product->image;
			$data['description']       = nl2br($product->description);
			$data['available']         = $product->available;
			$data['weight']            = $product->weight;
			$data['price_per']         = $product->price_per;
			$data['price_per_text']    = $price_per_text;
			$data['original_price']    = '$'.number_format($product->original_price, '2', '.', ',');
			$data['price_1']           = '$'.number_format(Model_Products_Price::get_price($product->id, 1), '2', '.', ',');
			$data['price_2']           = '$'.number_format(Model_Products_Price::get_price($product->id, 2), '2', '.', ',');
			$data['price_3']           = '$'.number_format(Model_Products_Price::get_price($product->id, 3), '2', '.', ',');
			$data['prices_wholesales'] = $prices_wholesales_info;
			$data['galleries']         = $galleries_info;
			$data['files'] = $files_info;

			# DEPENDIENDO DEL CAMPO PRECIO POR
			switch($product->price_per)
			{
				# MONTO
				case 'm':
					# SE OBTIENE INFORMACION A TRAVES DEL MODELO
					$product_price_amount = Model_Products_Prices_Amount::query()
					->related('amount')
					->where('product_id', $product->id)
					->get_one();

					# SI SE OBTIENE INFORMACION
					if(!empty($product_price_amount))
					{
						# SE ALMACENA LA INFORMACION PARA LA VISTA
						$data['amount'] = $product_price_amount->amount->name;
					}
					else
					{
						# SE ALMACENA LA INFORMACION PARA LA VISTA
						$data['amount'] = 'Sin definir';
					}
				break;
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $product_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información de la producto';
		$this->template->content = View::forge('admin/catalogo/productos/info', $data, false);
	}


	/**
	 * EDITAR
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar($product_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($product_id == 0 || !is_numeric($product_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data                   = array();
		$classes                = array();
		$fields                 = array('name','name_order', 'category', 'subcategory','brand', 'image', 'description', 'code', 'code_order', 'sku', 'factor', 'purchase_unit_id', 'sale_unit_id', 'claveprodserv', 'claveunidad', 'codebar', 'original_price', 'price_1', 'price_2', 'price_3', 'available', 'weight', 'price_per', 'amount');
		$sale_unit_opts 	    = array();
		$purchase_unit_opts     = array();
		$claveunidad_sat_opts   = array();
		$category_opts 	        = array();
		$subcategory_opts       = array();
		$brand_opts             = array();
		$amount_opts            = array();
		$price_per_opts         = array(
			''  => 'Lista de precios',
			'u' => 'Unidades',
			'm' => 'Montos',
		);
		$prices_wholesales_info = array();
		$galleries_info         = array();

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
		$product = Model_Product::query()
		->related('products_prices_wholesales')
		->related('galleries')
		->where('id', $product_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($product))
		{
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

			# SI EXISTE LA RELACION
			if(!empty($product->galleries))
			{
				# SE RECORRE ELEMENTO POR ELEMENTO
				foreach($product->galleries as $image)
				{
					# SE ALMACENA LA INFORMACION
					$galleries_info[] = array(
						'id'    => $image->id,
						'image' => $image->image,
						'order' => $image->order
					);
				}
			}

			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name']              = $product->name;
			$data['name_order']        = $product->name_order;
			$data['category']          = $product->category_id;
			$data['subcategory']       = $product->subcategory_id;
			$data['brand']             = $product->brand_id;
			$data['code']              = $product->code;
			$data['code_order']        = $product->code_order;
			$data['sku']               = $product->sku;
			$data['factor']            = $product->factor;
			$data['purchase_unit_id']  = $product->purchase_unit_id;
			$data['sale_unit_id']      = $product->sale_unit_id;
			$data['claveprodserv']     = $product->claveprodserv;
			$data['claveunidad']       = $product->claveunidad;
			$data['codebar']           = $product->codebar;
			$data['image']             = $product->image;
			$data['description']       = $product->description;
			$data['available']         = $product->available;
			$data['weight']            = $product->weight;
			$data['price_per']         = $product->price_per;
			$data['original_price']    = $product->original_price;
			$data['price_1']           = Model_Products_Price::get_price($product->id, 1);
			$data['price_2']           = Model_Products_Price::get_price($product->id, 2);
			$data['price_3']           = Model_Products_Price::get_price($product->id, 3);
			$data['prices_wholesales'] = $prices_wholesales_info;
			$data['galleries']         = $galleries_info;

			# DEPENDIENDO DEL CAMPO PRECIO POR
			switch($product->price_per)
			{
				# MONTO
				case 'm':
					# SE OBTIENE INFORMACION A TRAVES DEL MODELO
					$product_price_amount = Model_Products_Prices_Amount::query()
					->related('amount')
					->where('product_id', $product->id)
					->get_one();

					# SI SE OBTIENE INFORMACION
					if(!empty($product_price_amount))
					{
						# SE ALMACENA LA INFORMACION PARA LA VISTA
						$data['amount'] = $product_price_amount->amount_id;
					}
					else
					{
						# SE ALMACENA LA INFORMACION PARA LA VISTA
						$data['amount'] = 0;
					}
				break;
			}
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('product');
			$val->add_callable('Rules');
			$val->add_field('category', 'categoría', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('subcategory', 'grupo', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('brand', 'marca', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('name', 'nombre', 'required|min_length[1]|max_length[255]');
			$val->add_field('name_order', 'nombre proveedor', 'min_length[1]|max_length[255]');
			$val->add_field('code', 'código', 'required|min_length[1]|max_length[255]');
			$val->add_field('code_order', 'código proveedor', 'min_length[1]|max_length[255]');
			$val->add_field('sku', 'sku', 'min_length[1]|max_length[255]');
			$val->add_field('factor', 'factor', 'min_length[1]|max_length[255]');
			$val->add_field('purchase_unit_id', 'unidad compra', 'min_length[1]|max_length[255]');
			$val->add_field('sale_unit_id', 'unidad venta', 'min_length[1]|max_length[255]');
			$val->add_field('factor', 'factor', 'min_length[1]|max_length[255]');
			$val->add_field('claveprodserv', 'codigo sat', 'min_length[1]|max_length[255]');
			$val->add_field('claveunidad', 'unidad sat', 'min_length[1]|max_length[255]');
			$val->add_field('codebar', 'código de barras', 'required|min_length[1]|max_length[255]');
			$val->add_field('image', 'imagen', 'required|min_length[1]');
			$val->add_field('description', 'descripción', 'required|min_length[1]');
			$val->add_field('available', 'cantidad disponible', 'required|valid_string[numeric]|numeric_min[0]');
			$val->add_field('weight', 'peso producto', 'required|float');
			$val->add_field('price_per', 'precio por', 'min_length[0]');
			$val->add_field('original_price', 'precio original', 'required|float');
			$val->add_field('price_1', 'precio (normal)', 'required|float');
			$val->add_field('price_2', 'precio (mayorista #1)', 'required|float');
			$val->add_field('price_3', 'precio (mayorista #3)', 'required|float');

			# DEPENDIENDO DEL CAMPO PRECIO POR
			switch(Input::post('price_per'))
			{
				# MONTO
				case 'm':
					# SE AGREGA LA VALIDACION DEL CAMPO
					$val->add_field('amount', 'monto', 'required|valid_string[numeric]|numeric_min[1]');
				break;
			}

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE INICIALIZA EL CONTADOR
				$count = 1;

				# SE GENERA EL SLUG A PARTIR DEL NOMBRE
				$original_slug = Inflector::friendly_title($val->validated('name'), '-', true);
				$slug_temp     = Inflector::friendly_title($val->validated('name'), '-', true);

				# HACER HASTA NO ENCONTRAR EL SLUG REPETIDO
				do{
					# SE VERIFICA SI EXISTE EL SLUG EN LA BASE DE DATOS
					$exist = Model_Product::query()
					->where('slug', $slug_temp)
					->get_one();

					# SI EL SLUG EXISTE
					if(!empty($exist))
					{
						# SI EL ID DEL SLUG ES DIFERENTE AL ID ORIGINAL
						if($exist->id != $product->id)
						{
							# SE LE AGREGA EL VALOR DEL CONTADOR AL FINAL DE SLUG
							$slug_temp = $original_slug.'-'.$count;

							# SE INCREMENTA EL CONTADOR
							$count++;
						}
						else
						{
							# SE ROMPE EL CICLO DEL DO-WHILE
							break;
						}
					}
				}while(!empty($exist));
				# FIN DE LA VERIFICACION DEL SLUG

				# SE ESTEBLECE LA NUEVA INFORMACION
				$product->category_id    		= $val->validated('category');
				$product->subcategory_id 		= $val->validated('subcategory');
				$product->brand_id       		= $val->validated('brand');
				$product->slug           		= $slug_temp;
				$product->name           		= $val->validated('name');
				$product->name_order     		= $val->validated('name_order');
				$product->code           		= $val->validated('code');
				$product->code_order     		= $val->validated('code_order');
				$product->sku            		= $val->validated('sku');
				$product->factor         		= $val->validated('factor');
				$product->purchase_unit_id      = $val->validated('purchase_unit_id');
				$product->sale_unit_id         	= $val->validated('sale_unit_id');
				$product->claveprodserv  		= $val->validated('claveprodserv');
				$product->claveunidad    		= $val->validated('claveunidad');
				$product->codebar        		= $val->validated('codebar');
				$product->image          		= $val->validated('image');
				$product->description    		= $val->validated('description');
				$product->original_price 		= $val->validated('original_price');
				$product->available      		= $val->validated('available');
				$product->weight         		= $val->validated('weight');
				$product->price_per      		= $val->validated('price_per');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($product->save())
				{
					# DEPENDIENDO DEL CAMPO PRECIO POR
					switch(Input::post('price_per'))
					{
						# LISTA DE PRECIO
						case '':
							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$product_price_1 = Model_Products_Price::query()
							->where('product_id', $product->id)
							->where('type_id', 1)
							->get_one();

							# SI SE OBTIENE INFORMACION
							if(!empty($product_price_1))
							{
								# SE ESTEBLECE LA NUEVA INFORMACION
								$product_price_1->price = $val->validated('price_1');

								# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
								$product_price_1->save();
							}
							else
							{
								# SE CREA EL MODELO CON LA INFORMACION
								$product_price_1 = new Model_Products_Price(array(
									'type_id'    => 1,
									'product_id' => $product->id,
									'price'      => $val->validated('price_1')
								));

								# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
								$product_price_1->save();
							}

							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$product_price_2 = Model_Products_Price::query()
							->where('product_id', $product->id)
							->where('type_id', 2)
							->get_one();

							# SI SE OBTIENE INFORMACION
							if(!empty($product_price_2))
							{
								# SE ESTEBLECE LA NUEVA INFORMACION
								$product_price_2->price = $val->validated('price_2');

								# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
								$product_price_2->save();
							}
							else
							{
								# SE CREA EL MODELO CON LA INFORMACION
								$product_price_2 = new Model_Products_Price(array(
									'type_id'    => 2,
									'product_id' => $product->id,
									'price'      => $val->validated('price_2')
								));

								# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
								$product_price_2->save();
							}

							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$product_price_3 = Model_Products_Price::query()
							->where('product_id', $product->id)
							->where('type_id', 3)
							->get_one();

							# SI SE OBTIENE INFORMACION
							if(!empty($product_price_3))
							{
								# SE ESTEBLECE LA NUEVA INFORMACION
								$product_price_3->price = $val->validated('price_3');

								# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
								$product_price_3->save();
							}
							else
							{
								# SE CREA EL MODELO CON LA INFORMACION
								$product_price_3 = new Model_Products_Price(array(
									'type_id'    => 3,
									'product_id' => $product->id,
									'price'      => $val->validated('price_3')
								));

								# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
								$product_price_3->save();
							}

							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$prices_wholesales = Model_Products_Prices_Wholesale::query()
							->where('product_id', $product->id)
							->get();

							# SI SE OBTIENE INFORMACION
							if(!empty($prices_wholesales))
							{
								# SE RECORRE ELEMENTO POR ELEMENTO
								foreach($prices_wholesales as $price_wholesale)
								{
									# SE ELIMINA EL REGISTRO EN LA BASE DE DATOS
									$price_wholesale->delete();
								}
							}

							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$price_amount = Model_Products_Prices_Amount::query()
							->where('product_id', $product->id)
							->get_one();

							# SI SE OBTIENE INFORMACION
							if(!empty($price_amount))
							{
								# SE ELIMINA EL REGISTRO EN LA BASE DE DATOS
								$price_amount->delete();
							}
						break;

						# UNIDADES
						case 'u':
							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$product_price_1 = Model_Products_Price::query()
							->where('product_id', $product->id)
							->where('type_id', 1)
							->get_one();

							# SI SE OBTIENE INFORMACION
							if(!empty($product_price_1))
							{
								# SE ESTEBLECE LA NUEVA INFORMACION
								$product_price_1->price = $val->validated('price_1');

								# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
								$product_price_1->save();
							}
							else
							{
								# SE CREA EL MODELO CON LA INFORMACION
								$product_price_1 = new Model_Products_Price(array(
									'type_id'    => 1,
									'product_id' => $product->id,
									'price'      => $val->validated('price_1')
								));

								# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
								$product_price_1->save();
							}

							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$product_price_2 = Model_Products_Price::query()
							->where('product_id', $product->id)
							->where('type_id', 2)
							->get_one();

							# SI SE OBTIENE INFORMACION
							if(!empty($product_price_2))
							{
								# SE ESTEBLECE LA NUEVA INFORMACION
								$product_price_2->price = $val->validated('price_2');

								# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
								$product_price_2->save();
							}
							else
							{
								# SE CREA EL MODELO CON LA INFORMACION
								$product_price_2 = new Model_Products_Price(array(
									'type_id'    => 2,
									'product_id' => $product->id,
									'price'      => $val->validated('price_2')
								));

								# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
								$product_price_2->save();
							}

							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$product_price_3 = Model_Products_Price::query()
							->where('product_id', $product->id)
							->where('type_id', 3)
							->get_one();

							# SI SE OBTIENE INFORMACION
							if(!empty($product_price_3))
							{
								# SE ESTEBLECE LA NUEVA INFORMACION
								$product_price_3->price = $val->validated('price_3');

								# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
								$product_price_3->save();
							}
							else
							{
								# SE CREA EL MODELO CON LA INFORMACION
								$product_price_3 = new Model_Products_Price(array(
									'type_id'    => 3,
									'product_id' => $product->id,
									'price'      => $val->validated('price_3')
								));

								# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
								$product_price_3->save();
							}

							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$price_amount = Model_Products_Prices_Amount::query()
							->where('product_id', $product->id)
							->get_one();

							# SI SE OBTIENE INFORMACION
							if(!empty($price_amount))
							{
								# SE ELIMINA EL REGISTRO EN LA BASE DE DATOS
								$price_amount->delete();
							}
						break;

						# MONTOS
						case 'm':
							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$price_amount = Model_Products_Prices_Amount::query()
							->where('product_id', $product->id)
							->get_one();

							# SI SE OBTIENE INFORMACION
							if(!empty($price_amount))
							{
								# SE ESTEBLECE LA NUEVA INFORMACION
								$price_amount->amount_id = $val->validated('amount');

								# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
								$price_amount->save();
							}
							else
							{
								# SE CREA EL MODELO CON LA INFORMACION
								$price_amount = new Model_Products_Prices_Amount(array(
									'product_id' => $product->id,
									'amount_id'  => $val->validated('amount')
								));

								# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
								$price_amount->save();
							}

							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$product_price_1 = Model_Products_Price::query()
							->where('product_id', $product->id)
							->where('type_id', 1)
							->get_one();

							# SI SE OBTIENE INFORMACION
							if(!empty($product_price_1))
							{
								# SE ESTEBLECE LA NUEVA INFORMACION
								$product_price_1->price = $val->validated('price_1');

								# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
								$product_price_1->save();
							}
							else
							{
								# SE CREA EL MODELO CON LA INFORMACION
								$product_price_1 = new Model_Products_Price(array(
									'type_id'    => 1,
									'product_id' => $product->id,
									'price'      => $val->validated('price_1')
								));

								# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
								$product_price_1->save();
							}

							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$product_price_2 = Model_Products_Price::query()
							->where('product_id', $product->id)
							->where('type_id', 2)
							->get_one();

							# SI SE OBTIENE INFORMACION
							if(!empty($product_price_2))
							{
								# SE ESTEBLECE LA NUEVA INFORMACION
								$product_price_2->price = $val->validated('price_2');

								# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
								$product_price_2->save();
							}
							else
							{
								# SE CREA EL MODELO CON LA INFORMACION
								$product_price_2 = new Model_Products_Price(array(
									'type_id'    => 2,
									'product_id' => $product->id,
									'price'      => $val->validated('price_2')
								));

								# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
								$product_price_2->save();
							}

							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$product_price_3 = Model_Products_Price::query()
							->where('product_id', $product->id)
							->where('type_id', 3)
							->get_one();

							# SI SE OBTIENE INFORMACION
							if(!empty($product_price_3))
							{
								# SE ESTEBLECE LA NUEVA INFORMACION
								$product_price_3->price = $val->validated('price_3');

								# SE ACTUALIZA EL REGISTRO EN LA BASE DE DATOS
								$product_price_3->save();
							}
							else
							{
								# SE CREA EL MODELO CON LA INFORMACION
								$product_price_3 = new Model_Products_Price(array(
									'type_id'    => 3,
									'product_id' => $product->id,
									'price'      => $val->validated('price_3')
								));

								# SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
								$product_price_3->save();
							}

							# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
							$prices_wholesales = Model_Products_Prices_Wholesale::query()
							->where('product_id', $product->id)
							->get();

							# SI SE OBTIENE INFORMACION
							if(!empty($prices_wholesales))
							{
								# SE RECORRE ELEMENTO POR ELEMENTO
								foreach($prices_wholesales as $price_wholesale)
								{
									# SE ELIMINA EL REGISTRO EN LA BASE DE DATOS
									$price_wholesale->delete();
								}
							}
						break;
					}

					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de <b>'.$product->name.'</b> correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/productos/editar/'.$product_id);
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

				# SI LA DIVISA ES PESOS
				if(Input::post('badge') == 0)
				{
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes['exchange_rate']['form-group']   = '';
					$classes['exchange_rate']['form-control'] = '';
				}
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$sale_unit_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$saleunits = Model_Sat_Unit::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($saleunits))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($saleunits as $sale_unit_id)
			{
				# SE ALMACENA LA OPCION
				$sale_unit_opts += array($sale_unit_id->id => $sale_unit_id->name);
			}
		}
		
		# SE ESTBLECE LA OPCION POR DEFAULT
		$purchase_unit_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$purchaseunits = Model_Sat_Unit::query()
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($purchaseunits))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($purchaseunits as $purcahse_unit_id)
			{
				# SE ALMACENA LA OPCION
				$purchase_unit_opts += array($purcahse_unit_id->id => $purcahse_unit_id->name);
			}
		}
		
		# SE ESTBLECE LA OPCION POR DEFAULT
		$claveunidad_sat_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$claveunidades = Model_Sat_Unit::query()
		->where('deleted', 0)
		->order_by('code', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($claveunidades))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($claveunidades as $claveunidad_id)
			{
				# SE ALMACENA LA OPCION
				$claveunidad_sat_opts += array($claveunidad_id->code => $claveunidad_id->code);
			}
		}
		
		
		# SE ESTBLECE LA OPCION POR DEFAULT
		$category_opts += array('0' => 'Selecciona una opción');

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
				# SE ALMACENA LA OPCION
				$category_opts += array($category->id => $category->name);
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$subcategory_opts += array('0' => 'Selecciona una opción');

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
				# SE ALMACENA LA OPCION
				$subcategory_opts += array($subcategory->id => $subcategory->name);
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$brand_opts += array('0' => 'Selecciona una opción');

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
				# SE ALMACENA LA OPCION
				$brand_opts += array($brand->id => $brand->name);
			}
		}

		# SE ESTBLECE LA OPCION POR DEFAULT
		$amount_opts += array('0' => 'Selecciona una opción');

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$amounts = Model_Amount::query()
		->order_by('name', 'asc')
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($amounts))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($amounts as $amount)
			{
				# SE ALMACENA LA OPCION
				$amount_opts += array($amount->id => $amount->name);
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']            		= $product_id;
		$data['classes']       		= $classes;
		$data['sale_unit_opts'] 	= $sale_unit_opts;
		$data['purchase_unit_opts'] = $purchase_unit_opts;
		$data['claveunidad_sat_opts'] = $claveunidad_sat_opts;
		$data['category_opts'] 		= $category_opts;
		$data['subcategory_opts'] 	= $subcategory_opts;
		$data['brand_opts']    		= $brand_opts;
		$data['brand_opts']    		= $brand_opts;
		$data['amount_opts']        = $amount_opts;
		$data['price_per_opts']     = $price_per_opts;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar producto';
		$this->template->content = View::forge('admin/catalogo/productos/editar', $data);
	}


	/**
	 * ELIMINAR
	 *
	 * CAMBIA EL VALOR DEL CAMPO DELETED PARA UN BORRADO LOGICO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar($product_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($product_id == 0 || !is_numeric($product_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$relations_info = '';

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$product = Model_Product::query()
		->where('id', $product_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($product))
		{
			# SE ESTEBLECE LA NUEVA INFORMACION
			$product->deleted = 1;

			# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
			if($product->save())
			{
				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se eliminó el producto <b>'.$product->name.'</b> correctamente.');
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/catalogo/productos');
	}


	/**
	 * ACCIÓN AGREGAR ARCHIVO PDF TÉCNICO AL PRODUCTO
	 * PERMITE ASOCIAR UNO O VARIOS ARCHIVOS PDF AL PRODUCTO SELECCIONADO
	 * DOCUMENTADO Y MANTENIENDO LA ESTRUCTURA Y ESTILO ORIGINAL
	 * @access  public
	 * @return  Void
	 */
	public function action_agregar_archivo($product_id = 0)
	{
		

		# Validar el ID del producto
		if($product_id == 0 || !is_numeric($product_id)) {
			Response::redirect('admin/catalogo/productos');
		}

		# Buscar el producto
		$product = Model_Product::query()
			->where('id', $product_id)
			->where('deleted', 0)
			->get_one();

		if(empty($product)) {
			Response::redirect('admin/catalogo/productos');
		}

		# Catálogo de tipos de archivo
		$file_type_opts = array('' => 'Selecciona tipo de archivo');
		$file_types = Model_Products_File_Type::query()->order_by('name', 'asc')->get();
		if(!empty($file_types)) {
			foreach($file_types as $ft) {
				$file_type_opts[$ft->id] = $ft->name;
			}
		}

		# Manejo de clases/errores para la vista
		$data = array('name' => $product->name, 'id' => $product_id, 'file_type_opts' => $file_type_opts, 'classes' => array());
		$fields = array('file', 'file_type_id', 'file_name');
		foreach($fields as $field) {
			$data['classes'][$field] = array('form-group' => null, 'form-control' => null);
		}

		# Si el método es POST
		if(Input::method() == 'POST') {

			# Validación básica
			$val = Validation::forge('product_file');
			$val->add_callable('Rules');
			//$val->add_field('file', 'archivo', 'required');
			$val->add_field('file_type_id', 'tipo de archivo', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('file_name', 'nombre del archivo', 'required|min_length[2]|max_length[100]');

			if($val->run()) {

				# Configuración de subida
				$upload_config = array(
					'auto_process'        => false,
					'path'                => DOCROOT.'assets/uploads/products/files',
					'randomize'           => true,
					'auto_rename'         => true,
					'normalize'           => true,
					'normalize_separator' => '-',
					'ext_whitelist'       => array('pdf'),
					'max_size'            => 20971520, // 20 MB
				);

				# Procesar upload
				Upload::process($upload_config);

				# Si el archivo es válido
				if(Upload::is_valid()) {
					Upload::save();
					$file_data = Upload::get_files();

					$saved_file = (isset($file_data[0]['saved_as'])) ? $file_data[0]['saved_as'] : '';
					$file_path = $saved_file ? 'assets/uploads/products/files/'.$saved_file : '';


					# Obtener el orden máximo
					$order = Model_Products_File::query()->where('product_id', $product_id)->max('order');

					# Guardar registro en base de datos
					$product_file = new Model_Products_File(array(
						'product_id'   => $product_id,
						'file_type_id' => $val->validated('file_type_id'),
						'file_name'    => $val->validated('file_name'),
						'file_path'    => $file_path,
						'order'        => $order + 1
					));

					if($product_file->save()) {
						Session::set_flash('success', 'Se agregó el archivo PDF correctamente.');
						Response::redirect('admin/catalogo/productos/info/'.$product_id);
					} else {
						Session::set_flash('error', 'No se pudo guardar el registro del archivo.');
					}
				} else {
					Session::set_flash('error', 'El archivo no es válido o es demasiado grande.');
				}
			} else {

				Session::set_flash('error', 'Encontramos algunos errores en el formulario, por favor verifícalo.');
				$data['errors'] = $val->error();

				foreach($fields as $name) {
					$data['classes'][$name]['form-group']   = ($val->error($name)) ? 'has-danger' : 'has-success';
					$data['classes'][$name]['form-control'] = ($val->error($name)) ? 'is-invalid' : 'is-valid';
					$data[$name] = Input::post($name);
				}
			}
		}

		$this->template->title   = 'Agregar archivo PDF';
		$this->template->content = View::forge('admin/catalogo/productos/agregar_archivo', $data);
	}


	/**
	 * INFO ARCHIVO PDF
	 *
	 * MUESTRA LA INFORMACION DE UN ARCHIVO PDF RELACIONADO AL PRODUCTO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info_archivo($file_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($file_id == 0 || !is_numeric($file_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION DEL ARCHIVO PDF
		$file = Model_Products_File::query()
			->related('product')
			->related('file_type')
			->where('id', $file_id)
			->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($file))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['product_id']   = $file->product_id;
			$data['name']         = $file->product->name;
			$data['file_path']    = $file->file_path;
			$data['file_name']    = $file->file_name;
			$data['file_type']    = $file->file_type ? $file->file_type->name : '';
			$data['created_at']   = $file->created_at ?? '';
			$data['updated_at']   = $file->updated_at ?? '';
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $file_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del archivo PDF';
		$this->template->content = View::forge('admin/catalogo/productos/info_archivo', $data, false);
	}


	/**
	 * EDITAR ARCHIVO PDF
	 *
	 * PERMITE EDITAR UN ARCHIVO PDF RELACIONADO AL PRODUCTO
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar_archivo($file_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($file_id == 0 || !is_numeric($file_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('file', 'file_type_id', 'file_name');
		$file_type_opts = array();

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
		$file = Model_Products_File::query()
			->related('file_type')
			->related('product')
			->where('id', $file_id)
			->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($file))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['product_id']   = $file->product_id;
			$data['name']         = $file->product->name;
			$data['file']         = $file->file_path;
			$data['file_type_id'] = $file->file_type_id;
			$data['file_name']    = $file->file_name;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# LLENAR CATALOGO DE TIPOS DE ARCHIVO
		$file_types = Model_Products_File_Type::query()
			->order_by('name', 'asc')
			->get();

		$file_type_opts += array('' => 'Selecciona tipo de archivo');
		if(!empty($file_types))
		{
			foreach($file_types as $ft)
			{
				$file_type_opts[$ft->id] = $ft->name;
			}
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('product_file');
			$val->add_callable('Rules');
			$val->add_field('file_type_id', 'tipo de archivo', 'required|valid_string[numeric]|numeric_min[1]');
			$val->add_field('file_name', 'nombre del archivo', 'required|min_length[2]|max_length[100]');

			# VALIDAR SOLO SI SUBEN NUEVO ARCHIVO
			if (Input::file('file.name') && Input::file('file.name') != '') {
				$val->add_field('file', 'archivo', 'required|min_length[1]');
			}

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SI SE SUBE UN NUEVO ARCHIVO
				$file_uploaded = Input::file('file');
				if ($file_uploaded && $file_uploaded['error'] === UPLOAD_ERR_OK)
				{
					# BORRAR EL ARCHIVO ANTERIOR SI EXISTE
					if (!empty($file->file_path) && file_exists(DOCROOT.$file->file_path)) {
						File::delete(DOCROOT.$file->file_path);
					}
					# GUARDAR EL NUEVO ARCHIVO
					$filename = uniqid().'_'.Inflector::friendly_title($file_uploaded['name'], '-', true).'.pdf';
					$destination = DOCROOT.'uploads/products/files/'.$filename;

					# CREAR CARPETA SI NO EXISTE
					if (!is_dir(DOCROOT.'uploads/products/files/')) {
						File::create_dir(DOCROOT.'uploads/products', 'files');
					}
					if (@move_uploaded_file($file_uploaded['tmp_name'], $destination))
					{
						$file->file_path = 'uploads/products/files/'.$filename;
					}
					else
					{
						Session::set_flash('error', 'No se pudo guardar el archivo PDF.');
					}
				}

				# SE ESTABLECE LA NUEVA INFORMACION
				$file->file_type_id = $val->validated('file_type_id');
				$file->file_name    = $val->validated('file_name');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($file->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información del archivo correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/productos/editar_archivo/'.$file_id);
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
		$data['id']              = $file_id;
		$data['classes']         = $classes;
		$data['file_type_opts']  = $file_type_opts;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar archivo PDF';
		$this->template->content = View::forge('admin/catalogo/productos/editar_archivo', $data);
	}


	/**
	 * ELIMINAR ARCHIVO PDF
	 *
	 * ELIMINA UN REGISTRO DE ARCHIVO PDF DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar_archivo($file_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($file_id == 0 || !is_numeric($file_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$file = Model_Products_File::query()
			->where('id', $file_id)
			->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($file))
		{
			$product_id = $file->product_id;

			# SI SE ELIMINO EL REGISTRO EN LA BASE DE DATOS
			if($file->delete())
			{
				# SI EL ARCHIVO PDF EXISTE
				if(!empty($file->file_path) && file_exists(DOCROOT.$file->file_path))
				{
					# SE ELIMINA EL ARCHIVO PDF
					File::delete(DOCROOT.$file->file_path);
				}

				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se eliminó el archivo correctamente.');

				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/catalogo/productos/info/'.$product_id);
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/catalogo/productos');
	}



	/**
	 * AGREGAR FOTO
	 *
	 * PERMITE AGREGAR UNA FOTO A UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_agregar_foto($product_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($product_id == 0 || !is_numeric($product_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('image');

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
		$product = Model_Product::query()
		->where('id', $product_id)
		->where('deleted', 0)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($product))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name'] = $product->name;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('product');
			$val->add_callable('Rules');
			$val->add_field('image', 'imagen', 'required|min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE OBTIENE EL ORDEN MAXIMO
				$order = Model_Products_Image::query()
				->where('product_id', $product_id)
				->max('order');

				# SE CREA EL MODELO CON LA INFORMACION
				$product_image = new Model_Products_Image(array(
					'product_id' => $product_id,
					'image'      => $val->validated('image'),
					'order'      => $order + 1
				));

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($product_image->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó la imagen correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/productos/info/'.$product_id);
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

				# SI LA DIVISA ES PESOS
				if(Input::post('badge') == 0)
				{
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes['exchange_rate']['form-group']   = '';
					$classes['exchange_rate']['form-control'] = '';
				}
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']      = $product_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar producto';
		$this->template->content = View::forge('admin/catalogo/productos/agregar_foto', $data);
	}
	
	
	/**
	 * INFO FOTO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info_foto($photo_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($photo_id == 0 || !is_numeric($photo_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$photo = Model_Products_Image::query()
		->where('id', $photo_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($photo))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['product_id'] = $photo->product_id;
			$data['name']       = $photo->product->name;
			$data['image']      = $photo->image;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $photo_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información de la fotografía';
		$this->template->content = View::forge('admin/catalogo/productos/info_foto', $data, false);
	}


	/**
	 * EDITAR FOTO
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar_foto($photo_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($photo_id == 0 || !is_numeric($photo_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('image');

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
		$photo = Model_Products_Image::query()
		->where('id', $photo_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($photo))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['product_id'] = $photo->product_id;
			$data['name']       = $photo->product->name;
			$data['image']      = $photo->image;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('product');
			$val->add_callable('Rules');
			$val->add_field('image', 'imagen', 'required|min_length[1]');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE ESTEBLECE LA NUEVA INFORMACION
				$photo->image = $val->validated('image');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($photo->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información de la fotografía correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/productos/editar_foto/'.$photo_id);
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

				# SI LA DIVISA ES PESOS
				if(Input::post('badge') == 0)
				{
					# SE ESTABLECE EL VALOR DE LAS CLASES
					$classes['exchange_rate']['form-group']   = '';
					$classes['exchange_rate']['form-control'] = '';
				}
			}
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id']      = $photo_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar fotografía';
		$this->template->content = View::forge('admin/catalogo/productos/editar_foto', $data);
	}


	/**
	 * ELIMINAR FOTO
	 *
	 * ELIMINA UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar_foto($photo_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($photo_id == 0 || !is_numeric($photo_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$photo = Model_Products_Image::query()
		->where('id', $photo_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($photo))
		{
			# SI SE ELIMINO EL REGISTRO EN LA BASE DE DATOS
			if($photo->delete())
			{
				# SI EL ARCHIVO EXISTE
				if(file_exists(DOCROOT.'assets/uploads/'.$photo->image))
				{
					# SE ELIMINAN EL ARCHIVO
					File::delete(DOCROOT.'assets/uploads/'.$photo->image);
				}

				# SI EL ARCHIVO EXISTE
				if(file_exists(DOCROOT.'assets/uploads/thumb_'.$photo->image))
				{
					# SE ELIMINAN EL ARCHIVO
					File::delete(DOCROOT.'assets/uploads/thumb_'.$photo->image);
				}

				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se eliminó la fotografía correctamente.');

				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/catalogo/productos/info/'.$photo->product_id);
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/catalogo/productos');
	}


	/**
	 * AGREGAR RANGO
	 *
	 * PERMITE AGREGAR UN REGISTRO A LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_agregar_rango($product_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($product_id == 0 || !is_numeric($product_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/montos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('min_quantity', 'max_quantity', 'price');

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
		$product = Model_Product::query()
		->where('id', $product_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($product))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['name'] = $product->name;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SI SE UTILIZA EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('product');
			$val->add_callable('Rules');
			$val->add_field('min_quantity', 'cantidad mínima', 'required|valid_string[numeric]|numeric_min[0]');
			$val->add_field('max_quantity', 'cantidad máxima', 'required|valid_string[numeric]|numeric_min[0]');
			$val->add_field('price', 'precio', 'required|float');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE CREA EL MODELO CON LA INFORMACION
				$product_price_wholesale = new Model_Products_Prices_Wholesale(array(
					'product_id'   => $product_id,
					'min_quantity' => $val->validated('min_quantity'),
					'max_quantity' => $val->validated('max_quantity'),
					'price'        => $val->validated('price')
				));

				# SI SE ALMACENA EL REGISTRO EN LA BASE DE DATOS
				if($product_price_wholesale->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se agregó el rango correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/productos/info/'.$product_id);
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
		$data['id']      = $product_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Agregar rango';
		$this->template->content = View::forge('admin/catalogo/productos/agregar_rango', $data);
	}


	/**
	 * INFO_RANGO
	 *
	 * MUESTRA LA INFORMACION DE UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_info_rango($price_wholesale_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($price_wholesale_id == 0 || !is_numeric($price_wholesale_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/montos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$price_wholeslae = Model_Products_Prices_Wholesale::query()
		->related('product')
		->where('id', $price_wholesale_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($price_wholeslae))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['product_id']   = $price_wholeslae->product_id;
			$data['name']         = $price_wholeslae->product->name;
			$data['min_quantity'] = $price_wholeslae->min_quantity;
			$data['max_quantity'] = $price_wholeslae->max_quantity;
			$data['price']        = $price_wholeslae->price;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE ALMACENA LA INFORMACION PARA LA VISTA
		$data['id'] = $price_wholesale_id;

		# SE CARGA LA VISTA
		$this->template->title   = 'Información del rango';
		$this->template->content = View::forge('admin/catalogo/productos/info_rango', $data);
	}


	/**
	 * EDITAR_RANGO
	 *
	 * PERMITE EDITAR UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_editar_rango($price_wholesale_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($price_wholesale_id == 0 || !is_numeric($price_wholesale_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/montos');
		}

		# SE INICIALIZAN LAS VARIABLES
		$data    = array();
		$classes = array();
		$fields  = array('min_quantity', 'max_quantity', 'price');

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
		$price_wholeslae = Model_Products_Prices_Wholesale::query()
		->related('product')
		->where('id', $price_wholesale_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($price_wholeslae))
		{
			# SE ALMACENA LA INFORMACION PARA LA VISTA
			$data['product_id']   = $price_wholeslae->product_id;
			$data['name']         = $price_wholeslae->product->name;
			$data['min_quantity'] = $price_wholeslae->min_quantity;
			$data['max_quantity'] = $price_wholeslae->max_quantity;
			$data['price']        = $price_wholeslae->price;
		}
		else
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SI SE UTILIZO EL METODO POST
		if(Input::method() == 'POST')
		{
			# SE CREA LA VALIDACION DE LOS CAMPOS
			$val = Validation::forge('product');
			$val->add_callable('Rules');
			$val->add_field('min_quantity', 'cantidad mínima', 'required|valid_string[numeric]|numeric_min[0]');
			$val->add_field('max_quantity', 'cantidad máxima', 'required|valid_string[numeric]|numeric_min[0]');
			$val->add_field('price', 'precio', 'required|float');

			# SI NO HAY NINGUN PROBLEMA CON LA VALIDACION
			if($val->run())
			{
				# SE ESTEBLECE LA NUEVA INFORMACION
				$price_wholeslae->min_quantity = $val->validated('min_quantity');
				$price_wholeslae->max_quantity = $val->validated('max_quantity');
				$price_wholeslae->price        = $val->validated('price');

				# SI SE ALMACENO EL REGISTRO EN LA BASE DE DATOS
				if($price_wholeslae->save())
				{
					# SE ESTABLECE EL MENSAJE DE EXITO
					Session::set_flash('success', 'Se actualizó la información correctamente.');

					# SE REDIRECCIONA AL USUARIO
					Response::redirect('admin/catalogo/productos/editar_rango/'.$price_wholesale_id);
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
		$data['id']      = $price_wholesale_id;
		$data['classes'] = $classes;

		# SE CARGA LA VISTA
		$this->template->title   = 'Editar rango';
		$this->template->content = View::forge('admin/catalogo/productos/editar_rango', $data);
	}


	/**
	 * ELIMINAR RANGO
	 *
	 * ELIMINA UN REGISTRO DE LA BASE DE DATOS
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_eliminar_rango($price_wholesale_id = 0)
	{
		# SI NO SE RECIBE UN ID O NO ES UN NUMERO
		if($price_wholesale_id == 0 || !is_numeric($price_wholesale_id))
		{
			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin/catalogo/productos');
		}

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$price_wholeslae = Model_Products_Prices_Wholesale::query()
		->related('product')
		->where('id', $price_wholesale_id)
		->get_one();

		# SI SE OBTIENE INFORMACION
		if(!empty($price_wholeslae))
		{
			# SE ALMACENA EL ID
			$product_id = $price_wholeslae->product->id;

			# SI SE ELIMINO EL REGISTRO EN LA BASE DE DATOS
			if($price_wholeslae->delete())
			{
				# SE ESTABLECE EL MENSAJE DE EXITO
				Session::set_flash('success', 'Se eliminó el rango correctamente.');

				# SE REDIRECCIONA AL USUARIO
				Response::redirect('admin/catalogo/productos/info/'.$product_id);
			}
		}

		# SE REDIRECCIONA AL USUARIO
		Response::redirect('admin/catalogo/productos');
	}
}
