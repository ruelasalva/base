<div class="row">
	<div class="col-md-12">
		<div class="jumbotron">
			<h2>¡Bienvenido al ERP Multi-tenant!</h2>
			<p>Sistema ERP completo con backends para administración, proveedores, vendedores y clientes, además de tienda online y landing page.</p>
			<p>
				<a class="btn btn-primary btn-lg" href="<?php echo Uri::base(); ?>tienda" role="button">
					<span class="glyphicon glyphicon-shopping-cart"></span> Ir a la Tienda
				</a>
				<a class="btn btn-default btn-lg" href="<?php echo Uri::base(); ?>landing" role="button">
					<span class="glyphicon glyphicon-globe"></span> Ver Landing Page
				</a>
			</p>
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
	<div class="col-md-3 col-sm-6">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-cog"></span> Administración</h3>
			</div>
			<div class="panel-body">
				<p>Panel de control para administradores del sistema.</p>
				<ul class="list-unstyled">
					<li><span class="glyphicon glyphicon-ok"></span> Gestión de Usuarios</li>
					<li><span class="glyphicon glyphicon-ok"></span> Configuración</li>
					<li><span class="glyphicon glyphicon-ok"></span> Reportes</li>
				</ul>
			</div>
			<div class="panel-footer">
				<a href="<?php echo Uri::base(); ?>admin" class="btn btn-primary btn-block">Acceder</a>
			</div>
		</div>
	</div>

	<!-- Backend Proveedor -->
	<div class="col-md-3 col-sm-6">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-briefcase"></span> Proveedores</h3>
			</div>
			<div class="panel-body">
				<p>Portal para proveedores y gestión de inventario.</p>
				<ul class="list-unstyled">
					<li><span class="glyphicon glyphicon-ok"></span> Gestión de Productos</li>
					<li><span class="glyphicon glyphicon-ok"></span> Inventario</li>
					<li><span class="glyphicon glyphicon-ok"></span> Órdenes de Compra</li>
				</ul>
			</div>
			<div class="panel-footer">
				<a href="<?php echo Uri::base(); ?>provider" class="btn btn-success btn-block">Acceder</a>
			</div>
		</div>
	</div>

	<!-- Backend Vendedores -->
	<div class="col-md-3 col-sm-6">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-usd"></span> Vendedores</h3>
			</div>
			<div class="panel-body">
				<p>Panel para equipo de ventas y CRM.</p>
				<ul class="list-unstyled">
					<li><span class="glyphicon glyphicon-ok"></span> Gestión de Ventas</li>
					<li><span class="glyphicon glyphicon-ok"></span> Clientes</li>
					<li><span class="glyphicon glyphicon-ok"></span> Comisiones</li>
				</ul>
			</div>
			<div class="panel-footer">
				<a href="<?php echo Uri::base(); ?>sellers" class="btn btn-info btn-block">Acceder</a>
			</div>
		</div>
	</div>

	<!-- Backend Clientes -->
	<div class="col-md-3 col-sm-6">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h3 class="panel-title"><span class="glyphicon glyphicon-user"></span> Clientes</h3>
			</div>
			<div class="panel-body">
				<p>Portal de autoservicio para clientes.</p>
				<ul class="list-unstyled">
					<li><span class="glyphicon glyphicon-ok"></span> Mis Pedidos</li>
					<li><span class="glyphicon glyphicon-ok"></span> Mi Perfil</li>
					<li><span class="glyphicon glyphicon-ok"></span> Soporte</li>
				</ul>
			</div>
			<div class="panel-footer">
				<a href="<?php echo Uri::base(); ?>clients" class="btn btn-warning btn-block">Acceder</a>
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
				<a href="<?php echo Uri::base(); ?>tienda" class="btn btn-primary btn-block">
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
				<a href="<?php echo Uri::base(); ?>landing" class="btn btn-default btn-block">
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

