<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-stats"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-3">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Tipos de Reporte</h3>
			</div>
			<div class="list-group">
				<?php foreach ($report_types as $key => $label): ?>
				<a href="<?php echo Uri::base(); ?>admin/reports/<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>" class="list-group-item">
					<?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
				</a>
				<?php endforeach; ?>
			</div>
		</div>
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Exportar</h3>
			</div>
			<div class="panel-body">
				<button class="btn btn-default btn-block">
					<span class="glyphicon glyphicon-download-alt"></span> Exportar Excel
				</button>
				<button class="btn btn-default btn-block">
					<span class="glyphicon glyphicon-file"></span> Exportar PDF
				</button>
			</div>
		</div>
	</div>
	
	<div class="col-md-9">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Filtros</h3>
			</div>
			<div class="panel-body">
				<form method="get" class="form-inline">
					<div class="form-group">
						<label for="fecha_inicio">Desde:</label>
						<input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
					</div>
					<div class="form-group">
						<label for="fecha_fin">Hasta:</label>
						<input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
					</div>
					<button type="submit" class="btn btn-primary">
						<span class="glyphicon glyphicon-search"></span> Generar
					</button>
				</form>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-3">
				<div class="panel panel-success">
					<div class="panel-heading">
						<h3 class="panel-title">Ventas Totales</h3>
					</div>
					<div class="panel-body text-center">
						<h2 class="text-success">$0.00</h2>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title">Pedidos</h3>
					</div>
					<div class="panel-body text-center">
						<h2 class="text-info">0</h2>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="panel panel-warning">
					<div class="panel-heading">
						<h3 class="panel-title">Usuarios</h3>
					</div>
					<div class="panel-body text-center">
						<h2 class="text-warning">0</h2>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="panel panel-danger">
					<div class="panel-heading">
						<h3 class="panel-title">Productos</h3>
					</div>
					<div class="panel-body text-center">
						<h2 class="text-danger">0</h2>
					</div>
				</div>
			</div>
		</div>
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Gráfico de Ventas</h3>
			</div>
			<div class="panel-body text-center" style="height: 300px;">
				<span class="glyphicon glyphicon-signal" style="font-size: 100px; color: #ddd; margin-top: 70px;"></span>
				<p class="text-muted">Selecciona un rango de fechas para ver el gráfico</p>
			</div>
		</div>
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Datos del Reporte</h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Fecha</th>
							<th>Descripción</th>
							<th>Cantidad</th>
							<th>Monto</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="4" class="text-center text-muted">
								<em>No hay datos para mostrar.</em>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
