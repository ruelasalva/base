<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">
					<i class="fas fa-envelope"></i> Configuración de Email
				</h3>
			</div>

			<?php echo Form::open(['class' => 'form-horizontal']); ?>
			
			<div class="card-body">
				
				<!-- Email Habilitado -->
				<div class="alert alert-info">
					<i class="fas fa-info-circle"></i>
					<strong>Importante:</strong> Configure correctamente el servidor SMTP para el envío de correos electrónicos del sistema.
				</div>

				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Sistema de Email:</label>
					<div class="col-sm-9">
						<div class="form-check form-switch">
							<?php 
							$email_enabled = isset($settings['email_enabled']) && $settings['email_enabled'] == 1;
							echo Form::checkbox('email_enabled', 1, $email_enabled, [
								'class' => 'form-check-input',
								'id' => 'email_enabled'
							]); 
							?>
							<label class="form-check-label" for="email_enabled">
								<strong>Habilitar envío de emails</strong>
							</label>
							<br>
							<small class="text-muted">Cuando está desactivado, no se enviarán correos electrónicos</small>
						</div>
					</div>
				</div>

				<hr class="my-4">

				<h5 class="mb-3"><i class="fas fa-user"></i> Datos del Remitente</h5>

				<!-- Email del Remitente -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">
						<span class="text-danger">*</span> Email del Remitente:
					</label>
					<div class="col-sm-9">
						<?php echo Form::input('email_from_address', isset($settings['email_from_address']) ? $settings['email_from_address'] : '', [
							'class' => 'form-control',
							'type' => 'email',
							'required' => 'required',
							'placeholder' => 'noreply@ejemplo.com'
						]); ?>
						<small class="text-muted">Dirección de correo que aparecerá como remitente</small>
					</div>
				</div>

				<!-- Nombre del Remitente -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">
						<span class="text-danger">*</span> Nombre del Remitente:
					</label>
					<div class="col-sm-9">
						<?php echo Form::input('email_from_name', isset($settings['email_from_name']) ? $settings['email_from_name'] : '', [
							'class' => 'form-control',
							'required' => 'required',
							'placeholder' => 'Mi Sistema'
						]); ?>
						<small class="text-muted">Nombre que aparecerá como remitente</small>
					</div>
				</div>

				<hr class="my-4">

				<h5 class="mb-3"><i class="fas fa-server"></i> Configuración SMTP</h5>

				<!-- Servidor SMTP -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">
						<span class="text-danger">*</span> Servidor SMTP:
					</label>
					<div class="col-sm-9">
						<?php echo Form::input('smtp_host', isset($settings['smtp_host']) ? $settings['smtp_host'] : '', [
							'class' => 'form-control',
							'required' => 'required',
							'placeholder' => 'smtp.gmail.com'
						]); ?>
						<small class="text-muted">Dirección del servidor SMTP</small>
					</div>
				</div>

				<!-- Puerto SMTP -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">
						<span class="text-danger">*</span> Puerto SMTP:
					</label>
					<div class="col-sm-9">
						<?php echo Form::input('smtp_port', isset($settings['smtp_port']) ? $settings['smtp_port'] : '587', [
							'class' => 'form-control',
							'type' => 'number',
							'required' => 'required',
							'placeholder' => '587'
						]); ?>
						<small class="text-muted">Puerto común: 25, 465 (SSL), 587 (TLS)</small>
					</div>
				</div>

				<!-- Usuario SMTP -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Usuario SMTP:</label>
					<div class="col-sm-9">
						<?php echo Form::input('smtp_username', isset($settings['smtp_username']) ? $settings['smtp_username'] : '', [
							'class' => 'form-control',
							'placeholder' => 'usuario@ejemplo.com'
						]); ?>
						<small class="text-muted">Usuario para autenticación SMTP</small>
					</div>
				</div>

				<!-- Contraseña SMTP -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Contraseña SMTP:</label>
					<div class="col-sm-9">
						<?php echo Form::password('smtp_password', isset($settings['smtp_password']) ? $settings['smtp_password'] : '', [
							'class' => 'form-control',
							'placeholder' => '••••••••'
						]); ?>
						<small class="text-muted">Contraseña para autenticación SMTP</small>
					</div>
				</div>

				<!-- Encriptación -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Tipo de Encriptación:</label>
					<div class="col-sm-9">
						<?php 
						$encryptions = [
							'' => 'Sin encriptación',
							'tls' => 'TLS',
							'ssl' => 'SSL'
						];
						echo Form::select('smtp_encryption', isset($settings['smtp_encryption']) ? $settings['smtp_encryption'] : 'tls', $encryptions, [
							'class' => 'form-select'
						]); 
						?>
						<small class="text-muted">TLS es recomendado para la mayoría de servidores</small>
					</div>
				</div>

				<!-- Probar Conexión -->
				<div class="row mb-3">
					<div class="col-sm-9 offset-sm-3">
						<div class="form-check">
							<?php echo Form::checkbox('test_connection', 1, false, [
								'class' => 'form-check-input',
								'id' => 'test_connection'
							]); ?>
							<label class="form-check-label" for="test_connection">
								Probar conexión SMTP al guardar
							</label>
						</div>
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
