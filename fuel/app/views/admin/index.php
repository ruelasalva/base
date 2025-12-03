<!-- DASHBOARD HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">
	<div>
		<h2 class="mb-1"><?php echo isset($title) ? $title : 'Dashboard'; ?></h2>
		<p class="text-muted mb-0">
			<i class="fas fa-calendar-alt me-2"></i><?php echo isset($date) ? $date : date('d/m/Y'); ?>
			<span class="ms-3 text-muted small">
				<i class="fas fa-sync-alt me-1"></i>
				<span id="last-update">Actualizado ahora</span>
			</span>
		</p>
	</div>
	<div class="d-flex gap-2">
		<button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.location.reload();" title="Refrescar dashboard">
			<i class="fas fa-sync-alt me-1"></i>Actualizar
		</button>
		<button type="button" class="btn btn-outline-primary" id="btn-configure-widgets">
			<i class="fas fa-cog me-2"></i>Configurar Widgets
		</button>
	</div>
</div>

<!-- WIDGETS GRID -->
<div class="row" id="dashboard-widgets">
	
	<?php if (isset($widgets_data) && is_array($widgets_data) && count($widgets_data) > 0): ?>
		
		<?php foreach ((isset($widgets_config['widgets']) && is_array($widgets_config['widgets'])) ? $widgets_config['widgets'] : [] as $widget_key): ?>
			<?php if (!isset($widgets_data[$widget_key])) continue; ?>
			<?php $widget = $widgets_data[$widget_key]; ?>
			
			<?php if ($widget_key === 'stats_users'): ?>
				<!-- WIDGET: Estadísticas de Usuarios -->
				<div class="col-xl-3 col-md-6 mb-4">
					<div class="card border-0 shadow-sm h-100">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1 small text-uppercase">Usuarios Totales</p>
								<h3 class="mb-0 fw-bold"><?php echo number_format(isset($widget['total_users']) ? $widget['total_users'] : 0); ?></h3>
								<small class="text-success">
									<i class="fas fa-arrow-up"></i> <?php echo number_format(isset($widget['active_percentage']) ? $widget['active_percentage'] : 0, 1); ?>% activos hoy
								</small>
							</div>
								<div class="bg-primary bg-opacity-10 rounded-3 p-3">
									<i class="fas fa-users fa-2x text-primary"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
			
			<?php if ($widget_key === 'sales_today'): ?>
				<!-- WIDGET: Ventas de Hoy -->
				<div class="col-xl-3 col-md-6 mb-4">
					<div class="card border-0 shadow-sm h-100">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1 small text-uppercase">Ventas de Hoy</p>
								<h3 class="mb-0 fw-bold">$<?php echo number_format(isset($widget['total_today']) ? $widget['total_today'] : 0, 2); ?></h3>
								<?php $trend = isset($widget['trend']) ? $widget['trend'] : 0; ?>
								<small class="<?php echo $trend >= 0 ? 'text-success' : 'text-danger'; ?>">
									<i class="fas fa-arrow-<?php echo $trend >= 0 ? 'up' : 'down'; ?>"></i>
									<?php echo abs($trend); ?>% vs ayer
								</small>
							</div>
								<div class="bg-success bg-opacity-10 rounded-3 p-3">
									<i class="fas fa-dollar-sign fa-2x text-success"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
			
			<?php if ($widget_key === 'sales_chart_week'): ?>
				<!-- WIDGET: Gráfica de Ventas Semanal -->
				<div class="col-xl-8 col-lg-7 mb-4">
					<div class="card border-0 shadow-sm h-100">
						<div class="card-header bg-white border-0 pb-0">
							<h5 class="card-title mb-0">
								<i class="fas fa-chart-line me-2 text-primary"></i>Ventas de los Últimos 7 Días
							</h5>
						</div>
						<div class="card-body">
							<canvas id="salesChartWeek" height="80"></canvas>
						</div>
					</div>
				</div>
			<?php endif; ?>
			
			<?php if ($widget_key === 'top_products'): ?>
				<!-- WIDGET: Top Productos -->
				<div class="col-xl-4 col-lg-5 mb-4">
					<div class="card border-0 shadow-sm h-100">
						<div class="card-header bg-white border-0 pb-0">
							<h5 class="card-title mb-0">
								<i class="fas fa-trophy me-2 text-warning"></i>Top 10 Productos
							</h5>
						</div>
						<div class="card-body">
							<canvas id="topProductsChart" height="250"></canvas>
						</div>
					</div>
				</div>
			<?php endif; ?>
			
			<?php if ($widget_key === 'cash_flow'): ?>
				<!-- WIDGET: Flujo de Efectivo -->
				<div class="col-xl-6 mb-4">
					<div class="card border-0 shadow-sm h-100">
						<div class="card-header bg-white border-0 pb-0">
							<h5 class="card-title mb-0">
								<i class="fas fa-chart-area me-2 text-success"></i>Flujo de Efectivo (30 días)
							</h5>
						</div>
						<div class="card-body">
							<canvas id="cashFlowChart" height="100"></canvas>
						</div>
					</div>
				</div>
			<?php endif; ?>
			
			<?php if ($widget_key === 'critical_inventory'): ?>
				<!-- WIDGET: Inventario Crítico -->
				<div class="col-xl-6 mb-4">
					<div class="card border-0 shadow-sm h-100">
						<div class="card-header bg-white border-0 pb-0">
							<h5 class="card-title mb-0">
								<i class="fas fa-exclamation-triangle me-2 text-danger"></i>Inventario Crítico
							</h5>
						</div>
						<div class="card-body">
							<?php if (isset($widget['products']) && is_array($widget['products']) && count($widget['products']) > 0): ?>
								<div class="table-responsive">
									<table class="table table-sm table-hover mb-0">
										<thead>
											<tr>
												<th>Producto</th>
												<th class="text-center">Stock</th>
												<th class="text-center">Mínimo</th>
												<th class="text-center">Estado</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($widget['products'] as $product): ?>
												<tr>
													<td><?php echo Html::anchor('admin/inventory/view/' . (isset($product['id']) ? $product['id'] : ''), isset($product['name']) ? $product['name'] : 'Sin nombre'); ?></td>
													<td class="text-center"><span class="badge bg-danger"><?php echo isset($product['stock']) ? $product['stock'] : 0; ?></span></td>
													<td class="text-center"><?php echo isset($product['min_stock']) ? $product['min_stock'] : 0; ?></td>
													<td class="text-center">
														<span class="badge bg-warning">
															<i class="fas fa-exclamation-circle"></i> Bajo
														</span>
													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							<?php else: ?>
								<div class="text-center py-4 text-muted">
									<i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
									<p class="mb-0">Todos los productos tienen stock suficiente</p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			
			<?php if ($widget_key === 'recent_activity'): ?>
				<!-- WIDGET: Actividad Reciente -->
				<div class="col-xl-6 mb-4">
					<div class="card border-0 shadow-sm h-100">
						<div class="card-header bg-white border-0 pb-0">
							<h5 class="card-title mb-0">
								<i class="fas fa-history me-2 text-info"></i>Actividad Reciente
							</h5>
						</div>
						<div class="card-body">
							<?php if (isset($widget['activities']) && is_array($widget['activities']) && count($widget['activities']) > 0): ?>
								<div class="activity-timeline">
									<?php foreach ($widget['activities'] as $activity): ?>
										<div class="activity-item d-flex mb-3">
											<div class="activity-icon me-3">
												<div class="bg-primary bg-opacity-10 rounded-circle p-2">
													<i class="fas fa-circle text-primary" style="font-size: 8px;"></i>
												</div>
											</div>
											<div class="activity-content flex-grow-1">
												<p class="mb-0"><?php echo isset($activity['description']) ? $activity['description'] : 'Actividad'; ?></p>
												<small class="text-muted">
													<?php echo isset($activity['user']) ? $activity['user'] : 'Sistema'; ?> • 
													<?php echo isset($activity['time_ago']) ? $activity['time_ago'] : 'Hace un momento'; ?>
												</small>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							<?php else: ?>
								<div class="text-center py-4 text-muted">
									<i class="fas fa-info-circle fa-3x mb-3"></i>
									<p class="mb-0">No hay actividad reciente</p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			
			<?php if ($widget_key === 'inventory_value'): ?>
				<!-- WIDGET: Valor de Inventario (ya existe arriba como card pequeña, esta es versión extendida) -->
			<?php endif; ?>
			
			<?php if ($widget_key === 'accounts_receivable'): ?>
				<!-- WIDGET: Cuentas por Cobrar (ya existe arriba como card pequeña, esta es versión extendida) -->
			<?php endif; ?>
			
		<?php endforeach; ?>
		
	<?php else: ?>
		
		<!-- NO HAY WIDGETS CONFIGURADOS -->
		<div class="col-12">
			<div class="alert alert-info">
				<i class="fas fa-info-circle me-2"></i>
				No hay widgets configurados. Haz clic en <strong>Configurar Widgets</strong> para personalizar tu dashboard.
			</div>
		</div>
		
	<?php endif; ?>
	
</div>

<!-- MODAL: Configurar Widgets -->
<div class="modal fade" id="modalConfigureWidgets" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">
					<i class="fas fa-cog me-2"></i>Configurar Widgets del Dashboard
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<p class="text-muted mb-4">Selecciona los widgets que deseas mostrar en tu dashboard:</p>
				
				<?php if (isset($available_widgets) && is_array($available_widgets) && count($available_widgets) > 0): ?>
					<div class="row">
						<?php foreach ($available_widgets as $widget): ?>
							<div class="col-md-6 mb-3">
								<div class="form-check">
									<input 
										class="form-check-input widget-checkbox" 
										type="checkbox" 
										value="<?php echo isset($widget['widget_key']) ? $widget['widget_key'] : ''; ?>" 
										id="widget_<?php echo isset($widget['id']) ? $widget['id'] : ''; ?>"
										<?php echo (isset($widgets_config['widgets']) && is_array($widgets_config['widgets']) && isset($widget['widget_key']) && in_array($widget['widget_key'], $widgets_config['widgets'])) ? 'checked' : ''; ?>
									>
									<label class="form-check-label" for="widget_<?php echo isset($widget['id']) ? $widget['id'] : ''; ?>">
										<strong><?php echo isset($widget['widget_name']) ? $widget['widget_name'] : 'Widget'; ?></strong>
										<br><small class="text-muted"><?php echo isset($widget['description']) ? $widget['description'] : 'Sin descripción'; ?></small>
									</label>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php else: ?>
					<div class="alert alert-warning">
						No hay widgets disponibles. Activa más módulos para obtener más widgets.
					</div>
				<?php endif; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary" id="btn-save-widgets">
					<i class="fas fa-save me-2"></i>Guardar Configuración
				</button>
			</div>
		</div>
	</div>
</div>

<!-- CHART.JS INITIALIZATION -->
<script>
document.addEventListener('DOMContentLoaded', function() {
	
	// Configurar Widgets Modal
	const btnConfigureWidgets = document.getElementById('btn-configure-widgets');
	const modalConfigureWidgets = new bootstrap.Modal(document.getElementById('modalConfigureWidgets'));
	
	if (btnConfigureWidgets) {
		btnConfigureWidgets.addEventListener('click', function() {
			modalConfigureWidgets.show();
		});
	}
	
	// Guardar Configuración de Widgets
	const btnSaveWidgets = document.getElementById('btn-save-widgets');
	if (btnSaveWidgets) {
		btnSaveWidgets.addEventListener('click', function() {
			const selectedWidgets = [];
			document.querySelectorAll('.widget-checkbox:checked').forEach(function(checkbox) {
				selectedWidgets.push(checkbox.value);
			});
			
			console.log('Widgets seleccionados:', selectedWidgets);
			
			// Mostrar loading
			btnSaveWidgets.disabled = true;
			btnSaveWidgets.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
			
			// AJAX para guardar configuración
			fetch('<?php echo Uri::create('admin/save_widget_config'); ?>', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					widgets: selectedWidgets
				})
			})
			.then(response => {
				console.log('Response status:', response.status);
				return response.json();
			})
			.then(data => {
				console.log('Response data:', data);
				btnSaveWidgets.disabled = false;
				btnSaveWidgets.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Configuración';
				
				if (data.success) {
					if (typeof Swal !== 'undefined') {
						Swal.fire({
							icon: 'success',
							title: 'Guardado',
							text: 'Configuración actualizada. Recargando dashboard...',
							timer: 1500,
							showConfirmButton: false
						}).then(() => {
							window.location.reload();
						});
					} else {
						alert('Configuración guardada correctamente');
						window.location.reload();
					}
				} else {
					if (typeof Swal !== 'undefined') {
						Swal.fire('Error', data.message || 'No se pudo guardar la configuración', 'error');
					} else {
						alert('Error: ' + (data.message || 'No se pudo guardar la configuración'));
					}
				}
			})
			.catch(error => {
				console.error('Error al guardar:', error);
				btnSaveWidgets.disabled = false;
				btnSaveWidgets.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Configuración';
				
				if (typeof Swal !== 'undefined') {
					Swal.fire('Error', 'Error al guardar la configuración: ' + error.message, 'error');
				} else {
					alert('Error al guardar la configuración: ' + error.message);
				}
			});
		});
	}
	
	<?php if (isset($widgets_data['sales_chart_week'])): ?>
	// GRÁFICA: Ventas Semanales (Line Chart)
	const salesChartWeekCtx = document.getElementById('salesChartWeek');
	if (salesChartWeekCtx) {
		const salesData = <?php echo json_encode($widgets_data['sales_chart_week']); ?>;
		new Chart(salesChartWeekCtx, {
			type: 'line',
			data: {
				labels: salesData.labels,
				datasets: [{
					label: 'Ventas',
					data: salesData.data,
					borderColor: '#5e72e4',
					backgroundColor: 'rgba(94, 114, 228, 0.1)',
					tension: 0.4,
					fill: true,
					pointBackgroundColor: '#5e72e4',
					pointBorderColor: '#fff',
					pointBorderWidth: 2,
					pointRadius: 4,
					pointHoverRadius: 6
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: true,
				plugins: {
					legend: {
						display: false
					},
					tooltip: {
						callbacks: {
							label: function(context) {
								return '$' + context.parsed.y.toFixed(2);
							}
						}
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							callback: function(value) {
								return '$' + value.toLocaleString();
							}
						}
					}
				}
			}
		});
	}
	<?php endif; ?>
	
	<?php if (isset($widgets_data['top_products'])): ?>
	// GRÁFICA: Top Productos (Donut Chart)
	const topProductsChartCtx = document.getElementById('topProductsChart');
	if (topProductsChartCtx) {
		const topProductsData = <?php echo json_encode($widgets_data['top_products']); ?>;
		new Chart(topProductsChartCtx, {
			type: 'doughnut',
			data: {
				labels: topProductsData.labels,
				datasets: [{
					data: topProductsData.data,
					backgroundColor: [
						'#5e72e4', '#11cdef', '#2dce89', '#f5365c', '#fb6340',
						'#ffd600', '#8965e0', '#525f7f', '#f7fafc', '#32325d'
					],
					borderWidth: 2,
					borderColor: '#fff'
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: true,
				plugins: {
					legend: {
						position: 'bottom',
						labels: {
							padding: 15,
							boxWidth: 12,
							font: {
								size: 11
							}
						}
					},
					tooltip: {
						callbacks: {
							label: function(context) {
								return context.label + ': ' + context.parsed + ' unidades';
							}
						}
					}
				}
			}
		});
	}
	<?php endif; ?>
	
	<?php if (isset($widgets_data['cash_flow'])): ?>
	// GRÁFICA: Flujo de Efectivo (Bar Chart)
	const cashFlowChartCtx = document.getElementById('cashFlowChart');
	if (cashFlowChartCtx) {
		const cashFlowData = <?php echo json_encode($widgets_data['cash_flow']); ?>;
		new Chart(cashFlowChartCtx, {
			type: 'bar',
			data: {
				labels: cashFlowData.labels,
				datasets: [
					{
						label: 'Ingresos',
						data: cashFlowData.income,
						backgroundColor: 'rgba(45, 206, 137, 0.8)',
						borderColor: '#2dce89',
						borderWidth: 1
					},
					{
						label: 'Egresos',
						data: cashFlowData.expenses,
						backgroundColor: 'rgba(245, 54, 92, 0.8)',
						borderColor: '#f5365c',
						borderWidth: 1
					}
				]
			},
			options: {
				responsive: true,
				maintainAspectRatio: true,
				plugins: {
					legend: {
						position: 'top',
					},
					tooltip: {
						callbacks: {
							label: function(context) {
								return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
							}
						}
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							callback: function(value) {
								return '$' + value.toLocaleString();
							}
						}
					}
				}
			}
		});
	}
	<?php endif; ?>
	
	// Auto-refresh del dashboard cada 5 minutos (300000 ms)
	<?php if (isset($widgets_config['refresh_interval']) && $widgets_config['refresh_interval'] > 0): ?>
	const refreshInterval = <?php echo intval($widgets_config['refresh_interval']); ?> * 1000;
	let refreshTimer = setInterval(function() {
		console.log('Auto-refreshing dashboard...');
		window.location.reload();
	}, refreshInterval);
	
	// Actualizar "Última actualización"
	let lastUpdateEl = document.getElementById('last-update');
	let secondsSinceUpdate = 0;
	
	setInterval(function() {
		secondsSinceUpdate++;
		if (secondsSinceUpdate < 60) {
			lastUpdateEl.textContent = 'Actualizado hace ' + secondsSinceUpdate + ' segundos';
		} else if (secondsSinceUpdate < 3600) {
			let minutes = Math.floor(secondsSinceUpdate / 60);
			lastUpdateEl.textContent = 'Actualizado hace ' + minutes + ' minuto' + (minutes > 1 ? 's' : '');
		} else {
			let hours = Math.floor(secondsSinceUpdate / 3600);
			lastUpdateEl.textContent = 'Actualizado hace ' + hours + ' hora' + (hours > 1 ? 's' : '');
		}
	}, 1000);
	<?php endif; ?>
	
});
</script>

<style>
.activity-timeline {
	max-height: 350px;
	overflow-y: auto;
	padding-right: 10px;
}
.activity-timeline::-webkit-scrollbar {
	width: 6px;
}
.activity-timeline::-webkit-scrollbar-track {
	background: #f1f1f1;
	border-radius: 10px;
}
.activity-timeline::-webkit-scrollbar-thumb {
	background: #888;
	border-radius: 10px;
}
.activity-timeline::-webkit-scrollbar-thumb:hover {
	background: #555;
}
.activity-item {
	border-left: 2px solid #e9ecef;
	padding-left: 0;
	position: relative;
}
.activity-item:last-child {
	border-left: none;
}
.activity-icon {
	position: relative;
	margin-left: -1px;
}
.card-header .btn-refresh {
	padding: 0.25rem 0.5rem;
	font-size: 0.875rem;
	opacity: 0.7;
	transition: opacity 0.2s;
}
.card-header .btn-refresh:hover {
	opacity: 1;
}

/* Skeleton Loaders */
.skeleton {
	animation: skeleton-loading 1s linear infinite alternate;
}
@keyframes skeleton-loading {
	0% {
		background-color: hsl(200, 20%, 80%);
	}
	100% {
		background-color: hsl(200, 20%, 95%);
	}
}
.skeleton-text {
	width: 100%;
	height: 0.7rem;
	margin-bottom: 0.5rem;
	border-radius: 0.25rem;
}
.skeleton-text:last-child {
	margin-bottom: 0;
	width: 80%;
}
.skeleton-chart {
	width: 100%;
	height: 200px;
	border-radius: 0.25rem;
}

/* Smooth transitions */
.card {
	transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card:hover {
	transform: translateY(-2px);
	box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* Badge animations */
.badge {
	animation: fadeIn 0.3s ease-in;
}
@keyframes fadeIn {
	from {
		opacity: 0;
		transform: scale(0.8);
	}
	to {
		opacity: 1;
		transform: scale(1);
	}
}

/* Chart.js responsive improvements */
canvas {
	max-height: 350px;
}
</style>