<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Blog</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/blog/publicaciones', 'Publicaciones'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/blog/publicaciones/agregar', 'Agregar'); ?>
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
									<legend class="mb-0 heading">Información de la publicación</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['image']['form-group']; ?>">
										<?php echo Form::label('Imagen', 'image', array('class' => 'form-control-label', 'for' => 'image')); ?>
										<?php if(isset($image) && $image != ''): ?>
											<div class="dropzone dropzone-single dz-clickable dz-max-files-reached" data-toggle="dropzone-img" data-dropzone-url="<?php echo Uri::create('admin/ajax/post_image'); ?>" data-id="image" data-width="820" data-height="380" data-last-file="<?php echo (isset($image)) ? $image : ''; ?>">
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
											<div class="dropzone dropzone-single" data-toggle="dropzone-img" data-dropzone-url="<?php echo Uri::create('admin/ajax/post_image'); ?>" data-id="image" data-width="820" data-height="380" data-last-file="<?php echo (isset($image)) ? $image : ''; ?>">
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
										<small id="image-help" class="form-text text-muted">Tamaño de la imagen: 820 X 380 px .</small>
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
									<div class="form-group <?php echo $classes['title']['form-group']; ?>">
										<?php echo Form::label('Título', 'title', array('class' => 'form-control-label', 'for' => 'title')); ?>
										<?php echo Form::input('title', (isset($title) ? $title : ''), array('id' => 'title', 'class' => 'form-control '.$classes['title']['form-control'], 'placeholder' => 'Título')); ?>
										<?php if(isset($errors['title'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['title']; ?>
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
									<div class="form-group <?php echo $classes['intro']['form-group']; ?>">
										<?php echo Form::label('Introducción', 'intro', array('class' => 'form-control-label', 'for' => 'intro')); ?>
										<div id="post-intro"><?php echo (isset($intro) ? $intro : ''); ?></div>
										<?php echo Form::hidden('intro', '', array('id' => 'intro', 'class' => 'form-control '.$classes['intro']['form-control'])); ?>
										<?php if(isset($errors['intro'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['intro']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['content']['form-group']; ?>">
										<?php echo Form::label('Contenido', 'content', array('class' => 'form-control-label', 'for' => 'content')); ?>
										<div id="post-content"><?php echo (isset($content) ? $content : ''); ?></div>
										<?php echo Form::hidden('content', '', array('id' => 'content', 'class' => 'form-control '.$classes['content']['form-control'])); ?>
										<?php if(isset($errors['content'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['content']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['labels']['form-group']; ?>">
										<?php echo Form::label('Etiquetas', 'labels', array('class' => 'form-control-label', 'for' => 'labels')); ?>
										<?php echo Form::select('labels[]', (isset($labels) ? $labels : 'none'), $labels_opts, array('id' => 'labels', 'class' => 'form-control '.$classes['labels']['form-control'], 'data-toggle' => 'select', 'multiple')); ?>
										<?php if(isset($errors['labels'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['labels']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['date']['form-group']; ?>">
										<?php echo Form::label('Fecha de publicación', 'date', array('class' => 'form-control-label', 'for' => 'date')); ?>
										<?php echo Form::input('date', (isset($date) ? $date : ''), array('id' => 'date', 'class' => 'form-control datepicker '.$classes['date']['form-control'], 'placeholder' => 'Fecha de publicación')); ?>
										<?php if(isset($errors['date'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['date']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['time']['form-group']; ?>">
										<?php echo Form::label('Hora de publicación', 'time', array('class' => 'form-control-label', 'for' => 'time')); ?>
										<?php echo Form::input('time', (isset($time) ? $time : ''), array('id' => 'example-time-input', 'class' => 'form-control '.$classes['time']['form-control'], 'type' => 'time')); ?>
										<?php if(isset($errors['time'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['time']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</fieldset>
						<?php echo Form::submit(array('value'=> 'Agregar', 'name'=>'submit', 'id' => 'add-post', 'class' => 'btn btn-primary submit-form')); ?>
						<?php echo Form::close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
