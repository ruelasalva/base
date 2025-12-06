<div class="container-fluid">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h2><i class="fas fa-chart-bar"></i> Estado de Resultados</h2>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/reportesfinancieros'); ?>">Reportes</a></li>
					<li class="breadcrumb-item active">Estado de Resultados</li>
				</ol>
			</nav>
		</div>
		<div>
			<a href="<?php echo Uri::create('admin/reportesfinancieros/export_resultados?period_from=' . $period_from . '&period_to=' . $period_to); ?>" 
			   class="btn btn-success me-2">
				<i class="fas fa-file-excel"></i> Exportar CSV
			</a>
			<a href="<?php echo Uri::create('admin/reportesfinancieros'); ?>" class="btn btn-secondary">
				<i class="fas fa-arrow-left"></i> Volver
			</a>
		</div>
	</div>

	<!-- Filtros de periodo -->
	<div class="card mb-4">
		<div class="card-body">
			<form method="get" class="row g-3">
				<div class="col-md-3">
					<label class="form-label">Periodo Desde</label>
					<input type="month" name="period_from" class="form-control" value="<?php echo $period_from; ?>">
				</div>
				<div class="col-md-3">
					<label class="form-label">Periodo Hasta</label>
					<input type="month" name="period_to" class="form-control" value="<?php echo $period_to; ?>">
				</div>
				<div class="col-md-2 d-flex align-items-end">
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-search"></i> Consultar
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- Estado de Resultados -->
	<div class="card mb-4">
		<div class="card-header bg-primary text-white">
			<h5 class="mb-0">Estado de Resultados del <?php echo $period_from; ?> al <?php echo $period_to; ?></h5>
		</div>
		<div class="card-body p-0">
			<table class="table table-sm mb-0">
				<!-- INGRESOS -->
				<thead class="table-success">
					<tr>
						<th colspan="3" class="fs-6">INGRESOS</th>
					</tr>
					<tr class="table-light">
						<th width="15%">Código</th>
						<th>Cuenta</th>
						<th width="20%" class="text-end">Monto</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($accounts['ingreso'])): ?>
						<tr>
							<td colspan="3" class="text-muted">Sin ingresos registrados</td>
						</tr>
					<?php else: ?>
						<?php foreach ($accounts['ingreso'] as $account): ?>
							<tr>
								<td><?php echo $account['account_code']; ?></td>
								<td>
									<a href="<?php echo Uri::create('admin/libromayor?account_id=' . $account['id'] . '&period_from=' . $period_from . '&period_to=' . $period_to); ?>">
										<?php echo $account['name']; ?>
									</a>
								</td>
								<td class="text-end">$<?php echo number_format(abs($account['balance']), 2); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
				<tfoot class="table-success">
					<tr>
						<th colspan="2">TOTAL INGRESOS</th>
						<th class="text-end">$<?php echo number_format($totals['ingreso'], 2); ?></th>
					</tr>
				</tfoot>

				<!-- GASTOS -->
				<thead class="table-warning">
					<tr>
						<th colspan="3" class="fs-6">GASTOS</th>
					</tr>
					<tr class="table-light">
						<th>Código</th>
						<th>Cuenta</th>
						<th class="text-end">Monto</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($accounts['gasto'])): ?>
						<tr>
							<td colspan="3" class="text-muted">Sin gastos registrados</td>
						</tr>
					<?php else: ?>
						<?php foreach ($accounts['gasto'] as $account): ?>
							<tr>
								<td><?php echo $account['account_code']; ?></td>
								<td>
									<a href="<?php echo Uri::create('admin/libromayor?account_id=' . $account['id'] . '&period_from=' . $period_from . '&period_to=' . $period_to); ?>">
										<?php echo $account['name']; ?>
									</a>
								</td>
								<td class="text-end">$<?php echo number_format(abs($account['balance']), 2); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
				<tfoot class="table-warning">
					<tr>
						<th colspan="2">TOTAL GASTOS</th>
						<th class="text-end">$<?php echo number_format($totals['gasto'], 2); ?></th>
					</tr>
				</tfoot>

				<!-- UTILIDAD -->
				<tfoot class="<?php echo $net_income >= 0 ? 'table-success' : 'table-danger'; ?>">
					<tr>
						<th colspan="2" class="fs-5">
							<?php echo $net_income >= 0 ? 'UTILIDAD NETA' : 'PÉRDIDA NETA'; ?>
						</th>
						<th class="text-end fs-5">
							$<?php echo number_format(abs($net_income), 2); ?>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

	<!-- Análisis -->
	<div class="row">
		<div class="col-md-4">
			<div class="card text-center border-success">
				<div class="card-body">
					<h6 class="text-muted">Ingresos Totales</h6>
					<h2 class="text-success mb-0">$<?php echo number_format($totals['ingreso'], 2); ?></h2>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card text-center border-warning">
				<div class="card-body">
					<h6 class="text-muted">Gastos Totales</h6>
					<h2 class="text-warning mb-0">$<?php echo number_format($totals['gasto'], 2); ?></h2>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card text-center border-<?php echo $net_income >= 0 ? 'success' : 'danger'; ?>">
				<div class="card-body">
					<h6 class="text-muted"><?php echo $net_income >= 0 ? 'Utilidad Neta' : 'Pérdida Neta'; ?></h6>
					<h2 class="text-<?php echo $net_income >= 0 ? 'success' : 'danger'; ?> mb-0">
						$<?php echo number_format(abs($net_income), 2); ?>
					</h2>
				</div>
			</div>
		</div>
	</div>

	<!-- Indicadores -->
	<div class="card mt-4">
		<div class="card-header bg-info text-white">
			<h5 class="mb-0"><i class="fas fa-chart-pie"></i> Indicadores de Rentabilidad</h5>
		</div>
		<div class="card-body">
			<div class="row text-center">
				<div class="col-md-3">
					<strong>Margen de Utilidad</strong><br>
					<?php 
					$margin = $totals['ingreso'] > 0 ? ($net_income / $totals['ingreso']) * 100 : 0;
					?>
					<h3 class="text-<?php echo $margin >= 0 ? 'success' : 'danger'; ?>">
						<?php echo number_format($margin, 1); ?>%
					</h3>
					<small class="text-muted">Utilidad / Ingresos</small>
				</div>
				<div class="col-md-3">
					<strong>Proporción de Gastos</strong><br>
					<?php 
					$expense_ratio = $totals['ingreso'] > 0 ? ($totals['gasto'] / $totals['ingreso']) * 100 : 0;
					?>
					<h3 class="text-warning">
						<?php echo number_format($expense_ratio, 1); ?>%
					</h3>
					<small class="text-muted">Gastos / Ingresos</small>
				</div>
				<div class="col-md-3">
					<strong>Punto de Equilibrio</strong><br>
					<h3>
						$<?php echo number_format($totals['gasto'], 2); ?>
					</h3>
					<small class="text-muted">Ingreso mínimo requerido</small>
				</div>
				<div class="col-md-3">
					<strong>Rentabilidad</strong><br>
					<h3 class="text-<?php echo $net_income >= 0 ? 'success' : 'danger'; ?>">
						<?php echo $net_income >= 0 ? 'POSITIVA' : 'NEGATIVA'; ?>
					</h3>
					<small class="text-muted">Estado del periodo</small>
				</div>
			</div>
		</div>
	</div>
</div>
