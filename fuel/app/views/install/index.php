<div class="text-center mb-4">
	<h2>Bienvenido al Instalador</h2>
	<p class="text-muted">Este asistente le ayudará a configurar su sistema ERP Multi-tenant</p>
</div>

<!-- Estado del Sistema -->
<div class="row">
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<span class="glyphicon glyphicon-info-sign"></span>
					Estado del Sistema
				</h4>
			</div>
			<div class="panel-body">
				<table class="table table-striped">
					<tr>
						<td>Versión PHP</td>
						<td>
							<?php if (version_compare(PHP_VERSION, '7.2.0', '>=')): ?>
								<span class="status-badge status-success">
									<span class="glyphicon glyphicon-ok"></span>
									<?php echo PHP_VERSION; ?>
								</span>
							<?php else: ?>
								<span class="status-badge status-danger">
									<span class="glyphicon glyphicon-remove"></span>
									<?php echo PHP_VERSION; ?> (Requiere 7.2+)
								</span>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td>Extensión PDO MySQL</td>
						<td>
							<?php if (extension_loaded('pdo_mysql')): ?>
								<span class="status-badge status-success">
									<span class="glyphicon glyphicon-ok"></span>
									Instalada
								</span>
							<?php else: ?>
								<span class="status-badge status-danger">
									<span class="glyphicon glyphicon-remove"></span>
									No instalada
								</span>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td>Conexión a Base de Datos</td>
						<td>
							<?php if ($db_connected): ?>
								<span class="status-badge status-success">
									<span class="glyphicon glyphicon-ok"></span>
									Conectado
								</span>
							<?php else: ?>
								<span class="status-badge status-warning">
									<span class="glyphicon glyphicon-warning-sign"></span>
									No configurado
								</span>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td>Estado de Instalación</td>
						<td>
							<?php if ($is_installed): ?>
								<span class="status-badge status-success">
									<span class="glyphicon glyphicon-ok"></span>
									Instalado
								</span>
							<?php else: ?>
								<span class="status-badge status-info">
									<span class="glyphicon glyphicon-info-sign"></span>
									Pendiente
								</span>
							<?php endif; ?>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<span class="glyphicon glyphicon-tasks"></span>
					Migraciones
				</h4>
			</div>
			<div class="panel-body">
				<?php if ($error): ?>
					<div class="alert alert-danger">
						<span class="glyphicon glyphicon-exclamation-sign"></span>
						<?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
					</div>
				<?php endif; ?>

				<?php if ($db_connected): ?>
					<table class="table table-striped">
						<tr>
							<td>Migraciones Ejecutadas</td>
							<td>
								<span class="badge"><?php echo count($executed_migrations); ?></span>
							</td>
						</tr>
						<tr>
							<td>Migraciones Pendientes</td>
							<td>
								<?php if (count($pending_migrations) > 0): ?>
									<span class="badge" style="background-color: #ffc107;"><?php echo count($pending_migrations); ?></span>
								<?php else: ?>
									<span class="badge" style="background-color: #28a745;">0</span>
								<?php endif; ?>
							</td>
						</tr>
					</table>

					<?php if (count($pending_migrations) > 0): ?>
						<div class="alert alert-warning">
							<span class="glyphicon glyphicon-warning-sign"></span>
							Hay migraciones pendientes por ejecutar.
						</div>
					<?php endif; ?>
				<?php else: ?>
					<div class="alert alert-info">
						<span class="glyphicon glyphicon-info-sign"></span>
						Configure la conexión a la base de datos para ver las migraciones.
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<!-- Acciones -->
<div class="row mt-4">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<span class="glyphicon glyphicon-play"></span>
					Acciones
				</h4>
			</div>
			<div class="panel-body">
				<div class="row">
					<?php if ( ! $db_connected): ?>
						<div class="col-md-4 text-center mb-3">
							<a href="<?php echo Uri::create('install/configurar'); ?>" class="btn btn-installer btn-lg btn-block">
								<span class="glyphicon glyphicon-cog"></span>
								Configurar Base de Datos
							</a>
							<p class="text-muted mt-2">Configure la conexión a MySQL</p>
						</div>
					<?php else: ?>
						<div class="col-md-4 text-center mb-3">
							<a href="<?php echo Uri::create('install/configurar'); ?>" class="btn btn-installer-outline btn-lg btn-block">
								<span class="glyphicon glyphicon-cog"></span>
								Reconfigurar BD
							</a>
							<p class="text-muted mt-2">Cambiar configuración de base de datos</p>
						</div>
					<?php endif; ?>

					<div class="col-md-4 text-center mb-3">
						<a href="<?php echo Uri::create('install/ejecutar'); ?>" class="btn btn-installer btn-lg btn-block <?php echo ! $db_connected ? 'disabled' : ''; ?>">
							<span class="glyphicon glyphicon-upload"></span>
							Ejecutar Migraciones
						</a>
						<p class="text-muted mt-2">Crear/Actualizar tablas de la base de datos</p>
					</div>

					<div class="col-md-4 text-center mb-3">
						<a href="<?php echo Uri::create('install/crear_admin'); ?>" class="btn btn-installer btn-lg btn-block <?php echo ! $db_connected ? 'disabled' : ''; ?>">
							<span class="glyphicon glyphicon-user"></span>
							Crear Administrador
						</a>
						<p class="text-muted mt-2">Crear el usuario administrador inicial</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Información de Migraciones Ejecutadas -->
<?php if ($db_connected && count($executed_migrations) > 0): ?>
<div class="row mt-4">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<span class="glyphicon glyphicon-list"></span>
					Historial de Migraciones
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

<style>
	.mt-4 { margin-top: 30px; }
	.mb-3 { margin-bottom: 15px; }
	.mt-2 { margin-top: 10px; }
	.text-success { color: #28a745; }
</style>
