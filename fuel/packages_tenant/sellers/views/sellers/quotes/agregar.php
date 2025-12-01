<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-plus"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Detalle de la Cotización</h3>
			</div>
			<div class="panel-body">
				<form method="post" action="<?php echo Uri::base(); ?>sellers/quotes/crear">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="cliente">Cliente</label>
								<select class="form-control" id="cliente" name="cliente_id" required>
									<option value="">Seleccionar cliente...</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="vigencia">Vigencia (días)</label>
								<input type="number" class="form-control" id="vigencia" name="vigencia" value="30" min="1">
							</div>
						</div>
					</div>
					
					<h4>Productos</h4>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Producto</th>
								<th width="100">Cantidad</th>
								<th width="120">Precio</th>
								<th width="120">Subtotal</th>
								<th width="50"></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<select class="form-control" name="productos[]">
										<option value="">Seleccionar producto...</option>
									</select>
								</td>
								<td><input type="number" class="form-control" name="cantidades[]" value="1" min="1"></td>
								<td><input type="text" class="form-control" name="precios[]" value="0.00"></td>
								<td><input type="text" class="form-control" value="0.00" readonly></td>
								<td><button type="button" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></button></td>
							</tr>
						</tbody>
					</table>
					
					<button type="button" class="btn btn-default btn-sm">
						<span class="glyphicon glyphicon-plus"></span> Agregar Producto
					</button>
					
					<hr>
					<div class="form-group">
						<label for="terminos">Términos y Condiciones</label>
						<textarea class="form-control" id="terminos" name="terminos" rows="3" placeholder="Términos de la cotización..."></textarea>
					</div>
					<hr>
					<div class="form-group">
						<button type="submit" class="btn btn-info">
							<span class="glyphicon glyphicon-ok"></span> Guardar Cotización
						</button>
						<a href="<?php echo Uri::base(); ?>sellers/quotes" class="btn btn-default">
							<span class="glyphicon glyphicon-remove"></span> Cancelar
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Resumen</h3>
			</div>
			<div class="panel-body">
				<p><strong>Subtotal:</strong> <span id="subtotal">$0.00</span></p>
				<p><strong>Impuestos:</strong> <span id="impuestos">$0.00</span></p>
				<hr>
				<h4><strong>Total:</strong> <span id="total">$0.00</span></h4>
			</div>
		</div>
	</div>
</div>
