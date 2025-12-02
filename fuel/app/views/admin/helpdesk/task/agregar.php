<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Pendientes</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/helpdesk/task', 'Pendientes'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/helpdesk/task/agregar', 'Agregar'); ?>
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
									<legend class="mb-0 heading">Información Pendiente </legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['employee_id']['form-group']; ?>">
										<?php echo Form::label('Usuario', 'employee_id', array('class' => 'form-control-label', 'for' => 'employee_id')); ?>
										<?php echo Form::select('employee_id', Input::post('employee_id', isset($employee_id) ? $employee_id : 'none'), $employee_opts, array('id' => 'employee_id', 'class' => 'form-control '.$classes['employee_id']['form-control'], 'data-toggle' => 'select')); ?>
										<?php if(isset($errors['employee_id'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['employee_id']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-6 mb-3">
									<div class="form-group <?php echo $classes['description']['form-group']; ?>">
										<?php echo Form::label('Descripción', 'description', array('class' => 'form-control-label', 'for' => 'description')); ?>
										<?php echo Form::textarea('description', (isset($description) ? $description : ''), array('id' => 'Description', 'class' => 'form-control '.$classes['description']['form-control'], 'placeholder' => 'Descripción', 'rows' => 7)); ?>
										<?php if(isset($errors['description'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['description']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['date']['form-group']; ?>">
										<?php echo Form::label('Fecha Compromiso', 'date', array('class' => 'form-control-label', 'for' => 'date')); ?>
										<?php echo Form::input('date', (isset($date) ? $date : ''), array('id' => 'date', 'class' => 'form-control datepicker '.$classes['date']['form-control'], 'placeholder' => 'Fecha de publicación')); ?>
										<?php if(isset($errors['date'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['date']; ?>
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
