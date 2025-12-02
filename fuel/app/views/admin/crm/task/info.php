<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Detalles de la Tarea Pendiente</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/crm/task', 'Tareas Pendientes'); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/crm/task/info/'.$id, $description); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/crm/task/editar/'.$id, 'Editar', array('class' => 'btn btn-sm btn-neutral')); ?>
					<?php echo Html::anchor('admin/crm/task/agregar', 'Nuevo', array('class' => 'btn btn-sm btn-neutral')); ?>
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
                                <div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Descirpcion del problema', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control from-table form-table-area"><?php echo $description; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Creado', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control"><?php echo $created_at; ?></span>
									</div>
								</div>
                                <div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Fecha compromiso', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control"><?php echo $commitment_at; ?></span>
									</div>
								</div>
                                <div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Fecha finalizacion:', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control"><?php echo $finish_at; ?></span>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Comentarios del problema', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control from-table form-table-area"><?php echo $comments; ?></span>
									</div>
								</div>
                                <div class="col-md-6 mb-3">
									<div class="form-group">
										<?php echo Form::label('Estatus', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control"><?php echo $status_id; ?></span>
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