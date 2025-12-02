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
								<?php echo Html::anchor('admin/crm/task/info/'.$id, $id); ?>
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
						<h3 class="mb-0">Editar</h3>
					</div>
					<!-- CARD BODY -->
					<div class="card-body">
						<?php echo Form::open(array('method' => 'post')); ?>
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
									<div class="form-group <?php echo $classes['date']['form-group']; ?>">
										<?php echo Form::label('Fecha de finalización', 'date', array('class' => 'form-control-label', 'for' => 'date')); ?>
										<?php echo Form::input('date', (isset($date) ? $date : ''), array('id' => 'date', 'class' => 'form-control datepicker '.$classes['date']['form-control'], 'placeholder' => 'Fecha de publicación')); ?>
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
									<div class="form-group">
										<?php echo Form::label('Estatus', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
										<span class="form-control"><?php echo $status_id; ?></span>
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
