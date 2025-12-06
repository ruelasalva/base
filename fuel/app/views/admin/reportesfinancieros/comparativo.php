<div class="container-fluid">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h2><i class="fas fa-chart-line"></i> Comparativo de Resultados</h2>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
					<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin/reportesfinancieros'); ?>">Reportes</a></li>
					<li class="breadcrumb-item active">Comparativo</li>
				</ol>
			</nav>
		</div>
		<a href="<?php echo Uri::create('admin/reportesfinancieros'); ?>" class="btn btn-secondary">
			<i class="fas fa-arrow-left"></i> Volver
		</a>
	</div>

	<!-- Tabla comparativa -->
	<div class="card mb-4">
		<div class="card-header bg-primary text-white">
			<h5 class="mb-0">Comparativo de Últimos 6 Meses</h5>
		</div>
		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="table table-sm mb-0">
					<thead class="table-light">
						<tr>
							<th>Concepto</th>
							<?php foreach ($periods as $period): ?>
								<th class="text-end"><?php echo $period; ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<!-- Ingresos -->
						<tr class="table-success">
							<td><strong>Ingresos</strong></td>
							<?php foreach ($periods as $period): ?>
								<td class="text-end">
									<strong>$<?php echo number_format($comparative[$period]['ingresos'], 2); ?></strong>
								</td>
							<?php endforeach; ?>
						</tr>
						
						<!-- Gastos -->
						<tr class="table-warning">
							<td><strong>Gastos</strong></td>
							<?php foreach ($periods as $period): ?>
								<td class="text-end">
									<strong>$<?php echo number_format($comparative[$period]['gastos'], 2); ?></strong>
								</td>
							<?php endforeach; ?>
						</tr>
						
						<!-- Utilidad -->
						<tr class="table-info">
							<td><strong>Utilidad Neta</strong></td>
							<?php foreach ($periods as $period): ?>
								<?php $util = $comparative[$period]['utilidad']; ?>
								<td class="text-end">
									<strong class="text-<?php echo $util >= 0 ? 'success' : 'danger'; ?>">
										$<?php echo number_format(abs($util), 2); ?>
									</strong>
								</td>
							<?php endforeach; ?>
						</tr>
						
						<!-- Margen -->
						<tr>
							<td><strong>Margen %</strong></td>
							<?php foreach ($periods as $period): ?>
								<?php 
								$ing = $comparative[$period]['ingresos'];
								$util = $comparative[$period]['utilidad'];
								$margin = $ing > 0 ? ($util / $ing) * 100 : 0;
								?>
								<td class="text-end">
									<span class="text-<?php echo $margin >= 0 ? 'success' : 'danger'; ?>">
										<?php echo number_format($margin, 1); ?>%
									</span>
								</td>
							<?php endforeach; ?>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- Gráficas visuales (simuladas con barras CSS) -->
	<div class="row">
		<div class="col-md-6">
			<div class="card">
				<div class="card-header">
					<h5 class="mb-0">Tendencia de Ingresos</h5>
				</div>
				<div class="card-body">
					<?php 
					$max_ing = 0;
					foreach ($comparative as $data) {
						if ($data['ingresos'] > $max_ing) $max_ing = $data['ingresos'];
					}
					?>
					<?php foreach ($periods as $period): ?>
						<?php 
						$ing = $comparative[$period]['ingresos'];
						$percent = $max_ing > 0 ? ($ing / $max_ing) * 100 : 0;
						?>
						<div class="mb-3">
							<div class="d-flex justify-content-between mb-1">
								<small><?php echo $period; ?></small>
								<small>$<?php echo number_format($ing, 2); ?></small>
							</div>
							<div class="progress" style="height: 25px;">
								<div class="progress-bar bg-success" style="width: <?php echo $percent; ?>%">
									<?php echo number_format($percent, 0); ?>%
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="card">
				<div class="card-header">
					<h5 class="mb-0">Tendencia de Utilidad</h5>
				</div>
				<div class="card-body">
					<?php 
					$max_util = 0;
					foreach ($comparative as $data) {
						if (abs($data['utilidad']) > $max_util) $max_util = abs($data['utilidad']);
					}
					?>
					<?php foreach ($periods as $period): ?>
						<?php 
						$util = $comparative[$period]['utilidad'];
						$percent = $max_util > 0 ? (abs($util) / $max_util) * 100 : 0;
						$color = $util >= 0 ? 'success' : 'danger';
						?>
						<div class="mb-3">
							<div class="d-flex justify-content-between mb-1">
								<small><?php echo $period; ?></small>
								<small class="text-<?php echo $color; ?>">$<?php echo number_format(abs($util), 2); ?></small>
							</div>
							<div class="progress" style="height: 25px;">
								<div class="progress-bar bg-<?php echo $color; ?>" style="width: <?php echo $percent; ?>%">
									<?php echo number_format($percent, 0); ?>%
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Resumen estadístico -->
	<div class="card mt-4">
		<div class="card-header bg-dark text-white">
			<h5 class="mb-0"><i class="fas fa-calculator"></i> Resumen Estadístico</h5>
		</div>
		<div class="card-body">
			<?php
			// Calcular promedios y totales
			$total_ing = 0;
			$total_gas = 0;
			$total_util = 0;
			$count = count($periods);
			
			foreach ($comparative as $data) {
				$total_ing += $data['ingresos'];
				$total_gas += $data['gastos'];
				$total_util += $data['utilidad'];
			}
			
			$avg_ing = $count > 0 ? $total_ing / $count : 0;
			$avg_gas = $count > 0 ? $total_gas / $count : 0;
			$avg_util = $count > 0 ? $total_util / $count : 0;
			?>
			<div class="row text-center">
				<div class="col-md-2">
					<strong>Periodos</strong><br>
					<h3><?php echo $count; ?></h3>
				</div>
				<div class="col-md-2">
					<strong>Total Ingresos</strong><br>
					<h3 class="text-success">$<?php echo number_format($total_ing, 0); ?></h3>
				</div>
				<div class="col-md-2">
					<strong>Promedio Ingresos</strong><br>
					<h3>$<?php echo number_format($avg_ing, 0); ?></h3>
				</div>
				<div class="col-md-2">
					<strong>Total Gastos</strong><br>
					<h3 class="text-warning">$<?php echo number_format($total_gas, 0); ?></h3>
				</div>
				<div class="col-md-2">
					<strong>Promedio Gastos</strong><br>
					<h3>$<?php echo number_format($avg_gas, 0); ?></h3>
				</div>
				<div class="col-md-2">
					<strong>Utilidad Promedio</strong><br>
					<h3 class="text-<?php echo $avg_util >= 0 ? 'success' : 'danger'; ?>">
						$<?php echo number_format(abs($avg_util), 0); ?>
					</h3>
				</div>
			</div>
		</div>
	</div>
</div>
