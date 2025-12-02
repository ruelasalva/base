<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Catálogo</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/catalogo/productos', 'Productos'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/catalogo/productos/csv', 'Carga masiva por CSV', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/catalogo/productos/agregar', 'Agregar', array('class' => 'btn btn-sm btn-neutral')); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
	<!-- TABLE -->
	<div class="row">
		<div class="col">
			<div class="card">
				<!-- CARD HEADER -->
				<div class="card-header border-0">
					<?php echo Form::open(array('action' => 'admin/catalogo/productos/buscar', 'method' => 'post')); ?>
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Lista de productos</h3>
						</div>
						<div class="col-md-3 mb-0">
							<div class="input-group input-group-sm mt-3 mt-md-0">
								<?php echo Form::input('search', (isset($search) ? $search : ''), array('id' => 'search', 'class' => 'form-control', 'placeholder' => 'Nombre', 'aria-describedby' => 'button-addon')); ?>
								<div class="input-group-append">
									<?php echo Form::submit(array('value'=> 'Buscar', 'name'=>'submit', 'id' => 'button-addon', 'class' => 'btn btn-outline-primary')); ?>
								</div>
							</div>
						</div>
					</div>
					<?php echo Form::close(); ?>
				</div>
				<!-- LIGHT TABLE -->
				<div class="table-responsive" data-toggle="lists" data-list-values='["code","name", "image","brand","category","available", "price_1", "price_2", "price_3","status", "status_index", "soon", "newproduct", "temporarily_sold_out"]'>
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th scope="col" class="sort" data-sort="code">Codigo</th>
								<th scope="col" class="sort" data-sort="image">Imagen</th>
								<th scope="col" class="sort" data-sort="name">Nombre</th>
								<th scope="col" class="sort" data-sort="available">Disponibles</th>
								<th scope="col" class="sort" data-sort="price_1">Precio<br> TOL</th>
								<th scope="col" class="sort" data-sort="price_2">Precio<br> Lista</th>
								<th scope="col" class="sort" data-sort="price_3">Precio<br> Mayoreo</th>
								<th scope="col" class="sort" data-sort="status">Disponible</th>
								<th scope="col" class="sort" data-sort="status_index">Mostrar<br> en inicio</th>
								<th scope="col" class="sort" data-sort="soon">Mostrar<br>como<br>proximamente</th>
								<th scope="col" class="sort" data-sort="newproduct">Mostrar<br>como<br>nuevo<br>producto</th>
								<th scope="col" class="sort" data-sort="temporarily_sold_out">Mostrar<br>como<br>agotado<br>temporalmente</th>
								<th scope="col" class="sort" data-sort="brand">Marca</th>
								<th scope="col" class="sort" data-sort="category">Familia</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="list">
							<?php if(!empty($products)): ?>
								<?php foreach($products as $product): ?>
									<tr>
									<th class="code" >
											<?php echo $product['code']; ?>
 										</th>
										 <td class="image">
											<?php
											if (file_exists(DOCROOT.'assets/uploads/thumb_'.$product['image']))
											{
												echo Html::anchor('admin/catalogo/productos/info/'.$product['id'], Asset::img('thumb_'.$product['image'], array('alt' => $product['name'], 'class' => 'avatar')), array('title' => $product['name'], 'class' => 'anchor-image'));
												} else{
												echo Html::anchor('admin/catalogo/productos/info/'.$product['id'], Asset::img('thumb_no_image.png', array('alt' => $product['name'], 'class' => 'avatar')), array('title' => $product['name'], 'class' => 'anchor-image'));
												}
												?>
										</td>
										<th class="name" title="<?php echo $product['name_complete']; ?>">
											<?php echo Html::anchor('admin/catalogo/productos/info/'.$product['id'], $product['name']); ?>
										</th>
										<td class="available">
											<?php echo $product['available']; ?>
										</td>
										<td class="price_1">
											<?php echo $product['price_1']; ?>
										</td>
										<td class="price_2">
											<?php echo $product['price_2']; ?>
										</td>
										<td class="price_3">
											<?php echo $product['price_3']; ?>
										</td>
										<td>
											<label class="custom-toggle">
                                                <input type="checkbox" class="toggle-ps" data-product="<?php echo $product['id']; ?>" <?php echo ($product['status']) ? 'checked' : ''; ?>>
                                                <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Sí"></span>
                                            </label>
										</td>
										<td>
											<label class="custom-toggle">
                                                <input type="checkbox" class="toggle-psi" data-product="<?php echo $product['id']; ?>" <?php echo ($product['status_index']) ? 'checked' : ''; ?>>
                                                <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Sí"></span>
                                            </label>
										</td>
										<td>
											<label class="custom-toggle">
                                                <input type="checkbox" class="toggle-soon" data-product="<?php echo $product['id']; ?>" <?php echo ($product['soon']) ? 'checked' : ''; ?>>
                                                <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Sí"></span>
                                            </label>
										</td>
										<td>
											<label class="custom-toggle">
                                                <input type="checkbox" class="toggle-new" data-product="<?php echo $product['id']; ?>" <?php echo ($product['newproduct']) ? 'checked' : ''; ?>>
                                                <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Sí"></span>
                                            </label>
										</td>
										<td>
											<label class="custom-toggle">
                                                <input type="checkbox" class="toggle-out" data-product="<?php echo $product['id']; ?>" <?php echo ($product['temporarily_sold_out']) ? 'checked' : ''; ?>>
                                                <span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Sí"></span>
                                            </label>
										</td>
										
										<td class="brand">
											<?php echo $product['brand']; ?>
										</td>
										<td class="category">
											<?php echo $product['category']; ?>
										</td>
										<td class="text-right">
											<div class="dropdown">
												<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<i class="fas fa-ellipsis-v"></i>
												</a>
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<?php echo Html::anchor('admin/catalogo/productos/info/'.$product['id'], 'Ver', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/catalogo/productos/editar/'.$product['id'], 'Editar', array('class' => 'dropdown-item')); ?>
													<?php echo Html::anchor('admin/catalogo/productos/agregar_foto/'.$product['id'], 'Agregar foto', array('class' => 'dropdown-item')); ?>
													<div class="dropdown-divider"></div>
													<?php echo Html::anchor('admin/catalogo/productos/eliminar/'.$product['id'], 'Eliminar', array('class' => 'dropdown-item delete-item')); ?>
												</div>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<th scope="row">
										No existen registros
									</th>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
				<?php if($pagination != ''): ?>
					<!-- CARD FOOTER -->
					<div class="card-footer py-4">
						<?php echo $pagination; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
