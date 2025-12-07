<div class="container-fluid">
	<!-- Header -->
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h2 class="mb-1"><i class="fa fa-users me-2"></i>Empleados</h2>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-0">
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
					<li class="breadcrumb-item active">Empleados</li>
				</ol>
			</nav>
		</div>
		<?php if (Helper_Permission::can('empleados', 'create')): ?>
		<div>
			<a href="<?php echo Uri::create('admin/empleados/create'); ?>" class="btn btn-primary">
				<i class="fa fa-plus me-2"></i>Nuevo Empleado
			</a>
		</div>
		<?php endif; ?>
	</div>

	<!-- Estadísticas -->
	<div class="row mb-4">
		<div class="col-md-3">
			<div class="card bg-primary text-white">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center">
						<div>
							<h6 class="mb-0">Total Empleados</h6>
							<h3 class="mb-0 mt-2"><?php echo number_format($stats['total']); ?></h3>
						</div>
						<div class="fs-1 opacity-75">
							<i class="fa fa-users"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card bg-success text-white">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center">
						<div>
							<h6 class="mb-0">Activos</h6>
							<h3 class="mb-0 mt-2"><?php echo number_format($stats['active']); ?></h3>
						</div>
						<div class="fs-1 opacity-75">
							<i class="fa fa-check-circle"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card bg-warning text-white">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center">
						<div>
							<h6 class="mb-0">Con Permiso</h6>
							<h3 class="mb-0 mt-2"><?php echo number_format($stats['on_leave']); ?></h3>
						</div>
						<div class="fs-1 opacity-75">
							<i class="fa fa-plane"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card bg-secondary text-white">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center">
						<div>
							<h6 class="mb-0">Inactivos</h6>
							<h3 class="mb-0 mt-2"><?php echo number_format($stats['inactive']); ?></h3>
						</div>
						<div class="fs-1 opacity-75">
							<i class="fa fa-pause-circle"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Filtros -->
	<div class="card mb-4">
		<div class="card-body">
			<form method="GET" action="<?php echo Uri::create('admin/empleados/index'); ?>" class="row g-3">
				<div class="col-md-4">
					<label class="form-label">Buscar</label>
					<input type="text" name="search" class="form-control" placeholder="Nombre, email, RFC, CURP..." value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
				</div>
				<div class="col-md-3">
					<label class="form-label">Estatus</label>
					<select name="status" class="form-select">
						<option value="">Todos</option>
						<option value="active" <?php echo $status == 'active' ? 'selected' : ''; ?>>Activo</option>
						<option value="inactive" <?php echo $status == 'inactive' ? 'selected' : ''; ?>>Inactivo</option>
						<option value="suspended" <?php echo $status == 'suspended' ? 'selected' : ''; ?>>Suspendido</option>
						<option value="on_leave" <?php echo $status == 'on_leave' ? 'selected' : ''; ?>>Con Permiso</option>
						<option value="terminated" <?php echo $status == 'terminated' ? 'selected' : ''; ?>>Terminado</option>
					</select>
				</div>
				<div class="col-md-3">
					<label class="form-label">Departamento</label>
					<select name="department" class="form-select">
						<option value="">Todos</option>
						<?php foreach ($departments as $dept): ?>
							<option value="<?php echo $dept->id; ?>" <?php echo $department_id == $dept->id ? 'selected' : ''; ?>>
								<?php echo htmlspecialchars($dept->name, ENT_QUOTES, 'UTF-8'); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-2 d-flex align-items-end">
					<button type="submit" class="btn btn-primary me-2">
						<i class="fa fa-search"></i> Buscar
					</button>
					<a href="<?php echo Uri::create('admin/empleados/index'); ?>" class="btn btn-secondary">
						<i class="fa fa-times"></i>
					</a>
				</div>
			</form>
		</div>
	</div>

	<!-- Tabla de empleados -->
	<div class="card">
		<div class="card-header">
			<i class="fa fa-list me-2"></i>Listado de Empleados
		</div>
		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="table table-hover table-striped mb-0">
					<thead class="table-light">
						<tr>
							<th width="80">Código</th>
							<th>Nombre</th>
							<th>Email</th>
							<th>Departamento</th>
							<th>Puesto</th>
							<th>Tipo</th>
							<th>Estatus</th>
							<th width="100" class="text-center">Usuario</th>
							<th width="150" class="text-end">Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($employees) > 0): ?>
							<?php foreach ($employees as $employee): ?>
								<tr>
									<td><code><?php echo htmlspecialchars($employee->code ?: '-', ENT_QUOTES, 'UTF-8'); ?></code></td>
									<td>
										<strong><?php echo htmlspecialchars($employee->get_full_name(), ENT_QUOTES, 'UTF-8'); ?></strong><br>
										<small class="text-muted">
											<?php if ($employee->rfc): ?>
												RFC: <?php echo htmlspecialchars($employee->rfc, ENT_QUOTES, 'UTF-8'); ?>
											<?php endif; ?>
										</small>
									</td>
									<td>
										<i class="fa fa-envelope text-muted me-1"></i>
										<?php echo htmlspecialchars($employee->email, ENT_QUOTES, 'UTF-8'); ?>
										<?php if ($employee->phone): ?>
											<br><small class="text-muted">
												<i class="fa fa-phone me-1"></i><?php echo htmlspecialchars($employee->phone, ENT_QUOTES, 'UTF-8'); ?>
											</small>
										<?php endif; ?>
									</td>
									<td>
										<?php if ($employee->department): ?>
											<span class="badge bg-info">
												<i class="fa fa-sitemap me-1"></i>
												<?php echo htmlspecialchars($employee->department->name, ENT_QUOTES, 'UTF-8'); ?>
											</span>
										<?php else: ?>
											<span class="text-muted">-</span>
										<?php endif; ?>
									</td>
									<td>
										<?php if ($employee->position): ?>
											<span class="badge bg-secondary">
												<?php echo htmlspecialchars($employee->position->name, ENT_QUOTES, 'UTF-8'); ?>
											</span>
										<?php else: ?>
											<span class="text-muted">-</span>
										<?php endif; ?>
									</td>
									<td><?php echo $employee->get_employment_type_badge(); ?></td>
									<td><?php echo $employee->get_status_badge(); ?></td>
									<td class="text-center">
										<?php if ($employee->has_system_user()): ?>
											<span class="badge bg-success" title="Tiene usuario del sistema">
												<i class="fa fa-user-check"></i>
											</span>
										<?php else: ?>
											<span class="badge bg-secondary" title="Sin usuario del sistema">
												<i class="fa fa-user-slash"></i>
											</span>
										<?php endif; ?>
									</td>
									<td class="text-end">
										<div class="btn-group btn-group-sm">
											<a href="<?php echo Uri::create('admin/empleados/view/' . $employee->id); ?>" class="btn btn-info" title="Ver">
												<i class="fa fa-eye"></i>
											</a>
											<?php if (Helper_Permission::can('empleados', 'edit')): ?>
												<a href="<?php echo Uri::create('admin/empleados/edit/' . $employee->id); ?>" class="btn btn-warning" title="Editar">
													<i class="fa fa-edit"></i>
												</a>
											<?php endif; ?>
											<?php if (Helper_Permission::can('empleados', 'delete')): ?>
												<button type="button" class="btn btn-danger" onclick="deleteEmployee(<?php echo $employee->id; ?>)" title="Eliminar">
													<i class="fa fa-trash"></i>
												</button>
											<?php endif; ?>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr>
								<td colspan="9" class="text-center py-4 text-muted">
									<i class="fa fa-info-circle me-2"></i>No se encontraron empleados
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php if (isset($pagination) && $pagination->total_pages > 1): ?>
		<div class="card-footer">
			<?php echo $pagination->render(); ?>
		</div>
		<?php endif; ?>
	</div>
</div>

<script>
function deleteEmployee(id) {
	Swal.fire({
		title: '¿Estás seguro?',
		text: 'Esta acción no se puede deshacer',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d33',
		cancelButtonColor: '#3085d6',
		confirmButtonText: 'Sí, eliminar',
		cancelButtonText: 'Cancelar'
	}).then((result) => {
		if (result.isConfirmed) {
			window.location.href = '<?php echo Uri::create("admin/empleados/delete/"); ?>' + id;
		}
	});
}
</script>
