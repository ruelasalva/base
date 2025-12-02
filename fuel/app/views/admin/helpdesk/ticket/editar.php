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
								<?php echo Html::anchor('admin/helpdesk/ticket', 'Tickets'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/helpdesk/ticket/info/'.$id, 'No. '.$id); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/helpdesk/ticket/editar/'.$id, 'Editar'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/helpdesk/ticket/info/'.$id, 'Ver', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/helpdesk/ticket/agregar', 'Nuevo', array('class' => 'btn btn-sm btn-neutral')); ?>
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
									<legend class="mb-0 heading">Información del Ticket elaborado por: <?php echo $username; ?> <br>Para : <?php echo $empleado; ?></legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['employee_id']['form-group']; ?>">
										<?php echo Form::label('Usuario', 'employee_id', array('class' => 'form-control-label', 'for' => 'employe_id')); ?>
										<?php echo Form::select('employee_id', Input::post('employee_id', isset($employee_id) ? $employee_id : 'none'), $employee_opts, array('id' => 'employee_id', 'class' => 'form-control '.$classes['employee_id']['form-control'], 'data-toggle' => 'select')); ?>
										<?php if(isset($errors['employee_id'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['employee_id']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['type_id']['form-group']; ?>">
										<?php echo Form::label('Tipo de ticket', 'type_id', array('class' => 'form-control-label', 'for' => 'type_id')); ?>
										<?php echo Form::select('type_id', (isset($type_id) ? $type_id : 'none'), $typeticket_opts, array('id' => 'type_id', 'class' => 'form-control '.$classes['type_id']['form-control'], 'data-toggle' => 'select')); ?>
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
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['asig_user_id']['form-group']; ?>">
										<?php echo Form::label('Personal de soporte asigando', 'asig_user_id', array('class' => 'form-control-label', 'for' => 'asig_user_id')); ?>
										<?php echo Form::select('asig_user_id', Input::post('asig_user_id', isset($asig_user_id) ? $asig_user_id : 'none'), $asig_user_opts, array('id' => 'asig_user_id', 'class' => 'form-control '.$classes['asig_user_id']['form-control'], 'data-toggle' => 'select')); ?>
										<?php if(isset($errors['asig_user_id'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['asig_user_id']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['status_id']['form-group']; ?>">
										<?php echo Form::label('Estatus del Ticket', 'status_id', array('class' => 'form-control-label', 'for' => 'status_id')); ?>
										<?php echo Form::select('status_id', Input::post('status_id', isset($status_id) ? $status_id : 'none'), $statusticket_opts, array('id' => 'status_id', 'class' => 'form-control '.$classes['status_id']['form-control'], 'data-toggle' => 'select')); ?>
										<?php if(isset($errors['status_id'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['status_id']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-6 mb-3">
									<div class="form-group <?php echo $classes['solution']['form-group']; ?>">
										<?php echo Form::label('Solución', 'solution', array('class' => 'form-control-label', 'for' => 'solution')); ?>
										<?php echo Form::textarea('solution', (isset($solution) ? $solution : ''), array('id' => 'solution', 'class' => 'form-control '.$classes['solution']['form-control'], 'placeholder' => 'Descripción', 'rows' => 7)); ?>
										<?php if(isset($errors['solution'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['solution']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['created_at']['form-group']; ?>">
										<?php echo Form::label('Fecha del ticket', 'created_at', array('class' => 'form-control-label', 'for' => 'created_at')); ?>
										<?php echo Form::input('created_at', (isset($created_at) ? $created_at : ''), array('id' => 'created_at', 'class' => 'form-control datepicker '.$classes['created_at']['form-control'], 'placeholder' => 'Fecha de publicación')); ?>
										<?php if(isset($errors['created_at'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['created_at']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</fieldset>
						<?php echo Form::submit(array('value'=> 'Guardar', 'id'=>'submit', 'class' => 'btn btn-primary')); ?>
						<?php echo Form::close(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php if(!empty($logs)): ?>
		<!-- TABLE -->
		<div class="row">
			<div class="col">
				<div class="card">
					<!-- CARD HEADER -->
					<div class="card-header border-0">
						<div class="form-row">
							<div class="col-md-9">
								<h3 class="mb-0">Registros</h3>
							</div>
						</div>
					</div>
					<!-- LIGHT TABLE -->
					<div class="table-responsive">
						<table class="table align-items-center">
							<thead class="thead-light">
								<tr>
									<th scope="col">Descripción</th>
									<th scope="col">Hora</th>
								</tr>
							</thead>
							<tbody class="list">
								<?php if(!empty($logs)): ?>
									<?php foreach($logs as $log): ?>
										<tr>
											<th>
												<span class="<?php echo $log['color']; ?>"><?php echo $log['message']; ?></span>
											</th>
											<td>
												<?php echo $log['date']; ?>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
