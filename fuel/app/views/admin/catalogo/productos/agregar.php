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
								<?php echo Html::anchor('admin/catalogo/productos/agregar', 'Agregar'); ?>
							</li>
						</ol>
					</nav>
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
						<h3 class="mb-0">Agregar</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<?php echo Form::open(array('method' => 'post')); ?>
						<fieldset>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Información del producto</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['name']['form-group']; ?>">
										<?php echo Form::label('Nombre', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<?php echo Form::input('name', (isset($name) ? $name : ''), array('id' => 'name', 'class' => 'form-control '.$classes['name']['form-control'], 'placeholder' => 'Nombre')); ?>
										<?php if(isset($errors['name'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['name']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['category']['form-group']; ?>">
										<?php echo Form::label('Categoría', 'category', array('class' => 'form-control-label', 'for' => 'category')); ?>
										<?php echo Form::select('category', (isset($category) ? $category : 'none'), $category_opts, array('id' => 'category', 'class' => 'form-control '.$classes['category']['form-control'], 'data-toggle' => 'select')); ?>
										<?php if(isset($errors['category'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['category']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['subcategory']['form-group']; ?>">
										<?php echo Form::label('Grupo', 'subcategory', array('class' => 'form-control-label', 'for' => 'subcategory')); ?>
										<?php echo Form::select('subcategory', (isset($subcategory) ? $subcategory : 'none'), $subcategory_opts, array('id' => 'subcategory', 'class' => 'form-control '.$classes['subcategory']['form-control'], 'data-toggle' => 'select')); ?>
										<?php if(isset($errors['subcategory'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['subcategory']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['brand']['form-group']; ?>">
										<?php echo Form::label('Marca', 'brand', array('class' => 'form-control-label', 'for' => 'brand')); ?>
										<?php echo Form::select('brand', (isset($brand) ? $brand : 'none'), $brand_opts, array('id' => 'brand', 'class' => 'form-control '.$classes['brand']['form-control'], 'data-toggle' => 'select')); ?>
										<?php if(isset($errors['brand'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['brand']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['code']['form-group']; ?>">
										<?php echo Form::label('Código', 'code', array('class' => 'form-control-label', 'for' => 'code')); ?>
										<?php echo Form::input('code', (isset($code) ? $code : ''), array('id' => 'code', 'class' => 'form-control '.$classes['code']['form-control'], 'placeholder' => 'Código')); ?>
										<?php if(isset($errors['code'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['code']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['sku']['form-group']; ?>">
										<?php echo Form::label('SKU', 'sku', array('class' => 'form-control-label', 'for' => 'sku')); ?>
										<?php echo Form::input('sku', (isset($sku) ? $sku : ''), array('id' => 'sku', 'class' => 'form-control '.$classes['sku']['form-control'], 'placeholder' => 'SKU')); ?>
										<?php if(isset($errors['sku'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['sku']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['claveprodserv']['form-group']; ?>">
										<?php echo Form::label('Clave SAT', 'claveprodserv', array('class' => 'form-control-label', 'for' => 'claveprodserv')); ?>
										<?php echo Form::input('claveprodserv', (isset($claveprodserv) ? $claveprodserv : ''), array('id' => 'claveprodserv', 'class' => 'form-control '.$classes['claveprodserv']['form-control'], 'placeholder' => 'Clave SAT')); ?>
										<?php if(isset($errors['claveprodserv'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['claveprodserv']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['claveunidad']['form-group']; ?>">
										<?php echo Form::label('Unidad SAT', 'claveunidad', array('class' => 'form-control-label', 'for' => 'claveunidad')); ?>
										<?php echo Form::input('claveunidad', (isset($claveunidad) ? $claveunidad : ''), array('id' => 'claveunidad', 'class' => 'form-control '.$classes['claveunidad']['form-control'], 'placeholder' => 'Unidad SAT')); ?>
										<?php if(isset($errors['claveunidad'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['claveunidad']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['available']['form-group']; ?>">
										<?php echo Form::label('Cantidad disponible', 'available', array('class' => 'form-control-label', 'for' => 'available')); ?>
										<?php echo Form::input('available', (isset($available) ? $available : ''), array('id' => 'available', 'class' => 'form-control '.$classes['available']['form-control'], 'placeholder' => 'Cantidad disponible')); ?>
										<?php if(isset($errors['available'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['available']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-12 mb-3">
									<div class="form-group <?php echo $classes['description']['form-group']; ?>">
										<?php echo Form::label('Descripción', 'description', array('class' => 'form-control-label', 'for' => 'description')); ?>
										<?php echo Form::textarea('description', (isset($description) ? $description : ''), array(
											'id' => 'description', // ESTE ID ES FUNDAMENTAL PARA QUE FUNCIONE
											'class' => 'form-control '.$classes['description']['form-control'],
											'placeholder' => 'Descripción',
											'rows' => 7
										)); ?>
										<?php if(isset($errors['description'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['description']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['weight']['form-group']; ?>">
										<?php echo Form::label('Peso del producto', 'weight', array('class' => 'form-control-label', 'for' => 'weight')); ?>
										<?php echo Form::input('weight', (isset($weight) ? $weight : ''), array('id' => 'weight', 'class' => 'form-control '.$classes['weight']['form-control'], 'placeholder' => 'Peso del producto')); ?>
										<?php if(isset($errors['weight'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['weight']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['codebar']['form-group']; ?>">
										<?php echo Form::label('Codigo de barras', 'codebar', array('class' => 'form-control-label', 'for' => 'codebar')); ?>
										<?php echo Form::input('codebar', (isset($codebar) ? $codebar : ''), array('id' => 'codebar', 'class' => 'form-control '.$classes['codebar']['form-control'], 'placeholder' => 'Codigo de barras')); ?>
										<?php if(isset($errors['codebar'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['codebar']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['price_per']['form-group']; ?>">
										<?php echo Form::label('Precio por', 'price_per', array('class' => 'form-control-label', 'for' => 'price_per')); ?>
										<?php echo Form::select('price_per', (isset($price_per) ? $price_per : 'none'), $price_per_opts, array('id' => 'price_per', 'class' => 'form-control '.$classes['price_per']['form-control'], 'data-toggle' => 'select')); ?>
										<?php if(isset($errors['price_per'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['price_per']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<?php $style = (isset($price_per)) ? ($price_per == 'm') ? '' : 'display: none' : 'display: none'; ?>
								<div id="amount_div" class="col-md-6 mb-3" style="<?php echo $style; ?>">
									<div class="form-group <?php echo $classes['amount']['form-group']; ?>">
										<?php echo Form::label('Monto', 'amount', array('class' => 'form-control-label', 'for' => 'amount')); ?>
										<?php echo Form::select('amount', (isset($amount) ? $amount : 'none'), $amount_opts, array('id' => 'amount', 'class' => 'form-control '.$classes['amount']['form-control'], 'data-toggle' => 'select')); ?>
										<?php if(isset($errors['amount'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['amount']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Lista de precios</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['original_price']['form-group']; ?>">
										<?php echo Form::label('Precio original', 'original_price', array('class' => 'form-control-label', 'for' => 'original_price')); ?>
										<?php echo Form::input('original_price', (isset($original_price) ? $original_price : ''), array('id' => 'original_price', 'class' => 'form-control '.$classes['original_price']['form-control'], 'placeholder' => 'Precio original')); ?>
										<?php if(isset($errors['original_price'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['original_price']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['price_1']['form-group']; ?>">
										<?php echo Form::label('Precio (normal)', 'price_1', array('class' => 'form-control-label', 'for' => 'price_1')); ?>
										<?php echo Form::input('price_1', (isset($price_1) ? $price_1 : ''), array('id' => 'price_1', 'class' => 'form-control '.$classes['price_1']['form-control'], 'placeholder' => 'Precio (normal)')); ?>
										<?php if(isset($errors['price_1'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['price_1']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['price_2']['form-group']; ?>">
										<?php echo Form::label('Precio (mayorista #2)', 'price_2', array('class' => 'form-control-label', 'for' => 'price_2')); ?>
										<?php echo Form::input('price_2', (isset($price_2) ? $price_2 : ''), array('id' => 'price_2', 'class' => 'form-control '.$classes['price_2']['form-control'], 'placeholder' => 'Precio (mayorista #2)')); ?>
										<?php if(isset($errors['price_2'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['price_2']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['price_3']['form-group']; ?>">
										<?php echo Form::label('Precio (mayorista #2)', 'price_3', array('class' => 'form-control-label', 'for' => 'price_3')); ?>
										<?php echo Form::input('price_3', (isset($price_3) ? $price_3 : ''), array('id' => 'price_3', 'class' => 'form-control '.$classes['price_3']['form-control'], 'placeholder' => 'Precio (mayorista #2)')); ?>
										<?php if(isset($errors['price_3'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['price_3']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['image']['form-group']; ?>">
										<?php echo Form::label('Imagen', 'image', array('class' => 'form-control-label', 'for' => 'image')); ?>
										<?php if(isset($image) && $image != ''): ?>
											<div class="dropzone dropzone-single dz-clickable dz-max-files-reached" data-toggle="dropzone-img" data-dropzone-url="<?php echo Uri::create('admin/ajax/product_image'); ?>" data-id="image" data-width="640" data-height="640" data-last-file="<?php echo (isset($image)) ? $image : ''; ?>">
												<div class="fallback">
													<div class="custom-file">
														<?php echo Form::file('image', array('class' => 'custom-file-input', 'id' => 'image')); ?>
														<label class="custom-file-label" for="image">Seleccionar archivo</label>
													</div>
												</div>
												<div class="dz-preview dz-preview-single">
													<div class="dz-preview-cover dz-processing dz-image-preview dz-complete">
														<?php
														if (file_exists(DOCROOT.'assets/uploads/'.$image))
														{
															echo Asset::img($image, array('class' => 'dz-preview-img', 'data-dz-thumbnail' => 'true'));
															}else{
															echo Asset::img('sw_no_image.png', array('class' => 'dz-preview-img', 'data-dz-thumbnail' => 'true'));
															}
														?>
													</div>
												</div>
											</div>
										<?php else: ?>
											<div class="dropzone dropzone-single" data-toggle="dropzone-img" data-dropzone-url="<?php echo Uri::create('admin/ajax/product_image'); ?>" data-id="image" data-width="640" data-height="640" data-last-file="<?php echo (isset($image)) ? $image : ''; ?>">
												<div class="fallback">
													<div class="custom-file">
														<?php echo Form::file('image', array('class' => 'custom-file-input', 'id' => 'image')); ?>
														<label class="custom-file-label" for="image">Seleccionar archivo</label>
													</div>
												</div>
												<div class="dz-preview dz-preview-single">
													<div class="dz-preview-cover">
														<img class="dz-preview-img" src="..." alt="..." data-dz-thumbnail>
													</div>
												</div>
											</div>
										<?php endif; ?>
										<small id="image-help" class="form-text text-muted">Tamaño de la imagen: 640 X 640 px.</small>
										<?php echo Form::hidden('image', (isset($image) ? $image : ''), array('id' => 'image', 'class' => 'form-control '.$classes['image']['form-control'])); ?>
										<?php if(isset($errors['image'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['image']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</fieldset>
						<?php echo Form::submit(array('value'=> 'Agregar', 'name'=>'submit', 'class' => 'btn btn-primary submit-form')); ?>
						<?php echo Form::close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo Asset::js('admin/catalogo/productos-vue.js'); ?>