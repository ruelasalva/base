<!-- HEADER -->
<div class="row mb-4">
	<div class="col-md-12">
		<div class="d-flex justify-content-between align-items-center">
			<div>
				<h2 class="mb-1">
					<i class="fas fa-user me-2"></i>
					<?php echo htmlspecialchars($user['username']); ?>
					<?php if (!$user['is_active']): ?>
					<span class="badge bg-secondary">Inactivo</span>
					<?php endif; ?>
				</h2>
				<p class="text-muted mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
			</div>
			<div>
				<?php if ($can_edit): ?>
				<a href="<?php echo Uri::create('admin/users/manage_tenants/' . $user['id']); ?>" class="btn btn-info">
					<i class="fas fa-building me-2"></i>Gestionar Backends
				</a>
				<a href="<?php echo Uri::create('admin/users/edit/' . $user['id']); ?>" class="btn btn-warning">
					<i class="fas fa-edit me-2"></i>Editar
				</a>
				<?php endif; ?>
				<a href="<?php echo Uri::create('admin/users'); ?>" class="btn btn-secondary">
					<i class="fas fa-arrow-left me-2"></i>Volver
				</a>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<!-- INFORMACIÓN DEL USUARIO -->
	<div class="col-md-4">
		<div class="card mb-4">
			<div class="card-header bg-primary text-white">
				<h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información</h5>
			</div>
			<div class="card-body">
				<div class="mb-3">
					<small class="text-muted">Usuario</small>
					<p class="mb-0"><strong><?php echo htmlspecialchars($user['username']); ?></strong></p>
				</div>
				<div class="mb-3">
					<small class="text-muted">Email</small>
					<p class="mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
				</div>
				<div class="mb-3">
					<small class="text-muted">Nombre Completo</small>
					<p class="mb-0">
						<?php 
						$full_name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
						echo $full_name ? htmlspecialchars($full_name) : '<span class="text-muted">No especificado</span>';
						?>
					</p>
				</div>
				<div class="mb-3">
					<small class="text-muted">Estado</small>
					<p class="mb-0">
						<?php if ($user['is_active']): ?>
						<span class="badge bg-success">Activo</span>
						<?php else: ?>
						<span class="badge bg-secondary">Inactivo</span>
						<?php endif; ?>
					</p>
				</div>
				<div class="mb-3">
					<small class="text-muted">Último Acceso</small>
					<p class="mb-0">
						<?php if ($user['last_login'] && $user['last_login'] > 0): ?>
						<?php echo date('d/m/Y H:i', $user['last_login']); ?>
						<?php else: ?>
						<span class="text-muted">Nunca</span>
						<?php endif; ?>
					</p>
				</div>
				<div class="mb-3">
					<small class="text-muted">Creado</small>
					<p class="mb-0"><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></p>
				</div>
			</div>
		</div>

		<!-- ROLES -->
		<div class="card mb-4">
			<div class="card-header bg-success text-white">
				<h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Roles Asignados</h5>
			</div>
			<div class="card-body">
				<?php if (!empty($roles)): ?>
					<?php foreach ($roles as $role): ?>
					<div class="mb-3 p-2 border rounded">
						<h6 class="mb-1">
							<?php echo htmlspecialchars($role['display_name']); ?>
							<?php if ($role['is_system']): ?>
							<span class="badge bg-primary">Sistema</span>
							<?php endif; ?>
						</h6>
						<small class="text-muted d-block"><?php echo htmlspecialchars($role['description']); ?></small>
						<small class="text-muted">Nivel: <strong><?php echo $role['level']; ?></strong></small>
					</div>
					<?php endforeach; ?>
				<?php else: ?>
					<p class="text-muted mb-0">
						<i class="fas fa-info-circle me-1"></i>Este usuario no tiene roles asignados
					</p>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- PERMISOS DEL USUARIO -->
	<div class="col-md-8">
		<div class="card">
			<div class="card-header bg-info text-white">
				<h5 class="mb-0"><i class="fas fa-key me-2"></i>Permisos Efectivos</h5>
				<small>Permisos otorgados a través de los roles asignados</small>
			</div>
			<div class="card-body">
				<?php if (!empty($permissions_by_module)): ?>
					<?php
					$module_names = [
						'dashboard' => 'Dashboard',
						'users' => 'Usuarios',
						'roles' => 'Roles',
						'config' => 'Configuración',
						'inventory' => 'Inventario',
						'crm' => 'CRM - Clientes',
						'sales' => 'Ventas',
						'products' => 'Productos',
						'customers' => 'Clientes',
						'reports' => 'Reportes'
					];

					$module_icons = [
						'dashboard' => 'fa-home',
						'users' => 'fa-users',
						'roles' => 'fa-user-shield',
						'config' => 'fa-cog',
						'inventory' => 'fa-boxes',
						'crm' => 'fa-user-friends',
						'sales' => 'fa-shopping-cart',
						'products' => 'fa-box',
						'customers' => 'fa-address-book',
						'reports' => 'fa-chart-bar'
					];

					foreach ($permissions_by_module as $module => $perms):
						$module_name = $module_names[$module] ?? ucfirst($module);
						$module_icon = $module_icons[$module] ?? 'fa-folder';
					?>
					<div class="mb-4">
						<div class="d-flex align-items-center mb-2 pb-2 border-bottom">
							<i class="fas <?php echo $module_icon; ?> text-primary me-2"></i>
							<h6 class="mb-0"><?php echo $module_name; ?></h6>
							<span class="badge bg-secondary ms-auto"><?php echo count($perms); ?></span>
						</div>
						<div class="row">
							<?php foreach ($perms as $perm): ?>
							<div class="col-md-6 mb-2">
								<div class="d-flex align-items-center">
									<i class="fas fa-check-circle text-success me-2"></i>
									<div>
										<strong><?php echo htmlspecialchars($perm['name']); ?></strong>
										<br><small class="text-muted"><?php echo htmlspecialchars($perm['action']); ?></small>
									</div>
								</div>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="alert alert-warning mb-0">
						<i class="fas fa-exclamation-triangle me-2"></i>
						Este usuario no tiene permisos asignados. Asigna roles para otorgar permisos.
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
