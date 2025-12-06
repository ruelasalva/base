<div class="container-fluid">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h2><i class="fas fa-balance-scale"></i> Saldos por Cuenta</h2>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/libromayor'); ?>">Libro Mayor</a></li>
					<li class="breadcrumb-item active">Saldos</li>
				</ol>
			</nav>
		</div>
		<a href="<?php echo Uri::create('admin/libromayor'); ?>" class="btn btn-secondary">
			<i class="fas fa-arrow-left"></i> Volver
		</a>
	</div>

	<!-- Filtro de periodo -->
	<div class="card mb-4">
		<div class="card-body">
			<form method="get" class="row g-3">
				<div class="col-md-3">
					<label class="form-label">Periodo (hasta)</label>
					<input type="month" name="period" class="form-control" value="<?php echo $period; ?>">
				</div>
				<div class="col-md-2 d-flex align-items-end">
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-search"></i> Consultar
					</button>
				</div>
			</form>
		</div>
	</div>

	<div class="row">
		<!-- Activos -->
		<div class="col-md-6 mb-4">
			<div class="card">
				<div class="card-header bg-success text-white">
					<h5 class="mb-0">Activos</h5>
				</div>
				<div class="card-body p-0">
					<?php if (empty($grouped_balances['activo'])): ?>
						<div class="p-3 text-muted">Sin movimientos</div>
					<?php else: ?>
						<table class="table table-sm mb-0">
							<thead class="table-light">
								<tr>
									<th>Código</th>
									<th>Cuenta</th>
									<th class="text-end">Saldo</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($grouped_balances['activo'] as $account): ?>
									<tr>
										<td><?php echo $account['account_code']; ?></td>
										<td>
											<a href="<?php echo Uri::create('admin/libromayor?account_id=' . $account['id'] . '&period_to=' . $period); ?>">
												<?php echo $account['name']; ?>
											</a>
										</td>
										<td class="text-end">
											<strong>$<?php echo number_format($account['balance'], 2); ?></strong>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot class="table-light">
								<tr>
									<th colspan="2">Total Activos</th>
									<th class="text-end">$<?php echo number_format($totals['activo'], 2); ?></th>
								</tr>
							</tfoot>
						</table>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Pasivos -->
		<div class="col-md-6 mb-4">
			<div class="card">
				<div class="card-header bg-danger text-white">
					<h5 class="mb-0">Pasivos</h5>
				</div>
				<div class="card-body p-0">
					<?php if (empty($grouped_balances['pasivo'])): ?>
						<div class="p-3 text-muted">Sin movimientos</div>
					<?php else: ?>
						<table class="table table-sm mb-0">
							<thead class="table-light">
								<tr>
									<th>Código</th>
									<th>Cuenta</th>
									<th class="text-end">Saldo</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($grouped_balances['pasivo'] as $account): ?>
									<tr>
										<td><?php echo $account['account_code']; ?></td>
										<td>
											<a href="<?php echo Uri::create('admin/libromayor?account_id=' . $account['id'] . '&period_to=' . $period); ?>">
												<?php echo $account['name']; ?>
											</a>
										</td>
										<td class="text-end">
											<strong>$<?php echo number_format($account['balance'], 2); ?></strong>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot class="table-light">
								<tr>
									<th colspan="2">Total Pasivos</th>
									<th class="text-end">$<?php echo number_format($totals['pasivo'], 2); ?></th>
								</tr>
							</tfoot>
						</table>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Capital -->
		<div class="col-md-6 mb-4">
			<div class="card">
				<div class="card-header bg-info text-white">
					<h5 class="mb-0">Capital</h5>
				</div>
				<div class="card-body p-0">
					<?php if (empty($grouped_balances['capital'])): ?>
						<div class="p-3 text-muted">Sin movimientos</div>
					<?php else: ?>
						<table class="table table-sm mb-0">
							<thead class="table-light">
								<tr>
									<th>Código</th>
									<th>Cuenta</th>
									<th class="text-end">Saldo</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($grouped_balances['capital'] as $account): ?>
									<tr>
										<td><?php echo $account['account_code']; ?></td>
										<td>
											<a href="<?php echo Uri::create('admin/libromayor?account_id=' . $account['id'] . '&period_to=' . $period); ?>">
												<?php echo $account['name']; ?>
											</a>
										</td>
										<td class="text-end">
											<strong>$<?php echo number_format($account['balance'], 2); ?></strong>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot class="table-light">
								<tr>
									<th colspan="2">Total Capital</th>
									<th class="text-end">$<?php echo number_format($totals['capital'], 2); ?></th>
								</tr>
							</tfoot>
						</table>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Ingresos -->
		<div class="col-md-6 mb-4">
			<div class="card">
				<div class="card-header bg-primary text-white">
					<h5 class="mb-0">Ingresos</h5>
				</div>
				<div class="card-body p-0">
					<?php if (empty($grouped_balances['ingreso'])): ?>
						<div class="p-3 text-muted">Sin movimientos</div>
					<?php else: ?>
						<table class="table table-sm mb-0">
							<thead class="table-light">
								<tr>
									<th>Código</th>
									<th>Cuenta</th>
									<th class="text-end">Saldo</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($grouped_balances['ingreso'] as $account): ?>
									<tr>
										<td><?php echo $account['account_code']; ?></td>
										<td>
											<a href="<?php echo Uri::create('admin/libromayor?account_id=' . $account['id'] . '&period_to=' . $period); ?>">
												<?php echo $account['name']; ?>
											</a>
										</td>
										<td class="text-end">
											<strong>$<?php echo number_format($account['balance'], 2); ?></strong>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot class="table-light">
								<tr>
									<th colspan="2">Total Ingresos</th>
									<th class="text-end">$<?php echo number_format($totals['ingreso'], 2); ?></th>
								</tr>
							</tfoot>
						</table>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Gastos -->
		<div class="col-md-6 mb-4">
			<div class="card">
				<div class="card-header bg-warning">
					<h5 class="mb-0">Gastos</h5>
				</div>
				<div class="card-body p-0">
					<?php if (empty($grouped_balances['gasto'])): ?>
						<div class="p-3 text-muted">Sin movimientos</div>
					<?php else: ?>
						<table class="table table-sm mb-0">
							<thead class="table-light">
								<tr>
									<th>Código</th>
									<th>Cuenta</th>
									<th class="text-end">Saldo</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($grouped_balances['gasto'] as $account): ?>
									<tr>
										<td><?php echo $account['account_code']; ?></td>
										<td>
											<a href="<?php echo Uri::create('admin/libromayor?account_id=' . $account['id'] . '&period_to=' . $period); ?>">
												<?php echo $account['name']; ?>
											</a>
										</td>
										<td class="text-end">
											<strong>$<?php echo number_format($account['balance'], 2); ?></strong>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot class="table-light">
								<tr>
									<th colspan="2">Total Gastos</th>
									<th class="text-end">$<?php echo number_format($totals['gasto'], 2); ?></th>
								</tr>
							</tfoot>
						</table>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Resultado -->
		<div class="col-md-6 mb-4">
			<div class="card">
				<div class="card-header bg-secondary text-white">
					<h5 class="mb-0">Resultado</h5>
				</div>
				<div class="card-body p-0">
					<?php if (empty($grouped_balances['resultado'])): ?>
						<div class="p-3 text-muted">Sin movimientos</div>
					<?php else: ?>
						<table class="table table-sm mb-0">
							<thead class="table-light">
								<tr>
									<th>Código</th>
									<th>Cuenta</th>
									<th class="text-end">Saldo</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($grouped_balances['resultado'] as $account): ?>
									<tr>
										<td><?php echo $account['account_code']; ?></td>
										<td>
											<a href="<?php echo Uri::create('admin/libromayor?account_id=' . $account['id'] . '&period_to=' . $period); ?>">
												<?php echo $account['name']; ?>
											</a>
										</td>
										<td class="text-end">
											<strong>$<?php echo number_format($account['balance'], 2); ?></strong>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot class="table-light">
								<tr>
									<th colspan="2">Total Resultado</th>
									<th class="text-end">$<?php echo number_format($totals['resultado'], 2); ?></th>
								</tr>
							</tfoot>
						</table>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Ecuación contable -->
	<div class="card">
		<div class="card-header bg-dark text-white">
			<h5 class="mb-0"><i class="fas fa-equals"></i> Ecuación Contable</h5>
		</div>
		<div class="card-body">
			<div class="row text-center">
				<div class="col-md-4">
					<h3 class="text-success">$<?php echo number_format($totals['activo'], 2); ?></h3>
					<p class="mb-0">Activos</p>
				</div>
				<div class="col-md-1 d-flex align-items-center justify-content-center">
					<h2>=</h2>
				</div>
				<div class="col-md-3">
					<h3 class="text-danger">$<?php echo number_format($totals['pasivo'], 2); ?></h3>
					<p class="mb-0">Pasivos</p>
				</div>
				<div class="col-md-1 d-flex align-items-center justify-content-center">
					<h2>+</h2>
				</div>
				<div class="col-md-3">
					<h3 class="text-info">$<?php echo number_format($totals['capital'], 2); ?></h3>
					<p class="mb-0">Capital</p>
				</div>
			</div>
			<hr>
			<div class="row text-center">
				<div class="col-md-12">
					<p class="mb-0 text-muted">
						Utilidad del periodo: 
						<strong>$<?php echo number_format($totals['ingreso'] - $totals['gasto'], 2); ?></strong>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
