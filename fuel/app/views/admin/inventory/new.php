<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title mb-0">
					<i class="fas fa-box"></i> Nuevo Producto
				</h3>
			</div>

			<form action="" method="post">
				<?php echo Form::csrf(); ?>

				<div class="card-body">
					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label">SKU <span class="text-danger">*</span></label>
							<input type="text" name="sku" class="form-control" 
								   value="<?php echo 'PROD-' . time(); ?>" required>
						</div>

						<div class="col-md-6 mb-3">
							<label class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
							<input type="text" name="name" class="form-control" 
								   placeholder="Nombre del producto" required>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12 mb-3">
							<label class="form-label">Descripción</label>
							<textarea name="description" class="form-control" rows="3" 
									  placeholder="Descripción del producto"></textarea>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4 mb-3">
							<label class="form-label">Precio de Costo</label>
							<input type="number" name="cost_price" class="form-control" 
								   value="0" min="0" step="0.01">
						</div>

						<div class="col-md-4 mb-3">
							<label class="form-label">Precio de Venta <span class="text-danger">*</span></label>
							<input type="number" name="sale_price" class="form-control" 
								   value="0" min="0" step="0.01" required>
						</div>

						<div class="col-md-4 mb-3">
							<label class="form-label">Categoría</label>
							<select name="category_id" class="form-select">
								<option value="">Sin categoría</option>
								<!-- Aquí irían las categorías de la BD -->
							</select>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4 mb-3">
							<label class="form-label">Stock Inicial</label>
							<input type="number" name="stock_quantity" class="form-control" 
								   value="0" min="0" step="0.01">
						</div>

						<div class="col-md-4 mb-3">
							<label class="form-label">Stock Mínimo</label>
							<input type="number" name="min_stock" class="form-control" 
								   value="0" min="0" step="0.01">
						</div>

						<div class="col-md-4 mb-3">
							<label class="form-label">Estado</label>
							<select name="is_active" class="form-select">
								<option value="1">Activo</option>
								<option value="0">Inactivo</option>
							</select>
						</div>
					</div>
				</div>

				<div class="card-footer">
					<div class="d-flex justify-content-between">
						<a href="<?php echo Uri::create('admin/inventory'); ?>" class="btn btn-secondary">
							<i class="fas fa-arrow-left"></i> Cancelar
						</a>
						<button type="submit" class="btn btn-primary">
							<i class="fas fa-save"></i> Guardar Producto
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
