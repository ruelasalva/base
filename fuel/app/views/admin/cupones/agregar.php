<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Cupones</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/cupones', 'Cupones'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/cupones/agregar', 'Agregar'); ?>
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
										<legend class="mb-0 heading">Información del cupón</legend>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['name']['form-group']; ?>">
											<?php echo Form::label('Nombre descriptivo', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
											<?php echo Form::input('name', (isset($name) ? $name : ''), array('id' => 'name', 'class' => 'form-control '.$classes['name']['form-control'], 'placeholder' => 'Nombre descriptivo')); ?>
											<?php if(isset($errors['name'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['name']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['code']['form-group']; ?>">
											<?php echo Form::label('Texto del cupón', 'code', array('class' => 'form-control-label', 'for' => 'code')); ?>
											<?php echo Form::input('code', (isset($code) ? $code : ''), array('id' => 'code', 'class' => 'form-control '.$classes['code']['form-control'], 'placeholder' => 'Texto del cupón')); ?>
											<?php if(isset($errors['code'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['code']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['discount']['form-group']; ?>">
											<?php echo Form::label('Descuento', 'discount', array('class' => 'form-control-label', 'for' => 'discount')); ?>
											<?php echo Form::input('discount', (isset($discount) ? $discount : ''), array('id' => 'discount', 'class' => 'form-control '.$classes['discount']['form-control'], 'placeholder' => 'Descuento')); ?>
											<?php if(isset($errors['discount'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['discount']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['quantity']['form-group']; ?>">
											<?php echo Form::label('Cantidad de cupones', 'quantity', array('class' => 'form-control-label', 'for' => 'quantity')); ?>
											<?php echo Form::input('quantity', (isset($quantity) ? $quantity : ''), array('id' => 'quantity', 'class' => 'form-control '.$classes['quantity']['form-control'], 'placeholder' => 'Cantidad de cupones')); ?>
											<?php if(isset($errors['quantity'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['quantity']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
                                    <div class="col-md-6 mb-3">
    									<div class="form-group <?php echo $classes['start_date']['form-group']; ?>">
    										<?php echo Form::label('Fecha de inicio', 'start_date', array('class' => 'form-control-label', 'for' => 'start_date')); ?>
    										<?php echo Form::input('start_date', (isset($start_date) ? $start_date : ''), array('id' => 'start_date', 'class' => 'form-control datepicker '.$classes['start_date']['form-control'], 'placeholder' => 'Fecha de inicio')); ?>
    										<?php if(isset($errors['start_date'])) : ?>
    											<div class="invalid-feedback">
    												<?php echo $errors['start_date']; ?>
    											</div>
    										<?php endif; ?>
    									</div>
    								</div>
                                    <div class="col-md-6 mb-3">
    									<div class="form-group <?php echo $classes['end_date']['form-group']; ?>">
    										<?php echo Form::label('Fecha final', 'end_date', array('class' => 'form-control-label', 'for' => 'end_date')); ?>
    										<?php echo Form::input('end_date', (isset($end_date) ? $end_date : ''), array('id' => 'end_date', 'class' => 'form-control datepicker '.$classes['end_date']['form-control'], 'placeholder' => 'Fecha final')); ?>
    										<?php if(isset($errors['end_date'])) : ?>
    											<div class="invalid-feedback">
    												<?php echo $errors['end_date']; ?>
    											</div>
    										<?php endif; ?>
    									</div>
    								</div>
									<div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['minimum']['form-group']; ?>">
											<?php echo Form::label('Cantidad mínima', 'minimum', array('class' => 'form-control-label', 'for' => 'minimum')); ?>
											<?php
												echo Form::select('minimum', (isset($minimum) ? $minimum : 1), array(
													1 => 'Sí',
													0 => 'No'
												), array('id' => 'minimum', 'class' => 'form-control '.$classes['minimum']['form-control'], 'data-toggle' => 'select'));
											?>
											<?php if(isset($errors['minimum'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['minimum']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<?php $style = isset($minimum) ? ($minimum == 0) ? 'display: none;' : '' : ''; ?>
									<div id="total_minimum_div" style="<?php echo $style; ?>" class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['total_minimum']['form-group']; ?>">
											<?php echo Form::label('Mínimo del pedido', 'total_minimum', array('class' => 'form-control-label', 'for' => 'total_minimum')); ?>
											<?php echo Form::input('total_minimum', (isset($total_minimum) ? $total_minimum : ''), array('id' => 'total_minimum', 'class' => 'form-control '.$classes['total_minimum']['form-control'], 'placeholder' => 'Mínimo del pedido')); ?>
											<?php if(isset($errors['total_minimum'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['total_minimum']; ?>
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
