<!-- HEADER -->
<div class="row mb-4">
	<div class="col-md-12">
		<div class="d-flex justify-content-between align-items-center">
			<div>
				<h2 class="mb-1"><i class="fas fa-shopping-cart me-2"></i>Ventas</h2>
				<p class="text-muted mb-0">Gestión de ventas y pedidos</p>
			</div>
			<?php if ($can_create): ?>
			<div>
				<a href="<?php echo Uri::create('admin/sales/new'); ?>" class="btn btn-primary">
					<i class="fas fa-plus me-2"></i>Nueva Venta
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
						<h6 class="text-muted mb-2">Total Ventas</h6>
						<h3 class="mb-0"><?php echo number_format($stats['total_sales']); ?></h3>
					</div>
					<div class="text-primary">
						<i class="fas fa-shopping-cart fa-2x"></i>
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
						<h6 class="text-muted mb-2">Monto Total</h6>
						<h3 class="mb-0">$<?php echo number_format($stats['total_amount'], 2); ?></h3>
					</div>
					<div class="text-success">
						<i class="fas fa-dollar-sign fa-2x"></i>
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
						<h6 class="text-muted mb-2">Completadas</h6>
						<h3 class="mb-0">$<?php echo number_format($stats['completed_amount'], 2); ?></h3>
					</div>
					<div class="text-info">
						<i class="fas fa-check-circle fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- LISTA DE VENTAS -->
<div class="card">
	<div class="card-header">
		<h5 class="mb-0">Ventas Recientes</h5>
	</div>
	<div class="card-body">
		<?php if (count($sales) > 0): ?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>ID</th>
						<th>Fecha</th>
						<th>Cliente</th>
						<th>Usuario</th>
						<th>Total</th>
						<th>Estado</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($sales as $sale): ?>
					<tr>
						<td>#<?php echo $sale['id']; ?></td>
						<td><?php echo date('d/m/Y H:i', strtotime($sale['sale_date'])); ?></td>
						<td>
							<?php 
							if (!empty($sale['company_name'])) {
								echo htmlspecialchars($sale['company_name']);
							} elseif (!empty($sale['first_name']) || !empty($sale['last_name'])) {
								echo htmlspecialchars(trim($sale['first_name'] . ' ' . $sale['last_name']));
							} else {
								echo 'Cliente General';
							}
							?>
						</td>
						<td>-</td>
						<td><strong>$<?php echo number_format($sale['total'], 2); ?></strong></td>
						<td>
							<?php
							$status_badges = [
								0 => '<span class="badge bg-secondary"><i class="fas fa-shopping-cart me-1"></i>Carrito</span>',
								1 => '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Pagada</span>',
								2 => '<span class="badge bg-info"><i class="fas fa-exchange-alt me-1"></i>Transferencia</span>',
								3 => '<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Pendiente</span>',
								4 => '<span class="badge bg-primary"><i class="fas fa-truck me-1"></i>Enviada</span>',
								5 => '<span class="badge bg-success"><i class="fas fa-check-double me-1"></i>Entregada</span>',
								-1 => '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Cancelada</span>'
							];
							echo $status_badges[$sale['status']] ?? '<span class="badge bg-secondary">Desconocido</span>';
							?>
						</td>
						<td>
							<a href="<?php echo Uri::create('admin/sales/view/' . $sale['id']); ?>" class="btn btn-sm btn-info" title="Ver detalle">
								<i class="fas fa-eye"></i>
							</a>
							<?php if ($can_edit): ?>
							<a href="<?php echo Uri::create('admin/sales/edit/' . $sale['id']); ?>" class="btn btn-sm btn-warning" title="Editar">
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
			<i class="fas fa-info-circle me-2"></i>No hay ventas registradas.
			<?php if ($can_create): ?>
			<a href="<?php echo Uri::create('admin/sales/new'); ?>">Crear la primera venta</a>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</div>
