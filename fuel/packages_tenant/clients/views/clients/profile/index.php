<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-user"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-4">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h3 class="panel-title">Foto de Perfil</h3>
			</div>
			<div class="panel-body text-center">
				<span class="glyphicon glyphicon-user" style="font-size: 100px; color: #f0ad4e;"></span>
				<hr>
				<a href="<?php echo Uri::base(); ?>clients/profile/editar" class="btn btn-warning btn-block">
					<span class="glyphicon glyphicon-edit"></span> Editar Perfil
				</a>
			</div>
		</div>
	</div>
	
	<div class="col-md-8">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Información Personal</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6">
						<p><strong>Nombre:</strong></p>
						<p class="text-muted">No disponible</p>
					</div>
					<div class="col-md-6">
						<p><strong>Email:</strong></p>
						<p class="text-muted">No disponible</p>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-6">
						<p><strong>Teléfono:</strong></p>
						<p class="text-muted">No disponible</p>
					</div>
					<div class="col-md-6">
						<p><strong>Dirección:</strong></p>
						<p class="text-muted">No disponible</p>
					</div>
				</div>
			</div>
		</div>
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Configuración de la Cuenta</h3>
			</div>
			<div class="panel-body">
				<a href="<?php echo Uri::base(); ?>clients/profile/editar" class="btn btn-default">
					<span class="glyphicon glyphicon-lock"></span> Cambiar Contraseña
				</a>
			</div>
		</div>
	</div>
</div>
