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
								<?php echo Html::anchor('admin/ventas', 'Ventas'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/ventas/info/'.$sale_id, '#'.$sale_id); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/ventas/editar_factura/'.$id, 'Modificar Archivos'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-4 col-5 text-right">
                    <?php echo Html::anchor('admin/ventas/info/'.$id, 'Ver venta', array('class' => 'btn btn-sm btn-neutral')); ?>
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
										<legend class="mb-0 heading">Información de los archivos</legend>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['pdf']['form-group']; ?>">
											<?php echo Form::label('PDF', 'pdf', array('class' => 'form-control-label', 'for' => 'pdf')); ?>
											<div class="custom-file">
												<?php echo Form::input('pdf', (isset($pdf) ? $pdf : ''), array('id' => 'pdf', 'type' => 'file', 'class' => 'custom-file-input '.$classes['pdf']['form-control'], 'lang' => 'es')); ?>
												<label class="custom-file-label" for="file">Archivo en en formato PDF (Máximo 20MB)</label>
											</div>
											<?php if(isset($errors['pdf'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['pdf']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['xml']['form-group']; ?>">
											<?php echo Form::label('XML', 'xml', array('class' => 'form-control-label', 'for' => 'xml')); ?>
											<div class="custom-file">
												<?php echo Form::input('xml', (isset($xml) ? $xml : ''), array('id' => 'xml', 'type' => 'file', 'class' => 'custom-file-input '.$classes['xml']['form-control'], 'lang' => 'es')); ?>
												<label class="custom-file-label" for="file">Archivo en en formato XML (Máximo 20MB)</label>
											</div>
											<?php if(isset($errors['xml'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['xml']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</fieldset>
							<?php echo Form::submit(array('value'=> 'Guardar', 'name'=>'submit', 'class' => 'btn btn-primary')); ?>
						<?php echo Form::close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
