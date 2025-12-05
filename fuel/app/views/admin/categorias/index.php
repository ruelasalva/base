<div class="container-fluid p-4">
	<!-- Header -->
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h1 class="h3 mb-1">
				<i class="fa fa-sitemap me-2"></i> Categorías de Productos
			</h1>
			<p class="text-muted mb-0">Gestión de categorías y subcategorías</p>
		</div>
		<?php if ($can_create): ?>
			<a href="<?php echo Uri::create('admin/categorias/create'); ?>" class="btn btn-primary">
				<i class="fa fa-plus me-1"></i> Nueva Categoría
			</a>
		<?php endif; ?>
	</div>

	<!-- Estadísticas -->
	<div class="row mb-4">
		<div class="col-md-3">
			<div class="card border-0 shadow-sm">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div class="flex-shrink-0">
							<i class="fa fa-sitemap fa-2x text-primary"></i>
						</div>
						<div class="flex-grow-1 ms-3">
							<h6 class="text-muted mb-1">Total Categorías</h6>
							<h3 class="mb-0"><?php echo $stats['total']; ?></h3>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3">
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
		<div class="col-md-3">
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
		<div class="col-md-3">
			<div class="card border-0 shadow-sm">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div class="flex-shrink-0">
							<i class="fa fa-folder fa-2x text-info"></i>
						</div>
						<div class="flex-grow-1 ms-3">
							<h6 class="text-muted mb-1">Principales</h6>
							<h3 class="mb-0"><?php echo $stats['principales']; ?></h3>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Tabla de categorías -->
	<div class="card border-0 shadow-sm">
		<div class="card-header bg-white py-3">
			<h5 class="mb-0">Listado de Categorías</h5>
		</div>
		<div class="card-body p-0">
			<?php if (count($categories) > 0): ?>
				<div class="table-responsive">
					<table class="table table-hover mb-0">
						<thead class="table-light">
							<tr>
								<th width="5%">ID</th>
								<th width="30%">Nombre</th>
								<th width="15%">Slug</th>
								<th width="20%">Categoría Padre</th>
								<th width="8%" class="text-center">Orden</th>
								<th width="10%" class="text-center">Estado</th>
								<th width="12%" class="text-end">Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($categories as $category): ?>
								<tr>
									<td><?php echo $category->id; ?></td>
									<td>
										<?php if ($category->parent_id): ?>
											<i class="fa fa-level-up-alt me-2 text-muted"></i>
										<?php endif; ?>
										<strong><?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?></strong>
									</td>
									<td>
										<code><?php echo htmlspecialchars($category->slug, ENT_QUOTES, 'UTF-8'); ?></code>
									</td>
									<td>
										<?php if ($category->parent_id): ?>
											<?php
											$parent = Model_Category::find($category->parent_id);
											if ($parent) {
												echo htmlspecialchars($parent->name, ENT_QUOTES, 'UTF-8');
											} else {
												echo '<span class="text-muted">-</span>';
											}
											?>
										<?php else: ?>
											<span class="badge bg-primary">Principal</span>
										<?php endif; ?>
									</td>
									<td class="text-center">
										<?php echo $category->sort_order; ?>
									</td>
									<td class="text-center">
										<?php if ($category->is_active): ?>
											<span class="badge bg-success">Activa</span>
										<?php else: ?>
											<span class="badge bg-warning">Inactiva</span>
										<?php endif; ?>
									</td>
									<td class="text-end">
										<div class="btn-group btn-group-sm">
											<a href="<?php echo Uri::create('admin/categorias/view/' . $category->id); ?>" 
											   class="btn btn-outline-info" 
											   title="Ver detalle">
												<i class="fa fa-eye"></i>
											</a>
											<?php if ($can_edit): ?>
												<a href="<?php echo Uri::create('admin/categorias/edit/' . $category->id); ?>" 
												   class="btn btn-outline-primary" 
												   title="Editar">
													<i class="fa fa-edit"></i>
												</a>
												<a href="<?php echo Uri::create('admin/categorias/toggle_status/' . $category->id); ?>" 
												   class="btn btn-outline-secondary" 
												   title="Cambiar estado"
												   onclick="return confirm('¿Cambiar el estado de esta categoría?');">
													<i class="fa fa-toggle-on"></i>
												</a>
											<?php endif; ?>
											<?php if ($can_delete): ?>
												<a href="<?php echo Uri::create('admin/categorias/delete/' . $category->id); ?>" 
												   class="btn btn-outline-danger" 
												   title="Eliminar"
												   onclick="return confirm('¿Está seguro de eliminar esta categoría?');">
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
					<i class="fa fa-sitemap fa-3x mb-3"></i>
					<p class="mb-0">No hay categorías registradas</p>
					<?php if ($can_create): ?>
						<a href="<?php echo Uri::create('admin/categorias/create'); ?>" class="btn btn-primary mt-3">
							<i class="fa fa-plus me-1"></i> Crear Primera Categoría
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php if (count($categories) > 0): ?>
			<div class="card-footer bg-white">
				<?php echo $pagination->render(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
