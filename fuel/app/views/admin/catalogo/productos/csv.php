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
								<?php echo Html::anchor('admin/catalogo/productos/csv', 'Carga masiva por CSV'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor(Uri::base(false).'assets/catalogos/products.csv', 'Descargar plantilla', array('class' => 'btn btn-sm btn-neutral')); ?>
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
						<h3 class="mb-0">Carga masiva por CSV</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<?php echo Form::open(array('method' => 'post', 'enctype' => 'multipart/form-data')); ?>
                            <fieldset>
								<div class="form-row">
									<div class="col-md-12 mt-0 mb-3">
										<legend class="mb-0 heading">Información de los productos</legend>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['file']['form-group']; ?>">
											<?php echo Form::label('Archivo CSV', 'file', array('class' => 'form-control-label', 'for' => 'file')); ?>
											<div class="custom-file">
												<?php echo Form::input('file', (isset($file) ? $file : ''), array('id' => 'fileX', 'type' => 'file', 'class' => 'custom-file-input '.$classes['file']['form-control'], 'lang' => 'es')); ?>
												<label class="custom-file-label" for="fileX">Archivo en formato CSV</label>
											</div>
											<?php if(isset($errors['file'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['file']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</fieldset>
							<?php echo Form::submit(array('value'=> 'Enviar', 'name'=>'submit', 'class' => 'btn btn-primary')); ?>
						<?php echo Form::close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
