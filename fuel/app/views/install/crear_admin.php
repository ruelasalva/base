<div class="text-center mb-4">
	<h2>Crear Usuario Administrador</h2>
	<p class="text-muted">Configure el usuario administrador inicial del sistema</p>
</div>

<?php if ($error): ?>
	<div class="alert alert-danger">
		<span class="glyphicon glyphicon-exclamation-sign"></span>
		<?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
	</div>
<?php endif; ?>

<?php if ($success): ?>
	<div class="alert alert-success">
		<span class="glyphicon glyphicon-ok-circle"></span>
		<?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
	</div>
<?php endif; ?>

<form method="post" action="<?php echo Uri::create('install/crear_admin'); ?>" id="admin-form">
	<input type="hidden" name="<?php echo Config::get('security.csrf_token_key'); ?>" value="<?php echo Security::fetch_token(); ?>">

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<span class="glyphicon glyphicon-user"></span>
				Información del Administrador
			</h4>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="username" class="form-label">
							<span class="glyphicon glyphicon-user"></span>
							Nombre de Usuario
						</label>
						<input type="text" 
							   class="form-control" 
							   id="username" 
							   name="username" 
							   value="<?php echo htmlspecialchars(Input::post('username', ''), ENT_QUOTES, 'UTF-8'); ?>"
							   placeholder="admin"
							   pattern="[a-zA-Z0-9_]+"
							   title="Solo letras, números y guiones bajos"
							   required
							   autofocus>
						<small class="text-muted">Solo letras, números y guiones bajos</small>
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<label for="email" class="form-label">
							<span class="glyphicon glyphicon-envelope"></span>
							Correo Electrónico
						</label>
						<input type="email" 
							   class="form-control" 
							   id="email" 
							   name="email" 
							   value="<?php echo htmlspecialchars(Input::post('email', ''), ENT_QUOTES, 'UTF-8'); ?>"
							   placeholder="admin@ejemplo.com"
							   required>
					</div>
				</div>
			</div>

			<div class="row mt-3">
				<div class="col-md-6">
					<div class="form-group">
						<label for="password" class="form-label">
							<span class="glyphicon glyphicon-lock"></span>
							Contraseña
						</label>
						<input type="password" 
							   class="form-control" 
							   id="password" 
							   name="password" 
							   placeholder="********"
							   minlength="8"
							   required>
						<small class="text-muted">Mínimo 8 caracteres</small>
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<label for="confirm_password" class="form-label">
							<span class="glyphicon glyphicon-lock"></span>
							Confirmar Contraseña
						</label>
						<input type="password" 
							   class="form-control" 
							   id="confirm_password" 
							   name="confirm_password" 
							   placeholder="********"
							   minlength="8"
							   required>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Validación de contraseña -->
	<div id="password-validation" class="mb-4" style="display: none;">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6">
						<div id="length-check" class="validation-item text-muted">
							<span class="glyphicon glyphicon-remove"></span>
							Mínimo 8 caracteres
						</div>
					</div>
					<div class="col-md-6">
						<div id="match-check" class="validation-item text-muted">
							<span class="glyphicon glyphicon-remove"></span>
							Las contraseñas coinciden
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<hr>

	<div class="row">
		<div class="col-md-6">
			<a href="<?php echo Uri::create('install/auto_install'); ?>" class="btn btn-installer-outline">
				<span class="glyphicon glyphicon-arrow-left"></span>
				Volver
			</a>
		</div>
		<div class="col-md-6 text-right">
			<button type="submit" class="btn btn-installer" id="btn-submit">
				<span class="glyphicon glyphicon-ok"></span>
				Crear Administrador y Finalizar
			</button>
		</div>
	</div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
	var password = document.getElementById('password');
	var confirmPassword = document.getElementById('confirm_password');
	var validationDiv = document.getElementById('password-validation');
	var lengthCheck = document.getElementById('length-check');
	var matchCheck = document.getElementById('match-check');
	var submitBtn = document.getElementById('btn-submit');

	function validatePassword() {
		if (password.value.length > 0 || confirmPassword.value.length > 0) {
			validationDiv.style.display = 'block';
		} else {
			validationDiv.style.display = 'none';
		}

		// Validar longitud
		if (password.value.length >= 8) {
			lengthCheck.className = 'validation-item text-success';
			lengthCheck.innerHTML = '<span class="glyphicon glyphicon-ok"></span> Mínimo 8 caracteres';
		} else {
			lengthCheck.className = 'validation-item text-danger';
			lengthCheck.innerHTML = '<span class="glyphicon glyphicon-remove"></span> Mínimo 8 caracteres';
		}

		// Validar coincidencia
		if (password.value === confirmPassword.value && password.value.length > 0) {
			matchCheck.className = 'validation-item text-success';
			matchCheck.innerHTML = '<span class="glyphicon glyphicon-ok"></span> Las contraseñas coinciden';
		} else {
			matchCheck.className = 'validation-item text-danger';
			matchCheck.innerHTML = '<span class="glyphicon glyphicon-remove"></span> Las contraseñas no coinciden';
		}
	}

	password.addEventListener('input', validatePassword);
	confirmPassword.addEventListener('input', validatePassword);
});
</script>

<style>
	.mt-3 { margin-top: 20px; }
	.mb-4 { margin-bottom: 25px; }
	.text-right { text-align: right; }
	.text-success { color: #28a745; }
	.text-danger { color: #dc3545; }
	.text-muted { color: #6c757d; }
	.validation-item {
		padding: 5px 0;
	}
</style>
