<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-8 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Catálogo</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/catalogo/productos', 'Productos'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/catalogo/productos/info/'.$product_id, Str::truncate($name, 40)); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/catalogo/productos/editar_archivo/'.$id, 'Editar archivo'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-4 col-5 text-right">
					<?php echo Html::anchor('admin/catalogo/productos/info_archivo/'.$id, 'Ver archivo', array('class' => 'btn btn-sm btn-neutral')); ?>
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
						<h3 class="mb-0">Editar archivo</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<?php echo Form::open(array('method' => 'post', 'enctype' => 'multipart/form-data')); ?>
						<fieldset>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Información del archivo PDF</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['file']['form-group'] ?? ''; ?>">
										<?php echo Form::label('Archivo PDF', 'file', array('class' => 'form-control-label', 'for' => 'file')); ?>
										<div class="custom-file">
											<?php echo Form::file('file', array(
												'class' => 'custom-file-input',
												'id'    => 'file',
												'accept' => 'application/pdf'
											)); ?>
											<label class="custom-file-label" for="file" id="file-label">
												<?php echo isset($file) && $file != '' ? basename($file) : 'Seleccionar PDF'; ?>
											</label>
										</div>
										<?php if(isset($file) && $file != '' && file_exists(DOCROOT.$file)): ?>
											<div class="mt-2">
												<a href="<?php echo Uri::base(false).$file; ?>" target="_blank" class="btn btn-outline-danger btn-sm">
													<i class="fa fa-file-pdf-o fa-lg"></i> Ver / Descargar actual
												</a>
											</div>
										<?php endif; ?>
										<small id="file-help" class="form-text text-muted">
											Solo se acepta archivo PDF. Tamaño máximo sugerido: 10MB.
										</small>
										<?php if(isset($errors['file'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['file']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['file_type_id']['form-group'] ?? ''; ?>">
										<?php echo Form::label('Tipo de archivo', 'file_type_id', array('class' => 'form-control-label', 'for' => 'file_type_id')); ?>
										<?php echo Form::select('file_type_id', (isset($file_type_id) ? $file_type_id : ''), $file_type_opts, array(
											'class' => 'form-control '.($classes['file_type_id']['form-control'] ?? ''),
											'required' => 'required'
										)); ?>
										<?php if(isset($errors['file_type_id'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['file_type_id']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-12 mb-3">
									<div class="form-group <?php echo $classes['file_name']['form-group'] ?? ''; ?>">
										<?php echo Form::label('Nombre o descripción', 'file_name', array('class' => 'form-control-label', 'for' => 'file_name')); ?>
										<?php echo Form::input('file_name', (isset($file_name) ? $file_name : ''), array(
											'class' => 'form-control '.($classes['file_name']['form-control'] ?? ''),
											'maxlength' => '100',
											'placeholder' => 'Ejemplo: Manual de instalación',
											'required' => 'required'
										)); ?>
										<?php if(isset($errors['file_name'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['file_name']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</fieldset>
						<?php echo Form::submit(array('value'=> 'Guardar', 'name'=>'submit', 'class' => 'btn btn-primary submit-form')); ?>
						<?php echo Form::close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
