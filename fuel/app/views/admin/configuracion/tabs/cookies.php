<form action="<?php echo Uri::create('admin/configuracion/actualizar'); ?>" method="post">
	<input type="hidden" name="active_tab" value="cookies">
	<?php echo Form::csrf(); ?>

	<div class="form-check form-switch mb-4">
		<input class="form-check-input" type="checkbox" id="cookie_consent_enabled" name="cookie_consent_enabled" value="1" <?php echo ($config->cookie_consent_enabled) ? 'checked' : ''; ?>>
		<label class="form-check-label" for="cookie_consent_enabled">
			<strong>Mostrar Aviso de Cookies</strong>
		</label>
	</div>

	<div class="mb-3">
		<label for="cookie_message" class="form-label">Mensaje del Banner</label>
		<textarea class="form-control" id="cookie_message" name="cookie_message" rows="3"><?php echo e($config->cookie_message ?: 'Este sitio utiliza cookies para mejorar tu experiencia.'); ?></textarea>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="mb-3">
				<label for="privacy_policy_url" class="form-label">URL Política de Privacidad</label>
				<input type="url" class="form-control" id="privacy_policy_url" name="privacy_policy_url" value="<?php echo e($config->privacy_policy_url); ?>" placeholder="/privacidad">
			</div>
		</div>
		<div class="col-md-6">
			<div class="mb-3">
				<label for="terms_conditions_url" class="form-label">URL Términos y Condiciones</label>
				<input type="url" class="form-control" id="terms_conditions_url" name="terms_conditions_url" value="<?php echo e($config->terms_conditions_url); ?>" placeholder="/terminos">
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
