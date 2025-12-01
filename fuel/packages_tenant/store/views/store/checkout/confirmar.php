<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1><span class="glyphicon glyphicon-ok-circle"></span> <?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
			<hr>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Proceso de Compra</h3>
				</div>
				<div class="list-group">
					<a href="#" class="list-group-item">
						<span class="badge">✓</span> Revisar Carrito
					</a>
					<a href="#" class="list-group-item">
						<span class="badge">✓</span> Información de Envío
					</a>
					<a href="#" class="list-group-item">
						<span class="badge">✓</span> Método de Pago
					</a>
					<a href="#" class="list-group-item active">
						<span class="badge">4</span> Confirmar Pedido
					</a>
				</div>
			</div>
		</div>
		
		<div class="col-md-9">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Resumen de tu Pedido</h3>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-6">
							<h4>Productos</h4>
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Producto</th>
										<th>Cantidad</th>
										<th>Precio</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td colspan="3" class="text-center text-muted">
											<em>No hay productos en el carrito.</em>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="col-md-6">
							<h4>Dirección de Envío</h4>
							<address>
								<strong>No disponible</strong><br>
							</address>
							<h4>Método de Pago</h4>
							<p><span class="glyphicon glyphicon-credit-card"></span> No seleccionado</p>
						</div>
					</div>
					
					<hr>
					<div class="row">
						<div class="col-md-6 col-md-offset-6">
							<table class="table">
								<tr>
									<td><strong>Subtotal:</strong></td>
									<td class="text-right">$0.00</td>
								</tr>
								<tr>
									<td><strong>Envío:</strong></td>
									<td class="text-right">$0.00</td>
								</tr>
								<tr>
									<td><strong>Impuestos:</strong></td>
									<td class="text-right">$0.00</td>
								</tr>
								<tr class="active">
									<td><h4><strong>Total:</strong></h4></td>
									<td class="text-right"><h4>$0.00</h4></td>
								</tr>
							</table>
						</div>
					</div>
					
					<hr>
					<div class="form-group">
						<a href="<?php echo Uri::base(); ?>tienda/checkout/pago" class="btn btn-default">
							<span class="glyphicon glyphicon-chevron-left"></span> Volver
						</a>
						<form method="post" action="<?php echo Uri::base(); ?>tienda/checkout/procesar" style="display: inline;">
							<button type="submit" class="btn btn-success btn-lg pull-right">
								<span class="glyphicon glyphicon-ok"></span> Confirmar y Pagar
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
