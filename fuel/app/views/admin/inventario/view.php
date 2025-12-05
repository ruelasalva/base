<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h3 class="card-title mb-0">
					<i class="fas fa-dolly"></i> Movimiento: <?php echo htmlspecialchars($movement->code, ENT_QUOTES, 'UTF-8'); ?>
				</h3>
				<div>
					<?php if (Helper_Permission::can('inventario', 'edit') && $movement->can_edit()): ?>
						<a href="<?php echo Uri::create('admin/inventario/edit/' . $movement->id); ?>" class="btn btn-warning btn-sm">
							<i class="fas fa-edit"></i> Editar
						</a>
					<?php endif; ?>
					
					<?php if (Helper_Permission::can('inventario', 'approve') && $movement->can_approve()): ?>
						<a href="<?php echo Uri::create('admin/inventario/approve/' . $movement->id); ?>" 
						   class="btn btn-info btn-sm"
						   onclick="return confirm('¿Aprobar este movimiento?');">
							<i class="fas fa-check"></i> Aprobar
						</a>
					<?php endif; ?>
					
					<?php if (Helper_Permission::can('inventario', 'apply') && $movement->can_apply()): ?>
						<a href="<?php echo Uri::create('admin/inventario/apply/' . $movement->id); ?>" 
						   class="btn btn-success btn-sm"
						   onclick="return confirm('¿Aplicar este movimiento al inventario? Esta acción no se puede deshacer.');">
							<i class="fas fa-check-double"></i> Aplicar al Inventario
						</a>
					<?php endif; ?>
					
					<a href="<?php echo Uri::create('admin/inventario'); ?>" class="btn btn-secondary btn-sm">
						<i class="fas fa-arrow-left"></i> Volver
					</a>
				</div>
			</div>

			<div class="card-body">
				<!-- Información General -->
				<div class="row mb-4">
					<div class="col-md-6">
						<div class="card bg-light">
							<div class="card-body">
								<h5 class="card-title mb-3">
									<i class="fas fa-info-circle"></i> Información General
								</h5>
								
								<div class="row mb-2">
									<div class="col-4"><strong>Código:</strong></div>
									<div class="col-8">
										<span class="badge bg-dark fs-6"><?php echo htmlspecialchars($movement->code, ENT_QUOTES, 'UTF-8'); ?></span>
									</div>
								</div>

								<div class="row mb-2">
									<div class="col-4"><strong>Tipo:</strong></div>
									<div class="col-8"><?php echo $movement->get_type_badge(); ?></div>
								</div>

								<?php if ($movement->subtype): ?>
								<div class="row mb-2">
									<div class="col-4"><strong>Subtipo:</strong></div>
									<div class="col-8">
										<span class="badge bg-secondary"><?php echo ucfirst($movement->subtype); ?></span>
									</div>
								</div>
								<?php endif; ?>

								<div class="row mb-2">
									<div class="col-4"><strong>Estado:</strong></div>
									<div class="col-8"><?php echo $movement->get_status_badge(); ?></div>
								</div>

								<div class="row mb-2">
									<div class="col-4"><strong>Fecha:</strong></div>
									<div class="col-8"><?php echo date('d/m/Y', strtotime($movement->movement_date)); ?></div>
								</div>

								<?php if ($movement->reference_code): ?>
								<div class="row mb-2">
									<div class="col-4"><strong>Referencia:</strong></div>
									<div class="col-8"><?php echo htmlspecialchars($movement->reference_code, ENT_QUOTES, 'UTF-8'); ?></div>
								</div>
								<?php endif; ?>

								<?php if ($movement->reason): ?>
								<div class="row mb-2">
									<div class="col-4"><strong>Razón:</strong></div>
									<div class="col-8"><?php echo htmlspecialchars($movement->reason, ENT_QUOTES, 'UTF-8'); ?></div>
								</div>
								<?php endif; ?>

								<?php if ($movement->notes): ?>
								<div class="row mb-2">
									<div class="col-4"><strong>Notas:</strong></div>
									<div class="col-8"><?php echo nl2br(htmlspecialchars($movement->notes, ENT_QUOTES, 'UTF-8')); ?></div>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="card bg-light">
							<div class="card-body">
								<h5 class="card-title mb-3">
									<i class="fas fa-warehouse"></i> Almacenes
								</h5>

								<div class="row mb-2">
									<div class="col-4"><strong><?php echo $movement->type === 'transfer' ? 'Origen:' : 'Almacén:'; ?></strong></div>
									<div class="col-8">
										<?php if ($warehouse_from): ?>
											<span class="badge bg-info"><?php echo htmlspecialchars($warehouse_from['name'], ENT_QUOTES, 'UTF-8'); ?></span>
										<?php else: ?>
											<span class="text-muted">-</span>
										<?php endif; ?>
									</div>
								</div>

								<?php if ($movement->type === 'transfer' && $warehouse_to): ?>
								<div class="row mb-2">
									<div class="col-4"><strong>Destino:</strong></div>
									<div class="col-8">
										<span class="badge bg-info"><?php echo htmlspecialchars($warehouse_to['name'], ENT_QUOTES, 'UTF-8'); ?></span>
									</div>
								</div>
								<?php endif; ?>

								<hr>

								<h5 class="card-title mb-3">
									<i class="fas fa-chart-bar"></i> Totales
								</h5>

								<div class="row mb-2">
									<div class="col-4"><strong>Items:</strong></div>
									<div class="col-8">
										<span class="badge bg-secondary"><?php echo number_format($movement->total_items); ?></span>
									</div>
								</div>

								<div class="row mb-2">
									<div class="col-4"><strong>Cantidad:</strong></div>
									<div class="col-8">
										<strong><?php echo number_format($movement->total_quantity, 2); ?></strong>
									</div>
								</div>

								<div class="row mb-2">
									<div class="col-4"><strong>Total:</strong></div>
									<div class="col-8">
										<strong class="text-success fs-5">$<?php echo number_format($movement->total_cost, 2); ?></strong>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Detalles de Items -->
				<div class="card">
					<div class="card-header bg-light">
						<h5 class="card-title mb-0">
							<i class="fas fa-box"></i> Productos
						</h5>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-hover mb-0">
								<thead class="table-light">
									<tr>
										<th width="50">#</th>
										<th>Producto</th>
										<?php if ($movement->type === 'exit' || $movement->type === 'transfer' || $movement->type === 'relocation'): ?>
											<th width="200">Ubicación Origen</th>
										<?php endif; ?>
										<?php if ($movement->type === 'entry' || $movement->type === 'transfer' || $movement->type === 'relocation' || $movement->type === 'adjustment'): ?>
											<th width="200">Ubicación Destino</th>
										<?php endif; ?>
										<th width="100" class="text-end">Cantidad</th>
										<th width="120" class="text-end">Costo Unit.</th>
										<th width="120" class="text-end">Subtotal</th>
									</tr>
								</thead>
								<tbody>
									<?php if (count($movement->items)): ?>
										<?php foreach ($movement->items as $index => $item): ?>
											<tr>
												<td><?php echo $index + 1; ?></td>
												<td>
													<strong><?php echo htmlspecialchars($item->product->name, ENT_QUOTES, 'UTF-8'); ?></strong>
													<br><small class="text-muted">SKU: <?php echo htmlspecialchars($item->product->sku, ENT_QUOTES, 'UTF-8'); ?></small>
													<?php if ($item->batch_number): ?>
														<br><small class="text-info">Lote: <?php echo htmlspecialchars($item->batch_number, ENT_QUOTES, 'UTF-8'); ?></small>
													<?php endif; ?>
													<?php if ($item->notes): ?>
														<br><small class="text-muted"><i class="fas fa-comment"></i> <?php echo htmlspecialchars($item->notes, ENT_QUOTES, 'UTF-8'); ?></small>
													<?php endif; ?>
												</td>
												<?php if ($movement->type === 'exit' || $movement->type === 'transfer' || $movement->type === 'relocation'): ?>
													<td>
														<?php if ($item->location_from): ?>
															<span class="badge bg-secondary">
																<?php echo htmlspecialchars($item->location_from->code, ENT_QUOTES, 'UTF-8'); ?>
															</span>
														<?php else: ?>
															<span class="text-muted">Sin ubicación</span>
														<?php endif; ?>
													</td>
												<?php endif; ?>
												<?php if ($movement->type === 'entry' || $movement->type === 'transfer' || $movement->type === 'relocation' || $movement->type === 'adjustment'): ?>
													<td>
														<?php if ($item->location_to): ?>
															<span class="badge bg-secondary">
																<?php echo htmlspecialchars($item->location_to->code, ENT_QUOTES, 'UTF-8'); ?>
															</span>
														<?php else: ?>
															<span class="text-muted">Sin ubicación</span>
														<?php endif; ?>
													</td>
												<?php endif; ?>
												<td class="text-end">
													<strong><?php echo number_format($item->quantity, 2); ?></strong>
												</td>
												<td class="text-end">
													$<?php echo number_format($item->unit_cost, 2); ?>
												</td>
												<td class="text-end">
													<strong>$<?php echo number_format($item->subtotal, 2); ?></strong>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php else: ?>
										<tr>
											<td colspan="7" class="text-center text-muted py-4">
												No hay productos en este movimiento
											</td>
										</tr>
									<?php endif; ?>
								</tbody>
								<tfoot class="table-light">
									<tr>
										<td colspan="<?php echo ($movement->type === 'transfer' || $movement->type === 'relocation') ? '5' : '4'; ?>" class="text-end">
											<strong>Total:</strong>
										</td>
										<td class="text-end">
											<strong class="text-success fs-5">$<?php echo number_format($movement->total_cost, 2); ?></strong>
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>

				<!-- Información de Auditoría -->
				<div class="row mt-4">
					<div class="col-md-6">
						<div class="card bg-light">
							<div class="card-body">
								<h6 class="card-title mb-3">
									<i class="fas fa-user"></i> Información de Auditoría
								</h6>

								<div class="row mb-2">
									<div class="col-4"><strong>Creado por:</strong></div>
									<div class="col-8">
										<?php if ($movement->creator): ?>
											<?php echo htmlspecialchars($movement->creator->username, ENT_QUOTES, 'UTF-8'); ?>
										<?php else: ?>
											<span class="text-muted">-</span>
										<?php endif; ?>
									</div>
								</div>

								<div class="row mb-2">
									<div class="col-4"><strong>Fecha creación:</strong></div>
									<div class="col-8">
										<?php echo date('d/m/Y H:i', strtotime($movement->created_at)); ?>
									</div>
								</div>

								<?php if ($movement->approved_by): ?>
								<div class="row mb-2">
									<div class="col-4"><strong>Aprobado por:</strong></div>
									<div class="col-8">
										<?php if ($movement->approver): ?>
											<?php echo htmlspecialchars($movement->approver->username, ENT_QUOTES, 'UTF-8'); ?>
										<?php else: ?>
											ID: <?php echo $movement->approved_by; ?>
										<?php endif; ?>
									</div>
								</div>

								<div class="row mb-2">
									<div class="col-4"><strong>Fecha aprobación:</strong></div>
									<div class="col-8">
										<?php echo date('d/m/Y H:i', strtotime($movement->approved_at)); ?>
									</div>
								</div>
								<?php endif; ?>

								<?php if ($movement->applied_by): ?>
								<div class="row mb-2">
									<div class="col-4"><strong>Aplicado por:</strong></div>
									<div class="col-8">
										<?php if ($movement->applier): ?>
											<?php echo htmlspecialchars($movement->applier->username, ENT_QUOTES, 'UTF-8'); ?>
										<?php else: ?>
											ID: <?php echo $movement->applied_by; ?>
										<?php endif; ?>
									</div>
								</div>

								<div class="row mb-2">
									<div class="col-4"><strong>Fecha aplicación:</strong></div>
									<div class="col-8">
										<?php echo date('d/m/Y H:i', strtotime($movement->applied_at)); ?>
									</div>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<?php if ($movement->status === 'approved'): ?>
					<div class="col-md-6">
						<div class="alert alert-info">
							<h5 class="alert-heading">
								<i class="fas fa-info-circle"></i> Movimiento Aprobado
							</h5>
							<p class="mb-0">
								Este movimiento ha sido aprobado y está listo para aplicarse al inventario.
								Una vez aplicado, el stock se actualizará automáticamente.
							</p>
						</div>
					</div>
					<?php endif; ?>

					<?php if ($movement->status === 'applied'): ?>
					<div class="col-md-6">
						<div class="alert alert-success">
							<h5 class="alert-heading">
								<i class="fas fa-check-circle"></i> Movimiento Aplicado
							</h5>
							<p class="mb-0">
								Este movimiento ya ha sido aplicado al inventario. El stock ha sido actualizado correctamente.
							</p>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
