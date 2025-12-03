<!-- HEADER -->
<div class="row mb-4">
	<div class="col-md-12">
		<div class="d-flex justify-content-between align-items-center">
			<div>
				<h2 class="mb-1"><i class="fas fa-user-shield me-2"></i>Roles y Permisos</h2>
				<p class="text-muted mb-0">Gestión de roles de usuario y permisos del sistema</p>
			</div>
			<?php if ($can_create): ?>
			<div>
				<a href="<?php echo Uri::create('admin/roles/new'); ?>" class="btn btn-primary">
					<i class="fas fa-plus me-2"></i>Nuevo Rol
				</a>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<!-- LISTA DE ROLES -->
<div class="row">
	<?php if (count($roles) > 0): ?>
		<?php foreach ($roles as $role): ?>
		<div class="col-md-6 col-lg-4 mb-4">
			<div class="card h-100 <?php echo $role['is_system'] ? 'border-primary' : ''; ?>">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-start mb-3">
						<div>
							<h5 class="card-title mb-1">
								<?php echo htmlspecialchars($role['display_name']); ?>
								<?php if ($role['is_system']): ?>
								<span class="badge bg-primary ms-2">Sistema</span>
								<?php endif; ?>
							</h5>
							<p class="text-muted small mb-0">
								<code><?php echo htmlspecialchars($role['name']); ?></code>
							</p>
						</div>
						<div class="badge bg-secondary">
							Nivel <?php echo $role['level']; ?>
						</div>
					</div>

					<?php if ($role['description']): ?>
					<p class="card-text text-muted small mb-3">
						<?php echo htmlspecialchars($role['description']); ?>
					</p>
					<?php endif; ?>

					<div class="d-flex justify-content-between align-items-center mb-3">
						<div>
							<i class="fas fa-key text-primary me-1"></i>
							<strong><?php echo $role['permission_count']; ?></strong> permisos
						</div>
						<div>
							<i class="fas fa-users text-success me-1"></i>
							<strong><?php echo $role['user_count']; ?></strong> usuarios
						</div>
					</div>

					<div class="btn-group w-100" role="group">
						<a href="<?php echo Uri::create('admin/roles/view/' . $role['id']); ?>" class="btn btn-sm btn-outline-primary" title="Ver detalle">
							<i class="fas fa-eye"></i> Ver
						</a>
						
						<?php if ($can_permissions): ?>
						<a href="<?php echo Uri::create('admin/roles/permissions/' . $role['id']); ?>" class="btn btn-sm btn-outline-success" title="Gestionar permisos">
							<i class="fas fa-key"></i> Permisos
						</a>
						<?php endif; ?>
						
						<?php if ($can_edit && (!$role['is_system'] || $is_super_admin)): ?>
						<a href="<?php echo Uri::create('admin/roles/edit/' . $role['id']); ?>" class="btn btn-sm btn-outline-warning" title="Editar">
							<i class="fas fa-edit"></i>
						</a>
						<?php endif; ?>
						
						<?php if ($can_delete && !$role['is_system']): ?>
						<button class="btn btn-sm btn-outline-danger delete-role" data-id="<?php echo $role['id']; ?>" data-name="<?php echo htmlspecialchars($role['display_name']); ?>" title="Eliminar">
							<i class="fas fa-trash"></i>
						</button>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	<?php else: ?>
		<div class="col-12">
			<div class="alert alert-info">
				<i class="fas fa-info-circle me-2"></i>No hay roles registrados.
				<?php if ($can_create): ?>
				<a href="<?php echo Uri::create('admin/roles/new'); ?>">Crear el primer rol</a>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
</div>

<!-- SCRIPT PARA ELIMINAR -->
<?php if ($can_delete): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('.delete-role').forEach(btn => {
		btn.addEventListener('click', function() {
			const roleId = this.dataset.id;
			const roleName = this.dataset.name;
			
			if (confirm(`¿Estás seguro de eliminar el rol "${roleName}"?\n\nEsta acción no se puede deshacer.`)) {
				fetch('<?php echo Uri::create('admin/roles/delete/'); ?>' + roleId, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					}
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						alert('Rol eliminado correctamente');
						location.reload();
					} else {
						alert('Error: ' + data.message);
					}
				})
				.catch(error => {
					alert('Error al eliminar el rol: ' + error);
				});
			}
		});
	});
});
</script>
<?php endif; ?>
