<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="card shadow">
				<div class="card-header bg-gradient-success">
					<h3 class="mb-0 text-white">
						<i class="fas fa-user-plus"></i> Crear Usuario para Contacto
					</h3>
				</div>

				<div class="card-body">
					<!-- INFORMACIÓN DEL CONTACTO -->
					<div class="alert alert-info">
						<h5><strong><i class="fas fa-info-circle"></i> Información del Contacto</strong></h5>
						<div class="row mt-3">
							<div class="col-md-6">
								<p class="mb-2"><strong>Proveedor:</strong> <?php echo $provider->company_name; ?> (<?php echo $provider->code; ?>)</p>
								<p class="mb-2"><strong>Nombre:</strong> <?php echo $contact->get_full_name(); ?></p>
								<p class="mb-2"><strong>Email:</strong> <?php echo $contact->email; ?></p>
							</div>
							<div class="col-md-6">
								<p class="mb-2"><strong>Teléfono:</strong> <?php echo $contact->phone ?: 'N/A'; ?></p>
								<p class="mb-2"><strong>Celular:</strong> <?php echo $contact->cel ?: 'N/A'; ?></p>
								<p class="mb-2"><strong>Departamentos:</strong> <?php echo $contact->departments ?: 'N/A'; ?></p>
							</div>
						</div>
					</div>

					<!-- FORMULARIO -->
					<?php echo Form::open(array(
						'action' => 'admin/proveedores/create_contact_user',
						'method' => 'post',
						'class' => 'needs-validation',
						'novalidate' => true
					)); ?>

					<?php echo Form::hidden('contact_id', $contact->id); ?>
					<?php echo Form::hidden('provider_id', $provider->id); ?>

					<div class="row">
						<!-- CONTRASEÑA -->
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-control-label required">
									<i class="fas fa-lock"></i> Contraseña
								</label>
								<input type="password" 
									   name="password" 
									   class="form-control" 
									   required
									   minlength="6"
									   placeholder="Mínimo 6 caracteres">
								<small class="form-text text-muted">
									Se generará automáticamente el username como: <strong>prov_<?php echo strtolower($provider->code); ?>_<?php echo strtolower($contact->name); ?></strong>
								</small>
							</div>
						</div>

						<!-- NIVEL DE ACCESO -->
						<div class="col-md-6">
							<div class="form-group">
								<label class="form-control-label required">
									<i class="fas fa-shield-alt"></i> Nivel de Acceso
								</label>
								<select name="access_level" class="form-control" required>
									<option value="readonly">Solo Lectura</option>
									<option value="standard">Estándar</option>
									<option value="manager">Gerente</option>
								</select>
								<small class="form-text text-muted">
									Determina los permisos del usuario en el portal
								</small>
							</div>
						</div>
					</div>

					<!-- BACKENDS -->
					<div class="form-group">
						<label class="form-control-label required">
							<i class="fas fa-building"></i> Backends con Acceso
						</label>
						<p class="text-muted small">Selecciona los backends a los que podrá acceder este contacto</p>
						
						<?php if (!empty($all_tenants)): ?>
							<div style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; padding: 15px; border-radius: 4px; background: #f8f9fa;">
								<?php foreach ($all_tenants as $tenant): ?>
								<div class="custom-control custom-checkbox mb-3">
									<input type="checkbox" 
										   class="custom-control-input tenant-checkbox" 
										   id="tenant_<?php echo $tenant['id']; ?>" 
										   name="tenant_ids[]" 
										   value="<?php echo $tenant['id']; ?>"
										   <?php echo ($tenant['id'] == $current_tenant) ? 'checked' : ''; ?>>
									<label class="custom-control-label" for="tenant_<?php echo $tenant['id']; ?>">
										<strong><?php echo isset($tenant['name']) ? $tenant['name'] : $tenant['company_name']; ?></strong>
										<?php if (!empty($tenant['description'])): ?>
											<br><small class="text-muted"><?php echo $tenant['description']; ?></small>
										<?php endif; ?>
									</label>
								</div>
								<?php endforeach; ?>
							</div>
						<?php else: ?>
							<div class="alert alert-warning">No hay backends disponibles</div>
						<?php endif; ?>
					</div>

					<!-- BACKEND PREDETERMINADO -->
					<div class="form-group">
						<label class="form-control-label required">
							<i class="fas fa-star"></i> Backend Predeterminado
						</label>
						<select name="default_tenant" id="default_tenant" class="form-control" required>
							<option value="">-- Seleccionar --</option>
							<?php if (!empty($all_tenants)): ?>
								<?php foreach ($all_tenants as $tenant): ?>
								<option value="<?php echo $tenant['id']; ?>" 
										data-tenant-id="<?php echo $tenant['id']; ?>"
										<?php echo ($tenant['id'] == $current_tenant) ? 'selected' : ''; ?>>
									<?php echo isset($tenant['name']) ? $tenant['name'] : $tenant['company_name']; ?>
								</option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
						<small class="form-text text-muted">
							Backend al que accederá por defecto al iniciar sesión
						</small>
					</div>

					<!-- BOTONES -->
					<div class="form-group mt-4">
						<a href="<?php echo Uri::create('admin/proveedores/info/' . $provider->id . '#panel-contactos-proveedor'); ?>" 
						   class="btn btn-secondary">
							<i class="fas fa-arrow-left"></i> Cancelar
						</a>
						<button type="submit" class="btn btn-success">
							<i class="fas fa-user-plus"></i> Crear Usuario
						</button>
					</div>

					<?php echo Form::close(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	// Actualizar opciones de backend predeterminado según checkboxes
	$('.tenant-checkbox').on('change', function() {
		var defaultSelect = $('#default_tenant');
		var tenantId = $(this).val();
		var option = defaultSelect.find('option[data-tenant-id="' + tenantId + '"]');
		
		if ($(this).is(':checked')) {
			option.prop('disabled', false);
		} else {
			option.prop('disabled', true);
			if (defaultSelect.val() == tenantId) {
				defaultSelect.val('');
			}
		}
	});

	// Validación al enviar
	$('form').on('submit', function(e) {
		var selectedTenants = $('.tenant-checkbox:checked').length;
		if (selectedTenants === 0) {
			e.preventDefault();
			alert('Debe seleccionar al menos un backend');
			return false;
		}

		var defaultTenant = $('#default_tenant').val();
		if (!defaultTenant) {
			e.preventDefault();
			alert('Debe seleccionar un backend predeterminado');
			return false;
		}

		var isDefaultChecked = $('#tenant_' + defaultTenant).is(':checked');
		if (!isDefaultChecked) {
			e.preventDefault();
			alert('El backend predeterminado debe estar seleccionado en la lista');
			return false;
		}
	});
});
</script>
