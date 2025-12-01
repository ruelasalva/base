<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-file"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?> #<?php echo htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h3 class="panel-title">Productos del Pedido</h3>
			</div>
			<div class="panel-body">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Producto</th>
							<th>Cantidad</th>
							<th>Precio</th>
							<th>Subtotal</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="4" class="text-center text-muted">
								<em>Información del pedido no disponible.</em>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	
	<div class="col-md-4">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Resumen del Pedido</h3>
			</div>
			<div class="panel-body">
				<p><strong>Estado:</strong> <span class="label label-info">Pendiente</span></p>
				<p><strong>Fecha:</strong> --</p>
				<hr>
				<h4><strong>Total:</strong> $0.00</h4>
			</div>
			<div class="panel-footer">
				<a href="<?php echo Uri::base(); ?>clients/orders" class="btn btn-default btn-block">
					<span class="glyphicon glyphicon-arrow-left"></span> Volver a Mis Pedidos
				</a>
			</div>
		</div>
	</div>
</div>
