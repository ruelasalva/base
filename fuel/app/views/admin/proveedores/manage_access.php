<div class="card">
	<div class="card-header">
		<h3 class="card-title">
			<i class="fas fa-key"></i> Gestionar Acceso al Portal
		</h3>
		<div class="card-tools">
			<a href="<?php echo Uri::create('admin/proveedores/info/' . $provider->id); ?>" class="btn btn-sm btn-secondary">
				<i class="fas fa-arrow-left"></i> Volver
			</a>
		</div>
	</div>

	<div class="card-body">
		<div class="row">
			<div class="col-md-6">
				<h5>Información del Proveedor</h5>
				<table class="table table-sm">
					<tr>
						<th width="30%">Código:</th>
						<td><?php echo $provider->code; ?></td>
					</tr>
					<tr>
						<th>Razón Social:</th>
						<td><?php echo $provider->company_name; ?></td>
					</tr>
					<tr>
						<th>Email:</th>
						<td><?php echo $provider->email; ?></td>
					</tr>
					<tr>
						<th>RFC:</th>
						<td><?php echo $provider->tax_id; ?></td>
					</tr>
				</table>
			</div>

			<div class="col-md-6">
				<?php if ($user): ?>
					<h5>Usuario Actual</h5>
					<div class="alert alert-info">
						<i class="fas fa-user-check"></i> 
						<strong>Usuario:</strong> <?php echo $user->username; ?><br>
						<strong>Email:</strong> <?php echo $user->email; ?><br>
						<strong>Estado:</strong> 
						<?php if ($user->is_active): ?>
							<span class="badge badge-success">Activo</span>
						<?php else: ?>
							<span class="badge badge-danger">Inactivo</span>
						<?php endif; ?>
					</div>

					<button type="button" class="btn btn-danger btn-sm" onclick="deleteUser(<?php echo $user->id; ?>, <?php echo $provider->id; ?>)">
						<i class="fas fa-trash"></i> Eliminar Usuario
					</button>
				<?php else: ?>
					<h5>Sin Usuario Asignado</h5>
					<div class="alert alert-warning">
						<i class="fas fa-exclamation-triangle"></i> 
						Este proveedor no tiene acceso al portal aún.
					</div>
				<?php endif; ?>
			</div>
		</div>

		<hr>

		<?php if (!$user): ?>
			<!-- FORMULARIO PARA CREAR USUARIO -->
			<h5>Crear Usuario de Acceso</h5>
			<form action="<?php echo Uri::create('admin/proveedores/create_user'); ?>" method="post">
				<input type="hidden" name="provider_id" value="<?php echo $provider->id; ?>">

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label>Usuario (se genera automático)</label>
							<input type="text" class="form-control" value="prov_<?php echo strtolower($provider->code); ?>" disabled>
							<small class="text-muted">Basado en el código del proveedor</small>
						</div>

						<div class="form-group">
							<label>Contraseña Temporal</label>
							<input type="text" name="password" class="form-control" value="temporal123" required>
							<small class="text-muted">El usuario podrá cambiarla después</small>
						</div>

						<div class="form-group">
							<label>Nivel de Acceso</label>
							<select name="access_level" class="form-control">
								<option value="readonly">Solo Lectura</option>
								<option value="limited">Limitado</option>
								<option value="full">Completo</option>
							</select>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label>Grupo de Usuario</label>
							<select name="group_id" class="form-control">
								<option value="50">Proveedores (Portal)</option>
								<option value="10">Usuarios Normales</option>
							</select>
							<small class="text-muted">Determina los permisos base</small>
						</div>

						<div class="form-group">
							<label><i class="fas fa-building"></i> Backends a los que puede acceder</label>
							<?php foreach ($all_tenants as $tenant): ?>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" 
										   class="custom-control-input tenant-checkbox" 
										   id="tenant_<?php echo $tenant['id']; ?>" 
										   name="tenants[]" 
										   value="<?php echo $tenant['id']; ?>"
										   <?php echo ($tenant['id'] == $provider->tenant_id) ? 'checked' : ''; ?>>
									<label class="custom-control-label" for="tenant_<?php echo $tenant['id']; ?>">
										<?php echo $tenant['company_name']; ?> 
										<small class="text-muted">(<?php echo $tenant['domain']; ?>)</small>
									</label>
								</div>
							<?php endforeach; ?>
						</div>

						<div class="form-group">
							<label>Backend por Defecto</label>
							<select name="default_tenant" class="form-control">
								<?php foreach ($all_tenants as $tenant): ?>
									<option value="<?php echo $tenant['id']; ?>" 
											<?php echo ($tenant['id'] == $provider->tenant_id) ? 'selected' : ''; ?>>
										<?php echo $tenant['company_name']; ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>

				<button type="submit" class="btn btn-primary">
					<i class="fas fa-user-plus"></i> Crear Usuario
				</button>
			</form>

		<?php else: ?>
			<!-- FORMULARIO PARA ACTUALIZAR TENANTS -->
			<h5>Gestionar Acceso a Backends</h5>
			<form action="<?php echo Uri::create('admin/proveedores/update_tenants'); ?>" method="post">
				<input type="hidden" name="user_id" value="<?php echo $user->id; ?>">
				<input type="hidden" name="provider_id" value="<?php echo $provider->id; ?>">

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label><i class="fas fa-building"></i> Backends con Acceso</label>
							<?php 
							$user_tenant_ids = array();
							foreach ($user_tenants as $ut) {
								$user_tenant_ids[] = $ut['tenant_id'];
							}
							?>

							<?php foreach ($all_tenants as $tenant): ?>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" 
										   class="custom-control-input" 
										   id="tenant_update_<?php echo $tenant['id']; ?>" 
										   name="tenants[]" 
										   value="<?php echo $tenant['id']; ?>"
										   <?php echo in_array($tenant['id'], $user_tenant_ids) ? 'checked' : ''; ?>>
									<label class="custom-control-label" for="tenant_update_<?php echo $tenant['id']; ?>">
										<?php echo $tenant['company_name']; ?> 
										<small class="text-muted">(<?php echo $tenant['domain']; ?>)</small>
									</label>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label>Backend por Defecto</label>
							<select name="default_tenant" class="form-control">
								<?php 
								$default_tenant_id = null;
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
							<small class="text-muted">Backend al que entra por defecto al hacer login</small>
						</div>

						<div class="alert alert-info mt-3">
							<strong>Tenants Actuales:</strong><br>
							<?php if (empty($user_tenants)): ?>
								<em>Sin acceso a ningún backend</em>
							<?php else: ?>
								<ul class="mb-0">
								<?php foreach ($user_tenants as $ut): ?>
									<li>
										<?php echo $ut['tenant_name']; ?>
										<?php if ($ut['is_default']): ?>
											<span class="badge badge-primary">Por Defecto</span>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					</div>
				</div>

				<button type="submit" class="btn btn-success">
					<i class="fas fa-save"></i> Actualizar Acceso
				</button>
			</form>
		<?php endif; ?>
	</div>
</div>

<script>
function deleteUser(userId, providerId) {
	if (!confirm('¿Estás seguro de eliminar el usuario? Esta acción no se puede deshacer.')) {
		return;
	}

	$.ajax({
		url: '<?php echo Uri::create('admin/proveedores/delete_user'); ?>',
		type: 'POST',
		data: {
			user_id: userId,
			provider_id: providerId
		},
		success: function(response) {
			if (response.success) {
				alert(response.message);
				location.reload();
			} else {
				alert(response.error || 'Error al eliminar usuario');
			}
		},
		error: function() {
			alert('Error en la comunicación con el servidor');
		}
	});
}
</script>
