<div class="row">
	<div class="col-12">
		<!-- Estadísticas -->
		<div class="row mb-4">
			<div class="col-lg-3 col-md-6">
				<div class="card border-0 shadow-sm">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h6 class="text-muted mb-2">Total Proveedores</h6>
								<h3 class="mb-0"><?php echo number_format($stats['total']); ?></h3>
							</div>
							<div class="text-primary">
								<i class="fas fa-building fa-2x"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6">
				<div class="card border-0 shadow-sm">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h6 class="text-muted mb-2">Activos</h6>
								<h3 class="mb-0 text-success"><?php echo number_format($stats['active']); ?></h3>
							</div>
							<div class="text-success">
								<i class="fas fa-check-circle fa-2x"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6">
				<div class="card border-0 shadow-sm">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h6 class="text-muted mb-2">Suspendidos</h6>
								<h3 class="mb-0 text-warning"><?php echo number_format($stats['suspended']); ?></h3>
							</div>
							<div class="text-warning">
								<i class="fas fa-pause-circle fa-2x"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6">
				<div class="card border-0 shadow-sm">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h6 class="text-muted mb-2">Inactivos</h6>
								<h3 class="mb-0 text-danger"><?php echo number_format($stats['inactive']); ?></h3>
							</div>
							<div class="text-danger">
								<i class="fas fa-times-circle fa-2x"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Card Principal -->
		<div class="card shadow-sm">
			<div class="card-header bg-white border-0 py-3">
				<div class="row align-items-center">
					<div class="col">
						<h3 class="mb-0">
							<i class="fas fa-building text-primary"></i> Proveedores
						</h3>
					</div>
					<div class="col-auto">
						<a href="<?php echo Uri::create('admin/proveedores/create'); ?>" class="btn btn-primary">
							<i class="fas fa-plus"></i> Nuevo Proveedor
						</a>
					</div>
				</div>
			</div>

			<div class="card-body">
				<!-- Búsqueda y Filtros -->
				<div class="row mb-3">
					<div class="col-md-6">
						<form action="<?php echo Uri::current(); ?>" method="get" id="search-form">
							<div class="input-group">
								<span class="input-group-text bg-light border-end-0">
									<i class="fas fa-search text-muted"></i>
								</span>
								<input 
									type="text" 
									name="search" 
									class="form-control border-start-0" 
									placeholder="Buscar por razón social, RFC, código, email, teléfono..."
									value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
								>
								<?php if (!empty($current_status)): ?>
									<input type="hidden" name="status" value="<?php echo htmlspecialchars($current_status, ENT_QUOTES, 'UTF-8'); ?>">
								<?php endif; ?>
								<button type="submit" class="btn btn-outline-primary">
									Buscar
								</button>
								<?php if (!empty($search) || !empty($current_status)): ?>
									<a href="<?php echo Uri::create('admin/proveedores'); ?>" class="btn btn-outline-secondary">
										<i class="fas fa-times"></i>
									</a>
								<?php endif; ?>
							</div>
						</form>
					</div>
					<div class="col-md-6">
						<div class="btn-group w-100" role="group">
							<a href="<?php echo Uri::create('admin/proveedores'); ?>" 
							   class="btn <?php echo empty($current_status) ? 'btn-primary' : 'btn-outline-primary'; ?>">
								<i class="fas fa-list"></i> Todos
							</a>
							<a href="<?php echo Uri::create('admin/proveedores', ['status' => 'active']); ?>" 
							   class="btn <?php echo $current_status == 'active' ? 'btn-success' : 'btn-outline-success'; ?>">
								<i class="fas fa-check-circle"></i> Activos
							</a>
							<a href="<?php echo Uri::create('admin/proveedores', ['status' => 'suspended']); ?>" 
							   class="btn <?php echo $current_status == 'suspended' ? 'btn-warning' : 'btn-outline-warning'; ?>">
								<i class="fas fa-pause-circle"></i> Suspendidos
							</a>
							<a href="<?php echo Uri::create('admin/proveedores', ['status' => 'inactive']); ?>" 
							   class="btn <?php echo $current_status == 'inactive' ? 'btn-danger' : 'btn-outline-danger'; ?>">
								<i class="fas fa-times-circle"></i> Inactivos
							</a>
						</div>
					</div>
				</div>

				<!-- Tabla de Proveedores -->
				<div class="table-responsive">
					<table class="table table-hover align-middle">
						<thead class="table-light">
							<tr>
								<th width="80">Código</th>
								<th>Razón Social</th>
								<th>RFC</th>
								<th>Contacto</th>
								<th>Email</th>
								<th>Teléfono</th>
								<th width="120" class="text-center">Estado</th>
								<th width="150" class="text-center">Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($providers)): ?>
								<?php foreach ($providers as $provider): ?>
									<tr>
										<td>
											<span class="badge bg-secondary">
												<?php echo htmlspecialchars($provider->code, ENT_QUOTES, 'UTF-8'); ?>
											</span>
										</td>
										<td>
											<strong><?php echo htmlspecialchars($provider->company_name, ENT_QUOTES, 'UTF-8'); ?></strong>
											<?php if (!empty($provider->city)): ?>
												<br><small class="text-muted">
													<i class="fas fa-map-marker-alt"></i> 
													<?php echo htmlspecialchars($provider->city, ENT_QUOTES, 'UTF-8'); ?>
													<?php if (!empty($provider->state)): ?>
														, <?php echo htmlspecialchars($provider->state, ENT_QUOTES, 'UTF-8'); ?>
													<?php endif; ?>
												</small>
											<?php endif; ?>
										</td>
										<td>
											<span class="font-monospace">
												<?php echo htmlspecialchars($provider->tax_id, ENT_QUOTES, 'UTF-8'); ?>
											</span>
										</td>
										<td>
											<?php if (!empty($provider->contact_name)): ?>
												<i class="fas fa-user text-muted"></i>
												<?php echo htmlspecialchars($provider->contact_name, ENT_QUOTES, 'UTF-8'); ?>
											<?php else: ?>
												<span class="text-muted">-</span>
											<?php endif; ?>
										</td>
										<td>
											<?php if (!empty($provider->email)): ?>
												<a href="mailto:<?php echo htmlspecialchars($provider->email, ENT_QUOTES, 'UTF-8'); ?>">
													<?php echo htmlspecialchars($provider->email, ENT_QUOTES, 'UTF-8'); ?>
												</a>
											<?php else: ?>
												<span class="text-muted">-</span>
											<?php endif; ?>
										</td>
										<td>
											<?php if (!empty($provider->phone)): ?>
												<i class="fas fa-phone text-muted"></i>
												<?php echo htmlspecialchars($provider->phone, ENT_QUOTES, 'UTF-8'); ?>
											<?php else: ?>
												<span class="text-muted">-</span>
											<?php endif; ?>
										</td>
										<td class="text-center">
											<?php if ($provider->is_suspended): ?>
												<span class="badge bg-warning text-dark">
													<i class="fas fa-pause-circle"></i> Suspendido
												</span>
											<?php elseif ($provider->is_active): ?>
												<span class="badge bg-success">
													<i class="fas fa-check-circle"></i> Activo
												</span>
											<?php else: ?>
												<span class="badge bg-danger">
													<i class="fas fa-times-circle"></i> Inactivo
												</span>
											<?php endif; ?>
										</td>
										<td class="text-center">
											<div class="btn-group btn-group-sm" role="group">
												<a href="<?php echo Uri::create('admin/proveedores/view/' . $provider->id); ?>" 
												   class="btn btn-outline-info" 
												   title="Ver detalles">
													<i class="fas fa-eye"></i>
												</a>
												<a href="<?php echo Uri::create('admin/proveedores/edit/' . $provider->id); ?>" 
												   class="btn btn-outline-primary" 
												   title="Editar">
													<i class="fas fa-edit"></i>
												</a>
												<button type="button" 
														class="btn btn-outline-danger btn-delete" 
														data-id="<?php echo $provider->id; ?>"
														data-name="<?php echo htmlspecialchars($provider->company_name, ENT_QUOTES, 'UTF-8'); ?>"
														title="Eliminar">
													<i class="fas fa-trash"></i>
												</button>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="8" class="text-center py-5">
										<div class="text-muted">
											<i class="fas fa-inbox fa-3x mb-3"></i>
											<p class="mb-0">No se encontraron proveedores</p>
											<?php if (!empty($search)): ?>
												<small>Intenta con otros términos de búsqueda</small>
											<?php endif; ?>
										</div>
									</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>

				<!-- Paginación -->
				<?php if (!empty($pagination)): ?>
					<div class="d-flex justify-content-center mt-4">
						<?php echo $pagination; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-danger text-white">
				<h5 class="modal-title">
					<i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
				</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<p>¿Estás seguro de que deseas eliminar al proveedor <strong id="provider-name"></strong>?</p>
				<div class="alert alert-warning">
					<i class="fas fa-info-circle"></i> Esta acción no se puede deshacer.
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
				<form method="post" id="delete-form" style="display: inline;">
					<button type="submit" class="btn btn-danger">
						<i class="fas fa-trash"></i> Eliminar
					</button>
				</form>
			</div>
		</div>
	</div>
</div>

<style>
.table > tbody > tr:hover {
	background-color: #f8f9fa;
}

.badge {
	font-weight: 500;
}

.btn-group-sm > .btn {
	padding: 0.25rem 0.5rem;
}

.card {
	border-radius: 0.5rem;
}

.card-header {
	border-bottom: 1px solid #e9ecef;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// Eliminar proveedor
	var deleteButtons = document.querySelectorAll('.btn-delete');
	var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
	var deleteForm = document.getElementById('delete-form');
	var providerName = document.getElementById('provider-name');

	deleteButtons.forEach(function(button) {
		button.addEventListener('click', function() {
			var id = this.getAttribute('data-id');
			var name = this.getAttribute('data-name');
			
			providerName.textContent = name;
			deleteForm.action = '<?php echo Uri::create('admin/proveedores/delete/'); ?>' + id;
			deleteModal.show();
		});
	});
});
</script>
