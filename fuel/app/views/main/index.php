<div class="row">
	<div class="col-md-12">
		<div class="jumbotron">
			<h2>¡Bienvenido a la Aplicación Base!</h2>
			<p>Esta es la vista estándar de la aplicación. Utiliza la estructura MVC con un controlador base y un template reutilizable.</p>
			<p><a class="btn btn-primary btn-lg" href="<?php echo Uri::base(); ?>main/agregar" role="button">Agregar Nuevo</a></p>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<span class="glyphicon glyphicon-list"></span> Listado de Registros
					<a href="<?php echo Uri::base(); ?>main/agregar" class="btn btn-success btn-sm pull-right">
						<span class="glyphicon glyphicon-plus"></span> Agregar
					</a>
				</h3>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>ID</th>
								<th>Nombre</th>
								<th>Descripción</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="4" class="text-center text-muted">
									<em>No hay registros para mostrar. Usa este controlador como ejemplo para tu módulo.</em>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-4">
		<h3><span class="glyphicon glyphicon-folder-open"></span> Estructura MVC</h3>
		<p>Esta aplicación utiliza el patrón Modelo-Vista-Controlador:</p>
		<ul>
			<li><strong>Modelo:</strong> <code>classes/model/</code></li>
			<li><strong>Vista:</strong> <code>views/</code></li>
			<li><strong>Controlador:</strong> <code>classes/controller/</code></li>
		</ul>
	</div>
	<div class="col-md-4">
		<h3><span class="glyphicon glyphicon-cog"></span> Acciones Estándar</h3>
		<p>Cada controlador tiene las siguientes acciones:</p>
		<ul>
			<li><strong>index:</strong> Listado de registros</li>
			<li><strong>agregar:</strong> Insertar nuevo</li>
			<li><strong>info:</strong> Ver detalle</li>
			<li><strong>editar:</strong> Modificar</li>
			<li><strong>eliminar:</strong> Eliminación lógica</li>
		</ul>
	</div>
	<div class="col-md-4">
		<h3><span class="glyphicon glyphicon-file"></span> Template</h3>
		<p>El template principal se encuentra en:</p>
		<p><code>APPPATH/views/template.php</code></p>
		<p>Personalízalo según las necesidades del proyecto.</p>
	</div>
</div>
