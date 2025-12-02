<div class="text-center mb-4">
	<h2>Ejecutar Migraciones</h2>
	<p class="text-muted">Seleccione las migraciones a ejecutar para crear/actualizar las tablas</p>
</div>

<?php if ($error): ?>
	<div class="alert alert-danger">
		<span class="glyphicon glyphicon-exclamation-sign"></span>
		<?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
	</div>
<?php endif; ?>

<?php if ( ! empty($results)): ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<span class="glyphicon glyphicon-list-alt"></span>
				Resultados de la Ejecución
			</h4>
		</div>
		<div class="panel-body">
			<?php foreach ($results as $result): ?>
				<div class="alert <?php echo $result['success'] ? 'alert-success' : 'alert-danger'; ?>">
					<?php if ($result['success']): ?>
						<span class="glyphicon glyphicon-ok-circle"></span>
					<?php else: ?>
						<span class="glyphicon glyphicon-exclamation-sign"></span>
					<?php endif; ?>
					<strong><?php echo htmlspecialchars($result['migration'], ENT_QUOTES, 'UTF-8'); ?></strong>
					- <?php echo htmlspecialchars($result['message'], ENT_QUOTES, 'UTF-8'); ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif; ?>

<?php if (empty($pending_migrations)): ?>
	<div class="alert alert-success">
		<span class="glyphicon glyphicon-ok-circle"></span>
		<strong>¡Todo actualizado!</strong> No hay migraciones pendientes.
	</div>

	<div class="text-center mt-4">
		<a href="<?php echo Uri::create('install/crear_admin'); ?>" class="btn btn-installer btn-lg">
			<span class="glyphicon glyphicon-arrow-right"></span>
			Continuar - Crear Administrador
		</a>
	</div>
<?php else: ?>
	<form method="post" action="<?php echo Uri::create('install/ejecutar'); ?>">
		<input type="hidden" name="<?php echo Config::get('security.csrf_token_key'); ?>" value="<?php echo Security::fetch_token(); ?>">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<span class="glyphicon glyphicon-list"></span>
					Migraciones Pendientes
					<span class="badge"><?php echo count($pending_migrations); ?></span>
				</h4>
			</div>
			<div class="panel-body">
				<div class="mb-3">
					<label>
						<input type="checkbox" id="select-all-migrations">
						<strong>Seleccionar todas</strong>
					</label>
				</div>

				<ul class="migration-list">
					<?php foreach ($pending_migrations as $migration): ?>
					<li class="migration-item">
						<div class="migration-checkbox">
							<input type="checkbox" 
								   name="migrations[]" 
								   value="<?php echo htmlspecialchars($migration['name'], ENT_QUOTES, 'UTF-8'); ?>"
								   class="migration-checkbox-item"
								   id="mig_<?php echo htmlspecialchars($migration['version'], ENT_QUOTES, 'UTF-8'); ?>">
						</div>
						<div class="migration-info">
							<label for="mig_<?php echo htmlspecialchars($migration['version'], ENT_QUOTES, 'UTF-8'); ?>" class="migration-name">
								<?php echo htmlspecialchars($migration['name'], ENT_QUOTES, 'UTF-8'); ?>
							</label>
							<div class="migration-description">
								<?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $migration['description'])), ENT_QUOTES, 'UTF-8'); ?>
							</div>
						</div>
						<div class="migration-status">
							<span class="status-badge status-warning">
								<span class="glyphicon glyphicon-time"></span>
								Pendiente
							</span>
						</div>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<hr>

		<div class="row">
			<div class="col-md-6">
				<a href="<?php echo Uri::create('install'); ?>" class="btn btn-installer-outline">
					<span class="glyphicon glyphicon-arrow-left"></span>
					Volver
				</a>
			</div>
			<div class="col-md-6 text-right">
				<button type="submit" class="btn btn-installer">
					<span class="glyphicon glyphicon-play"></span>
					Ejecutar Migraciones Seleccionadas
				</button>
			</div>
		</div>
	</form>
<?php endif; ?>

<div class="row mt-4">
	<div class="col-md-12">
		<a href="<?php echo Uri::create('install'); ?>" class="btn btn-installer-outline">
			<span class="glyphicon glyphicon-arrow-left"></span>
			Volver al Inicio
		</a>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	var selectAll = document.getElementById('select-all-migrations');
	var checkboxes = document.querySelectorAll('.migration-checkbox-item');

	if (selectAll) {
		selectAll.addEventListener('change', function() {
			checkboxes.forEach(function(checkbox) {
				checkbox.checked = selectAll.checked;
			});
		});

		// Actualizar "seleccionar todas" cuando se cambia un checkbox individual
		checkboxes.forEach(function(checkbox) {
			checkbox.addEventListener('change', function() {
				var allChecked = true;
				checkboxes.forEach(function(cb) {
					if (!cb.checked) allChecked = false;
				});
				selectAll.checked = allChecked;
			});
		});
	}
});
</script>

<style>
	.mb-3 { margin-bottom: 15px; }
	.mt-4 { margin-top: 30px; }
	.text-right { text-align: right; }
</style>
