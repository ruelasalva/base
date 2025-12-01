<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Información del Registro</h3>
			</div>
			<div class="panel-body">
				<dl class="dl-horizontal">
					<dt>ID</dt>
					<dd><?php echo isset($registro['id']) ? $registro['id'] : '-'; ?></dd>
					
					<dt>Nombre</dt>
					<dd><?php echo isset($registro['nombre']) ? $registro['nombre'] : '-'; ?></dd>
					
					<dt>Descripción</dt>
					<dd><?php echo isset($registro['descripcion']) ? $registro['descripcion'] : '-'; ?></dd>
					
					<dt>Fecha de Creación</dt>
					<dd><?php echo isset($registro['created_at']) ? $registro['created_at'] : '-'; ?></dd>
				</dl>
				<hr>
				<a href="<?php echo Uri::base(); ?>main/editar/<?php echo isset($registro['id']) ? $registro['id'] : ''; ?>" class="btn btn-warning">Editar</a>
				<a href="<?php echo Uri::base(); ?>main" class="btn btn-default">Volver</a>
			</div>
		</div>
	</div>
</div>
