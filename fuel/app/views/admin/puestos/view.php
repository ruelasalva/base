<div class="container-fluid">
	<!-- Header -->
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h2 class="mb-1"><i class="fa fa-user-tag me-2"></i><?php echo htmlspecialchars($position->name, ENT_QUOTES, 'UTF-8'); ?></h2>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-0">
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/puestos'); ?>">Puestos</a></li>
					<li class="breadcrumb-item active">Detalle</li>
				</ol>
			</nav>
		</div>
		<div>
			<?php if (Helper_Permission::can('puestos', 'edit')): ?>
				<a href="<?php echo Uri::create('admin/puestos/edit/' . $position->id); ?>" class="btn btn-warning">
					<i class="fa fa-edit me-2"></i>Editar
				</a>
			<?php endif; ?>
			<a href="<?php echo Uri::create('admin/puestos'); ?>" class="btn btn-secondary">
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
							<p class="mb-0"><code><?php echo htmlspecialchars($position->code ?: '-', ENT_QUOTES, 'UTF-8'); ?></code></p>
						</div>
						<div class="col-md-6">
							<label class="text-muted small">Nombre del Puesto</label>
							<p class="mb-0"><strong><?php echo htmlspecialchars($position->name, ENT_QUOTES, 'UTF-8'); ?></strong></p>
						</div>
						<?php if ($position->description): ?>
							<div class="col-12">
								<label class="text-muted small">Descripción</label>
								<p class="mb-0"><?php echo nl2br(htmlspecialchars($position->description, ENT_QUOTES, 'UTF-8')); ?></p>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<!-- Información Salarial -->
			<div class="card mb-4">
				<div class="card-header bg-success text-white">
					<i class="fa fa-money-bill-wave me-2"></i>Rango Salarial
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label class="text-muted small">Salario Mínimo</label>
							<p class="mb-0">
								<?php if ($position->salary_min): ?>
									<strong class="text-success">$<?php echo number_format($position->salary_min, 2); ?></strong>
								<?php else: ?>
									<span class="text-muted">-</span>
								<?php endif; ?>
							</p>
						</div>
						<div class="col-md-6">
							<label class="text-muted small">Salario Máximo</label>
							<p class="mb-0">
								<?php if ($position->salary_max): ?>
									<strong class="text-success">$<?php echo number_format($position->salary_max, 2); ?></strong>
								<?php else: ?>
									<span class="text-muted">-</span>
								<?php endif; ?>
							</p>
						</div>
						<div class="col-12">
							<label class="text-muted small">Rango Completo</label>
							<div class="alert alert-success mb-0">
								<i class="fa fa-chart-line me-2"></i>
								<strong><?php echo htmlspecialchars($position->get_salary_range(), ENT_QUOTES, 'UTF-8'); ?></strong>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Empleados con este Puesto -->
			<div class="card mb-4">
				<div class="card-header bg-info text-white">
					<i class="fa fa-users me-2"></i>Empleados en este Puesto
					<span class="badge bg-light text-dark ms-2"><?php echo $position->count_active_employees(); ?></span>
				</div>
				<div class="card-body">
					<?php if ($position->count_active_employees() > 0): ?>
						<div class="table-responsive">
							<table class="table table-sm table-hover mb-0">
								<thead class="table-light">
									<tr>
										<th>Código</th>
										<th>Nombre</th>
										<th>Departamento</th>
										<th>Email</th>
										<th>Salario</th>
										<th>Estatus</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($position->employees as $employee): ?>
										<?php if ($employee->deleted_at === null): ?>
											<tr>
												<td><code><?php echo htmlspecialchars($employee->code, ENT_QUOTES, 'UTF-8'); ?></code></td>
												<td>
													<a href="<?php echo Uri::create('admin/empleados/view/' . $employee->id); ?>">
														<?php echo htmlspecialchars($employee->get_full_name(), ENT_QUOTES, 'UTF-8'); ?>
													</a>
												</td>
												<td>
													<?php if ($employee->department): ?>
														<span class="badge bg-secondary">
															<?php echo htmlspecialchars($employee->department->name, ENT_QUOTES, 'UTF-8'); ?>
														</span>
													<?php endif; ?>
												</td>
												<td><small><?php echo htmlspecialchars($employee->email, ENT_QUOTES, 'UTF-8'); ?></small></td>
												<td>
													<?php if ($employee->salary): ?>
														<small>$<?php echo number_format($employee->salary, 2); ?></small>
													<?php else: ?>
														<small class="text-muted">-</small>
													<?php endif; ?>
												</td>
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
							<p class="text-muted mb-0">No hay empleados asignados a este puesto.</p>
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
			<!-- Estatus -->
			<div class="card mb-4">
				<div class="card-header bg-warning text-dark">
					<i class="fa fa-cog me-2"></i>Estado
				</div>
				<div class="card-body">
					<div class="mb-0">
						<label class="text-muted small">Estatus</label>
						<div><?php echo $position->get_status_badge(); ?></div>
					</div>
				</div>
			</div>

			<!-- Estadísticas -->
			<div class="card mb-4">
				<div class="card-header">
					<i class="fa fa-chart-bar me-2"></i>Estadísticas
				</div>
				<div class="card-body">
					<div class="mb-3">
						<label class="text-muted small">Total de Empleados</label>
						<h3 class="mb-0 text-primary"><?php echo $position->count_active_employees(); ?></h3>
					</div>

					<?php if ($position->salary_min && $position->salary_max && $position->count_active_employees() > 0): ?>
						<div class="mb-3">
							<label class="text-muted small">Salario Promedio</label>
							<?php
								$total_salary = 0;
								$count = 0;
								foreach ($position->employees as $emp) {
									if ($emp->deleted_at === null && $emp->salary) {
										$total_salary += $emp->salary;
										$count++;
									}
								}
								$avg_salary = $count > 0 ? $total_salary / $count : 0;
							?>
							<p class="mb-0">
								<?php if ($avg_salary > 0): ?>
									<strong class="text-success">$<?php echo number_format($avg_salary, 2); ?></strong>
								<?php else: ?>
									<span class="text-muted">-</span>
								<?php endif; ?>
							</p>
						</div>
					<?php endif; ?>

					<?php if ($position->salary_min && $position->salary_max): ?>
						<div class="mb-0">
							<label class="text-muted small">Rango Salarial</label>
							<div class="progress" style="height: 25px;">
								<div class="progress-bar bg-success" role="progressbar" style="width: 100%">
									<?php echo htmlspecialchars($position->get_salary_range(), ENT_QUOTES, 'UTF-8'); ?>
								</div>
							</div>
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
						<small><?php echo date('d/m/Y H:i', strtotime($position->created_at)); ?></small>
					</div>
					<div>
						<small class="text-muted">Última actualización:</small><br>
						<small><?php echo date('d/m/Y H:i', strtotime($position->updated_at)); ?></small>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
