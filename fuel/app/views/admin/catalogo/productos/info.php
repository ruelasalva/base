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
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/catalogo/productos', 'Productos'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/catalogo/productos/info/'.$id, Str::truncate($name, 40)); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php if($price_per == 'u'): ?>
						<?php echo Html::anchor('admin/catalogo/productos/agregar_rango/'.$id, 'Agregar rango', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php endif; ?>
					<?php echo Html::anchor('admin/catalogo/productos/agregar_archivo/'.$id, 'Agregar archivo', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/catalogo/productos/agregar_foto/'.$id, 'Agregar foto', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/catalogo/productos/editar/'.$id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- PAGE CONTENT -->
<div class="container-fluid mt--6">
	<div class="row">
		<div class="col">
			<div class="card-wrapper">
				<!-- CUSTOM FORM VALIDATION -->
				<div class="card">
					<!-- CARD HEADER -->
					<div class="card-header">
						<h3 class="mb-0">Ver información</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<fieldset>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Información del producto</legend>
								</div>
								<div class="col-md-12 mb-3">
									<div class="form-group">
										<?php echo Form::label('Nombre', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control"><?php echo $name; ?></span>
									</div>
								</div>
								<div class="col-md-12 mb-3">
									<div class="form-group">
										<?php echo Form::label('Decripción proveedor', 'name_order', array('class' => 'form-control-label', 'for' => 'name_order')); ?>
										<span class="form-control"><?php echo $name_order; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Código Interno', 'code', array('class' => 'form-control-label', 'for' => 'code')); ?>
										<span class="form-control"><?php echo $code; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Código del Proveedor', 'code_order', array('class' => 'form-control-label', 'for' => 'code_order')); ?>
										<span class="form-control"><?php echo $code_order; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Unidad de Venta', 'sale_unit_id', array('class' => 'form-control-label', 'for' => 'sale_unit_id')); ?>
										<span class="form-control"><?php echo $sale_unit_id; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Unidad de Compra', 'purchase_unit_id', array('class' => 'form-control-label', 'for' => 'purchase_unit_id')); ?>
										<span class="form-control"><?php echo $purchase_unit_id; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Factor de Conversion', 'factor', array('class' => 'form-control-label', 'for' => 'factor')); ?>
										<span class="form-control"><?php echo $factor; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('SKU', 'sku', array('class' => 'form-control-label', 'for' => 'sku')); ?>
										<span class="form-control"><?php echo $sku; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Categoría', 'category', array('class' => 'form-control-label', 'for' => 'category')); ?>
										<span class="form-control"><?php echo $category; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
									<?php echo Form::label('Grupo', 'subcategory', array('class' => 'form-control-label', 'for' => 'subcategory')); ?>
										<span class="form-control"><?php echo $subcategory; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Marca', 'brand', array('class' => 'form-control-label', 'for' => 'brand')); ?>
										<span class="form-control"><?php echo $brand; ?></span>
									</div>
								</div>
								
								
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Clave SAT', 'claveprodserv', array('class' => 'form-control-label', 'for' => 'claveprodserv')); ?>
										<span class="form-control"><?php echo $claveprodserv; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Unidad SAT', 'claveunidad', array('class' => 'form-control-label', 'for' => 'claveunidad')); ?>
										<span class="form-control"><?php echo $claveunidad; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Cantidad disponible', 'available', array('class' => 'form-control-label', 'for' => 'available')); ?>
										<span class="form-control"><?php echo $available; ?></span>
									</div>
								</div>
								<div class="col-md-12 mb-3">
									<div class="form-group">
										<?php echo Form::label('Descripción larga', 'description', array('class' => 'form-control-label', 'for' => 'description')); ?>
										<div class="form-control from-table form-table-area" style="min-height:120px; overflow:auto;">
											<?php echo !empty($description) ? html_entity_decode($description) : '<span class="text-muted">Sin descripción registrada.</span>'; ?>
										</div>
									</div>
								</div>

								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Peso por producto', 'weight', array('class' => 'form-control-label', 'for' => 'weight')); ?>
										<span class="form-control"><?php echo $weight; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Codigo de Barras', 'codebar', array('class' => 'form-control-label', 'for' => 'codebar')); ?>
										<span class="form-control"><?php echo $codebar; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Precio por', 'price_per', array('class' => 'form-control-label', 'for' => 'price_per')); ?>
										<span class="form-control"><?php echo $price_per_text; ?></span>
									</div>
								</div>
								<?php if($price_per == 'm'): ?>
									<div class="col-md-6 mb-3">
										<div class="form-group">
											<?php echo Form::label('Monto', 'amount', array('class' => 'form-control-label', 'for' => 'amount')); ?>
											<span class="form-control"><?php echo $amount; ?></span>
										</div>
									</div>
								<?php endif; ?>
							</div>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Lista de precios</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Precio original', 'original_price', array('class' => 'form-control-label', 'for' => 'original_price')); ?>
										<span class="form-control"><?php echo $original_price; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Precio (normal)', 'price_1', array('class' => 'form-control-label', 'for' => 'price_1')); ?>
										<span class="form-control"><?php echo $price_1; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Precio (mayorista #1)', 'price_2', array('class' => 'form-control-label', 'for' => 'price_2')); ?>
										<span class="form-control"><?php echo $price_2; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Precio (mayorista #2)', 'price_3', array('class' => 'form-control-label', 'for' => 'price_3')); ?>
										<span class="form-control"><?php echo $price_3; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Imagen', 'image', array('class' => 'form-control-label', 'for' => 'image')); ?>
										<?php
										if (file_exists(DOCROOT.'assets/uploads/'.$image))
										{
											echo Asset::img($image, array('alt' => $name, 'class' => 'img-fluid d-block', 'data-gc-caption','none' => $name));
											}else{
											echo Asset::img('sw_no_image.png', array('alt' => $name, 'class' => 'img-fluid d-block', 'data-gc-caption','none' => $name));
											}
										?>
										<small id="image-help" class="form-text text-muted">Tamaño de la imagen: 640 X 640 px.</small>
									</div>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php if(!empty($prices_wholesales)): ?>
	<!-- TABLE -->
	<div class="row">
		<div class="col">
			<div class="card">
				<!-- CARD HEADER -->
				<div class="card-header border-0">
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Rangos por unidad</h3>
						</div>
						<?php if($price_per == 'u'): ?>
							<?php echo Html::anchor('admin/catalogo/productos/agregar_rango/'.$id, 'Agregar rango', array('class' => 'btn btn-sm btn-neutral')); ?>
						<?php endif; ?>
					</div>
				</div>
				<!-- LIGHT TABLE -->
				<div class="table-responsive">
					<table class="table align-items-center">
						<thead class="thead-light">
							<tr>
								<th scope="col">Cantidad mínima</th>
								<th scope="col">Cantidad máxima</th>
								<th scope="col">Precio</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="list">
							<?php foreach($prices_wholesales as $price_wholesale): ?>
								<tr>
									<td>
										<?php echo $price_wholesale['min_quantity']; ?>
									</td>
									<td>
										<?php echo $price_wholesale['max_quantity']; ?>
									</td>
									<td>
										<?php echo $price_wholesale['price']; ?>
									</td>
									<td class="text-right">
										<div class="dropdown">
											<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="fas fa-ellipsis-v"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
												<?php echo Html::anchor('admin/catalogo/productos/info_rango/'.$price_wholesale['id'], 'Ver', array('class' => 'dropdown-item')); ?>
												<?php echo Html::anchor('admin/catalogo/productos/editar_rango/'.$price_wholesale['id'], 'Editar', array('class' => 'dropdown-item')); ?>
												<div class="dropdown-divider"></div>
												<?php echo Html::anchor('admin/catalogo/productos/eliminar_rango/'.$price_wholesale['id'], 'Eliminar', array('class' => 'dropdown-item delete-item')); ?>
											</div>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php if(!empty($galleries)): ?>
	<!-- TABLE -->
	<div class="row">
		<div class="col">
			<div class="card">
				<!-- CARD HEADER -->
				<div class="card-header border-0">
					<div class="form-row">
						<div class="col-md-9">
							<h3 class="mb-0">Galería de imágenes</h3>
						</div>
					</div>
				</div>
				<!-- LIGHT TABLE -->
				<div class="table-responsive">
					<table class="table align-items-center table-flush sorted-table-product-images">
						<thead class="thead-light">
							<tr>
								<th scope="col">Imagen</th>
								<th scope="col">Orden</th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody class="list">
							<?php foreach($galleries as $image): ?>
								<tr data-item-id="<?php echo $image['id']; ?>">
									<th>
										<?php
											if (file_exists(DOCROOT.'assets/uploads/'.$image['image']) && !empty($image['image'])) {
												echo Asset::img($image['image'], array('class' => 'avatar', 'alt' => $image['image']));
											} else {
												echo Asset::img('sw_no_image.png', array('class' => 'avatar', 'alt' => $image['image']));
											}
										?>
									</th>
									<td>
										<i class="fas fa-arrows-alt-v" title="Arrastra la fila para modificar el orden"></i> <span class="order-num"><?php echo $image['order']; ?></span>
									</td>
									<td class="text-right">
										<div class="dropdown">
											<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="fas fa-ellipsis-v"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
												<?php echo Html::anchor('admin/catalogo/productos/info_foto/'.$image['id'], 'Ver', array('class' => 'dropdown-item')); ?>
												<?php echo Html::anchor('admin/catalogo/productos/editar_foto/'.$image['id'], 'Editar', array('class' => 'dropdown-item')); ?>
												<div class="dropdown-divider"></div>
												<?php echo Html::anchor('admin/catalogo/productos/eliminar_foto/'.$image['id'], 'Eliminar', array('class' => 'dropdown-item delete-item')); ?>
											</div>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	
	
	<?php if(!empty($files)): ?>
		<!-- TABLE DE ARCHIVOS / MANUALES / PDF -->
		<div class="row">
			<div class="col">
				<div class="card">
					<!-- CARD HEADER -->
					<div class="card-header border-0">
						<div class="form-row">
							<div class="col-md-9">
								<h3 class="mb-0">Fichas técnicas, manuales y archivos</h3>
							</div>
						</div>
					</div>
					<!-- LIGHT TABLE -->
					<div class="table-responsive">
						<table class="table align-items-center table-flush sorted-table-product-files">
							<thead class="thead-light">
								<tr>
									<th scope="col">Archivo</th>
									<th scope="col">Nombre/Descripción</th>
									<th scope="col">Tipo</th>
									<th scope="col">Descargas</th>
									<th scope="col">Fecha</th>
									<th scope="col"></th>
								</tr>
							</thead>
							<tbody class="list">
								<?php foreach($files as $file): ?>
								<tr data-item-id="<?php echo $file['id']; ?>">
									<td>
										<?php if (!empty($file['file_path']) && file_exists(DOCROOT.$file['file_path'])): ?>
											<a href="<?php echo Uri::base(false).$file['file_path']; ?>" target="_blank">
												<i class="fa fa-file-pdf-o text-danger fa-lg"></i>
												<span class="ml-2">Descargar</span>
											</a>
										<?php else: ?>
											<span class="text-muted"><i class="fa fa-file-pdf-o"></i> Archivo no disponible</span>
										<?php endif; ?>
									</td>
									<td><?php echo e($file['file_name']); ?></td>
									<td><?php echo e($file['file_type']); ?></td>
									<td><?php echo e($file['downloads']); ?></td>
									<td>
										<?php
										if(!empty($file['updated_at'])){
											echo date('d/m/Y H:i', $file['updated_at']);
										} elseif(!empty($file['created_at'])){
											echo date('d/m/Y H:i', $file['created_at']);
										} else {
											echo '-';
										}
										?>
									</td>
									<td class="text-right">
										<div class="dropdown">
											<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<i class="fas fa-ellipsis-v"></i>
											</a>
											<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
												<?php echo Html::anchor('admin/catalogo/productos/info_archivo/'.$file['id'], 'Ver', array('class' => 'dropdown-item')); ?>
												<?php echo Html::anchor('admin/catalogo/productos/editar_archivo/'.$file['id'], 'Editar', array('class' => 'dropdown-item')); ?>
												<div class="dropdown-divider"></div>
												<?php echo Html::anchor('admin/catalogo/productos/eliminar_archivo/'.$file['id'], 'Eliminar', array('class' => 'dropdown-item delete-item')); ?>
											</div>
										</div>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>

</div>
