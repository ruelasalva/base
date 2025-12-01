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
				<h3 class="panel-title">Información de la Alianza</h3>
			</div>
			<div class="panel-body">
				<form method="post" action="<?php echo Uri::base(); ?>partners/alliances/crear">
					<div class="form-group">
						<label for="empresa">Empresa</label>
						<input type="text" class="form-control" id="empresa" name="empresa" placeholder="Nombre de la empresa" required>
					</div>
					<div class="form-group">
						<label for="tipo">Tipo de Alianza</label>
						<select class="form-control" id="tipo" name="tipo">
							<option value="distribuidor">Distribuidor</option>
							<option value="revendedor">Revendedor</option>
							<option value="referidor">Referidor</option>
							<option value="estrategico">Estratégico</option>
						</select>
					</div>
					<div class="form-group">
						<label for="contacto">Nombre de Contacto</label>
						<input type="text" class="form-control" id="contacto" name="contacto" placeholder="Nombre del contacto principal">
					</div>
					<div class="form-group">
						<label for="email">Email de Contacto</label>
						<input type="email" class="form-control" id="email" name="email" placeholder="email@empresa.com">
					</div>
					<div class="form-group">
						<label for="notas">Notas</label>
						<textarea class="form-control" id="notas" name="notas" rows="3" placeholder="Notas adicionales..."></textarea>
					</div>
					<hr>
					<div class="form-group">
						<button type="submit" class="btn btn-default" style="background: #9b59b6; color: white;">
							<span class="glyphicon glyphicon-ok"></span> Crear Alianza
						</button>
						<a href="<?php echo Uri::base(); ?>partners/alliances" class="btn btn-default">
							<span class="glyphicon glyphicon-remove"></span> Cancelar
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
