<!-- HEADER -->
<div class="row mb-4">
	<div class="col-md-12">
		<div class="d-flex justify-content-between align-items-center">
			<div>
				<h2 class="mb-1"><i class="fas fa-users me-2"></i>Gestión de Usuarios</h2>
				<p class="text-muted mb-0">Administra los usuarios del sistema</p>
			</div>
			<?php if ($can_create): ?>
			<div>
				<a href="<?php echo Uri::create('admin/users/new'); ?>" class="btn btn-primary">
					<i class="fas fa-user-plus me-2"></i>Nuevo Usuario
				</a>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<!-- STATS -->
<div class="row mb-4">
	<div class="col-md-4">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div>
						<h6 class="text-muted mb-2">Total Usuarios</h6>
						<h3 class="mb-0"><?php echo $stats['total_users']; ?></h3>
					</div>
					<div class="text-primary">
						<i class="fas fa-users fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div>
						<h6 class="text-muted mb-2">Usuarios Activos</h6>
						<h3 class="mb-0 text-success"><?php echo $stats['active_users']; ?></h3>
					</div>
					<div class="text-success">
						<i class="fas fa-user-check fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div>
						<h6 class="text-muted mb-2">Administradores</h6>
						<h3 class="mb-0 text-info"><?php echo $stats['admin_users']; ?></h3>
					</div>
					<div class="text-info">
						<i class="fas fa-user-shield fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- LISTA DE USUARIOS -->
<div class="card">
	<div class="card-header">
		<h5 class="mb-0">Usuarios del Sistema</h5>
	</div>
	<div class="card-body">
		<?php if (count($users) > 0): ?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>ID</th>
						<th>Usuario</th>
						<th>Email</th>
						<th>Roles</th>
						<th>Último Acceso</th>
						<th>Creado</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($users as $user): ?>
					<tr>
						<td><strong>#<?php echo $user['id']; ?></strong></td>
						<td>
							<div class="d-flex align-items-center">
								<div class="avatar avatar-sm me-2">
									<i class="fas fa-user-circle fa-2x text-secondary"></i>
								</div>
								<strong><?php echo htmlspecialchars($user['username']); ?></strong>
							</div>
						</td>
						<td><?php echo htmlspecialchars($user['email']); ?></td>
						<td>
							<?php if (!empty($user['roles'])): ?>
								<?php foreach ($user['roles'] as $role): ?>
									<span class="badge bg-primary me-1" title="Nivel <?php echo $role['level']; ?>">
										<?php echo htmlspecialchars($role['display_name']); ?>
									</span>
								<?php endforeach; ?>
							<?php else: ?>
								<span class="badge bg-secondary">Sin rol</span>
							<?php endif; ?>
						</td>
						<td>
							<?php if ($user['last_login'] && $user['last_login'] > 0): ?>
								<small><?php echo date('d/m/Y H:i', $user['last_login']); ?></small>
							<?php else: ?>
								<small class="text-muted">Nunca</small>
							<?php endif; ?>
						</td>
						<td><small><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></small></td>
						<td>
							<a href="<?php echo Uri::create('admin/users/view/' . $user['id']); ?>" class="btn btn-sm btn-info" title="Ver detalle">
								<i class="fas fa-eye"></i>
							</a>
							<?php if ($can_edit): ?>
							<a href="<?php echo Uri::create('admin/users/edit/' . $user['id']); ?>" class="btn btn-sm btn-warning" title="Editar">
								<i class="fas fa-edit"></i>
							</a>
							<?php endif; ?>
							<?php if ($can_delete && $user['id'] != Auth::get('id')): ?>
							<button class="btn btn-sm btn-danger delete-user" data-id="<?php echo $user['id']; ?>" data-name="<?php echo htmlspecialchars($user['username']); ?>" title="Eliminar">
								<i class="fas fa-trash"></i>
							</button>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php else: ?>
		<div class="alert alert-info">
			<i class="fas fa-info-circle me-2"></i>No hay usuarios registrados.
		</div>
		<?php endif; ?>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('.delete-user').forEach(btn => {
		btn.addEventListener('click', function() {
			const userId = this.dataset.id;
			const userName = this.dataset.name;
			
			if (confirm(`¿Estás seguro de eliminar el usuario "${userName}"?\n\nEsta acción marcará el usuario como eliminado.`)) {
				fetch('<?php echo Uri::create('admin/users/delete/'); ?>' + userId, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					}
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						alert('Usuario eliminado correctamente');
						location.reload();
					} else {
						alert('Error: ' + data.message);
					}
				})
				.catch(error => {
					alert('Error al eliminar el usuario: ' + error);
				});
			}
		});
	});
});
</script>
