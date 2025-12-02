<div class="text-center mb-4">
	<h2>Instalación Automática</h2>
	<p class="text-muted">Ejecute todas las migraciones pendientes con un solo clic</p>
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

<?php if ( ! $db_connected): ?>
	<div class="alert alert-warning">
		<span class="glyphicon glyphicon-warning-sign"></span>
		<strong>Atención:</strong> No hay conexión a la base de datos. Por favor configure la conexión primero.
	</div>
	<div class="text-center">
		<a href="<?php echo Uri::create('install/configurar'); ?>" class="btn btn-installer btn-lg">
			<span class="glyphicon glyphicon-cog"></span>
			Configurar Base de Datos
		</a>
	</div>
<?php elseif (empty($pending_migrations) && empty($results)): ?>
	<div class="alert alert-success">
		<span class="glyphicon glyphicon-ok-circle"></span>
		<strong>¡Sistema actualizado!</strong> Todas las migraciones han sido ejecutadas.
	</div>
	<div class="text-center mt-4">
		<a href="<?php echo Uri::create('install/crear_admin'); ?>" class="btn btn-installer btn-lg">
			<span class="glyphicon glyphicon-user"></span>
			Continuar - Crear Administrador
		</a>
	</div>
<?php elseif ( ! empty($results) && $success): ?>
	<div class="text-center mt-4">
		<a href="<?php echo Uri::create('install/crear_admin'); ?>" class="btn btn-installer btn-lg">
			<span class="glyphicon glyphicon-arrow-right"></span>
			Continuar - Crear Administrador
		</a>
	</div>
<?php else: ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<span class="glyphicon glyphicon-list"></span>
				Migraciones Pendientes
				<span class="badge"><?php echo count($pending_migrations); ?></span>
			</h4>
		</div>
		<div class="panel-body">
			<ul class="migration-list">
				<?php foreach ($pending_migrations as $migration): ?>
				<li class="migration-item">
					<div class="migration-info">
						<span class="migration-name">
							<span class="glyphicon glyphicon-file"></span>
							<?php echo htmlspecialchars($migration['name'], ENT_QUOTES, 'UTF-8'); ?>
						</span>
						<div class="migration-description">
							<?php echo htmlspecialchars(ucwords(str_replace('_', ' ', isset($migration['description']) ? $migration['description'] : '')), ENT_QUOTES, 'UTF-8'); ?>
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

	<form method="post" action="<?php echo Uri::create('install/auto_install'); ?>">
		<input type="hidden" name="<?php echo Config::get('security.csrf_token_key'); ?>" value="<?php echo Security::fetch_token(); ?>">

		<div class="text-center mt-4">
			<button type="submit" class="btn btn-installer btn-lg">
				<span class="glyphicon glyphicon-play"></span>
				Ejecutar Todas las Migraciones
			</button>
		</div>
	</form>
<?php endif; ?>

<!-- Migraciones Ejecutadas -->
<?php if ( ! empty($executed_migrations)): ?>
<div class="row mt-4">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<span class="glyphicon glyphicon-ok-circle"></span>
					Migraciones Ejecutadas
					<span class="badge" style="background-color: #28a745;"><?php echo count($executed_migrations); ?></span>
				</h4>
			</div>
			<div class="panel-body">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Migración</th>
							<th>Lote</th>
							<th>Fecha de Ejecución</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($executed_migrations as $migration): ?>
						<tr>
							<td>
								<span class="glyphicon glyphicon-ok-circle text-success"></span>
								<?php echo htmlspecialchars($migration['migration'], ENT_QUOTES, 'UTF-8'); ?>
							</td>
							<td>
								<span class="badge"><?php echo $migration['batch']; ?></span>
							</td>
							<td><?php echo $migration['executed_at']; ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<div class="row mt-4">
	<div class="col-md-12">
		<a href="<?php echo Uri::create('install'); ?>" class="btn btn-installer-outline">
			<span class="glyphicon glyphicon-arrow-left"></span>
			Volver al Inicio
		</a>
	</div>
</div>

<style>
	.mt-4 { margin-top: 30px; }
	.mb-4 { margin-bottom: 25px; }
	.text-success { color: #28a745; }
</style>
