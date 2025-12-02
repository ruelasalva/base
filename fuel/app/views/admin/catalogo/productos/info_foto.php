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
								<?php echo Html::anchor('admin/catalogo/productos/info/'.$product_id, $name); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/catalogo/productos/info_foto/'.$id, 'Ver foto'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-4 col-5 text-right">
					<?php echo Html::anchor('admin/catalogo/productos/editar_foto/'.$id, 'Editar foto', array('class' => 'btn btn-sm btn-neutral')); ?>
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
						<h3 class="mb-0">Ver foto</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<fieldset>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Información de la fotografía</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Imagen', 'image', array('class' => 'form-control-label', 'for' => 'image')); ?>
										<?php
											echo Asset::img(
												(isset($image) && $image != '' && file_exists(DOCROOT.'assets/uploads/'.$image)) ? $image : 'sw_no_image.png',
												array('class' => 'dz-preview-img fit-img')
											);
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
</div>
