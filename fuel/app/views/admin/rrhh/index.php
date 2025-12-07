<?php
/**
 * Vista: Dashboard de Recursos Humanos
 */
?>
<div class="container-fluid">
	<div class="mb-4">
		<h2><i class="fa fa-chart-line"></i> Dashboard de Recursos Humanos</h2>
		<p class="text-muted">Vista ejecutiva de indicadores clave de personal</p>
	</div>

	<!-- KPIs Principales -->
	<div class="row mb-4">
		<div class="col-lg-3 col-md-6 mb-3">
			<div class="card border-left-primary shadow h-100">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
								Empleados Activos
							</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								<?php echo number_format($kpis['total_employees']); ?>
							</div>
						</div>
						<div class="col-auto">
							<i class="fa fa-users fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-3 col-md-6 mb-3">
			<div class="card border-left-success shadow h-100">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-success text-uppercase mb-1">
								Nuevas Contrataciones
							</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								<?php echo number_format($kpis['new_hires']); ?>
							</div>
							<small class="text-muted">Este mes</small>
						</div>
						<div class="col-auto">
							<i class="fa fa-user-plus fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-3 col-md-6 mb-3">
			<div class="card border-left-info shadow h-100">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-info text-uppercase mb-1">
								Nómina Mensual
							</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								$<?php echo number_format($kpis['current_payroll'], 2); ?>
							</div>
						</div>
						<div class="col-auto">
							<i class="fa fa-money-bill-wave fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-3 col-md-6 mb-3">
			<div class="card border-left-warning shadow h-100">
				<div class="card-body">
					<div class="row no-gutters align-items-center">
						<div class="col mr-2">
							<div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
								Tasa de Rotación
							</div>
							<div class="h5 mb-0 font-weight-bold text-gray-800">
								<?php echo number_format($kpis['turnover_rate'], 2); ?>%
							</div>
							<small class="text-muted"><?php echo $kpis['terminations']; ?> bajas este mes</small>
						</div>
						<div class="col-auto">
							<i class="fa fa-exchange-alt fa-2x text-gray-300"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Alertas -->
	<?php if (!empty($alerts)): ?>
		<div class="row mb-4">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h5 class="mb-0"><i class="fa fa-bell"></i> Alertas y Notificaciones</h5>
					</div>
					<div class="card-body">
						<?php foreach ($alerts as $alert): ?>
							<div class="alert alert-<?php echo $alert['type']; ?> d-flex align-items-center">
								<i class="fa <?php echo $alert['icon']; ?> fa-2x me-3"></i>
								<div class="flex-grow-1">
									<strong><?php echo htmlspecialchars($alert['title'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
									<?php echo htmlspecialchars($alert['message'], ENT_QUOTES, 'UTF-8'); ?>
								</div>
								<a href="<?php echo Uri::create($alert['link']); ?>" class="btn btn-sm btn-<?php echo $alert['type']; ?>">
									Ver más <i class="fa fa-arrow-right"></i>
								</a>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<!-- Gráficos -->
	<div class="row mb-4">
		<div class="col-lg-8 mb-3">
			<div class="card shadow">
				<div class="card-header">
					<h5 class="mb-0"><i class="fa fa-chart-bar"></i> Contrataciones por Mes</h5>
				</div>
				<div class="card-body">
					<canvas id="hiresChart" height="80"></canvas>
				</div>
			</div>
		</div>

		<div class="col-lg-4 mb-3">
			<div class="card shadow">
				<div class="card-header">
					<h5 class="mb-0"><i class="fa fa-chart-pie"></i> Por Género</h5>
				</div>
				<div class="card-body">
					<canvas id="genderChart"></canvas>
					<div class="mt-3">
						<?php if (!empty($employee_stats['by_gender'])): ?>
							<?php foreach ($employee_stats['by_gender'] as $stat): ?>
								<div class="d-flex justify-content-between mb-2">
									<span><?php echo $stat['gender'] === 'M' ? 'Masculino' : ($stat['gender'] === 'F' ? 'Femenino' : 'Otro'); ?></span>
									<strong><?php echo $stat['count']; ?></strong>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Estadísticas detalladas -->
	<div class="row">
		<div class="col-lg-6 mb-3">
			<div class="card shadow">
				<div class="card-header">
					<h5 class="mb-0"><i class="fa fa-sitemap"></i> Empleados por Departamento</h5>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-sm">
							<thead>
								<tr>
									<th>Departamento</th>
									<th class="text-end">Empleados</th>
									<th width="40%">Proporción</th>
								</tr>
							</thead>
							<tbody>
								<?php if (!empty($employee_stats['by_department'])): ?>
									<?php 
									$total = array_sum(array_column($employee_stats['by_department'], 'count'));
									foreach ($employee_stats['by_department'] as $stat): 
										$percentage = $total > 0 ? ($stat['count'] / $total) * 100 : 0;
									?>
										<tr>
											<td><?php echo htmlspecialchars($stat['department'] ?: 'Sin asignar', ENT_QUOTES, 'UTF-8'); ?></td>
											<td class="text-end"><strong><?php echo $stat['count']; ?></strong></td>
											<td>
												<div class="progress" style="height: 20px;">
													<div class="progress-bar bg-primary" role="progressbar" 
														 style="width: <?php echo $percentage; ?>%"
														 aria-valuenow="<?php echo $percentage; ?>" 
														 aria-valuemin="0" aria-valuemax="100">
														<?php echo round($percentage, 1); ?>%
													</div>
												</div>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="3" class="text-center text-muted">No hay datos disponibles</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-6 mb-3">
			<div class="card shadow">
				<div class="card-header">
					<h5 class="mb-0"><i class="fa fa-clock"></i> Antigüedad del Personal</h5>
				</div>
				<div class="card-body">
					<canvas id="seniorityChart"></canvas>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Gráfico de contrataciones
const hiresCtx = document.getElementById('hiresChart').getContext('2d');
const hiresData = <?php echo json_encode($charts_data['hires_by_month']); ?>;
new Chart(hiresCtx, {
	type: 'line',
	data: {
		labels: hiresData.map(d => d.month),
		datasets: [{
			label: 'Contrataciones',
			data: hiresData.map(d => d.count),
			backgroundColor: 'rgba(54, 162, 235, 0.2)',
			borderColor: 'rgba(54, 162, 235, 1)',
			borderWidth: 2,
			tension: 0.4
		}]
	},
	options: {
		responsive: true,
		maintainAspectRatio: false
	}
});

// Gráfico de género
const genderCtx = document.getElementById('genderChart').getContext('2d');
const genderData = <?php echo json_encode($employee_stats['by_gender']); ?>;
new Chart(genderCtx, {
	type: 'doughnut',
	data: {
		labels: genderData.map(d => d.gender === 'M' ? 'Masculino' : (d.gender === 'F' ? 'Femenino' : 'Otro')),
		datasets: [{
			data: genderData.map(d => d.count),
			backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56']
		}]
	}
});

// Gráfico de antigüedad
const seniorityCtx = document.getElementById('seniorityChart').getContext('2d');
const seniorityData = <?php echo json_encode($employee_stats['by_seniority']); ?>;
new Chart(seniorityCtx, {
	type: 'bar',
	data: {
		labels: seniorityData.map(d => d.seniority),
		datasets: [{
			label: 'Empleados',
			data: seniorityData.map(d => d.count),
			backgroundColor: 'rgba(75, 192, 192, 0.6)',
			borderColor: 'rgba(75, 192, 192, 1)',
			borderWidth: 1
		}]
	},
	options: {
		responsive: true,
		scales: {
			y: {
				beginAtZero: true,
				ticks: {
					stepSize: 1
				}
			}
		}
	}
});
</script>

<style>
.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
</style>
