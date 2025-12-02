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
								<?php echo Html::anchor('admin/blog/publicaciones/info/'.$id, $title); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/blog/publicaciones/editar/'.$id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
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
									<legend class="mb-0 heading">Información de la publicación</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Imagen', 'image', array('class' => 'form-control-label', 'for' => 'image')); ?>
										<?php echo Asset::img($image, array('class' => 'dz-preview-img fit-img')) ?>
										<small id="image-help" class="form-text text-muted">Tamaño de la imagen: 820 X 380 px .</small>
									</div>
								</div>
								<div class="col-md-6 mb-3"></div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Título', 'title', array('class' => 'form-control-label', 'for' => 'title')); ?>
										<span class="form-control"><?php echo $title; ?></span>
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
                                        <?php echo Form::label('Introducción', 'intro', array('class' => 'form-control-label', 'for' => 'intro')); ?>
                                        <span class="form-control from-table form-table-area"><?php echo $intro; ?></span>
                                    </div>
                                </div>
								<div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <?php echo Form::label('Contenido', 'content', array('class' => 'form-control-label', 'for' => 'content')); ?>
                                        <span class="form-control from-table form-table-area"><?php echo $content; ?></span>
                                    </div>
                                </div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Etiquetas', 'labels', array('class' => 'form-control-label', 'for' => 'labels')); ?>
										<span class="form-control"><?php echo $labels; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Fecha de publicación', 'date', array('class' => 'form-control-label', 'for' => 'date')); ?>
										<span class="form-control"><?php echo $date; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Hora de publicación', 'time', array('class' => 'form-control-label', 'for' => 'time')); ?>
										<span class="form-control"><?php echo $time; ?></span>
									</div>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
