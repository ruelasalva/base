<div class="container-fluid py-4">
	<!-- Encabezado -->
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h2 class="mb-1" style="color: #1f2937; font-weight: 600;">
				<i class="fas fa-boxes mr-2" style="color: #4f46e5;"></i>
				Productos de Inventario
			</h2>
			<p class="text-muted mb-0">Gestión de productos, stock y precios</p>
		</div>
		<div>
			<?php if (Helper_Permission::can('inventory_products', 'create')): ?>
				<a href="<?php echo Uri::create('admin/inventory/products/create'); ?>" 
				   class="btn shadow-sm mr-2" 
				   style="background: #4f46e5; color: white;">
					<i class="fas fa-plus mr-1"></i> Nuevo Producto
				</a>
			<?php endif; ?>
			<a href="<?php echo Uri::create('admin/inventory/products/export'); ?>" 
			   class="btn shadow-sm mr-2" 
			   style="background: #10b981; color: white;">
				<i class="fas fa-file-excel mr-1"></i> Exportar
			</a>
			<a href="<?php echo Uri::create('admin/inventory/products/low_stock'); ?>" 
			   class="btn shadow-sm" 
			   style="background: #f59e0b; color: white;">
				<i class="fas fa-exclamation-triangle mr-1"></i> Stock Bajo
			</a>
		</div>
	</div>

	<!-- Estadísticas -->
	<div class="row mb-4">
		<div class="col-md-3">
			<div class="card shadow-sm border-0" style="border-left: 4px solid #4f46e5 !important;">
				<div class="card-body py-3">
					<div class="d-flex justify-content-between align-items-center">
						<div>
							<p class="text-muted mb-1" style="font-size: 0.875rem;">Total Productos</p>
							<h3 class="mb-0" style="color: #1f2937; font-weight: 600;"><?php echo number_format($stats['total']); ?></h3>
						</div>
						<div style="background: #eef2ff; width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
							<i class="fas fa-boxes" style="color: #4f46e5; font-size: 1.5rem;"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card shadow-sm border-0" style="border-left: 4px solid #10b981 !important;">
				<div class="card-body py-3">
					<div class="d-flex justify-content-between align-items-center">
						<div>
							<p class="text-muted mb-1" style="font-size: 0.875rem;">Productos Activos</p>
							<h3 class="mb-0" style="color: #1f2937; font-weight: 600;"><?php echo number_format($stats['active']); ?></h3>
						</div>
						<div style="background: #d1fae5; width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
							<i class="fas fa-check-circle" style="color: #10b981; font-size: 1.5rem;"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card shadow-sm border-0" style="border-left: 4px solid #f59e0b !important;">
				<div class="card-body py-3">
					<div class="d-flex justify-content-between align-items-center">
						<div>
							<p class="text-muted mb-1" style="font-size: 0.875rem;">Stock Bajo</p>
							<h3 class="mb-0" style="color: #1f2937; font-weight: 600;"><?php echo number_format($stats['low_stock']); ?></h3>
						</div>
						<div style="background: #fef3c7; width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
							<i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 1.5rem;"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card shadow-sm border-0" style="border-left: 4px solid #ef4444 !important;">
				<div class="card-body py-3">
					<div class="d-flex justify-content-between align-items-center">
						<div>
							<p class="text-muted mb-1" style="font-size: 0.875rem;">Sin Stock</p>
							<h3 class="mb-0" style="color: #1f2937; font-weight: 600;"><?php echo number_format($stats['out_of_stock']); ?></h3>
						</div>
						<div style="background: #fee2e2; width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
							<i class="fas fa-times-circle" style="color: #ef4444; font-size: 1.5rem;"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Búsqueda -->
	<div class="card shadow-sm border-0 mb-4">
		<div class="card-body py-3">
			<form action="<?php echo Uri::create('admin/inventory/products/index'); ?>" method="get" class="form-inline">
				<div class="input-group" style="width: 400px;">
					<input type="text" 
						   name="search" 
						   class="form-control border-0" 
						   style="background: #f9fafb;" 
						   placeholder="Buscar por código, nombre o código de barras..." 
						   value="<?php echo Html::chars($search); ?>">
					<div class="input-group-append">
						<button type="submit" class="btn" style="background: #4f46e5; color: white;">
							<i class="fas fa-search"></i>
						</button>
					</div>
				</div>
				<?php if ($search): ?>
					<a href="<?php echo Uri::create('admin/inventory/products'); ?>" class="btn btn-light ml-2">
						<i class="fas fa-times"></i> Limpiar
					</a>
				<?php endif; ?>
			</form>
		</div>
	</div>

	<!-- Tabla de productos -->
	<div class="card shadow-sm border-0">
		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="table table-hover mb-0" id="productsTable">
					<thead style="background: #f9fafb;">
						<tr>
							<th class="border-0" style="color: #6b7280; font-weight: 600; padding: 1rem;">Código</th>
							<th class="border-0" style="color: #6b7280; font-weight: 600;">Nombre</th>
							<th class="border-0" style="color: #6b7280; font-weight: 600;">Categoría</th>
							<th class="border-0" style="color: #6b7280; font-weight: 600; text-align: right;">Precio</th>
							<th class="border-0" style="color: #6b7280; font-weight: 600; text-align: right;">Costo</th>
							<th class="border-0" style="color: #6b7280; font-weight: 600; text-align: center;">Stock</th>
							<th class="border-0" style="color: #6b7280; font-weight: 600; text-align: center;">Estado</th>
							<th class="border-0" style="color: #6b7280; font-weight: 600; text-align: center;">Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($products)): ?>
							<tr>
								<td colspan="8" class="text-center py-5 text-muted">
									<i class="fas fa-inbox fa-3x mb-3" style="color: #d1d5db;"></i>
									<p class="mb-0">No hay productos registrados</p>
								</td>
							</tr>
						<?php else: ?>
							<?php foreach ($products as $product): ?>
								<tr style="border-bottom: 1px solid #f3f4f6;">
									<td class="align-middle" style="padding: 1rem;">
										<span class="font-weight-bold" style="color: #4f46e5;"><?php echo Html::chars($product->code); ?></span>
										<?php if ($product->barcode): ?>
											<br><small class="text-muted"><?php echo Html::chars($product->barcode); ?></small>
										<?php endif; ?>
									</td>
									<td class="align-middle">
										<div style="font-weight: 500; color: #1f2937;"><?php echo Html::chars($product->name); ?></div>
										<?php if ($product->is_service): ?>
											<small><span class="badge badge-info">Servicio</span></small>
										<?php endif; ?>
									</td>
									<td class="align-middle">
										<?php if ($product->category): ?>
											<span class="badge badge-secondary"><?php echo Html::chars($product->category->name); ?></span>
										<?php else: ?>
											<span class="text-muted">Sin categoría</span>
										<?php endif; ?>
									</td>
									<td class="align-middle text-right">
										<span style="font-weight: 500; color: #059669;">
											<?php echo Helper_Inventory_Product::format_price($product->unit_price); ?>
										</span>
									</td>
									<td class="align-middle text-right">
										<span class="text-muted">
											<?php echo Helper_Inventory_Product::format_price($product->cost); ?>
										</span>
									</td>
									<td class="align-middle text-center">
										<?php if (!$product->is_service): ?>
											<span style="font-weight: 600; color: #1f2937;"><?php echo number_format($product->stock, 2); ?></span>
											<small class="text-muted d-block"><?php echo Html::chars($product->unit_of_measure); ?></small>
										<?php else: ?>
											<span class="text-muted">N/A</span>
										<?php endif; ?>
									</td>
									<td class="align-middle text-center">
										<?php echo Helper_Inventory_Product::get_stock_badge($product->stock, $product->min_stock, $product->is_service); ?>
										<?php if (!$product->is_active): ?>
											<br><span class="badge badge-danger mt-1">Inactivo</span>
										<?php endif; ?>
									</td>
									<td class="align-middle text-center">
										<div class="btn-group">
											<a href="<?php echo Uri::create('admin/inventory/products/view/' . $product->id); ?>" 
											   class="btn btn-sm" 
											   style="background: #f3f4f6; color: #374151;"
											   title="Ver detalles">
												<i class="fas fa-eye"></i>
											</a>
											<?php if (Helper_Permission::can('inventory_products', 'edit')): ?>
												<a href="<?php echo Uri::create('admin/inventory/products/edit/' . $product->id); ?>" 
												   class="btn btn-sm" 
												   style="background: #4f46e5; color: white;"
												   title="Editar">
													<i class="fas fa-edit"></i>
												</a>
											<?php endif; ?>
											<?php if (Helper_Permission::can('inventory_products', 'delete')): ?>
												<a href="<?php echo Uri::create('admin/inventory/products/delete/' . $product->id); ?>" 
												   class="btn btn-sm" 
												   style="background: #ef4444; color: white;"
												   title="Eliminar"
												   onclick="return confirm('¿Estás seguro de eliminar este producto?');">
													<i class="fas fa-trash"></i>
												</a>
											<?php endif; ?>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php if ($pagination): ?>
			<div class="card-footer bg-white border-0">
				<?php echo $pagination; ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<script>
$(document).ready(function() {
	// DataTables (opcional)
	// $('#productsTable').DataTable({
	// 	"language": {"url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"}
	// });
});
</script>
