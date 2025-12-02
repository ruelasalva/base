<div class="row">
	<div class="col-md-12">
		<h2><span class="glyphicon glyphicon-cog"></span> <?php echo htmlspecialchars($module_name, ENT_QUOTES, 'UTF-8'); ?></h2>
		<hr>
	</div>
</div>

<div class="row">
	<div class="col-md-3">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Categorías</h3>
			</div>
			<div class="list-group">
				<?php foreach ($settings_groups as $key => $label): ?>
				<a href="#<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>" class="list-group-item">
					<?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
				</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	
	<div class="col-md-9">
		<div class="panel panel-default" id="general">
			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-wrench"></span> Configuración General</h3>
			</div>
			<div class="panel-body">
				<form method="post" action="<?php echo Uri::base(); ?>admin/settings/guardar">
					<input type="hidden" name="grupo" value="general">
					<div class="form-group">
						<label for="site_name">Nombre del Sitio</label>
						<input type="text" class="form-control" id="site_name" name="site_name" placeholder="Mi ERP">
					</div>
					<div class="form-group">
						<label for="site_email">Email del Sistema</label>
						<input type="email" class="form-control" id="site_email" name="site_email" placeholder="admin@example.com">
					</div>
					<div class="form-group">
						<label for="timezone">Zona Horaria</label>
						<select class="form-control" id="timezone" name="timezone">
							<option value="America/Mexico_City">America/Mexico_City</option>
							<option value="America/New_York">America/New_York</option>
							<option value="America/Los_Angeles">America/Los_Angeles</option>
							<option value="Europe/Madrid">Europe/Madrid</option>
						</select>
					</div>
					<button type="submit" class="btn btn-primary">
						<span class="glyphicon glyphicon-ok"></span> Guardar Cambios
					</button>
				</form>
			</div>
		</div>
		
		<div class="panel panel-default" id="email">
			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-envelope"></span> Configuración de Email</h3>
			</div>
			<div class="panel-body">
				<form method="post" action="<?php echo Uri::base(); ?>admin/settings/guardar">
					<input type="hidden" name="grupo" value="email">
					<div class="form-group">
						<label for="smtp_host">Servidor SMTP</label>
						<input type="text" class="form-control" id="smtp_host" name="smtp_host" placeholder="smtp.example.com">
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="smtp_user">Usuario SMTP</label>
								<input type="text" class="form-control" id="smtp_user" name="smtp_user">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="smtp_port">Puerto</label>
								<input type="number" class="form-control" id="smtp_port" name="smtp_port" value="587">
							</div>
						</div>
					</div>
					<button type="submit" class="btn btn-primary">
						<span class="glyphicon glyphicon-ok"></span> Guardar Cambios
					</button>
				</form>
			</div>
		</div>
		
		<div class="panel panel-default" id="security">
			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-lock"></span> Seguridad</h3>
			</div>
			<div class="panel-body">
				<form method="post" action="<?php echo Uri::base(); ?>admin/settings/guardar">
					<input type="hidden" name="grupo" value="security">
					<div class="form-group">
						<label>Autenticación de dos factores</label>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="two_factor_enabled" value="1"> Habilitar 2FA
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="session_timeout">Tiempo de sesión (minutos)</label>
						<input type="number" class="form-control" id="session_timeout" name="session_timeout" value="60" min="5">
					</div>
					<button type="submit" class="btn btn-primary">
						<span class="glyphicon glyphicon-ok"></span> Guardar Cambios
					</button>
				</form>
			</div>
		</div>
		
		<div class="panel panel-default" id="integrations">
			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-transfer"></span> Integraciones</h3>
			</div>
			<div class="panel-body">
				<div class="alert alert-info">
					<span class="glyphicon glyphicon-info-sign"></span>
					Las integraciones con servicios externos se configuran aquí.
				</div>
				<p class="text-muted">No hay integraciones configuradas.</p>
			</div>
		</div>
	</div>
</div>
