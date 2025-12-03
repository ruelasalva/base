<div class="animated fadeIn">
	<div class="row">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-bell"></i> Detalle de Notificación
					<div class="card-header-actions">
						<a href="<?php echo Uri::create('admin/notifications'); ?>" class="btn btn-sm btn-secondary">
							<i class="fa fa-arrow-left"></i> Volver
						</a>
					</div>
				</div>
				<div class="card-body">
					<!-- Tipo y Título -->
					<div class="mb-4">
						<?php
							$type_badges = [
								'info' => 'badge-info',
								'success' => 'badge-success',
								'warning' => 'badge-warning',
								'danger' => 'badge-danger',
								'system' => 'badge-secondary'
							];
							$badge = isset($type_badges[$notification['type']]) ? $type_badges[$notification['type']] : 'badge-info';
						?>
						<span class="badge <?php echo $badge; ?> badge-lg">
							<?php echo ucfirst($notification['type']); ?>
						</span>
						<h3 class="mt-2 mb-0"><?php echo $notification['title']; ?></h3>
					</div>

					<!-- Mensaje -->
					<div class="mb-4">
						<h5 class="text-muted mb-2">Mensaje:</h5>
						<div class="alert alert-light">
							<?php echo nl2br($notification['message']); ?>
						</div>
					</div>

					<!-- Enlace -->
					<?php if (!empty($notification['link'])): ?>
						<div class="mb-4">
							<h5 class="text-muted mb-2">Enlace:</h5>
							<a href="<?php echo $notification['link']; ?>" class="btn btn-primary" target="_blank">
								<i class="fa fa-external-link-alt"></i> Ir al enlace
							</a>
						</div>
					<?php endif; ?>

					<!-- Fechas -->
					<div class="row mb-3">
						<div class="col-md-6">
							<h6 class="text-muted mb-1">Fecha de Creación:</h6>
							<p class="mb-0">
								<i class="fa fa-calendar"></i> <?php echo date('d/m/Y', strtotime($notification['created_at'])); ?>
								<i class="fa fa-clock ml-2"></i> <?php echo date('H:i:s', strtotime($notification['created_at'])); ?>
							</p>
						</div>
						<div class="col-md-6">
							<?php if ($notification['is_read']): ?>
								<h6 class="text-muted mb-1">Fecha de Lectura:</h6>
								<p class="mb-0">
									<i class="fa fa-calendar"></i> <?php echo date('d/m/Y', strtotime($notification['read_at'])); ?>
									<i class="fa fa-clock ml-2"></i> <?php echo date('H:i:s', strtotime($notification['read_at'])); ?>
								</p>
							<?php else: ?>
								<h6 class="text-warning mb-1">
									<i class="fa fa-exclamation-circle"></i> No leída
								</h6>
							<?php endif; ?>
						</div>
					</div>

					<!-- Acciones -->
					<div class="border-top pt-3 mt-4">
						<a href="<?php echo Uri::create('admin/notifications'); ?>" class="btn btn-secondary">
							<i class="fa fa-arrow-left"></i> Volver a Notificaciones
						</a>
						<a href="<?php echo Uri::create('admin/notifications/delete/' . $notification['id']); ?>" 
						   class="btn btn-danger" 
						   onclick="return confirm('¿Eliminar esta notificación?')">
							<i class="fa fa-trash"></i> Eliminar
						</a>
					</div>
				</div>
			</div>
		</div>

		<!-- Sidebar -->
		<div class="col-lg-4">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-info-circle"></i> Información
				</div>
				<div class="card-body">
					<table class="table table-sm table-borderless mb-0">
						<tbody>
							<tr>
								<td class="text-muted"><strong>ID:</strong></td>
								<td><code><?php echo $notification['id']; ?></code></td>
							</tr>
							<tr>
								<td class="text-muted"><strong>Estado:</strong></td>
								<td>
									<?php if ($notification['is_read']): ?>
										<span class="badge badge-success">Leída</span>
									<?php else: ?>
										<span class="badge badge-warning">No leída</span>
									<?php endif; ?>
								</td>
							</tr>
							<tr>
								<td class="text-muted"><strong>Tipo:</strong></td>
								<td>
									<span class="badge <?php echo $badge; ?>">
										<?php echo ucfirst($notification['type']); ?>
									</span>
								</td>
							</tr>
							<tr>
								<td class="text-muted"><strong>Tenant ID:</strong></td>
								<td><code><?php echo $notification['tenant_id']; ?></code></td>
							</tr>
							<tr>
								<td class="text-muted"><strong>Usuario ID:</strong></td>
								<td><code><?php echo $notification['user_id']; ?></code></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Navegación rápida -->
			<div class="card">
				<div class="card-header">
					<i class="fa fa-link"></i> Navegación Rápida
				</div>
				<div class="card-body">
					<div class="list-group list-group-flush">
						<a href="<?php echo Uri::create('admin/notifications'); ?>" class="list-group-item list-group-item-action">
							<i class="fa fa-list"></i> Todas las notificaciones
						</a>
						<a href="<?php echo Uri::create('admin/notifications') . '?is_read=0'; ?>" class="list-group-item list-group-item-action">
							<i class="fa fa-envelope"></i> No leídas
						</a>
						<a href="<?php echo Uri::create('admin/notifications') . '?type=' . $notification['type']; ?>" class="list-group-item list-group-item-action">
							<i class="fa fa-filter"></i> Tipo: <?php echo ucfirst($notification['type']); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
