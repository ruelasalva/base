<!-- CONTENT -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Detalle Plantilla</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item"><?php echo Html::anchor('admin','<i class="fas fa-home"></i>'); ?></li>
							<li class="breadcrumb-item"><?php echo Html::anchor('admin/configuracion/correos','Correos'); ?></li>
							<li class="breadcrumb-item"><?php echo Html::anchor('admin/configuracion/correos/templates','Plantillas'); ?></li>
							<li class="breadcrumb-item active" aria-current="page">Detalle</li>
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
			<div class="card">
				<div class="card-header">
					<h3 class="mb-0">Información de la Plantilla</h3>
				</div>
				<div class="card-body">

					<div class="form-group">
						<?php echo Form::label('ID','id'); ?>
						<span class="form-control"><?php echo $id; ?></span>
					</div>

					<div class="form-group">
						<?php echo Form::label('Código','code'); ?>
						<span class="form-control"><?php echo $code; ?></span>
					</div>

					<div class="form-group">
						<?php echo Form::label('Rol asociado','role'); ?>
						<span class="form-control"><?php echo $role; ?></span>
					</div>

					<div class="form-group">
						<?php echo Form::label('Asunto','subject'); ?>
						<span class="form-control"><?php echo $subject; ?></span>
					</div>

					<div class="form-group">
						<?php echo Form::label('Vista FuelPHP','view'); ?>
						<span class="form-control"><?php echo $view; ?></span>
					</div>

					<div class="form-group">
						<?php echo Form::label('Creado','created_at'); ?>
						<span class="form-control"><?php echo $created_at; ?></span>
					</div>

					<div class="form-group">
						<?php echo Form::label('Última actualización','updated_at'); ?>
						<span class="form-control"><?php echo $updated_at; ?></span>
					</div>

					<?php echo Html::anchor('admin/configuracion/correos/templates','Regresar',['class'=>'btn btn-secondary']); ?>
				</div>
			</div>
		</div>
	</div>
</div>
