<!-- MODAL: CREAR USUARIO PARA CONTACTO -->
<div class="modal fade" id="modal-create-contact-user" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-gradient-primary">
				<h5 class="modal-title text-white">
					<i class="fas fa-user-plus"></i> Crear Usuario para Contacto
				</h5>
				<button type="button" class="close text-white" data-dismiss="modal">
					<span>&times;</span>
				</button>
			</div>

			<?php echo Form::open(array(
				'action' => 'admin/proveedores/create_contact_user',
				'method' => 'post',
				'id' => 'form-create-contact-user'
			)); ?>

			<div class="modal-body">
				<?php echo Form::hidden('contact_id', '', array('id' => 'contact_user_contact_id')); ?>
				<?php echo Form::hidden('provider_id', $provider_id); ?>

				<div class="alert alert-info">
					<strong><i class="fas fa-info-circle"></i> Información</strong><br>
					Se creará un usuario que permitirá a este contacto acceder al portal de proveedores.
					El nombre de usuario se generará automáticamente.
				</div>

				<!-- DATOS DEL CONTACTO -->
				<div class="card mb-3">
					<div class="card-body bg-light">
						<h6 class="mb-2"><strong>Contacto seleccionado:</strong></h6>
						<div id="contact-user-info">
							<strong>Nombre:</strong> <span id="contact-user-name">-</span><br>
							<strong>Email:</strong> <span id="contact-user-email">-</span><br>
							<strong>Teléfono:</strong> <span id="contact-user-phone">-</span>
						</div>
					</div>
				</div>

				<!-- CONTRASEÑA -->
				<div class="form-group">
					<label class="form-control-label required">Contraseña</label>
					<input type="password" 
						   name="password" 
						   id="contact_password" 
						   class="form-control" 
						   required
						   minlength="6"
						   placeholder="Mínimo 6 caracteres">
					<small class="form-text text-muted">
						El contacto usará esta contraseña para acceder al portal
					</small>
				</div>

				<!-- NIVEL DE ACCESO -->
				<div class="form-group">
					<label class="form-control-label">Nivel de Acceso</label>
					<select name="access_level" class="form-control">
						<option value="readonly">Solo Lectura</option>
						<option value="limited">Limitado (ver y comentar)</option>
						<option value="full">Completo (ver, editar)</option>
					</select>
					<small class="form-text text-muted">
						Define qué puede hacer el contacto en el portal
					</small>
				</div>

				<!-- BACKENDS -->
				<div class="form-group">
					<label class="form-control-label">Backends con Acceso</label>
					<p class="text-muted small mb-2">Selecciona los backends a los que podrá acceder este contacto</p>
					
					<?php if (!empty($all_tenants)): ?>
						<div style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; padding: 10px; border-radius: 4px;">
							<?php foreach ($all_tenants as $tenant): ?>
							<div class="custom-control custom-checkbox mb-2">
								<input type="checkbox" 
									   class="custom-control-input contact-tenant-checkbox" 
									   id="contact_tenant_<?php echo $tenant['id']; ?>" 
									   name="tenant_ids[]" 
									   value="<?php echo $tenant['id']; ?>">
								<label class="custom-control-label" for="contact_tenant_<?php echo $tenant['id']; ?>">
									<strong><?php echo $tenant['name']; ?></strong>
									<?php if (!empty($tenant['description'])): ?>
										<br><small class="text-muted"><?php echo $tenant['description']; ?></small>
									<?php endif; ?>
								</label>
							</div>
							<?php endforeach; ?>
						</div>
					<?php else: ?>
						<div class="alert alert-warning mb-0">No hay backends disponibles</div>
					<?php endif; ?>
				</div>

				<!-- BACKEND PREDETERMINADO -->
				<div class="form-group">
					<label class="form-control-label required">Backend Predeterminado</label>
					<select name="default_tenant" id="contact_default_tenant" class="form-control" required>
						<option value="">-- Seleccionar --</option>
						<?php if (!empty($all_tenants)): ?>
							<?php foreach ($all_tenants as $tenant): ?>
							<option value="<?php echo $tenant['id']; ?>" data-tenant-id="<?php echo $tenant['id']; ?>">
								<?php echo isset($tenant['name']) ? $tenant['name'] : 'Backend '.$tenant['id']; ?>
							</option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
					<small class="form-text text-muted">
						Backend al que accederá por defecto al iniciar sesión
					</small>
				</div>

			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					<i class="fas fa-times"></i> Cancelar
				</button>
				<button type="submit" class="btn btn-primary">
					<i class="fas fa-user-plus"></i> Crear Usuario
				</button>
			</div>

			<?php echo Form::close(); ?>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	// Abrir modal con datos del contacto
	$(document).on('click', '.btn-create-contact-user', function() {
		var contactId = $(this).data('contact-id');
		var contactName = $(this).data('contact-name');
		var contactEmail = $(this).data('contact-email');
		var contactPhone = $(this).data('contact-phone');

		$('#contact_user_contact_id').val(contactId);
		$('#contact-user-name').text(contactName);
		$('#contact-user-email').text(contactEmail);
		$('#contact-user-phone').text(contactPhone || 'N/A');

		$('#modal-create-contact-user').modal('show');
	});

	// Actualizar opciones de backend predeterminado
	$('.contact-tenant-checkbox').on('change', function() {
		var defaultSelect = $('#contact_default_tenant');
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
	$('#form-create-contact-user').on('submit', function(e) {
		var selectedTenants = $('.contact-tenant-checkbox:checked').length;
		if (selectedTenants === 0) {
			e.preventDefault();
			alert('Debe seleccionar al menos un backend');
			return false;
		}

		var defaultTenant = $('#contact_default_tenant').val();
		if (!defaultTenant) {
			e.preventDefault();
			alert('Debe seleccionar un backend predeterminado');
			return false;
		}

		var isDefaultChecked = $('#contact_tenant_' + defaultTenant).is(':checked');
		if (!isDefaultChecked) {
			e.preventDefault();
			alert('El backend predeterminado debe estar seleccionado en la lista');
			return false;
		}

		var password = $('#contact_password').val();
		if (password.length < 6) {
			e.preventDefault();
			alert('La contraseña debe tener al menos 6 caracteres');
			return false;
		}
	});
});
</script>
