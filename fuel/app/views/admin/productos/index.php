<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h3 class="card-title mb-0">
					<i class="fas fa-box"></i> Productos
				</h3>
				<?php if (Helper_Permission::can('productos', 'create')): ?>
					<a href="<?php echo Uri::create('admin/productos/create'); ?>" class="btn btn-primary">
						<i class="fas fa-plus"></i> Nuevo Producto
					</a>
				<?php endif; ?>
			</div>

			<div class="card-body">
				<!-- Búsqueda -->
				<form method="get" class="mb-3">
					<div class="input-group">
						<input type="text" name="search" class="form-control" placeholder="Buscar por nombre, SKU, código de barras o marca..." value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
						<button type="submit" class="btn btn-secondary">
							<i class="fas fa-search"></i> Buscar
						</button>
						<?php if (!empty($search)): ?>
							<a href="<?php echo Uri::create('admin/productos'); ?>" class="btn btn-outline-secondary">
								<i class="fas fa-times"></i> Limpiar
							</a>
						<?php endif; ?>
					</div>
				</form>

				<!-- Tabla de productos -->
				<div class="table-responsive">
					<table class="table table-hover table-striped">
						<thead class="table-light">
							<tr>
								<th width="80">ID</th>
								<th width="100">SKU</th>
								<th>Producto</th>
								<th>Categoría</th>
								<th>Marca</th>
								<th width="120" class="text-end">Precio Venta</th>
								<th width="100" class="text-center">Stock</th>
								<th width="80" class="text-center">Estado</th>
								<th width="150" class="text-center">Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php if (count($products)): ?>
								<?php foreach ($products as $product): ?>
									<tr>
										<td><?php echo $product->id; ?></td>
										<td>
											<span class="badge bg-secondary"><?php echo htmlspecialchars($product->sku, ENT_QUOTES, 'UTF-8'); ?></span>
										</td>
										<td>
											<strong><?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></strong>
											<?php if ($product->barcode): ?>
												<br><small class="text-muted">
													<i class="fas fa-barcode"></i> <?php echo htmlspecialchars($product->barcode, ENT_QUOTES, 'UTF-8'); ?>
												</small>
											<?php endif; ?>
										</td>
										<td>
											<?php if ($product->category): ?>
												<span class="badge bg-info"><?php echo htmlspecialchars($product->category->name, ENT_QUOTES, 'UTF-8'); ?></span>
											<?php else: ?>
												<span class="text-muted">Sin categoría</span>
											<?php endif; ?>
										</td>
										<td>
											<?php echo $product->brand ? htmlspecialchars($product->brand, ENT_QUOTES, 'UTF-8') : '<span class="text-muted">-</span>'; ?>
										</td>
										<td class="text-end">
											<strong>$<?php echo number_format($product->sale_price, 2); ?></strong>
										</td>
										<td class="text-center">
											<?php 
												$stock = (int)$product->stock_quantity;
												$min_stock = (int)$product->min_stock;
												
												if ($stock <= 0) {
													$badge_class = 'bg-danger';
												} elseif ($min_stock > 0 && $stock <= $min_stock) {
													$badge_class = 'bg-warning text-dark';
												} else {
													$badge_class = 'bg-success';
												}
											?>
											<span class="badge <?php echo $badge_class; ?>">
												<?php echo $stock; ?>
											</span>
										</td>
										<td class="text-center">
											<?php if ($product->is_active): ?>
												<span class="badge bg-success">
													<i class="fas fa-check"></i> Activo
												</span>
											<?php else: ?>
												<span class="badge bg-secondary">
													<i class="fas fa-times"></i> Inactivo
												</span>
											<?php endif; ?>
										</td>
										<td class="text-center">
											<div class="btn-group btn-group-sm" role="group">
												<?php if (Helper_Permission::can('productos', 'view')): ?>
													<a href="<?php echo Uri::create('admin/productos/view/' . $product->id); ?>" 
													   class="btn btn-info" 
													   title="Ver detalles">
														<i class="fas fa-eye"></i>
													</a>
												<?php endif; ?>

												<?php if (Helper_Permission::can('productos', 'edit')): ?>
													<a href="<?php echo Uri::create('admin/productos/edit/' . $product->id); ?>" 
													   class="btn btn-warning" 
													   title="Editar">
														<i class="fas fa-edit"></i>
													</a>
												<?php endif; ?>

												<?php if (Helper_Permission::can('productos', 'delete')): ?>
													<a href="<?php echo Uri::create('admin/productos/delete/' . $product->id); ?>" 
													   class="btn btn-danger" 
													   title="Eliminar"
													   onclick="return confirm('¿Estás seguro de eliminar este producto?');">
														<i class="fas fa-trash"></i>
													</a>
												<?php endif; ?>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="9" class="text-center py-4">
										<i class="fas fa-inbox fa-3x text-muted mb-3"></i>
										<p class="text-muted mb-0">
											<?php echo !empty($search) ? 'No se encontraron productos con ese criterio.' : 'No hay productos registrados.'; ?>
										</p>
									</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>

				<!-- Paginación -->
				<?php if ($pagination->total_pages > 1): ?>
					<div class="d-flex justify-content-between align-items-center mt-3">
						<div class="text-muted">
							Mostrando <?php echo $pagination->offset + 1; ?> - 
							<?php echo min($pagination->offset + $pagination->per_page, $pagination->total_items); ?> 
							de <?php echo $pagination->total_items; ?> productos
						</div>
						<nav>
							<?php echo $pagination->render(); ?>
						</nav>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<style>
.table-hover tbody tr:hover {
	background-color: rgba(0,0,0,.02);
}

.btn-group-sm > .btn {
	padding: .25rem .5rem;
	font-size: .875rem;
}

.badge {
	font-weight: 500;
}
</style>
