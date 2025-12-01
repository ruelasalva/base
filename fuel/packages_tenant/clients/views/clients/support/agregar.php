<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-plus"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h3 class="panel-title">Crear Ticket de Soporte</h3>
			</div>
			<div class="panel-body">
				<form method="post" action="<?php echo Uri::base(); ?>clients/support/crear">
					<div class="form-group">
						<label for="asunto">Asunto</label>
						<input type="text" class="form-control" id="asunto" name="asunto" placeholder="Describe brevemente tu problema" required>
					</div>
					<div class="form-group">
						<label for="categoria">Categoría</label>
						<select class="form-control" id="categoria" name="categoria">
							<option value="general">Consulta General</option>
							<option value="pedido">Problema con Pedido</option>
							<option value="producto">Problema con Producto</option>
							<option value="facturacion">Facturación</option>
							<option value="otro">Otro</option>
						</select>
					</div>
					<div class="form-group">
						<label for="descripcion">Descripción</label>
						<textarea class="form-control" id="descripcion" name="descripcion" rows="6" placeholder="Describe tu problema en detalle..." required></textarea>
					</div>
					<hr>
					<div class="form-group">
						<button type="submit" class="btn btn-warning">
							<span class="glyphicon glyphicon-send"></span> Enviar Ticket
						</button>
						<a href="<?php echo Uri::base(); ?>clients/support" class="btn btn-default">
							<span class="glyphicon glyphicon-remove"></span> Cancelar
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
