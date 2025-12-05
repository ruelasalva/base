<!-- ENCABEZADO -->
<div class="header bg-info pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-8 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">
						<i class="fas fa-network-wired"></i> Gestionar Acceso a Backends
					</h6>
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item"><?php echo Html::anchor('admin', '<i class="fas fa-home"></i>'); ?></li>
							<li class="breadcrumb-item"><?php echo Html::anchor('admin/proveedores', 'Proveedores'); ?></li>
							<li class="breadcrumb-item"><?php echo Html::anchor('admin/proveedores/info/'.$provider->id, $provider->name); ?></li>
							<li class="breadcrumb-item active">Gestionar Acceso - <?php echo $contact->get_full_name(); ?></li>
						</ol>
					</nav>
				</div>
				<div class="col-lg-4 col-5 text-right">
					<?php echo Html::anchor('admin/proveedores/info/'.$provider->id.'#panel-contactos-proveedor', '<i class="fas fa-arrow-left"></i> Volver', array('class' => 'btn btn-sm btn-neutral')); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- CONTENIDO -->
<div class="container-fluid mt--6">
	<div class="row">
		<div class="col-lg-8 offset-lg-2">
			<div class="card shadow">
				<div class="card-header bg-white border-0">
					<div class="row align-items-center">
						<div class="col">
							<h3 class="mb-0">Acceso a Backends - Contacto</h3>
						</div>
					</div>
				</div>
				<div class="card-body">
					
					<!-- INFO DEL CONTACTO -->
					<div class="alert alert-info">
						<div class="row">
							<div class="col-md-6">
								<strong>Contacto:</strong> <?php echo $contact->get_full_name(); ?><br>
								<strong>Email:</strong> <?php echo $contact->email; ?><br>
								<strong>Proveedor:</strong> <?php echo $provider->name; ?>
							</div>
							<div class="col-md-6">
								<strong>Usuario:</strong> <?php echo $user->username; ?><br>
								<strong>Grupo:</strong> <?php echo $user->group->name ?? 'N/A'; ?><br>
								<strong>Estado:</strong> 
								<?php if ($user->is_active): ?>
									<span class="badge badge-success">Activo</span>
								<?php else: ?>
									<span class="badge badge-danger">Inactivo</span>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<!-- BACKENDS ACTUALES -->
					<?php if (!empty($user_tenants)): ?>
					<div class="mb-4">
						<h4 class="mb-3"><i class="fas fa-server text-primary"></i> Backends con Acceso Actual</h4>
						<div class="list-group">
							<?php foreach ($user_tenants as $ut): ?>
							<div class="list-group-item">
								<div class="row align-items-center">
									<div class="col">
										<h5 class="mb-0"><?php echo $ut['tenant_name']; ?></h5>
										<small class="text-muted">Tenant ID: <?php echo $ut['tenant_id']; ?></small>
									</div>
									<div class="col-auto">
										<?php if ($ut['is_default']): ?>
											<span class="badge badge-primary">Predeterminado</span>
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
					</div>
					<?php endif; ?>

					<!-- FORMULARIO DE ASIGNACIÓN -->
					<hr class="my-4">
					<h4 class="mb-3"><i class="fas fa-edit text-warning"></i> Actualizar Acceso</h4>

					<?php echo Form::open(array(
						'action' => 'admin/proveedores/update_contact_tenants',
						'method' => 'post',
						'id' => 'form-contact-tenants'
					)); ?>

					<?php echo Form::hidden('contact_id', $contact->id); ?>
					<?php echo Form::hidden('provider_id', $provider->id); ?>

					<div class="form-group">
						<label class="form-control-label">Seleccionar Backends</label>
						<p class="text-muted small">El contacto podrá acceder a los backends seleccionados</p>
						
						<?php if (!empty($all_tenants)): ?>
							<?php foreach ($all_tenants as $tenant): ?>
							<div class="custom-control custom-checkbox mb-2">
								<?php
								$is_checked = false;
								foreach ($user_tenants as $ut) {
									if ($ut['tenant_id'] == $tenant['id'] && $ut['is_active']) {
										$is_checked = true;
										break;
									}
								}
								?>
								<input type="checkbox" 
									   class="custom-control-input tenant-checkbox" 
									   id="tenant_<?php echo $tenant['id']; ?>" 
									   name="tenant_ids[]" 
									   value="<?php echo $tenant['id']; ?>"
									   <?php echo $is_checked ? 'checked' : ''; ?>>
								<label class="custom-control-label" for="tenant_<?php echo $tenant['id']; ?>">
									<strong><?php echo $tenant['name']; ?></strong>
									<br>
									<small class="text-muted"><?php echo $tenant['description'] ?? 'Sin descripción'; ?></small>
								</label>
							</div>
							<?php endforeach; ?>
						<?php else: ?>
							<div class="alert alert-warning">No hay backends disponibles en el sistema</div>
						<?php endif; ?>
					</div>

					<div class="form-group">
						<label class="form-control-label">Backend Predeterminado</label>
						<p class="text-muted small">Backend al que accederá por defecto al iniciar sesión</p>
						<select name="default_tenant" id="default_tenant" class="form-control" required>
							<option value="">-- Seleccionar --</option>
							<?php 
							$current_default = null;
							foreach ($user_tenants as $ut) {
								if ($ut['is_default']) {
									$current_default = $ut['tenant_id'];
									break;
								}
							}
							?>
							<?php foreach ($all_tenants as $tenant): ?>
							<option value="<?php echo $tenant['id']; ?>" 
									<?php echo ($current_default == $tenant['id']) ? 'selected' : ''; ?>
									data-tenant-id="<?php echo $tenant['id']; ?>">
								<?php echo $tenant['name']; ?>
							</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="text-right">
						<?php echo Html::anchor('admin/proveedores/info/'.$provider->id.'#panel-contactos-proveedor', 'Cancelar', array('class' => 'btn btn-secondary')); ?>
						<button type="submit" class="btn btn-primary">
							<i class="fas fa-save"></i> Guardar Cambios
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
			// Si el predeterminado era este, limpiar selección
			if (defaultSelect.val() == tenantId) {
				defaultSelect.val('');
			}
		}
	});

	// Al cargar, deshabilitar opciones de tenants no seleccionados
	$('.tenant-checkbox').each(function() {
		var tenantId = $(this).val();
		var option = $('#default_tenant').find('option[data-tenant-id="' + tenantId + '"]');
		if (!$(this).is(':checked')) {
			option.prop('disabled', true);
		}
	});

	// Validar que el predeterminado esté marcado
	$('#form-contact-tenants').on('submit', function(e) {
		var defaultTenant = $('#default_tenant').val();
		var isChecked = $('#tenant_' + defaultTenant).is(':checked');
		
		if (defaultTenant && !isChecked) {
			e.preventDefault();
			alert('El backend predeterminado debe estar seleccionado en la lista de backends con acceso');
			return false;
		}

		if (!defaultTenant) {
			e.preventDefault();
			alert('Debe seleccionar un backend predeterminado');
			return false;
		}
	});
});
</script>
