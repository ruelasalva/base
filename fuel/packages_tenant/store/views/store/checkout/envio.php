<div class="container">
	<div class="row">
		<div class="col-md-12">
			<h1><span class="glyphicon glyphicon-map-marker"></span> <?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
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
					<a href="#" class="list-group-item active">
						<span class="badge">2</span> Información de Envío
					</a>
					<a href="#" class="list-group-item disabled">
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
					<h3 class="panel-title">Dirección de Envío</h3>
				</div>
				<div class="panel-body">
					<form method="post" action="<?php echo Uri::base(); ?>tienda/checkout/pago">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="nombre">Nombre Completo</label>
									<input type="text" class="form-control" id="nombre" name="nombre" required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="telefono">Teléfono</label>
									<input type="tel" class="form-control" id="telefono" name="telefono" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="direccion">Dirección</label>
							<input type="text" class="form-control" id="direccion" name="direccion" placeholder="Calle y número" required>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="ciudad">Ciudad</label>
									<input type="text" class="form-control" id="ciudad" name="ciudad" required>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="estado">Estado</label>
									<input type="text" class="form-control" id="estado" name="estado" required>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="cp">Código Postal</label>
									<input type="text" class="form-control" id="cp" name="codigo_postal" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="notas">Notas de Envío (opcional)</label>
							<textarea class="form-control" id="notas" name="notas" rows="2" placeholder="Instrucciones especiales..."></textarea>
						</div>
						<hr>
						<div class="form-group">
							<a href="<?php echo Uri::base(); ?>tienda/checkout" class="btn btn-default">
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
