<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-plus"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default" style="border-color: #9b59b6;">
			<div class="panel-heading" style="background: #9b59b6; color: white;">
				<h3 class="panel-title">Información del Contrato</h3>
			</div>
			<div class="panel-body">
				<form method="post" action="<?php echo Uri::base(); ?>partners/contracts/crear">
					<div class="form-group">
						<label for="alianza">Alianza</label>
						<select class="form-control" id="alianza" name="alianza_id">
							<option value="">Seleccionar alianza...</option>
						</select>
					</div>
					<div class="form-group">
						<label for="tipo">Tipo de Contrato</label>
						<select class="form-control" id="tipo" name="tipo">
							<option value="exclusivo">Exclusivo</option>
							<option value="no_exclusivo">No Exclusivo</option>
							<option value="temporal">Temporal</option>
						</select>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="fecha_inicio">Fecha de Inicio</label>
								<input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="fecha_fin">Fecha de Fin</label>
								<input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="terminos">Términos y Condiciones</label>
						<textarea class="form-control" id="terminos" name="terminos" rows="5" placeholder="Describir los términos del contrato..."></textarea>
					</div>
					<hr>
					<div class="form-group">
						<button type="submit" class="btn btn-default" style="background: #9b59b6; color: white;">
							<span class="glyphicon glyphicon-ok"></span> Crear Contrato
						</button>
						<a href="<?php echo Uri::base(); ?>partners/contracts" class="btn btn-default">
							<span class="glyphicon glyphicon-remove"></span> Cancelar
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
