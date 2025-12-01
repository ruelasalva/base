<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-plus"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Información del Usuario</h3>
			</div>
			<div class="panel-body">
				<form method="post" action="<?php echo Uri::base(); ?>admin/users/crear">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="nombre">Nombre</label>
								<input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre completo" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="username">Usuario</label>
								<input type="text" class="form-control" id="username" name="username" placeholder="Nombre de usuario" required>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="email">Email</label>
						<input type="email" class="form-control" id="email" name="email" placeholder="email@ejemplo.com" required>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="password">Contraseña</label>
								<input type="password" class="form-control" id="password" name="password" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="password_confirm">Confirmar Contraseña</label>
								<input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="rol">Rol</label>
						<select class="form-control" id="rol" name="rol_id">
							<option value="">Seleccionar rol...</option>
							<option value="1">Administrador</option>
							<option value="2">Supervisor</option>
							<option value="3">Usuario</option>
						</select>
					</div>
					<div class="form-group">
						<label>Estado</label>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="activo" value="1" checked> Usuario activo
							</label>
						</div>
					</div>
					<hr>
					<div class="form-group">
						<button type="submit" class="btn btn-primary">
							<span class="glyphicon glyphicon-ok"></span> Crear Usuario
						</button>
						<a href="<?php echo Uri::base(); ?>admin/users" class="btn btn-default">
							<span class="glyphicon glyphicon-remove"></span> Cancelar
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
