<div class="container-fluid">
	<!-- Header -->
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h2 class="mb-1"><i class="fa fa-user me-2"></i><?php echo htmlspecialchars($employee->get_full_name(), ENT_QUOTES, 'UTF-8'); ?></h2>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-0">
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/empleados'); ?>">Empleados</a></li>
					<li class="breadcrumb-item active">Detalle</li>
				</ol>
			</nav>
		</div>
		<div>
			<?php if (Helper_Permission::can('empleados', 'edit')): ?>
				<a href="<?php echo Uri::create('admin/empleados/edit/' . $employee->id); ?>" class="btn btn-warning">
					<i class="fa fa-edit me-2"></i>Editar
				</a>
			<?php endif; ?>
			<a href="<?php echo Uri::create('admin/empleados'); ?>" class="btn btn-secondary">
				<i class="fa fa-arrow-left me-2"></i>Volver
			</a>
		</div>
	</div>

	<div class="row">
		<!-- Columna Principal -->
		<div class="col-lg-8">
			<!-- Información Personal -->
			<div class="card mb-4">
				<div class="card-header bg-primary text-white">
					<i class="fa fa-user me-2"></i>Información Personal
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label class="text-muted small">Código</label>
							<p class="mb-0"><code><?php echo htmlspecialchars($employee->code ?: '-', ENT_QUOTES, 'UTF-8'); ?></code></p>
						</div>
						<div class="col-md-6">
							<label class="text-muted small">Género</label>
							<p class="mb-0">
								<?php if ($employee->gender == 'M'): ?>
									<i class="fa fa-mars text-primary"></i> Masculino
								<?php elseif ($employee->gender == 'F'): ?>
									<i class="fa fa-venus text-danger"></i> Femenino
								<?php else: ?>
									-
								<?php endif; ?>
							</p>
						</div>

						<div class="col-md-4">
							<label class="text-muted small">Fecha de Nacimiento</label>
							<p class="mb-0"><?php echo $employee->birthdate ? date('d/m/Y', strtotime($employee->birthdate)) : '-'; ?></p>
							<?php if ($employee->get_age()): ?>
								<small class="text-muted"><?php echo $employee->get_age(); ?> años</small>
							<?php endif; ?>
						</div>
						<div class="col-md-4">
							<label class="text-muted small">CURP</label>
							<p class="mb-0"><code><?php echo htmlspecialchars($employee->curp ?: '-', ENT_QUOTES, 'UTF-8'); ?></code></p>
						</div>
						<div class="col-md-4">
							<label class="text-muted small">RFC</label>
							<p class="mb-0"><code><?php echo htmlspecialchars($employee->rfc ?: '-', ENT_QUOTES, 'UTF-8'); ?></code></p>
						</div>

						<div class="col-md-4">
							<label class="text-muted small">NSS</label>
							<p class="mb-0"><code><?php echo htmlspecialchars($employee->nss ?: '-', ENT_QUOTES, 'UTF-8'); ?></code></p>
						</div>
						<div class="col-md-4">
							<label class="text-muted small">Email</label>
							<p class="mb-0">
								<i class="fa fa-envelope text-muted me-1"></i>
								<a href="mailto:<?php echo $employee->email; ?>"><?php echo htmlspecialchars($employee->email, ENT_QUOTES, 'UTF-8'); ?></a>
							</p>
						</div>
						<div class="col-md-4">
							<label class="text-muted small">Teléfono</label>
							<p class="mb-0">
								<i class="fa fa-phone text-muted me-1"></i>
								<?php echo htmlspecialchars($employee->phone ?: '-', ENT_QUOTES, 'UTF-8'); ?>
							</p>
						</div>

						<?php if ($employee->phone_emergency || $employee->emergency_contact_name): ?>
						<div class="col-12">
							<div class="alert alert-warning mb-0">
								<strong><i class="fa fa-exclamation-triangle me-2"></i>Contacto de Emergencia:</strong><br>
								<?php if ($employee->emergency_contact_name): ?>
									<?php echo htmlspecialchars($employee->emergency_contact_name, ENT_QUOTES, 'UTF-8'); ?>
								<?php endif; ?>
								<?php if ($employee->phone_emergency): ?>
									- Tel: <?php echo htmlspecialchars($employee->phone_emergency, ENT_QUOTES, 'UTF-8'); ?>
								<?php endif; ?>
							</div>
						</div>
						<?php endif; ?>

						<?php if ($employee->address || $employee->city || $employee->state): ?>
						<div class="col-12">
							<label class="text-muted small">Dirección</label>
							<p class="mb-0">
								<i class="fa fa-map-marker-alt text-muted me-1"></i>
								<?php echo htmlspecialchars($employee->address ?: '', ENT_QUOTES, 'UTF-8'); ?>
								<?php if ($employee->city): ?>
									<br><?php echo htmlspecialchars($employee->city, ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars($employee->state ?: '', ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars($employee->postal_code ?: '', ENT_QUOTES, 'UTF-8'); ?>
								<?php endif; ?>
								<?php if ($employee->country && $employee->country != 'México'): ?>
									<br><?php echo htmlspecialchars($employee->country, ENT_QUOTES, 'UTF-8'); ?>
								<?php endif; ?>
							</p>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<!-- Información Financiera -->
			<div class="card mb-4">
				<div class="card-header bg-success text-white">
					<i class="fa fa-money-bill me-2"></i>Información Financiera
				</div>
				<div class="card-body">
					<div class="row g-3">
						<div class="col-md-6">
							<label class="text-muted small">Salario</label>
							<p class="mb-0 fs-5 text-success">
								<strong><?php echo $employee->get_formatted_salary(); ?></strong>
							</p>
						</div>
						<?php if ($employee->bank_name || $employee->bank_account): ?>
						<div class="col-md-6">
							<label class="text-muted small">Información Bancaria</label>
							<p class="mb-0">
								<?php echo htmlspecialchars($employee->bank_name ?: '-', ENT_QUOTES, 'UTF-8'); ?><br>
								<small><?php echo htmlspecialchars($employee->bank_account ?: '-', ENT_QUOTES, 'UTF-8'); ?></small>
							</p>
						</div>
						<?php endif; ?>
						<?php if ($employee->clabe): ?>
						<div class="col-12">
							<label class="text-muted small">CLABE</label>
							<p class="mb-0"><code><?php echo htmlspecialchars($employee->clabe, ENT_QUOTES, 'UTF-8'); ?></code></p>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<?php if (!empty($logs)): ?>
			<!-- Historial de Cambios -->
			<div class="card mb-4">
				<div class="card-header">
					<i class="fa fa-history me-2"></i>Historial de Cambios
				</div>
				<div class="card-body p-0">
					<div class="table-responsive">
						<table class="table table-sm table-striped mb-0">
							<thead>
								<tr>
									<th>Fecha</th>
									<th>Acción</th>
									<th>Descripción</th>
									<th>Usuario</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($logs as $log): ?>
									<tr>
										<td class="text-nowrap">
											<small><?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?></small>
										</td>
										<td>
											<?php if ($log['action'] == 'create'): ?>
												<span class="badge bg-success">Creado</span>
											<?php elseif ($log['action'] == 'edit'): ?>
												<span class="badge bg-warning">Editado</span>
											<?php elseif ($log['action'] == 'delete'): ?>
												<span class="badge bg-danger">Eliminado</span>
											<?php else: ?>
												<span class="badge bg-secondary"><?php echo ucfirst($log['action']); ?></span>
											<?php endif; ?>
										</td>
										<td><small><?php echo htmlspecialchars($log['description'], ENT_QUOTES, 'UTF-8'); ?></small></td>
										<td><small><?php echo htmlspecialchars($log['user_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></small></td>
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
			<!-- Información Laboral -->
			<div class="card mb-4">
				<div class="card-header bg-warning text-dark">
					<i class="fa fa-briefcase me-2"></i>Información Laboral
				</div>
				<div class="card-body">
					<div class="mb-3">
						<label class="text-muted small">Departamento</label>
						<p class="mb-0">
							<?php if ($employee->department): ?>
								<span class="badge bg-info">
									<i class="fa fa-sitemap me-1"></i>
									<?php echo htmlspecialchars($employee->department->name, ENT_QUOTES, 'UTF-8'); ?>
								</span>
							<?php else: ?>
								<span class="text-muted">-</span>
							<?php endif; ?>
						</p>
					</div>

					<div class="mb-3">
						<label class="text-muted small">Puesto</label>
						<p class="mb-0">
							<?php if ($employee->position): ?>
								<span class="badge bg-secondary">
									<?php echo htmlspecialchars($employee->position->name, ENT_QUOTES, 'UTF-8'); ?>
								</span>
							<?php else: ?>
								<span class="text-muted">-</span>
							<?php endif; ?>
						</p>
					</div>

					<div class="mb-3">
						<label class="text-muted small">Fecha de Contratación</label>
						<p class="mb-0">
							<?php echo date('d/m/Y', strtotime($employee->hire_date)); ?>
							<br><small class="text-muted">Antigüedad: <?php echo $employee->get_seniority_years(); ?> años</small>
						</p>
					</div>

					<?php if ($employee->termination_date): ?>
					<div class="mb-3">
						<label class="text-muted small">Fecha de Baja</label>
						<p class="mb-0 text-danger">
							<?php echo date('d/m/Y', strtotime($employee->termination_date)); ?>
						</p>
					</div>
					<?php endif; ?>

					<div class="mb-3">
						<label class="text-muted small">Tipo de Empleo</label>
						<p class="mb-0"><?php echo $employee->get_employment_type_badge(); ?></p>
					</div>

					<div class="mb-3">
						<label class="text-muted small">Estatus</label>
						<p class="mb-0"><?php echo $employee->get_status_badge(); ?></p>
					</div>

					<div class="mb-3">
						<label class="text-muted small">Usuario del Sistema</label>
						<p class="mb-0">
							<?php if ($employee->has_system_user()): ?>
								<span class="badge bg-success">
									<i class="fa fa-user-check me-1"></i>Tiene acceso al sistema
								</span>
							<?php else: ?>
								<span class="badge bg-secondary">
									<i class="fa fa-user-slash me-1"></i>Sin acceso al sistema
								</span>
							<?php endif; ?>
						</p>
					</div>
				</div>
			</div>

			<?php if ($employee->notes): ?>
			<!-- Notas -->
			<div class="card mb-4">
				<div class="card-header">
					<i class="fa fa-sticky-note me-2"></i>Notas
				</div>
				<div class="card-body">
					<p class="mb-0"><?php echo nl2br(htmlspecialchars($employee->notes, ENT_QUOTES, 'UTF-8')); ?></p>
				</div>
			</div>
			<?php endif; ?>

			<!-- Metadata -->
			<div class="card">
				<div class="card-body">
					<small class="text-muted">
						<i class="fa fa-calendar me-1"></i>Creado: <?php echo date('d/m/Y H:i', strtotime($employee->created_at)); ?><br>
						<?php if ($employee->updated_at): ?>
							<i class="fa fa-edit me-1"></i>Actualizado: <?php echo date('d/m/Y H:i', strtotime($employee->updated_at)); ?>
						<?php endif; ?>
					</small>
				</div>
			</div>
		</div>
	</div>
</div>
