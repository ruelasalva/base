<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-plus"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Información del Cliente</h3>
			</div>
			<div class="panel-body">
				<form method="post" action="<?php echo Uri::base(); ?>sellers/customers/crear">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="nombre">Nombre</label>
								<input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre completo" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="empresa">Empresa</label>
								<input type="text" class="form-control" id="empresa" name="empresa" placeholder="Nombre de la empresa">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="email">Email</label>
								<input type="email" class="form-control" id="email" name="email" placeholder="email@ejemplo.com" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="telefono">Teléfono</label>
								<input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Número de teléfono">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="direccion">Dirección</label>
						<textarea class="form-control" id="direccion" name="direccion" rows="2" placeholder="Dirección completa"></textarea>
					</div>
					<div class="form-group">
						<label for="notas">Notas</label>
						<textarea class="form-control" id="notas" name="notas" rows="2" placeholder="Notas adicionales sobre el cliente..."></textarea>
					</div>
					<hr>
					<div class="form-group">
						<button type="submit" class="btn btn-info">
							<span class="glyphicon glyphicon-ok"></span> Guardar Cliente
						</button>
						<a href="<?php echo Uri::base(); ?>sellers/customers" class="btn btn-default">
							<span class="glyphicon glyphicon-remove"></span> Cancelar
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
