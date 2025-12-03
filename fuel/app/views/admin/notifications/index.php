<div class="animated fadeIn">
	<!-- Estadísticas -->
	<div class="row mb-3">
		<div class="col-sm-6 col-lg-3">
			<div class="card text-white bg-primary">
				<div class="card-body pb-0">
					<div class="text-value"><?php echo number_format($stats['total']); ?></div>
					<div>Total Notificaciones</div>
				</div>
				<div class="chart-wrapper px-3" style="height:70px;"></div>
			</div>
		</div>

		<div class="col-sm-6 col-lg-3">
			<div class="card text-white bg-warning">
				<div class="card-body pb-0">
					<div class="text-value"><?php echo number_format($stats['unread']); ?></div>
					<div>No Leídas</div>
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
	</div>

	<!-- Lista de Notificaciones -->
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-bell"></i> <?php echo $title; ?>
					<div class="card-header-actions">
						<button onclick="markAllRead()" class="btn btn-sm btn-primary" title="Marcar todas como leídas">
							<i class="fa fa-check-double"></i> Marcar Todas Leídas
						</button>
						<?php if ($can_admin): ?>
							<a href="<?php echo Uri::create('admin/notifications/send'); ?>" class="btn btn-sm btn-success" title="Enviar notificación">
								<i class="fa fa-paper-plane"></i> Enviar
							</a>
						<?php endif; ?>
					</div>
				</div>
				<div class="card-body">
					<!-- Filtros -->
					<form method="get" action="<?php echo Uri::create('admin/notifications'); ?>" class="mb-3">
						<div class="row">
							<div class="col-md-3">
								<select name="type" class="form-control form-control-sm">
									<option value="">Todos los tipos</option>
									<?php foreach ($types as $t): ?>
										<option value="<?php echo $t; ?>" <?php echo (isset($filters['type']) && $filters['type'] == $t) ? 'selected' : ''; ?>>
											<?php echo ucfirst($t); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-2">
								<select name="is_read" class="form-control form-control-sm">
									<option value="">Todas</option>
									<option value="0" <?php echo (isset($filters['is_read']) && $filters['is_read'] === '0') ? 'selected' : ''; ?>>No leídas</option>
									<option value="1" <?php echo (isset($filters['is_read']) && $filters['is_read'] === '1') ? 'selected' : ''; ?>>Leídas</option>
								</select>
							</div>
							<div class="col-md-2">
								<input type="date" name="date_from" class="form-control form-control-sm" placeholder="Desde" value="<?php echo isset($filters['date_from']) ? $filters['date_from'] : ''; ?>">
							</div>
							<div class="col-md-2">
								<input type="date" name="date_to" class="form-control form-control-sm" placeholder="Hasta" value="<?php echo isset($filters['date_to']) ? $filters['date_to'] : ''; ?>">
							</div>
							<div class="col-md-3">
								<div class="btn-group btn-group-sm btn-block">
									<button type="submit" class="btn btn-primary">
										<i class="fa fa-search"></i> Filtrar
									</button>
									<a href="<?php echo Uri::create('admin/notifications'); ?>" class="btn btn-secondary">
										<i class="fa fa-times"></i>
									</a>
								</div>
							</div>
						</div>
					</form>

					<!-- Lista -->
					<?php if (empty($notifications)): ?>
						<div class="alert alert-info">
							<i class="fa fa-info-circle"></i> No tienes notificaciones con los filtros aplicados.
						</div>
					<?php else: ?>
						<div class="list-group">
							<?php foreach ($notifications as $notif): ?>
								<a href="<?php echo Uri::create('admin/notifications/view/' . $notif['id']); ?>" 
								   class="list-group-item list-group-item-action <?php echo $notif['is_read'] == 0 ? 'list-group-item-warning' : ''; ?>">
									<div class="d-flex w-100 justify-content-between align-items-start">
										<div class="flex-fill">
											<div class="d-flex align-items-center mb-1">
												<?php
													$type_icons = [
														'info' => 'fa-info-circle text-info',
														'success' => 'fa-check-circle text-success',
														'warning' => 'fa-exclamation-triangle text-warning',
														'danger' => 'fa-times-circle text-danger',
														'system' => 'fa-cog text-secondary'
													];
													$icon = isset($type_icons[$notif['type']]) ? $type_icons[$notif['type']] : 'fa-bell text-info';
												?>
												<i class="fa <?php echo $icon; ?> fa-lg mr-2"></i>
												<h6 class="mb-0 <?php echo $notif['is_read'] == 0 ? 'font-weight-bold' : ''; ?>">
													<?php echo $notif['title']; ?>
												</h6>
												<?php if ($notif['is_read'] == 0): ?>
													<span class="badge badge-warning ml-2">Nuevo</span>
												<?php endif; ?>
											</div>
											<p class="mb-1 text-muted">
												<?php echo strlen($notif['message']) > 150 ? substr($notif['message'], 0, 150) . '...' : $notif['message']; ?>
											</p>
											<small class="text-muted">
												<i class="fa fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($notif['created_at'])); ?>
											</small>
										</div>
										<div class="ml-3">
											<?php if ($notif['is_read'] == 0): ?>
												<button onclick="markAsRead(<?php echo $notif['id']; ?>, event)" 
												        class="btn btn-sm btn-success" 
												        title="Marcar como leída">
													<i class="fa fa-check"></i>
												</button>
											<?php else: ?>
												<span class="badge badge-success">Leída</span>
											<?php endif; ?>
										</div>
									</div>
								</a>
							<?php endforeach; ?>
						</div>

						<!-- Paginación -->
						<?php if ($pagination['total_pages'] > 1): ?>
							<nav aria-label="Paginación de notificaciones" class="mt-3">
								<ul class="pagination justify-content-center">
									<?php if ($pagination['page'] > 1): ?>
										<li class="page-item">
											<a class="page-link" href="<?php echo Uri::create('admin/notifications') . '?' . http_build_query(array_merge($filters, ['page' => $pagination['page'] - 1])); ?>">Anterior</a>
										</li>
									<?php endif; ?>

									<?php for ($i = max(1, $pagination['page'] - 2); $i <= min($pagination['total_pages'], $pagination['page'] + 2); $i++): ?>
										<li class="page-item <?php echo $i == $pagination['page'] ? 'active' : ''; ?>">
											<a class="page-link" href="<?php echo Uri::create('admin/notifications') . '?' . http_build_query(array_merge($filters, ['page' => $i])); ?>">
												<?php echo $i; ?>
											</a>
										</li>
									<?php endfor; ?>

									<?php if ($pagination['page'] < $pagination['total_pages']): ?>
										<li class="page-item">
											<a class="page-link" href="<?php echo Uri::create('admin/notifications') . '?' . http_build_query(array_merge($filters, ['page' => $pagination['page'] + 1])); ?>">Siguiente</a>
										</li>
									<?php endif; ?>
								</ul>
							</nav>
							<p class="text-center text-muted">
								Mostrando <?php echo (($pagination['page'] - 1) * $pagination['per_page']) + 1; ?> 
								- <?php echo min($pagination['page'] * $pagination['per_page'], $pagination['total']); ?> 
								de <?php echo number_format($pagination['total']); ?> notificaciones
							</p>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
function markAsRead(id, event) {
	event.preventDefault();
	event.stopPropagation();
	
	fetch('<?php echo Uri::create("admin/notifications/mark_read"); ?>', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-Requested-With': 'XMLHttpRequest'
		},
		body: JSON.stringify({ id: id })
	})
	.then(response => response.json())
	.then(data => {
		if (data.success) {
			location.reload();
		} else {
			alert('Error: ' + data.message);
		}
	})
	.catch(error => {
		console.error('Error:', error);
		alert('Error al marcar como leída');
	});
}

function markAllRead() {
	if (!confirm('¿Marcar todas las notificaciones como leídas?')) {
		return;
	}
	
	fetch('<?php echo Uri::create("admin/notifications/mark_all_read"); ?>', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-Requested-With': 'XMLHttpRequest'
		}
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
		alert('Error al marcar todas como leídas');
	});
}
</script>
