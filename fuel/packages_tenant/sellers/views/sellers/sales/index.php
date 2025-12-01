<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-shopping-cart"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-6">
						<h3 class="panel-title">Registro de Ventas</h3>
					</div>
					<div class="col-md-6 text-right">
						<a href="<?php echo Uri::base(); ?>sellers/sales/agregar" class="btn btn-success btn-sm">
							<span class="glyphicon glyphicon-plus"></span> Nueva Venta
						</a>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th>Venta #</th>
							<th>Cliente</th>
							<th>Productos</th>
							<th>Total</th>
							<th>Estado</th>
							<th>Fecha</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($sales)): ?>
						<tr>
							<td colspan="7" class="text-center text-muted">
								<span class="glyphicon glyphicon-shopping-cart" style="font-size: 32px;"></span>
								<p><em>No tienes ventas registradas.</em></p>
								<a href="<?php echo Uri::base(); ?>sellers/sales/agregar" class="btn btn-info">
									<span class="glyphicon glyphicon-plus"></span> Registrar Primera Venta
								</a>
							</td>
						</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
