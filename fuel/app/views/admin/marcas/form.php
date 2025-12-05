<div class="container-fluid p-4">
	<!-- Header -->
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h1 class="h3 mb-1">
				<i class="fa fa-copyright me-2"></i> <?php echo $brand ? 'Editar' : 'Nueva'; ?> Marca
			</h1>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-0">
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Dashboard</a></li>
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/marcas'); ?>">Marcas</a></li>
					<li class="breadcrumb-item active"><?php echo $brand ? 'Editar' : 'Nueva'; ?></li>
				</ol>
			</nav>
		</div>
		<a href="<?php echo Uri::create('admin/marcas'); ?>" class="btn btn-outline-secondary">
			<i class="fa fa-arrow-left me-1"></i> Volver
		</a>
	</div>

	<!-- Formulario -->
	<div class="row">
		<div class="col-lg-8">
			<form method="post" action="" class="needs-validation">
				<?php echo Form::csrf(); ?>

				<div class="card border-0 shadow-sm mb-4">
					<div class="card-header bg-white">
						<h5 class="mb-0">Información de la Marca</h5>
					</div>
					<div class="card-body">
						<!-- Nombre -->
						<div class="mb-3">
							<label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
							<input type="text" 
								   class="form-control" 
								   id="name" 
								   name="name" 
								   value="<?php echo Input::post('name', $brand ? $brand->name : ''); ?>"
								   required
								   maxlength="255">
							<div class="form-text">Nombre de la marca o fabricante</div>
						</div>

						<!-- Slug -->
						<div class="mb-3">
							<label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
							<input type="text" 
								   class="form-control" 
								   id="slug" 
								   name="slug" 
								   value="<?php echo Input::post('slug', $brand ? $brand->slug : ''); ?>"
								   required
								   maxlength="255"
								   pattern="[a-z0-9\-]+">
							<div class="form-text">URL amigable (solo letras minúsculas, números y guiones)</div>
						</div>

						<!-- Imagen URL (opcional) -->
						<div class="mb-3">
							<label for="image" class="form-label">URL de Imagen</label>
							<input type="text" 
								   class="form-control" 
								   id="image" 
								   name="image" 
								   value="<?php echo Input::post('image', $brand ? $brand->image : ''); ?>"
								   maxlength="255">
							<div class="form-text">URL de la imagen o logo de la marca (opcional)</div>
						</div>

						<!-- Estado -->
						<div class="mb-3">
							<label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
							<select class="form-select" id="status" name="status" required>
								<option value="1" <?php echo Input::post('status', $brand ? $brand->status : 1) == 1 ? 'selected' : ''; ?>>
									Activa
								</option>
								<option value="0" <?php echo Input::post('status', $brand ? $brand->status : 1) == 0 ? 'selected' : ''; ?>>
									Inactiva
								</option>
							</select>
							<div class="form-text">Las marcas inactivas no se mostrarán en el sitio</div>
						</div>
					</div>
					<div class="card-footer bg-white">
						<button type="submit" class="btn btn-primary">
							<i class="fa fa-save me-1"></i> Guardar Marca
						</button>
						<a href="<?php echo Uri::create('admin/marcas'); ?>" class="btn btn-outline-secondary">
							Cancelar
						</a>
					</div>
				</div>
			</form>
		</div>

		<!-- Sidebar de ayuda -->
		<div class="col-lg-4">
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-light">
					<h6 class="mb-0"><i class="fa fa-info-circle me-1"></i> Ayuda</h6>
				</div>
				<div class="card-body">
					<h6>Slug</h6>
					<p class="small text-muted">
						El slug es la versión amigable para URLs del nombre. 
						Usa solo letras minúsculas, números y guiones. 
						Ejemplo: "samsung", "hp", "dell-computers"
					</p>

					<h6>Marcas y Fabricantes</h6>
					<p class="small text-muted mb-0">
						Registra marcas comerciales o fabricantes de productos. 
						Esto te ayudará a clasificar mejor tu catálogo y facilitar las búsquedas.
					</p>
				</div>
			</div>

			<?php if ($brand): ?>
				<div class="card border-0 shadow-sm mt-3">
					<div class="card-header bg-light">
						<h6 class="mb-0"><i class="fa fa-clock me-1"></i> Información</h6>
					</div>
					<div class="card-body">
						<div class="small">
							<?php if ($brand->created_at): ?>
								<p class="mb-2">
									<strong>Creada:</strong><br>
									<?php echo date('d/m/Y H:i', $brand->created_at); ?>
								</p>
							<?php endif; ?>
							<?php if ($brand->updated_at): ?>
								<p class="mb-0">
									<strong>Última actualización:</strong><br>
									<?php echo date('d/m/Y H:i', $brand->updated_at); ?>
								</p>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<script>
// Auto-generar slug desde el nombre
document.getElementById('name').addEventListener('blur', function() {
	var slugField = document.getElementById('slug');
	if (!slugField.value) {
		var slug = this.value
			.toLowerCase()
			.normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Remover acentos
			.replace(/[^a-z0-9\s-]/g, '') // Remover caracteres especiales
			.replace(/\s+/g, '-') // Espacios a guiones
			.replace(/-+/g, '-') // Guiones múltiples a uno solo
			.trim();
		slugField.value = slug;
	}
});
</script>
