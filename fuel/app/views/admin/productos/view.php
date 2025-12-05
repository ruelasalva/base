<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h3 class="card-title mb-0">
					<i class="fas fa-box"></i> Detalle del Producto
				</h3>
				<div class="btn-group">
					<?php if (Helper_Permission::can('productos', 'edit')): ?>
						<a href="<?php echo Uri::create('admin/productos/edit/' . $product->id); ?>" class="btn btn-warning btn-sm">
							<i class="fas fa-edit"></i> Editar
						</a>
					<?php endif; ?>
					<a href="<?php echo Uri::create('admin/productos'); ?>" class="btn btn-secondary btn-sm">
						<i class="fas fa-arrow-left"></i> Volver
					</a>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<!-- Información Básica -->
					<div class="col-md-8">
						<div class="mb-4">
							<h4 class="border-bottom pb-2 mb-3">
								<i class="fas fa-info-circle text-primary"></i> Información Básica
							</h4>
							
							<table class="table table-borderless">
								<tr>
									<th width="200">SKU:</th>
									<td><span class="badge bg-secondary fs-6"><?php echo htmlspecialchars($product->sku, ENT_QUOTES, 'UTF-8'); ?></span></td>
								</tr>
								<?php if ($product->barcode): ?>
								<tr>
									<th>Código de Barras:</th>
									<td><code><?php echo htmlspecialchars($product->barcode, ENT_QUOTES, 'UTF-8'); ?></code></td>
								</tr>
								<?php endif; ?>
								<tr>
									<th>Nombre:</th>
									<td><strong class="fs-5"><?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></strong></td>
								</tr>
								<tr>
									<th>Slug:</th>
									<td><small class="text-muted"><?php echo htmlspecialchars($product->slug, ENT_QUOTES, 'UTF-8'); ?></small></td>
								</tr>
								<?php if ($product->short_description): ?>
								<tr>
									<th>Descripción Corta:</th>
									<td><?php echo nl2br(htmlspecialchars($product->short_description, ENT_QUOTES, 'UTF-8')); ?></td>
								</tr>
								<?php endif; ?>
								<?php if ($product->description): ?>
								<tr>
									<th>Descripción:</th>
									<td><?php echo nl2br(htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8')); ?></td>
								</tr>
								<?php endif; ?>
								<tr>
									<th>Categoría:</th>
									<td>
										<?php if ($product->category): ?>
											<span class="badge bg-info"><?php echo htmlspecialchars($product->category->name, ENT_QUOTES, 'UTF-8'); ?></span>
										<?php else: ?>
											<span class="text-muted">Sin categoría</span>
										<?php endif; ?>
									</td>
								</tr>
								<tr>
									<th>Proveedor:</th>
									<td>
										<?php if ($product->provider): ?>
											<?php echo htmlspecialchars($product->provider->company_name, ENT_QUOTES, 'UTF-8'); ?>
										<?php else: ?>
											<span class="text-muted">Sin proveedor</span>
										<?php endif; ?>
									</td>
								</tr>
								<?php if ($product->brand): ?>
								<tr>
									<th>Marca:</th>
									<td><?php echo htmlspecialchars($product->brand, ENT_QUOTES, 'UTF-8'); ?></td>
								</tr>
								<?php endif; ?>
								<?php if ($product->model): ?>
								<tr>
									<th>Modelo:</th>
									<td><?php echo htmlspecialchars($product->model, ENT_QUOTES, 'UTF-8'); ?></td>
								</tr>
								<?php endif; ?>
							</table>
						</div>

						<!-- Códigos Múltiples -->
						<?php if (isset($product->codigo_venta) || isset($product->codigo_compra) || isset($product->codigo_externo)): ?>
						<div class="mb-4">
							<h4 class="border-bottom pb-2 mb-3">
								<i class="fas fa-barcode text-info"></i> Códigos de Relación
							</h4>
							
							<table class="table table-borderless">
								<?php if (isset($product->codigo_venta) && $product->codigo_venta): ?>
								<tr>
									<th width="200">Código de Venta:</th>
									<td><code><?php echo htmlspecialchars($product->codigo_venta, ENT_QUOTES, 'UTF-8'); ?></code></td>
								</tr>
								<?php endif; ?>
								<?php if (isset($product->codigo_compra) && $product->codigo_compra): ?>
								<tr>
									<th>Código de Compra:</th>
									<td><code><?php echo htmlspecialchars($product->codigo_compra, ENT_QUOTES, 'UTF-8'); ?></code></td>
								</tr>
								<?php endif; ?>
								<?php if (isset($product->codigo_externo) && $product->codigo_externo): ?>
								<tr>
									<th>Código Externo:</th>
									<td><code><?php echo htmlspecialchars($product->codigo_externo, ENT_QUOTES, 'UTF-8'); ?></code></td>
								</tr>
								<?php endif; ?>
							</table>
						</div>
						<?php endif; ?>

						<!-- Precios -->
						<div class="mb-4">
							<h4 class="border-bottom pb-2 mb-3">
								<i class="fas fa-dollar-sign text-success"></i> Precios
							</h4>
							
							<div class="row">
								<div class="col-md-6 mb-3">
									<div class="card bg-light">
										<div class="card-body">
											<small class="text-muted d-block">Precio de Costo</small>
											<h4 class="mb-0">$<?php echo number_format($product->cost_price, 2); ?></h4>
										</div>
									</div>
								</div>

								<div class="col-md-6 mb-3">
									<div class="card bg-primary text-white">
										<div class="card-body">
											<small class="d-block opacity-75">Precio de Venta</small>
											<h4 class="mb-0">$<?php echo number_format($product->sale_price, 2); ?></h4>
										</div>
									</div>
								</div>

								<?php if ($product->wholesale_price > 0): ?>
								<div class="col-md-6 mb-3">
									<div class="card bg-light">
										<div class="card-body">
											<small class="text-muted d-block">Precio Mayorista</small>
											<h5 class="mb-0">$<?php echo number_format($product->wholesale_price, 2); ?></h5>
										</div>
									</div>
								</div>
								<?php endif; ?>

								<?php if ($product->min_price > 0): ?>
								<div class="col-md-6 mb-3">
									<div class="card bg-light">
										<div class="card-body">
											<small class="text-muted d-block">Precio Mínimo</small>
											<h5 class="mb-0">$<?php echo number_format($product->min_price, 2); ?></h5>
										</div>
									</div>
								</div>
								<?php endif; ?>
							</div>

							<?php if ($product->tax_rate > 0): ?>
							<div class="alert alert-info mb-0">
								<i class="fas fa-percentage"></i> 
								<strong>Impuesto:</strong> <?php echo number_format($product->tax_rate, 2); ?>%
								(Precio con impuesto: $<?php echo number_format($product->sale_price * (1 + $product->tax_rate / 100), 2); ?>)
							</div>
							<?php endif; ?>
						</div>

						<!-- Inventario -->
						<div class="mb-4">
							<h4 class="border-bottom pb-2 mb-3">
								<i class="fas fa-warehouse text-warning"></i> Inventario
							</h4>
							
							<div class="row">
								<div class="col-md-4">
									<div class="card text-center">
										<div class="card-body">
											<small class="text-muted d-block">Stock Actual</small>
											<?php 
												$stock = (int)$product->stock_quantity;
												$min_stock = (int)$product->min_stock;
												
												if ($stock <= 0) {
													$badge_class = 'bg-danger';
													$icon = 'fa-times-circle';
												} elseif ($min_stock > 0 && $stock <= $min_stock) {
													$badge_class = 'bg-warning text-dark';
													$icon = 'fa-exclamation-triangle';
												} else {
													$badge_class = 'bg-success';
													$icon = 'fa-check-circle';
												}
											?>
											<h2 class="mb-2">
												<span class="badge <?php echo $badge_class; ?>">
													<i class="fas <?php echo $icon; ?>"></i> <?php echo $stock; ?>
												</span>
											</h2>
											<?php if ($product->unit): ?>
												<small class="text-muted"><?php echo htmlspecialchars($product->unit, ENT_QUOTES, 'UTF-8'); ?></small>
											<?php endif; ?>
										</div>
									</div>
								</div>

								<div class="col-md-4">
									<div class="card text-center bg-light">
										<div class="card-body">
											<small class="text-muted d-block">Stock Mínimo</small>
											<h3 class="mb-0"><?php echo $min_stock; ?></h3>
										</div>
									</div>
								</div>

								<?php if ($product->weight > 0): ?>
								<div class="col-md-4">
									<div class="card text-center bg-light">
										<div class="card-body">
											<small class="text-muted d-block">Peso</small>
											<h4 class="mb-0"><?php echo number_format($product->weight, 2); ?> kg</h4>
										</div>
									</div>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<!-- Sidebar -->
					<div class="col-md-4">
						<!-- Estado -->
						<div class="card mb-3">
							<div class="card-header bg-light">
								<strong><i class="fas fa-toggle-on"></i> Estado</strong>
							</div>
							<div class="card-body">
								<div class="mb-2">
									<strong>Activo:</strong>
									<?php if ($product->is_active): ?>
										<span class="badge bg-success float-end">
											<i class="fas fa-check"></i> Sí
										</span>
									<?php else: ?>
										<span class="badge bg-secondary float-end">
											<i class="fas fa-times"></i> No
										</span>
									<?php endif; ?>
								</div>

								<div class="mb-2">
									<strong>Disponible:</strong>
									<?php if ($product->is_available): ?>
										<span class="badge bg-success float-end">
											<i class="fas fa-check"></i> Sí
										</span>
									<?php else: ?>
										<span class="badge bg-secondary float-end">
											<i class="fas fa-times"></i> No
										</span>
									<?php endif; ?>
								</div>

								<?php if ($product->is_featured): ?>
								<div>
									<strong>Destacado:</strong>
									<span class="badge bg-warning float-end">
										<i class="fas fa-star"></i> Sí
									</span>
								</div>
								<?php endif; ?>
							</div>
						</div>

						<!-- Fechas -->
						<div class="card">
							<div class="card-header bg-light">
								<strong><i class="fas fa-calendar"></i> Registro</strong>
							</div>
							<div class="card-body">
								<small class="text-muted d-block mb-1">Creado</small>
								<div class="mb-3">
									<?php echo date('d/m/Y H:i', strtotime($product->created_at)); ?>
								</div>

								<?php if ($product->updated_at && $product->updated_at != '0000-00-00 00:00:00'): ?>
								<small class="text-muted d-block mb-1">Última actualización</small>
								<div>
									<?php echo date('d/m/Y H:i', strtotime($product->updated_at)); ?>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
.card-body table th {
	font-weight: 600;
	color: #6c757d;
}

.badge.fs-6 {
	font-size: 1rem !important;
	padding: 0.5rem 1rem;
}
</style>
