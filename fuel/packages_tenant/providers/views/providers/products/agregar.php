<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-plus"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title">Información del Producto</h3>
			</div>
			<div class="panel-body">
				<form method="post" action="<?php echo Uri::base(); ?>providers/products/crear" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="sku">SKU</label>
								<input type="text" class="form-control" id="sku" name="sku" placeholder="Código único del producto" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="categoria">Categoría</label>
								<select class="form-control" id="categoria" name="categoria_id">
									<option value="">Seleccionar categoría...</option>
								</select>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="nombre">Nombre del Producto</label>
						<input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del producto" required>
					</div>
					<div class="form-group">
						<label for="descripcion">Descripción</label>
						<textarea class="form-control" id="descripcion" name="descripcion" rows="4" placeholder="Descripción detallada del producto..."></textarea>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="precio">Precio</label>
								<div class="input-group">
									<span class="input-group-addon">$</span>
									<input type="number" step="0.01" class="form-control" id="precio" name="precio" placeholder="0.00" required>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="stock">Stock Inicial</label>
								<input type="number" class="form-control" id="stock" name="stock" value="0" min="0">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="stock_minimo">Stock Mínimo</label>
								<input type="number" class="form-control" id="stock_minimo" name="stock_minimo" value="0" min="0">
							</div>
						</div>
					</div>
					<hr>
					<div class="form-group">
						<button type="submit" class="btn btn-success">
							<span class="glyphicon glyphicon-ok"></span> Guardar Producto
						</button>
						<a href="<?php echo Uri::base(); ?>providers/products" class="btn btn-default">
							<span class="glyphicon glyphicon-remove"></span> Cancelar
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
