<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Detalles del Ticket</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/crm/ticket', 'Tikects'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/crm/ticket/info/'.$id, $description); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php if($status == 6): ?>
						<?php echo Html::anchor('admin/crm/ticket/finalizar/'.$id, 'Cerrar', array('class' => 'btn btn-sm btn-neutral')); ?>
						<?php echo Html::anchor('admin/crm/ticket/iniciar/'.$id, 'Inconforme', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php endif; ?>
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
									<legend class="mb-0 heading">Informacion del Ticket solicitado por <?php echo $user_id; ?></legend>
								</div>
                                <div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Tipo de Ticket', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control"><?php echo $type_id; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Incidencia', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control"><?php echo $incident_id; ?></span>
									</div>
								</div>
                                <div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Descirpcion del problema', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control from-table form-table-area"><?php echo $description; ?></span>
									</div>
								</div>
                                <div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Estatus del ticket', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control"><?php echo $status_id; ?></span>
									</div>
								</div>
                                <div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Usuario asignado:', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control"><?php echo $asig_user_id; ?></span>
									</div>
								</div>
                                <div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Prioridad', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control"><?php echo $priority_id; ?></span>
									</div>
								</div>
                                 <div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Solucion', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control from-table form-table-area"><?php echo $solution; ?></span>
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