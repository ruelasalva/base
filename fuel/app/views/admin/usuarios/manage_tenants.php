<div class="card">
	<div class="card-header">
		<h3 class="card-title">
			<i class="fas fa-building"></i> Gestionar Acceso a Backends
		</h3>
		<div class="card-tools">
			<a href="<?php echo Uri::create('admin/usuarios/info/' . $user->id); ?>" class="btn btn-sm btn-secondary">
				<i class="fas fa-arrow-left"></i> Volver
			</a>
		</div>
	</div>

	<div class="card-body">
		<div class="row">
			<div class="col-md-6">
				<h5>Información del Usuario</h5>
				<table class="table table-sm">
					<tr>
						<th width="30%">Usuario:</th>
						<td><?php echo $user->username; ?></td>
					</tr>
					<tr>
						<th>Nombre:</th>
						<td><?php echo $user->first_name . ' ' . $user->last_name; ?></td>
					</tr>
					<tr>
						<th>Email:</th>
						<td><?php echo $user->email; ?></td>
					</tr>
					<tr>
						<th>Grupo:</th>
						<td>
							<?php 
							switch($user->group_id) {
								case 100: echo '<span class="badge badge-danger">Super Admin</span>'; break;
								case 50: echo '<span class="badge badge-warning">Admin</span>'; break;
								case 25: echo '<span class="badge badge-info">Gerente</span>'; break;
								case 10: echo '<span class="badge badge-primary">Usuario</span>'; break;
								default: echo '<span class="badge badge-secondary">ID: ' . $user->group_id . '</span>';
							}
							?>
						</td>
					</tr>
				</table>
			</div>

			<div class="col-md-6">
				<h5>Backends Actuales</h5>
				<?php if (empty($user_tenants)): ?>
					<div class="alert alert-warning">
						<i class="fas fa-exclamation-triangle"></i> 
						Este usuario no tiene acceso a ningún backend.
					</div>
				<?php else: ?>
					<div class="list-group">
						<?php foreach ($user_tenants as $ut): ?>
							<div class="list-group-item">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<strong><?php echo $ut['tenant_name']; ?></strong><br>
										<small class="text-muted"><?php echo $ut['tenant_domain']; ?></small>
									</div>
									<div>
										<?php if ($ut['is_default']): ?>
											<span class="badge badge-primary">Por Defecto</span>
										<?php endif; ?>
										<?php if ($ut['is_active']): ?>
											<span class="badge badge-success">Activo</span>
										<?php else: ?>
											<span class="badge badge-secondary">Inactivo</span>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ($user->group_id == 100): ?>
					<div class="alert alert-info mt-3">
						<i class="fas fa-crown"></i> 
						<strong>Super Admin:</strong> 
						<button type="button" class="btn btn-sm btn-primary" onclick="assignAllTenants(<?php echo $user->id; ?>)">
							<i class="fas fa-check-double"></i> Asignar TODOS los Backends
						</button>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<hr>

		<h5>Configurar Acceso a Backends</h5>
		<form action="<?php echo Uri::create('admin/usuarios/update_user_tenants'); ?>" method="post">
			<input type="hidden" name="user_id" value="<?php echo $user->id; ?>">

			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label><i class="fas fa-building"></i> Backends Disponibles</label>
						<small class="form-text text-muted mb-2">
							Selecciona los backends a los que este usuario puede acceder
						</small>

						<?php 
						$user_tenant_ids = array();
						foreach ($user_tenants as $ut) {
							$user_tenant_ids[] = $ut['tenant_id'];
						}
						?>

						<?php if (empty($all_tenants)): ?>
							<div class="alert alert-warning">
								No hay backends disponibles
							</div>
						<?php else: ?>
							<div class="border rounded p-3">
								<?php foreach ($all_tenants as $tenant): ?>
									<div class="custom-control custom-checkbox mb-2">
										<input type="checkbox" 
											   class="custom-control-input tenant-checkbox" 
											   id="tenant_<?php echo $tenant['id']; ?>" 
											   name="tenants[]" 
											   value="<?php echo $tenant['id']; ?>"
											   <?php echo in_array($tenant['id'], $user_tenant_ids) ? 'checked' : ''; ?>>
										<label class="custom-control-label" for="tenant_<?php echo $tenant['id']; ?>">
											<strong><?php echo $tenant['company_name']; ?></strong><br>
											<small class="text-muted"><?php echo $tenant['domain']; ?></small>
										</label>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="col-md-6">
					<div class="form-group">
						<label>Backend por Defecto</label>
						<small class="form-text text-muted mb-2">
							Backend al que entra automáticamente al hacer login
						</small>

						<select name="default_tenant" class="form-control">
							<?php 
							$default_tenant_id = 1;
							foreach ($user_tenants as $ut) {
								if ($ut['is_default'] == 1) {
									$default_tenant_id = $ut['tenant_id'];
									break;
								}
							}
							?>
							<?php foreach ($all_tenants as $tenant): ?>
								<option value="<?php echo $tenant['id']; ?>" 
										<?php echo ($tenant['id'] == $default_tenant_id) ? 'selected' : ''; ?>>
									<?php echo $tenant['company_name']; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="alert alert-info mt-3">
						<strong><i class="fas fa-info-circle"></i> Nota:</strong><br>
						<ul class="mb-0">
							<li>Selecciona al menos un backend</li>
							<li>El backend por defecto debe estar seleccionado</li>
							<li>Los cambios aplicarán en el próximo login</li>
						</ul>
					</div>
				</div>
			</div>

			<button type="submit" class="btn btn-primary">
				<i class="fas fa-save"></i> Guardar Configuración
			</button>
		</form>
	</div>
</div>

<script>
function assignAllTenants(userId) {
	if (!confirm('¿Asignar este usuario a TODOS los backends disponibles?\n\nEsto es útil para Super Admins que necesitan acceso completo.')) {
		return;
	}

	const defaultTenant = $('select[name="default_tenant"]').val() || 1;

	$.ajax({
		url: '<?php echo Uri::create('admin/usuarios/assign_all_tenants'); ?>',
		type: 'POST',
		data: {
			user_id: userId,
			default_tenant: defaultTenant
		},
		success: function(response) {
			if (response.success) {
				alert(response.message);
				location.reload();
			} else {
				alert(response.error || 'Error al asignar backends');
			}
		},
		error: function() {
			alert('Error en la comunicación con el servidor');
		}
	});
}

// Validar que el backend por defecto esté seleccionado
$('form').on('submit', function(e) {
	const defaultTenant = $('select[name="default_tenant"]').val();
	const selectedTenants = $('input[name="tenants[]"]:checked').map(function() {
		return $(this).val();
	}).get();

	if (selectedTenants.length === 0) {
		alert('Debes seleccionar al menos un backend');
		e.preventDefault();
		return false;
	}

	if (!selectedTenants.includes(defaultTenant)) {
		alert('El backend por defecto debe estar seleccionado en la lista');
		e.preventDefault();
		return false;
	}
});
</script>
