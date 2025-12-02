<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-user"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-6"><h3 class="panel-title">Listado de Usuarios</h3></div>
					<div class="col-md-6 text-right">
						<a href="<?php echo Uri::base(); ?>admin/users/agregar" class="btn btn-success btn-sm">
							<span class="glyphicon glyphicon-plus"></span> Agregar Usuario
						</a>
					</div>
				</div>
			</div>
			<div class="panel-body">
				<table class="table table-striped">
					<thead>
						<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Acciones</th></tr>
					</thead>
					<tbody>
						<?php if (empty($users)): ?>
						<tr><td colspan="5" class="text-center text-muted"><em>No hay usuarios registrados.</em></td></tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
