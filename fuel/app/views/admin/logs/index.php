<div class="animated fadeIn">
	<!-- Estadísticas -->
	<div class="row mb-3">
		<div class="col-sm-6 col-lg-3">
			<div class="card text-white bg-primary">
				<div class="card-body pb-0">
					<div class="text-value"><?php echo number_format($stats['total']); ?></div>
					<div>Total Registros</div>
				</div>
				<div class="chart-wrapper px-3" style="height:70px;"></div>
			</div>
		</div>

		<div class="col-sm-6 col-lg-3">
			<div class="card text-white bg-success">
				<div class="card-body pb-0">
					<div class="text-value"><?php echo number_format($stats['today']); ?></div>
					<div>Hoy</div>
				</div>
				<div class="chart-wrapper px-3" style="height:70px;"></div>
			</div>
		</div>

		<div class="col-sm-6 col-lg-3">
			<div class="card text-white bg-info">
				<div class="card-body pb-0">
					<div class="text-value"><?php echo number_format($stats['week']); ?></div>
					<div>Esta Semana</div>
				</div>
				<div class="chart-wrapper px-3" style="height:70px;"></div>
			</div>
		</div>

		<div class="col-sm-6 col-lg-3">
			<div class="card text-white bg-warning">
				<div class="card-body pb-0">
					<div class="text-value"><?php echo count($stats['top_modules']); ?></div>
					<div>Módulos Activos</div>
				</div>
				<div class="chart-wrapper px-3" style="height:70px;"></div>
			</div>
		</div>
	</div>

	<!-- Filtros y Lista -->
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-list"></i> <?php echo $title; ?>
					<div class="card-header-actions">
						<?php if ($can_export): ?>
							<a href="<?php echo Uri::create('admin/logs/export') . '?' . http_build_query($filters); ?>" class="btn btn-sm btn-success" title="Exportar a CSV">
								<i class="fa fa-download"></i> Exportar
							</a>
						<?php endif; ?>
						<?php if ($can_delete): ?>
							<button onclick="cleanupLogs()" class="btn btn-sm btn-danger" title="Limpiar logs antiguos">
								<i class="fa fa-trash"></i> Limpiar
							</button>
						<?php endif; ?>
					</div>
				</div>
				<div class="card-body">
					<!-- Formulario de Filtros -->
					<form method="get" action="<?php echo Uri::create('admin/logs'); ?>" class="mb-3">
						<div class="row">
							<div class="col-md-2">
								<select name="module" class="form-control form-control-sm">
									<option value="">Todos los módulos</option>
									<?php foreach ($modules as $mod): ?>
										<option value="<?php echo $mod; ?>" <?php echo (isset($filters['module']) && $filters['module'] == $mod) ? 'selected' : ''; ?>>
											<?php echo ucfirst($mod); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-2">
								<select name="action" class="form-control form-control-sm">
									<option value="">Todas las acciones</option>
									<?php foreach ($actions as $act): ?>
										<option value="<?php echo $act; ?>" <?php echo (isset($filters['action']) && $filters['action'] == $act) ? 'selected' : ''; ?>>
											<?php echo $act; ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-2">
								<select name="user_id" class="form-control form-control-sm">
									<option value="">Todos los usuarios</option>
									<?php foreach ($users as $user): ?>
										<option value="<?php echo $user['id']; ?>" <?php echo (isset($filters['user_id']) && $filters['user_id'] == $user['id']) ? 'selected' : ''; ?>>
											<?php echo $user['username']; ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-2">
								<input type="date" name="date_from" class="form-control form-control-sm" placeholder="Desde" value="<?php echo isset($filters['date_from']) ? $filters['date_from'] : ''; ?>">
							</div>
							<div class="col-md-2">
								<input type="date" name="date_to" class="form-control form-control-sm" placeholder="Hasta" value="<?php echo isset($filters['date_to']) ? $filters['date_to'] : ''; ?>">
							</div>
							<div class="col-md-2">
								<div class="btn-group btn-group-sm btn-block">
									<button type="submit" class="btn btn-primary">
										<i class="fa fa-search"></i> Filtrar
									</button>
									<a href="<?php echo Uri::create('admin/logs'); ?>" class="btn btn-secondary">
										<i class="fa fa-times"></i>
									</a>
								</div>
							</div>
						</div>
						<div class="row mt-2">
							<div class="col-md-12">
								<input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar en descripción o usuario..." value="<?php echo isset($filters['search']) ? $filters['search'] : ''; ?>">
							</div>
						</div>
					</form>

					<!-- Tabla de Logs -->
					<?php if (empty($logs)): ?>
						<div class="alert alert-info">
							<i class="fa fa-info-circle"></i> No se encontraron registros con los filtros aplicados.
						</div>
					<?php else: ?>
						<div class="table-responsive">
							<table class="table table-hover table-sm">
								<thead>
									<tr>
										<th width="5%">ID</th>
										<th width="12%">Fecha/Hora</th>
										<th width="10%">Usuario</th>
										<th width="10%">Módulo</th>
										<th width="10%">Acción</th>
										<th width="40%">Descripción</th>
										<th width="8%">IP</th>
										<th width="5%" class="text-right">Ver</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($logs as $log): ?>
										<tr>
											<td><code><?php echo $log['id']; ?></code></td>
											<td>
												<small>
													<?php echo date('d/m/Y', strtotime($log['created_at'])); ?><br>
													<span class="text-muted"><?php echo date('H:i:s', strtotime($log['created_at'])); ?></span>
												</small>
											</td>
											<td>
												<strong><?php echo $log['username']; ?></strong>
												<?php if ($log['username'] == 'system'): ?>
													<span class="badge badge-secondary badge-sm">Sistema</span>
												<?php endif; ?>
											</td>
											<td>
												<span class="badge badge-primary"><?php echo $log['module']; ?></span>
											</td>
											<td>
												<?php
													$action_colors = [
														'create' => 'success',
														'edit' => 'info',
														'delete' => 'danger',
														'view' => 'secondary',
														'login_success' => 'success',
														'login_failed' => 'danger',
														'logout' => 'warning'
													];
													$color = isset($action_colors[$log['action']]) ? $action_colors[$log['action']] : 'dark';
												?>
												<span class="badge badge-<?php echo $color; ?>"><?php echo $log['action']; ?></span>
											</td>
											<td>
												<small><?php echo $log['description']; ?></small>
											</td>
											<td><small class="text-muted"><?php echo $log['ip_address']; ?></small></td>
											<td class="text-right">
												<a href="<?php echo Uri::create('admin/logs/view/' . $log['id']); ?>" class="btn btn-sm btn-info" title="Ver detalle">
													<i class="fa fa-eye"></i>
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>

						<!-- Paginación -->
						<?php if ($pagination['total_pages'] > 1): ?>
							<nav aria-label="Paginación de logs">
								<ul class="pagination justify-content-center">
									<?php if ($pagination['page'] > 1): ?>
										<li class="page-item">
											<a class="page-link" href="<?php echo Uri::create('admin/logs') . '?' . http_build_query(array_merge($filters, ['page' => $pagination['page'] - 1])); ?>">Anterior</a>
										</li>
									<?php endif; ?>

									<?php for ($i = max(1, $pagination['page'] - 2); $i <= min($pagination['total_pages'], $pagination['page'] + 2); $i++): ?>
										<li class="page-item <?php echo $i == $pagination['page'] ? 'active' : ''; ?>">
											<a class="page-link" href="<?php echo Uri::create('admin/logs') . '?' . http_build_query(array_merge($filters, ['page' => $i])); ?>">
												<?php echo $i; ?>
											</a>
										</li>
									<?php endfor; ?>

									<?php if ($pagination['page'] < $pagination['total_pages']): ?>
										<li class="page-item">
											<a class="page-link" href="<?php echo Uri::create('admin/logs') . '?' . http_build_query(array_merge($filters, ['page' => $pagination['page'] + 1])); ?>">Siguiente</a>
										</li>
									<?php endif; ?>
								</ul>
							</nav>
							<p class="text-center text-muted">
								Mostrando <?php echo (($pagination['page'] - 1) * $pagination['per_page']) + 1; ?> 
								- <?php echo min($pagination['page'] * $pagination['per_page'], $pagination['total']); ?> 
								de <?php echo number_format($pagination['total']); ?> registros
							</p>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Top Módulos y Usuarios -->
	<div class="row">
		<div class="col-lg-6">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-chart-pie"></i> Módulos Más Activos
				</div>
				<div class="card-body">
					<?php if (empty($stats['top_modules'])): ?>
						<p class="text-muted mb-0"><em>Sin datos</em></p>
					<?php else: ?>
						<table class="table table-sm mb-0">
							<tbody>
								<?php foreach ($stats['top_modules'] as $mod): ?>
									<tr>
										<td width="40%"><strong><?php echo ucfirst($mod['module']); ?></strong></td>
										<td width="60%">
											<div class="progress" style="height: 20px;">
												<?php $percentage = ($mod['count'] / $stats['total']) * 100; ?>
												<div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $percentage; ?>%">
													<?php echo number_format($mod['count']); ?> (<?php echo number_format($percentage, 1); ?>%)
												</div>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-users"></i> Usuarios Más Activos
				</div>
				<div class="card-body">
					<?php if (empty($stats['top_users'])): ?>
						<p class="text-muted mb-0"><em>Sin datos</em></p>
					<?php else: ?>
						<table class="table table-sm mb-0">
							<tbody>
								<?php foreach ($stats['top_users'] as $usr): ?>
									<tr>
										<td width="40%"><strong><?php echo $usr['username']; ?></strong></td>
										<td width="60%">
											<div class="progress" style="height: 20px;">
												<?php $percentage = ($usr['count'] / $stats['total']) * 100; ?>
												<div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $percentage; ?>%">
													<?php echo number_format($usr['count']); ?> (<?php echo number_format($percentage, 1); ?>%)
												</div>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
function cleanupLogs() {
	var days = prompt('¿Cuántos días de logs deseas conservar?\n\nSe eliminarán los registros más antiguos que esta cantidad de días.', '90');
	
	if (days === null) return;
	
	days = parseInt(days);
	if (isNaN(days) || days < 1) {
		alert('Por favor ingresa un número válido de días');
		return;
	}
	
	if (!confirm('¿Estás seguro de eliminar los logs con más de ' + days + ' días?\n\nEsta acción no se puede deshacer.')) {
		return;
	}
	
	fetch('<?php echo Uri::create("admin/logs/cleanup"); ?>', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-Requested-With': 'XMLHttpRequest'
		},
		body: JSON.stringify({ days: days })
	})
	.then(response => response.json())
	.then(data => {
		if (data.success) {
			alert(data.message);
			location.reload();
		} else {
			alert('Error: ' + data.message);
		}
	})
	.catch(error => {
		console.error('Error:', error);
		alert('Error al limpiar los logs');
	});
}
</script>
