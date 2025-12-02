<div class="text-center">
	<div class="mb-4">
		<span class="glyphicon glyphicon-ok-circle" style="font-size: 80px; color: #28a745;"></span>
	</div>

	<h2>¡Instalación Completada!</h2>
	<p class="text-muted mb-4">El sistema ERP Multi-tenant ha sido instalado correctamente.</p>

	<div class="panel panel-success">
		<div class="panel-heading">
			<h4 class="panel-title">
				<span class="glyphicon glyphicon-info-sign"></span>
				Información Importante
			</h4>
		</div>
		<div class="panel-body text-left">
			<ul class="list-unstyled">
				<li class="mb-2">
					<span class="glyphicon glyphicon-ok text-success"></span>
					La base de datos ha sido configurada correctamente
				</li>
				<li class="mb-2">
					<span class="glyphicon glyphicon-ok text-success"></span>
					Todas las migraciones han sido ejecutadas
				</li>
				<li class="mb-2">
					<span class="glyphicon glyphicon-ok text-success"></span>
					El usuario administrador ha sido creado
				</li>
				<li class="mb-2">
					<span class="glyphicon glyphicon-warning-sign text-warning"></span>
					<strong>Recomendación de seguridad:</strong> Considere restringir el acceso al instalador en producción
				</li>
			</ul>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<span class="glyphicon glyphicon-question-sign"></span>
				Próximos Pasos
			</h4>
		</div>
		<div class="panel-body text-left">
			<ol>
				<li class="mb-2">
					<strong>Inicie sesión</strong> con las credenciales del administrador que acaba de crear
				</li>
				<li class="mb-2">
					<strong>Configure los módulos</strong> del sistema según sus necesidades
				</li>
				<li class="mb-2">
					<strong>Configure los tenants</strong> si va a usar el sistema en modo multi-tenant
				</li>
				<li class="mb-2">
					<strong>Revise la documentación</strong> para conocer todas las funcionalidades disponibles
				</li>
			</ol>
		</div>
	</div>

	<div class="panel panel-info">
		<div class="panel-heading">
			<h4 class="panel-title">
				<span class="glyphicon glyphicon-plus"></span>
				Añadir Nuevas Migraciones
			</h4>
		</div>
		<div class="panel-body text-left">
			<p>Para añadir nuevas tablas o modificaciones a la base de datos en el futuro:</p>
			<ol>
				<li class="mb-2">
					Cree un archivo SQL en <code>fuel/app/migrations/</code> con el formato:
					<br>
					<code>NNN_nombre_descriptivo.sql</code>
					<br>
					<small class="text-muted">Ejemplo: 002_productos.sql, 003_categorias.sql</small>
				</li>
				<li class="mb-2">
					Visite el instalador en <code>/install</code> para ejecutar las nuevas migraciones
				</li>
			</ol>
		</div>
	</div>

	<hr>

	<div class="row mt-4">
		<div class="col-md-4">
			<a href="<?php echo Uri::base(); ?>" class="btn btn-installer btn-lg btn-block">
				<span class="glyphicon glyphicon-home"></span>
				Ir al Inicio
			</a>
		</div>
		<div class="col-md-4">
			<a href="<?php echo Uri::create('auth/login'); ?>" class="btn btn-installer btn-lg btn-block">
				<span class="glyphicon glyphicon-log-in"></span>
				Iniciar Sesión
			</a>
		</div>
		<div class="col-md-4">
			<a href="<?php echo Uri::create('admin'); ?>" class="btn btn-installer btn-lg btn-block">
				<span class="glyphicon glyphicon-cog"></span>
				Panel Admin
			</a>
		</div>
	</div>

	<div class="mt-4">
		<a href="<?php echo Uri::create('install'); ?>" class="btn btn-installer-outline">
			<span class="glyphicon glyphicon-repeat"></span>
			Volver al Instalador
		</a>
	</div>
</div>

<style>
	.mb-2 { margin-bottom: 10px; }
	.mb-4 { margin-bottom: 25px; }
	.mt-4 { margin-top: 30px; }
	.text-success { color: #28a745; }
	.text-warning { color: #ffc107; }
	.list-unstyled {
		list-style: none;
		padding-left: 0;
	}
</style>
