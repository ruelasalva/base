<div class="container-fluid">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h2><i class="fas fa-file-invoice"></i> Detalle de Póliza</h2>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/polizas'); ?>">Pólizas</a></li>
					<li class="breadcrumb-item active"><?php echo $entry->entry_number; ?></li>
				</ol>
			</nav>
		</div>
		<a href="<?php echo Uri::create('admin/polizas'); ?>" class="btn btn-secondary">
			<i class="fas fa-arrow-left"></i> Volver
		</a>
	</div>

	<div class="row">
		<div class="col-md-8">
			<!-- Información de la póliza -->
			<div class="card mb-4">
				<div class="card-header d-flex justify-content-between align-items-center">
					<h5 class="mb-0">Información General</h5>
					<?php
					$status_badges = array(
						'borrador' => 'warning',
						'aplicada' => 'success',
						'cancelada' => 'secondary',
						'revisada' => 'info'
					);
					$badge = $status_badges[$entry->status] ?? 'secondary';
					?>
					<span class="badge bg-<?php echo $badge; ?> fs-6">
						<?php echo strtoupper($entry->status); ?>
					</span>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6 mb-3">
							<strong>Folio:</strong><br>
							<span class="fs-5"><?php echo $entry->entry_number; ?></span>
						</div>
						<div class="col-md-6 mb-3">
							<strong>Tipo:</strong><br>
							<?php
							$type_badges = array(
								'ingreso' => 'success',
								'egreso' => 'danger',
								'diario' => 'primary',
								'apertura' => 'info',
								'ajuste' => 'warning',
								'cierre' => 'secondary'
							);
							$badge = $type_badges[$entry->entry_type] ?? 'secondary';
							?>
							<span class="badge bg-<?php echo $badge; ?>">
								<?php echo ucfirst($entry->entry_type); ?>
							</span>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<strong>Fecha:</strong><br>
							<?php echo date('d/m/Y', strtotime($entry->entry_date)); ?>
						</div>
						<div class="col-md-6 mb-3">
							<strong>Periodo:</strong><br>
							<?php echo $entry->period; ?> (Año fiscal: <?php echo $entry->fiscal_year; ?>)
						</div>
					</div>
					<div class="mb-3">
						<strong>Concepto:</strong><br>
						<?php echo $entry->concept; ?>
					</div>
					<?php if ($entry->reference): ?>
						<div class="mb-3">
							<strong>Referencia:</strong><br>
							<?php echo $entry->reference; ?>
						</div>
					<?php endif; ?>
					<?php if ($entry->status == 'cancelada' && $entry->cancellation_reason): ?>
						<div class="alert alert-warning">
							<strong><i class="fas fa-info-circle"></i> Motivo de Cancelación:</strong><br>
							<?php echo $entry->cancellation_reason; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Partidas / Movimientos -->
			<div class="card">
				<div class="card-header">
					<h5 class="mb-0">Partidas / Movimientos</h5>
				</div>
				<div class="card-body">
					<?php if (empty($lines)): ?>
						<div class="alert alert-info">
							No hay movimientos registrados
						</div>
					<?php else: ?>
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead class="table-light">
									<tr>
										<th width="5%">#</th>
										<th width="15%">Código</th>
										<th width="30%">Cuenta</th>
										<th width="25%">Descripción</th>
										<th width="12%" class="text-end">Cargo</th>
										<th width="12%" class="text-end">Abono</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($lines as $line): ?>
										<tr>
											<td class="text-center"><?php echo $line['line_number']; ?></td>
											<td><?php echo $line['account_code']; ?></td>
											<td><?php echo $line['account_name']; ?></td>
											<td>
												<?php echo $line['description']; ?>
												<?php if ($line['reference']): ?>
													<br><small class="text-muted">Ref: <?php echo $line['reference']; ?></small>
												<?php endif; ?>
											</td>
											<td class="text-end">
												<?php if ($line['debit'] > 0): ?>
													<strong>$<?php echo number_format($line['debit'], 2); ?></strong>
												<?php else: ?>
													<span class="text-muted">-</span>
												<?php endif; ?>
											</td>
											<td class="text-end">
												<?php if ($line['credit'] > 0): ?>
													<strong>$<?php echo number_format($line['credit'], 2); ?></strong>
												<?php else: ?>
													<span class="text-muted">-</span>
												<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
								<tfoot class="table-light">
									<tr>
										<th colspan="4" class="text-end">Totales:</th>
										<th class="text-end">$<?php echo number_format($entry->total_debit, 2); ?></th>
										<th class="text-end">$<?php echo number_format($entry->total_credit, 2); ?></th>
									</tr>
									<tr>
										<th colspan="4" class="text-end">Estado:</th>
										<th colspan="2" class="text-center">
											<?php if ($entry->is_balanced): ?>
												<span class="badge bg-success">
													<i class="fas fa-check-circle"></i> Balanceada
												</span>
											<?php else: ?>
												<span class="badge bg-danger">
													<i class="fas fa-exclamation-triangle"></i> 
													Desbalanceada (Dif: $<?php echo number_format(abs($entry->total_debit - $entry->total_credit), 2); ?>)
												</span>
											<?php endif; ?>
										</th>
									</tr>
								</tfoot>
							</table>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<!-- Acciones rápidas -->
			<div class="card mb-4">
				<div class="card-header">
					<h5 class="mb-0">Acciones</h5>
				</div>
				<div class="card-body">
					<?php if ($entry->status == 'borrador'): ?>
						<?php if ($entry->is_balanced && $can_edit): ?>
							<a href="<?php echo Uri::create('admin/polizas/apply/' . $entry->id); ?>" 
							   class="btn btn-success w-100 mb-2"
							   onclick="return confirm('¿Aplicar esta póliza? Una vez aplicada no se puede modificar.');">
								<i class="fas fa-check"></i> Aplicar Póliza
							</a>
						<?php elseif (!$entry->is_balanced): ?>
							<div class="alert alert-warning mb-2">
								<i class="fas fa-exclamation-triangle"></i> No se puede aplicar una póliza desbalanceada
							</div>
						<?php endif; ?>
						<?php if ($can_delete): ?>
							<a href="<?php echo Uri::create('admin/polizas/delete/' . $entry->id); ?>" 
							   class="btn btn-danger w-100 mb-2"
							   onclick="return confirm('¿Eliminar esta póliza?');">
								<i class="fas fa-trash"></i> Eliminar
							</a>
						<?php endif; ?>
					<?php elseif ($entry->status == 'aplicada' && $can_delete): ?>
						<a href="<?php echo Uri::create('admin/polizas/cancel/' . $entry->id); ?>" 
						   class="btn btn-warning w-100 mb-2">
							<i class="fas fa-ban"></i> Cancelar Póliza
						</a>
					<?php endif; ?>
				</div>
			</div>

			<!-- Información de auditoría -->
			<div class="card">
				<div class="card-header">
					<h5 class="mb-0"><i class="fas fa-history"></i> Auditoría</h5>
				</div>
				<div class="card-body">
					<div class="mb-3">
						<strong>Creada por:</strong><br>
						<small>Usuario ID: <?php echo $entry->created_by; ?></small><br>
						<small><?php echo date('d/m/Y H:i', strtotime($entry->created_at)); ?></small>
					</div>
					<?php if ($entry->status == 'aplicada' && $entry->applied_by): ?>
						<div class="mb-3">
							<strong>Aplicada por:</strong><br>
							<small>Usuario ID: <?php echo $entry->applied_by; ?></small><br>
							<small><?php echo date('d/m/Y H:i', strtotime($entry->applied_at)); ?></small>
						</div>
					<?php endif; ?>
					<?php if ($entry->status == 'cancelada' && $entry->cancelled_by): ?>
						<div class="mb-3">
							<strong>Cancelada por:</strong><br>
							<small>Usuario ID: <?php echo $entry->cancelled_by; ?></small><br>
							<small><?php echo date('d/m/Y H:i', strtotime($entry->cancelled_at)); ?></small>
						</div>
					<?php endif; ?>
					<div>
						<strong>Última actualización:</strong><br>
						<small><?php echo date('d/m/Y H:i', strtotime($entry->updated_at)); ?></small>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
