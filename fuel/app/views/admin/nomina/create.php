<?php
/**
 * Vista: Crear Período de Nómina
 */
?>
<div class="container-fluid">
	<div class="mb-4">
		<h2><i class="fa fa-plus"></i> Crear Período de Nómina</h2>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/dashboard'); ?>">Dashboard</a></li>
				<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/nomina'); ?>">Nómina</a></li>
				<li class="breadcrumb-item active">Crear Período</li>
			</ol>
		</nav>
	</div>

	<div class="row">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header bg-primary text-white">
					<h5 class="mb-0"><i class="fa fa-info-circle"></i> Información del Período</h5>
				</div>
				<div class="card-body">
					<?php echo Form::open(array('class' => 'form-horizontal')); ?>

						<div class="mb-3">
							<label class="form-label required">Código del Período</label>
							<?php echo Form::input('code', Input::post('code', date('Y-m')), array('class' => 'form-control', 'required' => true, 'placeholder' => '2025-01')); ?>
							<small class="text-muted">Ejemplo: 2025-01 para enero 2025</small>
						</div>

						<div class="mb-3">
							<label class="form-label required">Nombre</label>
							<?php echo Form::input('name', Input::post('name', 'Nómina ' . date('F Y')), array('class' => 'form-control', 'required' => true)); ?>
						</div>

						<div class="row">
							<div class="col-md-4 mb-3">
								<label class="form-label required">Tipo de Período</label>
								<?php echo Form::select('period_type', Input::post('period_type', 'monthly'), array(
									'monthly' => 'Mensual',
									'biweekly' => 'Quincenal',
									'weekly' => 'Semanal',
								), array('class' => 'form-control', 'required' => true)); ?>
							</div>

							<div class="col-md-4 mb-3">
								<label class="form-label required">Año</label>
								<?php echo Form::input('year', Input::post('year', date('Y')), array('class' => 'form-control', 'type' => 'number', 'required' => true, 'min' => 2020)); ?>
							</div>

							<div class="col-md-4 mb-3">
								<label class="form-label required">Número de Período</label>
								<?php echo Form::input('period_number', Input::post('period_number', date('n')), array('class' => 'form-control', 'type' => 'number', 'required' => true, 'min' => 1, 'max' => 24)); ?>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4 mb-3">
								<label class="form-label required">Fecha de Inicio</label>
								<?php echo Form::input('start_date', Input::post('start_date', date('Y-m-01')), array('class' => 'form-control', 'type' => 'date', 'required' => true)); ?>
							</div>

							<div class="col-md-4 mb-3">
								<label class="form-label required">Fecha de Fin</label>
								<?php echo Form::input('end_date', Input::post('end_date', date('Y-m-t')), array('class' => 'form-control', 'type' => 'date', 'required' => true)); ?>
							</div>

							<div class="col-md-4 mb-3">
								<label class="form-label required">Fecha de Pago</label>
								<?php echo Form::input('payment_date', Input::post('payment_date', date('Y-m-t', strtotime('+5 days'))), array('class' => 'form-control', 'type' => 'date', 'required' => true)); ?>
							</div>
						</div>

						<div class="mb-3">
							<label class="form-label">Notas</label>
							<?php echo Form::textarea('notes', Input::post('notes'), array('class' => 'form-control', 'rows' => 3)); ?>
						</div>

						<div class="d-flex justify-content-end gap-2">
							<a href="<?php echo Uri::create('admin/nomina'); ?>" class="btn btn-secondary">
								<i class="fa fa-times"></i> Cancelar
							</a>
							<?php echo Form::submit('submit', 'Crear Período', array('class' => 'btn btn-primary')); ?>
						</div>

					<?php echo Form::close(); ?>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="card">
				<div class="card-header">
					<h5 class="mb-0"><i class="fa fa-lightbulb"></i> Ayuda</h5>
				</div>
				<div class="card-body">
					<h6>Tipos de Período</h6>
					<ul class="small">
						<li><strong>Mensual:</strong> Un período por mes (1-12)</li>
						<li><strong>Quincenal:</strong> Dos períodos por mes (1-24)</li>
						<li><strong>Semanal:</strong> Cuatro períodos por mes (1-52)</li>
					</ul>

					<h6 class="mt-3">Flujo de Proceso</h6>
					<ol class="small">
						<li>Crear período en estado "Borrador"</li>
						<li>Calcular nómina de empleados activos</li>
						<li>Revisar y aprobar</li>
						<li>Generar archivo de dispersión bancaria</li>
						<li>Marcar como "Pagado"</li>
						<li>Cerrar período</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.required:after {
		content: " *";
		color: red;
	}
</style>
