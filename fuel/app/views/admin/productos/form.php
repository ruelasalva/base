<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title mb-0">
					<i class="fas fa-box"></i> <?php echo isset($product) && $product ? 'Editar Producto' : 'Nuevo Producto'; ?>
				</h3>
			</div>

			<?php echo Form::open(['class' => 'needs-validation', 'novalidate' => 'novalidate']); ?>
			
			<div class="card-body">
				<!-- Tabs -->
				<ul class="nav nav-tabs mb-4" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#info-basica" type="button">
							<i class="fas fa-info-circle"></i> Información Básica
						</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#codigos" type="button">
							<i class="fas fa-barcode"></i> Códigos
						</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#precios" type="button">
							<i class="fas fa-dollar-sign"></i> Precios
						</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#inventario" type="button">
							<i class="fas fa-warehouse"></i> Inventario
						</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" data-bs-toggle="tab" data-bs-target="#atributos" type="button">
							<i class="fas fa-tags"></i> Atributos/Filtros
						</button>
					</li>
				</ul>

				<div class="tab-content">
					<!-- Información Básica -->
					<div class="tab-pane fade show active" id="info-basica" role="tabpanel">
						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label required">SKU (Código Interno)</label>
									<?php echo Form::input('sku', Input::post('sku', isset($product) ? $product->sku : ''), ['class' => 'form-control', 'required' => true]); ?>
									<div class="invalid-feedback">El SKU es obligatorio.</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Código de Barras (EAN/UPC)</label>
									<?php echo Form::input('barcode', Input::post('barcode', isset($product) ? $product->barcode : ''), ['class' => 'form-control']); ?>
									<small class="text-muted">Código de barras estándar del producto</small>
								</div>
							</div>
						</div>

						<div class="mb-3">
							<label class="form-label required">Nombre del Producto</label>
							<?php echo Form::input('name', Input::post('name', isset($product) ? $product->name : ''), ['class' => 'form-control', 'required' => true]); ?>
							<div class="invalid-feedback">El nombre es obligatorio.</div>
						</div>

						<div class="mb-3">
							<label class="form-label">Descripción Corta</label>
							<?php echo Form::textarea('short_description', Input::post('short_description', isset($product) ? $product->short_description : ''), ['class' => 'form-control', 'rows' => 2]); ?>
							<small class="text-muted">Resumen breve (máximo 500 caracteres)</small>
						</div>

						<div class="mb-3">
							<label class="form-label">Descripción Completa</label>
							<?php echo Form::textarea('description', Input::post('description', isset($product) ? $product->description : ''), ['class' => 'form-control', 'rows' => 4]); ?>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Categoría</label>
									<select name="category_id" class="form-select">
										<option value="">-- Seleccionar --</option>
										<?php foreach ($categories as $category): ?>
											<?php 
												$selected = Input::post('category_id', isset($product) ? $product->category_id : '') == $category->id;
											?>
											<option value="<?php echo $category->id; ?>" <?php echo $selected ? 'selected' : ''; ?>>
												<?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>

							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Proveedor</label>
									<select name="provider_id" class="form-select">
										<option value="">-- Seleccionar --</option>
										<?php foreach ($providers as $provider): ?>
											<?php 
												$selected = Input::post('provider_id', isset($product) ? $product->provider_id : '') == $provider->id;
											?>
											<option value="<?php echo $provider->id; ?>" <?php echo $selected ? 'selected' : ''; ?>>
												<?php echo htmlspecialchars($provider->company_name, ENT_QUOTES, 'UTF-8'); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Marca</label>
									<?php echo Form::input('brand', Input::post('brand', isset($product) ? $product->brand : ''), ['class' => 'form-control']); ?>
								</div>
							</div>

							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Modelo</label>
									<?php echo Form::input('model', Input::post('model', isset($product) ? $product->model : ''), ['class' => 'form-control']); ?>
								</div>
							</div>
						</div>
					</div>

					<!-- Códigos Múltiples -->
					<div class="tab-pane fade" id="codigos" role="tabpanel">
						<div class="alert alert-info">
							<i class="fas fa-info-circle"></i>
							<strong>Códigos para Relaciones Flexibles:</strong> Estos códigos permiten relacionar el producto en otros módulos sin depender del ID.
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Código de Venta</label>
									<?php echo Form::input('codigo_venta', Input::post('codigo_venta', isset($product) ? $product->codigo_venta : ''), ['class' => 'form-control', 'placeholder' => 'Ej: VTA-001']); ?>
									<small class="text-muted">Código para facturación y ventas</small>
								</div>
							</div>

							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Código de Compra</label>
									<?php echo Form::input('codigo_compra', Input::post('codigo_compra', isset($product) ? $product->codigo_compra : ''), ['class' => 'form-control', 'placeholder' => 'Ej: COM-001']); ?>
									<small class="text-muted">Código para órdenes de compra</small>
								</div>
							</div>

							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">Código Externo</label>
									<?php echo Form::input('codigo_externo', Input::post('codigo_externo', isset($product) ? $product->codigo_externo : ''), ['class' => 'form-control', 'placeholder' => 'Ej: EXT-001']); ?>
									<small class="text-muted">Código para integraciones externas</small>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">SKU Principal</label>
									<input type="text" class="form-control" value="<?php echo isset($product) ? htmlspecialchars($product->sku, ENT_QUOTES, 'UTF-8') : '(Generado automáticamente)'; ?>" disabled>
									<small class="text-muted">Código interno único del sistema</small>
								</div>
							</div>

							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Código de Barras</label>
									<input type="text" class="form-control" value="<?php echo isset($product) && $product->barcode ? htmlspecialchars($product->barcode, ENT_QUOTES, 'UTF-8') : '(Opcional)'; ?>" disabled>
									<small class="text-muted">EAN/UPC configurado en Info Básica</small>
								</div>
							</div>
						</div>
					</div>

					<!-- Precios -->
					<div class="tab-pane fade" id="precios" role="tabpanel">
						<div class="alert alert-secondary">
							<i class="fas fa-lightbulb"></i>
							<strong>Precios Base:</strong> Define los precios principales. Las listas de precios personalizadas se configuran después de crear el producto.
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label required">Precio de Costo</label>
									<div class="input-group">
										<span class="input-group-text">$</span>
										<?php echo Form::input('cost_price', Input::post('cost_price', isset($product) ? $product->cost_price : '0.00'), ['class' => 'form-control', 'type' => 'number', 'step' => '0.01', 'required' => true]); ?>
									</div>
									<small class="text-muted">Costo de adquisición</small>
								</div>
							</div>

							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label required">Precio de Venta (Público)</label>
									<div class="input-group">
										<span class="input-group-text">$</span>
										<?php echo Form::input('sale_price', Input::post('sale_price', isset($product) ? $product->sale_price : '0.00'), ['class' => 'form-control', 'type' => 'number', 'step' => '0.01', 'required' => true]); ?>
									</div>
									<small class="text-muted">Precio al público general</small>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Precio Mayorista</label>
									<div class="input-group">
										<span class="input-group-text">$</span>
										<?php echo Form::input('wholesale_price', Input::post('wholesale_price', isset($product) ? $product->wholesale_price : '0.00'), ['class' => 'form-control', 'type' => 'number', 'step' => '0.01']); ?>
									</div>
									<small class="text-muted">Precio para venta al por mayor</small>
								</div>
							</div>

							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Precio Mínimo</label>
									<div class="input-group">
										<span class="input-group-text">$</span>
										<?php echo Form::input('min_price', Input::post('min_price', isset($product) ? $product->min_price : '0.00'), ['class' => 'form-control', 'type' => 'number', 'step' => '0.01']); ?>
									</div>
									<small class="text-muted">Precio mínimo permitido en descuentos</small>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Tasa de Impuesto (%)</label>
									<div class="input-group">
										<?php echo Form::input('tax_rate', Input::post('tax_rate', isset($product) ? $product->tax_rate : '16.00'), ['class' => 'form-control', 'type' => 'number', 'step' => '0.01']); ?>
										<span class="input-group-text">%</span>
									</div>
									<small class="text-muted">IVA u otro impuesto aplicable</small>
								</div>
							</div>
						</div>

						<?php if (isset($product) && $product): ?>
						<div class="alert alert-warning">
							<i class="fas fa-list-alt"></i>
							<strong>Listas de Precios Personalizadas:</strong> 
							<a href="<?php echo Uri::create('admin/productos/precios/' . $product->id); ?>" class="alert-link">
								Gestionar listas de precios especiales →
							</a>
						</div>
						<?php endif; ?>
					</div>

					<!-- Inventario -->
					<div class="tab-pane fade" id="inventario" role="tabpanel">
						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Stock Inicial</label>
									<?php echo Form::input('stock_quantity', Input::post('stock_quantity', isset($product) ? $product->stock_quantity : '0'), ['class' => 'form-control', 'type' => 'number', 'min' => '0']); ?>
									<small class="text-muted">Cantidad actual en inventario (puede ser 0)</small>
								</div>
							</div>

							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Stock Mínimo (Alertas)</label>
									<?php echo Form::input('min_stock', Input::post('min_stock', isset($product) ? $product->min_stock : '0'), ['class' => 'form-control', 'type' => 'number', 'min' => '0']); ?>
									<small class="text-muted">Alerta cuando el stock sea menor</small>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Unidad de Medida</label>
									<select name="unit" class="form-select">
										<?php 
											$units = ['Pieza', 'Caja', 'Paquete', 'Kilogramo', 'Gramo', 'Litro', 'Mililitro', 'Metro', 'Centímetro'];
											$current_unit = Input::post('unit', isset($product) ? $product->unit : '');
										?>
										<option value="">-- Seleccionar --</option>
										<?php foreach ($units as $unit): ?>
											<option value="<?php echo $unit; ?>" <?php echo $current_unit == $unit ? 'selected' : ''; ?>>
												<?php echo $unit; ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>

							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Peso (kg)</label>
									<div class="input-group">
										<?php echo Form::input('weight', Input::post('weight', isset($product) ? $product->weight : '0.00'), ['class' => 'form-control', 'type' => 'number', 'step' => '0.01']); ?>
										<span class="input-group-text">kg</span>
									</div>
									<small class="text-muted">Para cálculo de envíos</small>
								</div>
							</div>
						</div>
					</div>

					<!-- Atributos/Filtros -->
					<div class="tab-pane fade" id="atributos" role="tabpanel">
						<div class="alert alert-info">
							<i class="fas fa-filter"></i>
							<strong>Atributos de Filtrado:</strong> Similar a Mercado Libre, estos atributos permiten crear filtros dinámicos para búsquedas avanzadas.
						</div>

						<div class="mb-3">
							<label class="form-label">
								<i class="fas fa-tags"></i> Palabras Clave / Tags
							</label>
							<?php echo Form::textarea('tags', Input::post('tags', isset($product) && isset($product->tags) ? $product->tags : ''), [
								'class' => 'form-control', 
								'rows' => '3',
								'placeholder' => 'ropa, hombre, verano, algodón, casual, manga corta',
								'id' => 'tags-input'
							]); ?>
							<small class="text-muted d-block mt-1">
								<i class="fas fa-info-circle"></i> Separar con comas. Estas etiquetas se utilizan para:
							</small>
							<ul class="text-muted small mb-0">
								<li>Búsquedas de productos (el usuario puede encontrar este producto)</li>
								<li>Filtros dinámicos (se mostrarán como opciones de filtrado)</li>
								<li>SEO y recomendaciones automáticas</li>
							</ul>
							<div id="tags-preview" class="mt-2" style="min-height: 30px;"></div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="card bg-light">
									<div class="card-body py-2">
										<strong class="text-success"><i class="fas fa-check-circle"></i> Ejemplos de Tags Útiles:</strong>
										<div class="mt-2">
											<span class="badge bg-primary me-1">color-azul</span>
											<span class="badge bg-primary me-1">talla-M</span>
											<span class="badge bg-primary me-1">material-algodón</span>
											<span class="badge bg-primary me-1">temporada-verano</span>
											<span class="badge bg-primary me-1">marca-nike</span>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="card bg-light">
									<div class="card-body py-2">
										<strong class="text-warning"><i class="fas fa-lightbulb"></i> Tips:</strong>
										<ul class="small mb-0 mt-2">
											<li>Usa palabras simples y descriptivas</li>
											<li>Incluye variaciones (polo, playera, remera)</li>
											<li>Considera sinónimos comunes</li>
										</ul>
									</div>
								</div>
							</div>
						</div>

						<div class="alert alert-secondary mt-3">
							<i class="fas fa-wrench"></i>
							<strong>En Desarrollo:</strong> Sistema de atributos estructurados (color, talla, material) con valores predefinidos y filtros avanzados estilo Mercado Libre.
						</div>
					</div>
				</div>
			</div>

			<div class="card-footer d-flex justify-content-between">
				<a href="<?php echo Uri::create('admin/productos'); ?>" class="btn btn-secondary">
					<i class="fas fa-arrow-left"></i> Cancelar
				</a>
				<button type="submit" class="btn btn-primary">
					<i class="fas fa-save"></i> <?php echo isset($product) && $product ? 'Actualizar' : 'Crear'; ?> Producto
				</button>
			</div>

			<?php echo Form::close(); ?>
		</div>
	</div>
</div>

<style>
.required:after {
	content: " *";
	color: #dc3545;
}

.nav-tabs .nav-link {
	color: #6c757d;
}

.nav-tabs .nav-link.active {
	font-weight: 600;
}

.input-group-text {
	min-width: 45px;
	justify-content: center;
}
</style>

<script>
// Inicializar tabs (compatible con Bootstrap 4 y 5)
document.addEventListener('DOMContentLoaded', function() {
	// Método para Bootstrap 5
	if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
		var triggerTabList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tab"]'))
		triggerTabList.forEach(function (triggerEl) {
			new bootstrap.Tab(triggerEl)
		})
	}
	// Método para Bootstrap 4 con jQuery
	else if (typeof $ !== 'undefined' && $.fn.tab) {
		$('[data-bs-toggle="tab"], [data-toggle="tab"]').on('click', function (e) {
			e.preventDefault()
			$(this).tab('show')
		})
	}
	// Método manual si Bootstrap no está disponible
	else {
		var tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]')
		tabButtons.forEach(function(button) {
			button.addEventListener('click', function(e) {
				e.preventDefault()
				
				// Remover active de todos los botones y panes
				var allButtons = document.querySelectorAll('[data-bs-toggle="tab"]')
				var allPanes = document.querySelectorAll('.tab-pane')
				
				allButtons.forEach(function(btn) {
					btn.classList.remove('active')
				})
				allPanes.forEach(function(pane) {
					pane.classList.remove('show', 'active')
				})
				
				// Activar el botón y pane actual
				button.classList.add('active')
				var targetId = button.getAttribute('data-bs-target')
				var targetPane = document.querySelector(targetId)
				if (targetPane) {
					targetPane.classList.add('show', 'active')
				}
			})
		})
	}
	
	// Vista previa de tags en tiempo real
	var tagsInput = document.getElementById('tags-input')
	var tagsPreview = document.getElementById('tags-preview')
	
	if (tagsInput && tagsPreview) {
		function updateTagsPreview() {
			var tags = tagsInput.value.split(',').map(function(tag) {
				return tag.trim()
			}).filter(function(tag) {
				return tag.length > 0
			})
			
			if (tags.length > 0) {
				tagsPreview.innerHTML = '<strong class="text-muted small">Vista previa:</strong><br>' + 
					tags.map(function(tag) {
						return '<span class="badge bg-info text-dark me-1 mb-1">' + tag + '</span>'
					}).join('')
			} else {
				tagsPreview.innerHTML = '<span class="text-muted small"><i>Los tags aparecerán aquí mientras escribes...</i></span>'
			}
		}
		
		// Actualizar al cargar si hay tags
		updateTagsPreview()
		
		// Actualizar mientras escribe
		tagsInput.addEventListener('input', updateTagsPreview)
	}
})

// Validación Bootstrap
(function () {
	'use strict'
	var forms = document.querySelectorAll('.needs-validation')
	Array.prototype.slice.call(forms).forEach(function (form) {
		form.addEventListener('submit', function (event) {
			if (!form.checkValidity()) {
				event.preventDefault()
				event.stopPropagation()
			}
			form.classList.add('was-validated')
		}, false)
	})
})()
</script>
