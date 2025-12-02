<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Edición de la Tarea Pendiente</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/helpdesk/task', 'Tareas Pendientes'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/helpdesk/task/info/'.$id, $description); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/helpdesk/task/editar/'.$id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/helpdesk/task/agregar', 'Nuevo', array('class' => 'btn btn-sm btn-neutral')); ?>
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
						<h3 class="mb-0">Editar tarea No. <?php echo $id; ?></h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<?php echo Form::open(array('method' => 'post')); ?>
						<fieldset>
							<div class="form-row">
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['employee_id']['form-group']; ?>">
										<?php echo Form::label('Empleado', 'employee_id', array('class' => 'form-control-label', 'for' => 'employe_id')); ?>
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
										<?php echo Form::label('Descripción del problema', 'description', array('class' => 'form-control-label', 'for' => 'description')); ?>
										<?php echo Form::textarea('description', (isset($description) ? $description : ''), array('id' => 'Descripción', 'class' => 'form-control '.$classes['description']['form-control'], 'placeholder' => 'Descripción', 'rows' => 7)); ?>
										<?php if(isset($errors['description'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['description']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Fecha de Creación', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										 <input type="text" class="form-control" id="created_at" value="<?php echo $created_at; ?>" readonly>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['date']['form-group']; ?>">
										<?php echo Form::label('Fecha de Compromiso', 'commitment_date', array('class' => 'form-control-label', 'for' => 'commitment_date')); ?>
										<?php echo Form::input('commitment_date', (isset($commitment_date) ? $commitment_date : ''), array('id' => 'commitment_date', 'class' => 'form-control datepicker '.$classes['date']['form-control'], 'placeholder' => 'Fecha de compromiso')); ?>
										<?php if(isset($errors['commitment_date'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['commitment_date']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
                                <div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['date']['form-group']; ?>">
										<?php echo Form::label('Fecha de finalización', 'date', array('class' => 'form-control-label', 'for' => 'date')); ?>
										<?php echo Form::input('date', (isset($date) ? $date : ''), array('id' => 'date', 'class' => 'form-control datepicker '.$classes['date']['form-control'], 'placeholder' => 'Fecha de finalización')); ?>
										<?php if(isset($errors['date'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['date']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-6 mb-3">
									<div class="form-group <?php echo $classes['comments']['form-group']; ?>">
										<?php echo Form::label('Comentarios', 'comments', array('class' => 'form-control-label', 'for' => 'comments')); ?>
										<?php echo Form::textarea('comments', (isset($comments) ? $comments : ''), array('id' => 'Comentarios', 'class' => 'form-control '.$classes['comments']['form-control'], 'placeholder' => 'Comentarios', 'rows' => 7)); ?>
										<?php if(isset($errors['comments'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['comments']; ?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group <?php echo $classes['status_id']['form-group']; ?>">
										<?php echo Form::label('Estatus de la tarea', 'status_id', array('class' => 'form-control-label', 'for' => 'status_id')); ?>
										<?php echo Form::select('status_id', Input::post('status_id', isset($status_id) ? $status_id : 'none'), $statustask_opts, array('id' => 'status_id', 'class' => 'form-control '.$classes['status_id']['form-control'], 'data-toggle' => 'select')); ?>
										<?php if(isset($errors['status_id'])) : ?>
											<div class="invalid-feedback">
												<?php echo $errors['status_id']; ?>
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
</div>
