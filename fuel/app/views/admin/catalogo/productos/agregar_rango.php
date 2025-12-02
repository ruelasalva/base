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
								<?php echo Html::anchor('admin/catalogo/productos', 'Montos'); ?>
							</li>
                            <li class="breadcrumb-item">
								<?php echo Html::anchor('admin/catalogo/productos/info/'.$id, Str::truncate($name, 40)); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/catalogo/productos/agregar_rango/'.$id, 'Agregar rango'); ?>
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
						<h3 class="mb-0">Agregar rango</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<?php echo Form::open(array('method' => 'post')); ?>
							<fieldset>
								<div class="form-row">
									<div class="col-md-12 mt-0 mb-3">
										<legend class="mb-0 heading">Información del rango</legend>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['min_quantity']['form-group']; ?>">
											<?php echo Form::label('Cantidad mínima', 'min_quantity', array('class' => 'form-control-label', 'for' => 'min_quantity')); ?>
											<?php echo Form::input('min_quantity', (isset($min_quantity) ? $min_quantity : ''), array('id' => 'min_quantity', 'class' => 'form-control '.$classes['min_quantity']['form-control'], 'placeholder' => 'Cantidad mínima')); ?>
											<?php if(isset($errors['min_quantity'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['min_quantity']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['max_quantity']['form-group']; ?>">
											<?php echo Form::label('Cantidad máxima', 'max_quantity', array('class' => 'form-control-label', 'for' => 'max_quantity')); ?>
											<?php echo Form::input('max_quantity', (isset($max_quantity) ? $max_quantity : ''), array('id' => 'max_quantity', 'class' => 'form-control '.$classes['max_quantity']['form-control'], 'placeholder' => 'Cantidad máxima')); ?>
											<?php if(isset($errors['max_quantity'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['max_quantity']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['price']['form-group']; ?>">
											<?php echo Form::label('Precio', 'price', array('class' => 'form-control-label', 'for' => 'price')); ?>
											<?php echo Form::input('price', (isset($price) ? $price : ''), array('id' => 'price', 'class' => 'form-control '.$classes['price']['form-control'], 'placeholder' => 'Precio')); ?>
											<?php if(isset($errors['price'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['price']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</fieldset>
							<?php echo Form::submit(array('value'=> 'Agregar', 'name'=>'submit', 'class' => 'btn btn-primary')); ?>
						<?php echo Form::close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
