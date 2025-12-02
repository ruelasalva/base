<form action="<?php echo Uri::create('admin/configuracion/actualizar'); ?>" method="post" id="form-general">
	<input type="hidden" name="active_tab" value="general">
	<?php echo Form::csrf(); ?>

	<div class="row">
		<div class="col-md-6">
			<h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Información del Sitio</h6>
			
			<!-- Nombre del sitio -->
			<div class="mb-3">
				<label for="site_name" class="form-label">Nombre del Sitio *</label>
				<input type="text" 
					class="form-control" 
					id="site_name" 
					name="site_name" 
					value="<?php echo e($config->site_name); ?>" 
					required>
				<small class="form-text text-muted">Nombre principal del sitio (aparece en navegador y SEO)</small>
			</div>

			<!-- Eslogan -->
			<div class="mb-3">
				<label for="site_tagline" class="form-label">Eslogan</label>
				<input type="text" 
					class="form-control" 
					id="site_tagline" 
					name="site_tagline" 
					value="<?php echo e($config->site_tagline); ?>">
				<small class="form-text text-muted">Descripción corta del sitio</small>
			</div>

			<!-- Email -->
			<div class="mb-3">
				<label for="contact_email" class="form-label">Email de Contacto</label>
				<input type="email" 
					class="form-control" 
					id="contact_email" 
					name="contact_email" 
					value="<?php echo e($config->contact_email); ?>">
			</div>

			<!-- Teléfono -->
			<div class="mb-3">
				<label for="contact_phone" class="form-label">Teléfono</label>
				<input type="tel" 
					class="form-control" 
					id="contact_phone" 
					name="contact_phone" 
					value="<?php echo e($config->contact_phone); ?>">
			</div>

			<!-- Dirección -->
			<div class="mb-3">
				<label for="address" class="form-label">Dirección</label>
				<textarea 
					class="form-control" 
					id="address" 
					name="address" 
					rows="3"><?php echo e($config->address); ?></textarea>
			</div>
		</div>

		<div class="col-md-6">
			<h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Logos</h6>
			
			<!-- Logo Principal -->
			<div class="mb-4">
				<label for="logo_url" class="form-label">Logo Principal</label>
				<div class="input-group">
					<input type="text" 
						class="form-control" 
						id="logo_url" 
						name="logo_url" 
						value="<?php echo e($config->logo_url); ?>" 
						placeholder="URL del logo">
					<button class="btn btn-outline-primary" type="button" onclick="uploadImage('logo_url')">
						<i class="fas fa-upload"></i> Subir
					</button>
				</div>
				<?php if ($config->logo_url): ?>
					<div class="mt-2">
						<img src="<?php echo e($config->logo_url); ?>" alt="Logo" class="img-thumbnail" style="max-height: 100px;">
					</div>
				<?php endif; ?>
				<small class="form-text text-muted">Logo para fondo claro (formato PNG con transparencia recomendado)</small>
			</div>

			<!-- Logo Alternativo -->
			<div class="mb-4">
				<label for="logo_alt_url" class="form-label">Logo Alternativo</label>
				<div class="input-group">
					<input type="text" 
						class="form-control" 
						id="logo_alt_url" 
						name="logo_alt_url" 
						value="<?php echo e($config->logo_alt_url); ?>" 
						placeholder="URL del logo alternativo">
					<button class="btn btn-outline-primary" type="button" onclick="uploadImage('logo_alt_url')">
						<i class="fas fa-upload"></i> Subir
					</button>
				</div>
				<?php if ($config->logo_alt_url): ?>
					<div class="mt-2 p-3 bg-dark">
						<img src="<?php echo e($config->logo_alt_url); ?>" alt="Logo Alt" class="img-thumbnail" style="max-height: 100px;">
					</div>
				<?php endif; ?>
				<small class="form-text text-muted">Logo para fondo oscuro (usualmente blanco)</small>
			</div>
		</div>
	</div>

	<hr class="my-4">

	<div class="text-end">
		<button type="submit" class="btn btn-primary">
			<i class="fas fa-save me-1"></i> Guardar Cambios
		</button>
	</div>
</form>

<script>
function uploadImage(fieldId) {
	const input = document.createElement('input');
	input.type = 'file';
	input.accept = 'image/*';
	
	input.onchange = function(e) {
		const file = e.target.files[0];
		if (!file) return;
		
		const formData = new FormData();
		formData.append('file', file);
		
		// Mostrar loading
		const btn = event.target.closest('button');
		const originalHtml = btn.innerHTML;
		btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...';
		btn.disabled = true;
		
		fetch('<?php echo Uri::create('admin/configuracion/upload'); ?>', {
			method: 'POST',
			body: formData,
			headers: {
				'X-CSRF-TOKEN': document.querySelector('[name="fuel_csrf_token"]').value
			}
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				document.getElementById(fieldId).value = data.url;
				Swal.fire('¡Éxito!', 'Imagen subida correctamente', 'success');
				// Recargar para mostrar preview
				location.reload();
			} else {
				Swal.fire('Error', data.message, 'error');
			}
		})
		.catch(error => {
			Swal.fire('Error', 'Error al subir la imagen', 'error');
			console.error('Error:', error);
		})
		.finally(() => {
			btn.innerHTML = originalHtml;
			btn.disabled = false;
		});
	};
	
	input.click();
}
</script>
