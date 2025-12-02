<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Slides</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/slides', 'Slides'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/slides/info/'.$id, 'Slide'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/slides/editar/'.$id, 'Editar'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/slides/info/'.$id, 'Ver', array('class' => 'btn btn-sm btn-neutral')); ?>
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
						<h3 class="mb-0">Editar</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<?php echo Form::open(array('method' => 'post')); ?>
							<fieldset>
								<div class="form-row">
									<div class="col-md-12 mt-0 mb-3">
										<legend class="mb-0 heading">Información del slide</legend>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['image']['form-group']; ?>">
											<?php echo Form::label('Imagen', 'image', array('class' => 'form-control-label', 'for' => 'image')); ?>
											<?php if(isset($image) && $image != ''): ?>
												<div class="dropzone dropzone-single dz-clickable dz-max-files-reached" data-toggle="dropzone-img" data-dropzone-url="<?php echo Uri::create('admin/ajax/image'); ?>" data-id="image" data-width="1920" data-height="600" data-last-file="<?php echo (isset($image)) ? $image : ''; ?>">
													<div class="fallback">
														<div class="custom-file">
															<?php echo Form::file('image', array('class' => 'custom-file-input', 'id' => 'image')); ?>
															<label class="custom-file-label" for="image">Seleccionar archivo</label>
														</div>
													</div>
													<div class="dz-preview dz-preview-single">
														<div class="dz-preview-cover dz-processing dz-image-preview dz-complete">
															<?php echo Asset::img($image, array('class' => 'dz-preview-img', 'data-dz-thumbnail' => 'true')) ?>
														</div>
													</div>
												</div>
											<?php else: ?>
												<div class="dropzone dropzone-single" data-toggle="dropzone-img" data-dropzone-url="<?php echo Uri::create('admin/ajax/image'); ?>" data-id="image" data-width="1920" data-height="600" data-last-file="<?php echo (isset($image)) ? $image : ''; ?>">
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
											<small id="image-help" class="form-text text-muted">Tamaño de la imagen: 1920 X 600 px .</small>
											<?php echo Form::hidden('image', (isset($image) ? $image : ''), array('id' => 'image', 'class' => 'form-control '.$classes['image']['form-control'])); ?>
											<?php if(isset($errors['image'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['image']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3"></div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['url']['form-group']; ?>">
											<?php echo Form::label('URL', 'url', array('class' => 'form-control-label', 'for' => 'url')); ?>
											<?php echo Form::input('url', (isset($url) ? $url : ''), array('id' => 'url', 'class' => 'form-control '.$classes['url']['form-control'], 'placeholder' => 'URL')); ?>
											<?php if(isset($errors['url'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['url']; ?>
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
