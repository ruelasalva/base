<div class="container-fluid">
	<div class="mb-4">
		<h2><i class="fas fa-chart-line"></i> Reportes Financieros</h2>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?php echo Uri::create('admin'); ?>">Inicio</a></li>
				<li class="breadcrumb-item active">Reportes Financieros</li>
			</ol>
		</nav>
	</div>

	<div class="row">
		<!-- Balance General -->
		<div class="col-md-4 mb-4">
			<div class="card h-100">
				<div class="card-body text-center">
					<div class="mb-3">
						<i class="fas fa-balance-scale fa-4x text-primary"></i>
					</div>
					<h4>Balance General</h4>
					<p class="text-muted">Estado de situación financiera que muestra activos, pasivos y capital en un momento específico.</p>
					<a href="<?php echo Uri::create('admin/reportesfinancieros/balance'); ?>" class="btn btn-primary">
						<i class="fas fa-file-alt"></i> Ver Balance
					</a>
				</div>
			</div>
		</div>

		<!-- Estado de Resultados -->
		<div class="col-md-4 mb-4">
			<div class="card h-100">
				<div class="card-body text-center">
					<div class="mb-3">
						<i class="fas fa-chart-bar fa-4x text-success"></i>
					</div>
					<h4>Estado de Resultados</h4>
					<p class="text-muted">Muestra los ingresos, gastos y utilidad neta de un periodo determinado.</p>
					<a href="<?php echo Uri::create('admin/reportesfinancieros/resultados'); ?>" class="btn btn-success">
						<i class="fas fa-file-alt"></i> Ver Resultados
					</a>
				</div>
			</div>
		</div>

		<!-- Comparativo -->
		<div class="col-md-4 mb-4">
			<div class="card h-100">
				<div class="card-body text-center">
					<div class="mb-3">
						<i class="fas fa-chart-line fa-4x text-info"></i>
					</div>
					<h4>Comparativo Mensual</h4>
					<p class="text-muted">Análisis comparativo de ingresos, gastos y utilidades entre diferentes periodos.</p>
					<a href="<?php echo Uri::create('admin/reportesfinancieros/comparativo'); ?>" class="btn btn-info">
						<i class="fas fa-chart-area"></i> Ver Comparativo
					</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Información adicional -->
	<div class="card">
		<div class="card-header">
			<h5 class="mb-0"><i class="fas fa-info-circle"></i> Acerca de los Reportes Financieros</h5>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<h6>Balance General</h6>
					<p>También conocido como Estado de Situación Financiera, presenta:</p>
					<ul>
						<li><strong>Activos:</strong> Recursos que posee la empresa</li>
						<li><strong>Pasivos:</strong> Obligaciones y deudas</li>
						<li><strong>Capital:</strong> Patrimonio de los accionistas</li>
					</ul>
					<p class="mb-0 text-muted small">Ecuación: Activos = Pasivos + Capital</p>
				</div>
				<div class="col-md-6">
					<h6>Estado de Resultados</h6>
					<p>También llamado Estado de Pérdidas y Ganancias, muestra:</p>
					<ul>
						<li><strong>Ingresos:</strong> Ventas y otros ingresos operativos</li>
						<li><strong>Gastos:</strong> Costos y gastos operativos</li>
						<li><strong>Utilidad Neta:</strong> Resultado del periodo</li>
					</ul>
					<p class="mb-0 text-muted small">Fórmula: Utilidad = Ingresos - Gastos</p>
				</div>
			</div>
		</div>
	</div>
</div>
