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
					<a href="#" class="list-group-item <?php echo $step == 1 ? 'active' : ''; ?>">
						<span class="badge">1</span> Revisar Carrito
					</a>
					<a href="#" class="list-group-item <?php echo $step == 2 ? 'active' : ''; ?>">
						<span class="badge">2</span> Información de Envío
					</a>
					<a href="#" class="list-group-item <?php echo $step == 3 ? 'active' : ''; ?>">
						<span class="badge">3</span> Método de Pago
					</a>
					<a href="#" class="list-group-item <?php echo $step == 4 ? 'active' : ''; ?>">
						<span class="badge">4</span> Confirmar Pedido
					</a>
				</div>
			</div>
		</div>
		
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Resumen del Carrito</h3>
				</div>
				<div class="panel-body">
					<?php if (empty($cart_items)): ?>
					<div class="alert alert-warning text-center">
						<span class="glyphicon glyphicon-shopping-cart" style="font-size: 48px;"></span>
						<h4>Tu carrito está vacío</h4>
						<p>Agrega productos antes de continuar.</p>
						<a href="<?php echo Uri::base(); ?>tienda/catalogo" class="btn btn-primary">
							<span class="glyphicon glyphicon-search"></span> Ver Catálogo
						</a>
					</div>
					<?php else: ?>
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Producto</th>
								<th>Cantidad</th>
								<th>Precio</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					<?php endif; ?>
				</div>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Total</h3>
				</div>
				<div class="panel-body text-center">
					<h2>$<?php echo htmlspecialchars($total, ENT_QUOTES, 'UTF-8'); ?></h2>
				</div>
				<div class="panel-footer">
					<?php if (!empty($cart_items)): ?>
					<a href="<?php echo Uri::base(); ?>tienda/checkout/envio" class="btn btn-success btn-block">
						Continuar <span class="glyphicon glyphicon-chevron-right"></span>
					</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
