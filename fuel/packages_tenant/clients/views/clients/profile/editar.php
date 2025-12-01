<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-edit"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h3 class="panel-title">Actualizar Información</h3>
			</div>
			<div class="panel-body">
				<form method="post" action="<?php echo Uri::base(); ?>clients/profile/actualizar">
					<div class="form-group">
						<label for="nombre">Nombre Completo</label>
						<input type="text" class="form-control" id="nombre" name="nombre" placeholder="Tu nombre completo">
					</div>
					<div class="form-group">
						<label for="email">Email</label>
						<input type="email" class="form-control" id="email" name="email" placeholder="tu@email.com">
					</div>
					<div class="form-group">
						<label for="telefono">Teléfono</label>
						<input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Tu número de teléfono">
					</div>
					<div class="form-group">
						<label for="direccion">Dirección</label>
						<textarea class="form-control" id="direccion" name="direccion" rows="3" placeholder="Tu dirección"></textarea>
					</div>
					<hr>
					<div class="form-group">
						<button type="submit" class="btn btn-warning">
							<span class="glyphicon glyphicon-ok"></span> Guardar Cambios
						</button>
						<a href="<?php echo Uri::base(); ?>clients/profile" class="btn btn-default">
							<span class="glyphicon glyphicon-remove"></span> Cancelar
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
