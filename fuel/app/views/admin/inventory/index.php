<!-- HEADER -->
<div class="row mb-4">
	<div class="col-md-12">
		<div class="d-flex justify-content-between align-items-center">
			<div>
				<h2 class="mb-1"><i class="fas fa-boxes me-2"></i>Inventario</h2>
				<p class="text-muted mb-0">Gestión de productos y stock</p>
			</div>
			<?php if ($can_create): ?>
			<div>
				<a href="<?php echo Uri::create('admin/inventory/new'); ?>" class="btn btn-primary">
					<i class="fas fa-plus me-2"></i>Nuevo Producto
				</a>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<!-- STATS -->
<div class="row mb-4">
	<div class="col-md-4">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div>
						<h6 class="text-muted mb-2">Total Productos</h6>
						<h3 class="mb-0"><?php echo number_format($stats['total_products']); ?></h3>
					</div>
					<div class="text-primary">
						<i class="fas fa-boxes fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div>
						<h6 class="text-muted mb-2">Valor Total</h6>
						<h3 class="mb-0">$<?php echo number_format($stats['total_value'], 2); ?></h3>
					</div>
					<div class="text-success">
						<i class="fas fa-dollar-sign fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card border-warning">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div>
						<h6 class="text-muted mb-2">Stock Bajo</h6>
						<h3 class="mb-0 text-warning"><?php echo $stats['low_stock']; ?></h3>
					</div>
					<div class="text-warning">
						<i class="fas fa-exclamation-triangle fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- LISTA DE PRODUCTOS -->
<div class="card">
	<div class="card-header">
		<div class="d-flex justify-content-between align-items-center">
			<h5 class="mb-0">Productos</h5>
			<div class="input-group" style="width: 300px;">
				<input type="text" class="form-control" placeholder="Buscar producto..." id="searchProduct">
				<button class="btn btn-outline-secondary" type="button">
					<i class="fas fa-search"></i>
				</button>
			</div>
		</div>
	</div>
	<div class="card-body">
		<?php if (count($products) > 0): ?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>SKU</th>
						<th>Producto</th>
						<th>Categoría</th>
						<th>Precio</th>
						<th>Costo</th>
						<th>Stock</th>
						<th>Estado</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($products as $product): ?>
					<tr class="<?php echo ($product['stock_quantity'] <= $product['min_stock']) ? 'table-warning' : ''; ?>">
						<td><strong><?php echo htmlspecialchars($product['sku']); ?></strong></td>
						<td><?php echo htmlspecialchars($product['name']); ?></td>
						<td><?php echo htmlspecialchars($product['category_id'] ?? 'Sin categoría'); ?></td>
						<td>$<?php echo number_format($product['sale_price'], 2); ?></td>
						<td>$<?php echo number_format($product['cost_price'], 2); ?></td>
						<td>
							<strong><?php echo $product['stock_quantity']; ?></strong>
							<?php if ($product['stock_quantity'] <= $product['min_stock']): ?>
							<i class="fas fa-exclamation-triangle text-warning ms-1" title="Stock bajo"></i>
							<?php endif; ?>
						</td>
						<td>
							<?php if ($product['is_active']): ?>
							<span class="badge bg-success">Activo</span>
							<?php else: ?>
							<span class="badge bg-secondary">Inactivo</span>
							<?php endif; ?>
						</td>
						<td>
							<a href="<?php echo Uri::create('admin/inventory/view/' . $product['id']); ?>" class="btn btn-sm btn-info" title="Ver">
								<i class="fas fa-eye"></i>
							</a>
							<?php if ($can_edit): ?>
							<a href="<?php echo Uri::create('admin/inventory/edit/' . $product['id']); ?>" class="btn btn-sm btn-warning" title="Editar">
								<i class="fas fa-edit"></i>
							</a>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php else: ?>
		<div class="alert alert-info">
			<i class="fas fa-info-circle me-2"></i>No hay productos en el inventario.
			<?php if ($can_create): ?>
			<a href="<?php echo Uri::create('admin/inventory/new'); ?>">Agregar el primer producto</a>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</div>
