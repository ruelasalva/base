<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-shopping-cart"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title">Órdenes Pendientes</h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th>Orden #</th>
							<th>Cliente</th>
							<th>Productos</th>
							<th>Total</th>
							<th>Estado</th>
							<th>Fecha</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($orders)): ?>
						<tr>
							<td colspan="7" class="text-center text-muted">
								<span class="glyphicon glyphicon-ok-circle" style="font-size: 32px; color: #5cb85c;"></span>
								<p><em>No hay órdenes pendientes en este momento.</em></p>
							</td>
						</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
