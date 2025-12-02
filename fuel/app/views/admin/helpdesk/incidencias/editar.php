<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Incidencias</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/helpdesk/incident', 'Incidencias'); ?>
							</li>
							<li class="breadcrumb-item">
								<?php echo Html::anchor('admin/helpdesk/incident/info/'.$id,Str::truncate($name, 40)); ?>
							</li>
							<li class="breadcrumb-item active" aria-current="page">
								<?php echo Html::anchor('admin/helpdesk/incident/editar/'.$id, 'Editar'); ?>
							</li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<?php echo Html::anchor('admin/helpdesk/incident/info/'.$id, 'Ver', array('class' => 'btn btn-sm btn-neutral')); ?>
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
										<legend class="mb-0 heading">Información de la Incidencia</legend>
									</div>
									<div class="col-6 mb-3">
										<div class="form-group <?php echo $classes['id']['form-group']; ?>">
											<?php echo Form::label('Id', 'id', array('class' => 'form-control-label', 'for' => 'id')); ?>
											<?php echo Form::textarea('id', (isset($id) ? $id : ''), array('id' => 'Id', 'class' => 'form-control '.$classes['id']['form-control'], 'placeholder' => 'ID')); ?>
											<?php if(isset($errors['id'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['id']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
                                    <div class="col-md-6 mb-3">
										<div class="form-group <?php echo $classes['type_id']['form-group']; ?>">
											<?php echo Form::label('Tipo', 'type_id', array('class' => 'form-control-label', 'for' => 'type_id')); ?>
											<?php echo Form::select('type_id', (isset($type_id) ? $type_id : 'none'), $typetickets_opts, array('id' => 'type_id', 'class' => 'form-control '.$classes['type_id']['form-control'], 'data-toggle' => 'select')); ?>
											<?php if(isset($errors['type_id'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['type_id']; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-6 mb-3">
										<div class="form-group <?php echo $classes['name']['form-group']; ?>">
											<?php echo Form::label('Tipo de Incidencia', 'name', array('class' => 'form-control-label', 'for' => 'name')); ?>
											<?php echo Form::textarea('name', (isset($name) ? $name : ''), array('id' => 'name', 'class' => 'form-control '.$classes['name']['form-control'], 'placeholder' => 'Tipo de incidencia')); ?>
											<?php if(isset($errors['name'])) : ?>
												<div class="invalid-feedback">
													<?php echo $errors['name']; ?>
												</div>
											<?php endif; ?>
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
