<form action="<?php echo Uri::create('admin/configuracion/actualizar'); ?>" method="post" id="form-tracking">
	<input type="hidden" name="active_tab" value="tracking">
	<?php echo Form::csrf(); ?>

	<!-- Google Analytics -->
	<div class="card mb-4">
		<div class="card-header pb-0">
			<div class="d-flex align-items-center justify-content-between">
				<div>
					<h6 class="mb-0">Google Analytics</h6>
					<small class="text-muted">Seguimiento de visitas y comportamiento</small>
				</div>
				<div class="form-check form-switch">
					<input class="form-check-input" 
						type="checkbox" 
						id="ga_enabled" 
						name="ga_enabled" 
						value="1" 
						<?php echo ($config->ga_enabled) ? 'checked' : ''; ?>>
					<label class="form-check-label" for="ga_enabled">Activado</label>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<div class="mb-3">
						<label for="ga_tracking_id" class="form-label">Tracking ID</label>
						<input type="text" 
							class="form-control" 
							id="ga_tracking_id" 
							name="ga_tracking_id" 
							value="<?php echo e($config->ga_tracking_id); ?>" 
							placeholder="G-XXXXXXXXXX">
						<small class="form-text text-muted">
							Ejemplo: G-9K8BB87HW6 (GA4) o UA-XXXXXX-X (Universal Analytics)
						</small>
					</div>
				</div>
				<div class="col-md-6">
					<div class="mb-3">
						<label for="ga_script" class="form-label">Script Personalizado (Opcional)</label>
						<textarea 
							class="form-control font-monospace" 
							id="ga_script" 
							name="ga_script" 
							rows="3" 
							placeholder="Si dejas esto vacío, se generará automáticamente"><?php echo e($config->ga_script); ?></textarea>
						<small class="form-text text-muted">Solo si necesitas configuración avanzada</small>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Google Tag Manager -->
	<div class="card mb-4">
		<div class="card-header pb-0">
			<div class="d-flex align-items-center justify-content-between">
				<div>
					<h6 class="mb-0">Google Tag Manager</h6>
					<small class="text-muted">Gestión centralizada de tags</small>
				</div>
				<div class="form-check form-switch">
					<input class="form-check-input" 
						type="checkbox" 
						id="gtm_enabled" 
						name="gtm_enabled" 
						value="1" 
						<?php echo ($config->gtm_enabled) ? 'checked' : ''; ?>>
					<label class="form-check-label" for="gtm_enabled">Activado</label>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="mb-3">
				<label for="gtm_container_id" class="form-label">Container ID</label>
				<input type="text" 
					class="form-control" 
					id="gtm_container_id" 
					name="gtm_container_id" 
					value="<?php echo e($config->gtm_container_id); ?>" 
					placeholder="GTM-XXXXXXX">
				<small class="form-text text-muted">Ejemplo: GTM-AB12CD3</small>
			</div>
		</div>
	</div>

	<!-- Facebook Pixel -->
	<div class="card mb-4">
		<div class="card-header pb-0">
			<div class="d-flex align-items-center justify-content-between">
				<div>
					<h6 class="mb-0">Facebook Pixel</h6>
					<small class="text-muted">Tracking para anuncios de Facebook</small>
				</div>
				<div class="form-check form-switch">
					<input class="form-check-input" 
						type="checkbox" 
						id="fb_pixel_enabled" 
						name="fb_pixel_enabled" 
						value="1" 
						<?php echo ($config->fb_pixel_enabled) ? 'checked' : ''; ?>>
					<label class="form-check-label" for="fb_pixel_enabled">Activado</label>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="mb-3">
				<label for="fb_pixel_id" class="form-label">Pixel ID</label>
				<input type="text" 
					class="form-control" 
					id="fb_pixel_id" 
					name="fb_pixel_id" 
					value="<?php echo e($config->fb_pixel_id); ?>" 
					placeholder="123456789012345">
				<small class="form-text text-muted">ID numérico de 15 dígitos</small>
			</div>
		</div>
	</div>

	<!-- reCAPTCHA -->
	<div class="card mb-4">
		<div class="card-header pb-0">
			<div class="d-flex align-items-center justify-content-between">
				<div>
					<h6 class="mb-0">Google reCAPTCHA</h6>
					<small class="text-muted">Protección contra bots y spam</small>
				</div>
				<div class="form-check form-switch">
					<input class="form-check-input" 
						type="checkbox" 
						id="recaptcha_enabled" 
						name="recaptcha_enabled" 
						value="1" 
						<?php echo ($config->recaptcha_enabled) ? 'checked' : ''; ?>>
					<label class="form-check-label" for="recaptcha_enabled">Activado</label>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<div class="mb-3">
						<label for="recaptcha_site_key" class="form-label">Site Key</label>
						<input type="text" 
							class="form-control font-monospace" 
							id="recaptcha_site_key" 
							name="recaptcha_site_key" 
							value="<?php echo e($config->recaptcha_site_key); ?>" 
							placeholder="6Lc...">
						<small class="form-text text-muted">Clave pública (visible en frontend)</small>
					</div>
				</div>
				<div class="col-md-6">
					<div class="mb-3">
						<label for="recaptcha_secret_key" class="form-label">Secret Key</label>
						<input type="password" 
							class="form-control font-monospace" 
							id="recaptcha_secret_key" 
							name="recaptcha_secret_key" 
							value="<?php echo e($config->recaptcha_secret_key); ?>" 
							placeholder="6Lc...">
						<small class="form-text text-muted">Clave secreta (validación servidor)</small>
					</div>
				</div>
				<div class="col-md-6">
					<div class="mb-3">
						<label for="recaptcha_version" class="form-label">Versión</label>
						<select class="form-select" id="recaptcha_version" name="recaptcha_version">
							<option value="v2" <?php echo ($config->recaptcha_version === 'v2') ? 'selected' : ''; ?>>
								v2 (Checkbox "No soy un robot")
							</option>
							<option value="v3" <?php echo ($config->recaptcha_version === 'v3') ? 'selected' : ''; ?>>
								v3 (Invisible, basado en puntuación)
							</option>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<div class="mb-3">
						<label class="form-label">Probar reCAPTCHA</label>
						<button type="button" 
							class="btn btn-sm btn-outline-info w-100" 
							onclick="testRecaptcha()">
							<i class="fas fa-vial me-1"></i> Test de Validación
						</button>
						<small class="form-text text-muted">Verificar que las claves funcionan</small>
					</div>
				</div>
			</div>

			<div class="alert alert-info mb-0">
				<i class="fas fa-info-circle me-2"></i>
				<strong>Obtener claves reCAPTCHA:</strong>
				<a href="https://www.google.com/recaptcha/admin" target="_blank" class="alert-link">
					https://www.google.com/recaptcha/admin
				</a>
			</div>
		</div>
	</div>

	<hr class="my-4">

	<div class="text-end">
		<button type="submit" class="btn btn-primary">
			<i class="fas fa-save me-1"></i> Guardar Configuración
		</button>
	</div>
</form>

<script>
function testRecaptcha() {
	Swal.fire({
		title: 'Probar reCAPTCHA',
		html: `
			<p>Esta función requiere que guardes primero la configuración.</p>
			<p>¿Deseas continuar?</p>
		`,
		icon: 'info',
		showCancelButton: true,
		confirmButtonText: 'Guardar y Probar',
		cancelButtonText: 'Cancelar'
	}).then((result) => {
		if (result.isConfirmed) {
			document.getElementById('form-tracking').submit();
		}
	});
}
</script>
