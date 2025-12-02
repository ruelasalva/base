<div class="text-center mb-4">
	<h2>Configurar Base de Datos</h2>
	<p class="text-muted">Configure la conexión a su servidor MySQL</p>
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

<form method="post" action="<?php echo Uri::create('install/configurar'); ?>" id="db-config-form">
	<input type="hidden" name="<?php echo Config::get('security.csrf_token_key'); ?>" value="<?php echo Security::fetch_token(); ?>">

	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="db_host" class="form-label">
					<span class="glyphicon glyphicon-globe"></span>
					Host del Servidor
				</label>
				<input type="text" 
					   class="form-control" 
					   id="db_host" 
					   name="db_host" 
					   value="<?php echo htmlspecialchars(Input::post('db_host', $current_host), ENT_QUOTES, 'UTF-8'); ?>"
					   placeholder="localhost"
					   required>
				<small class="text-muted">Generalmente es "localhost" o una dirección IP</small>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="db_name" class="form-label">
					<span class="glyphicon glyphicon-hdd"></span>
					Nombre de la Base de Datos
				</label>
				<input type="text" 
					   class="form-control" 
					   id="db_name" 
					   name="db_name" 
					   value="<?php echo htmlspecialchars(Input::post('db_name', $current_db), ENT_QUOTES, 'UTF-8'); ?>"
					   placeholder="nombre_base_datos"
					   required>
				<small class="text-muted">Si no existe, se creará automáticamente</small>
			</div>
		</div>
	</div>

	<div class="row mt-3">
		<div class="col-md-6">
			<div class="form-group">
				<label for="db_user" class="form-label">
					<span class="glyphicon glyphicon-user"></span>
					Usuario de MySQL
				</label>
				<input type="text" 
					   class="form-control" 
					   id="db_user" 
					   name="db_user" 
					   value="<?php echo htmlspecialchars(Input::post('db_user', $current_user), ENT_QUOTES, 'UTF-8'); ?>"
					   placeholder="root"
					   required>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label for="db_pass" class="form-label">
					<span class="glyphicon glyphicon-lock"></span>
					Contraseña
				</label>
				<input type="password" 
					   class="form-control" 
					   id="db_pass" 
					   name="db_pass" 
					   placeholder="********">
				<small class="text-muted">Déjelo vacío si no tiene contraseña</small>
			</div>
		</div>
	</div>

	<div class="mt-4">
		<div id="connection-test-result"></div>
	</div>

	<hr class="mt-4">

	<div class="row">
		<div class="col-md-6">
			<a href="<?php echo Uri::create('install'); ?>" class="btn btn-installer-outline">
				<span class="glyphicon glyphicon-arrow-left"></span>
				Volver
			</a>
		</div>
		<div class="col-md-6 text-right">
			<button type="button" id="btn-test-connection" class="btn btn-installer-outline">
				<span class="glyphicon glyphicon-refresh"></span>
				Probar Conexión
			</button>
			<button type="submit" class="btn btn-installer">
				<span class="glyphicon glyphicon-save"></span>
				Guardar Configuración
			</button>
		</div>
	</div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
	var btnTest = document.getElementById('btn-test-connection');
	var resultDiv = document.getElementById('connection-test-result');

	if (btnTest) {
		btnTest.addEventListener('click', function() {
			var host = document.getElementById('db_host').value;
			var name = document.getElementById('db_name').value;
			var user = document.getElementById('db_user').value;
			var pass = document.getElementById('db_pass').value;

			resultDiv.innerHTML = '<div class="alert alert-info"><span class="glyphicon glyphicon-refresh"></span> Probando conexión...</div>';

			var xhr = new XMLHttpRequest();
			xhr.open('POST', '<?php echo Uri::create("install/verificar_db"); ?>', true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

			xhr.onreadystatechange = function() {
				if (xhr.readyState === 4) {
					try {
						var response = JSON.parse(xhr.responseText);
						if (response.success) {
							var alertClass = response.db_exists ? 'alert-success' : 'alert-info';
							var icon = response.db_exists ? 'glyphicon-ok-circle' : 'glyphicon-info-sign';
							resultDiv.innerHTML = '<div class="alert ' + alertClass + '"><span class="glyphicon ' + icon + '"></span> ' + response.message + '</div>';
						} else {
							resultDiv.innerHTML = '<div class="alert alert-danger"><span class="glyphicon glyphicon-exclamation-sign"></span> ' + response.message + '</div>';
						}
					} catch (e) {
						resultDiv.innerHTML = '<div class="alert alert-danger"><span class="glyphicon glyphicon-exclamation-sign"></span> Error al procesar la respuesta</div>';
					}
				}
			};

			xhr.send('db_host=' + encodeURIComponent(host) + 
					 '&db_name=' + encodeURIComponent(name) + 
					 '&db_user=' + encodeURIComponent(user) + 
					 '&db_pass=' + encodeURIComponent(pass));
		});
	}
});
</script>

<style>
	.mt-3 { margin-top: 20px; }
	.mt-4 { margin-top: 30px; }
	.text-right { text-align: right; }
</style>
