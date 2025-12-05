<div class="container-fluid py-4">
	<div class="row">
		<div class="col-md-8 offset-md-2">
			<!-- Encabezado -->
			<div class="mb-4">
				<h2 class="mb-1" style="color: #1f2937; font-weight: 600;">
					<i class="fas fa-plus-circle mr-2" style="color: #4f46e5;"></i>
					Crear Nuevo Producto
				</h2>
				<p class="text-muted mb-0">Complete el formulario para registrar un nuevo producto</p>
			</div>

			<form action="<?php echo Uri::create('admin/inventory/products/create'); ?>" method="post">

				<!-- Información Básica -->
				<div class="card shadow-sm border-0 mb-4">
					<div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
						<h5 class="mb-0" style="color: #1f2937; font-weight: 600;">Información Básica</h5>
					</div>
					<div class="card-body" style="padding: 1.5rem;">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label style="color: #374151; font-weight: 500;">Código del Producto <span class="text-danger">*</span></label>
									<input type="text" 
										   name="code" 
										   class="form-control" 
										   style="border: 1px solid #d1d5db;"
										   value="<?php echo Input::post('code', $suggested_code); ?>" 
										   required>
									<small class="form-text text-muted">Código único del producto (SKU). Sugerido: <?php echo $suggested_code; ?></small>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label style="color: #374151; font-weight: 500;">Código de Barras</label>
									<input type="text" 
										   name="barcode" 
										   class="form-control" 
										   style="border: 1px solid #d1d5db;"
										   value="<?php echo Input::post('barcode'); ?>">
									<small class="form-text text-muted">Código de barras del producto (opcional)</small>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label style="color: #374151; font-weight: 500;">Nombre del Producto <span class="text-danger">*</span></label>
							<input type="text" 
								   name="name" 
								   class="form-control" 
								   style="border: 1px solid #d1d5db;"
								   value="<?php echo Input::post('name'); ?>" 
								   required>
						</div>

						<div class="form-group">
							<label style="color: #374151; font-weight: 500;">Descripción</label>
							<textarea name="description" 
									  class="form-control" 
									  style="border: 1px solid #d1d5db;"
									  rows="4"><?php echo Input::post('description'); ?></textarea>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label style="color: #374151; font-weight: 500;">Categoría</label>
									<?php echo Form::select('category_id', Input::post('category_id'), $categories, array('class' => 'form-control', 'style' => 'border: 1px solid #d1d5db;')); ?>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label style="color: #374151; font-weight: 500;">Unidad de Medida <span class="text-danger">*</span></label>
									<?php echo Form::select('unit_of_measure', Input::post('unit_of_measure', 'PZA'), $units, array('class' => 'form-control', 'style' => 'border: 1px solid #d1d5db;', 'required' => 'required')); ?>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Precios y Costos -->
				<div class="card shadow-sm border-0 mb-4">
					<div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
						<h5 class="mb-0" style="color: #1f2937; font-weight: 600;">Precios y Costos</h5>
					</div>
					<div class="card-body" style="padding: 1.5rem;">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label style="color: #374151; font-weight: 500;">Precio de Venta <span class="text-danger">*</span></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text" style="background: #f9fafb; border: 1px solid #d1d5db;">$</span>
										</div>
										<input type="number" 
											   name="unit_price" 
											   class="form-control" 
											   style="border: 1px solid #d1d5db;"
											   step="0.01" 
											   min="0" 
											   value="<?php echo Input::post('unit_price', 0); ?>" 
											   required>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label style="color: #374151; font-weight: 500;">Costo <span class="text-danger">*</span></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text" style="background: #f9fafb; border: 1px solid #d1d5db;">$</span>
										</div>
										<input type="number" 
											   name="cost" 
											   class="form-control" 
											   style="border: 1px solid #d1d5db;"
											   step="0.01" 
											   min="0" 
											   value="<?php echo Input::post('cost', 0); ?>" 
											   required>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label style="color: #374151; font-weight: 500;">Tasa de IVA (%)</label>
									<input type="number" 
										   name="tax_rate" 
										   class="form-control" 
										   style="border: 1px solid #d1d5db;"
										   step="0.01" 
										   min="0" 
										   max="100" 
										   value="<?php echo Input::post('tax_rate', 16); ?>">
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Inventario -->
				<div class="card shadow-sm border-0 mb-4">
					<div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
						<h5 class="mb-0" style="color: #1f2937; font-weight: 600;">Control de Inventario</h5>
					</div>
					<div class="card-body" style="padding: 1.5rem;">
						<div class="form-group">
							<div class="custom-control custom-checkbox">
								<input type="checkbox" 
									   class="custom-control-input" 
									   id="is_service" 
									   name="is_service" 
									   value="1" 
									   <?php echo Input::post('is_service') ? 'checked' : ''; ?>>
								<label class="custom-control-label" for="is_service" style="color: #374151; font-weight: 500;">
									Este es un servicio (no requiere control de inventario)
								</label>
							</div>
						</div>

						<div id="stock_fields">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label style="color: #374151; font-weight: 500;">Stock Inicial</label>
										<input type="number" 
											   name="stock" 
											   class="form-control" 
											   style="border: 1px solid #d1d5db;"
											   step="0.01" 
											   min="0" 
											   value="<?php echo Input::post('stock', 0); ?>">
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label style="color: #374151; font-weight: 500;">Stock Mínimo</label>
										<input type="number" 
											   name="min_stock" 
											   class="form-control" 
											   style="border: 1px solid #d1d5db;"
											   step="0.01" 
											   min="0" 
											   value="<?php echo Input::post('min_stock'); ?>">
										<small class="form-text text-muted">Alerta cuando el stock sea menor o igual a este valor</small>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label style="color: #374151; font-weight: 500;">Stock Máximo</label>
										<input type="number" 
											   name="max_stock" 
											   class="form-control" 
											   style="border: 1px solid #d1d5db;"
											   step="0.01" 
											   min="0" 
											   value="<?php echo Input::post('max_stock'); ?>">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Estado -->
				<div class="card shadow-sm border-0 mb-4">
					<div class="card-header bg-white border-bottom" style="padding: 1rem 1.5rem;">
						<h5 class="mb-0" style="color: #1f2937; font-weight: 600;">Estado</h5>
					</div>
					<div class="card-body" style="padding: 1.5rem;">
						<div class="form-group mb-0">
							<div class="custom-control custom-checkbox">
								<input type="checkbox" 
									   class="custom-control-input" 
									   id="is_active" 
									   name="is_active" 
									   value="1" 
									   <?php echo Input::post('is_active', 1) ? 'checked' : ''; ?>>
								<label class="custom-control-label" for="is_active" style="color: #374151; font-weight: 500;">
									Producto activo
								</label>
								<small class="form-text text-muted">Los productos inactivos no aparecerán en ventas ni reportes</small>
							</div>
						</div>
					</div>
				</div>

				<!-- Botones -->
				<div class="d-flex justify-content-between">
					<a href="<?php echo Uri::create('admin/inventory/products'); ?>" 
					   class="btn btn-light shadow-sm">
						<i class="fas fa-arrow-left mr-1"></i> Cancelar
					</a>
					<button type="submit" 
							class="btn shadow-sm" 
							style="background: #4f46e5; color: white;">
						<i class="fas fa-save mr-1"></i> Guardar Producto
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	// Mostrar/ocultar campos de stock según tipo
	$('#is_service').on('change', function() {
		if ($(this).is(':checked')) {
			$('#stock_fields').hide();
		} else {
			$('#stock_fields').show();
		}
	}).trigger('change');
});
</script>
