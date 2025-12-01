<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1><span class="glyphicon glyphicon-shopping-cart"></span> <?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
			<hr>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-8">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Productos en tu carrito</h3>
				</div>
				<div class="panel-body">
					<?php if (empty($items)): ?>
					<div class="alert alert-info text-center">
						<span class="glyphicon glyphicon-shopping-cart" style="font-size: 48px;"></span>
						<h4>Tu carrito está vacío</h4>
						<p>¡Agrega productos para comenzar tu compra!</p>
						<a href="<?php echo Uri::base(); ?>tienda/catalogo" class="btn btn-primary">
							<span class="glyphicon glyphicon-search"></span> Ver Catálogo
						</a>
					</div>
					<?php else: ?>
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Producto</th>
									<th>Precio</th>
									<th>Cantidad</th>
									<th>Subtotal</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($items as $item): ?>
								<tr>
									<td><?php echo htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
									<td>$<?php echo htmlspecialchars($item['price'] ?? '0.00', ENT_QUOTES, 'UTF-8'); ?></td>
									<td>
										<input type="number" class="form-control" value="<?php echo htmlspecialchars($item['quantity'] ?? 1, ENT_QUOTES, 'UTF-8'); ?>" min="1" style="width: 70px;">
									</td>
									<td>$<?php echo htmlspecialchars($item['subtotal'] ?? '0.00', ENT_QUOTES, 'UTF-8'); ?></td>
									<td>
										<a href="<?php echo Uri::base(); ?>tienda/carrito/eliminar/<?php echo htmlspecialchars($item['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-danger btn-xs">
											<span class="glyphicon glyphicon-trash"></span>
										</a>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">Resumen del Pedido</h3>
				</div>
				<div class="panel-body">
					<p><strong>Subtotal:</strong> $<?php echo htmlspecialchars($subtotal, ENT_QUOTES, 'UTF-8'); ?></p>
					<p><strong>Envío:</strong> Por calcular</p>
					<hr>
					<h4><strong>Total:</strong> $<?php echo htmlspecialchars($total, ENT_QUOTES, 'UTF-8'); ?></h4>
				</div>
				<div class="panel-footer">
					<?php if (!empty($items)): ?>
					<a href="<?php echo Uri::base(); ?>tienda/checkout" class="btn btn-success btn-block btn-lg">
						<span class="glyphicon glyphicon-credit-card"></span> Proceder al Pago
					</a>
					<?php endif; ?>
					<a href="<?php echo Uri::base(); ?>tienda/catalogo" class="btn btn-default btn-block">
						<span class="glyphicon glyphicon-arrow-left"></span> Seguir Comprando
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
