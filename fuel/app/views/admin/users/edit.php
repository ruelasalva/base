<!-- HEADER -->
<div class="row mb-4">
	<div class="col-md-12">
		<div class="d-flex justify-content-between align-items-center">
			<div>
				<h2 class="mb-1"><i class="fas fa-user-edit me-2"></i>Editar Usuario</h2>
				<p class="text-muted mb-0">Modifica la información del usuario: <strong><?php echo htmlspecialchars($user['username']); ?></strong></p>
			</div>
			<div>
				<a href="<?php echo Uri::create('admin/users'); ?>" class="btn btn-secondary">
					<i class="fas fa-arrow-left me-2"></i>Volver
				</a>
			</div>
		</div>
	</div>
</div>

<!-- FORMULARIO -->
<form method="POST" action="<?php echo Uri::current(); ?>">
	<div class="row">
		<!-- DATOS BÁSICOS -->
		<div class="col-md-8">
			<div class="card mb-4">
				<div class="card-header bg-primary text-white">
					<h5 class="mb-0"><i class="fas fa-user me-2"></i>Información del Usuario</h5>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="username" class="form-label">Usuario <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
						</div>
						<div class="col-md-6 mb-3">
							<label for="email" class="form-label">Email <span class="text-danger">*</span></label>
							<input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="first_name" class="form-label">Nombre</label>
							<input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
						</div>
						<div class="col-md-6 mb-3">
							<label for="last_name" class="form-label">Apellido</label>
							<input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="password" class="form-label">Nueva Contraseña</label>
							<input type="password" class="form-control" id="password" name="password">
							<small class="text-muted">Dejar en blanco para mantener la contraseña actual</small>
						</div>
						<div class="col-md-6 mb-3">
							<label for="password_confirm" class="form-label">Confirmar Nueva Contraseña</label>
							<input type="password" class="form-control" id="password_confirm" name="password_confirm">
						</div>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<div class="form-check form-switch">
								<input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo $user['is_active'] ? 'checked' : ''; ?>>
								<label class="form-check-label" for="is_active">
									<strong>Usuario Activo</strong>
									<br><small class="text-muted">Desmarcar para desactivar el acceso del usuario</small>
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ROLES -->
		<div class="col-md-4">
			<div class="card mb-4">
				<div class="card-header bg-success text-white">
					<h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Roles del Usuario</h5>
				</div>
				<div class="card-body">
					<p class="text-muted small mb-3">Selecciona uno o más roles para este usuario</p>
					
					<?php foreach ($roles as $role): ?>
					<div class="form-check mb-2">
						<input 
							class="form-check-input" 
							type="checkbox" 
							name="roles[]" 
							value="<?php echo $role['id']; ?>" 
							id="role-<?php echo $role['id']; ?>"
							<?php echo in_array($role['id'], $user_role_ids) ? 'checked' : ''; ?>
						>
						<label class="form-check-label" for="role-<?php echo $role['id']; ?>">
							<strong><?php echo htmlspecialchars($role['display_name']); ?></strong>
							<?php if ($role['is_system']): ?>
							<span class="badge bg-primary ms-1">Sistema</span>
							<?php endif; ?>
							<br>
							<small class="text-muted"><?php echo htmlspecialchars($role['description']); ?></small>
						</label>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- BOTONES -->
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<div class="d-flex justify-content-between">
						<a href="<?php echo Uri::create('admin/users'); ?>" class="btn btn-secondary">
							<i class="fas fa-times me-2"></i>Cancelar
						</a>
						<button type="submit" class="btn btn-primary">
							<i class="fas fa-save me-2"></i>Actualizar Usuario
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<script>
// Validar que las contraseñas coincidan (solo si se proporciona una nueva)
document.querySelector('form').addEventListener('submit', function(e) {
	const password = document.getElementById('password').value;
	const confirm = document.getElementById('password_confirm').value;
	
	if (password) {
		if (password !== confirm) {
			e.preventDefault();
			alert('Las contraseñas no coinciden');
			return false;
		}
		
		if (password.length < 6) {
			e.preventDefault();
			alert('La contraseña debe tener al menos 6 caracteres');
			return false;
		}
	}
});
</script>
