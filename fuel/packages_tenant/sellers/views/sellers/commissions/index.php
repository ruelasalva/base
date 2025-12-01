<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-usd"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-4">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Este Mes</h3>
			</div>
			<div class="panel-body text-center">
				<h2 class="text-info">$<?php echo htmlspecialchars($summary['month'], ENT_QUOTES, 'UTF-8'); ?></h2>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Este Año</h3>
			</div>
			<div class="panel-body text-center">
				<h2 class="text-info">$<?php echo htmlspecialchars($summary['year'], ENT_QUOTES, 'UTF-8'); ?></h2>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h3 class="panel-title">Pendiente de Pago</h3>
			</div>
			<div class="panel-body text-center">
				<h2 class="text-warning">$<?php echo htmlspecialchars($summary['pending'], ENT_QUOTES, 'UTF-8'); ?></h2>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Historial de Comisiones</h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th>Fecha</th>
							<th>Venta #</th>
							<th>Cliente</th>
							<th>Monto Venta</th>
							<th>% Comisión</th>
							<th>Comisión</th>
							<th>Estado</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($commissions)): ?>
						<tr>
							<td colspan="7" class="text-center text-muted">
								<span class="glyphicon glyphicon-usd" style="font-size: 32px;"></span>
								<p><em>No hay comisiones registradas aún.</em></p>
								<p>Las comisiones se generan automáticamente al registrar ventas.</p>
							</td>
						</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
