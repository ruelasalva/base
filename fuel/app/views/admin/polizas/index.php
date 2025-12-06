<div class="container-fluid">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h2><i class="fas fa-file-invoice"></i> Pólizas Contables</h2>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
					<li class="breadcrumb-item active">Pólizas</li>
				</ol>
			</nav>
		</div>
		<?php if ($can_create): ?>
			<a href="<?php echo Uri::create('admin/polizas/create'); ?>" class="btn btn-primary">
				<i class="fas fa-plus"></i> Nueva Póliza
			</a>
		<?php endif; ?>
	</div>

	<!-- Estadísticas -->
	<div class="row mb-4">
		<div class="col-md-2">
			<div class="card text-center">
				<div class="card-body">
					<h3 class="mb-0"><?php echo $stats['total']; ?></h3>
					<small class="text-muted">Total</small>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<div class="card text-center border-warning">
				<div class="card-body">
					<h3 class="mb-0 text-warning"><?php echo $stats['borradores']; ?></h3>
					<small class="text-muted">Borradores</small>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<div class="card text-center border-success">
				<div class="card-body">
					<h3 class="mb-0 text-success"><?php echo $stats['aplicadas']; ?></h3>
					<small class="text-muted">Aplicadas</small>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<div class="card text-center border-secondary">
				<div class="card-body">
					<h3 class="mb-0 text-secondary"><?php echo $stats['canceladas']; ?></h3>
					<small class="text-muted">Canceladas</small>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<div class="card text-center border-danger">
				<div class="card-body">
					<h3 class="mb-0 text-danger"><?php echo $stats['desbalanceadas']; ?></h3>
					<small class="text-muted">Desbalanceadas</small>
				</div>
			</div>
		</div>
	</div>

	<!-- Filtros -->
	<div class="card mb-4">
		<div class="card-body">
			<form method="get" action="<?php echo Uri::create('admin/polizas'); ?>" class="row g-3">
				<div class="col-md-3">
					<label class="form-label">Periodo</label>
					<input type="month" name="period" class="form-control" value="<?php echo $period; ?>">
				</div>
				<div class="col-md-2">
					<label class="form-label">Tipo</label>
					<select name="type" class="form-select">
						<option value="">Todos</option>
						<option value="ingreso" <?php echo $type_filter == 'ingreso' ? 'selected' : ''; ?>>Ingreso</option>
						<option value="egreso" <?php echo $type_filter == 'egreso' ? 'selected' : ''; ?>>Egreso</option>
						<option value="diario" <?php echo $type_filter == 'diario' ? 'selected' : ''; ?>>Diario</option>
						<option value="apertura" <?php echo $type_filter == 'apertura' ? 'selected' : ''; ?>>Apertura</option>
						<option value="ajuste" <?php echo $type_filter == 'ajuste' ? 'selected' : ''; ?>>Ajuste</option>
						<option value="cierre" <?php echo $type_filter == 'cierre' ? 'selected' : ''; ?>>Cierre</option>
					</select>
				</div>
				<div class="col-md-2">
					<label class="form-label">Estado</label>
					<select name="status" class="form-select">
						<option value="">Todos</option>
						<option value="borrador" <?php echo $status_filter == 'borrador' ? 'selected' : ''; ?>>Borrador</option>
						<option value="aplicada" <?php echo $status_filter == 'aplicada' ? 'selected' : ''; ?>>Aplicada</option>
						<option value="cancelada" <?php echo $status_filter == 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
						<option value="revisada" <?php echo $status_filter == 'revisada' ? 'selected' : ''; ?>>Revisada</option>
					</select>
				</div>
				<div class="col-md-3">
					<label class="form-label">Buscar</label>
					<input type="text" name="search" class="form-control" value="<?php echo $search; ?>" placeholder="Folio, concepto...">
				</div>
				<div class="col-md-2 d-flex align-items-end">
					<button type="submit" class="btn btn-primary me-2">
						<i class="fas fa-search"></i> Filtrar
					</button>
					<a href="<?php echo Uri::create('admin/polizas'); ?>" class="btn btn-secondary">
						<i class="fas fa-times"></i>
					</a>
				</div>
			</form>
		</div>
	</div>

	<!-- Tabla de pólizas -->
	<div class="card">
		<div class="card-body">
			<?php if (empty($entries)): ?>
				<div class="alert alert-info text-center">
					<i class="fas fa-info-circle"></i> No hay pólizas registradas con los filtros aplicados
				</div>
			<?php else: ?>
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Folio</th>
								<th>Fecha</th>
								<th>Tipo</th>
								<th>Concepto</th>
								<th>Cargos</th>
								<th>Abonos</th>
								<th>Balance</th>
								<th>Estado</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($entries as $entry): ?>
								<tr class="<?php echo !$entry->is_balanced ? 'table-danger' : ''; ?>">
									<td>
										<strong><?php echo $entry->entry_number; ?></strong>
									</td>
									<td><?php echo date('d/m/Y', strtotime($entry->entry_date)); ?></td>
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
										$type_icons = array(
											'ingreso' => 'arrow-down',
											'egreso' => 'arrow-up',
											'diario' => 'book',
											'apertura' => 'play',
											'ajuste' => 'wrench',
											'cierre' => 'stop'
										);
										$badge = $type_badges[$entry->entry_type] ?? 'secondary';
										$icon = $type_icons[$entry->entry_type] ?? 'file';
										?>
										<span class="badge bg-<?php echo $badge; ?>">
											<i class="fas fa-<?php echo $icon; ?>"></i> <?php echo ucfirst($entry->entry_type); ?>
										</span>
									</td>
									<td>
										<?php echo $entry->concept; ?>
										<?php if ($entry->reference): ?>
											<br><small class="text-muted">Ref: <?php echo $entry->reference; ?></small>
										<?php endif; ?>
									</td>
									<td class="text-end">$<?php echo number_format($entry->total_debit, 2); ?></td>
									<td class="text-end">$<?php echo number_format($entry->total_credit, 2); ?></td>
									<td class="text-center">
										<?php if ($entry->is_balanced): ?>
											<i class="fas fa-check-circle text-success" title="Balanceada"></i>
										<?php else: ?>
											<i class="fas fa-exclamation-triangle text-danger" title="Desbalanceada"></i>
										<?php endif; ?>
									</td>
									<td>
										<?php
										$status_badges = array(
											'borrador' => 'warning',
											'aplicada' => 'success',
											'cancelada' => 'secondary',
											'revisada' => 'info'
										);
										$badge = $status_badges[$entry->status] ?? 'secondary';
										?>
										<span class="badge bg-<?php echo $badge; ?>">
											<?php echo ucfirst($entry->status); ?>
										</span>
									</td>
									<td>
										<a href="<?php echo Uri::create('admin/polizas/view/' . $entry->id); ?>" 
										   class="btn btn-sm btn-info" title="Ver detalles">
											<i class="fas fa-eye"></i>
										</a>
										<?php if ($entry->status == 'borrador' && $can_delete): ?>
											<a href="<?php echo Uri::create('admin/polizas/delete/' . $entry->id); ?>" 
											   class="btn btn-sm btn-danger"
											   onclick="return confirm('¿Eliminar esta póliza?');"
											   title="Eliminar">
												<i class="fas fa-trash"></i>
											</a>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
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
