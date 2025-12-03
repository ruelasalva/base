<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">
					<i class="fas fa-bell"></i> Configuración de Notificaciones
				</h3>
			</div>

			<?php echo Form::open(['class' => 'form-horizontal']); ?>
			
			<div class="card-body">
				
				<div class="alert alert-info">
					<i class="fas fa-info-circle"></i>
					<strong>Sistema de Notificaciones:</strong> Configure cómo y cuándo el sistema enviará notificaciones a los usuarios.
				</div>

				<h5 class="mb-3"><i class="fas fa-toggle-on"></i> Estado del Sistema</h5>

				<!-- Notificaciones Habilitadas -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Sistema de Notificaciones:</label>
					<div class="col-sm-9">
						<div class="form-check form-switch">
							<?php 
							$enabled = isset($settings['notifications_enabled']) && $settings['notifications_enabled'] == 1;
							echo Form::checkbox('notifications_enabled', 1, $enabled, [
								'class' => 'form-check-input',
								'id' => 'notifications_enabled'
							]); 
							?>
							<label class="form-check-label" for="notifications_enabled">
								<strong>Habilitar notificaciones del sistema</strong>
							</label>
							<br>
							<small class="text-muted">Desactivar detendrá TODAS las notificaciones</small>
						</div>
					</div>
				</div>

				<hr class="my-4">

				<h5 class="mb-3"><i class="fas fa-envelope-open-text"></i> Canales de Notificación</h5>

				<!-- Email -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Notificaciones por Email:</label>
					<div class="col-sm-9">
						<div class="form-check form-switch">
							<?php 
							$email = isset($settings['notifications_email']) && $settings['notifications_email'] == 1;
							echo Form::checkbox('notifications_email', 1, $email, [
								'class' => 'form-check-input',
								'id' => 'notifications_email'
							]); 
							?>
							<label class="form-check-label" for="notifications_email">
								Enviar notificaciones por correo electrónico
							</label>
						</div>
					</div>
				</div>

				<!-- SMS -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Notificaciones por SMS:</label>
					<div class="col-sm-9">
						<div class="form-check form-switch">
							<?php 
							$sms = isset($settings['notifications_sms']) && $settings['notifications_sms'] == 1;
							echo Form::checkbox('notifications_sms', 1, $sms, [
								'class' => 'form-check-input',
								'id' => 'notifications_sms'
							]); 
							?>
							<label class="form-check-label" for="notifications_sms">
								Enviar notificaciones por SMS
							</label>
							<br>
							<small class="text-muted">Requiere integración con proveedor SMS (Twilio, Nexmo, etc.)</small>
						</div>
					</div>
				</div>

				<!-- Push -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Notificaciones Push:</label>
					<div class="col-sm-9">
						<div class="form-check form-switch">
							<?php 
							$push = isset($settings['notifications_push']) && $settings['notifications_push'] == 1;
							echo Form::checkbox('notifications_push', 1, $push, [
								'class' => 'form-check-input',
								'id' => 'notifications_push'
							]); 
							?>
							<label class="form-check-label" for="notifications_push">
								Enviar notificaciones push (navegador)
							</label>
							<br>
							<small class="text-muted">Notificaciones en tiempo real en el navegador</small>
						</div>
					</div>
				</div>

				<hr class="my-4">

				<h5 class="mb-3"><i class="fas fa-clock"></i> Frecuencia y Horarios</h5>

				<!-- Frecuencia -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Frecuencia de Envío:</label>
					<div class="col-sm-9">
						<?php 
						$frequencies = [
							'instant' => 'Instantáneo (inmediato)',
							'hourly' => 'Cada hora',
							'daily' => 'Diario (resumen)',
							'weekly' => 'Semanal (resumen)'
						];
						echo Form::select('notifications_frequency', isset($settings['notifications_frequency']) ? $settings['notifications_frequency'] : 'instant', $frequencies, [
							'class' => 'form-select'
						]); 
						?>
						<small class="text-muted">Frecuencia de envío de notificaciones no críticas</small>
					</div>
				</div>

				<!-- Horario Silencioso - Inicio -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Horario Silencioso Desde:</label>
					<div class="col-sm-9">
						<?php echo Form::input('notifications_quiet_hours_start', isset($settings['notifications_quiet_hours_start']) ? $settings['notifications_quiet_hours_start'] : '22:00', [
							'class' => 'form-control',
							'type' => 'time'
						]); ?>
						<small class="text-muted">Hora de inicio del periodo sin notificaciones</small>
					</div>
				</div>

				<!-- Horario Silencioso - Fin -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Horario Silencioso Hasta:</label>
					<div class="col-sm-9">
						<?php echo Form::input('notifications_quiet_hours_end', isset($settings['notifications_quiet_hours_end']) ? $settings['notifications_quiet_hours_end'] : '08:00', [
							'class' => 'form-control',
							'type' => 'time'
						]); ?>
						<small class="text-muted">Hora de fin del periodo sin notificaciones</small>
					</div>
				</div>

				<hr class="my-4">

				<div class="alert alert-warning">
					<i class="fas fa-exclamation-triangle"></i>
					<strong>Nota:</strong> Las notificaciones críticas (alertas de seguridad, errores del sistema) siempre se envían independientemente de estas configuraciones.
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
