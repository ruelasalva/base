<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1><span class="glyphicon glyphicon-credit-card"></span> <?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
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
					<a href="<?php echo Uri::base(); ?>tienda/checkout" class="list-group-item">
						<span class="badge">✓</span> Revisar Carrito
					</a>
					<a href="<?php echo Uri::base(); ?>tienda/checkout/envio" class="list-group-item">
						<span class="badge">✓</span> Información de Envío
					</a>
					<a href="#" class="list-group-item active">
						<span class="badge">3</span> Método de Pago
					</a>
					<a href="#" class="list-group-item disabled">
						<span class="badge">4</span> Confirmar Pedido
					</a>
				</div>
			</div>
		</div>
		
		<div class="col-md-9">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Seleccionar Método de Pago</h3>
				</div>
				<div class="panel-body">
					<form method="post" action="<?php echo Uri::base(); ?>tienda/checkout/confirmar">
						<div class="form-group">
							<div class="radio">
								<label>
									<input type="radio" name="metodo_pago" value="tarjeta" checked>
									<span class="glyphicon glyphicon-credit-card"></span> Tarjeta de Crédito/Débito
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="metodo_pago" value="paypal">
									<span class="glyphicon glyphicon-globe"></span> PayPal
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="metodo_pago" value="transferencia">
									<span class="glyphicon glyphicon-transfer"></span> Transferencia Bancaria
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="metodo_pago" value="efectivo">
									<span class="glyphicon glyphicon-usd"></span> Pago en Efectivo (al entregar)
								</label>
							</div>
						</div>
						
						<div id="tarjeta-form" class="well">
							<h4>Información de la Tarjeta</h4>
							<div class="form-group">
								<label for="numero_tarjeta">Número de Tarjeta</label>
								<input type="text" class="form-control" id="numero_tarjeta" placeholder="XXXX XXXX XXXX XXXX">
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="vencimiento">Fecha de Vencimiento</label>
										<input type="text" class="form-control" id="vencimiento" placeholder="MM/AA">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="cvv">CVV</label>
										<input type="text" class="form-control" id="cvv" placeholder="123">
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="nombre_tarjeta">Nombre en la Tarjeta</label>
								<input type="text" class="form-control" id="nombre_tarjeta" placeholder="Como aparece en la tarjeta">
							</div>
						</div>
						
						<hr>
						<div class="form-group">
							<a href="<?php echo Uri::base(); ?>tienda/checkout/envio" class="btn btn-default">
								<span class="glyphicon glyphicon-chevron-left"></span> Volver
							</a>
							<button type="submit" class="btn btn-success pull-right">
								Continuar <span class="glyphicon glyphicon-chevron-right"></span>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
