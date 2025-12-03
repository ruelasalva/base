<div class="animated fadeIn">
	<!-- Información del Rol -->
	<div class="row mb-3">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-shield-alt"></i> <?php echo $title; ?>
					<?php if ($role['is_system']): ?>
						<span class="badge badge-warning ml-2">Sistema</span>
					<?php endif; ?>
					<?php if ($role['is_active']): ?>
						<span class="badge badge-success ml-2">Activo</span>
					<?php else: ?>
						<span class="badge badge-secondary ml-2">Inactivo</span>
					<?php endif; ?>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<p><strong>Nombre Interno:</strong> <code><?php echo $role['name']; ?></code></p>
							<p><strong>Nombre para Mostrar:</strong> <?php echo $role['display_name']; ?></p>
							<p><strong>Nivel:</strong> <?php echo $role['level']; ?></p>
						</div>
						<div class="col-md-6">
							<p><strong>Descripción:</strong><br><?php echo $role['description'] ?: '<em class="text-muted">Sin descripción</em>'; ?></p>
							<p><strong>Creado:</strong> <?php echo date('d/m/Y H:i', $role['created_at']); ?></p>
							<?php if ($role['updated_at']): ?>
								<p><strong>Actualizado:</strong> <?php echo date('d/m/Y H:i', $role['updated_at']); ?></p>
							<?php endif; ?>
						</div>
					</div>

					<?php if ($can_edit): ?>
						<hr>
						<a href="<?php echo Uri::create('admin/roles/edit/' . $role['id']); ?>" class="btn btn-primary">
							<i class="fa fa-edit"></i> Editar Rol
						</a>
					<?php endif; ?>
					
					<?php if ($can_permissions): ?>
						<a href="<?php echo Uri::create('admin/roles/permissions/' . $role['id']); ?>" class="btn btn-info">
							<i class="fa fa-key"></i> Gestionar Permisos
						</a>
					<?php endif; ?>
					
					<a href="<?php echo Uri::create('admin/roles'); ?>" class="btn btn-secondary">
						<i class="fa fa-arrow-left"></i> Volver a Lista
					</a>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-chart-bar"></i> Estadísticas
				</div>
				<div class="card-body text-center">
					<div class="row">
						<div class="col-6">
							<h3 class="text-primary mb-0"><?php echo count($users); ?></h3>
							<small class="text-muted">Usuarios</small>
						</div>
						<div class="col-6">
							<h3 class="text-info mb-0"><?php echo count($permissions_by_module); ?></h3>
							<small class="text-muted">Módulos</small>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-12">
							<h3 class="text-success mb-0">
								<?php 
									$total_perms = 0;
									foreach ($permissions_by_module as $perms) {
										$total_perms += count($perms);
									}
									echo $total_perms;
								?>
							</h3>
							<small class="text-muted">Permisos Asignados</small>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Permisos por Módulo -->
	<div class="row">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-key"></i> Permisos Asignados
					<?php if ($can_permissions): ?>
						<a href="<?php echo Uri::create('admin/roles/permissions/' . $role['id']); ?>" class="btn btn-sm btn-info float-right">
							<i class="fa fa-edit"></i> Modificar
						</a>
					<?php endif; ?>
				</div>
				<div class="card-body">
					<?php if (empty($permissions_by_module)): ?>
						<div class="alert alert-warning mb-0">
							<i class="fa fa-exclamation-triangle"></i>
							Este rol no tiene permisos asignados. 
							<?php if ($can_permissions): ?>
								<a href="<?php echo Uri::create('admin/roles/permissions/' . $role['id']); ?>">Asignar permisos ahora</a>
							<?php endif; ?>
						</div>
					<?php else: ?>
						<div class="accordion" id="permissionsAccordion">
							<?php $index = 0; foreach ($permissions_by_module as $module => $perms): ?>
								<div class="card mb-2">
									<div class="card-header p-2" id="heading<?php echo $index; ?>">
										<h6 class="mb-0">
											<button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse<?php echo $index; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
												<i class="fa fa-folder"></i> <?php echo ucfirst($module); ?>
												<span class="badge badge-info float-right"><?php echo count($perms); ?> permisos</span>
											</button>
										</h6>
									</div>
									<div id="collapse<?php echo $index; ?>" class="collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $index; ?>" data-parent="#permissionsAccordion">
										<div class="card-body p-2">
											<div class="row">
												<?php foreach ($perms as $perm): ?>
													<div class="col-md-6">
														<div class="border rounded p-2 mb-2">
															<strong><?php echo $perm['name']; ?></strong>
															<span class="badge badge-secondary badge-sm"><?php echo $perm['action']; ?></span>
															<?php if ($perm['description']): ?>
																<br><small class="text-muted"><?php echo $perm['description']; ?></small>
															<?php endif; ?>
														</div>
													</div>
												<?php endforeach; ?>
											</div>
										</div>
									</div>
								</div>
							<?php $index++; endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Usuarios con este rol -->
		<div class="col-lg-4">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-users"></i> Usuarios Asignados (<?php echo count($users); ?>)
				</div>
				<div class="card-body" style="max-height: 500px; overflow-y: auto;">
					<?php if (empty($users)): ?>
						<p class="text-muted mb-0"><em>No hay usuarios con este rol</em></p>
					<?php else: ?>
						<div class="list-group list-group-flush">
							<?php foreach ($users as $user): ?>
								<div class="list-group-item px-0">
									<div class="d-flex justify-content-between align-items-center">
										<div>
											<strong><?php echo $user['username']; ?></strong>
											<?php if ($user['is_active']): ?>
												<span class="badge badge-success badge-sm">activo</span>
											<?php else: ?>
												<span class="badge badge-secondary badge-sm">inactivo</span>
											<?php endif; ?>
											<br>
											<small class="text-muted"><?php echo $user['email']; ?></small>
										</div>
										<a href="<?php echo Uri::create('admin/users/view/' . $user['user_id']); ?>" class="btn btn-sm btn-outline-primary">
											<i class="fa fa-eye"></i>
										</a>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
