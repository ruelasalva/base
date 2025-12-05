<div class="container-fluid p-4">
	<!-- Header -->
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h1 class="h3 mb-1">
				<i class="fa fa-sitemap me-2"></i> Detalle de Categoría
			</h1>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-0">
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Dashboard</a></li>
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/categorias'); ?>">Categorías</a></li>
					<li class="breadcrumb-item active"><?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?></li>
				</ol>
			</nav>
		</div>
		<div>
			<?php if ($can_edit): ?>
				<a href="<?php echo Uri::create('admin/categorias/edit/' . $category->id); ?>" class="btn btn-primary me-2">
					<i class="fa fa-edit me-1"></i> Editar
				</a>
			<?php endif; ?>
			<a href="<?php echo Uri::create('admin/categorias'); ?>" class="btn btn-outline-secondary">
				<i class="fa fa-arrow-left me-1"></i> Volver
			</a>
		</div>
	</div>

	<div class="row">
		<!-- Información Principal -->
		<div class="col-lg-8">
			<div class="card border-0 shadow-sm mb-4">
				<div class="card-header bg-white d-flex justify-content-between align-items-center">
					<h5 class="mb-0">Información General</h5>
					<?php if ($category->is_active): ?>
						<span class="badge bg-success">Activa</span>
					<?php else: ?>
						<span class="badge bg-warning">Inactiva</span>
					<?php endif; ?>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="text-muted small">ID</label>
							<p class="mb-0 fw-bold">#<?php echo $category->id; ?></p>
						</div>
						<div class="col-md-6 mb-3">
							<label class="text-muted small">Slug</label>
							<p class="mb-0"><code><?php echo htmlspecialchars($category->slug, ENT_QUOTES, 'UTF-8'); ?></code></p>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12 mb-3">
							<label class="text-muted small">Nombre</label>
							<p class="mb-0 fw-bold h5"><?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?></p>
						</div>
					</div>

					<?php if ($category->description): ?>
						<div class="row">
							<div class="col-md-12 mb-3">
								<label class="text-muted small">Descripción</label>
								<p class="mb-0"><?php echo nl2br(htmlspecialchars($category->description, ENT_QUOTES, 'UTF-8')); ?></p>
							</div>
						</div>
					<?php endif; ?>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="text-muted small">Categoría Padre</label>
							<?php if ($parent): ?>
								<p class="mb-0">
									<a href="<?php echo Uri::create('admin/categorias/view/' . $parent->id); ?>">
										<?php echo htmlspecialchars($parent->name, ENT_QUOTES, 'UTF-8'); ?>
									</a>
								</p>
							<?php else: ?>
								<p class="mb-0"><span class="badge bg-primary">Categoría Principal</span></p>
							<?php endif; ?>
						</div>
						<div class="col-md-6 mb-3">
							<label class="text-muted small">Orden de Visualización</label>
							<p class="mb-0"><?php echo $category->sort_order; ?></p>
						</div>
					</div>

					<hr>

					<div class="row">
						<div class="col-md-6">
							<label class="text-muted small">Fecha de Creación</label>
							<p class="mb-0"><?php echo date('d/m/Y H:i', strtotime($category->created_at)); ?></p>
						</div>
						<?php if ($category->updated_at): ?>
							<div class="col-md-6">
								<label class="text-muted small">Última Actualización</label>
								<p class="mb-0"><?php echo date('d/m/Y H:i', strtotime($category->updated_at)); ?></p>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<!-- Subcategorías -->
			<?php if (count($subcategories) > 0): ?>
				<div class="card border-0 shadow-sm mb-4">
					<div class="card-header bg-white">
						<h5 class="mb-0">
							<i class="fa fa-folder me-2"></i> Subcategorías (<?php echo count($subcategories); ?>)
						</h5>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-hover mb-0">
								<thead class="table-light">
									<tr>
										<th>ID</th>
										<th>Nombre</th>
										<th>Slug</th>
										<th>Orden</th>
										<th class="text-center">Estado</th>
										<th class="text-end">Acciones</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($subcategories as $sub): ?>
										<tr>
											<td><?php echo $sub->id; ?></td>
											<td><?php echo htmlspecialchars($sub->name, ENT_QUOTES, 'UTF-8'); ?></td>
											<td><code><?php echo htmlspecialchars($sub->slug, ENT_QUOTES, 'UTF-8'); ?></code></td>
											<td><?php echo $sub->sort_order; ?></td>
											<td class="text-center">
												<?php if ($sub->is_active): ?>
													<span class="badge bg-success">Activa</span>
												<?php else: ?>
													<span class="badge bg-warning">Inactiva</span>
												<?php endif; ?>
											</td>
											<td class="text-end">
												<a href="<?php echo Uri::create('admin/categorias/view/' . $sub->id); ?>" 
												   class="btn btn-sm btn-outline-info">
													<i class="fa fa-eye"></i>
												</a>
												<?php if ($can_edit): ?>
													<a href="<?php echo Uri::create('admin/categorias/edit/' . $sub->id); ?>" 
													   class="btn btn-sm btn-outline-primary">
														<i class="fa fa-edit"></i>
													</a>
												<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<!-- Sidebar Estadísticas -->
		<div class="col-lg-4">
			<!-- Estadística de Productos -->
			<div class="card border-0 shadow-sm mb-3">
				<div class="card-body text-center">
					<i class="fa fa-box fa-3x text-primary mb-3"></i>
					<h2 class="mb-1"><?php echo $products_count; ?></h2>
					<p class="text-muted mb-0">Productos Asociados</p>
				</div>
				<?php if ($products_count > 0): ?>
					<div class="card-footer bg-white text-center">
						<a href="<?php echo Uri::create('admin/productos?category_id=' . $category->id); ?>" class="text-decoration-none">
							Ver Productos <i class="fa fa-arrow-right ms-1"></i>
						</a>
					</div>
				<?php endif; ?>
			</div>

			<!-- Estadística de Subcategorías -->
			<div class="card border-0 shadow-sm mb-3">
				<div class="card-body text-center">
					<i class="fa fa-folder fa-3x text-info mb-3"></i>
					<h2 class="mb-1"><?php echo count($subcategories); ?></h2>
					<p class="text-muted mb-0">Subcategorías</p>
				</div>
			</div>

			<!-- Acciones Rápidas -->
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-light">
					<h6 class="mb-0"><i class="fa fa-bolt me-1"></i> Acciones Rápidas</h6>
				</div>
				<div class="card-body">
					<div class="d-grid gap-2">
						<?php if ($can_edit): ?>
							<a href="<?php echo Uri::create('admin/categorias/edit/' . $category->id); ?>" 
							   class="btn btn-outline-primary">
								<i class="fa fa-edit me-1"></i> Editar Categoría
							</a>
							<a href="<?php echo Uri::create('admin/categorias/toggle_status/' . $category->id); ?>" 
							   class="btn btn-outline-secondary"
							   onclick="return confirm('¿Cambiar el estado de esta categoría?');">
								<i class="fa fa-toggle-on me-1"></i> 
								<?php echo $category->is_active ? 'Desactivar' : 'Activar'; ?>
							</a>
							<a href="<?php echo Uri::create('admin/categorias/create?parent_id=' . $category->id); ?>" 
							   class="btn btn-outline-success">
								<i class="fa fa-plus me-1"></i> Agregar Subcategoría
							</a>
						<?php endif; ?>
						<?php if ($can_delete && count($subcategories) == 0 && $products_count == 0): ?>
							<a href="<?php echo Uri::create('admin/categorias/delete/' . $category->id); ?>" 
							   class="btn btn-outline-danger"
							   onclick="return confirm('¿Está seguro de eliminar esta categoría?');">
								<i class="fa fa-trash me-1"></i> Eliminar
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
