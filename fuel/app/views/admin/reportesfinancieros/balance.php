<div class="container-fluid">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h2><i class="fas fa-balance-scale"></i> Balance General</h2>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/reportesfinancieros'); ?>">Reportes</a></li>
					<li class="breadcrumb-item active">Balance</li>
				</ol>
			</nav>
		</div>
		<div>
			<a href="<?php echo Uri::create('admin/reportesfinancieros/export_balance?period=' . $period); ?>" 
			   class="btn btn-success me-2">
				<i class="fas fa-file-excel"></i> Exportar CSV
			</a>
			<a href="<?php echo Uri::create('admin/reportesfinancieros'); ?>" class="btn btn-secondary">
				<i class="fas fa-arrow-left"></i> Volver
			</a>
		</div>
	</div>

	<!-- Filtro de periodo -->
	<div class="card mb-4">
		<div class="card-body">
			<form method="get" class="row g-3">
				<div class="col-md-3">
					<label class="form-label">Periodo (al)</label>
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

	<!-- Balance -->
	<div class="row">
		<div class="col-md-6">
			<!-- ACTIVOS -->
			<div class="card mb-4">
				<div class="card-header bg-success text-white">
					<h5 class="mb-0">ACTIVOS</h5>
				</div>
				<div class="card-body p-0">
					<?php if (empty($accounts['activo'])): ?>
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
								<?php foreach ($accounts['activo'] as $account): ?>
									<tr>
										<td><?php echo $account['account_code']; ?></td>
										<td>
											<a href="<?php echo Uri::create('admin/libromayor?account_id=' . $account['id'] . '&period_to=' . $period); ?>">
												<?php echo $account['name']; ?>
											</a>
										</td>
										<td class="text-end">$<?php echo number_format(abs($account['balance']), 2); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot class="table-light">
								<tr>
									<th colspan="2">TOTAL ACTIVOS</th>
									<th class="text-end">$<?php echo number_format($totals['activo'], 2); ?></th>
								</tr>
							</tfoot>
						</table>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<!-- PASIVOS -->
			<div class="card mb-4">
				<div class="card-header bg-danger text-white">
					<h5 class="mb-0">PASIVOS</h5>
				</div>
				<div class="card-body p-0">
					<?php if (empty($accounts['pasivo'])): ?>
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
								<?php foreach ($accounts['pasivo'] as $account): ?>
									<tr>
										<td><?php echo $account['account_code']; ?></td>
										<td>
											<a href="<?php echo Uri::create('admin/libromayor?account_id=' . $account['id'] . '&period_to=' . $period); ?>">
												<?php echo $account['name']; ?>
											</a>
										</td>
										<td class="text-end">$<?php echo number_format(abs($account['balance']), 2); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot class="table-light">
								<tr>
									<th colspan="2">TOTAL PASIVOS</th>
									<th class="text-end">$<?php echo number_format($totals['pasivo'], 2); ?></th>
								</tr>
							</tfoot>
						</table>
					<?php endif; ?>
				</div>
			</div>

			<!-- CAPITAL -->
			<div class="card mb-4">
				<div class="card-header bg-info text-white">
					<h5 class="mb-0">CAPITAL</h5>
				</div>
				<div class="card-body p-0">
					<?php if (empty($accounts['capital'])): ?>
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
								<?php foreach ($accounts['capital'] as $account): ?>
									<tr>
										<td><?php echo $account['account_code']; ?></td>
										<td>
											<a href="<?php echo Uri::create('admin/libromayor?account_id=' . $account['id'] . '&period_to=' . $period); ?>">
												<?php echo $account['name']; ?>
											</a>
										</td>
										<td class="text-end">$<?php echo number_format(abs($account['balance']), 2); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot class="table-light">
								<tr>
									<th colspan="2">TOTAL CAPITAL</th>
									<th class="text-end">$<?php echo number_format($totals['capital'], 2); ?></th>
								</tr>
							</tfoot>
						</table>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Ecuación contable y razones financieras -->
	<div class="row">
		<div class="col-md-6">
			<div class="card">
				<div class="card-header bg-dark text-white">
					<h5 class="mb-0"><i class="fas fa-equals"></i> Ecuación Contable</h5>
				</div>
				<div class="card-body">
					<div class="row text-center">
						<div class="col-4">
							<h3 class="text-success">$<?php echo number_format($totals['activo'], 2); ?></h3>
							<p class="mb-0">Activos</p>
						</div>
						<div class="col-1 d-flex align-items-center justify-content-center">
							<h2>=</h2>
						</div>
						<div class="col-3">
							<h3 class="text-danger">$<?php echo number_format($totals['pasivo'], 2); ?></h3>
							<p class="mb-0">Pasivos</p>
						</div>
						<div class="col-1 d-flex align-items-center justify-content-center">
							<h2>+</h2>
						</div>
						<div class="col-3">
							<h3 class="text-info">$<?php echo number_format($totals['capital'], 2); ?></h3>
							<p class="mb-0">Capital</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="card">
				<div class="card-header bg-primary text-white">
					<h5 class="mb-0"><i class="fas fa-chart-pie"></i> Indicadores Financieros</h5>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-6 mb-3">
							<strong>Capital de Trabajo:</strong><br>
							<h4 class="mb-0">$<?php echo number_format($ratios['working_capital'], 2); ?></h4>
						</div>
						<div class="col-6 mb-3">
							<strong>Patrimonio:</strong><br>
							<h4 class="mb-0">$<?php echo number_format($ratios['equity'], 2); ?></h4>
						</div>
						<div class="col-6">
							<strong>Razón de Endeudamiento:</strong><br>
							<h4 class="mb-0"><?php echo number_format($ratios['debt_ratio'], 1); ?>%</h4>
						</div>
						<div class="col-6">
							<strong>Razón de Autonomía:</strong><br>
							<h4 class="mb-0"><?php echo number_format($ratios['autonomy_ratio'], 1); ?>%</h4>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
