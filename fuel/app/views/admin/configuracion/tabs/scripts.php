<form action="<?php echo Uri::create('admin/configuracion/actualizar'); ?>" method="post">
	<input type="hidden" name="active_tab" value="scripts">
	<?php echo Form::csrf(); ?>

	<div class="alert alert-warning">
		<i class="fas fa-exclamation-triangle me-2"></i>
		<strong>Advertencia:</strong> Solo agrega scripts si sabes lo que haces. Código incorrecto puede romper el sitio.
	</div>

	<div class="mb-4">
		<label for="custom_head_scripts" class="form-label">Scripts personalizados en &lt;head&gt;</label>
		<textarea class="form-control font-monospace" id="custom_head_scripts" name="custom_head_scripts" rows="10" placeholder="<!-- Scripts que se cargan en <head> -->"><?php echo e($config->custom_head_scripts); ?></textarea>
		<small class="form-text text-muted">
			Scripts que deben cargarse antes del contenido (ej: fuentes, CSS crítico, configuraciones)
		</small>
	</div>

	<div class="mb-4">
		<label for="custom_body_scripts" class="form-label">Scripts personalizados antes de &lt;/body&gt;</label>
		<textarea class="form-control font-monospace" id="custom_body_scripts" name="custom_body_scripts" rows="10" placeholder="<!-- Scripts que se cargan al final del body -->"><?php echo e($config->custom_body_scripts); ?></textarea>
		<small class="form-text text-muted">
			Scripts que deben cargarse después del contenido (mejor rendimiento)
		</small>
	</div>

	<hr class="my-4">
	<div class="text-end">
		<button type="submit" class="btn btn-primary">
			<i class="fas fa-save me-1"></i> Guardar Cambios
		</button>
	</div>
</form>
