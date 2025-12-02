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
								<?php echo Html::anchor('admin/catalogo/montos', 'Montos'); ?>
							</li>
                            <li class="breadcrumb-item">
								<?php echo Html::anchor('admin/catalogo/montos/info/'.$amount_id, Str::truncate($name, 40)); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/catalogo/montos/editar_rango/'.$id, 'Editar rango'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/catalogo/montos/info_rango/'.$id, 'Ver rango', array('class' => 'btn btn-sm btn-neutral')); ?>
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
						<h3 class="mb-0">Editar rango</h3>
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
										<div class="form-group <?php echo $classes['min_amount']['form-group']; ?>">
											<?php echo Form::label('Monto mínimo', 'min_amount', array('class' => 'form-control-label', 'for' => 'min_amount')); ?>
											<?php echo Form::input('min_amount', (isset($min_amount) ? $min_amount : ''), array('id' => 'min_amount', 'class' => 'form-control '.$classes['min_amount']['form-control'], 'placeholder' => 'Monto mínimo')); ?>
											<?php if(isset($errors['min_amount'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['min_amount']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['max_amount']['form-group']; ?>">
											<?php echo Form::label('Monto máximo', 'max_amount', array('class' => 'form-control-label', 'for' => 'max_amount')); ?>
											<?php echo Form::input('max_amount', (isset($max_amount) ? $max_amount : ''), array('id' => 'max_amount', 'class' => 'form-control '.$classes['max_amount']['form-control'], 'placeholder' => 'Monto máximo')); ?>
											<?php if(isset($errors['max_amount'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['max_amount']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['percentage']['form-group']; ?>">
											<?php echo Form::label('Porcentaje de descuento', 'percentage', array('class' => 'form-control-label', 'for' => 'percentage')); ?>
											<?php echo Form::input('percentage', (isset($percentage) ? $percentage : ''), array('id' => 'percentage', 'class' => 'form-control '.$classes['percentage']['form-control'], 'placeholder' => 'Porcentaje de descuento')); ?>
											<?php if(isset($errors['percentage'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['percentage']; ?>
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
