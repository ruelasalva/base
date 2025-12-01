<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Agregar Nuevo Registro</h3>
			</div>
			<div class="panel-body">
				<form method="post" action="">
					<div class="form-group">
						<label for="nombre">Nombre</label>
						<input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese el nombre">
					</div>
					<div class="form-group">
						<label for="descripcion">Descripción</label>
						<textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Ingrese la descripción"></textarea>
					</div>
					<button type="submit" class="btn btn-primary">Guardar</button>
					<a href="<?php echo Uri::base(); ?>main" class="btn btn-default">Cancelar</a>
				</form>
			</div>
		</div>
	</div>
</div>
