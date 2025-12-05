<div class="container-fluid p-4">
	<!-- Header -->
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h1 class="h3 mb-1">
				<i class="fa fa-sitemap me-2"></i> <?php echo $category ? 'Editar' : 'Nueva'; ?> Categoría
			</h1>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-0">
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Dashboard</a></li>
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/categorias'); ?>">Categorías</a></li>
					<li class="breadcrumb-item active"><?php echo $category ? 'Editar' : 'Nueva'; ?></li>
				</ol>
			</nav>
		</div>
		<a href="<?php echo Uri::create('admin/categorias'); ?>" class="btn btn-outline-secondary">
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
						<h5 class="mb-0">Información General</h5>
					</div>
					<div class="card-body">
						<!-- Nombre -->
						<div class="mb-3">
							<label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
							<input type="text" 
								   class="form-control" 
								   id="name" 
								   name="name" 
								   value="<?php echo Input::post('name', $category ? $category->name : ''); ?>"
								   required
								   maxlength="100">
							<div class="form-text">Nombre descriptivo de la categoría</div>
						</div>

						<!-- Slug -->
						<div class="mb-3">
							<label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
							<input type="text" 
								   class="form-control" 
								   id="slug" 
								   name="slug" 
								   value="<?php echo Input::post('slug', $category ? $category->slug : ''); ?>"
								   required
								   maxlength="100"
								   pattern="[a-z0-9\-]+">
							<div class="form-text">URL amigable (solo letras minúsculas, números y guiones)</div>
						</div>

						<!-- Categoría Padre -->
						<div class="mb-3">
							<label for="parent_id" class="form-label">Categoría Padre</label>
							<select class="form-select" id="parent_id" name="parent_id">
								<option value="">Sin categoría padre (Categoría Principal)</option>
								<?php foreach ($parent_categories as $parent): ?>
									<option value="<?php echo $parent->id; ?>" 
											<?php echo Input::post('parent_id', $category ? $category->parent_id : '') == $parent->id ? 'selected' : ''; ?>>
										<?php echo htmlspecialchars($parent->name, ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<div class="form-text">Selecciona una categoría padre para crear una subcategoría</div>
						</div>

						<!-- Descripción -->
						<div class="mb-3">
							<label for="description" class="form-label">Descripción</label>
							<textarea class="form-control" 
									  id="description" 
									  name="description" 
									  rows="4"
									  maxlength="1000"><?php echo Input::post('description', $category ? $category->description : ''); ?></textarea>
							<div class="form-text">Descripción opcional de la categoría</div>
						</div>

						<div class="row">
							<!-- Orden -->
							<div class="col-md-6 mb-3">
								<label for="sort_order" class="form-label">Orden de visualización</label>
								<input type="number" 
									   class="form-control" 
									   id="sort_order" 
									   name="sort_order" 
									   value="<?php echo Input::post('sort_order', $category ? $category->sort_order : 0); ?>"
									   min="0"
									   max="9999">
								<div class="form-text">Orden de aparición (menor primero)</div>
							</div>

							<!-- Estado -->
							<div class="col-md-6 mb-3">
								<label for="is_active" class="form-label">Estado <span class="text-danger">*</span></label>
								<select class="form-select" id="is_active" name="is_active" required>
									<option value="1" <?php echo Input::post('is_active', $category ? $category->is_active : 1) == 1 ? 'selected' : ''; ?>>
										Activa
									</option>
									<option value="0" <?php echo Input::post('is_active', $category ? $category->is_active : 1) == 0 ? 'selected' : ''; ?>>
										Inactiva
									</option>
								</select>
								<div class="form-text">Las categorías inactivas no se mostrarán en el sitio</div>
							</div>
						</div>
					</div>
					<div class="card-footer bg-white">
						<button type="submit" class="btn btn-primary">
							<i class="fa fa-save me-1"></i> Guardar Categoría
						</button>
						<a href="<?php echo Uri::create('admin/categorias'); ?>" class="btn btn-outline-secondary">
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
						Ejemplo: "electronica", "ropa-deportiva"
					</p>

					<h6>Categorías Jerárquicas</h6>
					<p class="small text-muted">
						Puedes crear subcategorías seleccionando una categoría padre. 
						Ejemplo: "Computadoras" puede ser padre de "Laptops" y "Desktops".
					</p>

					<h6>Orden de Visualización</h6>
					<p class="small text-muted mb-0">
						Define el orden en que aparecerán las categorías. 
						Números más bajos aparecen primero.
					</p>
				</div>
			</div>

			<?php if ($category): ?>
				<div class="card border-0 shadow-sm mt-3">
					<div class="card-header bg-light">
						<h6 class="mb-0"><i class="fa fa-clock me-1"></i> Información</h6>
					</div>
					<div class="card-body">
						<div class="small">
							<p class="mb-2">
								<strong>Creada:</strong><br>
								<?php echo date('d/m/Y H:i', strtotime($category->created_at)); ?>
							</p>
							<?php if ($category->updated_at): ?>
								<p class="mb-0">
									<strong>Última actualización:</strong><br>
									<?php echo date('d/m/Y H:i', strtotime($category->updated_at)); ?>
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
