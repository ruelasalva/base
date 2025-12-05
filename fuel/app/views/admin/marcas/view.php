<div class="container-fluid p-4">
	<!-- Header -->
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h1 class="h3 mb-1">
				<i class="fa fa-copyright me-2"></i> Detalle de Marca
			</h1>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-0">
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Dashboard</a></li>
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/marcas'); ?>">Marcas</a></li>
					<li class="breadcrumb-item active"><?php echo htmlspecialchars($brand->name, ENT_QUOTES, 'UTF-8'); ?></li>
				</ol>
			</nav>
		</div>
		<div>
			<?php if ($can_edit): ?>
				<a href="<?php echo Uri::create('admin/marcas/edit/' . $brand->id); ?>" class="btn btn-primary me-2">
					<i class="fa fa-edit me-1"></i> Editar
				</a>
			<?php endif; ?>
			<a href="<?php echo Uri::create('admin/marcas'); ?>" class="btn btn-outline-secondary">
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
					<?php if ($brand->status == 1): ?>
						<span class="badge bg-success">Activa</span>
					<?php else: ?>
						<span class="badge bg-warning">Inactiva</span>
					<?php endif; ?>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="text-muted small">ID</label>
							<p class="mb-0 fw-bold">#<?php echo $brand->id; ?></p>
						</div>
						<div class="col-md-6 mb-3">
							<label class="text-muted small">Slug</label>
							<p class="mb-0"><code><?php echo htmlspecialchars($brand->slug, ENT_QUOTES, 'UTF-8'); ?></code></p>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12 mb-3">
							<label class="text-muted small">Nombre</label>
							<p class="mb-0 fw-bold h4"><?php echo htmlspecialchars($brand->name, ENT_QUOTES, 'UTF-8'); ?></p>
						</div>
					</div>

					<?php if ($brand->image): ?>
						<div class="row">
							<div class="col-md-12 mb-3">
								<label class="text-muted small">Logo/Imagen</label>
								<div>
									<img src="<?php echo htmlspecialchars($brand->image, ENT_QUOTES, 'UTF-8'); ?>" 
										 alt="<?php echo htmlspecialchars($brand->name, ENT_QUOTES, 'UTF-8'); ?>"
										 class="img-thumbnail"
										 style="max-height: 150px;"
										 onerror="this.style.display='none'">
								</div>
								<p class="small text-muted mt-2 mb-0">
									<a href="<?php echo htmlspecialchars($brand->image, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
										<?php echo htmlspecialchars($brand->image, ENT_QUOTES, 'UTF-8'); ?>
									</a>
								</p>
							</div>
						</div>
					<?php endif; ?>

					<hr>

					<div class="row">
						<?php if ($brand->created_at): ?>
							<div class="col-md-6">
								<label class="text-muted small">Fecha de Creación</label>
								<p class="mb-0"><?php echo date('d/m/Y H:i', $brand->created_at); ?></p>
							</div>
						<?php endif; ?>
						<?php if ($brand->updated_at): ?>
							<div class="col-md-6">
								<label class="text-muted small">Última Actualización</label>
								<p class="mb-0"><?php echo date('d/m/Y H:i', $brand->updated_at); ?></p>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<!-- Sidebar Estadísticas -->
		<div class="col-lg-4">
			<!-- Estadística de Productos -->
			<div class="card border-0 shadow-sm mb-3">
				<div class="card-body text-center">
					<i class="fa fa-box fa-3x text-primary mb-3"></i>
					<h2 class="mb-1"><?php echo $products_count; ?></h2>
					<p class="text-muted mb-0">Productos con esta Marca</p>
				</div>
				<?php if ($products_count > 0): ?>
					<div class="card-footer bg-white text-center">
						<a href="<?php echo Uri::create('admin/productos?brand=' . urlencode($brand->name)); ?>" class="text-decoration-none">
							Ver Productos <i class="fa fa-arrow-right ms-1"></i>
						</a>
					</div>
				<?php endif; ?>
			</div>

			<!-- Acciones Rápidas -->
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-light">
					<h6 class="mb-0"><i class="fa fa-bolt me-1"></i> Acciones Rápidas</h6>
				</div>
				<div class="card-body">
					<div class="d-grid gap-2">
						<?php if ($can_edit): ?>
							<a href="<?php echo Uri::create('admin/marcas/edit/' . $brand->id); ?>" 
							   class="btn btn-outline-primary">
								<i class="fa fa-edit me-1"></i> Editar Marca
							</a>
							<a href="<?php echo Uri::create('admin/marcas/toggle_status/' . $brand->id); ?>" 
							   class="btn btn-outline-secondary"
							   onclick="return confirm('¿Cambiar el estado de esta marca?');">
								<i class="fa fa-toggle-on me-1"></i> 
								<?php echo $brand->status == 1 ? 'Desactivar' : 'Activar'; ?>
							</a>
						<?php endif; ?>
						<?php if ($can_delete && $products_count == 0): ?>
							<a href="<?php echo Uri::create('admin/marcas/delete/' . $brand->id); ?>" 
							   class="btn btn-outline-danger"
							   onclick="return confirm('¿Está seguro de eliminar esta marca?');">
								<i class="fa fa-trash me-1"></i> Eliminar
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
