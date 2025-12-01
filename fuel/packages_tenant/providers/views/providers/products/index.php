<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-cube"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-success">
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-6">
						<h3 class="panel-title">Catálogo de Productos</h3>
					</div>
					<div class="col-md-6 text-right">
						<a href="<?php echo Uri::base(); ?>providers/products/agregar" class="btn btn-success btn-sm">
							<span class="glyphicon glyphicon-plus"></span> Agregar Producto
						</a>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th>SKU</th>
							<th>Nombre</th>
							<th>Categoría</th>
							<th>Precio</th>
							<th>Stock</th>
							<th>Estado</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($products)): ?>
						<tr>
							<td colspan="7" class="text-center text-muted">
								<span class="glyphicon glyphicon-cube" style="font-size: 32px;"></span>
								<p><em>No tienes productos registrados.</em></p>
								<a href="<?php echo Uri::base(); ?>providers/products/agregar" class="btn btn-success">
									<span class="glyphicon glyphicon-plus"></span> Agregar Primer Producto
								</a>
							</td>
						</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
