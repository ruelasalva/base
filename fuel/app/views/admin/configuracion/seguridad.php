<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">
					<i class="fas fa-shield-alt"></i> Configuración de Seguridad
				</h3>
			</div>

			<?php echo Form::open(['class' => 'form-horizontal']); ?>
			
			<div class="card-body">
				
				<div class="alert alert-danger">
					<i class="fas fa-exclamation-triangle"></i>
					<strong>¡Importante!</strong> Cambios en esta configuración afectan la seguridad del sistema. Proceda con precaución.
				</div>

				<h5 class="mb-3"><i class="fas fa-user-lock"></i> Sesiones y Autenticación</h5>

				<!-- Tiempo de Sesión -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">
						<span class="text-danger">*</span> Tiempo de Sesión:
					</label>
					<div class="col-sm-9">
						<div class="input-group">
							<?php echo Form::input('session_timeout', isset($settings['session_timeout']) ? $settings['session_timeout'] : '3600', [
								'class' => 'form-control',
								'type' => 'number',
								'min' => 300,
								'max' => 86400,
								'required' => 'required'
							]); ?>
							<span class="input-group-text">segundos</span>
						</div>
						<small class="text-muted">Tiempo de inactividad antes de cerrar sesión (300s = 5min, 3600s = 1hr)</small>
					</div>
				</div>

				<!-- Intentos Máximos de Login -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">
						<span class="text-danger">*</span> Máx. Intentos de Login:
					</label>
					<div class="col-sm-9">
						<?php echo Form::input('max_login_attempts', isset($settings['max_login_attempts']) ? $settings['max_login_attempts'] : '3', [
							'class' => 'form-control',
							'type' => 'number',
							'min' => 1,
							'max' => 10,
							'required' => 'required'
						]); ?>
						<small class="text-muted">Intentos fallidos antes de bloquear la cuenta temporalmente</small>
					</div>
				</div>

				<!-- Duración del Bloqueo -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">
						<span class="text-danger">*</span> Duración del Bloqueo:
					</label>
					<div class="col-sm-9">
						<div class="input-group">
							<?php echo Form::input('lockout_duration', isset($settings['lockout_duration']) ? $settings['lockout_duration'] : '15', [
								'class' => 'form-control',
								'type' => 'number',
								'min' => 5,
								'max' => 1440,
								'required' => 'required'
							]); ?>
							<span class="input-group-text">minutos</span>
						</div>
						<small class="text-muted">Tiempo que la cuenta permanece bloqueada después de intentos fallidos</small>
					</div>
				</div>

				<hr class="my-4">

				<h5 class="mb-3"><i class="fas fa-key"></i> Política de Contraseñas</h5>

				<!-- Longitud Mínima -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">
						<span class="text-danger">*</span> Longitud Mínima:
					</label>
					<div class="col-sm-9">
						<div class="input-group">
							<?php echo Form::input('password_min_length', isset($settings['password_min_length']) ? $settings['password_min_length'] : '8', [
								'class' => 'form-control',
								'type' => 'number',
								'min' => 6,
								'max' => 32,
								'required' => 'required'
							]); ?>
							<span class="input-group-text">caracteres</span>
						</div>
						<small class="text-muted">Número mínimo de caracteres para contraseñas</small>
					</div>
				</div>

				<!-- Requerir Mayúsculas -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Requisitos:</label>
					<div class="col-sm-9">
						<div class="form-check">
							<?php 
							$uppercase = isset($settings['password_require_uppercase']) && $settings['password_require_uppercase'] == 1;
							echo Form::checkbox('password_require_uppercase', 1, $uppercase, [
								'class' => 'form-check-input',
								'id' => 'password_require_uppercase'
							]); 
							?>
							<label class="form-check-label" for="password_require_uppercase">
								Requerir al menos una letra mayúscula
							</label>
						</div>
					</div>
				</div>

				<!-- Requerir Números -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label"></label>
					<div class="col-sm-9">
						<div class="form-check">
							<?php 
							$numbers = isset($settings['password_require_numbers']) && $settings['password_require_numbers'] == 1;
							echo Form::checkbox('password_require_numbers', 1, $numbers, [
								'class' => 'form-check-input',
								'id' => 'password_require_numbers'
							]); 
							?>
							<label class="form-check-label" for="password_require_numbers">
								Requerir al menos un número
							</label>
						</div>
					</div>
				</div>

				<!-- Requerir Caracteres Especiales -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label"></label>
					<div class="col-sm-9">
						<div class="form-check">
							<?php 
							$special = isset($settings['password_require_special']) && $settings['password_require_special'] == 1;
							echo Form::checkbox('password_require_special', 1, $special, [
								'class' => 'form-check-input',
								'id' => 'password_require_special'
							]); 
							?>
							<label class="form-check-label" for="password_require_special">
								Requerir al menos un carácter especial (!@#$%^&*)
							</label>
						</div>
					</div>
				</div>

				<hr class="my-4">

				<h5 class="mb-3"><i class="fas fa-robot"></i> Protección contra Bots</h5>

				<!-- Captcha Habilitado -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Google reCAPTCHA:</label>
					<div class="col-sm-9">
						<div class="form-check form-switch">
							<?php 
							$captcha = isset($settings['captcha_enabled']) && $settings['captcha_enabled'] == 1;
							echo Form::checkbox('captcha_enabled', 1, $captcha, [
								'class' => 'form-check-input',
								'id' => 'captcha_enabled'
							]); 
							?>
							<label class="form-check-label" for="captcha_enabled">
								<strong>Habilitar reCAPTCHA en formularios</strong>
							</label>
							<br>
							<small class="text-muted">Protege contra registro y login automatizado</small>
						</div>
					</div>
				</div>

				<!-- Site Key -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">reCAPTCHA Site Key:</label>
					<div class="col-sm-9">
						<?php echo Form::input('captcha_site_key', isset($settings['captcha_site_key']) ? $settings['captcha_site_key'] : '', [
							'class' => 'form-control',
							'placeholder' => '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI'
						]); ?>
						<small class="text-muted">Clave pública de Google reCAPTCHA v2/v3</small>
					</div>
				</div>

				<!-- Secret Key -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">reCAPTCHA Secret Key:</label>
					<div class="col-sm-9">
						<?php echo Form::password('captcha_secret_key', isset($settings['captcha_secret_key']) ? $settings['captcha_secret_key'] : '', [
							'class' => 'form-control',
							'placeholder' => '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'
						]); ?>
						<small class="text-muted">Clave secreta de Google reCAPTCHA</small>
					</div>
				</div>

				<hr class="my-4">

				<h5 class="mb-3"><i class="fas fa-mobile-alt"></i> Autenticación de Dos Factores (2FA)</h5>

				<!-- 2FA Habilitado -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Two-Factor Auth:</label>
					<div class="col-sm-9">
						<div class="form-check form-switch">
							<?php 
							$two_factor = isset($settings['two_factor_enabled']) && $settings['two_factor_enabled'] == 1;
							echo Form::checkbox('two_factor_enabled', 1, $two_factor, [
								'class' => 'form-check-input',
								'id' => 'two_factor_enabled'
							]); 
							?>
							<label class="form-check-label" for="two_factor_enabled">
								<strong>Habilitar autenticación de dos factores</strong>
							</label>
							<br>
							<small class="text-muted">Requiere segundo factor (SMS, email, app) para login</small>
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
	border-bottom: 2px solid #dc3545;
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

.alert-danger {
	border-left: 4px solid #dc3545;
}
</style>
