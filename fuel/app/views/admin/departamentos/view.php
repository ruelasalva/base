<div class="container-fluid">
	<!-- Header -->
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h2 class="mb-1"><i class="fa fa-sitemap me-2"></i><?php echo htmlspecialchars($department->name, ENT_QUOTES, 'UTF-8'); ?></h2>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-0">
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/departamentos'); ?>">Departamentos</a></li>
					<li class="breadcrumb-item active">Detalle</li>
				</ol>
			</nav>
		</div>
		<div>
			<?php if (Helper_Permission::can('departamentos', 'edit')): ?>
				<a href="<?php echo Uri::create('admin/departamentos/edit/' . $department->id); ?>" class="btn btn-warning">
					<i class="fa fa-edit me-2"></i>Editar
				</a>
			<?php endif; ?>
			<a href="<?php echo Uri::create('admin/departamentos'); ?>" class="btn btn-secondary">
				<i class="fa fa-arrow-left me-2"></i>Volver
			</a>
		</div>
	</div>

	<div class="row">
		<!-- Columna Principal -->
		<div class="col-lg-8">
			<!-- Información General -->
			<div class="card mb-4">
				<div class="card-header bg-primary text-white">
					<i class="fa fa-info-circle me-2"></i>Información General
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label class="text-muted small">Código</label>
							<p class="mb-0"><code><?php echo htmlspecialchars($department->code ?: '-', ENT_QUOTES, 'UTF-8'); ?></code></p>
						</div>
						<div class="col-md-6">
							<label class="text-muted small">Nombre</label>
							<p class="mb-0"><strong><?php echo htmlspecialchars($department->name, ENT_QUOTES, 'UTF-8'); ?></strong></p>
						</div>
						<div class="col-12">
							<label class="text-muted small">Jerarquía</label>
							<p class="mb-0">
								<i class="fa fa-sitemap text-muted me-1"></i>
								<?php echo htmlspecialchars($department->get_hierarchy(), ENT_QUOTES, 'UTF-8'); ?>
							</p>
						</div>
						<?php if ($department->description): ?>
							<div class="col-12">
								<label class="text-muted small">Descripción</label>
								<p class="mb-0"><?php echo nl2br(htmlspecialchars($department->description, ENT_QUOTES, 'UTF-8')); ?></p>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<!-- Empleados del Departamento -->
			<div class="card mb-4">
				<div class="card-header bg-info text-white">
					<i class="fa fa-users me-2"></i>Empleados 
					<span class="badge bg-light text-dark ms-2"><?php echo $department->count_active_employees(); ?></span>
				</div>
				<div class="card-body">
					<?php if ($department->count_active_employees() > 0): ?>
						<div class="table-responsive">
							<table class="table table-sm table-hover mb-0">
								<thead class="table-light">
									<tr>
										<th>Código</th>
										<th>Nombre</th>
										<th>Puesto</th>
										<th>Email</th>
										<th>Estatus</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($department->employees as $employee): ?>
										<?php if ($employee->deleted_at === null): ?>
											<tr>
												<td><code><?php echo htmlspecialchars($employee->code, ENT_QUOTES, 'UTF-8'); ?></code></td>
												<td>
													<a href="<?php echo Uri::create('admin/empleados/view/' . $employee->id); ?>">
														<?php echo htmlspecialchars($employee->get_full_name(), ENT_QUOTES, 'UTF-8'); ?>
													</a>
												</td>
												<td>
													<?php if ($employee->position): ?>
														<span class="badge bg-secondary">
															<?php echo htmlspecialchars($employee->position->name, ENT_QUOTES, 'UTF-8'); ?>
														</span>
													<?php endif; ?>
												</td>
												<td><?php echo htmlspecialchars($employee->email, ENT_QUOTES, 'UTF-8'); ?></td>
												<td><?php echo $employee->get_status_badge(); ?></td>
											</tr>
										<?php endif; ?>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					<?php else: ?>
						<div class="text-center py-4">
							<i class="fa fa-users fa-3x text-muted mb-3 d-block"></i>
							<p class="text-muted mb-0">No hay empleados asignados a este departamento.</p>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Historial de Cambios -->
			<?php if (count($logs) > 0): ?>
				<div class="card mb-4">
					<div class="card-header bg-secondary text-white">
						<i class="fa fa-history me-2"></i>Historial de Cambios
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-sm table-hover mb-0">
								<thead class="table-light">
									<tr>
										<th width="120">Fecha</th>
										<th width="100">Acción</th>
										<th>Usuario</th>
										<th>Descripción</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($logs as $log): ?>
										<tr>
											<td><small><?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?></small></td>
											<td>
												<?php if ($log['action'] == 'create'): ?>
													<span class="badge bg-success"><i class="fa fa-plus me-1"></i>Crear</span>
												<?php elseif ($log['action'] == 'edit'): ?>
													<span class="badge bg-warning"><i class="fa fa-edit me-1"></i>Editar</span>
												<?php elseif ($log['action'] == 'delete'): ?>
													<span class="badge bg-danger"><i class="fa fa-trash me-1"></i>Eliminar</span>
												<?php else: ?>
													<span class="badge bg-secondary"><?php echo htmlspecialchars($log['action'], ENT_QUOTES, 'UTF-8'); ?></span>
												<?php endif; ?>
											</td>
											<td><small><?php echo htmlspecialchars($log['username'], ENT_QUOTES, 'UTF-8'); ?></small></td>
											<td><small><?php echo htmlspecialchars($log['description'], ENT_QUOTES, 'UTF-8'); ?></small></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<!-- Columna Lateral -->
		<div class="col-lg-4">
			<!-- Estatus y Información Adicional -->
			<div class="card mb-4">
				<div class="card-header bg-warning text-dark">
					<i class="fa fa-cog me-2"></i>Estado y Configuración
				</div>
				<div class="card-body">
					<div class="mb-3">
						<label class="text-muted small">Estatus</label>
						<div><?php echo $department->get_status_badge(); ?></div>
					</div>

					<?php if ($department->parent): ?>
						<div class="mb-3">
							<label class="text-muted small">Departamento Padre</label>
							<p class="mb-0">
								<a href="<?php echo Uri::create('admin/departamentos/view/' . $department->parent->id); ?>">
									<i class="fa fa-sitemap me-1"></i>
									<?php echo htmlspecialchars($department->parent->name, ENT_QUOTES, 'UTF-8'); ?>
								</a>
							</p>
						</div>
					<?php endif; ?>

					<?php if ($department->manager): ?>
						<div class="mb-3">
							<label class="text-muted small">Responsable</label>
							<p class="mb-0">
								<a href="<?php echo Uri::create('admin/empleados/view/' . $department->manager->id); ?>">
									<i class="fa fa-user me-1"></i>
									<?php echo htmlspecialchars($department->manager->get_full_name(), ENT_QUOTES, 'UTF-8'); ?>
								</a>
							</p>
						</div>
					<?php endif; ?>

					<?php if ($department->children && count($department->children) > 0): ?>
						<div class="mb-3">
							<label class="text-muted small">Departamentos Subordinados</label>
							<ul class="list-unstyled mb-0">
								<?php foreach ($department->children as $child): ?>
									<li class="mb-1">
										<a href="<?php echo Uri::create('admin/departamentos/view/' . $child->id); ?>">
											<i class="fa fa-angle-right me-1"></i>
											<?php echo htmlspecialchars($child->name, ENT_QUOTES, 'UTF-8'); ?>
										</a>
										<span class="badge bg-info ms-1"><?php echo $child->count_active_employees(); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Metadatos -->
			<div class="card">
				<div class="card-header">
					<i class="fa fa-clock me-2"></i>Información del Sistema
				</div>
				<div class="card-body">
					<div class="mb-2">
						<small class="text-muted">Creado:</small><br>
						<small><?php echo date('d/m/Y H:i', strtotime($department->created_at)); ?></small>
					</div>
					<div>
						<small class="text-muted">Última actualización:</small><br>
						<small><?php echo date('d/m/Y H:i', strtotime($department->updated_at)); ?></small>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
