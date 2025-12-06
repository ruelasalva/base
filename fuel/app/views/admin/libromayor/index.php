<div class="container-fluid">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h2><i class="fas fa-book"></i> Libro Mayor</h2>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
					<li class="breadcrumb-item active">Libro Mayor</li>
				</ol>
			</nav>
		</div>
		<div>
			<a href="<?php echo Uri::create('admin/libromayor/balances' . (!empty($period_from) ? '?period=' . $period_from : '')); ?>" 
			   class="btn btn-info me-2">
				<i class="fas fa-balance-scale"></i> Ver Saldos
			</a>
			<a href="<?php echo Uri::create('admin/libromayor/export' . $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''); ?>" 
			   class="btn btn-success">
				<i class="fas fa-file-excel"></i> Exportar CSV
			</a>
		</div>
	</div>

	<!-- Estadísticas -->
	<div class="row mb-4">
		<div class="col-md-2">
			<div class="card text-center">
				<div class="card-body">
					<h4 class="mb-0"><?php echo number_format($stats['total_movements']); ?></h4>
					<small class="text-muted">Movimientos</small>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<div class="card text-center">
				<div class="card-body">
					<h4 class="mb-0"><?php echo $stats['applied_entries']; ?></h4>
					<small class="text-muted">Pólizas</small>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-center border-success">
				<div class="card-body">
					<h4 class="mb-0 text-success">$<?php echo number_format($stats['total_debit'], 2); ?></h4>
					<small class="text-muted">Total Cargos</small>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-center border-danger">
				<div class="card-body">
					<h4 class="mb-0 text-danger">$<?php echo number_format($stats['total_credit'], 2); ?></h4>
					<small class="text-muted">Total Abonos</small>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<div class="card text-center">
				<div class="card-body">
					<h4 class="mb-0"><?php echo $stats['accounts_with_movement']; ?></h4>
					<small class="text-muted">Cuentas</small>
				</div>
			</div>
		</div>
	</div>

	<!-- Filtros -->
	<div class="card mb-4">
		<div class="card-header">
			<h5 class="mb-0"><i class="fas fa-filter"></i> Filtros de Consulta</h5>
		</div>
		<div class="card-body">
			<form method="get" action="<?php echo Uri::create('admin/libromayor'); ?>" class="row g-3">
				<div class="col-md-4">
					<label class="form-label">Cuenta Contable</label>
					<select name="account_id" class="form-select">
						<option value="">Todas las cuentas</option>
						<?php foreach ($accounts as $account): ?>
							<option value="<?php echo $account->id; ?>" 
							        <?php echo $account_id == $account->id ? 'selected' : ''; ?>>
								<?php echo $account->account_code . ' - ' . $account->name; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-2">
					<label class="form-label">Tipo de Cuenta</label>
					<select name="account_type" class="form-select">
						<option value="">Todos</option>
						<option value="activo" <?php echo $account_type == 'activo' ? 'selected' : ''; ?>>Activo</option>
						<option value="pasivo" <?php echo $account_type == 'pasivo' ? 'selected' : ''; ?>>Pasivo</option>
						<option value="capital" <?php echo $account_type == 'capital' ? 'selected' : ''; ?>>Capital</option>
						<option value="ingreso" <?php echo $account_type == 'ingreso' ? 'selected' : ''; ?>>Ingreso</option>
						<option value="gasto" <?php echo $account_type == 'gasto' ? 'selected' : ''; ?>>Gasto</option>
						<option value="resultado" <?php echo $account_type == 'resultado' ? 'selected' : ''; ?>>Resultado</option>
					</select>
				</div>
				<div class="col-md-2">
					<label class="form-label">Periodo Desde</label>
					<input type="month" name="period_from" class="form-control" value="<?php echo $period_from; ?>">
				</div>
				<div class="col-md-2">
					<label class="form-label">Periodo Hasta</label>
					<input type="month" name="period_to" class="form-control" value="<?php echo $period_to; ?>">
				</div>
				<div class="col-md-2 d-flex align-items-end">
					<button type="submit" class="btn btn-primary me-2">
						<i class="fas fa-search"></i> Consultar
					</button>
					<a href="<?php echo Uri::create('admin/libromayor'); ?>" class="btn btn-secondary">
						<i class="fas fa-times"></i>
					</a>
				</div>
			</form>
		</div>
	</div>

	<!-- Saldo inicial si hay cuenta seleccionada -->
	<?php if ($account_id && $initial_balance != 0): ?>
		<div class="alert alert-info">
			<strong>Saldo Inicial al <?php echo $period_from; ?>:</strong> 
			$<?php echo number_format($initial_balance, 2); ?>
		</div>
	<?php endif; ?>

	<!-- Tabla de movimientos -->
	<div class="card">
		<div class="card-body">
			<?php if (empty($movements)): ?>
				<div class="alert alert-info text-center">
					<i class="fas fa-info-circle"></i> No hay movimientos registrados con los filtros aplicados
				</div>
			<?php else: ?>
				<div class="table-responsive">
					<table class="table table-sm table-hover">
						<thead class="table-light">
							<tr>
								<th>Fecha</th>
								<th>Folio</th>
								<th>Tipo</th>
								<th>Cuenta</th>
								<th>Descripción</th>
								<th class="text-end">Cargo</th>
								<th class="text-end">Abono</th>
								<?php if ($account_id): ?>
									<th class="text-end">Saldo</th>
								<?php endif; ?>
								<th>Concepto</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($movements as $movement): ?>
								<tr>
									<td><?php echo date('d/m/Y', strtotime($movement['entry_date'])); ?></td>
									<td>
										<a href="<?php echo Uri::create('admin/polizas/view/' . $movement['entry_id'] ?? '#'); ?>" 
										   target="_blank" class="text-decoration-none">
											<?php echo $movement['entry_number']; ?>
										</a>
									</td>
									<td>
										<?php
										$type_badges = array(
											'ingreso' => 'success',
											'egreso' => 'danger',
											'diario' => 'primary',
											'apertura' => 'info',
											'ajuste' => 'warning',
											'cierre' => 'secondary'
										);
										$badge = $type_badges[$movement['entry_type']] ?? 'secondary';
										?>
										<span class="badge bg-<?php echo $badge; ?> badge-sm">
											<?php echo strtoupper(substr($movement['entry_type'], 0, 1)); ?>
										</span>
									</td>
									<td>
										<small>
											<strong><?php echo $movement['account_code']; ?></strong><br>
											<?php echo $movement['account_name']; ?>
										</small>
									</td>
									<td>
										<?php echo $movement['description']; ?>
										<?php if ($movement['reference']): ?>
											<br><small class="text-muted">Ref: <?php echo $movement['reference']; ?></small>
										<?php endif; ?>
									</td>
									<td class="text-end">
										<?php if ($movement['debit'] > 0): ?>
											<strong class="text-success">$<?php echo number_format($movement['debit'], 2); ?></strong>
										<?php else: ?>
											<span class="text-muted">-</span>
										<?php endif; ?>
									</td>
									<td class="text-end">
										<?php if ($movement['credit'] > 0): ?>
											<strong class="text-danger">$<?php echo number_format($movement['credit'], 2); ?></strong>
										<?php else: ?>
											<span class="text-muted">-</span>
										<?php endif; ?>
									</td>
									<?php if ($account_id): ?>
										<td class="text-end">
											<strong><?php echo number_format($movement['running_balance'], 2); ?></strong>
										</td>
									<?php endif; ?>
									<td>
										<small class="text-muted"><?php echo substr($movement['concept'], 0, 50); ?></small>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
						<?php if ($account_id): ?>
							<tfoot class="table-light">
								<tr>
									<th colspan="<?php echo $account_id ? 7 : 6; ?>" class="text-end">Saldo Final:</th>
									<th class="text-end">$<?php echo number_format($final_balance, 2); ?></th>
									<th></th>
								</tr>
							</tfoot>
						<?php endif; ?>
					</table>
				</div>

				<!-- Paginación -->
				<?php if ($pagination->total_pages > 1): ?>
					<div class="d-flex justify-content-center mt-4">
						<?php echo $pagination->render(); ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</div>
