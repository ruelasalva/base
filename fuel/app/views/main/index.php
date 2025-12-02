<div class="row">
	<div class="col-md-12">
		<div class="jumbotron">
			<h2>¡Bienvenido al ERP Multi-tenant!</h2>
			<p>Sistema ERP completo con backends para administración, proveedores, partners, vendedores y clientes, además de tienda online y landing page.</p>
			<p>
				<a class="btn btn-primary btn-lg" href="<?php echo Uri::create('tienda'); ?>" role="button">
					<span class="glyphicon glyphicon-shopping-cart"></span> Ir a la Tienda
				</a>
				<a class="btn btn-default btn-lg" href="<?php echo Uri::create('landing'); ?>" role="button">
					<span class="glyphicon glyphicon-globe"></span> Ver Landing Page
				</a>
			</p>
		</div>
	</div>
</div>

<!-- Mensaje informativo sobre el estado del sistema -->
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-info">
			<span class="glyphicon glyphicon-info-sign"></span>
			<strong>Nota:</strong> Esta es la instalación base del ERP Multi-tenant. 
			Los módulos mostrados abajo son placeholders. Para activarlos, configure su sistema a través del 
			<a href="<?php echo Uri::create('install'); ?>">instalador</a>.
		</div>
	</div>
</div>

<!-- Módulos del ERP -->
<div class="row">
	<div class="col-md-12">
		<h3><span class="glyphicon glyphicon-th-large"></span> Módulos del ERP</h3>
		<hr>
	</div>
</div>

<div class="row">
	<!-- Backend Admin -->
	<div class="col-lg-2 col-md-4 col-sm-6">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-cog"></span> Admin</h3>
			</div>
			<div class="panel-body">
				<p>Panel de administración del sistema.</p>
				<ul class="list-unstyled small">
					<li><span class="glyphicon glyphicon-ok"></span> Usuarios</li>
					<li><span class="glyphicon glyphicon-ok"></span> Configuración</li>
					<li><span class="glyphicon glyphicon-ok"></span> Reportes</li>
				</ul>
			</div>
			<div class="panel-footer">
				<a href="<?php echo Uri::create('admin'); ?>" class="btn btn-primary btn-block">Acceder</a>
			</div>
		</div>
	</div>

	<!-- Backend Providers -->
	<div class="col-lg-2 col-md-4 col-sm-6">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-briefcase"></span> Providers</h3>
			</div>
			<div class="panel-body">
				<p>Portal para proveedores.</p>
				<ul class="list-unstyled small">
					<li><span class="glyphicon glyphicon-ok"></span> Productos</li>
					<li><span class="glyphicon glyphicon-ok"></span> Inventario</li>
					<li><span class="glyphicon glyphicon-ok"></span> Órdenes</li>
				</ul>
			</div>
			<div class="panel-footer">
				<a href="<?php echo Uri::create('providers'); ?>" class="btn btn-success btn-block">Acceder</a>
			</div>
		</div>
	</div>

	<!-- Backend Partners -->
	<div class="col-lg-2 col-md-4 col-sm-6">
		<div class="panel panel-default" style="border-color: #9b59b6;">
			<div class="panel-heading" style="background: #9b59b6; color: white; border-color: #9b59b6;">
				<h3 class="panel-title"><span class="glyphicon glyphicon-link"></span> Partners</h3>
			</div>
			<div class="panel-body">
				<p>Portal para socios comerciales.</p>
				<ul class="list-unstyled small">
					<li><span class="glyphicon glyphicon-ok"></span> Alianzas</li>
					<li><span class="glyphicon glyphicon-ok"></span> Contratos</li>
					<li><span class="glyphicon glyphicon-ok"></span> Comisiones</li>
				</ul>
			</div>
			<div class="panel-footer">
				<a href="<?php echo Uri::create('partners'); ?>" class="btn btn-block" style="background: #9b59b6; color: white; border-color: #8e44ad;">Acceder</a>
			</div>
		</div>
	</div>

	<!-- Backend Sellers -->
	<div class="col-lg-2 col-md-4 col-sm-6">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-usd"></span> Sellers</h3>
			</div>
			<div class="panel-body">
				<p>Panel para vendedores.</p>
				<ul class="list-unstyled small">
					<li><span class="glyphicon glyphicon-ok"></span> Ventas</li>
					<li><span class="glyphicon glyphicon-ok"></span> Clientes</li>
					<li><span class="glyphicon glyphicon-ok"></span> Comisiones</li>
				</ul>
			</div>
			<div class="panel-footer">
				<a href="<?php echo Uri::create('sellers'); ?>" class="btn btn-info btn-block">Acceder</a>
			</div>
		</div>
	</div>

	<!-- Backend Clients -->
	<div class="col-lg-2 col-md-4 col-sm-6">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-user"></span> Clients</h3>
			</div>
			<div class="panel-body">
				<p>Portal para clientes.</p>
				<ul class="list-unstyled small">
					<li><span class="glyphicon glyphicon-ok"></span> Pedidos</li>
					<li><span class="glyphicon glyphicon-ok"></span> Perfil</li>
					<li><span class="glyphicon glyphicon-ok"></span> Soporte</li>
				</ul>
			</div>
			<div class="panel-footer">
				<a href="<?php echo Uri::create('clients'); ?>" class="btn btn-warning btn-block">Acceder</a>
			</div>
		</div>
	</div>
</div>

<!-- Frontend -->
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<h3><span class="glyphicon glyphicon-globe"></span> Frontend Público</h3>
		<hr>
	</div>
</div>

<div class="row">
	<!-- Tienda -->
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-shopping-cart"></span> Tienda Online</h3>
			</div>
			<div class="panel-body">
				<p>Tienda online completa con catálogo de productos, carrito de compras y proceso de checkout.</p>
				<ul class="list-unstyled">
					<li><span class="glyphicon glyphicon-ok"></span> Catálogo de Productos</li>
					<li><span class="glyphicon glyphicon-ok"></span> Carrito de Compras</li>
					<li><span class="glyphicon glyphicon-ok"></span> Proceso de Checkout</li>
					<li><span class="glyphicon glyphicon-ok"></span> Búsqueda de Productos</li>
				</ul>
			</div>
			<div class="panel-footer">
				<a href="<?php echo Uri::create('tienda'); ?>" class="btn btn-primary btn-block">
					<span class="glyphicon glyphicon-shopping-cart"></span> Ir a la Tienda
				</a>
			</div>
		</div>
	</div>

	<!-- Landing Page -->
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-home"></span> Landing Page</h3>
			</div>
			<div class="panel-body">
				<p>Página de aterrizaje con información institucional, contacto y páginas de contenido.</p>
				<ul class="list-unstyled">
					<li><span class="glyphicon glyphicon-ok"></span> Página Principal</li>
					<li><span class="glyphicon glyphicon-ok"></span> Acerca de Nosotros</li>
					<li><span class="glyphicon glyphicon-ok"></span> Formulario de Contacto</li>
					<li><span class="glyphicon glyphicon-ok"></span> Páginas Dinámicas</li>
				</ul>
			</div>
			<div class="panel-footer">
				<a href="<?php echo Uri::create('landing'); ?>" class="btn btn-default btn-block">
					<span class="glyphicon glyphicon-globe"></span> Ver Landing Page
				</a>
			</div>
		</div>
	</div>
</div>

<!-- Arquitectura Multi-tenant -->
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-cloud"></span> Arquitectura Multi-tenant</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-4">
						<h4><span class="glyphicon glyphicon-hdd"></span> Base de Datos</h4>
						<p>Cada tenant tiene su propia base de datos, configurada dinámicamente según el dominio (HTTP_HOST).</p>
					</div>
					<div class="col-md-4">
						<h4><span class="glyphicon glyphicon-th"></span> Módulos</h4>
						<p>Los módulos se cargan condicionalmente según los módulos activos para cada tenant.</p>
					</div>
					<div class="col-md-4">
						<h4><span class="glyphicon glyphicon-refresh"></span> Actualizable</h4>
						<p>La base del sistema puede actualizarse de forma remota sin afectar los tenants.</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

