<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Tickets</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/crm/ticket', 'Tickets'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/crm/ticket/agregar', 'Agregar'); ?>
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
									<legend class="mb-0 heading">Información del Ticket</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['type_id']['form-group']; ?>">
										<?php echo Form::label('Tipo de ticket', 'type_id', array('class' => 'form-control-label', 'for' => 'type_id')); ?>
										<?php echo Form::select('type_id', Input::post('type_id', isset($type_id) ? $type_id : 'none'), $typeticket_opts, array('id' => 'type_id', 'class' => 'form-control '.$classes['type_id']['form-control'], 'data-toggle' => 'select')); ?>
										<?php if(isset($errors['type_id'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['type_id']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['incident_id']['form-group']; ?>">
										<?php echo Form::label('Tipo de incidencia', 'incident_id', array('class' => 'form-control-label', 'for' => 'incident_id')); ?>
										<?php echo Form::select('incident_id', Input::post('incident_id', isset($incident_id) ? $incident_id : 'none'), $incidentticket_opts, array('id' => 'incident_id', 'class' => 'form-control '.$classes['incident_id']['form-control'], 'data-toggle' => 'select')); ?>
										<?php if(isset($errors['incident_id'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['incident_id']; ?>
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
									<div class="form-group <?php echo $classes['priority_id']['form-group']; ?>">
										<?php echo Form::label('Tipo de prioridad', 'priority_id', array('class' => 'form-control-label', 'for' => 'priority_id')); ?>
										<?php echo Form::select('priority_id', Input::post('priority_id', isset($priority_id) ? $priority_id : 'none'), $priorityticket_opts, array('id' => 'priority_id', 'class' => 'form-control '.$classes['priority_id']['form-control'], 'data-toggle' => 'select')); ?>
										<?php if(isset($errors['priority_id'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['priority_id']; ?>
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
