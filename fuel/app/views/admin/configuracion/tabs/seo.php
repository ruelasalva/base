<form action="<?php echo Uri::create('admin/configuracion/actualizar'); ?>" method="post">
	<input type="hidden" name="active_tab" value="seo">
	<?php echo Form::csrf(); ?>

	<div class="row">
		<div class="col-md-12">
			<div class="mb-3">
				<label for="meta_description" class="form-label">Meta Description</label>
				<textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="160"><?php echo e($config->meta_description); ?></textarea>
				<small class="form-text text-muted">Descripción que aparece en resultados de búsqueda (máx. 160 caracteres)</small>
			</div>

			<div class="mb-3">
				<label for="meta_keywords" class="form-label">Meta Keywords</label>
				<input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="<?php echo e($config->meta_keywords); ?>">
				<small class="form-text text-muted">Palabras clave separadas por comas</small>
			</div>

			<div class="mb-3">
				<label for="meta_author" class="form-label">Meta Author</label>
				<input type="text" class="form-control" id="meta_author" name="meta_author" value="<?php echo e($config->meta_author); ?>">
			</div>

			<div class="mb-3">
				<label for="og_image" class="form-label">Imagen Open Graph</label>
				<input type="text" class="form-control" id="og_image" name="og_image" value="<?php echo e($config->og_image); ?>" placeholder="URL de imagen para compartir en redes sociales">
				<small class="form-text text-muted">Imagen que aparece al compartir en Facebook/Twitter (1200x630px recomendado)</small>
			</div>

			<div class="mb-3">
				<label for="theme_color" class="form-label">Color del Tema</label>
				<div class="input-group">
					<input type="color" class="form-control form-control-color" id="theme_color" name="theme_color" value="<?php echo e($config->theme_color ?: '#008ad5'); ?>">
					<input type="text" class="form-control" value="<?php echo e($config->theme_color ?: '#008ad5'); ?>" onchange="document.getElementById('theme_color').value = this.value">
				</div>
				<small class="form-text text-muted">Color de la barra de direcciones en móviles</small>
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
