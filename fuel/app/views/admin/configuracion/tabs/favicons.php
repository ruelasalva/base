<form action="<?php echo Uri::create('admin/configuracion/actualizar'); ?>" method="post">
	<input type="hidden" name="active_tab" value="favicons">
	<?php echo Form::csrf(); ?>

	<div class="alert alert-info">
		<i class="fas fa-info-circle me-2"></i>
		<strong>Tip:</strong> Usa un generador de favicons como 
		<a href="https://realfavicongenerator.net/" target="_blank" class="alert-link">RealFaviconGenerator</a>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="form-label">Favicon 16x16</label>
			<input type="text" class="form-control" name="favicon_16" value="<?php echo e($config->favicon_16); ?>" placeholder="/assets/img/favicon-16x16.png">
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">Favicon 32x32</label>
			<input type="text" class="form-control" name="favicon_32" value="<?php echo e($config->favicon_32); ?>" placeholder="/assets/img/favicon-32x32.png">
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">Apple Touch Icon 57x57</label>
			<input type="text" class="form-control" name="favicon_57" value="<?php echo e($config->favicon_57); ?>" placeholder="/assets/img/apple-touch-icon-57x57.png">
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">Apple Touch Icon 72x72</label>
			<input type="text" class="form-control" name="favicon_72" value="<?php echo e($config->favicon_72); ?>" placeholder="/assets/img/apple-touch-icon-72x72.png">
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">Apple Touch Icon 114x114</label>
			<input type="text" class="form-control" name="favicon_114" value="<?php echo e($config->favicon_114); ?>" placeholder="/assets/img/apple-touch-icon-114x114.png">
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">Apple Touch Icon 144x144</label>
			<input type="text" class="form-control" name="favicon_144" value="<?php echo e($config->favicon_144); ?>" placeholder="/assets/img/apple-touch-icon-144x144.png">
		</div>
	</div>

	<hr class="my-4">
	<div class="text-end">
		<button type="submit" class="btn btn-primary">
			<i class="fas fa-save me-1"></i> Guardar Cambios
		</button>
	</div>
</form>
