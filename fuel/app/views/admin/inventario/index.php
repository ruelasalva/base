<div class="row">
	<div class="col-12">
		<!-- Estadísticas -->
		<div class="row mb-4">
			<div class="col-md-3">
				<div class="card bg-primary text-white">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h6 class="mb-0">Total Movimientos</h6>
								<h3 class="mb-0"><?php echo number_format($total_movements); ?></h3>
							</div>
							<div class="text-white-50">
								<i class="fas fa-dolly fa-3x"></i>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card bg-success text-white">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h6 class="mb-0">Entradas</h6>
								<h3 class="mb-0"><?php echo number_format($entries); ?></h3>
							</div>
							<div class="text-white-50">
								<i class="fas fa-arrow-down fa-3x"></i>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card bg-danger text-white">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h6 class="mb-0">Salidas</h6>
								<h3 class="mb-0"><?php echo number_format($exits); ?></h3>
							</div>
							<div class="text-white-50">
								<i class="fas fa-arrow-up fa-3x"></i>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-3">
				<div class="card bg-warning text-white">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h6 class="mb-0">Pendientes</h6>
								<h3 class="mb-0"><?php echo number_format($pending); ?></h3>
							</div>
							<div class="text-white-50">
								<i class="fas fa-clock fa-3x"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h3 class="card-title mb-0">
					<i class="fas fa-dolly"></i> Movimientos de Inventario
				</h3>
				<?php if (Helper_Permission::can('inventario', 'create')): ?>
					<div class="dropdown">
						<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown">
							<i class="fas fa-plus"></i> Nuevo Movimiento
						</button>
						<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							<li><a class="dropdown-item" href="<?php echo Uri::create('admin/inventario/create/entry'); ?>">
								<i class="fas fa-arrow-down text-success"></i> Entrada
							</a></li>
							<li><a class="dropdown-item" href="<?php echo Uri::create('admin/inventario/create/exit'); ?>">
								<i class="fas fa-arrow-up text-danger"></i> Salida
							</a></li>
							<li><a class="dropdown-item" href="<?php echo Uri::create('admin/inventario/create/transfer'); ?>">
								<i class="fas fa-exchange-alt text-info"></i> Traspaso
							</a></li>
							<li><a class="dropdown-item" href="<?php echo Uri::create('admin/inventario/create/adjustment'); ?>">
								<i class="fas fa-adjust text-warning"></i> Ajuste
							</a></li>
							<li><a class="dropdown-item" href="<?php echo Uri::create('admin/inventario/create/relocation'); ?>">
								<i class="fas fa-map-marker-alt text-secondary"></i> Reubicación
							</a></li>
						</ul>
					</div>
				<?php endif; ?>
			</div>

			<div class="card-body">
				<!-- Filtros -->
				<form method="get" class="mb-3">
					<div class="row g-2">
						<div class="col-md-3">
							<input type="text" name="search" class="form-control" placeholder="Buscar código, referencia..." value="<?php echo htmlspecialchars(Input::get('search', ''), ENT_QUOTES, 'UTF-8'); ?>">
						</div>
						<div class="col-md-2">
							<select name="type" class="form-select">
								<option value="">Todos los tipos</option>
								<option value="entry" <?php echo Input::get('type') === 'entry' ? 'selected' : ''; ?>>Entrada</option>
								<option value="exit" <?php echo Input::get('type') === 'exit' ? 'selected' : ''; ?>>Salida</option>
								<option value="transfer" <?php echo Input::get('type') === 'transfer' ? 'selected' : ''; ?>>Traspaso</option>
								<option value="adjustment" <?php echo Input::get('type') === 'adjustment' ? 'selected' : ''; ?>>Ajuste</option>
								<option value="relocation" <?php echo Input::get('type') === 'relocation' ? 'selected' : ''; ?>>Reubicación</option>
							</select>
						</div>
						<div class="col-md-2">
							<select name="status" class="form-select">
								<option value="">Todos los estados</option>
								<option value="draft" <?php echo Input::get('status') === 'draft' ? 'selected' : ''; ?>>Borrador</option>
								<option value="pending" <?php echo Input::get('status') === 'pending' ? 'selected' : ''; ?>>Pendiente</option>
								<option value="approved" <?php echo Input::get('status') === 'approved' ? 'selected' : ''; ?>>Aprobado</option>
								<option value="applied" <?php echo Input::get('status') === 'applied' ? 'selected' : ''; ?>>Aplicado</option>
								<option value="cancelled" <?php echo Input::get('status') === 'cancelled' ? 'selected' : ''; ?>>Cancelado</option>
							</select>
						</div>
						<div class="col-md-2">
							<select name="warehouse_id" class="form-select">
								<option value="">Todos los almacenes</option>
								<?php foreach ($warehouses as $wh): ?>
									<option value="<?php echo $wh['id']; ?>" <?php echo Input::get('warehouse_id') == $wh['id'] ? 'selected' : ''; ?>>
										<?php echo htmlspecialchars($wh['name'], ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-md-3">
							<div class="btn-group w-100">
								<button type="submit" class="btn btn-secondary">
									<i class="fas fa-search"></i> Buscar
								</button>
								<?php if (Input::get('search') || Input::get('type') || Input::get('status') || Input::get('warehouse_id')): ?>
									<a href="<?php echo Uri::create('admin/inventario'); ?>" class="btn btn-outline-secondary">
										<i class="fas fa-times"></i> Limpiar
									</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</form>

				<!-- Tabla de movimientos -->
				<div class="table-responsive">
					<table class="table table-hover table-striped">
						<thead class="table-light">
							<tr>
								<th width="120">Código</th>
								<th width="100" class="text-center">Tipo</th>
								<th>Almacén</th>
								<th width="100">Fecha</th>
								<th width="80" class="text-end">Items</th>
								<th width="100" class="text-end">Total</th>
								<th width="100" class="text-center">Estado</th>
								<th width="150" class="text-center">Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php if (count($movements)): ?>
								<?php foreach ($movements as $movement): ?>
									<?php $warehouse = DB::select('name')->from('almacenes')->where('id', $movement->warehouse_id)->execute()->current(); ?>
									<tr>
										<td>
											<strong><?php echo htmlspecialchars($movement->code, ENT_QUOTES, 'UTF-8'); ?></strong>
											<?php if ($movement->reference_code): ?>
												<br><small class="text-muted"><?php echo htmlspecialchars($movement->reference_code, ENT_QUOTES, 'UTF-8'); ?></small>
											<?php endif; ?>
										</td>
										<td class="text-center">
											<?php echo $movement->get_type_badge(); ?>
										</td>
										<td>
											<?php echo $warehouse ? htmlspecialchars($warehouse['name'], ENT_QUOTES, 'UTF-8') : '-'; ?>
											<?php if ($movement->type === 'transfer' && $movement->warehouse_to_id): ?>
												<?php $warehouse_to = DB::select('name')->from('almacenes')->where('id', $movement->warehouse_to_id)->execute()->current(); ?>
												<br><small class="text-muted">
													<i class="fas fa-arrow-right"></i> <?php echo $warehouse_to ? htmlspecialchars($warehouse_to['name'], ENT_QUOTES, 'UTF-8') : '-'; ?>
												</small>
											<?php endif; ?>
										</td>
										<td>
											<?php echo date('d/m/Y', strtotime($movement->movement_date)); ?>
										</td>
										<td class="text-end">
											<span class="badge bg-secondary"><?php echo number_format($movement->total_items); ?></span>
										</td>
										<td class="text-end">
											<strong>$<?php echo number_format($movement->total_cost, 2); ?></strong>
										</td>
										<td class="text-center">
											<?php echo $movement->get_status_badge(); ?>
										</td>
										<td class="text-center">
											<div class="btn-group btn-group-sm" role="group">
												<?php if (Helper_Permission::can('inventario', 'view')): ?>
													<a href="<?php echo Uri::create('admin/inventario/view/' . $movement->id); ?>" 
													   class="btn btn-info" 
													   title="Ver detalles">
														<i class="fas fa-eye"></i>
													</a>
												<?php endif; ?>

												<?php if (Helper_Permission::can('inventario', 'edit') && $movement->can_edit()): ?>
													<a href="<?php echo Uri::create('admin/inventario/edit/' . $movement->id); ?>" 
													   class="btn btn-warning" 
													   title="Editar">
														<i class="fas fa-edit"></i>
													</a>
												<?php endif; ?>

												<?php if (Helper_Permission::can('inventario', 'delete') && $movement->can_delete()): ?>
													<a href="<?php echo Uri::create('admin/inventario/delete/' . $movement->id); ?>" 
													   class="btn btn-danger" 
													   title="Eliminar"
													   onclick="return confirm('¿Estás seguro de eliminar este movimiento?');">
														<i class="fas fa-trash"></i>
													</a>
												<?php endif; ?>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="8" class="text-center py-4">
										<i class="fas fa-inbox fa-3x text-muted mb-3"></i>
										<p class="text-muted mb-0">
											<?php echo Input::get('search') || Input::get('type') || Input::get('status') ? 'No se encontraron movimientos con ese criterio.' : 'No hay movimientos registrados.'; ?>
										</p>
									</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>

				<!-- Paginación -->
				<?php if ($pagination_info['total_pages'] > 1): ?>
					<div class="d-flex justify-content-between align-items-center mt-3">
						<div class="text-muted">
							Mostrando <?php echo $pagination_info['offset'] + 1; ?> a 
							<?php echo min($pagination_info['offset'] + $pagination_info['per_page'], $pagination_info['total']); ?> 
							de <?php echo number_format($pagination_info['total']); ?> movimientos
						</div>
						<div>
							<?php echo $pagination; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
