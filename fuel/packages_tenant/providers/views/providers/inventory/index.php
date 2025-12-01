<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-list-alt"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-3">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title">Stock Total</h3>
			</div>
			<div class="panel-body text-center">
				<h2>0</h2>
				<p class="text-muted">unidades</p>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h3 class="panel-title">Stock Bajo</h3>
			</div>
			<div class="panel-body text-center">
				<h2>0</h2>
				<p class="text-muted">productos</p>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-danger">
			<div class="panel-heading">
				<h3 class="panel-title">Sin Stock</h3>
			</div>
			<div class="panel-body text-center">
				<h2>0</h2>
				<p class="text-muted">productos</p>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Productos</h3>
			</div>
			<div class="panel-body text-center">
				<h2>0</h2>
				<p class="text-muted">total</p>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title">Control de Inventario</h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th>SKU</th>
							<th>Producto</th>
							<th>Stock Actual</th>
							<th>Stock Mínimo</th>
							<th>Estado</th>
							<th>Última Actualización</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($inventory)): ?>
						<tr>
							<td colspan="7" class="text-center text-muted">
								<span class="glyphicon glyphicon-list-alt" style="font-size: 32px;"></span>
								<p><em>No hay productos en el inventario.</em></p>
								<a href="<?php echo Uri::base(); ?>providers/products/agregar" class="btn btn-success">
									<span class="glyphicon glyphicon-plus"></span> Agregar Producto
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
