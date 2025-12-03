<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">
					<i class="fas fa-file-invoice-dollar"></i> Configuración de Facturación
				</h3>
			</div>

			<?php echo Form::open(['class' => 'form-horizontal']); ?>
			
			<div class="card-body">
				
				<div class="alert alert-info">
					<i class="fas fa-info-circle"></i>
					<strong>Módulo de Proveedores:</strong> Configuración de parámetros para facturación, carga de XMLs y generación de contrarecibos.
				</div>

				<h5 class="mb-3"><i class="fas fa-calendar-alt"></i> Plazos y Términos</h5>

				<!-- Días Válidos para Subir Facturas -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">
						<span class="text-danger">*</span> Días Válidos para Subir:
					</label>
					<div class="col-sm-9">
						<?php echo Form::input('billing_days_to_upload', isset($settings['billing_days_to_upload']) ? $settings['billing_days_to_upload'] : '5', [
							'class' => 'form-control',
							'type' => 'number',
							'min' => 1,
							'max' => 30,
							'required' => 'required'
						]); ?>
						<small class="text-muted">Días del mes 1-5, 1-10, etc. en los que proveedores pueden subir facturas</small>
					</div>
				</div>

				<!-- Hora Límite de Carga -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">
						<span class="text-danger">*</span> Hora Límite:
					</label>
					<div class="col-sm-9">
						<?php echo Form::input('billing_upload_deadline', isset($settings['billing_upload_deadline']) ? $settings['billing_upload_deadline'] : '18:00', [
							'class' => 'form-control',
							'type' => 'time',
							'required' => 'required'
						]); ?>
						<small class="text-muted">Hora límite para subir facturas en el día hábil</small>
					</div>
				</div>

				<!-- Términos de Pago -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">
						<span class="text-danger">*</span> Términos de Pago:
					</label>
					<div class="col-sm-9">
						<div class="input-group">
							<?php echo Form::input('billing_payment_terms', isset($settings['billing_payment_terms']) ? $settings['billing_payment_terms'] : '30', [
								'class' => 'form-control',
								'type' => 'number',
								'min' => 1,
								'max' => 180,
								'required' => 'required'
							]); ?>
							<span class="input-group-text">días</span>
						</div>
						<small class="text-muted">Días naturales para pago después de recepción de factura</small>
					</div>
				</div>

				<!-- Días de Pago -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Días de Pago:</label>
					<div class="col-sm-9">
						<?php echo Form::input('billing_payment_days', isset($settings['billing_payment_days']) ? $settings['billing_payment_days'] : 'Lunes,Miércoles,Viernes', [
							'class' => 'form-control',
							'placeholder' => 'Lunes,Miércoles,Viernes'
						]); ?>
						<small class="text-muted">Días de la semana para programar pagos (separados por comas)</small>
					</div>
				</div>

				<hr class="my-4">

				<h5 class="mb-3"><i class="fas fa-calendar-times"></i> Días Festivos</h5>

				<!-- Días Festivos -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Días Festivos:</label>
					<div class="col-sm-9">
						<?php echo Form::textarea('billing_holidays', isset($settings['billing_holidays']) ? $settings['billing_holidays'] : '', [
							'class' => 'form-control',
							'rows' => 4,
							'placeholder' => '["2025-01-01", "2025-02-05", "2025-03-21", "2025-05-01", "2025-09-16", "2025-11-20", "2025-12-25"]'
						]); ?>
						<small class="text-muted">Array JSON con fechas en formato YYYY-MM-DD</small>
					</div>
				</div>

				<hr class="my-4">

				<h5 class="mb-3"><i class="fas fa-cog"></i> Validaciones y Automatización</h5>

				<!-- Validación SAT Obligatoria -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Validación SAT:</label>
					<div class="col-sm-9">
						<div class="form-check form-switch">
							<?php 
							$require_sat = isset($settings['billing_require_sat_validation']) && $settings['billing_require_sat_validation'] == 1;
							echo Form::checkbox('billing_require_sat_validation', 1, $require_sat, [
								'class' => 'form-check-input',
								'id' => 'billing_require_sat_validation'
							]); 
							?>
							<label class="form-check-label" for="billing_require_sat_validation">
								<strong>Validar CFDIs con SAT antes de aceptar</strong>
							</label>
							<br>
							<small class="text-muted">Rechaza automáticamente facturas canceladas o no encontradas en SAT</small>
						</div>
					</div>
				</div>

				<!-- Auto-generar Contrarecibos -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Contrarecibos:</label>
					<div class="col-sm-9">
						<div class="form-check form-switch">
							<?php 
							$auto_receipt = isset($settings['billing_auto_receipt']) && $settings['billing_auto_receipt'] == 1;
							echo Form::checkbox('billing_auto_receipt', 1, $auto_receipt, [
								'class' => 'form-check-input',
								'id' => 'billing_auto_receipt'
							]); 
							?>
							<label class="form-check-label" for="billing_auto_receipt">
								<strong>Generar contrarecibos automáticamente</strong>
							</label>
							<br>
							<small class="text-muted">Crea contrarecibos al aprobar facturas, calculando fechas con días hábiles</small>
						</div>
					</div>
				</div>

				<hr class="my-4">

				<h5 class="mb-3"><i class="fas fa-file-upload"></i> Límites de Carga</h5>

				<!-- Tamaño Máximo de Archivo -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Tamaño Máximo de XML:</label>
					<div class="col-sm-9">
						<div class="input-group">
							<?php echo Form::input('billing_max_file_size', isset($settings['billing_max_file_size']) ? $settings['billing_max_file_size'] : '5', [
								'class' => 'form-control',
								'type' => 'number',
								'min' => 1,
								'max' => 50
							]); ?>
							<span class="input-group-text">MB</span>
						</div>
						<small class="text-muted">Tamaño máximo permitido para archivos XML (1-50 MB)</small>
					</div>
				</div>

			</div>

			<div class="card-footer">
				<div class="row">
					<div class="col-sm-9 offset-sm-3">
						<button type="submit" class="btn btn-success">
							<i class="fas fa-save"></i> Guardar Configuración
						</button>
						<a href="<?php echo Uri::create('admin/configuracion'); ?>" class="btn btn-secondary">
							<i class="fas fa-times"></i> Cancelar
						</a>
					</div>
				</div>
			</div>

			<?php echo Form::close(); ?>
		</div>
	</div>
</div>

<style>
.card-header {
	background-color: #f8f9fa;
	border-bottom: 2px solid #007bff;
}

.text-danger {
	font-weight: bold;
}

.form-check-input:checked {
	background-color: #28a745;
	border-color: #28a745;
}

hr.my-4 {
	border-top: 2px solid #e9ecef;
	margin: 2rem 0;
}

h5 {
	color: #495057;
	font-weight: 600;
}
</style>
