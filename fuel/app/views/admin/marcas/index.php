<div class="container-fluid p-4">
	<!-- Header -->
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h1 class="h3 mb-1">
				<i class="fa fa-copyright me-2"></i> Marcas y Fabricantes
			</h1>
			<p class="text-muted mb-0">Gestión de marcas de productos</p>
		</div>
		<?php if ($can_create): ?>
			<a href="<?php echo Uri::create('admin/marcas/create'); ?>" class="btn btn-primary">
				<i class="fa fa-plus me-1"></i> Nueva Marca
			</a>
		<?php endif; ?>
	</div>

	<!-- Estadísticas -->
	<div class="row mb-4">
		<div class="col-md-4">
			<div class="card border-0 shadow-sm">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div class="flex-shrink-0">
							<i class="fa fa-copyright fa-2x text-primary"></i>
						</div>
						<div class="flex-grow-1 ms-3">
							<h6 class="text-muted mb-1">Total Marcas</h6>
							<h3 class="mb-0"><?php echo $stats['total']; ?></h3>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card border-0 shadow-sm">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div class="flex-shrink-0">
							<i class="fa fa-check-circle fa-2x text-success"></i>
						</div>
						<div class="flex-grow-1 ms-3">
							<h6 class="text-muted mb-1">Activas</h6>
							<h3 class="mb-0"><?php echo $stats['activas']; ?></h3>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card border-0 shadow-sm">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div class="flex-shrink-0">
							<i class="fa fa-times-circle fa-2x text-warning"></i>
						</div>
						<div class="flex-grow-1 ms-3">
							<h6 class="text-muted mb-1">Inactivas</h6>
							<h3 class="mb-0"><?php echo $stats['inactivas']; ?></h3>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Tabla de marcas -->
	<div class="card border-0 shadow-sm">
		<div class="card-header bg-white py-3">
			<h5 class="mb-0">Listado de Marcas</h5>
		</div>
		<div class="card-body p-0">
			<?php if (count($brands) > 0): ?>
				<div class="table-responsive">
					<table class="table table-hover mb-0">
						<thead class="table-light">
							<tr>
								<th width="8%">ID</th>
								<th width="35%">Nombre</th>
								<th width="30%">Slug</th>
								<th width="12%" class="text-center">Estado</th>
								<th width="15%" class="text-end">Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($brands as $brand): ?>
								<tr>
									<td><?php echo $brand->id; ?></td>
									<td>
										<strong><?php echo htmlspecialchars($brand->name, ENT_QUOTES, 'UTF-8'); ?></strong>
									</td>
									<td>
										<code><?php echo htmlspecialchars($brand->slug, ENT_QUOTES, 'UTF-8'); ?></code>
									</td>
									<td class="text-center">
										<?php if ($brand->status == 1): ?>
											<span class="badge bg-success">Activa</span>
										<?php else: ?>
											<span class="badge bg-warning">Inactiva</span>
										<?php endif; ?>
									</td>
									<td class="text-end">
										<div class="btn-group btn-group-sm">
											<a href="<?php echo Uri::create('admin/marcas/view/' . $brand->id); ?>" 
											   class="btn btn-outline-info" 
											   title="Ver detalle">
												<i class="fa fa-eye"></i>
											</a>
											<?php if ($can_edit): ?>
												<a href="<?php echo Uri::create('admin/marcas/edit/' . $brand->id); ?>" 
												   class="btn btn-outline-primary" 
												   title="Editar">
													<i class="fa fa-edit"></i>
												</a>
												<a href="<?php echo Uri::create('admin/marcas/toggle_status/' . $brand->id); ?>" 
												   class="btn btn-outline-secondary" 
												   title="Cambiar estado"
												   onclick="return confirm('¿Cambiar el estado de esta marca?');">
													<i class="fa fa-toggle-on"></i>
												</a>
											<?php endif; ?>
											<?php if ($can_delete): ?>
												<a href="<?php echo Uri::create('admin/marcas/delete/' . $brand->id); ?>" 
												   class="btn btn-outline-danger" 
												   title="Eliminar"
												   onclick="return confirm('¿Está seguro de eliminar esta marca?');">
													<i class="fa fa-trash"></i>
												</a>
											<?php endif; ?>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php else: ?>
				<div class="p-5 text-center text-muted">
					<i class="fa fa-copyright fa-3x mb-3"></i>
					<p class="mb-0">No hay marcas registradas</p>
					<?php if ($can_create): ?>
						<a href="<?php echo Uri::create('admin/marcas/create'); ?>" class="btn btn-primary mt-3">
							<i class="fa fa-plus me-1"></i> Crear Primera Marca
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php if (count($brands) > 0): ?>
			<div class="card-footer bg-white">
				<?php echo $pagination->render(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
