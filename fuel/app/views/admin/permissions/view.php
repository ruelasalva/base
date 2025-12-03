<div class="animated fadeIn">
	<!-- Información del Permiso -->
	<div class="row mb-3">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-key"></i> <?php echo $title; ?>
					<?php if ($permission['is_active']): ?>
						<span class="badge badge-success ml-2">Activo</span>
					<?php else: ?>
						<span class="badge badge-secondary ml-2">Inactivo</span>
					<?php endif; ?>
				</div>
				<div class="card-body">
					<div class="row mb-3">
						<div class="col-md-6">
							<p><strong>Módulo:</strong> <code><?php echo $permission['module']; ?></code></p>
							<p><strong>Acción:</strong> <code><?php echo $permission['action']; ?></code></p>
							<p><strong>Identificador:</strong> <code><?php echo $permission['module'] . '.' . $permission['action']; ?></code></p>
						</div>
						<div class="col-md-6">
							<p><strong>Nombre:</strong> <?php echo $permission['name']; ?></p>
							<p><strong>Descripción:</strong><br><?php echo $permission['description'] ?: '<em class="text-muted">Sin descripción</em>'; ?></p>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<p class="mb-0">
								<small class="text-muted">
									<strong>Creado:</strong> <?php echo date('d/m/Y H:i', $permission['created_at']); ?>
									<?php if ($permission['updated_at']): ?>
										| <strong>Actualizado:</strong> <?php echo date('d/m/Y H:i', $permission['updated_at']); ?>
									<?php endif; ?>
								</small>
							</p>
						</div>
					</div>

					<hr>

					<?php if ($can_edit): ?>
						<a href="<?php echo Uri::create('admin/permissions/edit/' . $permission['id']); ?>" class="btn btn-primary">
							<i class="fa fa-edit"></i> Editar
						</a>
					<?php endif; ?>
					
					<a href="<?php echo Uri::create('admin/permissions'); ?>" class="btn btn-secondary">
						<i class="fa fa-arrow-left"></i> Volver a Lista
					</a>

					<?php if ($can_delete): ?>
						<button onclick="deletePermission(<?php echo $permission['id']; ?>)" class="btn btn-danger float-right">
							<i class="fa fa-trash"></i> Eliminar
						</button>
					<?php endif; ?>
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
							<h3 class="text-primary mb-0"><?php echo count($roles); ?></h3>
							<small class="text-muted">Roles</small>
						</div>
						<div class="col-6">
							<h3 class="text-info mb-0"><?php echo $user_count; ?></h3>
							<small class="text-muted">Usuarios Afectados</small>
						</div>
					</div>
				</div>
			</div>

			<div class="card">
				<div class="card-header">
					<i class="fa fa-info-circle"></i> Uso en el Código
				</div>
				<div class="card-body">
					<p><strong>Helper de Permisos:</strong></p>
					<pre class="bg-light p-2 rounded"><code>Helper_Permission::can(
  '<?php echo $permission['module']; ?>', 
  '<?php echo $permission['action']; ?>'
)</code></pre>
					
					<p class="mt-3"><strong>En Controlador:</strong></p>
					<pre class="bg-light p-2 rounded mb-0"><code>if (!Helper_Permission::can('<?php echo $permission['module']; ?>', '<?php echo $permission['action']; ?>')) {
  Response::redirect('admin');
}</code></pre>
				</div>
			</div>
		</div>
	</div>

	<!-- Roles que tienen este permiso -->
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header">
					<i class="fa fa-shield-alt"></i> Roles con este Permiso (<?php echo count($roles); ?>)
				</div>
				<div class="card-body">
					<?php if (empty($roles)): ?>
						<div class="alert alert-warning mb-0">
							<i class="fa fa-exclamation-triangle"></i>
							Este permiso no está asignado a ningún rol. 
							<a href="<?php echo Uri::create('admin/roles'); ?>">Ver roles disponibles</a>
						</div>
					<?php else: ?>
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th width="5%"></th>
										<th width="25%">Rol</th>
										<th width="40%">Descripción</th>
										<th width="10%" class="text-center">Nivel</th>
										<th width="10%" class="text-center">Estado</th>
										<th width="10%" class="text-right">Acciones</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($roles as $role): ?>
										<tr>
											<td class="text-center">
												<?php if ($role['is_system']): ?>
													<i class="fa fa-lock text-warning" title="Rol del sistema"></i>
												<?php else: ?>
													<i class="fa fa-shield-alt text-primary"></i>
												<?php endif; ?>
											</td>
											<td>
												<strong><?php echo $role['display_name']; ?></strong>
												<?php if ($role['is_system']): ?>
													<span class="badge badge-warning badge-sm">Sistema</span>
												<?php endif; ?>
												<br>
												<small class="text-muted"><code><?php echo $role['name']; ?></code></small>
											</td>
											<td>
												<small><?php echo $role['description'] ?: '<em class="text-muted">Sin descripción</em>'; ?></small>
											</td>
											<td class="text-center">
												<span class="badge badge-info"><?php echo $role['level']; ?></span>
											</td>
											<td class="text-center">
												<?php if ($role['is_active']): ?>
													<span class="badge badge-success">Activo</span>
												<?php else: ?>
													<span class="badge badge-secondary">Inactivo</span>
												<?php endif; ?>
											</td>
											<td class="text-right">
												<div class="btn-group btn-group-sm">
													<a href="<?php echo Uri::create('admin/roles/view/' . $role['id']); ?>" class="btn btn-info" title="Ver rol">
														<i class="fa fa-eye"></i>
													</a>
													<a href="<?php echo Uri::create('admin/roles/permissions/' . $role['id']); ?>" class="btn btn-primary" title="Gestionar permisos">
														<i class="fa fa-key"></i>
													</a>
												</div>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Información adicional -->
	<?php if ($user_count > 0): ?>
		<div class="row">
			<div class="col-lg-12">
				<div class="card border-info">
					<div class="card-body">
						<i class="fa fa-info-circle text-info"></i>
						Este permiso afecta a <strong><?php echo $user_count; ?> usuario<?php echo $user_count != 1 ? 's' : ''; ?></strong> 
						en el tenant actual a través de 
						<strong><?php echo count($roles); ?> rol<?php echo count($roles) != 1 ? 'es' : ''; ?></strong>.
						
						<?php if ($can_delete): ?>
							Si eliminas este permiso, todos estos usuarios perderán esta capacidad.
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>

<script>
function deletePermission(id) {
	var userCount = <?php echo $user_count; ?>;
	var roleCount = <?php echo count($roles); ?>;
	
	var message = '¿Estás seguro de eliminar este permiso?\n\n';
	
	if (roleCount > 0) {
		message += 'Este permiso está asignado a ' + roleCount + ' rol(es) ';
		if (userCount > 0) {
			message += 'y afecta a ' + userCount + ' usuario(s).\n\n';
		}
		message += 'Esta acción no se puede deshacer.';
	} else {
		message += 'Esta acción no se puede deshacer.';
	}
	
	if (confirm(message)) {
		fetch('<?php echo Uri::create("admin/permissions/delete"); ?>/' + id, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-Requested-With': 'XMLHttpRequest'
			}
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				alert(data.message);
				window.location.href = '<?php echo Uri::create("admin/permissions"); ?>';
			} else {
				alert('Error: ' + data.message);
			}
		})
		.catch(error => {
			console.error('Error:', error);
			alert('Error al eliminar el permiso');
		});
	}
}
</script>
