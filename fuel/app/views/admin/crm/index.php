<!-- HEADER -->
<div class="row mb-4">
	<div class="col-md-12">
		<div class="d-flex justify-content-between align-items-center">
			<div>
				<h2 class="mb-1"><i class="fas fa-user-friends me-2"></i>CRM - Gestión de Clientes</h2>
				<p class="text-muted mb-0">Administra tus relaciones con clientes</p>
			</div>
			<?php if ($can_create): ?>
			<div>
				<a href="<?php echo Uri::create('admin/crm/new'); ?>" class="btn btn-primary">
					<i class="fas fa-user-plus me-2"></i>Nuevo Cliente
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
						<h6 class="text-muted mb-2">Total Clientes</h6>
						<h3 class="mb-0"><?php echo number_format($stats['total_customers']); ?></h3>
					</div>
					<div class="text-primary">
						<i class="fas fa-users fa-2x"></i>
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
						<h6 class="text-muted mb-2">Clientes Activos</h6>
						<h3 class="mb-0 text-success"><?php echo number_format($stats['active_customers']); ?></h3>
					</div>
					<div class="text-success">
						<i class="fas fa-user-check fa-2x"></i>
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
						<h6 class="text-muted mb-2">Nuevos este Mes</h6>
						<h3 class="mb-0 text-info"><?php echo $stats['new_this_month']; ?></h3>
					</div>
					<div class="text-info">
						<i class="fas fa-user-plus fa-2x"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- LISTA DE CLIENTES -->
<div class="card">
	<div class="card-header">
		<div class="d-flex justify-content-between align-items-center">
			<h5 class="mb-0">Clientes</h5>
			<div class="input-group" style="width: 300px;">
				<input type="text" class="form-control" placeholder="Buscar cliente..." id="searchCustomer">
				<button class="btn btn-outline-secondary" type="button">
					<i class="fas fa-search"></i>
				</button>
			</div>
		</div>
	</div>
	<div class="card-body">
		<?php if (count($customers) > 0): ?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Nombre/Empresa</th>
						<th>Razón Social</th>
						<th>Código</th>
						<th>RFC/Tax ID</th>
						<th>Tipo</th>
						<th>Estado</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($customers as $customer): ?>
					<tr>
						<td>
							<strong>
								<?php 
								if ($customer['customer_type'] == 'business' && $customer['company_name']) {
									echo htmlspecialchars($customer['company_name']);
								} else {
									echo htmlspecialchars(trim($customer['first_name'] . ' ' . $customer['last_name']));
								}
								?>
							</strong>
						</td>
						<td><?php echo htmlspecialchars($customer['company_name'] ?? '-'); ?></td>
						<td><?php echo htmlspecialchars($customer['code'] ?? '-'); ?></td>
						<td><?php echo htmlspecialchars($customer['tax_id'] ?? '-'); ?></td>
						<td><?php echo htmlspecialchars($customer['customer_type'] == 'business' ? 'Empresa' : 'Individual'); ?></td>
						<td>
							<?php if ($customer['is_active']): ?>
							<span class="badge bg-success">Activo</span>
							<?php else: ?>
							<span class="badge bg-secondary">Inactivo</span>
							<?php endif; ?>
						</td>
						<td>
							<a href="<?php echo Uri::create('admin/crm/view/' . $customer['id']); ?>" class="btn btn-sm btn-info" title="Ver">
								<i class="fas fa-eye"></i>
							</a>
							<?php if ($can_edit): ?>
							<a href="<?php echo Uri::create('admin/crm/edit/' . $customer['id']); ?>" class="btn btn-sm btn-warning" title="Editar">
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
			<i class="fas fa-info-circle me-2"></i>No hay clientes registrados.
			<?php if ($can_create): ?>
			<a href="<?php echo Uri::create('admin/crm/new'); ?>">Agregar el primer cliente</a>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</div>
