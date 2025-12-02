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
								<?php echo Html::anchor('admin/catalogo/productos/info_archivo/'.$id, 'Ver archivo'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-4 col-5 text-right">
					<?php echo Html::anchor('admin/catalogo/productos/editar_archivo/'.$id, 'Editar archivo', array('class' => 'btn btn-sm btn-neutral')); ?>
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
						<h3 class="mb-0">Ver archivo</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<fieldset>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Información del archivo</legend>
								</div>
								<div class="col-md-8 mb-3">
									<div class="form-group">
										<?php echo Form::label('Nombre o descripción', 'file_name', array('class' => 'form-control-label')); ?>
										<p class="form-control-plaintext"><?php echo $file_name; ?></p>
									</div>
									<div class="form-group">
										<?php echo Form::label('Tipo de archivo', 'file_type', array('class' => 'form-control-label')); ?>
										<p class="form-control-plaintext"><?php echo $file_type; ?></p>
									</div>
									<div class="form-group">
										<?php echo Form::label('Archivo PDF', 'file', array('class' => 'form-control-label')); ?>
										<?php if (!empty($file_path) && file_exists(DOCROOT.$file_path)): ?>
											<a href="<?php echo Uri::base(false).$file_path; ?>" target="_blank" class="btn btn-outline-danger">
												<i class="fa fa-file-pdf-o fa-lg"></i> Ver / Descargar PDF
											</a>
										<?php else: ?>
											<span class="badge badge-danger">Archivo no disponible</span>
										<?php endif; ?>
									</div>
									<?php if (!empty($created_at)): ?>
									<div class="form-group">
										<?php echo Form::label('Creado', 'created_at', array('class' => 'form-control-label')); ?>
										<p class="form-control-plaintext"><?php echo date('d/m/Y H:i', $created_at); ?></p>
									</div>
									<?php endif; ?>
									<?php if (!empty($updated_at)): ?>
									<div class="form-group">
										<?php echo Form::label('Actualizado', 'updated_at', array('class' => 'form-control-label')); ?>
										<p class="form-control-plaintext"><?php echo date('d/m/Y H:i', $updated_at); ?></p>
									</div>
									<?php endif; ?>
								</div>
								<div class="col-md-4 mb-3 text-center">
									<i class="fa fa-file-pdf-o fa-5x text-danger"></i>
									<p class="mt-2 text-muted">Archivo PDF asociado</p>
								</div>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
