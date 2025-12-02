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
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/helpdesk/ticket/info/'.$id, 'No. '.$id); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/helpdesk/ticket/editar/'.$id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
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
						<h3 class="mb-0">Ver información</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<fieldset>
							<div class="form-row">
								<div class="col-md-12 mt-0 mb-3">
									<legend class="mb-0 heading">Información del Ticket</legend>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Usuario', 'user_id', array('class' => 'form-control-label', 'for' => 'asig_user_id')); ?>
										<span class="form-control"><?php echo $user_id; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Tipo de ticket', 'type_id', array('class' => 'form-control-label', 'for' => 'type_id')); ?>
										<span class="form-control"><?php echo $type_id; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Tipo de incidencia', 'incident_id', array('class' => 'form-control-label', 'for' => 'incident_id')); ?>
										<span class="form-control"><?php echo $incident_id; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Descripción', 'description', array('class' => 'form-control-label', 'for' => 'description')); ?>
										<?php echo Form::textarea('description', $description, array('class' => 'form-control description-field', 'rows' => 7, 'readonly' => 'readonly')); ?>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Tipo de prioridad', 'priority_id', array('class' => 'form-control-label', 'for' => 'priority_id')); ?>
										<span class="form-control"><?php echo $priority_id; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Personal de soporte asignado', 'asig_user_id', array('class' => 'form-control-label', 'for' => 'asig_user_id')); ?>
										<span class="form-control"><?php echo $asig_user_id; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Estatus del ticket', 'status_id', array('class' => 'form-control-label', 'for' => 'status_id')); ?>
										<span class="form-control"><?php echo $status_id; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Solución', 'solution', array('class' => 'form-control-label', 'for' => 'solution')); ?>
										<?php echo Form::textarea('solution', $solution, array('class' => 'form-control description-field', 'rows' => 7, 'readonly' => 'readonly')); ?>
									</div>
								</div>
							</div>
						</fieldset>
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
